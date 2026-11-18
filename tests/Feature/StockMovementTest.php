<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use App\Models\User;
use App\Support\StockLedger;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class StockMovementTest extends TestCase
{
    use RefreshDatabase;

    private int $sequence = 0;

    private function admin(string $name = 'Budi Admin'): User
    {
        $user = new User;
        $user->forceFill(['name' => $name, 'email' => 'admin'.++$this->sequence.'@example.com', 'password' => 'not-used', 'role' => 'admin'])->save();

        return $user;
    }

    private function product(int $stock = 10, string $name = 'Kamera'): Product
    {
        $category = Category::firstOrCreate(['slug' => 'test'], ['name' => 'Test']);

        return Product::create(['category_id' => $category->id, 'name' => $name, 'slug' => str($name)->slug().$this->sequence++, 'price' => 100000, 'stock' => $stock, 'is_active' => true]);
    }

    private function order(Product $product, int $qty, ?ProductVariant $variant = null, string $status = 'paid'): Order
    {
        $order = Order::create([
            'order_number' => 'SPR-S'.++$this->sequence, 'name' => 'Pelanggan', 'email' => 'c@example.com', 'phone' => '0800', 'address' => 'Jl', 'city' => 'Jakarta', 'postal_code' => '10110',
            'subtotal' => 100000 * $qty, 'tax' => 0, 'shipping' => 0, 'total' => 100000 * $qty, 'status' => $status, 'payment_method' => 'qris',
        ]);
        $order->items()->create([
            'product_id' => $product->id, 'product_variant_id' => $variant?->id, 'product_name' => $product->name, 'variant_label' => $variant?->label,
            'unit_price' => 100000, 'qty' => $qty, 'subtotal' => 100000 * $qty,
        ]);

        return $order;
    }

    public function test_an_incoming_order_is_recorded_with_before_and_after(): void
    {
        $product = $this->product(10);
        $order = $this->order($product, 3);

        DB::transaction(fn () => StockLedger::move($product, -3, 'order', $order));

        $this->assertSame(7, $product->fresh()->stock);
        $row = StockMovement::sole();
        $this->assertSame([-3, 10, 7, 'order', $product->id, $order->id], [$row->quantity, $row->stock_before, $row->stock_after, $row->type, $row->product_id, $row->order_id]);
        $this->assertSame('Pesanan '.$order->order_number, $row->reason);
        $this->assertSame('Pelanggan (pelanggan)', $row->actor_name);
    }

    public function test_the_stock_change_and_its_record_roll_back_together(): void
    {
        $product = $this->product(10);

        try {
            DB::transaction(function () use ($product) {
                StockLedger::move($product, -4, 'order');

                throw new \RuntimeException('checkout gagal');
            });
        } catch (\RuntimeException) {
        }

        $this->assertSame(10, $product->fresh()->stock);
        $this->assertSame(0, StockMovement::count());
    }

    public function test_cancelling_and_reactivating_an_order_records_both(): void
    {
        $product = $this->product(7);
        $order = $this->order($product, 3);
        $admin = $this->admin();

        $this->actingAs($admin)->patch(route('admin.orders.update-status', $order), ['status' => 'cancelled'])->assertRedirect();
        $this->assertSame(10, $product->fresh()->stock);

        $this->actingAs($admin)->patch(route('admin.orders.update-status', $order), ['status' => 'paid'])->assertRedirect();
        $this->assertSame(7, $product->fresh()->stock);

        $rows = StockMovement::orderBy('id')->get();
        $this->assertSame(['return', 'reactivate'], $rows->pluck('type')->all());
        $this->assertSame([3, -3], $rows->pluck('quantity')->all());
        $this->assertSame([[7, 10], [10, 7]], $rows->map(fn ($r) => [$r->stock_before, $r->stock_after])->all());
        $this->assertSame('Budi Admin', $rows[0]->actor_name);
        $this->assertSame($admin->id, $rows[0]->user_id);
    }

    public function test_deleting_an_active_order_returns_its_stock_on_record(): void
    {
        $product = $this->product(7);
        $order = $this->order($product, 2);

        $this->actingAs($this->admin())->delete(route('admin.orders.destroy', $order))->assertRedirect();

        $this->assertSame(9, $product->fresh()->stock);
        $row = StockMovement::sole();
        $this->assertSame([2, 'return'], [$row->quantity, $row->type]);
        $this->assertStringContainsString('dihapus', $row->reason);
    }

    public function test_a_variant_movement_names_the_variant_and_keeps_the_product_total(): void
    {
        $product = $this->product(0);
        $a = $product->variants()->create(['label' => '128 GB', 'price' => 100000, 'stock' => 4, 'weight' => 0, 'sort_order' => 0]);
        $product->variants()->create(['label' => '256 GB', 'price' => 120000, 'stock' => 6, 'weight' => 0, 'sort_order' => 1]);
        $product->syncFromVariants();
        $order = $this->order($product, 1, $a);

        $this->actingAs($this->admin())->patch(route('admin.orders.update-status', $order), ['status' => 'cancelled']);

        $this->assertSame(5, $a->fresh()->stock);
        $this->assertSame(11, $product->fresh()->stock);
        $row = StockMovement::sole();
        $this->assertSame([$a->id, '128 GB', 4, 5], [$row->product_variant_id, $row->variant_label, $row->stock_before, $row->stock_after]);
    }

    public function test_a_manual_edit_by_an_admin_is_recorded(): void
    {
        $product = $this->product(10);

        $this->actingAs($this->admin('Sari Admin'))->put(route('admin.products.update', $product), [
            'category_id' => $product->category_id, 'name' => $product->name, 'price' => 100000, 'stock' => 25, 'weight' => 100,
        ])->assertRedirect();

        $this->assertSame(25, $product->fresh()->stock);
        $row = StockMovement::sole();
        $this->assertSame(['manual', 15, 10, 25, 'Sari Admin'], [$row->type, $row->quantity, $row->stock_before, $row->stock_after, $row->actor_name]);
        $this->assertStringContainsString('Sari Admin', $row->reason);
    }

    public function test_saving_a_product_without_changing_stock_records_nothing(): void
    {
        $product = $this->product(10);

        $this->actingAs($this->admin())->put(route('admin.products.update', $product), [
            'category_id' => $product->category_id, 'name' => 'Nama baru', 'price' => 100000, 'stock' => 10, 'weight' => 100,
        ])->assertRedirect();

        $this->assertSame(0, StockMovement::count());
    }

    public function test_a_new_product_and_its_variants_start_with_a_record(): void
    {
        $category = Category::create(['name' => 'Cat', 'slug' => 'cat']);
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.products.store'), [
            'category_id' => $category->id, 'name' => 'Polos', 'price' => 50000, 'stock' => 8, 'weight' => 100,
        ])->assertRedirect();
        $this->actingAs($admin)->post(route('admin.products.store'), [
            'category_id' => $category->id, 'name' => 'Bervarian', 'price' => 50000, 'stock' => 0, 'weight' => 100,
            'variants' => [['label' => 'Merah', 'price' => 50000, 'stock' => 3], ['label' => 'Biru', 'price' => 50000, 'stock' => 2]],
        ])->assertRedirect();

        $rows = StockMovement::orderBy('id')->get();
        $this->assertSame(['Polos', 'Bervarian', 'Bervarian'], $rows->pluck('product_name')->all());
        $this->assertSame([null, 'Merah', 'Biru'], $rows->pluck('variant_label')->all());
        $this->assertSame([8, 3, 2], $rows->pluck('quantity')->all());
        $this->assertSame(['initial'], $rows->pluck('type')->unique()->all());
    }

    public function test_editing_a_variants_stock_records_the_variant_not_the_product_total(): void
    {
        $product = $this->product(0);
        $variant = $product->variants()->create(['label' => 'Merah', 'price' => 50000, 'stock' => 5, 'weight' => 0, 'sort_order' => 0]);
        $product->syncFromVariants();

        $this->actingAs($this->admin())->put(route('admin.products.update', $product), [
            'category_id' => $product->category_id, 'name' => $product->name, 'price' => 50000, 'stock' => 5, 'weight' => 100,
            'variants' => [['id' => $variant->id, 'label' => 'Merah', 'price' => 50000, 'stock' => 9]],
        ])->assertRedirect();

        $this->assertSame(9, $product->fresh()->stock);
        $row = StockMovement::sole();
        $this->assertSame([$variant->id, 4, 5, 9], [$row->product_variant_id, $row->quantity, $row->stock_before, $row->stock_after]);
    }

    public function test_history_page_filters_by_product_type_and_date_and_paginates(): void
    {
        $kamera = $this->product(10, 'Kamera');
        $lensa = $this->product(10, 'Lensa');
        StockLedger::move($kamera, -1, 'order', $this->order($kamera, 1));
        StockLedger::move($lensa, 5, 'manual');
        StockMovement::where('product_id', $lensa->id)->update(['created_at' => '2026-10-01 03:00:00']);
        $admin = $this->admin();

        $this->actingAs($admin)->get(route('admin.stock-movements.index'))->assertOk()->assertSee('Kamera')->assertSee('Lensa');

        $this->actingAs($admin)->get(route('admin.stock-movements.index', ['product' => $kamera->id]))
            ->assertOk()->assertSee('Pesanan SPR-')->assertDontSee('Diubah manual');

        $this->actingAs($admin)->get(route('admin.stock-movements.index', ['type' => 'manual']))
            ->assertOk()->assertSee('Diubah manual')->assertDontSee('Pesanan SPR-');

        // 1 Okt 03:00 UTC = 10:00 WIB, jadi tanggal 1 Okt masuk; filter tanggal lain tidak.
        $this->actingAs($admin)->get(route('admin.stock-movements.index', ['from' => '2026-10-01', 'to' => '2026-10-01']))
            ->assertOk()->assertSeeInOrder(['01 Oct 2026', '10:00:00'])->assertDontSee('Pesanan SPR-')
            ->assertViewHas('unitsIn', 5)->assertViewHas('unitsOut', 0);

        for ($i = 0; $i < 35; $i++) {
            StockLedger::move($lensa, 1, 'manual');
        }
        $this->actingAs($admin)->get(route('admin.stock-movements.index', ['page' => 2]))->assertOk()
            ->assertViewHas('movements', fn ($page) => $page->currentPage() === 2 && $page->total() === 37 && $page->count() === 7);
    }

    public function test_history_page_needs_an_admin(): void
    {
        $this->get(route('admin.stock-movements.index'))->assertRedirect();
    }
}
