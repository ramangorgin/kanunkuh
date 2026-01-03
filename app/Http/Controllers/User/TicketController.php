<?php

namespace App\Http\Controllers\User;

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

class TicketController extends Controller
{
    public function __construct(private NotificationService $notifications)
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $tickets = Ticket::with('latestMessage')
            ->ownedBy($user)
            ->when($request->status, fn($q, $status) => $q->where('status', $status))
            ->latest()
            ->paginate(15);

        return view('user.tickets.index', compact('tickets'));
    }

    public function create()
    {
        return view('user.tickets.create');
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'attachments.*' => 'file|max:5120',
        ]);

        $ticket = Ticket::create([
            'user_id' => $user->id,
            'subject' => $validated['subject'],
            'status' => 'waiting_admin',
            'last_reply_by' => 'user',
        ]);

        $message = TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'sender_role' => 'user',
            'message' => $validated['message'],
        ]);

        $this->storeAttachments($request, $message, $ticket->id);

        // Notify admins
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $this->notifications->notify('ticket_created', $admin, [
                'title' => 'تیکت جدید',
                'message' => 'موضوع: '. $ticket->subject,
                'audience' => 'admin',
                'user' => trim(($user->profile->first_name ?? '') . ' ' . ($user->profile->last_name ?? '')) ?: ($user->phone ?? 'کاربر'),
                'subject' => $ticket->subject,
                'url' => route('admin.tickets.show', $ticket->id),
            ]);
        }

        return redirect()->route('dashboard.tickets.show', $ticket->id)
            ->with('success', 'تیکت ثبت شد و به زودی پاسخ داده می‌شود.');
    }

    public function show(Ticket $ticket)
    {
        $this->authorizeTicket($ticket);
        $ticket->load(['messages.attachments', 'messages.user.profile']);
        return view('user.tickets.show', compact('ticket'));
    }

    public function reply(Request $request, Ticket $ticket)
    {
        $this->authorizeTicket($ticket);

        if ($ticket->status === 'closed') {
            return back()->withErrors(['message' => 'این تیکت بسته شده است.']);
        }

        $validated = $request->validate([
            'message' => 'required|string',
            'attachments.*' => 'file|max:5120',
        ]);

        $msg = TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'sender_role' => 'user',
            'message' => $validated['message'],
        ]);

        $this->storeAttachments($request, $msg, $ticket->id);

        $ticket->forceFill([
            'status' => 'waiting_admin',
            'last_reply_by' => 'user',
            'closed_at' => null,
        ])->save();

        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $this->notifications->notify('ticket_user_replied', $admin, [
                'title' => 'پاسخ جدید کاربر',
                'message' => 'موضوع: '. $ticket->subject,
                'audience' => 'admin',
                'subject' => $ticket->subject,
                'user' => trim(($ticket->user->profile->first_name ?? '') . ' ' . ($ticket->user->profile->last_name ?? '')) ?: ($ticket->user->phone ?? 'کاربر'),
                'url' => route('admin.tickets.show', $ticket->id),
            ]);
        }

        return back()->with('success', 'پاسخ شما ثبت شد.');
    }

    public function close(Ticket $ticket)
    {
        $this->authorizeTicket($ticket);
        $ticket->markClosed();

        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $this->notifications->notify('ticket_status_changed', $admin, [
                'title' => 'تغییر وضعیت تیکت',
                'message' => 'تیکت بسته شد',
                'audience' => 'admin',
                'subject' => $ticket->subject,
                'status' => 'بسته شد',
                'url' => route('admin.tickets.show', $ticket->id),
            ]);
        }

        return back()->with('success', 'تیکت بسته شد.');
    }

    public function reopen(Ticket $ticket)
    {
        $this->authorizeTicket($ticket);
        $ticket->reopen();
        $ticket->update(['status' => 'waiting_admin', 'last_reply_by' => 'user']);

        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $this->notifications->notify('ticket_status_changed', $admin, [
                'title' => 'تغییر وضعیت تیکت',
                'message' => 'تیکت بازگشایی شد',
                'audience' => 'admin',
                'subject' => $ticket->subject,
                'status' => 'دوباره باز شد',
                'url' => route('admin.tickets.show', $ticket->id),
            ]);
        }

        return back()->with('success', 'تیکت مجدداً باز شد.');
    }

    public function downloadAttachment(TicketAttachment $attachment)
    {
        $attachment->load('message.ticket');
        $ticket = $attachment->message?->ticket;
        if (!$ticket || $ticket->user_id !== Auth::id()) {
            abort(403);
        }
        if (!Storage::disk('public')->exists($attachment->path)) {
            abort(404);
        }
        return Storage::disk('public')->download($attachment->path, $attachment->original_name);
    }

    private function storeAttachments(Request $request, TicketMessage $message, int $ticketId): void
    {
        if (!$request->hasFile('attachments')) {
            return;
        }

        $i = 0;
        foreach ($request->file('attachments') as $file) {
            if (!$file || !$file->isValid()) {
                continue;
            }
            if (++$i > 5) {
                break; // hard cap
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

    private function authorizeTicket(Ticket $ticket): void
    {
        if ($ticket->user_id !== Auth::id()) {
            abort(403);
        }
    }
}
