{{-- Registration form view. --}}
@extends('layout')

@section('title', 'ثبت‌نام')

@section('content')
<div class="auth-bg" style="min-height:100vh; background: url('/images/bg.jpg') center center / cover no-repeat; direction: rtl;">
    <div class="d-flex justify-content-center align-items-start" style="min-height:100vh; padding-top:60px;">
        <div style="width:100%; max-width:420px;">
            <div style="background: rgba(0,0,0,0.45); padding:26px; border-radius:12px; box-shadow: 0 6px 18px rgba(0,0,0,0.35);">
                <div style="background:#F37021; color:#fff; border-radius:8px; padding:10px 12px; text-align:center; font-weight:700; margin-bottom:14px;">
                    شماره تلفن خود را وارد کنید
                </div>

                <form method="POST" action="{{ route('auth.register.requestOtp') }}" id="register-phone-form" novalidate>
                    @csrf

                    <div class="mb-3">
                        <input
                            type="text"
                            name="phone"
                            id="phone"
                            placeholder="۰۹۱۲۱۲۳۴۵۶۷"
                            class="form-control @error('phone') is-invalid @enderror"
                            style="height:56px; font-size:1.05rem; border-radius:6px; background:#efefef; text-align:center; direction:ltr;"
                            value="{{ old('phone') }}"
                            required
                            autocomplete="tel"
                        >
                        @error('phone')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        @if (! app()->environment('local'))
                            @arcaptchaWidget
                        @else
                            {{-- در محیط محلی arcaptcha نمایش داده نمی‌شود --}}
                        @endif
                    </div>

                    <button type="submit" class="btn w-100" style="background:#0077A9; color:#fff; font-weight:700; padding:12px 16px; border-radius:8px;">
                        ارسال کد تایید
                    </button>
                </form>

                <div class="mt-3 text-center" style="color:#fff; opacity:0.95;">
                    <a href="{{ route('auth.login') }}" style="color:#fff; text-decoration:underline;">قبلاً ثبت‌نام کرده‌اید؟ ورود</a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function normalizeToEnglishDigits(str){
    const persian = ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'];
    const arabic  = ['٠','١','٢','٣','٤','٥','٦','٧','٨','٩'];
    let output = '' + str;
    for (let i = 0; i < 10; i++) {
        output = output.replace(new RegExp(persian[i], 'g'), i).replace(new RegExp(arabic[i], 'g'), i);
    }
    return output;
}

document.addEventListener('DOMContentLoaded', function(){
    const phoneInput = document.querySelector('#register-phone-form input[name="phone"]');
    if (!phoneInput) return;

    phoneInput.addEventListener('input', function(){
        this.value = this.value.replace(/[^\d۰-۹٠-٩]/g, '').slice(0,11);
    });

    document.getElementById('register-phone-form').addEventListener('submit', function(e){
        phoneInput.value = normalizeToEnglishDigits(phoneInput.value);
    });
});
</script>
@endpush
@endsection