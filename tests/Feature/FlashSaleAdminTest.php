<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Discount;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// update 4: tes halaman Flash Sale / Diskon Terjadwal
class FlashSaleAdminTest extends TestCase
{
    use RefreshDatabase;

    private int $sequence = 0;

    private function admin(): User
    {
        $user = new User;
        $user->forceFill(['name' => 'Admin', 'email' => 'admin'.++$this->sequence.'@example.com', 'password' => 'not-used', 'role' => 'admin'])->save();

        return $user;
    }

    private function product(): Product
    {
        $category = Category::create(['name' => 'Test', 'slug' => 'test']);

        return Product::create(['category_id' => $category->id, 'name' => 'iPad Uji', 'slug' => 'ipad-uji', 'price' => 1000000, 'stock' => 20, 'is_active' => true]);
    }

    private function flash(Product $product, array $overrides = []): Discount
    {
        return Discount::create($overrides + [
            'name' => 'Flash Uji', 'scope' => 'product', 'product_id' => $product->id, 'type' => 'percent', 'value' => 20,
            'starts_at' => now()->subHour(), 'ends_at' => now()->addHours(5), 'is_flash' => true, 'quota' => 10, 'sold' => 4, 'is_active' => true,
        ]);
    }

    public function test_the_page_shows_summary_status_counts_and_remaining_time(): void
    {
        $product = $this->product();
        $this->flash($product);
        $this->flash($product, ['name' => 'Besok', 'starts_at' => now()->addDay(), 'ends_at' => now()->addDays(2), 'sold' => 0]);
        $this->flash($product, ['name' => 'Habis', 'sold' => 10]);

        $response = $this->actingAs($this->admin())->get(route('admin.flash-sales.index'))->assertOk();

        $response->assertViewHas('stats', fn ($s) => $s['live'] === 1 && $s['scheduled'] === 1 && $s['sold'] === 14 && $s['remaining'] === 6)
            ->assertSee('Sisa waktu')
            ->assertSee('Sisa 6 unit')
            ->assertSee('Kuota habis')
            ->assertSee('−20%');
    }

    public function test_the_status_tab_filters_the_list(): void
    {
        $product = $this->product();
        $this->flash($product, ['name' => 'Sedang Jalan']);
        $this->flash($product, ['name' => 'Sudah Habis', 'sold' => 10]);

        $this->actingAs($this->admin())->get(route('admin.flash-sales.index', ['status' => 'live']))
            ->assertOk()->assertSee('Sedang Jalan')->assertDontSee('Sudah Habis');

        $this->actingAs($this->admin())->get(route('admin.flash-sales.index', ['status' => 'bukan-status']))
            ->assertOk()->assertSee('Sedang Jalan')->assertSee('Sudah Habis');
    }

    public function test_scheduled_discounts_page_still_works_and_stays_separate(): void
    {
        $product = $this->product();
        $this->flash($product, ['name' => 'Hanya Flash']);
        Discount::create(['name' => 'Diskon Biasa', 'scope' => 'product', 'product_id' => $product->id, 'type' => 'amount', 'value' => 50000, 'is_flash' => false, 'is_active' => true]);

        $this->actingAs($this->admin())->get(route('admin.discounts.index'))
            ->assertOk()->assertSee('Diskon Biasa')->assertDontSee('Hanya Flash')->assertSee('Total aturan');
    }

    public function test_the_form_pages_open_and_carry_prices_for_the_preview(): void
    {
        $product = $this->product();
        $discount = $this->flash($product);
        $admin = $this->admin();

        $this->actingAs($admin)->get(route('admin.flash-sales.create'))->assertOk()->assertSee('Pratinjau harga')->assertSee('&quot;price&quot;:1000000', false);
        $this->actingAs($admin)->get(route('admin.flash-sales.edit', $discount))->assertOk()->assertSee('Simpan perubahan');
    }

    public function test_creating_a_flash_sale_still_validates_and_saves(): void
    {
        $product = $this->product();

        $this->actingAs($this->admin())->post(route('admin.flash-sales.store'), [
            'name' => 'Baru', 'scope' => 'product', 'product_id' => $product->id, 'type' => 'percent', 'value' => 15,
            'quota' => 5, 'starts_at' => now('Asia/Jakarta')->format('Y-m-d\TH:i'), 'ends_at' => now('Asia/Jakarta')->addDay()->format('Y-m-d\TH:i'), 'is_active' => 1,
        ])->assertRedirect(route('admin.flash-sales.index'));

        $this->assertDatabaseHas('discounts', ['name' => 'Baru', 'is_flash' => true, 'quota' => 5]);
    }
}
