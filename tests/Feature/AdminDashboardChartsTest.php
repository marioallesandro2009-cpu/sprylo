<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\FinancialReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardChartsTest extends TestCase
{
    use RefreshDatabase;

    private int $sequence = 0;

    protected function setUp(): void
    {
        parent::setUp();
        $this->travelTo('2026-11-16 05:00:00'); // 12:00 WIB
    }

    private function admin(): User
    {
        $user = new User;
        $user->forceFill(['name' => 'Admin', 'email' => 'admin'.++$this->sequence.'@example.com', 'password' => 'not-used', 'role' => 'admin'])->save();

        return $user;
    }

    private function order(string $placedUtc, string $status, int $total, ?Product $product = null, int $qty = 1): Order
    {
        $order = Order::create([
            'order_number' => 'SPR-D'.++$this->sequence, 'name' => 'Pelanggan', 'email' => 'c@example.com', 'phone' => '0800', 'address' => 'Jl', 'city' => 'Jakarta', 'postal_code' => '10110',
            'subtotal' => $total, 'tax' => 0, 'shipping' => 0, 'total' => $total, 'status' => $status, 'payment_method' => 'qris',
        ]);
        $order->forceFill(['created_at' => $placedUtc])->save();

        if ($product) {
            $order->items()->create(['product_id' => $product->id, 'product_name' => $product->name, 'unit_price' => $total / $qty, 'qty' => $qty, 'subtotal' => $total]);
        }

        return $order;
    }

    private function product(string $name, int $stock = 50): Product
    {
        $category = Category::firstOrCreate(['slug' => 'test'], ['name' => 'Test']);

        return Product::create(['category_id' => $category->id, 'name' => $name, 'slug' => str($name)->slug(), 'price' => 1000, 'stock' => $stock, 'is_active' => true]);
    }

    public function test_sales_chart_matches_the_financial_report_for_every_range(): void
    {
        $this->order('2026-11-16 03:00:00', 'paid', 500000);
        $this->order('2026-11-12 03:00:00', 'completed', 250000);
        $this->order('2026-11-12 03:00:00', 'cancelled', 999000);
        $this->order('2026-11-12 03:00:00', 'awaiting_proof', 888000);
        $this->order('2026-10-20 03:00:00', 'paid', 100000);
        $this->order('2026-03-05 03:00:00', 'paid', 70000);

        $admin = $this->actingAs($this->admin());

        $expected = [
            '7d' => [750000, 2, 7],
            '30d' => [850000, 3, 30],
            '12m' => [920000, 4, 12],
        ];

        foreach ($expected as $range => [$total, $count, $bars]) {
            $response = $admin->get(route('admin.dashboard', ['range' => $range]))->assertOk();
            $chart = $response->viewData('chart');

            $this->assertSame($bars, $chart->count(), "jumlah batang $range");
            $this->assertEquals($total, $chart->sum('total'), "total grafik $range");
            $this->assertEquals($total, $response->viewData('chartTotal'));
            $this->assertSame($count, $response->viewData('chartOrders'));

            $days = ['7d' => 6, '30d' => 29, '12m' => null][$range];
            $report = $days
                ? FinancialReport::between(now('Asia/Jakarta')->subDays($days)->toDateString(), now('Asia/Jakarta')->toDateString())
                : FinancialReport::between('2025-12-01', '2026-11-16');
            $this->assertEquals($report->summary()['total'], $response->viewData('chartTotal'), "sama dengan laporan $range");
        }
    }

    public function test_an_unknown_range_falls_back_to_seven_days(): void
    {
        $this->actingAs($this->admin())->get(route('admin.dashboard', ['range' => 'abc']))
            ->assertOk()->assertViewHas('range', '7d');
    }

    public function test_orders_by_status_follow_the_range(): void
    {
        $this->order('2026-11-15 03:00:00', 'paid', 1000);
        $this->order('2026-11-15 03:00:00', 'cancelled', 1000);
        $this->order('2026-10-01 03:00:00', 'cancelled', 1000);

        $counts = fn (string $range) => $this->actingAs($this->admin())->get(route('admin.dashboard', ['range' => $range]))
            ->viewData('statuses')->pluck('count', 'label');

        $this->assertSame(['Bayar' => 1, 'Dibatalkan' => 1], $counts('7d')->filter()->all());
        $this->assertSame(2, $counts('12m')['Dibatalkan']);
    }

    public function test_best_sellers_count_only_paid_orders_in_range_and_rank_by_units(): void
    {
        $a = $this->product('Alfa');
        $b = $this->product('Beta');
        $this->order('2026-11-15 03:00:00', 'paid', 300000, $a, 3);
        $this->order('2026-11-14 03:00:00', 'completed', 100000, $b, 1);
        $this->order('2026-11-14 03:00:00', 'cancelled', 900000, $b, 9);
        $this->order('2026-09-01 03:00:00', 'paid', 900000, $b, 9);

        $response = $this->actingAs($this->admin())->get(route('admin.dashboard', ['range' => '7d']))->assertOk();

        $this->assertSame(['Alfa', 'Beta'], $response->viewData('topProducts')->pluck('product_name')->all());
        $this->assertSame([3, 1], $response->viewData('topProducts')->pluck('units')->map(fn ($u) => (int) $u)->all());
        $response->assertSee('Produk terlaris');
    }

    public function test_low_stock_panel_lists_the_emptiest_products_first(): void
    {
        $this->product('Habis', 0);
        $this->product('Tinggal dua', 2);
        $this->product('Melimpah', 100);

        $this->actingAs($this->admin())->get(route('admin.dashboard'))->assertOk()
            ->assertSeeInOrder(['Stok menipis', 'Habis', 'Tinggal dua'])
            ->assertDontSee('Melimpah');
    }

    public function test_dashboard_renders_with_no_data_at_all(): void
    {
        foreach (['7d', '30d', '12m'] as $range) {
            $this->actingAs($this->admin())->get(route('admin.dashboard', ['range' => $range]))
                ->assertOk()->assertSee('Belum ada penjualan pada rentang ini');
        }
    }
}
