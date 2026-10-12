<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageMeta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageMetaController extends Controller
{
    public function index(): View
    {
        $pages = PageMeta::orderBy('page_slug')->get();

        return view('admin.page-meta.index', compact('pages'));
    }

    public function edit(PageMeta $pageMeta): View
    {
        return view('admin.page-meta.edit', compact('pageMeta'));
    }

    public function update(Request $request, PageMeta $pageMeta): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
        ]);

        $pageMeta->update($data);

        return redirect()->route('admin.page-meta.index')->with('success', 'Page SEO updated.');
    }
}