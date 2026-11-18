<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UiTextController extends Controller
{
    public function index(): View
    {
        $texts = Setting::orderBy('id')->get()
            ->filter(fn ($setting) => str_starts_with($setting->key, 'ui_'))
            ->values();

        $groups = $texts->groupBy(fn ($setting) => explode('_', $setting->key)[1] ?? 'other');

        return view('admin.ui-texts.index', compact('groups'));
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'texts' => ['array'],
            'texts.*' => ['nullable', 'string', 'max:2000'],
        ]);

        $posted = $request->input('texts', []);

        Setting::orderBy('id')->get()
            ->filter(fn ($setting) => str_starts_with($setting->key, 'ui_') && array_key_exists($setting->key, $posted))
            ->each(fn ($setting) => $setting->update(['value' => $posted[$setting->key]]));

        return redirect()->route('admin.ui-texts.index')->with('success', 'Page texts updated.');
    }
}
