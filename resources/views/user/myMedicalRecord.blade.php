@extends('user.layout')

@section('title', 'ویرایش پرونده پزشکی')

@section('breadcrumb')
    <a href="{{ route('dashboard.index') }}">داشبورد</a> / <span>پرونده پزشکی</span>
@endsection

@push('styles')
<!--
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
-->
<style>
    /* Jalali datepicker z-index fixes */
    .jalali-datepicker { z-index: 2000 !important; }
    .jalali-datepicker .jalali-datepicker-legend { z-index: 2001 !important; }
    .jalali-datepicker-portal { z-index: 2000 !important; }
    
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
$medical = $medical ?? ($user ? $user->medicalRecord : (object)[]);

if (! function_exists('toPersianDate')) {
    function toPersianDate($date)
    {
        if (!$date) return '';
        try {
            return Jalalian::fromCarbon(Carbon::parse($date))->format('Y/m/d');
        } catch (\Throwable $e) {
            return '';
        }
    }
}
@endphp

<div class="container py-4">
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div>
                <strong>مرحله ۲ از ۳</strong>
                <div class="text-muted small">پرونده پزشکی — لطفاً اطلاعات سلامت و بیمه ورزشی را وارد کنید.</div>
            </div>
            <div style="min-width:220px;">
                <div class="progress" style="height:10px; border-radius:8px;">
                    <div class="progress-bar bg-warning" role="progressbar" style="width:66%"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm" style="background: rgba(255,255,255,0.92); border-radius:12px;">
        <div class="card-body">
            <h4 class="mb-3">ویرایش پرونده پزشکی</h4>

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

            <div id="client-errors-medical" class="alert alert-danger d-none"></div>
            <form id="medical-form" method="POST" action="{{ route('dashboard.medicalRecord.update') }}" enctype="multipart/form-data" class="row g-3">
                @csrf
                @method('PUT')

                <div class="col-md-6">
                    <label class="form-label"><i class="bi bi-calendar-event"></i> تاریخ صدور بیمه ورزشی</label>
                    <div class="input-group">
                        <input type="text" id="insurance_issue_date" name="insurance_issue_date" data-jdp
                            class="form-control"
                            value="{{ old('insurance_issue_date', toPersianDate($medical->insurance_issue_date ?? null)) }}">
                        <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                    </div>
                    <small class="form-text text-muted">تاریخ شروع پوشش بیمه (شمسی)</small>
                </div>

                <div class="col-md-6">
                    <label class="form-label"><i class="bi bi-calendar-check"></i> تاریخ اعتبار بیمه ورزشی</label>
                    <div class="input-group">
                        <input type="text" id="insurance_expiry_date" name="insurance_expiry_date" data-jdp
                            class="form-control"
                            value="{{ old('insurance_expiry_date', toPersianDate($medical->insurance_expiry_date ?? null)) }}">
                        <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                    </div>
                    <small class="form-text text-muted">تا چه تاریخی بیمه معتبر است</small>
                </div>

                <div class="col-md-12">
                    <label class="form-label"><i class="bi bi-file-earmark-medical"></i> فایل بیمه ورزشی (حداکثر ۲ مگابایت)</label>
                    <div class="file-input-wrapper">
                        <input type="file" class="form-control file-upload-input" name="insurance_file" accept=".jpg,.jpeg,.png,.pdf" data-max-size="2">
                        <div class="file-name"></div>
                        <div class="file-error"></div>
                    </div>
                    <small class="form-text text-muted">اسکن یا عکس بیمه ورزشی (JPEG/PNG/PDF)</small>
                    @if(!empty($medical->insurance_file))
                        <div class="mt-2">
                            <a href="{{ asset('storage/' . $medical->insurance_file) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                مشاهده فایل فعلی
                            </a>
                        </div>
                    @endif
                </div>

                <div class="col-md-3">
                    <label class="form-label"><i class="bi bi-droplet-half"></i> گروه خونی</label>
                    <select class="form-select" name="blood_type">
                        <option value="">انتخاب کنید...</option>
                        @foreach(['O+','O-','A+','A-','B+','B-','AB+','AB-'] as $type)
                            <option value="{{ $type }}" {{ (old('blood_type', $medical->blood_type ?? '') == $type) ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                    <small class="form-text text-muted">گروه خونی خود را انتخاب کنید یا خالی بگذارید</small>
                </div>

                <div class="col-md-3">
                    <label class="form-label"><i class="bi bi-rulers"></i> قد (سانتی‌متر)</label>
                    <input type="number" class="form-control" name="height" value="{{ old('height', $medical->height ?? '') }}">
                    <small class="form-text text-muted">مثلاً: 175</small>
                </div>

                <div class="col-md-3">
                    <label class="form-label"><i class="bi bi-person-lines-fill"></i> وزن (کیلوگرم)</label>
                    <input type="number" class="form-control" name="weight" value="{{ old('weight', $medical->weight ?? '') }}">
                    <small class="form-text text-muted">مثلاً: 70</small>
                </div>

                <div class="col-12 mt-3">
                    <h6 class="mb-2">سؤالات پزشکی</h6>
                    <div class="text-muted small mb-3">در صورت پاسخ «بله»، توضیحات را در کادر مربوطه وارد کنید.</div>
                </div>

                @php
                    $questions = [
                        'head_injury' => 'آیا سابقه ضربه مغزی یا آسیب به سر دارید؟',
                        'eye_ear_problems' => 'مشکلات چشمی یا گوشی (بیماری یا جراحی)',
                        'seizures' => 'حملات گیج‌کننده، غش یا تشنج',
                        'respiratory' => 'بیماری‌های تنفسی (آسم، برونشیت)',
                        'heart' => 'مشکلات قلبی یا تب روماتیسمی'
                    ];
                @endphp

                @foreach($questions as $name => $label)
                <div class="col-md-6">
                    <label class="form-label">{{ $label }}</label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input toggle-explain" type="radio"
                                   name="{{ $name }}" value="1" id="{{ $name }}_yes"
                                   {{ old($name, $medical->{$name} ?? null) == 1 ? 'checked' : '' }}>
                            <label class="form-check-label" for="{{ $name }}_yes">بله</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input toggle-explain" type="radio"
                                   name="{{ $name }}" value="0" id="{{ $name }}_no"
                                   {{ old($name, $medical->{$name} ?? null) == 0 ? 'checked' : '' }}>
                            <label class="form-check-label" for="{{ $name }}_no">خیر</label>
                        </div>
                    </div>
                    <textarea class="form-control mt-2 explain-field"
                              name="{{ $name }}_details"
                              placeholder="توضیحات..."
                              style="{{ (old($name, $medical->{$name} ?? null)) ? '' : 'display:none;' }}">{{ old($name.'_details', $medical->{$name.'_details'} ?? '') }}</textarea>
                    <small class="form-text text-muted">در صورت «بله»، موارد و تاریخچه را وارد کنید (مثلاً تاریخ جراحی)</small>
                </div>
                @endforeach

                <div class="col-12 mt-3">
                    <label class="form-label"><i class="bi bi-journal-text"></i> بیماری‌های دیگر یا توضیحات تکمیلی</label>
                    <textarea class="form-control" name="other_conditions" rows="3">{{ old('other_conditions', $medical->other_conditions ?? '') }}</textarea>
                    <small class="form-text text-muted">در صورت وجود موارد دیگر، وارد کنید</small>
                </div>

                <div class="col-12 mt-2">
                    <div class="form-check d-flex align-items-center gap-2">
                        <input type="hidden" name="commitment_signed" value="0">
                        <input type="checkbox" id="commitment_signed" class="form-check-input" name="commitment_signed" value="1"
                               {{ old('commitment_signed', $medical->commitment_signed ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label mb-0" for="commitment_signed">
                            <span class="text-danger">*</span>
                            ضمن تأیید مطالب فوق، مسئولیت ناشی از تمامی پیش‌آمدهای ممکن برای خود در برنامه را می‌پذیرم.
                        </label>
                    </div>
                </div>

                <div class="col-12 mt-3 text-end">
                    <button type="submit" id="medical-submit" class="btn btn-success px-4 py-2" {{ old('commitment_signed', $medical->commitment_signed ?? false) ? '' : 'disabled' }}>
                        {{ session('onboarding') ? 'ذخیره و رفتن به مرحله بعد' : 'ذخیره تغییرات' }}
                    </button>
                </div>
            </form>

            <div class="mt-3 text-muted small">
                توضیح سریع: اطلاعات پزشکی به‌صورت محرمانه نگهداری می‌شوند. اگر شک دارید، فیلدها را پر نکنید و بعداً پزشک مراجعه کنید.
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
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

        // Toggle explain fields
        document.querySelectorAll('.toggle-explain').forEach(function(radio) {
            radio.addEventListener('change', function() {
                const parent = this.closest('.col-md-6');
                const explainField = parent ? parent.querySelector('.explain-field') : null;
                if (explainField) {
                    if (this.value === "1") {
                        explainField.style.display = '';
                    } else {
                        explainField.style.display = 'none';
                        explainField.value = '';
                    }
                }
            });
        });

        // Enable/disable submit based on commitment checkbox
        const checkbox = document.getElementById('commitment_signed');
        const submitBtn = document.getElementById('medical-submit');
        if (checkbox && submitBtn) {
            const sync = () => { submitBtn.disabled = !checkbox.checked; };
            checkbox.addEventListener('change', sync);
            sync();
        }

        @if(session('onboarding'))
        const modalEl = document.getElementById('onboardingMedicalModal');
        if (modalEl) {
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        }
        @endif

        // Basic client-side validation
        const form = document.getElementById('medical-form');
        const errorBox = document.getElementById('client-errors-medical');
        
        function showErrors(errors){
            if (!errors.length) { errorBox.classList.add('d-none'); errorBox.innerHTML=''; return; }
            errorBox.classList.remove('d-none');
            errorBox.innerHTML = '<ul class="mb-0">' + errors.map(e=>'<li>'+e+'</li>').join('') + '</ul>';
            window.scrollTo({ top: form.getBoundingClientRect().top + window.scrollY - 120, behavior: 'smooth' });
        }
        
        form?.addEventListener('submit', function(e){
            const errs = [];
            const h = form.querySelector('[name="height"]')?.value?.trim();
            const w = form.querySelector('[name="weight"]')?.value?.trim();
            const issue = form.querySelector('[name="insurance_issue_date"]')?.value?.trim();
            const exp   = form.querySelector('[name="insurance_expiry_date"]')?.value?.trim();
            const commit = document.getElementById('commitment_signed')?.checked;
            
            if (h && (+h < 50 || +h > 250)) errs.push('قد باید بین ۵۰ تا ۲۵۰ سانتی‌متر باشد.');
            if (w && (+w < 20 || +w > 250)) errs.push('وزن باید بین ۲۰ تا ۲۵۰ کیلوگرم باشد.');
            if (issue && !/^\d{4}\/\d{2}\/\d{2}$/.test(issue)) errs.push('فرمت تاریخ صدور بیمه صحیح نیست.');
            if (exp && !/^\d{4}\/\d{2}\/\d{2}$/.test(exp)) errs.push('فرمت تاریخ انقضاء بیمه صحیح نیست.');
            if (!commit) errs.push('پذیرش تعهدنامه الزامی است.');
            
            // Check file sizes
            form.querySelectorAll('.file-upload-input').forEach(input => {
                if (input.files && input.files.length > 0) {
                    const file = input.files[0];
                    const maxSizeMB = parseFloat(input.dataset.maxSize) || 2;
                    if (file.size > maxSizeMB * 1024 * 1024) {
                        errs.push(`حجم فایل بیشتر از ${maxSizeMB} مگابایت است.`);
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
<div class="modal fade" id="onboardingMedicalModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">مرحله ۲ از ۳ — پرونده پزشکی</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                لطفاً اطلاعات سلامت و بیمه ورزشی خود را وارد کنید:
                <ul class="mt-2 mb-0">
                    <li>تاریخ صدور و اعتبار بیمه ورزشی و فایل آن</li>
                    <li>گروه خونی، قد و وزن</li>
                    <li>پاسخ به چند سؤال پزشکی (در صورت «بله»، توضیح کوتاه)</li>
                    <li>تأیید تعهدنامه برای ادامه مراحل</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">متوجه شدم</button>
            </div>
        </div>
    </div>
</div>
@endpush
