<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PromoCode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PromoCodeController extends Controller
{
    public function index(): View
    {
        $promoCodes = PromoCode::latest()->get();

        return view('admin.promo-codes.index', compact('promoCodes'));
    }

    public function create(): View
    {
        return view('admin.promo-codes.create');
    }

    public function store(Request $request): RedirectResponse
    {
        PromoCode::create($this->validatePromoCode($request));

        return redirect()->route('admin.promo-codes.index')->with('success', 'Promo code created.');
    }

    public function edit(PromoCode $promoCode): View
    {
        return view('admin.promo-codes.edit', compact('promoCode'));
    }

    public function update(Request $request, PromoCode $promoCode): RedirectResponse
    {
        $promoCode->update($this->validatePromoCode($request, $promoCode));

        return redirect()->route('admin.promo-codes.index')->with('success', 'Promo code updated.');
    }

    public function destroy(PromoCode $promoCode): RedirectResponse
    {
        $promoCode->delete();

        return redirect()->route('admin.promo-codes.index')->with('success', 'Promo code deleted.');
    }

    private function validatePromoCode(Request $request, ?PromoCode $promoCode = null): array
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:promo_codes,code,'.($promoCode?->id ?? 'NULL')],
            'type' => ['required', 'in:percent,fixed'],
            'value' => ['required', 'numeric', 'gt:0', $request->input('type') === 'percent' ? 'max:100' : 'max:999999999'],
            'min_subtotal' => ['nullable', 'numeric', 'min:0'],
            'expires_at' => ['nullable', 'date'],
        ]);

        $data['code'] = strtoupper($data['code']);
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
