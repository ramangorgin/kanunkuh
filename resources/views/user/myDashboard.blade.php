{{-- User dashboard overview page. --}}
@extends('user.layout')


@section('title', 'داشبورد')


@section('content')

@php
    $user = $user ?? auth()->user();

    // safety: relationship checks (use whichever relationship names exist in your models)
    $hasProfile = (bool) ($user->profile ?? false);
    $hasMedical = (bool) ($user->medicalRecord ?? false);

    $educationCount = 0;
    try {
        $educationCount = method_exists($user, 'educationalHistories') ? $user->educationalHistories()->count() : (method_exists($user, 'educational_histories') ? $user->educational_histories()->count() : 0);
    } catch (\Throwable $e) {
        $educationCount = 0;
    }
    $hasEducation = $educationCount > 0;

    $completedSteps = ($hasProfile ? 1 : 0) + ($hasMedical ? 1 : 0) + ($hasEducation ? 1 : 0);

    $registrationStatus = optional($user->profile)->membership_status; // null / 'pending' / 'approved' / 'rejected'
    $rejectionReason = optional($user->profile)->rejection_reason;

    // Photo logic
    $userPhoto = asset('images/default-avatar.png');
    if ($hasProfile && !empty($user->profile->photo)) {
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($user->profile->photo)) {
            $userPhoto = \Illuminate\Support\Facades\Storage::disk('public')->url($user->profile->photo);
        } elseif (file_exists(public_path($user->profile->photo))) {
            $userPhoto = asset($user->profile->photo);
        }
    }
@endphp

<div class="container py-4">

    {{-- Registration status / onboarding progress --}}
    <div class="mb-4">
        @if($completedSteps < 3)
            <div class="alert alert-info">
                <strong>ثبت‌نام نیمه‌تمام</strong>
                <div>شما در مرحله {{ $completedSteps }} از 3 قرار دارید. برای تکمیل ثبت‌نام مراحل زیر را انجام دهید:</div>
                <ul class="mt-2 mb-0">
                    <li>
                        مشخصات پایه:
                        @if($hasProfile) <span class="badge bg-success">تکمیل</span>
                        @else <a href="{{ route('dashboard.profile.edit') }}" class="btn btn-sm btn-outline-primary">تکمیل کنید</a> @endif
                    </li>
                    <li>
                        پرونده پزشکی:
                        @if($hasMedical) <span class="badge bg-success">تکمیل</span>
                        @else <a href="{{ route('dashboard.medicalRecord.edit') }}" class="btn btn-sm btn-outline-primary">تکمیل کنید</a> @endif
                    </li>
                    <li>
                        سوابق آموزشی:
                        @if($hasEducation) <span class="badge bg-success">تکمیل ({{ $educationCount }})</span>
                        @else <a href="{{ route('dashboard.educationalHistory.index') }}" class="btn btn-sm btn-outline-primary">افزودن سابقه</a> @endif
                    </li>
                </ul>
                <div class="mt-2 small text-muted">بعد از تکمیل همه مراحل، درخواست شما برای تایید ارسال می‌شود و نتیجه از طریق داشبورد و پیامک اطلاع داده خواهد شد.</div>
            </div>
        @else
            {{-- All steps complete: show registration approval state --}}
            @if($registrationStatus === 'approved')
                <div id="registration-success-alert" class="alert alert-success d-none">
                    <strong>ثبت‌نام شما تایید شده است.</strong>
                    <div class="small">خوش آمدید! اکنون می‌توانید از تمامی امکانات حساب کاربری استفاده کنید.</div>
                </div>
            @elseif($registrationStatus === 'rejected')
                <div class="alert alert-danger">
                    <strong>ثبت‌نام شما رد شده است.</strong>
                    @if($rejectionReason)
                        <div class="mt-1">دلیل: {{ $rejectionReason }}</div>
                    @endif
                    <div class="mt-2">برای اصلاح اطلاعات، فرم‌ها را ویرایش کنید یا با پشتیبانی تماس بگیرید.</div>
                    <div class="mt-2">
                        <a href="{{ route('dashboard.profile.edit') }}" class="btn btn-sm btn-outline-primary">ویرایش مشخصات</a>
                    </div>
                </div>
            @else
                <div class="alert alert-warning">
                    <strong>در انتظار بررسی</strong>
                    <div class="small">ثبت‌نام شما تکمیل شده و در انتظار بررسی و تایید مدیریت است. نتیجه به زودی اعلام خواهد شد.</div>
                    <div class="mt-2 small text-muted">می‌توانید وضعیت را از همین صفحه پیگیری کنید.</div>
                </div>
            @endif
        @endif
    </div>

    {{-- existing dashboard content (profile card, payments, settings) --}}
    <div class="card mb-4">
        <div class="card-body d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <img src="{{ $userPhoto }}" alt="عکس کاربر" class="img-thumbnail me-3" style="width: 120px; height: 120px; object-fit: cover;">

                <div>
                    <h5 class="mb-3">
                        {{ optional($user->profile)->first_name ?? '' }} {{ optional($user->profile)->last_name ?? '' }}
                    </h5>
                    <small class="text-muted mb-2">
                        وضعیت عضویت:
                        {{ optional($user->profile)->membership_status ? (optional($user->profile)->membership_status == 'approved' ? 'تایید شده' : (optional($user->profile)->membership_status == 'pending' ? 'در انتظار' : (optional($user->profile)->membership_status == 'rejected' ? 'رد شده' : optional($user->profile)->membership_status))) : ($completedSteps < 3 ? 'نیمه‌تمام' : 'در انتظار') }}
                    </small><br>
                    <small class="text-muted">
                        تاریخ عضویت:
                        {{ optional($user->profile)->membership_date ? toPersianDate(optional($user->profile)->membership_date) : 'تنظیم نشده' }}
                    </small>
                </div>
            </div>

            @if($user->role === 'admin')
                <div class="d-none d-md-block">
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-dark shadow-sm">
                        <i class="bi bi-speedometer2 me-2"></i> ورود به پنل مدیریت
                    </a>
                </div>
            @endif
        </div>
        {{-- Mobile Admin Button --}}
        @if($user->role === 'admin')
            <div class="card-footer d-md-none text-center">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-dark w-100">
                    <i class="bi bi-speedometer2 me-2"></i> ورود به پنل مدیریت
                </a>
            </div>
        @endif
    </div>

    {{-- rest of existing dashboard cards and links --}}
    <div class="row g-4">

        {{-- مشخصات کاربری --}}
        <div class="col-lg-3 col-md-6">
            <div class="card h-100">
                <div class="card-header">مشخصات کاربری</div>
                <div class="card-body">
                    <p><strong>نام:</strong> {{ optional($user->profile)->first_name ?? '' }} {{ optional($user->profile)->last_name ?? '' }}</p>
                    <p><strong>شماره تلفن:</strong> {{ $user->phone }}</p>
                    <a href="{{ route('dashboard.profile.edit') }}" class="btn btn-sm btn-outline-primary">ویرایش مشخصات</a>
                </div>
            </div>
        </div>


        {{-- پرداخت‌ها --}}
        <div class="col-lg-3 col-md-6">
            <div class="card h-100">
                <div class="card-header">پرداخت‌ها</div>
                <div class="card-body">
                    <p>لیست تراکنش‌های اخیر شما در این بخش نمایش داده خواهد شد.</p>
                    <a href="{{ route('dashboard.payments.index') }}" class="btn btn-sm btn-outline-primary">پرداخت جدید</a>
                </div>
            </div>
        </div>

        {{-- دوره‌ها --}}
        <div class="col-lg-3 col-md-6">
            <div class="card h-100">
                <div class="card-header">دوره‌ها</div>
                <div class="card-body">
                    <p>به لیست دوره‌های ثبت‌نام‌شده و گواهی‌های خود دسترسی سریع داشته باشید.</p>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('dashboard.courses.index') }}" class="btn btn-sm btn-outline-primary">دوره‌های من</a>
                        <a href="{{ route('dashboard.courses.index') }}#available" class="btn btn-sm btn-light">مشاهده دوره‌ها</a>
                    </div>
                </div>
            </div>
        </div>

        {{-- برنامه‌ها --}}
        <div class="col-lg-3 col-md-6">
            <div class="card h-100">
                <div class="card-header">برنامه‌ها</div>
                <div class="card-body">
                    <p>برنامه‌های ثبت‌شده یا در دسترس را مرور و وضعیت خود را مدیریت کنید.</p>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('dashboard.programs.index') }}" class="btn btn-sm btn-outline-primary">برنامه‌های من</a>
                        <a href="{{ route('dashboard.programs.index') }}#available" class="btn btn-sm btn-light">برنامه‌های فعال</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var alertEl = document.getElementById('registration-success-alert');
    if (!alertEl) return;

    var STORAGE_KEY = 'dashboard_approval_shown_at';
    var FIVE_DAYS_MS = 5 * 24 * 60 * 60 * 1000;
    var now = Date.now();
    var firstSeen = parseInt(localStorage.getItem(STORAGE_KEY), 10);

    if (Number.isNaN(firstSeen)) {
        localStorage.setItem(STORAGE_KEY, now.toString());
        alertEl.classList.remove('d-none');
        return;
    }

    if (now - firstSeen <= FIVE_DAYS_MS) {
        alertEl.classList.remove('d-none');
    } else {
        alertEl.remove();
    }
});
</script>
@endpush
