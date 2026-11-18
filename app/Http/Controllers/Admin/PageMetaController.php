<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageMeta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PageMetaController extends Controller
{
    /** Slugs the storefront controllers look up via PageMeta::forPage(). */
    public const KNOWN_PAGES = [
        'home', 'shop', 'cart', 'checkout', 'contact', 'about', 'categories',
        'brands', 'wishlist', 'help', 'privacy-policy', 'track-order',
    ];

    public function index(): View
    {
        $pages = PageMeta::orderBy('page_slug')->get();

        return view('admin.page-meta.index', compact('pages'));
    }

    public function create(): View
    {
        $missing = array_values(array_diff(self::KNOWN_PAGES, PageMeta::pluck('page_slug')->all()));

        return view('admin.page-meta.create', compact('missing'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'page_slug' => ['required', 'string', 'max:100', 'alpha_dash', Rule::unique('page_meta', 'page_slug')],
            'title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
        ]);

        PageMeta::create($data);

        return redirect()->route('admin.page-meta.index')->with('success', 'Page SEO created.');
    }

    public function edit(PageMeta $pageMeta): View
    {
        return view('admin.page-meta.edit', compact('pageMeta'));
    }

    public function update(Request $request, PageMeta $pageMeta): RedirectResponse
    {
        $data = $request->validate([
            'page_slug' => ['required', 'string', 'max:100', 'alpha_dash', Rule::unique('page_meta', 'page_slug')->ignore($pageMeta->id)],
            'title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
        ]);

        $pageMeta->update($data);

        return redirect()->route('admin.page-meta.index')->with('success', 'Page SEO updated.');
    }

    public function destroy(PageMeta $pageMeta): RedirectResponse
    {
        $pageMeta->delete();

        return redirect()->route('admin.page-meta.index')->with('success', 'Page SEO deleted.');
    }
}
