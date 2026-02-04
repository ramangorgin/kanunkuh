<?php

/**
 * Admin ticket management: listing, replying, and status transitions.
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\TicketAttachment;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Morilog\Jalali\Jalalian;

/**
 * Handles administrative actions for support tickets and attachments.
 */
class TicketController extends Controller
{
    /**
     * Initialize controller with notification service and admin middleware.
     */
    public function __construct(private NotificationService $notifications)
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index(Request $request)
    {
        /**
         * Returns a paginated list of tickets with optional filters (status, query, date range).
         */
        $query = Ticket::with(['user.profile', 'latestMessage']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('subject', 'like', "%{$q}%")
                    ->orWhereHas('user.profile', function ($p) use ($q) {
                        $p->where('first_name', 'like', "%{$q}%")
                          ->orWhere('last_name', 'like', "%{$q}%");
                    })
                    ->orWhereHas('user', function ($u) use ($q) {
                        $u->where('phone', 'like', "%{$q}%");
                    });
            });
        }

        if ($request->filled('from')) {
            $from = $this->toGregorianDate($request->from);
            if ($from) {
                $query->whereDate('created_at', '>=', $from);
            }
        }

        if ($request->filled('to')) {
            $to = $this->toGregorianDate($request->to);
            if ($to) {
                $query->whereDate('created_at', '<=', $to);
            }
        }

        $tickets = $query->latest()->paginate(20);

        return view('admin.tickets.index', compact('tickets'));
    }

    public function show(Ticket $ticket)
    {
        /**
         * Show a ticket with its messages, attachments, and user profile.
         */
        $ticket->load(['messages.attachments', 'messages.user.profile', 'user.profile']);
        return view('admin.tickets.show', compact('ticket'));
    }

    public function reply(Request $request, Ticket $ticket)
    {
        /**
         * Persist an admin reply with optional attachments and notify the ticket owner.
         */
        $validated = $request->validate([
            'message' => 'required|string',
            'attachments.*' => 'file|max:5120',
        ]);

        $msg = TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'sender_role' => 'admin',
            'message' => $validated['message'],
        ]);

        $this->storeAttachments($request, $msg, $ticket->id);

        $ticket->forceFill([
            'status' => 'waiting_user',
            'last_reply_by' => 'admin',
            'closed_at' => null,
        ])->save();

        // Notify ticket owner
        $this->notifications->notify('ticket_admin_replied', $ticket->user, [
            'title' => 'پاسخ جدید پشتیبانی',
            'message' => 'موضوع: '. $ticket->subject,
            'audience' => 'user',
            'subject' => $ticket->subject,
            'url' => route('dashboard.tickets.show', $ticket->id),
        ]);

        return back()->with('success', 'پاسخ ارسال شد.');
    }

    public function close(Ticket $ticket)
    {
        /**
         * Mark ticket as closed and notify the ticket owner of the status change.
         */
        $ticket->markClosed();

        $this->notifications->notify('ticket_status_changed', $ticket->user, [
            'title' => 'تغییر وضعیت تیکت',
            'message' => 'تیکت بسته شد',
            'audience' => 'user',
            'subject' => $ticket->subject,
            'status' => 'بسته شد',
            'url' => route('dashboard.tickets.show', $ticket->id),
        ]);

        return back()->with('success', 'تیکت بسته شد.');
    }

    public function reopen(Ticket $ticket)
    {
        /**
         * Reopen a closed ticket and notify the ticket owner.
         */
        $ticket->reopen();
        $ticket->update(['status' => 'waiting_user', 'last_reply_by' => 'admin']);

        $this->notifications->notify('ticket_status_changed', $ticket->user, [
            'title' => 'تغییر وضعیت تیکت',
            'message' => 'تیکت بازگشایی شد',
            'audience' => 'user',
            'subject' => $ticket->subject,
            'status' => 'بازگشایی شد',
            'url' => route('dashboard.tickets.show', $ticket->id),
        ]);

        return back()->with('success', 'تیکت بازگشایی شد.');
    }

    public function downloadAttachment(TicketAttachment $attachment)
    {
        /**
         * Download an attachment if it belongs to the specified ticket message.
         */
        $attachment->load('message.ticket');
        if (!$attachment->message || !$attachment->message->ticket) {
            abort(404);
        }
        if (!Storage::disk('public')->exists($attachment->path)) {
            abort(404);
        }
        return Storage::disk('public')->download($attachment->path, $attachment->original_name);
    }

    private function storeAttachments(Request $request, TicketMessage $message, int $ticketId): void
    {
        /**
         * Store uploaded attachments for a ticket message. Limits to 10 files for admin uploads.
         */
        if (!$request->hasFile('attachments')) {
            return;
        }

        $i = 0;
        foreach ($request->file('attachments') as $file) {
            if (!$file || !$file->isValid()) {
                continue;
            }
            if (++$i > 10) {
                break; // admin side slightly higher cap
            }
            $path = $file->store("tickets/{$ticketId}/messages/{$message->id}", 'public');
            TicketAttachment::create([
                'ticket_message_id' => $message->id,
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime' => $file->getClientMimeType(),
                'size' => $file->getSize(),
            ]);
        }
    }

    private function toGregorianDate(?string $value): ?string
    {
        /**
         * Convert a Jalali or numeric date string to a Gregorian date string (Y-m-d).
         * Returns null for invalid input.
         */
        if (!$value) {
            return null;
        }
        $value = str_replace(['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹','٠','١','٢','٣','٤','٥','٦','٧','٨','٩'], ['0','1','2','3','4','5','6','7','8','9','0','1','2','3','4','5','6','7','8','9'], $value);
        try {
            if (preg_match('/^\d{4}\/\d{2}\/\d{2}$/', $value)) {
                return Jalalian::fromFormat('Y/m/d', $value)->toCarbon()->toDateString();
            }
            return \Carbon\Carbon::parse($value)->toDateString();
        } catch (\Throwable $e) {
            return null;
        }
    }
}
