<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VoucherRule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class VoucherRuleController extends Controller
{
    public function index(): View
    {
        $rules = VoucherRule::withCount(['vouchers as used_vouchers' => fn ($q) => $q->where('used_count', '>', 0)])->latest()->get();

        return view('admin.voucher-rules.index', compact('rules'));
    }

    public function create(): View
    {
        return view('admin.voucher-rules.create');
    }

    public function store(Request $request): RedirectResponse
    {
        VoucherRule::create($this->validated($request));

        return redirect()->route('admin.voucher-rules.index')->with('success', 'Voucher rule created.');
    }

    public function edit(VoucherRule $voucherRule): View
    {
        return view('admin.voucher-rules.edit', ['rule' => $voucherRule]);
    }

    public function update(Request $request, VoucherRule $voucherRule): RedirectResponse
    {
        $voucherRule->update($this->validated($request));

        return redirect()->route('admin.voucher-rules.index')->with('success', 'Voucher rule updated.');
    }

    public function destroy(VoucherRule $voucherRule): RedirectResponse
    {
        // Vouchers already handed out keep working; they just lose their link to the rule.
        $voucherRule->delete();

        return redirect()->route('admin.voucher-rules.index')->with('success', 'Voucher rule deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'trigger' => ['required', Rule::in(array_keys(VoucherRule::TRIGGERS))],
            'milestone_every' => [$request->input('trigger') === 'order_milestone' ? 'required' : 'nullable', 'integer', 'min:2', 'max:1000'],
            'type' => ['required', Rule::in(['percent', 'fixed'])],
            'value' => ['required', 'numeric', 'gt:0', $request->input('type') === 'percent' ? 'max:100' : 'max:999999999'],
            'min_subtotal' => ['nullable', 'numeric', 'min:0'],
            'valid_days' => ['required', 'integer', 'min:1', 'max:365'],
        ]);

        $data['milestone_every'] = $data['trigger'] === 'order_milestone' ? $data['milestone_every'] : null;
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
