<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\PageMeta;
use Illuminate\View\View;

class BrandController extends Controller
{
    public function index(): View
    {
        $pageMeta = PageMeta::forPage('brands');
        $allBrands = Brand::withCount(['products' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('sort_order')
            ->get();

        return view('pages.brands', compact('pageMeta', 'allBrands'));
    }
}
