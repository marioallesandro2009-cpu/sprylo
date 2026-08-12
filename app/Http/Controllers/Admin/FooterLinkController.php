<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FooterLink;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FooterLinkController extends Controller
{
    public function index(): View
    {
        $footerLinks = FooterLink::orderBy('group')->orderBy('sort_order')->get();

        return view('admin.footer-links.index', compact('footerLinks'));
    }

    public function create(): View
    {
        return view('admin.footer-links.create');
    }

    public function store(Request $request): RedirectResponse
    {
        FooterLink::create($this->validateLink($request));

        return redirect()->route('admin.footer-links.index')->with('success', 'Footer link created.');
    }

    public function edit(FooterLink $footerLink): View
    {
        return view('admin.footer-links.edit', ['link' => $footerLink]);
    }

    public function update(Request $request, FooterLink $footerLink): RedirectResponse
    {
        $footerLink->update($this->validateLink($request));

        return redirect()->route('admin.footer-links.index')->with('success', 'Footer link updated.');
    }

    public function destroy(FooterLink $footerLink): RedirectResponse
    {
        $footerLink->delete();

        return redirect()->route('admin.footer-links.index')->with('success', 'Footer link deleted.');
    }

    private function validateLink(Request $request): array
    {
        return $request->validate([
            'group' => ['required', 'in:'.implode(',', array_keys(FooterLink::GROUPS))],
            'label' => ['required', 'string', 'max:255'],
            'url' => ['required', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer'],
        ]);
    }
}
