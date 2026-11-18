<?php

namespace App\Http\Controllers;

use App\Models\PageMeta;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class CompareController extends Controller
{
    /** More columns than this don't fit on screen and stop being comparable. */
    public const MAX_ITEMS = 4;

    public function index(): View
    {
        $pageMeta = PageMeta::forPage('compare');

        $ids = session('compare', []);
        $products = Product::withRatingStats()
            ->with(['images', 'category', 'brand', 'variants'])
            ->where('is_active', true)
            ->whereIn('id', $ids)
            ->get()
            ->sortBy(fn (Product $product) => array_search($product->id, $ids))
            ->values();

        // Drop anything that has since been deactivated/deleted so the list and the bar stay honest.
        if ($products->pluck('id')->all() !== $ids) {
            session(['compare' => $products->pluck('id')->all()]);
        }

        [$overview, $specs] = $this->buildRows($products);

        $cheapestId = $products->count() > 1 ? $products->sortBy(fn ($p) => (float) ($p->sale_price ?? $p->price))->first()->id : null;
        $topRatedId = $products->count() > 1 && $products->max('rating_avg') > 0 ? $products->sortByDesc('rating_avg')->first()->id : null;

        return view('pages.compare', [
            'pageMeta' => $pageMeta,
            'products' => $products,
            'overview' => $overview,
            'specs' => $specs,
            'cheapestId' => $cheapestId,
            'topRatedId' => $topRatedId,
            'max' => self::MAX_ITEMS,
        ]);
    }

    /** Adds the product to the comparison, or removes it when it is already in there. */
    public function toggle(Product $product): RedirectResponse
    {
        $ids = session('compare', []);

        if (in_array($product->id, $ids, true)) {
            session(['compare' => array_values(array_diff($ids, [$product->id]))]);

            return back()->with('success', $product->name.' removed from comparison.');
        }

        if (! $product->is_active) {
            return back()->with('error', 'This product is no longer available.');
        }

        if (count($ids) >= self::MAX_ITEMS) {
            return back()->with('warning', 'You can compare up to '.self::MAX_ITEMS.' products. Remove one first.');
        }

        $ids[] = $product->id;
        session(['compare' => $ids]);

        return back()->with('success', $product->name.' added to comparison.');
    }

    public function remove(Product $product): RedirectResponse
    {
        session(['compare' => array_values(array_diff(session('compare', []), [$product->id]))]);

        return back();
    }

    public function clear(): RedirectResponse
    {
        session()->forget('compare');

        return redirect()->route('compare.index')->with('success', 'Comparison cleared.');
    }

    /**
     * Builds the comparison table: a few fixed overview rows plus one row per spec label
     * found on any of the products (a product without that spec shows a dash).
     *
     * @return array{0: Collection<int, array>, 1: Collection<int, array>}
     */
    private function buildRows(Collection $products): array
    {
        $none = '—';

        $overview = collect([
            ['label' => 'Brand', 'values' => $products->map(fn ($p) => $p->brand->name ?? $none)],
            ['label' => 'Category', 'values' => $products->map(fn ($p) => $p->category->name)],
            ['label' => 'Rating', 'values' => $products->map(fn ($p) => $p->reviews_count > 0 ? $p->rating_avg.' / 5 ('.$p->reviews_count.')' : 'No reviews yet')],
            ['label' => 'Availability', 'values' => $products->map(fn ($p) => $p->stock > 0 ? $p->stock.' in stock' : 'Sold out')],
            ['label' => 'Weight', 'values' => $products->map(fn ($p) => weight_label($p->weight))],
            ['label' => 'Options', 'values' => $products->map(fn ($p) => $p->variants->isNotEmpty() ? $p->variants->pluck('label')->implode(', ') : $none)],
        ]);

        $labels = $products->flatMap(fn ($p) => collect($p->specs ?? [])->pluck('label'))->unique(fn ($label) => mb_strtolower($label))->values();

        $specs = $labels->map(function ($label) use ($products, $none) {
            return [
                'label' => $label,
                'values' => $products->map(function ($p) use ($label, $none) {
                    $match = collect($p->specs ?? [])->first(fn ($spec) => mb_strtolower($spec['label']) === mb_strtolower($label));

                    return $match['value'] ?? $none;
                }),
            ];
        });

        $flagDifferences = fn (array $row) => $row + ['same' => $row['values']->unique(fn ($v) => mb_strtolower((string) $v))->count() <= 1];

        return [$overview->map($flagDifferences), $specs->map($flagDifferences)];
    }
}
