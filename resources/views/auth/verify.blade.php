{{-- OTP verification view. --}}
@extends('layout')

@section('title', 'تایید کد')

@section('content')
@php
    // $action must be provided by controller (route for verify)
    // optional $resendRoute for resend button
    $resendRoute = $resendRoute ?? null;
@endphp

<div class="auth-bg" style="min-height:100vh; background: url('/images/bg.jpg') center center / cover no-repeat; direction: rtl;">
    <div class="d-flex justify-content-center align-items-center" style="min-height:100vh;">
        <div class="card shadow-sm p-4" style="width: 100%; max-width: 350px; background: rgba(255,255,255,0.12); border-radius: 14px; border: none;">
            <div class="mb-3">
                <div class="btn w-100" style="background: #F37021; color: #fff; font-weight: 700; font-size: 1.05rem; border-radius: 8px;">
                    کد ارسال شده را وارد کنید
                </div>
            </div>

            <form method="POST" action="{{ $action ?? route('auth.verifyOtp') }}" id="verify-form">
                @csrf
                <div class="d-flex justify-content-center gap-2 mb-4" style="direction:ltr">
                    <input type="text" name="c1" maxlength="1" class="form-control text-center code-input" style="width:60px; height:56px; font-size:1.8rem; background:#eee; border-radius:8px; border:1px solid #ccc;" required>
                    <input type="text" name="c2" maxlength="1" class="form-control text-center code-input" style="width:60px; height:56px; font-size:1.8rem; background:#eee; border-radius:8px; border:1px solid #ccc;" required>
                    <input type="text" name="c3" maxlength="1" class="form-control text-center code-input" style="width:60px; height:56px; font-size:1.8rem; background:#eee; border-radius:8px; border:1px solid #ccc;" required>
                    <input type="text" name="c4" maxlength="1" class="form-control text-center code-input" style="width:60px; height:56px; font-size:1.8rem; background:#eee; border-radius:8px; border:1px solid #ccc;" required>
                </div>

                <input type="hidden" name="otp" id="otpHidden">

                <button type="submit" class="btn w-100" style="background:#0077A9; color:#fff; font-weight:700; padding:12px 16px; border-radius:8px;">
                    ارسال
                </button>
            </form>

            <div class="mt-4 d-flex justify-content-between align-items-center">
                <span class="text-light" style="font-size:1.05rem;" id="timer">۰۲:۰۰</span>

                <form method="POST" action="{{ $resendRoute ?? route('auth.requestOtp') }}">
                    @csrf
                    <input type="hidden" name="phone" value="{{ session('auth_phone') }}">
                    <button type="submit" id="resend-btn" class="btn btn-sm" style="background:#F37021; color:#fff; border-radius:6px;" disabled>
                        ارسال مجدد
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('vendor/cdn/jquery/3.6.0/jquery.min.js') }}"></script> 
<script>
$(document).ready(function() {
    // input navigation
    $('.code-input').on('input', function() {
        if (this.value.length === this.maxLength) {
            $(this).next('.code-input').focus();
        }
    }).on('keydown', function(e) {
        if (e.key === "Backspace" && this.value === "") {
            $(this).prev('.code-input').focus();
        }
    });

    // timer
    let duration = 120;
    let timerDisplay = $('#timer');
    let resendBtn = $('#resend-btn');

    function startTimer() {
        let remaining = duration;
        let interval = setInterval(function() {
            let minutes = String(Math.floor(remaining / 60)).padStart(2, '0');
            let seconds = String(remaining % 60).padStart(2, '0');
            let faDigits = minutes.replace(/\d/g, d => '۰۱۲۳۴۵۶۷۸۹'[d]) + ':' + seconds.replace(/\d/g, d => '۰۱۲۳۴۵۶۷۸۹'[d]);
            timerDisplay.text(faDigits);
            if (--remaining < 0) {
                clearInterval(interval);
                resendBtn.prop('disabled', false);
            }
        }, 1000);
    }
    startTimer();
});
</script>

<script>
function normalizeDigits(str){
  const p = '۰۱۲۳۴۵۶۷۸۹', a = '٠١٢٣٤٥٦٧٨٩';
  return str.replace(/[۰-۹٠-٩]/g, d => {
    const pi = p.indexOf(d); if (pi > -1) return String(pi);
    const ai = a.indexOf(d); if (ai > -1) return String(ai);
    return d;
  });
}

const inputs = document.querySelectorAll('.code-input');
const hidden = document.getElementById('otpHidden');

function fillHidden(){
  hidden.value = Array.from(inputs).map(i => i.value).join('');
}

inputs.forEach((el, idx) => {
  el.addEventListener('input', e => {
    let v = normalizeDigits(el.value).replace(/[^0-9]/g,'').slice(0,1);
    el.value = v;
    if (v && idx < inputs.length - 1) inputs[idx+1].focus();
    fillHidden();
  });
  el.addEventListener('keydown', e => {
    if (e.key === 'Backspace' && !el.value && idx > 0) inputs[idx-1].focus();
  });
});

document.getElementById('verify-form').addEventListener('submit', e => {
  fillHidden();
  if (hidden.value.length !== 4) {
    e.preventDefault();
    alert('کد ۴ رقمی را کامل وارد کنید');
  }
});
</script>
@endpush
@endsection
