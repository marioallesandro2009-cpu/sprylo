<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SuspendedCustomer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SuspendedCustomerController extends Controller
{
    public function index(): View
    {
        $suspensions = SuspendedCustomer::with('creator')
            ->orderByRaw('(suspended_until is not null and suspended_until <= ?) asc', [now()])
            ->latest()
            ->get();

        return view('admin.suspended-customers.index', compact('suspensions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['nullable', 'email', 'max:255', 'required_without:phone'],
            'phone' => ['nullable', 'string', 'max:50', 'required_without:email'],
            'reason' => ['nullable', 'string', 'max:255'],
            'suspended_until' => ['nullable', 'date', 'after:now'],
        ]);

        SuspendedCustomer::create([
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'reason' => $data['reason'] ?? null,
            'suspended_until' => $data['suspended_until'] ?? null,
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('admin.suspended-customers.index')->with('success', 'Customer has been suspended from checkout.');
    }

    public function destroy(SuspendedCustomer $suspendedCustomer): RedirectResponse
    {
        $suspendedCustomer->delete();

        return redirect()->route('admin.suspended-customers.index')->with('success', 'Suspension lifted.');
    }
}
