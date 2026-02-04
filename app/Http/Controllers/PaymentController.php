<?php

/**
 * User and admin payment management endpoints.
 */

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Program;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Morilog\Jalali\Jalalian;
use Illuminate\Support\Facades\Session;
use App\Services\NotificationService;

/**
 * Handles payment creation, listing, and approval actions.
 */
class PaymentController extends Controller
{
    protected NotificationService $notifications;

    public function __construct(NotificationService $notifications)
    {
        $this->notifications = $notifications;
    }
    
    /**
     * Show the user's payment history.
     */
    public function UserIndex()
    {
        $user = Auth::user();
        $payments = Payment::where('user_id', $user->id)
            ->latest()
            ->get();

        return view('user.myPayments', compact('payments'));
    }

    /**
        * Store a new payment request and notify admins.
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:membership,program,course',
            'amount' => 'required|numeric|min:1000',
            'year' => 'nullable|integer',
            'related_id' => 'nullable|integer',
            'description' => 'nullable|string|max:500',
        ], [
            'type.required' => 'موضوع پرداخت الزامی است.',
            'amount.required' => 'مبلغ را وارد کنید.',
            'amount.min' => 'حداقل مبلغ باید ۱۰۰۰ تومان باشد.',
        ]);

        $user = Auth::user();

        $membershipCode = $user->membership_code ?? null;

        $transactionCode = random_int(1000000000, 9999999999);

        $payment = new Payment([
            'user_id'          => $user->id,
            'amount'           => $request->amount,
            'type'             => $request->type,
            'year'             => $request->year,
            'related_id'       => $request->related_id,
            'description'      => $request->description,
            'membership_code'  => $membershipCode,
            'transaction_code' => $transactionCode,
            'status'           => 'pending',
        ]);

        $payment->save();

        // Notify all admins about the new payment (site only)
        $context = $this->buildContext($payment);
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $this->notifications->notify('payment_created', $admin, [
                'user' => trim(($user->profile->first_name ?? '') . ' ' . ($user->profile->last_name ?? '')) ?: ($user->phone ?? 'کاربر'),
                'amount' => number_format($payment->amount) . ' تومان',
                'context' => $context,
                // Link admins to the payments list (show route returns JSON)
                'url' => route('admin.payments.index'),
            ]);
        }

        Session::flash('payment_success', [
            'membership_code' => $membershipCode ?? 'نامشخص',
            'transaction_code' => $transactionCode,
        ]);

        return redirect()->route('dashboard.payments.index')
            ->with('status', 'پرداخت با موفقیت ثبت شد.');
    }

    /**
     * Build a contextual label for payment notifications.
     */
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

    /**
        * Return program options for AJAX consumers.
     */
    public function getPrograms()
    {
        $programs = Program::select('id', 'name')
            ->orderBy('name')
            ->get();

        return response()->json($programs);
    }

    /**
        * Return course options for AJAX consumers.
     */
    public function getCourses()
    {
        $courses = Course::select('id', 'name')
            ->orderBy('name')
            ->get();

        return response()->json($courses);
    }

    /**
     * List payments for the admin view.
     */
    public function AdminIndex()
    {
        $payments = Payment::with(['user.profile', 'relatedProgram', 'relatedCourse'])
            ->latest()
            ->get();

        return view('admin.payments', compact('payments'));
    }


    /**
     * Mark a payment as approved.
     */
    public function approve(Payment $payment)
    {
        $payment->approved = true;
        $payment->save();

        return redirect()->back()->with('success', 'پرداخت تایید شد.');
    }

    /**
     * Mark a payment as rejected.
     */
    public function reject(Payment $payment)
    {
        $payment->approved = null;
        $payment->save();

        return redirect()->back()->with('error', 'پرداخت رد شد.');
    }
}