<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::where('role', 'user')->orderByRaw("status = 'active' desc")->orderBy('name')->get();

        return view('admin.users.index', compact('users'));
    }

    public function suspend(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'status_reason' => ['nullable', 'string', 'max:255'],
            'suspended_until' => ['nullable', 'date', 'after:now'],
        ]);

        $user->update([
            'status' => 'suspended',
            'status_reason' => $data['status_reason'] ?? null,
            'suspended_until' => $data['suspended_until'] ?? null,
        ]);

        return redirect()->route('admin.users.index')->with('success', "{$user->name} has been suspended.");
    }

    public function ban(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'status_reason' => ['nullable', 'string', 'max:255'],
        ]);

        $user->update([
            'status' => 'banned',
            'status_reason' => $data['status_reason'] ?? null,
            'suspended_until' => null,
        ]);

        return redirect()->route('admin.users.index')->with('success', "{$user->name} has been banned.");
    }

    public function activate(User $user): RedirectResponse
    {
        $user->update([
            'status' => 'active',
            'status_reason' => null,
            'suspended_until' => null,
        ]);

        return redirect()->route('admin.users.index')->with('success', "{$user->name} has been reactivated.");
    }
}
