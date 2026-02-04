<?php

/**
 * Handles program registration flows for members and guests.
 */

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\ProgramRegistration;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

/**
 * Creates program registrations and related payment records when required.
 */
class ProgramRegistrationController extends Controller
{
    /**
     * Show the registration form for a program.
     */
    public function create(Program $program)
    {
        if ($program->register_deadline && now()->gt($program->register_deadline)) {
            return redirect()
                ->route('programs.show', $program->id)
                ->with('error', 'مهلت ثبت‌نام به پایان رسیده است.');
        }

        if (Auth::check()) {
            $existingRegistration = ProgramRegistration::where('program_id', $program->id)
                ->where('user_id', Auth::id())
                ->first();
            
            if ($existingRegistration) {
                return redirect()
                    ->route('programs.show', $program->id)
                    ->with('info', 'شما قبلاً در این برنامه ثبت‌نام کرده‌اید.');
            }
        }

        $paymentInfo = $program->payment_info ?? [];
        $isFree = empty($paymentInfo) || ($program->cost_member == 0 && $program->cost_guest == 0);
        
        $transportTehran = $program->move_from_tehran ? json_decode($program->move_from_tehran, true) : null;
        $transportKaraj = $program->move_from_karaj ? json_decode($program->move_from_karaj, true) : null;
        $hasTransport = ($transportTehran || $transportKaraj) ? true : false;

        $amount = Auth::check() ? $program->cost_member : $program->cost_guest;
        $user = Auth::user();
        $membershipCode = $user ? ($user->membership_code ?? $user->profile->membership_id ?? 'نامشخص') : 'GUEST';
        
        $transactionCode = random_int(1000000000, 9999999999);

        return view('programs.register', compact(
            'program',
            'isFree',
            'hasTransport',
            'paymentInfo',
            'amount',
            'membershipCode',
            'transactionCode'
        ));
    }

    /**
     * Store a program registration and create payment when required.
     */
    public function store(Request $request, Program $program)
    {
        if ($program->register_deadline && now()->gt($program->register_deadline)) {
            return redirect()
                ->route('programs.show', $program->id)
                ->with('error', 'مهلت ثبت‌نام به پایان رسیده است.');
        }

        $isFree = empty($program->payment_info) || ($program->cost_member == 0 && $program->cost_guest == 0);
        $hasTransport = ($program->move_from_tehran || $program->move_from_karaj) ? true : false;
        $isGuest = !Auth::check();

        $rules = [
            'pickup_location' => $hasTransport ? 'required|in:tehran,karaj' : 'nullable',
        ];

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
            'pickup_location.required' => 'لطفاً محل سوار شدن را انتخاب کنید.',
            'pickup_location.in' => 'محل سوار شدن انتخاب شده معتبر نیست.',
            'guest_name.required' => 'لطفاً نام مهمان را وارد کنید.',
            'guest_phone.required' => 'لطفاً شماره تماس مهمان را وارد کنید.',
            'guest_national_id.required' => 'لطفاً کد ملی مهمان را وارد کنید.',
            'transaction_code.required' => 'لطفاً کد پیگیری پرداخت را وارد کنید.',
            'transaction_code.size' => 'کد پیگیری باید 10 رقم باشد.',
            'transaction_code.regex' => 'کد پیگیری باید فقط شامل اعداد باشد.',
        ]);

        if (Auth::check()) {
            $existing = ProgramRegistration::where('program_id', $program->id)
                ->where('user_id', Auth::id())
                ->exists();
        } else {
            $existing = ProgramRegistration::where('program_id', $program->id)
                ->where('guest_phone', $validated['guest_phone'])
                ->exists();
        }

        if ($existing) {
            return back()
                ->withInput()
                ->with('error', 'شما قبلاً در این برنامه ثبت‌نام کرده‌اید.');
        }

        DB::transaction(function () use ($request, $program, $validated, $isFree, $isGuest) {
            $payment = null;
            
            if (!$isFree) {
                $amount = $isGuest ? $program->cost_guest : $program->cost_member;
                $user = Auth::user();
                $membershipCode = $user ? ($user->membership_code ?? $user->profile->membership_id ?? null) : 'GUEST';
                
                $transactionCode = $validated['transaction_code'] ?? random_int(1000000000, 9999999999);
                
                $payment = Payment::create([
                    'user_id' => Auth::id(),
                    'type' => 'program',
                    'related_id' => $program->id,
                    'amount' => $amount,
                    'membership_code' => $membershipCode,
                    'transaction_code' => $transactionCode,
                    'status' => 'pending',
                    'approved' => false,
                ]);

                $payment->update([
                    'metadata' => json_encode([
                        'guest_name' => $isGuest ? $validated['guest_name'] : null,
                        'guest_phone' => $isGuest ? $validated['guest_phone'] : null,
                        'guest_national_id' => $isGuest ? $validated['guest_national_id'] : null,
                        'pickup_location' => $validated['pickup_location'] ?? null,
                        'needs_transport' => !empty($validated['pickup_location']),
                    ])
                ]);

                Session::flash('registration_success', [
                    'transaction_code' => $payment->transaction_code,
                    'amount' => $amount,
                    'membership_code' => $membershipCode,
                ]);
            } else {
                $registration = ProgramRegistration::create([
                    'program_id' => $program->id,
                    'user_id' => Auth::id(),
                    'payment_id' => null,
                    'guest_name' => $isGuest ? $validated['guest_name'] : null,
                    'guest_phone' => $isGuest ? $validated['guest_phone'] : null,
                    'guest_national_id' => $isGuest ? $validated['guest_national_id'] : null,
                    'pickup_location' => $validated['pickup_location'] ?? null,
                    'needs_transport' => !empty($validated['pickup_location']),
                    'status' => 'approved',
                ]);
            }
        });

        $message = $isFree 
            ? 'ثبت‌نام شما با موفقیت انجام شد.'
            : 'ثبت‌نام شما ثبت شد. پس از تأیید پرداخت، ثبت‌نام شما فعال خواهد شد.';

        return redirect()
            ->route('programs.show', $program->id)
            ->with('success', $message);
    }
}

