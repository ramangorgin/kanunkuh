<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseRegistration;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CourseRegistrationController extends Controller
{
    /**
     * Show registration form for a course
     */
    public function create(Course $course)
    {
        // Check if registration deadline has passed
        if ($course->registration_deadline && now()->gt($course->registration_deadline)) {
            return redirect()
                ->route('courses.show', $course->id)
                ->with('error', 'مهلت ثبت‌نام به پایان رسیده است.');
        }

        // Check if capacity is full
        $currentRegistrations = $course->registrations()
            ->where('status', 'approved')
            ->count();
        if ($course->capacity && $currentRegistrations >= $course->capacity) {
            return redirect()
                ->route('courses.show', $course->id)
                ->with('error', 'ظرفیت دوره تکمیل شده است.');
        }

        // Check if user is already registered
        if (Auth::check()) {
            $existingRegistration = CourseRegistration::where('course_id', $course->id)
                ->where('user_id', Auth::id())
                ->first();
            
            if ($existingRegistration) {
                return redirect()
                    ->route('courses.show', $course->id)
                    ->with('info', 'شما قبلاً در این دوره ثبت‌نام کرده‌اید.');
            }

            // Check prerequisites
            if (!$course->userHasCompletedPrerequisites(Auth::id())) {
                return redirect()
                    ->route('courses.show', $course->id)
                    ->with('error', 'شما پیش‌نیازهای این دوره را تکمیل نکرده‌اید.');
            }
        }

        $isFree = $course->is_free;
        $amount = Auth::check() ? $course->member_cost : $course->guest_cost;
        $user = Auth::user();
        $membershipCode = $user ? ($user->membership_code ?? $user->profile->membership_id ?? 'نامشخص') : 'GUEST';
        
        // Generate transaction code for display
        $transactionCode = random_int(1000000000, 9999999999);

        return view('courses.register', compact(
            'course',
            'isFree',
            'amount',
            'membershipCode',
            'transactionCode'
        ));
    }

    /**
     * Store course registration with payment
     */
    public function store(Request $request, Course $course)
    {
        // Check if registration deadline has passed
        if ($course->registration_deadline && now()->gt($course->registration_deadline)) {
            return redirect()
                ->route('courses.show', $course->id)
                ->with('error', 'مهلت ثبت‌نام به پایان رسیده است.');
        }

        // Check capacity
        $currentRegistrations = $course->registrations()
            ->where('status', 'approved')
            ->count();
        if ($course->capacity && $currentRegistrations >= $course->capacity) {
            return redirect()
                ->route('courses.show', $course->id)
                ->with('error', 'ظرفیت دوره تکمیل شده است.');
        }

        $isFree = $course->is_free;
        $isGuest = !Auth::check();

        // Check prerequisites for authenticated users
        if (Auth::check() && !$course->userHasCompletedPrerequisites(Auth::id())) {
            return redirect()
                ->route('courses.show', $course->id)
                ->with('error', 'شما پیش‌نیازهای این دوره را تکمیل نکرده‌اید.');
        }

        // Validation rules
        $rules = [];

        if ($isGuest) {
            $rules = array_merge($rules, [
                'guest_name' => 'required|string|max:255',
                'guest_phone' => 'required|string|max:20',
                'guest_national_id' => 'required|string|max:20',
            ]);
        }

        if (!$isFree) {
            $rules['transaction_code'] = 'required|string|size:10|regex:/^\d{10}$/';
        }

        $validated = $request->validate($rules, [
            'guest_name.required' => 'لطفاً نام مهمان را وارد کنید.',
            'guest_phone.required' => 'لطفاً شماره تماس مهمان را وارد کنید.',
            'guest_national_id.required' => 'لطفاً کد ملی مهمان را وارد کنید.',
            'transaction_code.required' => 'لطفاً کد پیگیری پرداخت را وارد کنید.',
            'transaction_code.size' => 'کد پیگیری باید 10 رقم باشد.',
            'transaction_code.regex' => 'کد پیگیری باید فقط شامل اعداد باشد.',
        ]);

        // Check for duplicate registration
        if (Auth::check()) {
            $existing = CourseRegistration::where('course_id', $course->id)
                ->where('user_id', Auth::id())
                ->exists();
        } else {
            $existing = CourseRegistration::where('course_id', $course->id)
                ->where('guest_phone', $validated['guest_phone'])
                ->exists();
        }

        if ($existing) {
            return back()
                ->withInput()
                ->with('error', 'شما قبلاً در این دوره ثبت‌نام کرده‌اید.');
        }

        DB::transaction(function () use ($request, $course, $validated, $isFree, $isGuest) {
            $payment = null;
            
            if (!$isFree) {
                // Only create payment record - registration will be created after payment approval
                $amount = $isGuest ? $course->guest_cost : $course->member_cost;
                $user = Auth::user();
                $membershipCode = $user ? ($user->membership_code ?? $user->profile->membership_id ?? null) : 'GUEST';
                
                $transactionCode = $validated['transaction_code'] ?? random_int(1000000000, 9999999999);
                
                $payment = Payment::create([
                    'user_id' => Auth::id(),
                    'type' => 'course',
                    'related_id' => $course->id,
                    'amount' => $amount,
                    'membership_code' => $membershipCode,
                    'transaction_code' => $transactionCode,
                    'status' => 'pending',
                    'approved' => false,
                    'metadata' => json_encode([
                        'guest_name' => $isGuest ? $validated['guest_name'] : null,
                        'guest_phone' => $isGuest ? $validated['guest_phone'] : null,
                        'guest_national_id' => $isGuest ? $validated['guest_national_id'] : null,
                    ]),
                ]);

                // Store success message data
                Session::flash('registration_success', [
                    'transaction_code' => $payment->transaction_code,
                    'amount' => $amount,
                    'membership_code' => $membershipCode,
                ]);
            } else {
                // For free courses, create registration immediately
                $registration = CourseRegistration::create([
                    'course_id' => $course->id,
                    'user_id' => Auth::id(),
                    'payment_id' => null,
                    'guest_name' => $isGuest ? $validated['guest_name'] : null,
                    'guest_phone' => $isGuest ? $validated['guest_phone'] : null,
                    'guest_national_id' => $isGuest ? $validated['guest_national_id'] : null,
                    'status' => 'approved',
                ]);
            }
        });

        $message = $isFree 
            ? 'ثبت‌نام شما با موفقیت انجام شد.'
            : 'ثبت‌نام شما ثبت شد. پس از تأیید پرداخت، ثبت‌نام شما فعال خواهد شد.';

        return redirect()
            ->route('courses.show', $course->id)
            ->with('success', $message);
    }
}

