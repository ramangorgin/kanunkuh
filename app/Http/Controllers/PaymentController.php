<?php

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

class PaymentController extends Controller
{
    protected NotificationService $notifications;

    public function __construct(NotificationService $notifications)
    {
        $this->notifications = $notifications;
    }
    
    /**
     * نمایش فرم پرداخت و سوابق کاربر
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
     * ذخیره پرداخت جدید
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

        // شناسه عضویت (در آینده هنگام تایید اکانت کاربر ست می‌شود)
        $membershipCode = $user->membership_code ?? null;

        // شناسه واریز ده‌رقمی تصادفی
        $transactionCode = random_int(1000000000, 9999999999);

        // ذخیره پرداخت
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

        // ارسال شناسه‌ها به Blade از طریق Session
        Session::flash('payment_success', [
            'membership_code' => $membershipCode ?? 'نامشخص',
            'transaction_code' => $transactionCode,
        ]);

        return redirect()->route('dashboard.payments.index')
            ->with('status', 'پرداخت با موفقیت ثبت شد.');
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

    /**
     * فهرست برنامه‌ها (برای AJAX)
     */
    public function getPrograms()
    {
        $programs = Program::select('id', 'name')
            ->orderBy('name')
            ->get();

        return response()->json($programs);
    }

    /**
     * فهرست دوره‌ها (برای AJAX)
     */
    public function getCourses()
    {
        $courses = Course::select('id', 'name')
            ->orderBy('name')
            ->get();

        return response()->json($courses);
    }

    public function AdminIndex()
    {
        $payments = Payment::with(['user.profile', 'relatedProgram', 'relatedCourse'])
            ->latest()
            ->get();

        return view('admin.payments', compact('payments'));
    }


    public function approve(Payment $payment)
    {
        $payment->approved = true;
        $payment->save();

        return redirect()->back()->with('success', 'پرداخت تایید شد.');
    }

    public function reject(Payment $payment)
    {
        $payment->approved = null;
        $payment->save();

        return redirect()->back()->with('error', 'پرداخت رد شد.');
    }
}