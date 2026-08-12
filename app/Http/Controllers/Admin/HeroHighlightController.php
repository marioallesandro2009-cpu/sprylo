<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroHighlight;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class HeroHighlightController extends Controller
{
    public function index(): View
    {
        $slides = HeroHighlight::all()->keyBy('position');
        $products = Product::orderBy('name')->get();

        return view('admin.hero-highlights.index', compact('slides', 'products'));
    }

    public function update(Request $request): RedirectResponse
    {
        // Validate every slide up front so a bad value on one slide can't
        // leave the others partially saved.
        $rules = [];
        foreach (array_keys(HeroHighlight::POSITIONS) as $position) {
            $rules["{$position}.eyebrow"] = ['required', 'string', 'max:255'];
            $rules["{$position}.heading"] = ['required', 'string', 'max:255'];
            $rules["{$position}.product_id"] = ['nullable', 'exists:products,id'];
            $rules["{$position}.image"] = ['nullable', 'image', 'max:4096'];
        }
        $validated = $request->validate($rules);

        DB::transaction(function () use ($request, $validated) {
            foreach ($validated as $position => $data) {
                $slide = HeroHighlight::where('position', $position)->first();

                $update = [
                    'eyebrow' => $data['eyebrow'],
                    'heading' => $data['heading'],
                    'product_id' => $data['product_id'] ?? null,
                ];

                if ($request->hasFile("{$position}.image")) {
                    if ($slide?->image && str_starts_with($slide->image, '/storage/')) {
                        Storage::disk('public')->delete(str_replace('/storage/', '', $slide->image));
                    }
                    $update['image'] = Storage::url($request->file("{$position}.image")->store('hero', 'public'));
                }

                HeroHighlight::updateOrCreate(['position' => $position], $update);
            }
        });

        return redirect()->route('admin.hero-highlights.index')->with('success', 'Hero highlights updated.');
    }
}
