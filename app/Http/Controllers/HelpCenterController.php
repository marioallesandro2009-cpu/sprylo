<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\PageMeta;
use Illuminate\View\View;

class HelpCenterController extends Controller
{
    public function index(): View
    {
        $pageMeta = PageMeta::forPage('help');
        $faqs = Faq::orderBy('sort_order')->get();

        return view('pages.help', compact('pageMeta', 'faqs'));
    }
}
