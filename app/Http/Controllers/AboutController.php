<?php

namespace App\Http\Controllers;

use App\Models\PageMeta;
use Illuminate\View\View;

class AboutController extends Controller
{
    public function index(): View
    {
        $pageMeta = PageMeta::forPage('about');

        return view('pages.about', compact('pageMeta'));
    }
}
