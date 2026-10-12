<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NewsletterSubscriberController extends Controller
{
    public function index(Request $request): View
    {
        $subscribers = NewsletterSubscriber::query()
            ->when($request->filled('q'), fn ($q) => $q->where('email', 'like', '%'.$request->q.'%'))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.newsletter-subscribers.index', compact('subscribers'));
    }

    public function export(): StreamedResponse
    {
        $filename = 'newsletter-subscribers-'.now()->format('Ymd').'.csv';

        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['email', 'subscribed_at'], ',', '"', '\\');

            NewsletterSubscriber::orderBy('id')->chunk(500, function ($rows) use ($out) {
                foreach ($rows as $row) {
                    $email = preg_match('/^[=+\-@]/', $row->email) ? "'".$row->email : $row->email;
                    fputcsv($out, [$email, $row->created_at], ',', '"', '\\');
                }
            });

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function destroy(NewsletterSubscriber $newsletterSubscriber): RedirectResponse
    {
        $newsletterSubscriber->delete();

        return redirect()->route('admin.newsletter-subscribers.index')->with('success', 'Subscriber removed.');
    }
}