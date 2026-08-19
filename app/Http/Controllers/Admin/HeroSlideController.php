<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroSlide;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class HeroSlideController extends Controller
{
    public function index(): View
    {
        $slides = HeroSlide::orderBy('sort_order')->get();

        return view('admin.hero-slides.index', compact('slides'));
    }

    public function create(): View
    {
        $products = Product::orderBy('name')->get();

        return view('admin.hero-slides.create', compact('products'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateSlide($request, requireImage: true);

        if ($request->hasFile('image')) {
            $data['image'] = Storage::url($request->file('image')->store('hero', 'public'));
        }

        HeroSlide::create($data);

        return redirect()->route('admin.hero-slides.index')->with('success', 'Hero slide added.');
    }

    public function edit(HeroSlide $heroSlide): View
    {
        $products = Product::orderBy('name')->get();

        return view('admin.hero-slides.edit', ['slide' => $heroSlide, 'products' => $products]);
    }

    public function update(Request $request, HeroSlide $heroSlide): RedirectResponse
    {
        $data = $this->validateSlide($request, requireImage: false);

        if ($request->hasFile('image')) {
            if ($heroSlide->image && str_starts_with($heroSlide->image, '/storage/')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $heroSlide->image));
            }
            $data['image'] = Storage::url($request->file('image')->store('hero', 'public'));
        }

        $heroSlide->update($data);

        return redirect()->route('admin.hero-slides.index')->with('success', 'Hero slide updated.');
    }

    public function destroy(HeroSlide $heroSlide): RedirectResponse
    {
        if ($heroSlide->image && str_starts_with($heroSlide->image, '/storage/')) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $heroSlide->image));
        }

        $heroSlide->delete();

        return redirect()->route('admin.hero-slides.index')->with('success', 'Hero slide removed.');
    }

    private function validateSlide(Request $request, bool $requireImage): array
    {
        $validated = $request->validate([
            'size' => ['required', 'in:'.implode(',', array_keys(HeroSlide::SIZES))],
            'theme' => ['required', 'in:'.implode(',', array_keys(HeroSlide::THEMES))],
            'eyebrow' => ['required', 'string', 'max:255'],
            'heading' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'cta_label' => ['nullable', 'string', 'max:60'],
            'product_id' => ['nullable', 'exists:products,id'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'image' => [$requireImage ? 'required' : 'nullable', 'image', 'max:4096'],
        ]);

        unset($validated['image']);

        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['is_active'] = $request->boolean('is_active');

        return $validated;
    }
}
