<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(): View
    {
        $admins = User::whereIn('role', ['super_admin', 'admin'])
            ->orderByRaw("role = 'super_admin' desc")
            ->orderBy('name')
            ->get();

        return view('admin.admin-users.index', compact('admins'));
    }

    public function create(): View
    {
        return view('admin.admin-users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        (new User())->forceFill([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => 'admin',
            'status' => 'active',
            'email_verified_at' => now(),
        ])->save();

        return redirect()->route('admin.admin-users.index')->with('success', 'Admin account created.');
    }

    public function edit(User $adminUser): View
    {
        $this->ensureManageable($adminUser);

        return view('admin.admin-users.edit', compact('adminUser'));
    }

    public function update(Request $request, User $adminUser): RedirectResponse
    {
        $this->ensureManageable($adminUser);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$adminUser->id],
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ]);

        $adminUser->name = $data['name'];
        $adminUser->email = $data['email'];

        if (! empty($data['password'])) {
            $adminUser->password = $data['password'];
        }

        $adminUser->save();

        return redirect()->route('admin.admin-users.index')->with('success', 'Admin account updated.');
    }

    public function toggleStatus(User $adminUser): RedirectResponse
    {
        $this->ensureManageable($adminUser);

        if ($adminUser->isActive()) {
            $adminUser->forceFill([
                'status' => 'suspended',
                'status_reason' => 'Suspended by super admin',
                'suspended_until' => null,
            ])->save();

            $message = 'Admin account suspended.';
        } else {
            $adminUser->forceFill([
                'status' => 'active',
                'status_reason' => null,
                'suspended_until' => null,
            ])->save();

            $message = 'Admin account activated.';
        }

        return redirect()->route('admin.admin-users.index')->with('success', $message);
    }

    public function destroy(User $adminUser): RedirectResponse
    {
        $this->ensureManageable($adminUser);

        $adminUser->delete();

        return redirect()->route('admin.admin-users.index')->with('success', 'Admin account deleted.');
    }

    /**
     * Only plain admins can be managed from the UI. Super admins (and customers)
     * are never reachable through these routes.
     */
    private function ensureManageable(User $user): void
    {
        abort_unless($user->role === 'admin', 404);
    }
}