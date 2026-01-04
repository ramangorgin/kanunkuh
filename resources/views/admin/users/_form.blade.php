@php
    $user = $user ?? new \App\Models\User();
    $profile = $user->profile ?? new \App\Models\Profile();
    $medical = $user->medicalRecord ?? new \App\Models\MedicalRecord();
    $educations = $user->educationalHistories ?? collect();
    $federationCourses = \App\Models\FederationCourse::orderBy('title')->get();
@endphp

<div class="accordion" id="userAccordion">

    {{-- Account Info --}}
    <div class="accordion-item">
        <h2 class="accordion-header" id="accountHeading">
            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#accountSection">
                <i class="bi bi-shield-lock-fill me-2 text-primary"></i> اطلاعات حساب کاربری
            </button>
        </h2>
        <div id="accountSection" class="accordion-collapse collapse show">
            <div class="accordion-body row g-3">
                <div class="col-md-4">
                    <label class="form-label">شماره تماس <span class="text-danger">*</span></label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone ?? '') }}" class="form-control" dir="ltr">
                </div>
                <div class="col-md-4">
                    <label class="form-label">نقش کاربری</label>
                    <select name="role" class="form-select">
                        <option value="member" {{ old('role', $user->role ?? '') == 'member' ? 'selected' : '' }}>عضو عادی</option>
                        <option value="admin" {{ old('role', $user->role ?? '') == 'admin' ? 'selected' : '' }}>مدیر سیستم</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">وضعیت حساب</label>
                    <select name="status" class="form-select">
                        <option value="active" {{ old('status', $user->status ?? 'active') == 'active' ? 'selected' : '' }}>فعال</option>
                        <option value="inactive" {{ old('status', $user->status ?? '') == 'inactive' ? 'selected' : '' }}>غیرفعال</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Membership Info --}}
    <div class="accordion-item">
        <h2 class="accordion-header" id="membershipHeading">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#membershipSection">
                <i class="bi bi-patch-check-fill me-2 text-success"></i> اطلاعات عضویت
            </button>
        </h2>
        <div id="membershipSection" class="accordion-collapse collapse">
            <div class="accordion-body row g-3">
                <div class="col-md-3">
                    <label class="form-label">شناسه عضویت</label>
                    <input type="text" name="membership_id" value="{{ old('membership_id', $profile->membership_id ?? '') }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">وضعیت درخواست عضویت</label>
                    <select name="membership_status" class="form-select">
                        <option value="pending" {{ old('membership_status', $profile->membership_status ?? '') == 'pending' ? 'selected' : '' }}>در انتظار بررسی</option>
                        <option value="approved" {{ old('membership_status', $profile->membership_status ?? '') == 'approved' ? 'selected' : '' }}>تأیید شده</option>
                        <option value="rejected" {{ old('membership_status', $profile->membership_status ?? '') == 'rejected' ? 'selected' : '' }}>رد شده</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">نوع عضویت</label>
                    <select name="membership_type" class="form-select">
                        <option value="official" {{ old('membership_type', $profile->membership_type ?? '') == 'official' ? 'selected' : '' }}>رسمی</option>
                        <option value="unofficial" {{ old('membership_type', $profile->membership_type ?? '') == 'unofficial' ? 'selected' : '' }}>آزمایشی</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">تاریخ شروع عضویت</label>
                    <div class="input-group">
                        <input type="text" name="membership_start" value="{{ old('membership_start', $jalali['membership_start'] ?? '') }}" class="form-control jalali-picker" data-jdp autocomplete="off">
                        <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label">تاریخ پایان اعتبار</label>
                    <div class="input-group">
                        <input type="text" name="membership_expiry" value="{{ old('membership_expiry', $jalali['membership_expiry'] ?? '') }}" class="form-control jalali-picker" data-jdp autocomplete="off">
                        <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Personal Profile --}}
    <div class="accordion-item">
        <h2 class="accordion-header" id="personalHeading">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#personalSection">
                <i class="bi bi-person-vcard-fill me-2 text-info"></i> مشخصات هویتی و فردی
            </button>
        </h2>
        <div id="personalSection" class="accordion-collapse collapse">
            <div class="accordion-body row g-3">
                <div class="col-md-3">
                    <label class="form-label">نام <span class="text-danger">*</span></label>
                    <input type="text" name="first_name" value="{{ old('first_name', $profile->first_name ?? '') }}" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">نام خانوادگی <span class="text-danger">*</span></label>
                    <input type="text" name="last_name" value="{{ old('last_name', $profile->last_name ?? '') }}" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">نام پدر</label>
                    <input type="text" name="father_name" value="{{ old('father_name', $profile->father_name ?? '') }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">کد ملی <span class="text-danger">*</span></label>
                    <input type="text" name="national_id" value="{{ old('national_id', $profile->national_id ?? '') }}" class="form-control" maxlength="10">
                </div>
                <div class="col-md-3">
                    <label class="form-label">شماره شناسنامه</label>
                    <input type="text" name="id_number" value="{{ old('id_number', $profile->id_number ?? '') }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">محل صدور</label>
                    <input type="text" name="id_place" value="{{ old('id_place', $profile->id_place ?? '') }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">تاریخ تولد <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="text" name="birth_date" value="{{ old('birth_date', $jalali['birth_date'] ?? '') }}" class="form-control jalali-picker" data-jdp autocomplete="off">
                        <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label">وضعیت تأهل</label>
                    <select name="marital_status" class="form-select">
                        <option value="">انتخاب کنید...</option>
                        <option value="مجرد" {{ old('marital_status', $profile->marital_status ?? '') == 'مجرد' ? 'selected' : '' }}>مجرد</option>
                        <option value="متاهل" {{ old('marital_status', $profile->marital_status ?? '') == 'متاهل' ? 'selected' : '' }}>متأهل</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">شماره اضطراری</label>
                    <input type="text" name="emergency_phone" value="{{ old('emergency_phone', $profile->emergency_phone ?? '') }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">تحصیلات</label>
                    <input type="text" name="education" value="{{ old('education', $profile->education ?? '') }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">شغل</label>
                    <input type="text" name="job" value="{{ old('job', $profile->job ?? '') }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">معرف</label>
                    <input type="text" name="referrer" value="{{ old('referrer', $profile->referrer ?? '') }}" class="form-control">
                </div>
                
                <div class="col-12">
                    <label class="form-label">آدرس منزل</label>
                    <textarea name="home_address" class="form-control" rows="2">{{ old('home_address', $profile->home_address ?? '') }}</textarea>
                </div>
                <div class="col-12">
                    <label class="form-label">آدرس محل کار</label>
                    <textarea name="work_address" class="form-control" rows="2">{{ old('work_address', $profile->work_address ?? '') }}</textarea>
                </div>

                {{-- Files --}}
                <div class="col-md-6">
                    <label class="form-label">عکس پرسنلی</label>
                    <input type="file" name="photo" class="filepond" accept="image/*" data-label-idle="برای بارگذاری عکس کلیک کنید یا بکشید و رها کنید">
                    @if($profile->photo)
                        <div class="mt-2">
                            <img src="{{ asset('storage/'.$profile->photo) }}" alt="عکس فعلی" class="img-thumbnail" style="height: 100px;">
                        </div>
                    @endif
                </div>
                <div class="col-md-6">
                    <label class="form-label">تصویر کارت ملی</label>
                    <input type="file" name="national_card" class="filepond" accept="image/*,application/pdf" data-label-idle="برای بارگذاری فایل کلیک کنید یا بکشید و رها کنید">
                    @if($profile->national_card)
                        <div class="mt-2">
                            @php $ncExt = strtolower(pathinfo($profile->national_card, PATHINFO_EXTENSION)); @endphp
                            @if(in_array($ncExt, ['jpg','jpeg','png','gif','webp']))
                                <img src="{{ asset('storage/'.$profile->national_card) }}" alt="تصویر کارت ملی" class="img-thumbnail" style="max-height: 140px;">
                            @else
                                <a href="{{ asset('storage/'.$profile->national_card) }}" target="_blank" class="btn btn-sm btn-outline-primary">دانلود کارت ملی فعلی</a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Medical Record --}}
    <div class="accordion-item">
        <h2 class="accordion-header" id="medicalHeading">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#medicalSection">
                <i class="bi bi-heart-pulse-fill me-2 text-danger"></i> پرونده پزشکی
            </button>
        </h2>
        <div id="medicalSection" class="accordion-collapse collapse">
            <div class="accordion-body">
                @include('admin.users.partials._medical_form', ['medical' => $medical])
            </div>
        </div>
    </div>

    {{-- Educational History --}}
    <div class="accordion-item">
        <h2 class="accordion-header" id="eduHeading">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#eduSection">
                <i class="bi bi-book-fill me-2 text-warning"></i> سوابق آموزشی
            </button>
        </h2>
        <div id="eduSection" class="accordion-collapse collapse">
            <div class="accordion-body">
                @include('admin.users.partials._education_form', [
                    'educations' => $educations,
                    'federationCourses' => $federationCourses
                ])
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (window.jalaliDatepicker) {
            jalaliDatepicker.startWatch({ autoHide: true, showTodayBtn: true, persianDigits: true });
            document.addEventListener('click', function (e) {
                const target = e.target.closest('.jalali-picker');
                if (target) {
                    jalaliDatepicker.show(target);
                }
            });
        }

        if (window.FilePond) {
            FilePond.setOptions({
                credits: false,
                storeAsFile: true,
                labelIdle: 'برای بارگذاری فایل کلیک کنید یا بکشید و رها کنید'
            });
            document.querySelectorAll('.filepond').forEach(function (input) {
                FilePond.create(input, {
                    allowImagePreview: true,
                    imagePreviewHeight: 120,
                    labelIdle: input.dataset.labelIdle || 'برای بارگذاری فایل کلیک کنید یا بکشید و رها کنید'
                });
            });
        }
    });
</script>
@endpush
