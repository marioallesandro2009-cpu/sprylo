<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\PromoBanner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PromoBannerController extends Controller
{
    public function index(): View
    {
        $banners = PromoBanner::with('product')->orderBy('sort_order')->get();

        return view('admin.promo-banners.index', compact('banners'));
    }

    public function create(): View
    {
        $products = $this->eligibleProducts();

        return view('admin.promo-banners.create', compact('products'));
    }

    public function store(Request $request): RedirectResponse
    {
        PromoBanner::create($this->validateBanner($request));

        return redirect()->route('admin.promo-banners.index')->with('success', 'Promo banner added.');
    }

    public function edit(PromoBanner $promoBanner): View
    {
        $products = $this->eligibleProducts($promoBanner->product_id);

        return view('admin.promo-banners.edit', ['banner' => $promoBanner, 'products' => $products]);
    }

    public function update(Request $request, PromoBanner $promoBanner): RedirectResponse
    {
        $promoBanner->update($this->validateBanner($request));

        return redirect()->route('admin.promo-banners.index')->with('success', 'Promo banner updated.');
    }

    public function destroy(PromoBanner $promoBanner): RedirectResponse
    {
        $promoBanner->delete();

        return redirect()->route('admin.promo-banners.index')->with('success', 'Promo banner removed.');
    }

    /**
     * Only products with a sale price make sense as a discount banner (the
     * % off badge is derived from price vs sale_price), so the picker is
     * scoped to those. The banner's own current product is always included
     * so editing doesn't drop it off the list if its sale price was cleared.
     */
    private function eligibleProducts(?int $keepId = null)
    {
        return Product::whereNotNull('sale_price')
            ->orWhere('id', $keepId)
            ->orderBy('name')
            ->get();
    }

    private function validateBanner(Request $request): array
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'label' => ['required', 'string', 'max:60'],
            'theme' => ['required', 'in:'.implode(',', array_keys(PromoBanner::THEMES))],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['is_active'] = $request->boolean('is_active');

        return $validated;
    }
}
