<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use Carbon\Carbon;
use Ipe\Sdk\Facades\SmsIr;
use App\Models\Profile;
use App\Models\MedicalRecord;
use App\Models\Enrollment;
use App\Models\EducationalHistory;
use App\Models\FederationCourse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;


class AuthController extends Controller
{
    // Entering Phone Form
    // ==========================
    public function showPhoneForm()
    {
        return view('auth.login');
    }
    // ==========================

    // Sending OTP Request
    // ==========================
    public function requestOtp(Request $request)
    {
        $rules = [
            'phone' => 'required|digits:11',
        ];
        if (! app()->environment('local')) {
            $rules['arcaptcha-token'] = 'arcaptcha';
        }

        $request->validate($rules);

        $phone = $request->input('phone') ?? Session::get('auth_phone');

        if (!$phone) {
            return redirect()->route('auth.phone')
                ->withErrors(['phone' => 'شماره تلفن یافت نشد']);
        }

        $otp = rand(1000, 9999);

        // Log OTP for local/dev debugging
        Log::info('AuthController::requestOtp - OTP generated', [
            'flow' => 'requestOtp',
            'phone' => $phone,
            'otp'   => $otp,
        ]);

        $user = User::firstOrCreate(['phone' => $phone]);
        $user->otp_code = $otp;
        $user->otp_expires_at = now()->addMinutes(5);
        $user->save();

        if (config('app.env') === 'local') {
            $templateId = 123456; 
        } else {
            $templateId = 218734;
        }
        $parameters = [
            [
                "name" => "CODE",
                "value" => (string) $otp
            ]
        ];

        try {
            SmsIr::verifySend($phone, $templateId, $parameters);
        } catch (\Exception $e) {
            return back()->withErrors(['sms' => 'خطا در ارسال پیامک: ' . $e->getMessage()]);
        }

        Session::put('auth_phone', $phone);

        return redirect()->route('auth.verifyForm')->with('status', 'کد تایید ارسال شد');
    }
    // ==========================

    // Entering 4-digits Code Form
    // ==========================
    public function showVerifyForm()
    {
        return view('auth.verify', ['action' => route('auth.login.verifyOtp')]);
    }
    // ==========================

    // Varificating OTP Code
    // ==========================
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:4'
        ]);

        $phone = Session::get('auth_phone');
        if (!$phone) {
            return redirect()->route('auth.phone')->withErrors(['phone' => 'شماره تلفن یافت نشد']);
        }

        $user = User::where('phone', $phone)->first();

        if (!$user) {
            return redirect()->route('auth.phone')->withErrors(['phone' => 'کاربر یافت نشد']);
        }

        // Checking the Code
        if ($user->otp_code == $request->otp && Carbon::now()->lt($user->otp_expires_at)) {
            // Deleting Code after using
            $user->otp_code = null;
            $user->otp_expires_at = null;
            $user->save();

            // if User has other data → Go to Dashboard
            if ($user->isRegistrationComplete()) {
                Auth::login($user);
                return redirect()->route('dashboard.index');
            }

            // if the User is New → Go to next step
            return redirect()->route('auth.register.step1');
        }

        return back()->withErrors(['otp' => 'کد تایید اشتباه یا منقضی شده است']);
    }
    // ==========================

    private function nd($v){
        $map = ['۰'=>'0','۱'=>'1','۲'=>'2','۳'=>'3','۴'=>'4','۵'=>'5','۶'=>'6','۷'=>'7','۸'=>'8','۹'=>'9',
                '٠'=>'0','١'=>'1','٢'=>'2','٣'=>'3','٤'=>'4','٥'=>'5','٦'=>'6','٧'=>'7','٨'=>'8','٩'=>'9'];
        return strtr((string)$v, $map);
    }
    // Old 3-step register wizard removed; onboarding is handled via dashboard routes

    // ==========================
    // Logout
    public function logout(Request $request)
    {
        Auth::logout();

        // Invalidate session & regenerate CSRF token
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('auth.login')->with('success', 'خروج با موفقیت انجام شد.');
    }
    // ==========================

    // Login Methods
    // ==========================
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function loginRequestOtp(Request $request)
    {
        $rules = [
            'phone' => 'required|digits:11',
    ];
    if (! app()->environment('local')) {
        $rules['arcaptcha-token'] = 'arcaptcha';
    }

    $request->validate($rules);

        $phone = $request->input('phone');
        $user = User::where('phone', $phone)->first();
        if (!$user) {
            return back()->withErrors(['phone' => 'کاربری با این شماره وجود ندارد.']);
        }
        $otp = rand(1000, 9999);

        // Log OTP for local/dev debugging
        Log::info('AuthController::loginRequestOtp - OTP generated', [
            'flow' => 'loginRequestOtp',
            'phone' => $phone,
            'otp'   => $otp,
        ]);

        $user->otp_code = $otp;
        $user->otp_expires_at = now()->addMinutes(5);
        $user->save();

            if (config('app.env') === 'local') {
                $templateId = 123456; 
            } else {
                $templateId = 218734;
            }
        $parameters = [
            [
                "name" => "CODE",
                "value" => (string) $otp
            ]
        ];

        try {
            SmsIr::verifySend($phone, $templateId, $parameters);
        } catch (\Exception $e) {
            return back()->withErrors(['sms' => 'خطا در ارسال پیامک: ' . $e->getMessage()]);
        }

        Session::put('auth_phone', $phone);
        return redirect()->route('auth.login.verifyForm');
    }

    public function showLoginVerifyForm()
    {
        return view('auth.verify', ['action' => route('auth.login.verifyOtp')]);
    }
    public function showRegisterForm()
    {
        return view('auth.register');
    }
    public function showRegisterVerifyForm()
    {
        return view('auth.verify', ['action' => route('auth.register.verifyOtp')]);
    }

    public function loginVerifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required|digits:4']);
        $phone = Session::get('auth_phone');
        $user = User::where('phone', $phone)->first();
        if (!$user) {
            return redirect()->route('auth.login')->withErrors(['phone' => 'کاربر یافت نشد']);
        }

        // Checking the Code
        if ($user->otp_code == $request->otp && Carbon::now()->lt($user->otp_expires_at)) {
            // Deleting Code after using
            $user->otp_code = null;
            $user->otp_expires_at = null;
            $user->save();

            Auth::login($user);
            return $this->redirectToNextStep($user);
        }

        return back()->withErrors(['otp' => 'کد تایید اشتباه یا منقضی شده است']);
    }

    protected function redirectToNextStep($user)
    {
        $incomplete = (!$user->hasProfile()) || (!$user->hasMedicalRecord()) || (!$user->hasEducationalHistory());
        if ($incomplete) {
            session(['onboarding' => true]);
        }
        if (!$user->hasProfile()) {
            return redirect()->route('dashboard.profile.edit', $user->id);
        }
        if (!$user->hasMedicalRecord()) {
            return redirect()->route('dashboard.medicalRecord.edit');
        }
        if (!$user->hasEducationalHistory()) {
            return redirect()->route('dashboard.educationalHistory.index');
        }
        return redirect()->route('dashboard.index');
    }



    public function registerRequestOtp(Request $request)
    {
        $rules = [
            'phone' => 'required|digits:11',
    ];
    if (! app()->environment('local')) {
        $rules['arcaptcha-token'] = 'arcaptcha';
    }

    $request->validate($rules);

        $phone = $request->input('phone');
        $user = User::where('phone', $phone)->first();
        if ($user) {
            return back()->withErrors(['phone' => 'این شماره قبلاً ثبت شده است.']);
        }

        $otp = rand(1000, 9999);

        // Log OTP for local/dev debugging (registration flow)
        Log::info('AuthController::registerRequestOtp - OTP generated', [
            'flow' => 'registerRequestOtp',
            'phone' => $phone,
            'otp'   => $otp,
        ]);

        // Save OTP and expiry in session
        session([
            'register_phone' => $phone,
            'register_otp' => $otp,
            'register_otp_expires_at' => now()->addMinutes(5)
        ]);

        // Send OTP SMS
        $templateId = config('app.env') === 'local' ? 123456 : 218734;
        $parameters = [
            [
                "name" => "CODE",
                "value" => (string) $otp
            ]
        ];

        try {
            \Ipe\Sdk\Facades\SmsIr::verifySend($phone, $templateId, $parameters);
        } catch (\Exception $e) {
            return back()->withErrors(['sms' => 'خطا در ارسال پیامک: ' . $e->getMessage()]);
        }

        return redirect()->route('auth.register.verifyForm');
    }

    public function registerVerifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required|digits:4']);

        $phone = session('register_phone');
        $otp = session('register_otp');
        $expiresAt = session('register_otp_expires_at');

        if (!$phone || !$otp || !$expiresAt) {
            return redirect()->route('auth.register')->withErrors(['phone' => 'اطلاعات ثبت‌نام یافت نشد.']);
        }

        if (now()->gt($expiresAt)) {
            return redirect()->route('auth.register')->withErrors(['otp' => 'کد منقضی شده است.']);
        }

        if ($request->input('otp') != $otp) {
            return back()->withErrors(['otp' => 'کد وارد شده صحیح نیست.']);
        }

        // --- Create or get user safely (no mass-assignment issues) ---
        $user = User::where('phone', $phone)->first();
        if (! $user) {
            $user = new User();
            $user->phone = $phone;
            // set other default fields required by your users table (example:)
            // $user->name = null;
            // $user->password = null;
            $user->save();
        }

        Auth::login($user);

        // Clean up session
        session()->forget(['register_phone', 'register_otp', 'register_otp_expires_at']);

        return $this->redirectToNextStep($user);
    }
}
