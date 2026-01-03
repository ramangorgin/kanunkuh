@extends('admin.layout')

@section('title', 'مشاهده کاربر')

@section('content')
<div class="container-fluid py-4 animate__animated animate__fadeIn">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-dark mb-0">
            <i class="bi bi-person-badge-fill text-primary me-2"></i> جزئیات کاربر
        </h4>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-secondary">
                <i class="bi bi-pencil-square"></i> ویرایش
            </a>
            <button class="btn btn-danger delete-user" data-id="{{ $user->id }}">
                <i class="bi bi-trash3"></i> حذف
            </button>
        </div>
    </div>

    <div class="row g-4">
        {{-- Right Column: Profile & Membership --}}
        <div class="col-lg-8">
            
            {{-- Profile Info --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-4 text-primary border-bottom pb-2">
                        <i class="bi bi-person-lines-fill"></i> اطلاعات شخصی و هویتی
                    </h5>

                    <div class="row g-3">
                        {{-- Avatar --}}
                        <div class="col-md-12 text-center mb-3">
                            @if($user->profile->photo)
                                <img src="{{ asset('storage/'.$user->profile->photo) }}" class="rounded-circle shadow-sm" style="width: 120px; height: 120px; object-fit: cover;">
                            @else
                                <img src="{{ asset('images/default-avatar.png') }}" class="rounded-circle shadow-sm" style="width: 120px; height: 120px; object-fit: cover;">
                            @endif
                        </div>

                        <div class="col-md-4"><strong>نام:</strong> {{ $user->profile->first_name ?? '-' }}</div>
                        <div class="col-md-4"><strong>نام خانوادگی:</strong> {{ $user->profile->last_name ?? '-' }}</div>
                        <div class="col-md-4"><strong>نام پدر:</strong> {{ $user->profile->father_name ?? '-' }}</div>
                        
                        <div class="col-md-4"><strong>کد ملی:</strong> {{ toPersianNumber($user->profile->national_id ?? '-') }}</div>
                        <div class="col-md-4"><strong>شماره شناسنامه:</strong> {{ toPersianNumber($user->profile->id_number ?? '-') }}</div>
                        <div class="col-md-4"><strong>محل صدور:</strong> {{ $user->profile->id_place ?? '-' }}</div>

                        <div class="col-md-4"><strong>تاریخ تولد:</strong> {{ isset($user->profile->birth_date) ? toPersianNumber(jdate($user->profile->birth_date)->format('Y/m/d')) : '-' }}</div>
                        <div class="col-md-4"><strong>وضعیت تأهل:</strong> {{ $user->profile->marital_status ?? '-' }}</div>
                        <div class="col-md-4"><strong>شغل:</strong> {{ $user->profile->job ?? '-' }}</div>

                        <div class="col-md-4"><strong>تحصیلات:</strong> {{ $user->profile->education ?? '-' }}</div>
                        <div class="col-md-4"><strong>شماره تماس:</strong> {{ toPersianNumber($user->phone) }}</div>
                        <div class="col-md-4"><strong>شماره اضطراری:</strong> {{ toPersianNumber($user->profile->emergency_phone ?? '-') }}</div>

                        <div class="col-md-4"><strong>معرف:</strong> {{ $user->profile->referrer ?? '-' }}</div>
                        
                        <div class="col-12"><strong>آدرس منزل:</strong> {{ $user->profile->home_address ?? '-' }}</div>
                        <div class="col-12"><strong>آدرس محل کار:</strong> {{ $user->profile->work_address ?? '-' }}</div>

                        <div class="col-12 mt-3">
                            <strong>تصویر کارت ملی:</strong>
                            @if($user->profile->national_card)
                                <a href="{{ asset('storage/'.$user->profile->national_card) }}" target="_blank" class="btn btn-sm btn-outline-primary ms-2">مشاهده فایل</a>
                            @else
                                <span class="text-muted">ندارد</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Membership Info --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-4 text-success border-bottom pb-2">
                        <i class="bi bi-patch-check-fill"></i> وضعیت عضویت
                    </h5>

                    @if($user->profile)
                        <div class="row g-3">
                            <div class="col-md-3"><strong>شناسه عضویت:</strong> {{ toPersianNumber($user->profile->membership_id ?? '-') }}</div>
                            <div class="col-md-3"><strong>نوع عضویت:</strong> 
                                @if($user->profile->membership_type == 'official') رسمی @else آزمایشی @endif
                            </div>
                            <div class="col-md-3"><strong>تاریخ شروع:</strong> {{ isset($user->profile->membership_start) ? toPersianNumber(jdate($user->profile->membership_start)->format('Y/m/d')) : '-' }}</div>
                            <div class="col-md-3"><strong>تاریخ پایان:</strong> {{ isset($user->profile->membership_expiry) ? toPersianNumber(jdate($user->profile->membership_expiry)->format('Y/m/d')) : '-' }}</div>
                        </div>

                        <div class="mt-3 d-flex align-items-center gap-3">
                            <strong>وضعیت فعلی:</strong>
                            @if($user->profile->membership_status === 'approved')
                                <span class="badge bg-success">تایید شده</span>
                            @elseif($user->profile->membership_status === 'pending')
                                <span class="badge bg-warning text-dark">در انتظار</span>
                            @elseif($user->profile->membership_status === 'rejected')
                                <span class="badge bg-danger">رد شده</span>
                            @endif

                            @if($user->profile->membership_status === 'pending')
                                <button class="btn btn-success btn-sm approve-user" data-id="{{ $user->profile->id }}">
                                    <i class="bi bi-check-circle"></i> تایید عضویت
                                </button>
                                <button class="btn btn-danger btn-sm reject-user" data-id="{{ $user->profile->id }}">
                                    <i class="bi bi-x-circle"></i> رد عضویت
                                </button>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

        </div>

        {{-- Left Column: Medical & Education & Payments --}}
        <div class="col-lg-4">
            
            {{-- Medical --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-3 text-danger border-bottom pb-2">
                        <i class="bi bi-heart-pulse-fill"></i> پرونده پزشکی
                    </h5>
                    @if($user->medicalRecord)
                        <div class="row g-2 mb-3">
                            <div class="col-4"><strong>گروه خونی:</strong> {{ $user->medicalRecord->blood_type ?? '-' }}</div>
                            <div class="col-4"><strong>قد:</strong> {{ toPersianNumber($user->medicalRecord->height ?? '-') }}</div>
                            <div class="col-4"><strong>وزن:</strong> {{ toPersianNumber($user->medicalRecord->weight ?? '-') }}</div>
                        </div>
                        <div class="mb-2">
                            <strong>بیمه ورزشی:</strong>
                            @if($user->medicalRecord->insurance_file)
                                <a href="{{ asset('storage/'.$user->medicalRecord->insurance_file) }}" target="_blank" class="badge bg-primary text-decoration-none">دانلود فایل</a>
                            @else
                                <span class="badge bg-secondary">ندارد</span>
                            @endif
                        </div>
                        <div class="text-muted small">
                            تاریخ اعتبار: {{ isset($user->medicalRecord->insurance_expiry_date) ? toPersianNumber(jdate($user->medicalRecord->insurance_expiry_date)->format('Y/m/d')) : '-' }}
                        </div>
                        
                        <hr>
                        <h6>سوابق بیماری:</h6>
                        <ul class="list-unstyled small">
                            @php
                                $questions = [
                                    'head_injury' => 'ضربه به سر',
                                    'eye_ear_problems' => 'مشکلات چشم و گوش',
                                    'seizures' => 'تشنج',
                                    'respiratory' => 'تنفسی',
                                    'heart' => 'قلبی',
                                ];
                            @endphp
                            @foreach($questions as $field => $label)
                                @if($user->medicalRecord->$field)
                                    <li class="text-danger mb-1">
                                        <i class="bi bi-exclamation-circle-fill"></i> {{ $label }}
                                        @if($user->medicalRecord->{$field.'_details'})
                                            <br><span class="text-muted ms-3">({{ $user->medicalRecord->{$field.'_details'} }})</span>
                                        @endif
                                    </li>
                                @endif
                            @endforeach
                            @if($user->medicalRecord->other_conditions)
                                <li class="mt-2"><strong>سایر:</strong> {{ $user->medicalRecord->other_conditions }}</li>
                            @endif
                        </ul>
                    @else
                        <p class="text-muted">پرونده پزشکی ثبت نشده است.</p>
                    @endif
                </div>
            </div>

            {{-- Education --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-3 text-warning border-bottom pb-2">
                        <i class="bi bi-book-fill"></i> سوابق آموزشی
                    </h5>
                    @if($user->educationalHistories->count())
                        <div class="list-group list-group-flush">
                            @foreach($user->educationalHistories as $edu)
                                <div class="list-group-item px-0">
                                    <div class="d-flex justify-content-between">
                                        <strong class="mb-1">{{ $edu->federationCourse->title ?? $edu->custom_course_title }}</strong>
                                        @if($edu->certificate_file)
                                            <a href="{{ asset('storage/'.$edu->certificate_file) }}" target="_blank" class="text-primary"><i class="bi bi-download"></i></a>
                                        @endif
                                    </div>
                                    <small class="text-muted">تاریخ صدور: {{ isset($edu->issue_date) ? toPersianNumber(jdate($edu->issue_date)->format('Y/m/d')) : '-' }}</small>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">هیچ سابقه‌ای ثبت نشده است.</p>
                    @endif
                </div>
            </div>

            {{-- Payments --}}
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3 text-info border-bottom pb-2">
                        <i class="bi bi-credit-card"></i> آخرین پرداخت‌ها
                    </h5>
                    @if($user->payments->count())
                        <ul class="list-group list-group-flush small">
                            @foreach($user->payments->take(5) as $p)
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span>{{ number_format($p->amount) }} تومان</span>
                                    <span class="badge {{ $p->status == 'approved' ? 'bg-success' : ($p->status == 'rejected' ? 'bg-danger' : 'bg-warning text-dark') }}">
                                        {{ $p->status == 'approved' ? 'تایید' : ($p->status == 'rejected' ? 'رد' : 'انتظار') }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">پرداختی یافت نشد.</p>
                    @endif
                </div>
            </div>

            {{-- Tickets --}}
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3 text-primary border-bottom pb-2">
                        <i class="bi bi-life-preserver"></i> آخرین تیکت‌ها
                    </h5>
                    @php($ticketsList = $recentTickets ?? collect())
                    @if($ticketsList->count())
                        <ul class="list-group list-group-flush small">
                            @foreach($ticketsList as $t)
                                <li class="list-group-item d-flex justify-content-between align-items-start px-0">
                                    <div class="me-2">
                                        <div class="fw-semibold">{{ $t->subject }}</div>
                                        <div class="text-muted">{{ $t->updated_at ? toPersianNumber(jdate($t->updated_at)->format('Y/m/d H:i')) : '' }}</div>
                                    </div>
                                    <div>{!! ticket_status_badge($t->status, $t->last_reply_by) !!}</div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted mb-0">تیکتی ثبت نشده است.</p>
                    @endif
                </div>
            </div>

        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
$(function() {
    // Delete User
    $('.delete-user').click(function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'آیا مطمئن هستید؟',
            text: "کاربر برای همیشه حذف خواهد شد.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'بله حذف شود',
            cancelButtonText: 'انصراف',
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/admin/users/${id}`,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function() {
                        Swal.fire('حذف شد!', 'کاربر با موفقیت حذف شد.', 'success')
                            .then(() => window.location.href = '{{ route("admin.users.index") }}');
                    },
                    error: function() {
                        Swal.fire('خطا', 'مشکلی در حذف کاربر پیش آمد.', 'error');
                    }
                });
            }
        });
    });

    // Approve
    $('.approve-user').click(function() {
        const id = $(this).data('id'); // profile id
        Swal.fire({
            title: 'تایید عضویت؟',
            text: 'آیا از تایید این کاربر مطمئن هستید؟',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'بله',
            cancelButtonText: 'خیر',
            confirmButtonColor: '#198754'
        }).then((result) => {
            if (result.isConfirmed) {
                // Assuming route exists: POST /admin/users/{id}/approve where id is USER id or PROFILE id?
                // The route typically uses User ID. Let's check previous context or assume Profile ID logic.
                // Best to use User ID if the controller method finds user.
                // In show.blade.php, data-id is user->profile->id.
                // Let's assume the backend route handles profile ID or user ID correctly.
                // Standard Laravel resource controller usually uses User ID.
                // I will use User ID to be safe if I can, but here I only have profile->id in the loop variable context?
                // No, I have $user->id available globally in show blade.
                // Let's use $user->id for safety.
                
                const userId = {{ $user->id }};
                $.post(`/admin/users/${userId}/approve`, {_token: '{{ csrf_token() }}'}, function() {
                    Swal.fire('تایید شد!', 'عضویت کاربر تایید شد', 'success')
                        .then(() => location.reload());
                });
            }
        });
    });

    // Reject
    $('.reject-user').click(function() {
        const userId = {{ $user->id }};
        Swal.fire({
            title: 'رد عضویت؟',
            text: 'آیا مطمئن هستید؟',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'بله، رد شود',
            cancelButtonText: 'انصراف',
            confirmButtonColor: '#dc3545'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post(`/admin/users/${userId}/reject`, {_token: '{{ csrf_token() }}'}, function() {
                    Swal.fire('رد شد!', 'عضویت کاربر رد شد', 'success')
                        .then(() => location.reload());
                });
            }
        });
    });
});
</script>
@endpush
