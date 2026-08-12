<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(): View
    {
        $reviews = Review::with('product')->latest()->get();

        return view('admin.reviews.index', compact('reviews'));
    }

    public function create(): View
    {
        $products = Product::orderBy('name')->get();

        return view('admin.reviews.create', compact('products'));
    }

    public function store(Request $request): RedirectResponse
    {
        Review::create($this->validateReview($request));

        return redirect()->route('admin.reviews.index')->with('success', 'Review created.');
    }

    public function edit(Review $review): View
    {
        $products = Product::orderBy('name')->get();

        return view('admin.reviews.edit', compact('review', 'products'));
    }

    public function update(Request $request, Review $review): RedirectResponse
    {
        $review->update($this->validateReview($request));

        return redirect()->route('admin.reviews.index')->with('success', 'Review updated.');
    }

    public function destroy(Review $review): RedirectResponse
    {
        $review->delete();

        return redirect()->route('admin.reviews.index')->with('success', 'Review deleted.');
    }

    private function validateReview(Request $request): array
    {
        return $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'name' => ['required', 'string', 'max:255'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string'],
        ]);
    }
}
