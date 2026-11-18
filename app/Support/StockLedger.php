<?php

namespace App\Support;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

// update 1: pencatat stok, dipakai checkout, pesanan admin, dan edit produk
/**
 * Satu-satunya jalan mengubah stok yang perlu tercatat. Perubahan dan catatannya selalu
 * ditulis dalam satu transaksi, jadi tidak ada stok berubah tanpa riwayat (atau sebaliknya).
 */
class StockLedger
{
    /**
     * Ubah stok sebesar $delta (+ menambah, − mengurangi) dan catat. Baris dikunci dulu
     * supaya "sebelum" dan "sesudah" selalu benar walau ada dua perubahan bersamaan.
     * Mengembalikan null kalau produk/varian sudah tidak ada.
     */
    public static function move(Product|ProductVariant $item, int $delta, string $type, ?Order $order = null, ?string $reason = null): ?StockMovement
    {
        return DB::transaction(function () use ($item, $delta, $type, $order, $reason) {
            $fresh = $item::whereKey($item->getKey())->lockForUpdate()->first();

            if (! $fresh) {
                return null;
            }

            $before = (int) $fresh->stock;
            $after = $before + $delta;

            // Query langsung (bukan ->update()) supaya perubahan stok pesanan tidak membanjiri log audit.
            $item::whereKey($fresh->getKey())->update(['stock' => $after]);
            $item->setAttribute('stock', $after);

            return self::write($fresh, $before, $after, $type, $order, $reason);
        });
    }

    /**
     * Catat perubahan yang sudah ditulis oleh pemanggil (mis. form edit produk).
     * Tidak mencatat apa-apa kalau stoknya sama.
     */
    public static function record(Product|ProductVariant $item, int $before, int $after, string $type, ?Order $order = null, ?string $reason = null): ?StockMovement
    {
        if ($before === $after) {
            return null;
        }

        return self::write($item, $before, $after, $type, $order, $reason);
    }

    private static function write(Product|ProductVariant $item, int $before, int $after, string $type, ?Order $order, ?string $reason): StockMovement
    {
        $user = auth()->user();
        $variant = $item instanceof ProductVariant ? $item : null;
        $product = $variant ? ($variant->product ?? Product::find($variant->product_id)) : $item;

        // Pesanan masuk dilakukan pelanggan; selain itu pelakunya akun yang sedang login (admin).
        $actor = $type === 'order' && $order
            ? $order->name.' (pelanggan)'
            : ($user?->name ?? 'Sistem');

        return StockMovement::create([
            'product_id' => $product?->id,
            'product_variant_id' => $variant?->id,
            'order_id' => $order?->id,
            'user_id' => $user?->id,
            'product_name' => $product?->name ?? 'Produk #'.($variant?->product_id ?? $item->getKey()),
            'variant_label' => $variant?->label,
            'type' => $type,
            'quantity' => $after - $before,
            'stock_before' => $before,
            'stock_after' => $after,
            'reason' => $reason ?? self::defaultReason($type, $order, $user?->name),
            'actor_name' => $actor,
        ]);
    }

    private static function defaultReason(string $type, ?Order $order, ?string $admin): string
    {
        return match (true) {
            $type === 'order' => 'Pesanan '.$order?->order_number,
            $type === 'return' => 'Pesanan '.$order?->order_number.' dibatalkan',
            $type === 'reactivate' => 'Pesanan '.$order?->order_number.' diaktifkan lagi',
            $type === 'initial' => 'Stok awal saat produk dibuat'.($admin ? ' oleh '.$admin : ''),
            default => 'Diubah manual'.($admin ? ' oleh '.$admin : ''),
        };
    }
}
