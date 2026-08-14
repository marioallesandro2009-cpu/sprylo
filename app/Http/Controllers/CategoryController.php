<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\PageMeta;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $pageMeta = PageMeta::forPage('categories');
        $allCategories = Category::withCount(['products' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('name')
            ->get();

        return view('pages.categories', compact('pageMeta', 'allCategories'));
    }
}
