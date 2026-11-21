<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Discount;
use App\Models\Product;
use App\Support\DiscountSync;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Serves both admin pages: scheduled discounts (admin.discounts.*) and flash sales
 * (admin.flash-sales.*). A flash sale is a discount with a hard end time and a unit quota.
 */
class DiscountController extends Controller
{
    public function index(Request $request): View
    {
        $flash = $this->isFlash($request);
        $statusOrder = ['live' => 0, 'scheduled' => 1, 'sold_out' => 2, 'ended' => 3, 'inactive' => 4];

        $all = Discount::with(['product', 'variant', 'category', 'brand'])
            ->where('is_flash', $flash)
            ->latest()
            ->get()
            ->sortBy(fn (Discount $discount) => $statusOrder[$discount->status] ?? 9)
            ->values();

        // update 4: ringkasan, tab status dengan jumlah, dan filter status
        $counts = $all->countBy(fn (Discount $discount) => $discount->status);
        $status = (string) $request->query('status', '');
        $discounts = isset($statusOrder[$status]) ? $all->where('status', $status)->values() : $all;

        $stats = [
            'live' => (int) ($counts['live'] ?? 0),
            'scheduled' => (int) ($counts['scheduled'] ?? 0),
            'sold' => (int) $all->sum('sold'),
            // Sisa unit hanya dari yang sedang live dan punya kuota.
            'remaining' => (int) $all->where('status', 'live')->sum(fn (Discount $discount) => $discount->remaining ?? 0),
        ];

        return view('admin.discounts.index', $this->viewData($flash) + compact('discounts', 'counts', 'stats', 'status') + ['total' => $all->count()]);
    }

    public function create(Request $request): View
    {
        return view('admin.discounts.create', $this->viewData($this->isFlash($request)) + $this->options());
    }

    public function store(Request $request): RedirectResponse
    {
        $flash = $this->isFlash($request);
        Discount::create($this->validated($request, $flash));
        DiscountSync::run();

        return redirect()->route($this->viewData($flash)['routePrefix'].'.index')
            ->with('success', ($flash ? 'Flash sale' : 'Discount').' created.');
    }

    public function edit(Request $request, Discount $discount): View
    {
        $flash = $this->isFlash($request);
        abort_if($discount->is_flash !== $flash, 404);

        return view('admin.discounts.edit', $this->viewData($flash) + $this->options() + compact('discount'));
    }

    public function update(Request $request, Discount $discount): RedirectResponse
    {
        $flash = $this->isFlash($request);
        abort_if($discount->is_flash !== $flash, 404);

        $discount->update($this->validated($request, $flash));
        DiscountSync::run();

        return redirect()->route($this->viewData($flash)['routePrefix'].'.index')
            ->with('success', ($flash ? 'Flash sale' : 'Discount').' updated.');
    }

    public function destroy(Request $request, Discount $discount): RedirectResponse
    {
        $flash = $this->isFlash($request);
        abort_if($discount->is_flash !== $flash, 404);

        $discount->delete();
        DiscountSync::run();

        return redirect()->route($this->viewData($flash)['routePrefix'].'.index')
            ->with('success', ($flash ? 'Flash sale' : 'Discount').' deleted.');
    }

    private function isFlash(Request $request): bool
    {
        return $request->routeIs('admin.flash-sales.*');
    }

    private function viewData(bool $flash): array
    {
        return [
            'flash' => $flash,
            'routePrefix' => $flash ? 'admin.flash-sales' : 'admin.discounts',
            'noun' => $flash ? 'Flash Sale' : 'Scheduled Discount',
        ];
    }

    /** Pickers for the form; variants ride along so the variant dropdown can follow the chosen product. */
    private function options(): array
    {
        return [
            // update 4: harga ikut dimuat untuk pratinjau harga setelah diskon di form
            'products' => Product::with('variants:id,product_id,label,price')->orderBy('name')->get(['id', 'name', 'price']),
            'categories' => Category::orderBy('name')->get(['id', 'name']),
            'brands' => Brand::orderBy('name')->get(['id', 'name']),
        ];
    }

    private function validated(Request $request, bool $flash): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            // Flash sale quotas are per rule, so a flash sale always targets a single product.
            'scope' => ['required', $flash ? 'in:product' : 'in:'.implode(',', array_keys(Discount::SCOPES))],
            'product_id' => ['required_if:scope,product', 'nullable', 'exists:products,id'],
            'product_variant_id' => ['nullable', 'exists:product_variants,id'],
            'category_id' => ['required_if:scope,category', 'nullable', 'exists:categories,id'],
            'brand_id' => ['required_if:scope,brand', 'nullable', 'exists:brands,id'],
            'type' => ['required', 'in:'.implode(',', array_keys(Discount::TYPES))],
            'value' => ['required', 'numeric', 'gt:0', $request->input('type') === 'percent' ? 'max:99' : 'max:999999999'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => array_filter([$flash ? 'required' : 'nullable', 'date', $request->filled('starts_at') ? 'after:starts_at' : null]),
            'quota' => [$flash ? 'required' : 'nullable', 'integer', 'min:1', 'max:1000000'],
        ]);

        if ($data['scope'] === 'product' && ! empty($data['product_variant_id'])) {
            $belongs = Product::whereKey($data['product_id'])->whereHas('variants', fn ($q) => $q->whereKey($data['product_variant_id']))->exists();

            if (! $belongs) {
                throw ValidationException::withMessages(['product_variant_id' => 'That variant does not belong to the selected product.']);
            }
        }

        // Keep only the target that matches the chosen scope.
        $data['product_id'] = $data['scope'] === 'product' ? $data['product_id'] : null;
        $data['product_variant_id'] = $data['scope'] === 'product' ? ($data['product_variant_id'] ?? null) : null;
        $data['category_id'] = $data['scope'] === 'category' ? $data['category_id'] : null;
        $data['brand_id'] = $data['scope'] === 'brand' ? $data['brand_id'] : null;

        $data['starts_at'] = $this->fromShopTime($data['starts_at'] ?? null);
        $data['ends_at'] = $this->fromShopTime($data['ends_at'] ?? null);
        $data['quota'] = $data['quota'] ?? null;
        $data['is_flash'] = $flash;
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }

    /** Admin types local (Jakarta) time into datetime-local inputs; store it in the app timezone. */
    private function fromShopTime(?string $value): ?Carbon
    {
        return $value ? Carbon::parse($value, Discount::DISPLAY_TZ)->setTimezone(config('app.timezone')) : null;
    }
}
