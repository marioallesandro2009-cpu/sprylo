<?php

namespace Tests\Feature;

use App\Mail\OrderConfirmationMail;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\PromoCode;
use App\Services\InvoiceService;
use App\Support\Cart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PpnTest extends TestCase
{
    use RefreshDatabase;

    private function totals(float $subtotal): array
    {
        return Cart::totals(collect([['subtotal' => $subtotal]]));
    }

    private function promo(float $value): void
    {
        PromoCode::create(['code' => 'HEMAT', 'type' => 'fixed', 'value' => $value, 'is_active' => true]);
        session(['promo_code' => 'HEMAT']);
    }

    public function test_ppn_is_twelve_percent_of_the_goods(): void
    {
        $totals = $this->totals(1000000);

        $this->assertEquals(120000, $totals['tax']);
        $this->assertEquals(1120000, $totals['total']);
    }

    public function test_shipping_is_not_taxed(): void
    {
        $totals = $this->totals(100000);

        $this->assertEquals(25000, $totals['shipping']);
        $this->assertEquals(12000, $totals['tax']);
        $this->assertEquals(137000, $totals['total']);
    }

    public function test_ppn_is_charged_after_the_promo(): void
    {
        $this->promo(100000);
        $totals = $this->totals(1000000);

        $this->assertEquals(100000, $totals['promo_discount']);
        $this->assertEquals(108000, $totals['tax']);
        $this->assertEquals(1008000, $totals['total']);
    }

    public function test_a_promo_covering_the_whole_price_leaves_no_ppn(): void
    {
        $this->promo(100000);
        $totals = $this->totals(50000);

        $this->assertEquals(0, $totals['tax']);
        $this->assertEquals(25000, $totals['total']);
    }

    public function test_the_tax_line_names_what_the_order_was_charged(): void
    {
        $label = fn (array $fields) => (new Order($fields))->tax_label;

        $this->assertSame('PPN 12%', $label(['subtotal' => 1000000, 'promo_discount' => 100000, 'tax' => 108000]));
        $this->assertSame('Tax (8%)', $label(['subtotal' => 1000000, 'promo_discount' => 0, 'tax' => 80000]));
        $this->assertSame('Tax (8%)', $label(['subtotal' => 1000000, 'promo_discount' => 100000, 'tax' => 80000]));
        $this->assertSame('Tax', $label(['subtotal' => 100000, 'promo_discount' => 0, 'tax' => 0]));
    }

    public function test_the_cart_page_shows_ppn_after_the_promo(): void
    {
        $category = Category::create(['name' => 'Test', 'slug' => 'test']);
        $product = Product::create(['category_id' => $category->id, 'name' => 'Test', 'slug' => 'test', 'price' => 1000000, 'stock' => 5, 'is_active' => true]);
        $this->promo(100000);

        $this->withSession(['cart' => [(string) $product->id => 1], 'promo_code' => 'HEMAT'])
            ->get(route('cart.index'))
            ->assertOk()
            ->assertSeeInOrder(['HEMAT', 'PPN 12%', rupiah(108000), rupiah(1008000)])
            ->assertDontSee('Estimated tax');
    }

    public function test_the_checkout_total_starts_from_ppn_after_the_promo(): void
    {
        $category = Category::create(['name' => 'Test', 'slug' => 'test']);
        $product = Product::create(['category_id' => $category->id, 'name' => 'Test', 'slug' => 'test', 'price' => 1000000, 'stock' => 5, 'is_active' => true]);
        $this->promo(100000);

        $this->withSession(['cart' => [(string) $product->id => 1], 'promo_code' => 'HEMAT'])
            ->get(route('checkout.index'))
            ->assertOk()
            ->assertSee('PPN 12%')
            ->assertSee('data-base="1008000"', false);
    }

    public function test_the_email_and_invoice_name_the_tax(): void
    {
        $order = Order::create([
            'order_number' => 'SPR-PPN001', 'invoice_number' => 'INV-2611-0099', 'invoiced_at' => now(),
            'name' => 'Test', 'email' => 'test@example.com', 'phone' => '0800', 'address' => 'Test', 'city' => 'Jakarta', 'postal_code' => '10110',
            'subtotal' => 1000000, 'shipping' => 0, 'tax' => 108000, 'promo_code' => 'HEMAT', 'promo_discount' => 100000, 'total' => 1008000,
            'status' => 'paid', 'payment_method' => 'qris',
        ]);

        $this->assertStringContainsString('PPN 12%', (new OrderConfirmationMail($order))->render());
        $this->assertStringContainsString('PPN 12%', view('invoices.pdf', app(InvoiceService::class)->data($order))->render());
    }
}
