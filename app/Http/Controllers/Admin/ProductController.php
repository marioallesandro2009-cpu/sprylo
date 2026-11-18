<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Support\DiscountSync;
use App\Support\StockLedger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $products = Product::withRatingStats()
            ->with(['category', 'brand', 'images'])
            ->when($request->filled('q'), fn ($query) => $query->where('name', 'like', '%'.$request->q.'%'))
            ->when($request->filled('category'), fn ($query) => $query->where('category_id', $request->category))
            ->latest()
            ->get();

        $categories = Category::orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();
        $brands = Brand::orderBy('name')->get();

        return view('admin.products.create', compact('categories', 'brands'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateProduct($request);

        $data['slug'] = Product::generateUniqueSlug($data['name']);
        $data['is_active'] = $request->boolean('is_active');
        $data['specs'] = $this->buildSpecs($request);

        // One transaction so a failure while saving images/variants can't leave a half-created product behind.
        DB::transaction(function () use ($data, $request) {
            $product = Product::create($data);

            $this->storeImages($product, $request);

            // update 1: stok awal produk dicatat di riwayat stok
            if (! $this->syncVariants($product, $request)) {
                StockLedger::record($product, 0, (int) $product->stock, 'initial');
            }
        });

        DiscountSync::run();

        return redirect()->route('admin.products.index')->with('success', 'Product created.');
    }

    public function edit(Product $product): View
    {
        $categories = Category::orderBy('name')->get();
        $brands = Brand::orderBy('name')->get();
        $product->load(['images', 'variants']);

        return view('admin.products.edit', compact('product', 'categories', 'brands'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $this->validateProduct($request);

        if ($product->name !== $data['name']) {
            $data['slug'] = Product::generateUniqueSlug($data['name'], $product->id);
        }

        $data['is_active'] = $request->boolean('is_active');
        $data['specs'] = $this->buildSpecs($request);

        DB::transaction(function () use ($product, $data, $request) {
            // update 1: edit stok manual dicatat di riwayat stok
            // Stok produk bervarian adalah jumlah stok varian, jadi yang dicatat hanya stok varian.
            $hadVariants = $product->variants()->exists();
            $stockBefore = (int) $product->stock;

            $product->update($data);

            $this->storeImages($product, $request);

            if (! $this->syncVariants($product, $request)) {
                // Varian yang baru dihapus semua dihitung stoknya nol sebelum diisi angka dari form.
                StockLedger::record($product, $hadVariants ? 0 : $stockBefore, (int) $product->stock, 'manual');
            }
        });

        // Price changes (or a category/brand change) can alter which discount wins for this product.
        DiscountSync::run();

        return redirect()->route('admin.products.index')->with('success', 'Product updated.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        foreach ($product->images as $image) {
            $this->deleteLocalImage($image->image);
        }

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product deleted.');
    }

    private function validateProduct(Request $request): array
    {
        return $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'brand_id' => ['nullable', 'exists:brands,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0', 'lt:price'],
            'stock' => ['required', 'integer', 'min:0'],
            'weight' => ['required', 'integer', 'min:0'],
            'images.*' => ['nullable', 'image', 'max:4096'],
            'variants' => ['nullable', 'array'],
            'variants.*.id' => ['nullable', 'integer'],
            'variants.*.label' => ['nullable', 'string', 'max:255'],
            'variants.*.sku' => ['nullable', 'string', 'max:100'],
            'variants.*.price' => ['nullable', 'numeric', 'min:0'],
            'variants.*.sale_price' => ['nullable', 'numeric', 'min:0'],
            'variants.*.has_warranty' => ['nullable', 'boolean'],
            'variants.*.stock' => ['nullable', 'integer', 'min:0'],
            'variants.*.weight' => ['nullable', 'integer', 'min:0'],
            'specs' => ['nullable', 'array'],
            'specs.*.label' => ['nullable', 'string', 'max:100'],
            'specs.*.value' => ['nullable', 'string', 'max:255'],
        ]);
    }

    /**
     * Turns the submitted label/value rows into the compact array stored on
     * the product, dropping any row the admin left half-empty.
     */
    private function buildSpecs(Request $request): ?array
    {
        $specs = collect($request->input('specs', []))
            ->filter(fn ($row) => filled($row['label'] ?? null) && filled($row['value'] ?? null))
            ->map(fn ($row) => ['label' => $row['label'], 'value' => $row['value']])
            ->values()
            ->all();

        return $specs ?: null;
    }

    /**
     * Creates/updates/deletes the product's variant rows to match what was submitted, then
     * rolls the variant totals back up into the product's own price/stock/weight so every
     * listing/filter/sort query that reads those columns directly keeps working unchanged.
     */
    private function syncVariants(Product $product, Request $request): bool
    {
        $rows = collect($request->input('variants', []))
            ->filter(fn ($row) => filled($row['label'] ?? null) && filled($row['price'] ?? null));

        $keepIds = [];

        foreach ($rows->values() as $index => $row) {
            $payload = [
                'label' => $row['label'],
                'sku' => ($row['sku'] ?? null) ?: null,
                'price' => $row['price'],
                'sale_price' => filled($row['sale_price'] ?? null) ? $row['sale_price'] : null,
                'has_warranty' => filled($row['has_warranty'] ?? null),
                'stock' => $row['stock'] ?? 0,
                'weight' => $row['weight'] ?? 0,
                'sort_order' => $index,
            ];

            $variant = ! empty($row['id']) ? $product->variants()->whereKey($row['id'])->first() : null;

            // update 1: stok varian (baru, diedit, atau dihapus) dicatat di riwayat stok
            if ($variant) {
                $before = (int) $variant->stock;
                $variant->update($payload);
                StockLedger::record($variant, $before, (int) $variant->stock, 'manual');
            } else {
                $variant = $product->variants()->create($payload);
                StockLedger::record($variant, 0, (int) $variant->stock, 'initial');
            }

            $keepIds[] = $variant->id;
        }

        // Varian yang dihapus membawa stoknya pergi; catat sebagai pengurangan.
        foreach ($product->variants()->whereNotIn('id', $keepIds)->get() as $removed) {
            StockLedger::record($removed, (int) $removed->stock, 0, 'manual', null, 'Varian dihapus oleh '.(auth()->user()?->name ?? 'admin'));
            $removed->delete();
        }

        if (! empty($keepIds)) {
            $product->syncFromVariants();
        }

        return ! empty($keepIds);
    }

    private function storeImages(Product $product, Request $request): void
    {
        if (! $request->hasFile('images')) {
            return;
        }

        $sortOrder = $product->images()->max('sort_order') + 1;

        foreach ($request->file('images') as $file) {
            $product->images()->create([
                'image' => Storage::url($file->store('products', 'public')),
                'sort_order' => $sortOrder++,
            ]);
        }
    }

    private function deleteLocalImage(?string $image): void
    {
        if ($image && str_starts_with($image, '/storage/')) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $image));
        }
    }
}
