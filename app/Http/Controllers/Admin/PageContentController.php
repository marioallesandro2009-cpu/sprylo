<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageContent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PageContentController extends Controller
{
    public function index(Request $request): View
    {
        $groups = PageContent::orderBy('page')->orderBy('sort_order')->get()->groupBy('page');
        $requested = (string) $request->query('page', '');
        $active = $groups->has($requested) ? $requested : $groups->keys()->first();

        return view('admin.content.index', compact('groups', 'active'));
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'page' => ['required', 'string', 'max:60'],
            'content' => ['nullable', 'array'],
            'content.*' => ['nullable', 'string', 'max:5000'],
            'files' => ['nullable', 'array'],
            'files.*' => ['nullable', 'image', 'max:4096'],
            'reset' => ['nullable', 'array'],
        ]);

        $rows = PageContent::where('page', $request->input('page'))->get();

        // Validasi URL dulu supaya tidak ada simpan setengah-setengah.
        $errors = [];

        foreach ($rows as $row) {
            if (! in_array($row->type, ['url', 'image'], true)) {
                continue;
            }

            $value = trim((string) $request->input("content.{$row->id}"));

            if ($value !== '' && ! preg_match('#^(/|\#|https?://|mailto:|tel:)#i', $value)) {
                $errors["content.{$row->id}"] = "{$row->label}: harus diawali /, #, http(s)://, mailto: atau tel:.";
            }
        }

        if ($errors) {
            throw ValidationException::withMessages($errors);
        }

        foreach ($rows as $row) {
            $id = $row->id;

            if ($request->boolean("reset.{$id}")) {
                $row->update(['value' => null]);

                continue;
            }

            if ($row->type === 'image' && $request->hasFile("files.{$id}")) {
                $row->update(['value' => Storage::url($request->file("files.{$id}")->store('content', 'public'))]);

                continue;
            }

            if (! $request->has("content.{$id}")) {
                continue;
            }

            $value = trim((string) $request->input("content.{$id}"));
            $row->update(['value' => $value === '' ? null : $value]);
        }

        PageContent::flush();

        return redirect()
            ->route('admin.content.index', ['page' => $request->input('page')])
            ->with('success', 'Konten halaman berhasil disimpan.');
    }

    public function scan(Request $request): RedirectResponse
    {
        Artisan::call('cms:scan', ['--prune' => $request->boolean('prune')]);

        return redirect()
            ->route('admin.content.index', ['page' => $request->input('page')])
            ->with('success', trim(Artisan::output()) ?: 'Scan selesai.');
    }
}