@extends('admin.layout')

@section('title', 'ثبت‌نام‌های دوره: ' . $course->title)

@push('styles')
<style>
    .registrations-table-wrapper {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        overflow: hidden;
    }
    
    .table-header-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .table-header-section h5 {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 600;
    }
    
    #registrations-table {
        font-size: 0.875rem;
        margin: 0;
        min-width: 800px;
    }
    
    #registrations-table thead th {
        background-color: #f8f9fa;
        color: #495057;
        font-weight: 600;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 12px 15px;
        border-bottom: 2px solid #dee2e6;
        white-space: nowrap;
    }
    
    #registrations-table tbody td {
        padding: 12px 15px;
        vertical-align: middle;
        border-bottom: 1px solid #f0f0f0;
        white-space: nowrap;
    }
    
    @media (max-width: 768px) {
        .table-responsive {
            display: block;
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        #registrations-table {
            font-size: 0.75rem;
        }
        
        #registrations-table thead th,
        #registrations-table tbody td {
            padding: 8px 10px;
        }
        
        .action-btn {
            width: 28px;
            height: 28px;
            font-size: 0.75rem;
        }
        
        .status-badge {
            font-size: 0.7rem;
            padding: 3px 8px;
        }
    }
    
    #registrations-table tbody tr:hover {
        background-color: #f8f9fa;
        transition: background-color 0.2s;
    }
    
    .status-badge {
        font-size: 0.75rem;
        padding: 4px 10px;
        border-radius: 12px;
        font-weight: 500;
    }
    
    .action-buttons {
        display: flex;
        gap: 6px;
        align-items: center;
    }
    
    .action-btn {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        font-size: 0.85rem;
        transition: all 0.2s;
        border: none;
    }
    
    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    
    .btn-approve {
        background: #28a745;
        color: white;
    }
    
    .btn-approve:hover {
        background: #218838;
        color: white;
    }
    
    .btn-reject {
        background: #dc3545;
        color: white;
    }
    
    .btn-reject:hover {
        background: #bb2d3b;
        color: white;
    }
    
    .btn-cancel {
        background: #6c757d;
        color: white;
    }
    
    .btn-cancel:hover {
        background: #5a6268;
        color: white;
    }
    
    .certificate-upload-container .upload-area:hover {
        background: #e9ecef !important;
        border-color: #0d6efd !important;
    }
    .certificate-preview-item {
        position: relative;
    }
    .certificate-preview-item img {
        width: 100%;
        max-height: 200px;
        object-fit: contain;
        border-radius: 8px;
    }
</style>
@endpush

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.courses.index') }}">دوره‌ها</a></li>
            <li class="breadcrumb-item"><a href="{{ route('courses.show', $course->id) }}">{{ $course->title }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">ثبت‌نام‌ها</li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="registrations-table-wrapper">
        <div class="table-header-section">
            <h5><i class="bi bi-people me-2"></i> ثبت‌نام‌های دوره: {{ $course->title }}</h5>
            <a href="{{ route('courses.show', $course->id) }}" class="btn btn-light btn-sm" style="font-size: 0.875rem;">
                <i class="bi bi-arrow-right me-1"></i> بازگشت به دوره
            </a>
        </div>
        <div class="dataTables_wrapper" style="padding: 20px;">
            @if($registrations->count() > 0)
                <div class="table-responsive" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                    <table id="registrations-table" class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>نام</th>
                            <th>شماره تماس</th>
                            <th>کد پیگیری پرداخت</th>
                            <th>وضعیت پرداخت</th>
                            <th>وضعیت ثبت‌نام</th>
                            <th>تاریخ ثبت</th>
                            <th style="text-align: center; width: 150px;">عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($registrations as $index => $registration)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    @if($registration->user_id)
                                        <a href="{{ route('admin.users.show', $registration->user_id) }}" class="text-decoration-none fw-bold">
                                            {{ $registration->user->full_name ?? $registration->user->phone }}
                                        </a>
                                        <span class="badge bg-info ms-2 status-badge">عضو</span>
                                    @else
                                        <strong>{{ $registration->guest_name }}</strong>
                                        <span class="badge bg-warning ms-2 status-badge">مهمان</span>
                                    @endif
                                </td>
                                <td>
                                    @if($registration->user_id)
                                        {{ $registration->user->phone }}
                                    @else
                                        {{ $registration->guest_phone }}
                                    @endif
                                </td>
                                <td>
                                    @if($registration->payment)
                                        <code>{{ $registration->payment->transaction_code }}</code>
                                    @else
                                        <span class="text-muted">رایگان</span>
                                    @endif
                                </td>
                                <td>
                                    @if($registration->payment)
                                        @if($registration->payment->status == 'pending')
                                            <span class="badge bg-secondary status-badge">در انتظار</span>
                                        @elseif($registration->payment->status == 'approved')
                                            <span class="badge bg-success status-badge">تأیید شده</span>
                                        @else
                                            <span class="badge bg-danger status-badge">رد شده</span>
                                        @endif
                                    @else
                                        <span class="badge bg-info status-badge">رایگان</span>
                                    @endif
                                </td>
                                <td>
                                    @if($registration->status == 'pending')
                                        <span class="badge bg-secondary status-badge">در انتظار</span>
                                    @elseif($registration->status == 'paid')
                                        <span class="badge bg-info status-badge">پرداخت شده</span>
                                    @elseif($registration->status == 'approved')
                                        <span class="badge bg-success status-badge">تأیید شده</span>
                                    @elseif($registration->status == 'rejected')
                                        <span class="badge bg-danger status-badge">رد شده</span>
                                    @elseif($registration->status == 'cancelled')
                                        <span class="badge bg-dark status-badge">لغو شده</span>
                                    @else
                                        <span class="badge bg-warning status-badge">{{ $registration->status }}</span>
                                    @endif
                                    @if($registration->payment && $registration->payment->status == 'approved' && $registration->status == 'rejected')
                                        <br><small class="text-danger mt-1 d-block">
                                            <i class="bi bi-exclamation-triangle"></i> پرداخت تأیید شده - برای استرداد با امور مالی تماس بگیرید
                                        </small>
                                    @endif
                                </td>
                                <td class="text-muted" style="font-size: 0.85rem;">
                                    {{ verta($registration->created_at)->format('Y/m/d H:i') }}
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        @if($registration->status == 'pending' || $registration->status == 'paid')
                                            <button type="button" 
                                                    class="btn action-btn btn-approve approve-registration-btn" 
                                                    data-id="{{ $registration->id }}"
                                                    title="تأیید ثبت‌نام">
                                                <i class="bi bi-check-circle"></i>
                                            </button>
                                            <button type="button" 
                                                    class="btn action-btn btn-reject reject-registration-btn" 
                                                    data-id="{{ $registration->id }}"
                                                    title="رد ثبت‌نام">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                        @endif
                                        
                                        @if($registration->status == 'approved')
                                            <button type="button" 
                                                    class="btn action-btn btn-cancel cancel-registration-btn" 
                                                    data-id="{{ $registration->id }}"
                                                    title="لغو ثبت‌نام">
                                                <i class="bi bi-x-octagon"></i>
                                            </button>
                                            <button type="button" 
                                                    class="btn action-btn" 
                                                    style="background: #17a2b8; color: white;"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#certificateModal{{ $registration->id }}"
                                                    title="آپلود گواهینامه">
                                                <i class="bi bi-file-earmark-pdf"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-inbox display-1 text-muted"></i>
                    <p class="text-muted mt-3">هنوز ثبت‌نامی برای این دوره وجود ندارد.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Certificate Upload Modals --}}
    @foreach($registrations as $registration)
        @if($registration->status == 'approved')
            <div class="modal fade" id="certificateModal{{ $registration->id }}" tabindex="-1" aria-labelledby="certificateModalLabel{{ $registration->id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="certificateModalLabel{{ $registration->id }}">
                                <i class="bi bi-file-earmark-pdf me-2"></i> آپلود گواهینامه
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p class="mb-3">
                                <strong>نام:</strong> 
                                @if($registration->user_id)
                                    {{ $registration->user->full_name ?? $registration->user->phone }}
                                @else
                                    {{ $registration->guest_name }}
                                @endif
                            </p>
                            <p class="mb-3">
                                <strong>دوره:</strong> {{ $course->title }}
                            </p>

                            <form id="certificateForm{{ $registration->id }}" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">فایل گواهینامه <span class="text-danger">*</span></label>
                                    <div class="certificate-upload-container">
                                        <div class="upload-area border rounded p-4 text-center mb-3" id="certificate-upload-area{{ $registration->id }}" style="cursor: pointer; background: #f8f9fa; transition: all 0.3s;">
                                            <i class="bi bi-cloud-upload fs-1 text-primary d-block mb-2"></i>
                                            <p class="mb-1 fw-bold">برای آپلود فایل گواهینامه کلیک کنید</p>
                                            <p class="text-muted small mb-0">فرمت‌های مجاز: PDF, JPG, JPEG, PNG | حداکثر اندازه: 10 مگابایت</p>
                                        </div>
                                        <input type="file" name="certificate_file" id="certificate-input{{ $registration->id }}" class="d-none" accept=".pdf,.jpg,.jpeg,.png">
                                        <div id="certificate-preview{{ $registration->id }}" class="row g-3">
                                            @if($registration->certificate_file)
                                                <div class="col-md-6 certificate-preview-item">
                                                    @if(pathinfo($registration->certificate_file, PATHINFO_EXTENSION) == 'pdf')
                                                        <div class="border rounded p-3 text-center bg-light">
                                                            <i class="bi bi-file-earmark-pdf fs-1 text-danger d-block mb-2"></i>
                                                            <p class="mb-0 small">فایل PDF موجود است</p>
                                                            <a href="{{ Storage::url($registration->certificate_file) }}" target="_blank" class="btn btn-sm btn-primary mt-2">
                                                                <i class="bi bi-eye"></i> مشاهده
                                                            </a>
                                                        </div>
                                                    @else
                                                        <img src="{{ Storage::url($registration->certificate_file) }}" alt="گواهینامه" class="img-thumbnail" style="max-height: 200px;">
                                                    @endif
                                                    <button type="button" class="btn btn-danger btn-sm mt-2 remove-certificate-btn" data-registration-id="{{ $registration->id }}">
                                                        <i class="bi bi-trash"></i> حذف
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">انصراف</button>
                            <button type="button" class="btn btn-primary upload-certificate-btn" data-registration-id="{{ $registration->id }}">
                                <i class="bi bi-upload me-1"></i> آپلود
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            @if($registrations->count() > 0)
            $('#registrations-table').DataTable({
                responsive: true,
                language: {
                    "search": "جستجو:",
                    "lengthMenu": "نمایش _MENU_ مورد",
                    "info": "نمایش _START_ تا _END_ از _TOTAL_ مورد",
                    "infoEmpty": "نمایش 0 تا 0 از 0 مورد",
                    "infoFiltered": "(فیلتر شده از _MAX_ مورد)",
                    "paginate": {
                        "first": "اول",
                        "last": "آخر",
                        "next": "بعدی",
                        "previous": "قبلی"
                    },
                    "zeroRecords": "موردی پیدا نشد",
                    "emptyTable": "داده‌ای در جدول وجود ندارد"
                },
                order: [[6, 'desc']],
                pageLength: 15,
                lengthMenu: [[10, 15, 25, 50, -1], [10, 15, 25, 50, "همه"]],
            });
            @endif

            // Approve registration
            $('.approve-registration-btn').on('click', function() {
                const registrationId = $(this).data('id');
                
                Swal.fire({
                    title: 'آیا مطمئن هستید؟',
                    text: 'آیا از تأیید این ثبت‌نام اطمینان دارید؟',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: '<i class="bi bi-check-circle me-1"></i> بله، تأیید شود',
                    cancelButtonText: '<i class="bi bi-x-circle me-1"></i> انصراف',
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        const approveUrl = `{{ route('admin.courses.registrations.approve', ['course' => $course->id, 'registrationId' => '__ID__']) }}`.replace('__ID__', registrationId);
                        
                        $.ajax({
                            url: approveUrl,
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                _method: 'POST'
                            },
                            dataType: 'json',
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'تأیید شد!',
                                    text: 'ثبت‌نام با موفقیت تأیید شد.',
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            },
                            error: function(xhr) {
                                let errorMessage = 'مشکلی در تأیید ثبت‌نام پیش آمد.';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }
                                Swal.fire({
                                    icon: 'error',
                                    title: 'خطا!',
                                    text: errorMessage
                                });
                            }
                        });
                    }
                });
            });

            // Reject registration
            $('.reject-registration-btn').on('click', function() {
                const registrationId = $(this).data('id');
                
                Swal.fire({
                    title: 'آیا مطمئن هستید؟',
                    text: 'آیا از رد این ثبت‌نام اطمینان دارید؟',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '<i class="bi bi-x-circle me-1"></i> بله، رد شود',
                    cancelButtonText: '<i class="bi bi-arrow-left me-1"></i> انصراف',
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        const rejectUrl = `{{ route('admin.courses.registrations.reject', ['course' => $course->id, 'registrationId' => '__ID__']) }}`.replace('__ID__', registrationId);
                        
                        $.ajax({
                            url: rejectUrl,
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                _method: 'POST'
                            },
                            dataType: 'json',
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'رد شد!',
                                    text: 'ثبت‌نام با موفقیت رد شد.',
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            },
                            error: function(xhr) {
                                let errorMessage = 'مشکلی در رد ثبت‌نام پیش آمد.';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }
                                Swal.fire({
                                    icon: 'error',
                                    title: 'خطا!',
                                    text: errorMessage
                                });
                            }
                        });
                    }
                });
            });

            // Cancel registration
            $('.cancel-registration-btn').on('click', function() {
                const registrationId = $(this).data('id');
                
                Swal.fire({
                    title: 'آیا مطمئن هستید؟',
                    text: 'آیا از لغو این ثبت‌نام اطمینان دارید؟',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '<i class="bi bi-x-octagon me-1"></i> بله، لغو شود',
                    cancelButtonText: '<i class="bi bi-arrow-left me-1"></i> انصراف',
                    confirmButtonColor: '#6c757d',
                    cancelButtonColor: '#28a745',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        const cancelUrl = `{{ route('admin.courses.registrations.cancel', ['course' => $course->id, 'registrationId' => '__ID__']) }}`.replace('__ID__', registrationId);
                        
                        $.ajax({
                            url: cancelUrl,
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                _method: 'POST'
                            },
                            dataType: 'json',
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'لغو شد!',
                                    text: 'ثبت‌نام با موفقیت لغو شد.',
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            },
                            error: function(xhr) {
                                let errorMessage = 'مشکلی در لغو ثبت‌نام پیش آمد.';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }
                                Swal.fire({
                                    icon: 'error',
                                    title: 'خطا!',
                                    text: errorMessage
                                });
                            }
                        });
                    }
                });
            });

            // Certificate upload handling for each registration
            @foreach($registrations as $registration)
                @if($registration->status == 'approved')
                    (function(registrationId) {
                        const uploadArea = document.getElementById('certificate-upload-area' + registrationId);
                        const certificateInput = document.getElementById('certificate-input' + registrationId);
                        const certificatePreview = document.getElementById('certificate-preview' + registrationId);
                        const uploadBtn = document.querySelector('.upload-certificate-btn[data-registration-id="' + registrationId + '"]');
                        let certificateFile = null;

                        if (uploadArea && certificateInput) {
                            uploadArea.addEventListener('click', () => {
                                certificateInput.click();
                            });

                            certificateInput.addEventListener('change', function(e) {
                                const file = e.target.files[0];
                                if (!file) return;

                                // Check file size (10MB)
                                if (file.size > 10 * 1024 * 1024) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'خطا!',
                                        text: 'فایل بزرگتر از 10 مگابایت است'
                                    });
                                    e.target.value = '';
                                    return;
                                }

                                // Check file type
                                const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
                                if (!allowedTypes.includes(file.type)) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'خطا!',
                                        text: 'فرمت فایل معتبر نیست. فقط PDF, JPG, JPEG, PNG مجاز است.'
                                    });
                                    e.target.value = '';
                                    return;
                                }

                                certificateFile = file;
                                
                                if (file.type === 'application/pdf') {
                                    certificatePreview.innerHTML = `
                                        <div class="col-md-6 certificate-preview-item">
                                            <div class="border rounded p-3 text-center bg-light">
                                                <i class="bi bi-file-earmark-pdf fs-1 text-danger d-block mb-2"></i>
                                                <p class="mb-0 small">${file.name}</p>
                                                <button type="button" class="btn btn-danger btn-sm mt-2 remove-certificate-btn" data-registration-id="${registrationId}">
                                                    <i class="bi bi-trash"></i> حذف
                                                </button>
                                            </div>
                                        </div>
                                    `;
                                } else {
                                    const reader = new FileReader();
                                    reader.onload = function(e) {
                                        certificatePreview.innerHTML = `
                                            <div class="col-md-6 certificate-preview-item">
                                                <img src="${e.target.result}" alt="Preview" class="img-thumbnail">
                                                <button type="button" class="btn btn-danger btn-sm mt-2 remove-certificate-btn" data-registration-id="${registrationId}">
                                                    <i class="bi bi-trash"></i> حذف
                                                </button>
                                            </div>
                                        `;
                                    };
                                    reader.readAsDataURL(file);
                                }
                            });
                        }

                        // Remove certificate
                        $(document).on('click', '.remove-certificate-btn[data-registration-id="' + registrationId + '"]', function() {
                            certificateFile = null;
                            if (certificateInput) certificateInput.value = '';
                            certificatePreview.innerHTML = '';
                        });

                        // Upload certificate
                        if (uploadBtn) {
                            uploadBtn.addEventListener('click', function() {
                                if (!certificateFile && !certificateInput.files[0]) {
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'هشدار!',
                                        text: 'لطفاً فایل گواهینامه را انتخاب کنید.'
                                    });
                                    return;
                                }

                                const formData = new FormData();
                                formData.append('certificate_file', certificateFile || certificateInput.files[0]);
                                formData.append('_token', '{{ csrf_token() }}');

                                const uploadUrl = `{{ route('admin.courses.registrations.uploadCertificate', ['course' => $course->id, 'registrationId' => '__ID__']) }}`.replace('__ID__', registrationId);

                                Swal.fire({
                                    title: 'در حال آپلود...',
                                    text: 'لطفاً صبر کنید',
                                    allowOutsideClick: false,
                                    didOpen: () => {
                                        Swal.showLoading();
                                    }
                                });

                                $.ajax({
                                    url: uploadUrl,
                                    type: 'POST',
                                    data: formData,
                                    processData: false,
                                    contentType: false,
                                    dataType: 'json',
                                    success: function(response) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'موفق!',
                                            text: response.message || 'گواهینامه با موفقیت آپلود شد.',
                                            timer: 2000,
                                            showConfirmButton: false
                                        }).then(() => {
                                            location.reload();
                                        });
                                    },
                                    error: function(xhr) {
                                        let errorMessage = 'مشکلی در آپلود گواهینامه پیش آمد.';
                                        if (xhr.responseJSON && xhr.responseJSON.message) {
                                            errorMessage = xhr.responseJSON.message;
                                        }
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'خطا!',
                                            text: errorMessage
                                        });
                                    }
                                });
                            });
                        }
                    })({{ $registration->id }});
                @endif
            @endforeach
        });
    </script>
    
    <style>
        .swal2-popup {
            direction: rtl;
            text-align: right;
            font-family: 'Peyda', sans-serif;
        }
    </style>
@endpush

