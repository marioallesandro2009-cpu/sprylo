<?php

namespace App\Http\Controllers;

use App\Models\PageMeta;
use Illuminate\View\View;

class PrivacyPolicyController extends Controller
{
    public function index(): View
    {
        $pageMeta = PageMeta::forPage('privacy-policy');

        return view('pages.privacy-policy', compact('pageMeta'));
    }
}
