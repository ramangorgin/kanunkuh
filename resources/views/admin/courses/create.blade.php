@extends('admin.layout')

@section('title', 'ایجاد دوره جدید')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.courses.index') }}">دوره‌ها</a></li>
            <li class="breadcrumb-item active" aria-current="page">ایجاد دوره جدید</li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i> ایجاد دوره جدید</h5>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.courses.store') }}" id="course-form" enctype="multipart/form-data">
                @csrf

                {{-- 1. مشخصات اولیه --}}
                <h5 class="mb-3 text-primary"><i class="bi bi-info-circle me-2"></i> مشخصات اولیه</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-12">
                        <label class="form-label">عنوان دوره <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                    </div>
                </div>

                <hr>

                {{-- 2. دوره فدراسیون --}}
                <h5 class="mb-3 text-primary"><i class="bi bi-bookmark me-2"></i> دوره فدراسیون</h5>
                <div class="mb-3">
                    <label class="form-label">آیا این دوره مربوط به دوره فدراسیون است؟</label>
                    <select name="is_federation_course" id="is_federation_course" class="form-select">
                        <option value="0" {{ old('is_federation_course', '0') == '0' ? 'selected' : '' }}>خیر</option>
                        <option value="1" {{ old('is_federation_course') == '1' ? 'selected' : '' }}>بله</option>
                    </select>
                </div>

                <div id="federation_section" class="row g-3 mb-4" style="display: none;">
                    <div class="col-md-6">
                        <label class="form-label">دوره فدراسیون <span class="text-danger">*</span></label>
                        <select name="federation_course_id" id="federation_course_id" class="form-select">
                            <option value="">انتخاب کنید</option>
                            @foreach($federationCourses as $fc)
                                <option value="{{ $fc->id }}" {{ old('federation_course_id') == $fc->id ? 'selected' : '' }}>
                                    {{ $fc->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div id="prerequisites_section" class="mb-4" style="display: none;">
                    <label class="form-label">پیش‌نیازها</label>
                    <select name="prerequisites[]" id="prerequisites" class="form-select" multiple>
                        @foreach($federationCourses as $fc)
                            <option value="{{ $fc->id }}">{{ $fc->title }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">می‌توانید چند پیش‌نیاز انتخاب کنید</small>
                </div>

                <hr>

                {{-- 3. مدرس --}}
                <h5 class="mb-3 text-primary"><i class="bi bi-person-badge me-2"></i> مدرس</h5>
                <div class="mb-3">
                    <label class="form-label">نحوه انتخاب مدرس</label>
                    <select name="create_new_teacher" id="create_new_teacher" class="form-select">
                        <option value="0" {{ old('create_new_teacher', '0') == '0' ? 'selected' : '' }}>انتخاب از مدرسین موجود</option>
                        <option value="1" {{ old('create_new_teacher') == '1' ? 'selected' : '' }}>ایجاد مدرس جدید</option>
                    </select>
                </div>

                <div id="teacher_select_section" class="row g-3 mb-4">
                    <div class="col-md-12">
                        <label class="form-label">مدرس <span class="text-danger">*</span></label>
                        <select name="teacher_id" id="teacher_id" class="form-select">
                            <option value="">انتخاب کنید</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                    {{ $teacher->first_name }} {{ $teacher->last_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div id="teacher_create_section" class="row g-3 mb-4" style="display: none;">
                    <div class="col-md-6">
                        <label class="form-label">نام <span class="text-danger">*</span></label>
                        <input type="text" name="teacher_first_name" class="form-control" value="{{ old('teacher_first_name') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">نام خانوادگی <span class="text-danger">*</span></label>
                        <input type="text" name="teacher_last_name" class="form-control" value="{{ old('teacher_last_name') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">تاریخ تولد</label>
                        <div class="input-group">
                            <input type="text" name="teacher_birth_date" id="teacher_birth_date" class="form-control" data-jdp value="{{ old('teacher_birth_date') }}" autocomplete="off">
                            <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">تصویر پروفایل</label>
                        <div class="teacher-image-upload-container">
                            <div class="upload-area border rounded p-4 text-center mb-3" id="teacher-upload-area" style="cursor: pointer; background: #f8f9fa; transition: all 0.3s;">
                                <i class="bi bi-cloud-upload fs-1 text-primary d-block mb-2"></i>
                                <p class="mb-1 fw-bold">برای آپلود تصویر پروفایل کلیک کنید</p>
                                <p class="text-muted small mb-0">فرمت‌های مجاز: JPG, PNG, GIF | حداکثر اندازه: 2 مگابایت</p>
                            </div>
                            <input type="file" name="teacher_profile_image" id="teacher-image-input" class="d-none" accept="image/jpeg,image/png,image/gif">
                            <div id="teacher-image-preview" class="row g-3"></div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">زندگی‌نامه</label>
                        <textarea name="teacher_biography" class="form-control" rows="3">{{ old('teacher_biography') }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">مهارت‌ها</label>
                        <select name="teacher_skills[]" id="teacher_skills" class="form-select select2-tags" multiple>
                            <option value="کوهنوردی">کوهنوردی</option>
                            <option value="سنگ‌نوردی">سنگ‌نوردی</option>
                            <option value="یخ‌نوردی">یخ‌نوردی</option>
                            <option value="غارنوردی">غارنوردی</option>
                            <option value="دره‌نوردی">دره‌نوردی</option>
                            <option value="کوهنوردی با اسکی">کوهنوردی با اسکی</option>
                            <option value="نقشه‌خوانی">نقشه‌خوانی</option>
                            <option value="کار با GPS">کار با GPS</option>
                            <option value="کار با قطب‌نما">کار با قطب‌نما</option>
                            <option value="پزشکی کوهستان">پزشکی کوهستان</option>
                            <option value="نجات در کوهستان">نجات در کوهستان</option>
                            <option value="هواشناسی کوهستان">هواشناسی کوهستان</option>
                            <option value="حفظ محیط کوهستان">حفظ محیط کوهستان</option>
                        </select>
                        <small class="text-muted">می‌توانید مهارت‌های جدید اضافه کنید</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">گواهینامه‌ها</label>
                        <select name="teacher_certificates[]" id="teacher_certificates" class="form-select select2-tags" multiple>
                            <option value="راهنمای کوهستان سطح باشگاه‌های کوهپیمایی و کوهنوردی">راهنمای کوهستان سطح باشگاه‌های کوهپیمایی و کوهنوردی</option>
                            <option value="راهنمای کوهستان سطح باشگاه‌های غارنوردی">راهنمای کوهستان سطح باشگاه‌های غارنوردی</option>
                            <option value="راهنمای کوهستان سطح باشگاه‌های سنگ‌نوردی طبیعت">راهنمای کوهستان سطح باشگاه‌های سنگ‌نوردی طبیعت</option>
                            <option value="راهنمای کوهستان سطح باشگاه‌های دره‌نوردی">راهنمای کوهستان سطح باشگاه‌های دره‌نوردی</option>
                            <option value="راهنمای کوهستان سطح باشگاه‌های یخ‌نوردی">راهنمای کوهستان سطح باشگاه‌های یخ‌نوردی</option>
                            <option value="راهنمای کوهستان سطح باشگاه‌های کوهنوردی بالسکی">راهنمای کوهستان سطح باشگاه‌های کوهنوردی بالسکی</option>
                            <option value="کارآموزی کوهپیمایی">کارآموزی کوهپیمایی</option>
                            <option value="کارآموزی برف و یخ">کارآموزی برف و یخ</option>
                            <option value="پیشرفته برف و یخ">پیشرفته برف و یخ</option>
                            <option value="کارآموزی سنگ‌نوردی">کارآموزی سنگ‌نوردی</option>
                            <option value="پیشرفته سنگ‌نوردی">پیشرفته سنگ‌نوردی</option>
                            <option value="کارآموزی دره‌نوردی">کارآموزی دره‌نوردی</option>
                            <option value="پیشرفته دره‌نوردی">پیشرفته دره‌نوردی</option>
                            <option value="کارآموزی غارنوردی">کارآموزی غارنوردی</option>
                            <option value="پیشرفته غارنوردی">پیشرفته غارنوردی</option>
                            <option value="کارآموزی کوهنوردی با اسکی">کارآموزی کوهنوردی با اسکی</option>
                            <option value="پیشرفته کوهنوردی با اسکی">پیشرفته کوهنوردی با اسکی</option>
                            <option value="دوره آبشار یخی">دوره آبشار یخی</option>
                            <option value="دوره دیواره‌نوردی">دوره دیواره‌نوردی</option>
                            <option value="دوره غارپیمایی">دوره غارپیمایی</option>
                            <option value="امداد و نجات در غار">امداد و نجات در غار</option>
                            <option value="کارگاه آموزشی نقشه‌خوانی و کار با قطب‌نما">کارگاه آموزشی نقشه‌خوانی و کار با قطب‌نما</option>
                            <option value="کارگاه آموزشی حفظ محیط کوهستان">کارگاه آموزشی حفظ محیط کوهستان</option>
                            <option value="کارگاه آموزشی هواشناسی کوهستان">کارگاه آموزشی هواشناسی کوهستان</option>
                            <option value="کارگاه آموزشی کار با GPS">کارگاه آموزشی کار با GPS</option>
                            <option value="کارگاه آموزشی مبانی جستجو در کوه">کارگاه آموزشی مبانی جستجو در کوه</option>
                            <option value="کارگاه آموزشی پزشکی کوهستان">کارگاه آموزشی پزشکی کوهستان</option>
                            <option value="کارگاه آموزشی نجات در برف">کارگاه آموزشی نجات در برف</option>
                            <option value="کارگاه آموزشی مبانی نجات فنی">کارگاه آموزشی مبانی نجات فنی</option>
                            <option value="کارگاه آموزشی نقشه‌برداری غار">کارگاه آموزشی نقشه‌برداری غار</option>
                            <option value="کارگاه آموزشی زمین‌شناسی و مورفولوژی کارست">کارگاه آموزشی زمین‌شناسی و مورفولوژی کارست</option>
                        </select>
                        <small class="text-muted">می‌توانید گواهینامه‌های جدید اضافه کنید</small>
                    </div>
                </div>

                <hr>

                {{-- 4. تاریخ و زمان --}}
                <h5 class="mb-3 text-primary"><i class="bi bi-calendar-event me-2"></i> تاریخ و زمان</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label">تاریخ شروع <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" name="start_date" id="start_date" class="form-control" data-jdp value="{{ old('start_date') }}" required autocomplete="off">
                            <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">تاریخ پایان <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" name="end_date" id="end_date" class="form-control" data-jdp value="{{ old('end_date') }}" required autocomplete="off">
                            <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">مدت (روز)</label>
                        <input type="number" name="duration" id="duration" class="form-control" value="{{ old('duration') }}" min="0">
                        <small class="text-muted">به صورت خودکار از تاریخ شروع و پایان محاسبه می‌شود (قابل ویرایش)</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">ساعت شروع</label>
                        <input type="time" name="start_time" class="form-control" value="{{ old('start_time') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">ساعت پایان</label>
                        <input type="time" name="end_time" class="form-control" value="{{ old('end_time') }}">
                    </div>
                </div>

                <hr>

                {{-- 5. محل برگزاری --}}
                <h5 class="mb-3 text-primary"><i class="bi bi-geo-alt me-2"></i> محل برگزاری</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">نام محل</label>
                        <input type="text" name="place" class="form-control" value="{{ old('place') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">آدرس</label>
                        <input type="text" name="place_address" class="form-control" value="{{ old('place_address') }}">
                    </div>

                    <input type="hidden" name="place_lat" id="place_lat" value="{{ old('place_lat') }}">
                    <input type="hidden" name="place_lon" id="place_lon" value="{{ old('place_lon') }}">

                    <div class="col-md-12">
                        <label class="form-label">انتخاب موقعیت روی نقشه</label>
                        <div id="course_location_map" style="height: 400px; border-radius: 8px; border: 1px solid #dee2e6;"></div>
                        <small class="text-muted">برای انتخاب موقعیت، روی نقشه کلیک کنید</small>
                    </div>
                </div>

                <hr>

                {{-- 6. ظرفیت --}}
                <h5 class="mb-3 text-primary"><i class="bi bi-people me-2"></i> ظرفیت</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">ظرفیت دوره</label>
                        <input type="number" name="capacity" class="form-control" value="{{ old('capacity') }}" min="1">
                        <small class="text-muted">تعداد نفرات قابل ثبت‌نام</small>
                    </div>
                </div>

                <hr>

                {{-- 7. هزینه --}}
                <h5 class="mb-3 text-primary"><i class="bi bi-cash-coin me-2"></i> هزینه</h5>
                <div class="mb-3">
                    <label class="form-label">آیا دوره رایگان است؟</label>
                    <select name="is_free" id="is_free" class="form-select">
                        <option value="0" {{ old('is_free', '0') == '0' ? 'selected' : '' }}>خیر</option>
                        <option value="1" {{ old('is_free') == '1' ? 'selected' : '' }}>بله</option>
                    </select>
                </div>

                <div id="cost_section" class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">هزینه برای اعضا (ریال) <span class="text-danger">*</span></label>
                        <input type="text" name="member_cost" id="member_cost" class="form-control cost-input" value="{{ old('member_cost') }}" required>
                        <small class="text-muted">فقط اعداد انگلیسی مجاز است</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">هزینه برای مهمانان (ریال) <span class="text-danger">*</span></label>
                        <input type="text" name="guest_cost" id="guest_cost" class="form-control cost-input" value="{{ old('guest_cost') }}" required>
                        <small class="text-muted">فقط اعداد انگلیسی مجاز است</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">شماره کارت <span class="text-danger">*</span></label>
                        <input type="text" name="card_number" class="form-control" value="{{ old('card_number') }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">شماره شبا <span class="text-danger">*</span></label>
                        <input type="text" name="sheba_number" class="form-control" value="{{ old('sheba_number') }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">نام دارنده حساب <span class="text-danger">*</span></label>
                        <input type="text" name="card_holder" class="form-control" value="{{ old('card_holder') }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">نام بانک <span class="text-danger">*</span></label>
                        <input type="text" name="bank_name" class="form-control" value="{{ old('bank_name') }}" required>
                    </div>
                </div>

                <hr>

                {{-- 8. وضعیت و مهلت ثبت‌نام --}}
                <h5 class="mb-3 text-primary"><i class="bi bi-calendar-check me-2"></i> وضعیت و مهلت ثبت‌نام</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">وضعیت <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>پیش‌نویس</option>
                            <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>منتشر شده</option>
                            <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>تکمیل شده</option>
                            <option value="canceled" {{ old('status') == 'canceled' ? 'selected' : '' }}>لغو شده</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">ثبت‌نام باز است؟</label>
                        <select name="is_registration_open" class="form-select">
                            <option value="1" {{ old('is_registration_open', '1') == '1' ? 'selected' : '' }}>بله</option>
                            <option value="0" {{ old('is_registration_open') == '0' ? 'selected' : '' }}>خیر</option>
                        </select>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">مهلت ثبت‌نام</label>
                        <div class="input-group">
                            <input type="text" name="registration_deadline" id="registration_deadline" class="form-control" data-jdp data-jdp-time="true" value="{{ old('registration_deadline') }}" autocomplete="off">
                            <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                        </div>
                        <small class="text-muted">تاریخ و ساعت مهلت ثبت‌نام</small>
                    </div>
                </div>

                <hr>

                {{-- 9. توضیحات --}}
                <h5 class="mb-3 text-primary"><i class="bi bi-file-text me-2"></i> توضیحات</h5>
                <div class="mb-4">
                    <label class="form-label">توضیحات دوره</label>
                    <textarea name="description" id="description" class="form-control" rows="10">{{ old('description') }}</textarea>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary">انصراف</a>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle me-2"></i> ثبت دوره
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .select2-container {
            z-index: 9999;
        }
        #course_location_map {
            z-index: 1;
        }
        .teacher-image-upload-container .upload-area:hover {
            background: #e9ecef !important;
            border-color: #0d6efd !important;
        }
        .teacher-image-preview-item {
            position: relative;
        }
        .teacher-image-preview-item img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
        }
        .teacher-image-preview-item .remove-btn {
            position: absolute;
            top: 10px;
            left: 10px;
        }
    </style>
@endpush

@push('scripts')
<!--
    <script src="https://cdn.ckeditor.com/ckeditor5/41.3.1/classic/ckeditor.js"></script>
    -->
    <script>
        $(document).ready(function() {
            // Initialize CKEditor for description
            ClassicEditor
                .create(document.querySelector('#description'), {
                    language: 'fa'
                })
                .catch(error => {
                    console.error('CKEditor error:', error);
                });

            // Initialize Select2 for prerequisites
            $('#prerequisites').select2({
                dir: "rtl",
                width: '100%',
                theme: 'bootstrap-5'
            });

            // Initialize Select2 for teacher skills and certificates (tags mode)
            $('#teacher_skills, #teacher_certificates').select2({
                tags: true,
                dir: "rtl",
                width: '100%',
                theme: 'bootstrap-5',
                tokenSeparators: [',', ' '],
                createTag: function (params) {
                    var term = $.trim(params.term);
                    if (term === '') {
                        return null;
                    }
                    return {
                        id: term,
                        text: term,
                        newTag: true
                    };
                }
            });

            // Toggle federation section
            function toggleFederationSection() {
                const isFederation = $('#is_federation_course').val();
                if (isFederation === '1') {
                    $('#federation_section').show();
                    $('#prerequisites_section').show();
                } else {
                    $('#federation_section').hide();
                    $('#prerequisites_section').hide();
                }
            }
            toggleFederationSection();
            $('#is_federation_course').on('change', toggleFederationSection);

            // Toggle teacher sections
            function toggleTeacherSection() {
                const createNew = $('#create_new_teacher').val();
                if (createNew === '1') {
                    $('#teacher_select_section').hide();
                    $('#teacher_create_section').show();
                    $('#teacher_id').prop('required', false);
                    $('input[name="teacher_first_name"]').prop('required', true);
                    $('input[name="teacher_last_name"]').prop('required', true);
                } else {
                    $('#teacher_select_section').show();
                    $('#teacher_create_section').hide();
                    $('#teacher_id').prop('required', true);
                    $('input[name="teacher_first_name"]').prop('required', false);
                    $('input[name="teacher_last_name"]').prop('required', false);
                }
            }
            toggleTeacherSection();
            $('#create_new_teacher').on('change', toggleTeacherSection);

            // Toggle cost section
            function toggleCostSection() {
                const isFree = $('#is_free').val();
                if (isFree === '0') {
                    $('#cost_section').show();
                    $('#cost_section input[required]').prop('required', true);
                } else {
                    $('#cost_section').hide();
                    $('#cost_section input[required]').prop('required', false);
                }
            }
            toggleCostSection();
            $('#is_free').on('change', toggleCostSection);

            // Cost formatting with commas
            $('.cost-input').on('keydown', function(e) {
                if ([46, 8, 9, 27, 13, 110, 190].indexOf(e.keyCode) !== -1 ||
                    (e.keyCode === 65 && e.ctrlKey === true) ||
                    (e.keyCode === 67 && e.ctrlKey === true) ||
                    (e.keyCode === 86 && e.ctrlKey === true) ||
                    (e.keyCode === 88 && e.ctrlKey === true) ||
                    (e.keyCode >= 35 && e.keyCode <= 39)) {
                    return;
                }
                if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                    e.preventDefault();
                }
            });

            $('.cost-input').on('input', function() {
                let input = this;
                let value = input.value.replace(/,/g, '');
                value = value.replace(/[^0-9]/g, '');
                
                if (value) {
                    let formatted = parseInt(value).toLocaleString('en-US');
                    input.value = formatted;
                } else {
                    input.value = '';
                }
            });

            $('.cost-input').on('focus', function() {
                let value = $(this).val().replace(/,/g, '');
                $(this).val(value);
            });

            $('#course-form').on('submit', function() {
                $('.cost-input').each(function() {
                    $(this).val($(this).val().replace(/,/g, ''));
                });
            });

            // Configure jalalidatepicker
            const timeFields = ['#registration_deadline'];
            
            timeFields.forEach(function(fieldId) {
                const input = document.querySelector(fieldId);
                if (input) {
                    input.addEventListener('focus', function() {
                        jalaliDatepicker.updateOptions({ time: true, zIndex: 3000 });
                    });
                }
            });
            
            // Date fields without time
            const dateFields = ['#start_date', '#end_date', '#teacher_birth_date'];
            dateFields.forEach(function(fieldId) {
                const input = document.querySelector(fieldId);
                if (input) {
                    input.addEventListener('focus', function() {
                        jalaliDatepicker.updateOptions({ time: false, zIndex: 3000 });
                    });
                }
            });

            // Auto-calculate duration from start_date and end_date
            function calculateDuration() {
                const startDateInput = document.querySelector('#start_date');
                const endDateInput = document.querySelector('#end_date');
                const durationInput = document.querySelector('#duration');
                
                if (startDateInput && endDateInput && durationInput) {
                    const startValue = startDateInput.value.trim();
                    const endValue = endDateInput.value.trim();
                    
                    if (startValue && endValue) {
                        try {
                            // Parse Jalali dates (format: YYYY/MM/DD)
                            const startParts = startValue.split('/');
                            const endParts = endValue.split('/');
                            
                            if (startParts.length === 3 && endParts.length === 3) {
                                const startYear = parseInt(startParts[0]);
                                const startMonth = parseInt(startParts[1]);
                                const startDay = parseInt(startParts[2]);
                                
                                const endYear = parseInt(endParts[0]);
                                const endMonth = parseInt(endParts[1]);
                                const endDay = parseInt(endParts[2]);
                                
                                // Simple Jalali date difference calculation
                                // Convert Jalali dates to days since a reference date
                                function jalaliToDays(year, month, day) {
                                    // Reference: 1/1/1300 (Jalali)
                                    let days = 0;
                                    for (let y = 1300; y < year; y++) {
                                        days += isJalaliLeapYear(y) ? 366 : 365;
                                    }
                                    for (let m = 1; m < month; m++) {
                                        days += getJalaliMonthDays(year, m);
                                    }
                                    days += day - 1;
                                    return days;
                                }
                                
                                function isJalaliLeapYear(year) {
                                    const leapYears = [1, 5, 9, 13, 17, 22, 26, 30];
                                    return leapYears.includes(year % 33);
                                }
                                
                                function getJalaliMonthDays(year, month) {
                                    if (month <= 6) return 31;
                                    if (month <= 11) return 30;
                                    return isJalaliLeapYear(year) ? 30 : 29;
                                }
                                
                                const startDays = jalaliToDays(startYear, startMonth, startDay);
                                const endDays = jalaliToDays(endYear, endMonth, endDay);
                                const diffDays = Math.abs(endDays - startDays);
                                
                                // Only update if duration field is empty or user hasn't manually changed it
                                if (!durationInput.dataset.manuallyEdited) {
                                    durationInput.value = diffDays > 0 ? diffDays : 0;
                                }
                            }
                        } catch (error) {
                            console.warn('خطا در محاسبه مدت:', error);
                        }
                    }
                }
            }

            // Calculate duration when dates change
            $('#start_date, #end_date').on('change blur', function() {
                setTimeout(calculateDuration, 100);
            });

            // Mark duration as manually edited when user types
            $('#duration').on('input', function() {
                this.dataset.manuallyEdited = 'true';
            });

            // Teacher image upload handling
            const teacherUploadArea = document.getElementById('teacher-upload-area');
            const teacherImageInput = document.getElementById('teacher-image-input');
            const teacherImagePreview = document.getElementById('teacher-image-preview');
            let teacherImageFile = null;

            if (teacherUploadArea && teacherImageInput && teacherImagePreview) {
                teacherUploadArea.addEventListener('click', () => {
                    teacherImageInput.click();
                });

                teacherImageInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (!file) return;

                    // Check file size (2MB)
                    if (file.size > 2 * 1024 * 1024) {
                        toastr.error('فایل بزرگتر از 2 مگابایت است');
                        e.target.value = '';
                        return;
                    }

                    // Check file type
                    if (!file.type.match('image.*')) {
                        toastr.error('فایل انتخاب شده یک تصویر معتبر نیست');
                        e.target.value = '';
                        return;
                    }

                    teacherImageFile = file;
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        teacherImagePreview.innerHTML = `
                            <div class="col-md-4 teacher-image-preview-item">
                                <img src="${e.target.result}" alt="Preview">
                                <button type="button" class="btn btn-danger btn-sm remove-btn remove-teacher-image">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                        `;
                    };
                    reader.readAsDataURL(file);
                });

                // Remove teacher image
                $(document).on('click', '.remove-teacher-image', function() {
                    teacherImageFile = null;
                    teacherImageInput.value = '';
                    teacherImagePreview.innerHTML = '';
                });
            }

            // Initialize Leaflet map for course location (Golshahr, Karaj)
            function initCourseLocationMap() {
                try {
                    // Default: Golshahr, Karaj (approximately 35.8327, 50.9344)
                    const defaultLat = 35.8327;
                    const defaultLon = 50.9344;
                    const existingLat = $('#place_lat').val();
                    const existingLon = $('#place_lon').val();
                    
                    const lat = existingLat ? parseFloat(existingLat) : defaultLat;
                    const lon = existingLon ? parseFloat(existingLon) : defaultLon;
                    
                    const map = L.map('course_location_map').setView([lat, lon], existingLat ? 15 : 12);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 18,
                        attribution: '© OpenStreetMap contributors',
                        crossOrigin: true
                    }).addTo(map);

                    let marker = null;
                    if (existingLat && existingLon) {
                        marker = L.marker([lat, lon]).addTo(map);
                        $('#place_lat').val(lat.toFixed(7));
                        $('#place_lon').val(lon.toFixed(7));
                    }
                    
                    map.on('click', function(e) {
                        if (marker) {
                            map.removeLayer(marker);
                        }
                        marker = L.marker(e.latlng).addTo(map);
                        $('#place_lat').val(e.latlng.lat.toFixed(7));
                        $('#place_lon').val(e.latlng.lng.toFixed(7));
                    });
                } catch (error) {
                    console.warn('خطا در بارگذاری نقشه:', error);
                    $('#course_location_map').html('<div class="alert alert-warning">نقشه در دسترس نیست. لطفاً مختصات را به صورت دستی وارد کنید.</div>');
                }
            }

            // Initialize map after a short delay
            setTimeout(function() {
                initCourseLocationMap();
            }, 500);
        });
    </script>
@endpush

