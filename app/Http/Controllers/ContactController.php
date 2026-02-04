<?php

/**
 * Public contact page and form handling.
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Handles contact form display and submission (sends notification email).
 */
class ContactController extends Controller
{
    public function show()
    {
        /**
         * Display the contact form page.
         */
        return view('pages.contact');
    }

    public function submit(Request $request)
    {
        /**
         * Validate and process contact form submissions; attempt to email recipients.
         */
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        Log::info('Contact form submission', [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'subject' => $data['subject'] ?? null,
        ]);

        try {
            $recipient = config('mail.contact_to') ?? config('mail.from.address');
            if ($recipient) {
                Mail::raw(
                    "نام: {$data['name']}\nایمیل: {$data['email']}\nتلفن: " . ($data['phone'] ?? '—') . "\nموضوع: " . ($data['subject'] ?? '—') . "\n\n{$data['message']}",
                    function ($message) use ($recipient, $data) {
                        $message->to($recipient)
                            ->subject('پیام جدید از فرم تماس: ' . ($data['subject'] ?? 'بدون موضوع'));
                    }
                );
            }
        } catch (\Throwable $e) {
            Log::error('Contact form mail failed', ['error' => $e->getMessage()]);
        }

        return back()->with('success', 'پیام شما دریافت شد. در اولین فرصت پاسخ می‌دهیم.');
    }
}
