<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use App\Services\FinancialReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Tests\TestCase;

class FinancialReportTest extends TestCase
{
    use RefreshDatabase;

    private const NOVEMBER = ['from' => '2026-11-01', 'to' => '2026-11-30'];

    private int $sequence = 0;

    private function admin(string $role = 'admin'): User
    {
        $user = new User;
        $user->forceFill(['name' => 'Test', 'email' => $role.'@example.com', 'password' => 'not-used', 'role' => $role])->save();

        return $user;
    }

    /** Rp1.000.000 goods, Rp100.000 promo, PPN Rp108.000, Rp20.000 shipping, Rp15.000 insurance. */
    private function order(string $placedUtc, array $overrides = []): Order
    {
        $order = Order::create($overrides + [
            'order_number' => 'SPR-R'.++$this->sequence, 'name' => 'Pelanggan', 'email' => 'c@example.com', 'phone' => '0800', 'address' => 'Jl', 'city' => 'Jakarta', 'postal_code' => '10110',
            'subtotal' => 1000000, 'promo_discount' => 100000, 'tax' => 108000, 'shipping' => 20000, 'insurance_fee' => 15000, 'total' => 1043000,
            'status' => 'paid', 'payment_method' => 'qris',
        ]);
        $order->forceFill(['created_at' => $placedUtc])->save();

        return $order;
    }

    private function downloaded($response): string
    {
        $path = $response->baseResponse->getFile()->getPathname();
        $this->beforeApplicationDestroyed(fn () => @unlink($path));

        return $path;
    }

    public function test_only_paid_orders_inside_the_range_count(): void
    {
        $this->order('2026-11-10 03:00:00');
        $this->order('2026-11-11 03:00:00', ['status' => 'completed', 'payment_method' => 'bank_transfer']);
        $this->order('2026-11-12 03:00:00', ['status' => 'cancelled']);
        $this->order('2026-11-12 03:00:00', ['status' => 'awaiting_proof']);
        $this->order('2026-10-20 03:00:00');

        $report = FinancialReport::between(...array_values(self::NOVEMBER));
        $summary = $report->summary();

        $this->assertSame(2, $summary['count']);
        $this->assertEquals(2000000, $summary['goods']);
        $this->assertEquals(1800000, $summary['net']);
        $this->assertEquals(216000, $summary['tax']);
        $this->assertEquals(2086000, $summary['total']);
        $this->assertEquals(0, $report->difference());
        $this->assertSame(['QRIS', 'Transfer Bank'], $report->paymentMethods()->pluck('label')->sort()->values()->all());
    }

    public function test_the_month_follows_jakarta_time_not_utc(): void
    {
        $this->order('2026-10-31 17:30:00'); // 1 Nov 00:30 WIB: November.
        $this->order('2026-11-30 17:30:00'); // 1 Dec 00:30 WIB: not November.

        $report = FinancialReport::between(...array_values(self::NOVEMBER));

        $this->assertSame(1, $report->summary()['count']);
        $this->assertSame('1 Nov 2026', $report->periods()->first()['label']);
    }

    public function test_a_long_range_is_grouped_by_month(): void
    {
        $this->order('2026-03-10 03:00:00');
        $this->order('2026-03-20 03:00:00');
        $this->order('2026-07-10 03:00:00');

        $report = FinancialReport::between('2026-01-01', '2026-12-31');

        $this->assertTrue($report->isMonthly());
        $this->assertSame([2, 1], $report->periods()->pluck('count')->all());
    }

    public function test_a_stored_total_that_does_not_add_up_is_reported(): void
    {
        $this->order('2026-11-10 03:00:00', ['total' => 1000000]);

        $this->assertEquals(-43000, FinancialReport::between(...array_values(self::NOVEMBER))->difference());
    }

    public function test_the_page_opens_for_admins_only(): void
    {
        $this->order('2026-11-10 03:00:00', ['name' => 'Budi Santoso']);

        $this->actingAs($this->admin())->get(route('admin.reports.finance', self::NOVEMBER))
            ->assertOk()
            ->assertSee('Budi Santoso')
            ->assertSee('Penjualan bersih')
            ->assertSee(rupiah(108000));

        $this->actingAs($this->admin('user'))->get(route('admin.reports.finance', self::NOVEMBER))->assertForbidden();
    }

    public function test_guests_cannot_download(): void
    {
        $this->get(route('admin.reports.finance.download', ['format' => 'xlsx'] + self::NOVEMBER))->assertRedirect();
    }

    public function test_a_bad_range_is_refused(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->get(route('admin.reports.finance', ['from' => '2026-11-30', 'to' => '2026-11-01']))->assertSessionHasErrors('to');
        $this->actingAs($admin)->get(route('admin.reports.finance', ['from' => '2024-01-01', 'to' => '2026-11-01']))->assertSessionHasErrors('to');
        $this->actingAs($admin)->get(route('admin.reports.finance', ['from' => 'kemarin', 'to' => '2026-11-01']))->assertSessionHasErrors('from');
    }

    public function test_an_unknown_format_is_not_found(): void
    {
        $this->actingAs($this->admin())->get(route('admin.reports.finance').'/download/exe')->assertNotFound();
    }

    public function test_the_excel_file_is_one_table_with_real_numbers_and_safe_text(): void
    {
        $first = $this->order('2026-11-10 03:00:00', ['name' => '=1+1']);
        $first->items()->create(['product_name' => 'A', 'unit_price' => 100, 'qty' => 2, 'subtotal' => 200]);
        $first->items()->create(['product_name' => 'B', 'unit_price' => 100, 'qty' => 1, 'subtotal' => 100]);
        $this->order('2026-11-11 03:00:00', ['status' => 'completed']);

        $response = $this->actingAs($this->admin())->get(route('admin.reports.finance.download', ['format' => 'xlsx'] + self::NOVEMBER))->assertOk();
        $this->assertStringContainsString('laporan-keuangan-2026-11-01_2026-11-30.xlsx', $response->headers->get('content-disposition'));

        $book = IOFactory::load($this->downloaded($response));
        $this->assertSame(['Laporan Keuangan'], $book->getSheetNames());

        $sheet = $book->getActiveSheet();
        $this->assertSame(['No', 'Order #', 'Customer', 'Contact', 'Items', 'Total Amount', 'Status', 'Date & Time'], $sheet->rangeToArray('A1:H1')[0]);
        $this->assertSame('D1FAE5', $sheet->getStyle('A1')->getFill()->getStartColor()->getRGB());
        $this->assertTrue($sheet->getStyle('H1')->getFont()->getBold());

        $this->assertSame('s', $sheet->getCell('C2')->getDataType());
        $this->assertSame('=1+1', $sheet->getCell('C2')->getValue());
        $this->assertSame('c@example.com', $sheet->getCell('D2')->getValue());
        $this->assertEquals(3, $sheet->getCell('E2')->getValue());
        $this->assertEquals(1043000, $sheet->getCell('F2')->getValue());
        $this->assertSame('Paid', $sheet->getCell('G2')->getValue());
        $this->assertSame('10 Nov 2026 10:00', $sheet->getCell('H2')->getFormattedValue());

        $this->assertEquals(2, $sheet->getCell('A3')->getValue());
        $this->assertEquals(0, $sheet->getCell('E3')->getValue());
        $this->assertSame('Completed', $sheet->getCell('G3')->getValue());
        $this->assertSame(3, $sheet->getHighestRow());
    }

    public function test_word_is_no_longer_offered(): void
    {
        $this->actingAs($this->admin())->get(route('admin.reports.finance').'/download/docx')->assertNotFound();
    }

    public function test_the_pdf_has_the_same_columns_as_the_excel_file(): void
    {
        $this->order('2026-11-10 03:00:00', ['name' => 'Toko A&B <Jaya>']);

        $html = view('admin.reports.finance-pdf', [
            'report' => FinancialReport::between(...array_values(self::NOVEMBER)),
            'headers' => \App\Services\FinancialReportExporter::HEADERS,
            'headerColour' => \App\Services\FinancialReportExporter::HEADER_COLOUR,
        ])->render();
        $this->assertStringContainsString('<th>Total Amount</th>', $html);
        $this->assertStringContainsString('Toko A&amp;B &lt;Jaya&gt;', $html);
        $this->assertStringContainsString('10 Nov 2026 10:00', $html);

        $response = $this->actingAs($this->admin())->get(route('admin.reports.finance.download', ['format' => 'pdf'] + self::NOVEMBER))->assertOk();
        $this->assertStringContainsString('laporan-keuangan-2026-11-01_2026-11-30.pdf', $response->headers->get('content-disposition'));
        $this->assertStringStartsWith('%PDF', file_get_contents($this->downloaded($response)));
    }

    public function test_an_empty_period_still_downloads(): void
    {
        $admin = $this->admin();

        foreach (['xlsx', 'pdf'] as $format) {
            $this->downloaded($this->actingAs($admin)->get(route('admin.reports.finance.download', ['format' => $format] + self::NOVEMBER))->assertOk());
        }

        $this->actingAs($admin)->get(route('admin.reports.finance', self::NOVEMBER))->assertOk()->assertSee('Belum ada pesanan dibayar');
    }
}
