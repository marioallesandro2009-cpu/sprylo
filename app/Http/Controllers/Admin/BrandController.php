<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BrandController extends Controller
{
    public function index(): View
    {
        $brands = Brand::withCount('products')->orderBy('sort_order')->get();

        return view('admin.brands.index', compact('brands'));
    }

    public function create(): View
    {
        return view('admin.brands.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateBrand($request);
        $data['slug'] = Brand::generateUniqueSlug($data['name']);

        if ($request->hasFile('logo')) {
            $data['logo'] = Storage::url($request->file('logo')->store('brands', 'public'));
        }

        Brand::create($data);

        return redirect()->route('admin.brands.index')->with('success', 'Brand created.');
    }

    public function edit(Brand $brand): View
    {
        return view('admin.brands.edit', compact('brand'));
    }

    public function update(Request $request, Brand $brand): RedirectResponse
    {
        $data = $this->validateBrand($request, $brand);

        if ($brand->name !== $data['name']) {
            $data['slug'] = Brand::generateUniqueSlug($data['name'], $brand->id);
        }

        if ($request->hasFile('logo')) {
            $this->deleteLocalImage($brand->logo);
            $data['logo'] = Storage::url($request->file('logo')->store('brands', 'public'));
        }

        $brand->update($data);

        return redirect()->route('admin.brands.index')->with('success', 'Brand updated.');
    }

    public function destroy(Brand $brand): RedirectResponse
    {
        if ($brand->products()->exists()) {
            return back()->with('error', 'Cannot delete a brand that still has products.');
        }

        $this->deleteLocalImage($brand->logo);
        $brand->delete();

        return redirect()->route('admin.brands.index')->with('success', 'Brand deleted.');
    }

    private function validateBrand(Request $request, ?Brand $brand = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('brands', 'name')->ignore($brand?->id)],
            'website_url' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer'],
            'logo' => ['nullable', 'image', 'max:4096'],
        ]);
    }

    private function deleteLocalImage(?string $image): void
    {
        if ($image && str_starts_with($image, '/storage/')) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $image));
        }
    }
}
