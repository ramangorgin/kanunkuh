@extends('user.layout')

@section('title', 'ویرایش مشخصات کاربری')

@section('breadcrumb')
    <a href="{{ route('dashboard.index') }}">داشبورد</a> / <span>ویرایش مشخصات کاربری</span>
@endsection

@push('styles')
<!--
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
-->
<style>
    
    /* File input styling */
    .file-input-wrapper {
        position: relative;
    }
    .file-input-wrapper input[type="file"] {
        font-family: 'Peyda', sans-serif !important;
    }
    .file-input-wrapper .file-name {
        font-family: 'Peyda', sans-serif !important;
        font-size: 0.875rem;
        color: #198754;
        margin-top: 0.25rem;
    }
    .file-input-wrapper .file-error {
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }
    
    /* Button font fix */
    .btn {
        font-family: 'Peyda', sans-serif !important;
    }
    
    /* Form elements font */
    .form-control, .form-select, .form-label {
        font-family: 'Peyda', sans-serif !important;
    }
</style>
@endpush

@section('content')

@php
use Morilog\Jalali\Jalalian;
use Carbon\Carbon;

$user = $user ?? auth()->user();
$profile = $profile ?? ($user ? $user->profile : null);

if (! function_exists('toPersianDate')) {
    function toPersianDate($date)
    {
        if (! $date) return '';
        try {
            return Jalalian::fromCarbon(Carbon::parse($date))->format('Y/m/d');
        } catch (\Throwable $e) {
            return '';
        }
    }
}
@endphp

<div class="container py-4">
    <!-- Top progress / onboarding hint -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div>
                <strong>مرحله ۱ از ۳</strong>
                <div class="text-muted small">تکمیل مشخصات پایه — این بخش قبل از پرونده پزشکی و سوابق آموزشی باید تکمیل شود.</div>
            </div>
            <div style="min-width:220px;">
                <div class="progress" style="height:10px; border-radius:8px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width:33%"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm" style="background: rgba(255,255,255,0.92); border-radius:12px;">
        <div class="card-body">
            <h4 class="mb-3">ویرایش مشخصات کاربری</h4>

            @if(session('success'))
                <div class="alert alert-success d-none">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger d-none">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div id="client-errors-profile" class="alert alert-danger d-none"></div>
            <form id="profile-form" method="POST" action="{{ route('dashboard.profile.update', $user->id ?? auth()->id()) }}" enctype="multipart/form-data" class="row g-3">
                @csrf
                @method('PUT')

                <!-- نام -->
                <div class="col-md-4">
                    <label class="form-label"><i class="bi bi-person"></i> نام <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="first_name" value="{{ old('first_name', $profile->first_name ?? '') }}" required>
                    <small class="form-text text-muted">نام کوچک خود را وارد کنید (مثلاً: علی)</small>
                </div>

                <!-- نام خانوادگی -->
                <div class="col-md-4">
                    <label class="form-label"><i class="bi bi-people"></i> نام خانوادگی <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="last_name" value="{{ old('last_name', $profile->last_name ?? '') }}" required>
                    <small class="form-text text-muted">نام خانوادگی مطابق با مدارک</small>
                </div>

                <!-- نام پدر -->
                <div class="col-md-4">
                    <label class="form-label"><i class="bi bi-person-lines-fill"></i> نام پدر</label>
                    <input type="text" class="form-control" name="father_name" value="{{ old('father_name', $profile->father_name ?? '') }}">
                    <small class="form-text text-muted">در صورت تمایل وارد کنید</small>
                </div>

                <!-- شماره شناسنامه -->
                <div class="col-md-4">
                    <label class="form-label"><i class="bi bi-card-heading"></i> شماره شناسنامه</label>
                    <input type="text" class="form-control" name="id_number" value="{{ old('id_number', $profile->id_number ?? '') }}">
                    <small class="form-text text-muted">فقط ارقام، اگر ندارید خالی بگذارید</small>
                </div>

                <!-- محل صدور -->
                <div class="col-md-4">
                    <label class="form-label"><i class="bi bi-geo-alt"></i> محل صدور</label>
                    <input type="text" class="form-control" name="id_place" value="{{ old('id_place', $profile->id_place ?? '') }}">
                    <small class="form-text text-muted">مثلاً: تهران</small>
                </div>

                <!-- تاریخ تولد -->
                <div class="col-md-4">
                    <label class="form-label"><i class="bi bi-calendar-event"></i> تاریخ تولد <span class="text-danger">*</span></label>

                    <div class="input-group">
                        <input type="text" id="birth_date" name="birth_date" data-jdp
                               class="form-control"
                               value="{{ old('birth_date', toPersianDate($profile->birth_date ?? null)) }}">
                        <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                    </div>

                    <small class="form-text text-muted">تاریخ تولد خود را مطابق شناسنامه انتخاب کنید</small>
                </div>

                <!-- کدملی -->
                <div class="col-md-6">
                    <label class="form-label"><i class="bi bi-person-vcard"></i> کد ملی <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="national_id"
                           value="{{ old('national_id', $profile->national_id ?? '') }}">
                    <small class="form-text text-muted">۱۰ رقم کد ملی بدون فاصله</small>
                </div>

                <!-- عکس پرسنلی -->
                <div class="col-md-6">
                    <label class="form-label"><i class="bi bi-image"></i> عکس پرسنلی <span class="text-danger">*</span></label>
                    <div class="file-input-wrapper">
                        <input type="file" class="form-control file-upload-input" name="photo" accept=".jpg,.jpeg,.png" data-max-size="2">
                        <div class="file-name"></div>
                        <div class="file-error"></div>
                    </div>
                    <small class="form-text text-muted">عکس واضح، فرمت JPG یا PNG. حداکثر 2 مگابایت</small>

                    @if(!empty($profile->photo))
                        <div class="mt-3 text-center">
                            <img src="{{ asset('storage/' . $profile->photo) }}"
                                alt="عکس پرسنلی"
                                class="img-thumbnail shadow-sm"
                                style="max-width: 160px; border-radius: 10px;">
                        </div>
                    @endif
                </div>

                <!-- کارت ملی -->
                <div class="col-md-6">
                    <label class="form-label"><i class="bi bi-credit-card"></i> اسکن کارت ملی <span class="text-danger">*</span></label>
                    <div class="file-input-wrapper">
                        <input type="file" class="form-control file-upload-input" name="national_card" accept=".jpg,.jpeg,.png,.pdf" data-max-size="4">
                        <div class="file-name"></div>
                        <div class="file-error"></div>
                    </div>
                    <small class="form-text text-muted">اسکن خوانا از کارت ملی (حداکثر ۴ مگابایت)</small>
                    @if(!empty($profile->national_card))
                        <div class="mt-3 text-center">
                            <img src="{{ asset('storage/' . $profile->national_card) }}"
                                alt="کارت ملی"
                                class="img-thumbnail shadow-sm"
                                style="max-width: 160px; border-radius: 10px;">
                        </div>
                    @endif
                </div>

                <!-- تاهل -->
                <div class="col-md-6">
                    <label class="form-label"><i class="bi bi-heart"></i> وضعیت تأهل</label>
                    <select class="form-select" name="marital_status">
                        <option value="">انتخاب کنید...</option>
                        <option value="مجرد" {{ old('marital_status', $profile->marital_status ?? '') == 'مجرد' ? 'selected' : '' }}>مجرد</option>
                        <option value="متاهل" {{ old('marital_status', $profile->marital_status ?? '') == 'متاهل' ? 'selected' : '' }}>متاهل</option>
                    </select>
                    <small class="form-text text-muted">در صورت تغییر وضعیت، بروزرسانی شود</small>
                </div>

                <!-- تلفن ضروری -->
                <div class="col-md-6">
                    <label class="form-label"><i class="bi bi-telephone"></i> تلفن ضروری</label>
                    <input type="text" class="form-control" name="emergency_phone" value="{{ old('emergency_phone', $profile->emergency_phone ?? '') }}">
                    <small class="form-text text-muted">شماره‌ای که در شرایط اضطراری تماس گرفته شود</small>
                </div>

                <!-- معرف -->
                <div class="col-md-6">
                    <label class="form-label"><i class="bi bi-person-badge"></i> نام معرف</label>
                    <input type="text" class="form-control" name="referrer" value="{{ old('referrer', $profile->referrer ?? '') }}">
                    <small class="form-text text-muted">اگر کسی شما را معرفی کرده، نام او را وارد کنید</small>
                </div>

                <!-- تحصیلات -->
                <div class="col-md-6">
                    <label class="form-label"><i class="bi bi-book"></i> تحصیلات</label>
                    <input type="text" class="form-control" name="education" value="{{ old('education', $profile->education ?? '') }}">
                    <small class="form-text text-muted">مثلاً: کارشناسی ارشد — رشته</small>
                </div>

                <!-- شغل -->
                <div class="col-md-6">
                    <label class="form-label"><i class="bi bi-briefcase"></i> شغل</label>
                    <input type="text" class="form-control" name="job" value="{{ old('job', $profile->job ?? '') }}">
                    <small class="form-text text-muted">شغل فعلی یا عنوان کاری</small>
                </div>

                <!-- آدرس منزل -->
                <div class="col-12">
                    <label class="form-label"><i class="bi bi-house"></i> نشانی محل سکونت</label>
                    <textarea class="form-control" name="home_address" rows="2">{{ old('home_address', $profile->home_address ?? '') }}</textarea>
                    <small class="form-text text-muted">نشانی کامل برای ارسال مدارک (در صورت نیاز)</small>
                </div>

                <!-- آدرس محل کار -->
                <div class="col-12">
                    <label class="form-label"><i class="bi bi-building"></i> نشانی محل کار</label>
                    <textarea class="form-control" name="work_address" rows="2">{{ old('work_address', $profile->work_address ?? '') }}</textarea>
                    <small class="form-text text-muted">اختیاری</small>
                </div>

                <div class="col-12 mt-3 text-end">
                    <button type="submit" class="btn btn-success px-4 py-2">
                        {{ session('onboarding') ? 'ذخیره و رفتن به مرحله بعد' : 'ذخیره تغییرات' }}
                    </button>
                </div>
            </form>

            <div class="mt-3 text-muted small">
                راهنما: فرم بالا را تکمیل کرده و ذخیره کنید. بعد از ذخیره به صفحه پرونده پزشکی هدایت می‌شوید. در هر زمان می‌توانید این اطلاعات را ویرایش کنید.
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
        jalaliDatepicker.startWatch({ persianDigits: true });
</script>
<!--
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
-->
<script>
(function() {
    'use strict';
    
    // Configure Toastr
    toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: 'toast-bottom-center',
        timeOut: 6000,
        rtl: true,
    };

    // Show session messages
    @if(session('success'))
        toastr.success(@json(session('success')));
    @endif
    @if ($errors->any())
        @foreach ($errors->all() as $error)
            toastr.error(@json($error));
        @endforeach
    @endif

    // File input validation and display
    function setupFileInput(input) {
        if (input._fileSetup) return;
        input._fileSetup = true;
        
        const wrapper = input.closest('.file-input-wrapper');
        const fileNameDiv = wrapper ? wrapper.querySelector('.file-name') : null;
        const fileErrorDiv = wrapper ? wrapper.querySelector('.file-error') : null;
        const maxSizeMB = parseFloat(input.dataset.maxSize) || 2;
        const maxSizeBytes = maxSizeMB * 1024 * 1024;
        
        input.addEventListener('change', function() {
            // Clear previous messages
            if (fileNameDiv) fileNameDiv.textContent = '';
            if (fileErrorDiv) fileErrorDiv.textContent = '';
            
            if (this.files && this.files.length > 0) {
                const file = this.files[0];
                
                // Check file size
                if (file.size > maxSizeBytes) {
                    if (fileErrorDiv) {
                        fileErrorDiv.textContent = `حجم فایل (${(file.size / 1024 / 1024).toFixed(2)} مگابایت) بیشتر از حد مجاز (${maxSizeMB} مگابایت) است.`;
                    }
                    toastr.error(`حجم فایل "${file.name}" بیشتر از ${maxSizeMB} مگابایت است. لطفاً فایل کوچکتری انتخاب کنید.`);
                    this.value = ''; // Clear the input
                    return;
                }
                
                // Check file type based on accept attribute
                const acceptAttr = input.getAttribute('accept') || '';
                const allowedExtensions = acceptAttr.split(',').map(ext => ext.trim().toLowerCase());
                const fileExtension = '.' + file.name.split('.').pop().toLowerCase();
                
                let isValidType = false;
                for (const allowed of allowedExtensions) {
                    if (allowed === fileExtension || 
                        (allowed === 'image/*' && file.type.startsWith('image/')) ||
                        (allowed === 'application/pdf' && file.type === 'application/pdf')) {
                        isValidType = true;
                        break;
                    }
                }
                
                if (!isValidType && allowedExtensions.length > 0) {
                    if (fileErrorDiv) {
                        fileErrorDiv.textContent = 'نوع فایل مجاز نیست. فرمت‌های قابل قبول: ' + allowedExtensions.join(', ');
                    }
                    toastr.error('نوع فایل مجاز نیست.');
                    this.value = '';
                    return;
                }
                
                // Show file name
                if (fileNameDiv) {
                    fileNameDiv.textContent = `فایل انتخاب شده: ${file.name} (${(file.size / 1024).toFixed(1)} کیلوبایت)`;
                }
            }
        });
    }

    // DOM Ready
    document.addEventListener('DOMContentLoaded', function () {
        // Setup all file inputs
        document.querySelectorAll('.file-upload-input').forEach(setupFileInput);

        @if(session('onboarding'))
        const modalEl = document.getElementById('onboardingProfileModal');
        if (modalEl) {
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        }
        @endif

        // Basic client-side validation
        const form = document.getElementById('profile-form');
        const errorBox = document.getElementById('client-errors-profile');
        
        function showErrors(errors){
            if (!errors.length) { errorBox.classList.add('d-none'); errorBox.innerHTML=''; return; }
            errorBox.classList.remove('d-none');
            errorBox.innerHTML = '<ul class="mb-0">' + errors.map(e=>'<li>'+e+'</li>').join('') + '</ul>';
            window.scrollTo({ top: form.getBoundingClientRect().top + window.scrollY - 120, behavior: 'smooth' });
        }
        
        form?.addEventListener('submit', function(e){
            const errs = [];
            const get = name => form.querySelector('[name="'+name+'"]');
            const first = get('first_name')?.value?.trim() || '';
            const last  = get('last_name')?.value?.trim() || '';
            const nid   = get('national_id')?.value?.trim().replace(/[^\d]/g,'') || '';
            const bdate = get('birth_date')?.value?.trim() || '';

            if (first.length < 2 || first.length > 50) errs.push('طول نام باید بین ۲ تا ۵۰ باشد.');
            if (last.length < 2 || last.length > 50)  errs.push('طول نام خانوادگی باید بین ۲ تا ۵۰ باشد.');
            if (!/^\d{10}$/.test(nid)) errs.push('کد ملی باید ۱۰ رقم عددی باشد.');
            if (!/^\d{4}\/\d{2}\/\d{2}$/.test(bdate)) errs.push('تاریخ تولد را به فرمت YYYY/MM/DD وارد کنید.');

            // Check file sizes
            form.querySelectorAll('.file-upload-input').forEach(input => {
                if (input.files && input.files.length > 0) {
                    const file = input.files[0];
                    const maxSizeMB = parseFloat(input.dataset.maxSize) || 2;
                    if (file.size > maxSizeMB * 1024 * 1024) {
                        errs.push(`حجم فایل ${input.name} بیشتر از ${maxSizeMB} مگابایت است.`);
                    }
                }
            });

            if (errs.length){
                e.preventDefault();
                errs.forEach(m => toastr.error(m));
                showErrors(errs);
            }
        });
    });
})();
</script>
@endpush

@push('modals')
<div class="modal fade" id="onboardingProfileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">مرحله ۱ از ۳ — مشخصات هویتی</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                برای ثبت‌نام در باشگاه کانون کوه، ابتدا مشخصات هویتی خود را وارد کنید:
                <ul class="mt-2 mb-0">
                    <li>نام و نام‌خانوادگی، نام پدر، کدملی</li>
                    <li>تاریخ تولد (به شمسی)</li>
                    <li>آپلود عکس پرسنلی و کارت ملی</li>
                    <li>اطلاعات تماس، تحصیلات، شغل و نشانی‌ها</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">متوجه شدم</button>
            </div>
        </div>
    </div>
</div>
@endpush
