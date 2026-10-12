<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NewsletterController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email:rfc', 'max:255'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withFragment('newsletter')
                ->withErrors($validator, 'newsletter')->withInput();
        }

        $subscriber = NewsletterSubscriber::firstOrCreate([
            'email' => strtolower(trim($validator->validated()['email'])),
        ]);

        $message = $subscriber->wasRecentlyCreated
            ? Setting::get('newsletter_success_text', 'Thanks for subscribing!')
            : "You're already on the list — thanks!";

        return redirect()->back()->withFragment('newsletter')->with('newsletter_success', $message);
    }
}