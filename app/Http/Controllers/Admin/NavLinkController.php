<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NavLink;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NavLinkController extends Controller
{
    public function index(): View
    {
        $navLinks = NavLink::orderBy('sort_order')->orderBy('id')->get();

        return view('admin.nav-links.index', compact('navLinks'));
    }

    public function create(): View
    {
        return view('admin.nav-links.create');
    }

    public function store(Request $request): RedirectResponse
    {
        NavLink::create($this->validated($request));

        return redirect()->route('admin.nav-links.index')->with('success', 'Menu link created.');
    }

    public function edit(NavLink $navLink): View
    {
        return view('admin.nav-links.edit', ['link' => $navLink]);
    }

    public function update(Request $request, NavLink $navLink): RedirectResponse
    {
        $navLink->update($this->validated($request));

        return redirect()->route('admin.nav-links.index')->with('success', 'Menu link updated.');
    }

    public function destroy(NavLink $navLink): RedirectResponse
    {
        $navLink->delete();

        return redirect()->route('admin.nav-links.index')->with('success', 'Menu link deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'url' => ['required', 'string', 'max:255'],
            'placement' => ['required', 'in:'.implode(',', array_keys(NavLink::PLACEMENTS))],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
