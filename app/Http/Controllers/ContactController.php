<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\Faq;
use App\Models\PageMeta;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function index(): View
    {
        $pageMeta = PageMeta::forPage('contact');
        $faqs = Faq::orderBy('sort_order')->get();

        return view('pages.contact', compact('pageMeta', 'faqs'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'topic' => ['nullable', 'string', 'max:255'],
            'order_number' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string'],
        ]);

        ContactMessage::create($data);

        $message = Setting::where('key', 'contact_reply_sla_text')->value('value')
            ?? 'Message sent — we will reply within 4 hours.';

        return redirect()->route('contact.index')->with('success', $message);
    }
}
