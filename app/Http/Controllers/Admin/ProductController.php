<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

        $product = Product::create($data);

        $this->storeImages($product, $request);
        $this->syncVariants($product, $request);

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

        $product->update($data);

        $this->storeImages($product, $request);
        $this->syncVariants($product, $request);

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
    private function syncVariants(Product $product, Request $request): void
    {
        $rows = collect($request->input('variants', []))
            ->filter(fn ($row) => filled($row['label'] ?? null) && filled($row['price'] ?? null));

        $keepIds = [];

        foreach ($rows->values() as $index => $row) {
            $payload = [
                'label' => $row['label'],
                'sku' => $row['sku'] ?: null,
                'price' => $row['price'],
                'sale_price' => filled($row['sale_price'] ?? null) ? $row['sale_price'] : null,
                'has_warranty' => filled($row['has_warranty'] ?? null),
                'stock' => $row['stock'] ?? 0,
                'weight' => $row['weight'] ?? 0,
                'sort_order' => $index,
            ];

            $variant = ! empty($row['id']) ? $product->variants()->whereKey($row['id'])->first() : null;

            $variant
                ? $variant->update($payload)
                : $variant = $product->variants()->create($payload);

            $keepIds[] = $variant->id;
        }

        $product->variants()->whereNotIn('id', $keepIds)->delete();

        if (! empty($keepIds)) {
            $product->syncFromVariants();
        }
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
