<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validateWithBag('newsletter', [
            'email' => ['required', 'email', 'max:255'],
        ]);

        NewsletterSubscriber::firstOrCreate(['email' => strtolower($data['email'])]);

        return redirect()->back()
            ->with('newsletter_success', (string) Setting::get('ui_newsletter_success', ''))
            ->withFragment('newsletter');
    }
}
