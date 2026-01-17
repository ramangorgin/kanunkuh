@extends('user.layout')

@section('title', 'سوابق آموزشی من')

@push('styles')
<!--
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
-->
<style>
    /* Ensure Jalali datepicker renders over Bootstrap modals */
    .jalali-datepicker { z-index: 200000 !important; }
    .jalali-datepicker .jalali-datepicker-legend { z-index: 200001 !important; }
    .jalali-datepicker-portal { z-index: 200000 !important; position: fixed !important; }
    
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
        color: #6c757d;
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
$user = $user ?? auth()->user();
$histories = $histories ?? collect();
$federationCourses = $federationCourses ?? collect();
@endphp

<div class="container py-4">
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div>
                <strong>مرحله ۳ از ۳</strong>
                <div class="text-muted small">سوابق آموزشی — حداقل یک سابقه جهت تکمیل ثبت‌نام اضافه کنید یا بعداً اضافه کنید.</div>
            </div>
            <div style="min-width:220px;">
                <div class="progress" style="height:10px; border-radius:8px;">
                    <div class="progress-bar bg-info" role="progressbar" style="width:100%"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm" style="background: rgba(255,255,255,0.92); border-radius:12px;">
        <div class="card-body">
            <h4 class="mb-3"><i class="bi bi-book-half"></i> سوابق آموزشی من</h4>

            <div class="text-end mb-3">
                <button class="btn btn-success" data-bs-toggle="collapse" data-bs-target="#addForm" aria-expanded="false" aria-controls="addForm">
                    <i class="bi bi-plus-circle"></i> افزودن سابقه جدید
                </button>
            </div>

            <!-- Add Form -->
            <div id="addForm" class="collapse {{ $histories->isEmpty() || $errors->any() ? 'show' : '' }}">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div id="client-errors-edu" class="alert alert-danger d-none"></div>
                        <form method="POST" action="{{ route('dashboard.educationalHistory.store') }}" enctype="multipart/form-data" id="multi-course-form">
                            @csrf
                            <div id="courses-list">
                                @if(old('courses'))
                                    {{-- If validation failed, restore all rows from old input --}}
                                    @foreach(old('courses') as $idx => $oldCourse)
                                        <div class="course-item row g-3 align-items-end border rounded p-2 mb-3">
                                            <div class="col-md-4">
                                                <label class="form-label">عنوان دوره <span class="text-danger">*</span></label>
                                                <select class="form-select select-course" name="courses[{{ $idx }}][federation_course_id]">
                                                    <option value="">انتخاب کنید...</option>
                                                    @foreach($federationCourses as $course)
                                                        <option value="{{ $course->id }}" {{ $oldCourse['federation_course_id'] == $course->id ? 'selected' : '' }}>{{ $course->title }}</option>
                                                    @endforeach
                                                    <option value="_custom" {{ ($oldCourse['federation_course_id'] ?? '') == '_custom' || ($oldCourse['federation_course_id'] == null && !empty($oldCourse['custom_course_title'])) ? 'selected' : '' }}>سایر (دوره سفارشی)</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4 custom-course-wrap" style="display:none;">
                                                <label class="form-label">نام دوره سفارشی <span class="text-danger">*</span></label>
                                                <input type="text" name="courses[{{ $idx }}][custom_course_title]" class="form-control" placeholder="نام دوره" value="{{ $oldCourse['custom_course_title'] ?? '' }}">
                                                <small class="form-text text-muted">نام دوره را وارد کنید</small>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">تاریخ صدور مدرک</label>
                                                <div class="input-group">
                                                    <input type="text" name="courses[{{ $idx }}][issue_date]" class="form-control" data-jdp value="{{ $oldCourse['issue_date'] ?? '' }}">
                                                    <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                                                </div>
                                            </div>
                                            <div class="col-12 mt-2 mb-3">
                                                <label class="form-label">فایل مدرک (اختیاری - حداکثر ۲ مگابایت)</label>
                                                <div class="file-input-wrapper">
                                                    <input type="file" name="courses[{{ $idx }}][certificate_file]" class="form-control certificate-file-input" accept=".jpg,.jpeg,.png,.pdf">
                                                    <div class="file-name"></div>
                                                    <div class="file-error"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-1 text-end">
                                                <button type="button" class="btn btn-outline-danger remove-course" title="حذف"><i class="bi bi-x-lg"></i></button>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    {{-- Initial empty row --}}
                                    <div class="course-item row g-3 align-items-end border rounded p-2 mb-3">
                                        <div class="col-md-4">
                                            <label class="form-label">عنوان دوره <span class="text-danger">*</span></label>
                                            <select class="form-select select-course" name="courses[0][federation_course_id]">
                                                <option value="">انتخاب کنید...</option>
                                                @foreach($federationCourses as $course)
                                                    <option value="{{ $course->id }}">{{ $course->title }}</option>
                                                @endforeach
                                                <option value="_custom">سایر (دوره سفارشی)</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4 custom-course-wrap" style="display:none;">
                                            <label class="form-label">نام دوره سفارشی <span class="text-danger">*</span></label>
                                            <input type="text" name="courses[0][custom_course_title]" class="form-control" placeholder="نام دوره">
                                            <small class="form-text text-muted">نام دوره را وارد کنید</small>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">تاریخ صدور مدرک</label>
                                            <div class="input-group">
                                                <input type="text" name="courses[0][issue_date]" class="form-control" data-jdp>
                                                <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                                            </div>
                                        </div>
                                        <div class="col-12 mt-2 mb-3">
                                            <label class="form-label">فایل مدرک (اختیاری - حداکثر ۲ مگابایت)</label>
                                            <div class="file-input-wrapper">
                                                <input type="file" name="courses[0][certificate_file]" class="form-control certificate-file-input" accept=".jpg,.jpeg,.png,.pdf">
                                                <div class="file-name"></div>
                                                <div class="file-error"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-1 text-end">
                                            <button type="button" class="btn btn-outline-danger remove-course" title="حذف"><i class="bi bi-x-lg"></i></button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="text-end mt-3">
                                <button type="button" id="add-course-row" class="btn btn-outline-primary me-2">
                                    <i class="bi bi-plus-circle"></i> افزودن دوره
                                </button>
                                <button type="submit" class="btn btn-success">ذخیره</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            @if($histories->isEmpty())
                <div class="alert alert-light text-center">هیچ سابقه آموزشی ثبت نشده است. می‌توانید با کلیک روی «افزودن سابقه جدید» شروع کنید.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ردیف</th>
                                <th>عنوان دوره</th>
                                <th>مدرک</th>
                                <th>عملیات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($histories as $index => $history)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $history->federationCourse->title ?? ($history->custom_course_title ?? '---') }}</td>

                                    <td>
                                        @if($history->certificate_file)
                                            <a href="{{ asset('storage/' . $history->certificate_file) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-file-earmark-arrow-down"></i> مشاهده
                                            </a>
                                        @else
                                            <span class="text-muted">ندارد</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-warning btn-sm" data-bs-toggle="collapse" data-bs-target="#editRow{{ $history->id }}">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <form action="{{ route('dashboard.educationalHistory.destroy', $history->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                    onclick="return confirm('آیا از حذف این سابقه مطمئن هستید؟')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                                <!-- Inline edit collapse -->
                                <tr class="collapse" id="editRow{{ $history->id }}">
                                    <td colspan="4">
                                        <div class="card border-0 shadow-sm">
                                            <div class="card-body">
                                            <form method="POST" action="{{ route('dashboard.educationalHistory.update', $history->id) }}" enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')
                                                    <div class="row g-3">
                                                        <div class="col-md-6">
                                                        <label class="form-label">عنوان دوره</label>
                                                            <select class="form-select select-course" name="federation_course_id">
                                                                <option value="">انتخاب کنید...</option>
                                                            @foreach($federationCourses as $course)
                                                                    <option value="{{ $course->id }}" {{ $course->id == $history->federation_course_id ? 'selected' : '' }}>
                                                                    {{ $course->title }}
                                                                </option>
                                                            @endforeach
                                                                <option value="_custom" {{ !$history->federation_course_id ? 'selected' : '' }}>سایر (دوره سفارشی)</option>
                                                        </select>
                                                        <small class="form-text text-muted">از لیست دوره مرتبط را انتخاب کنید</small>
                                                    </div>
                                                        <div class="col-md-6 custom-course-wrap" style="{{ $history->federation_course_id ? 'display:none;' : '' }}">
                                                            <label class="form-label">نام دوره سفارشی</label>
                                                            <input type="text" class="form-control" name="custom_course_title" value="{{ old('custom_course_title', $history->custom_course_title) }}">
                                                            <small class="form-text text-muted">در صورت نبودن در لیست، نام دوره را اینجا وارد کنید</small>
                                                        </div>
                                                        <div class="col-md-6">
                                                        <label class="form-label">تاریخ صدور مدرک</label>
                                                            <div class="input-group">
                                                                <input type="text" class="form-control" data-jdp name="issue_date" value="{{ $history->issue_date_jalali }}">
                                                                <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                                                            </div>
                                                        <small class="form-text text-muted">تاریخ به فرمت شمسی</small>
                                                    </div>
                                                        <div class="col-12 mt-2 mb-4">
                                                        <label class="form-label">فایل مدرک (اختیاری - حداکثر ۲ مگابایت)</label>
                                                            <div class="file-input-wrapper">
                                                                <input type="file" name="certificate_file" class="form-control certificate-file-input" accept=".jpg,.jpeg,.png,.pdf">
                                                                <div class="file-name"></div>
                                                                <div class="file-error"></div>
                                                            </div>
                                                        @if($history->certificate_file)
                                                            <div class="mt-1"><small class="text-muted">فایل فعلی: {{ basename($history->certificate_file) }}</small></div>
                                                        @endif
                                                    </div>
                                                    </div>
                                                <div class="text-end mt-3">
                                                    <button type="button" class="btn btn-secondary" data-bs-toggle="collapse" data-bs-target="#editRow{{ $history->id }}">
                                                        انصراف
                                                    </button>
                                                    <button type="submit" class="btn btn-success">ذخیره تغییرات</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $histories->links() }}
                </div>

                <div class="text-end mt-3">
                    <button class="btn btn-success" data-bs-toggle="collapse" data-bs-target="#addForm" aria-expanded="false" aria-controls="addForm">
                        <i class="bi bi-plus-circle"></i> افزودن سابقه جدید
                    </button>
                </div>
            @endif
        </div>
    </div>

    <div class="mt-3 text-muted small">
        راهنما: بهتر است حداقل یک سابقه وارد کنید تا ثبت‌نام کامل شود. اما می‌توانید بعداً نیز اضافه کنید.
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
    
    // Max file size in bytes (2MB)
    const MAX_FILE_SIZE = 2 * 1024 * 1024;
    const MAX_FILE_SIZE_MB = 2;
    
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
    @if ($errors ?? false)
        @foreach (($errors->all() ?? []) as $error)
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
        
        input.addEventListener('change', function() {
            // Clear previous messages
            if (fileNameDiv) fileNameDiv.textContent = '';
            if (fileErrorDiv) fileErrorDiv.textContent = '';
            
            if (this.files && this.files.length > 0) {
                const file = this.files[0];
                
                // Check file size
                if (file.size > MAX_FILE_SIZE) {
                    if (fileErrorDiv) {
                        fileErrorDiv.textContent = `حجم فایل (${(file.size / 1024 / 1024).toFixed(2)} مگابایت) بیشتر از حد مجاز (${MAX_FILE_SIZE_MB} مگابایت) است.`;
                    }
                    toastr.error(`حجم فایل "${file.name}" بیشتر از ${MAX_FILE_SIZE_MB} مگابایت است. لطفاً فایل کوچکتری انتخاب کنید.`);
                    this.value = ''; // Clear the input
                    return;
                }
                
                // Check file type
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
                const allowedExtensions = ['.jpg', '.jpeg', '.png', '.pdf'];
                const fileExtension = '.' + file.name.split('.').pop().toLowerCase();
                
                if (!allowedTypes.includes(file.type) && !allowedExtensions.includes(fileExtension)) {
                    if (fileErrorDiv) {
                        fileErrorDiv.textContent = 'فقط فایل‌های JPG، PNG و PDF مجاز هستند.';
                    }
                    toastr.error('نوع فایل مجاز نیست. فقط JPG، PNG و PDF قابل قبول است.');
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

    // --- Unified helper for course logic ---
    function setupRowLogic(row) {
        if (!row || row.dataset.logicInitialized) return;
        row.dataset.logicInitialized = "true";

        // 1. Custom course toggle
        const sel = row.querySelector('.select-course');
        const wrap = row.querySelector('.custom-course-wrap');
        if (sel && wrap) {
            const sync = () => {
                const isCustom = sel.value === '_custom';
                wrap.style.display = isCustom ? '' : 'none';
            };
            sel.addEventListener('change', sync);
            sync();
        }
        
        // 2. Lock used courses
        const syncLocks = () => {
            const allSelects = document.querySelectorAll('.select-course');
            const used = new Set();
            allSelects.forEach(s => { if (s.value && s.value !== '_custom') used.add(s.value); });
            allSelects.forEach(s => {
                const current = s.value;
                s.querySelectorAll('option').forEach(opt => {
                    if (!opt.value || opt.value === '_custom') return;
                    opt.disabled = used.has(opt.value) && opt.value !== current;
                });
            });
        };
        if (sel) {
            sel.addEventListener('change', syncLocks);
            syncLocks();
        }
        
        // 3. Setup file inputs in this row
        row.querySelectorAll('.certificate-file-input').forEach(setupFileInput);
    }

    // DOM Ready
    document.addEventListener('DOMContentLoaded', function () {
        // Setup existing rows
        document.querySelectorAll('.course-item, .card-body').forEach(setupRowLogic);
        
        // Setup all file inputs
        document.querySelectorAll('.certificate-file-input').forEach(setupFileInput);

        // Handle collapse show events
        document.addEventListener('shown.bs.collapse', function(e) {
            const root = e.target;
            if (window.jalaliDatepicker && jalaliDatepicker.startWatch) {
                jalaliDatepicker.startWatch({ persianDigits: true });
            }
            root.querySelectorAll('.select-course').forEach(s => {
                const container = s.closest('.row, .card-body');
                if (container) setupRowLogic(container);
            });
            root.querySelectorAll('.certificate-file-input').forEach(setupFileInput);
        });

        @if(session('onboarding'))
        const modalEl = document.getElementById('onboardingEduModal');
        if (modalEl) {
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        }
        @endif
        // --- Dynamic Add Row (SAFE INDEX VERSION) ---
        (function(){
            const list = document.getElementById('courses-list');
            const addBtn = document.getElementById('add-course-row');

            // Function to detect the TRUE highest existing index
            function getNextIndex() {
                let maxIndex = -1;

                document.querySelectorAll('input[name^="courses["]').forEach(input => {
                    const match = input.name.match(/courses\[(\d+)\]/);
                    if (match) {
                        const num = parseInt(match[1]);
                        if (!isNaN(num)) maxIndex = Math.max(maxIndex, num);
                    }
                });

                return maxIndex + 1; // next safe index
            }

            let idx = getNextIndex();

            const template = (i) => `
                <div class="course-item row g-3 align-items-end border rounded p-2 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">عنوان دوره <span class="text-danger">*</span></label>
                        <select class="form-select select-course" name="courses[${i}][federation_course_id]">
                            <option value="">انتخاب کنید...</option>
                            @foreach($federationCourses as $course)
                                <option value="{{ $course->id }}">{{ $course->title }}</option>
                            @endforeach
                            <option value="_custom">سایر (دوره سفارشی)</option>
                        </select>
                    </div>

                    <div class="col-md-4 custom-course-wrap" style="display:none;">
                        <label class="form-label">نام دوره سفارشی <span class="text-danger">*</span></label>
                        <input type="text" name="courses[${i}][custom_course_title]" class="form-control" placeholder="نام دوره">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">تاریخ صدور مدرک</label>
                        <div class="input-group">
                            <input type="text" name="courses[${i}][issue_date]" class="form-control" data-jdp>
                            <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                        </div>
                    </div>

                    <div class="col-12 mt-2 mb-3">
                        <label class="form-label">فایل مدرک (اختیاری - حداکثر ۲ مگابایت)</label>
                        <div class="file-input-wrapper">
                            <input type="file" name="courses[${i}][certificate_file]" class="form-control certificate-file-input" accept=".jpg,.jpeg,.png,.pdf">
                            <div class="file-name"></div>
                            <div class="file-error"></div>
                        </div>
                    </div>

                    <div class="col-md-1 text-end">
                        <button type="button" class="btn btn-outline-danger remove-course" title="حذف">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                </div>`;

            if (addBtn) {
                addBtn.addEventListener('click', function() {
                    idx = getNextIndex(); // recalc every time

                    const div = document.createElement('div');
                    div.innerHTML = template(idx);
                    const newItem = div.firstElementChild;

                    list.appendChild(newItem);

                    if (window.jalaliDatepicker?.startWatch) {
                        jalaliDatepicker.startWatch({ persianDigits: true });
                    }

                    setupRowLogic(newItem);
                });
            }

            if (list) {
                list.addEventListener('click', function(e){
                    if (e.target.closest('.remove-course')) {
                        const item = e.target.closest('.course-item');
                        if (item && list.children.length > 1) {
                            item.remove();

                            const s = document.querySelector('.select-course'); 
                            if (s) s.dispatchEvent(new Event('change'));
                        }
                    }
                });
            }
        })();

    });
})();
</script>
@endpush
