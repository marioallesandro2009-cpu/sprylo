<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::withCount('products')->orderBy('name')->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('admin.categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('categories', 'name')],
            'image' => ['nullable', 'image', 'max:4096'],
        ]);

        $data['slug'] = Category::generateUniqueSlug($data['name']);

        if ($request->hasFile('image')) {
            $data['image'] = Storage::url($request->file('image')->store('categories', 'public'));
        }

        Category::create($data);

        return redirect()->route('admin.categories.index')->with('success', 'Category created.');
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('categories', 'name')->ignore($category->id)],
            'image' => ['nullable', 'image', 'max:4096'],
        ]);

        if ($category->name !== $data['name']) {
            $data['slug'] = Category::generateUniqueSlug($data['name'], $category->id);
        }

        if ($request->hasFile('image')) {
            $this->deleteLocalImage($category->image);
            $data['image'] = Storage::url($request->file('image')->store('categories', 'public'));
        }

        $category->update($data);

        return redirect()->route('admin.categories.index')->with('success', 'Category updated.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->products()->exists()) {
            return back()->with('error', 'Cannot delete a category that still has products.');
        }

        $this->deleteLocalImage($category->image);
        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Category deleted.');
    }

    private function deleteLocalImage(?string $image): void
    {
        if ($image && str_starts_with($image, '/storage/')) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $image));
        }
    }
}
