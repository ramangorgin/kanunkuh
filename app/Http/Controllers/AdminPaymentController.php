<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PaymentsExport;
use App\Services\NotificationService;
use App\Models\Program;
use App\Models\Course;

class AdminPaymentController extends Controller
{
    protected NotificationService $notifications;

    public function __construct(NotificationService $notifications)
    {
        $this->notifications = $notifications;
    }

    public function index()
    {
        $payments = Payment::with('user.profile')->latest()->get();
        return view('admin.payments.index', compact('payments'));
    }

    public function approve($id)
    {
        $payment = Payment::findOrFail($id);
        
        DB::transaction(function () use ($payment) {
            $payment->status = 'approved';
            $payment->approved = true;
            $payment->save();
            
            // Create registration with status 'paid' when payment is approved
            if ($payment->type === 'program') {
                // Check if registration already exists
                $existingRegistration = \App\Models\ProgramRegistration::where('payment_id', $payment->id)->first();
                
                if (!$existingRegistration) {
                    // Get registration data from payment metadata
                    $metadata = $payment->metadata ? json_decode($payment->metadata, true) : [];
                    
                    \App\Models\ProgramRegistration::create([
                        'program_id' => $payment->related_id,
                        'user_id' => $payment->user_id,
                        'payment_id' => $payment->id,
                        'guest_name' => $metadata['guest_name'] ?? null,
                        'guest_phone' => $metadata['guest_phone'] ?? null,
                        'guest_national_id' => $metadata['guest_national_id'] ?? null,
                        'pickup_location' => $metadata['pickup_location'] ?? null,
                        'needs_transport' => $metadata['needs_transport'] ?? false,
                        'status' => 'paid', // Status is 'paid' - waiting for admin approval
                    ]);
                }
            }
            
            if ($payment->type === 'course') {
                // Check if registration already exists
                $existingRegistration = \App\Models\CourseRegistration::where('payment_id', $payment->id)->first();
                
                if (!$existingRegistration) {
                    // Get registration data from payment metadata
                    $metadata = $payment->metadata ? json_decode($payment->metadata, true) : [];
                    
                    \App\Models\CourseRegistration::create([
                        'course_id' => $payment->related_id,
                        'user_id' => $payment->user_id,
                        'payment_id' => $payment->id,
                        'guest_name' => $metadata['guest_name'] ?? null,
                        'guest_phone' => $metadata['guest_phone'] ?? null,
                        'guest_national_id' => $metadata['guest_national_id'] ?? null,
                        'status' => 'paid', // Status is 'paid' - waiting for admin approval
                    ]);
                }
            }
        });

        // Notify user about approval (site only)
        $this->notifications->notify('payment_approved', $payment->user, [
            'amount' => number_format($payment->amount) . ' تومان',
            'context' => $this->buildContext($payment),
            'url' => route('dashboard.payments.index'),
        ]);
        
        return response()->json(['success' => true]);
    }

    public function reject($id)
    {
        $payment = Payment::findOrFail($id);
        $payment->status = 'rejected';
        $payment->approved = false;
        $payment->save();
        
        // When payment is rejected, no registration should be created
        // If registration exists (shouldn't happen in normal flow), delete it
        if ($payment->type === 'program') {
            $registration = \App\Models\ProgramRegistration::where('payment_id', $payment->id)->first();
            if ($registration && $registration->status === 'paid') {
                $registration->delete();
            }
        }
        
        if ($payment->type === 'course') {
            $registration = \App\Models\CourseRegistration::where('payment_id', $payment->id)->first();
            if ($registration && $registration->status === 'paid') {
                $registration->delete();
            }
        }

        // Notify user about rejection (site only)
        $this->notifications->notify('payment_rejected', $payment->user, [
            'amount' => number_format($payment->amount) . ' تومان',
            'context' => $this->buildContext($payment),
            'url' => route('dashboard.payments.index'),
        ]);
        
        return response()->json(['success' => true]);
    }

    private function buildContext(Payment $payment): string
    {
        if ($payment->type === 'program') {
            return optional(Program::find($payment->related_id))->name ?? 'برنامه';
        }
        if ($payment->type === 'course') {
            return optional(Course::find($payment->related_id))->name ?? 'دوره';
        }
        return 'حق عضویت' . ($payment->year ? ' ' . $payment->year : '');
    }


    public function export()
    {
        return Excel::download(new PaymentsExport, 'payments.xlsx');
    }


    public function show($id)
    {
        $payment = \App\Models\Payment::with('user.profile')->findOrFail($id);

        $typeMap = [
            'membership' => 'حق عضویت',
            'program' => 'برنامه',
            'course' => 'دوره',
        ];

        $statusMap = [
            'pending' => ['text' => 'در انتظار بررسی', 'color' => 'secondary'],
            'approved' => ['text' => 'تأیید شده', 'color' => 'success'],
            'rejected' => ['text' => 'رد شده', 'color' => 'danger'],
        ];

        $relatedLink = null;
        if ($payment->type == 'program')
            $relatedLink = "<a href='/admin/programs/{$payment->related_id}' class='btn btn-outline-success mt-2'><i class='bi bi-calendar-event'></i> مشاهده برنامه</a>";
        elseif ($payment->type == 'course')
            $relatedLink = "<a href='/admin/courses/{$payment->related_id}' class='btn btn-outline-warning mt-2'><i class='bi bi-book'></i> مشاهده دوره</a>";

        return response()->json([
            'id' => $payment->id,
            'transaction_code' => $payment->transaction_code,
            'amount' => $payment->amount,
            'type_fa' => $typeMap[$payment->type],
            'date' => jdate($payment->created_at)->format('Y/m/d H:i'),
            'status_text' => $statusMap[$payment->status]['text'],
            'status_color' => $statusMap[$payment->status]['color'],
            'membership_code' => $payment->user->profile->membership_id ?? '-',
            'user_id' => $payment->user->id,
            'user_name' => $payment->user->profile->first_name . ' ' . $payment->user->profile->last_name,
            'user_phone' => $payment->user->phone,
            'related_link' => $relatedLink
        ]);
    }

}
