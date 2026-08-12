<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ContactMessageController extends Controller
{
    public function index(): View
    {
        $messages = ContactMessage::latest()->get();

        return view('admin.contact-messages.index', compact('messages'));
    }

    public function show(ContactMessage $contact_message): View
    {
        if (! $contact_message->is_read) {
            $contact_message->update(['is_read' => true]);
        }

        return view('admin.contact-messages.show', ['message' => $contact_message]);
    }

    public function destroy(ContactMessage $contact_message): RedirectResponse
    {
        $contact_message->delete();

        return redirect()->route('admin.contact-messages.index')->with('success', 'Message deleted.');
    }
}
