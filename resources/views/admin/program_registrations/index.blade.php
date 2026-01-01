@extends('admin.layout')

@section('title', 'ثبت‌نام‌های برنامه: ' . $program->name)

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
        min-width: 800px; /* حداقل عرض برای حفظ ساختار */
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
    
    /* Responsive styles for mobile */
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
</style>
@endpush

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.programs.index') }}">برنامه‌ها</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.programs.show', $program->id) }}">{{ $program->name }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">ثبت‌نام‌ها</li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="registrations-table-wrapper">
        <div class="table-header-section">
            <h5><i class="bi bi-people me-2"></i> ثبت‌نام‌های برنامه: {{ $program->name }}</h5>
            <a href="{{ route('admin.programs.show', $program->id) }}" class="btn btn-light btn-sm" style="font-size: 0.875rem;">
                <i class="bi bi-arrow-right me-1"></i> بازگشت به برنامه
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
                            <th>محل سوار شدن</th>
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
                                    @if($registration->pickup_location)
                                        <span class="badge bg-secondary status-badge">
                                            @if($registration->pickup_location == 'tehran') تهران
                                            @elseif($registration->pickup_location == 'karaj') کرج
                                            @else {{ $registration->pickup_location }}
                                            @endif
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
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
                    <p class="text-muted mt-3">هنوز ثبت‌نامی برای این برنامه وجود ندارد.</p>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            // Initialize DataTable if there are registrations
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
                order: [[7, 'desc']],
                pageLength: 15,
                lengthMenu: [[10, 15, 25, 50, -1], [10, 15, 25, 50, "همه"]],
            });
            @endif

            // Approve registration
            $('.approve-registration-btn').on('click', function() {
                const registrationId = $(this).data('id');
                const btn = $(this);
                
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
                        const approveUrl = `{{ route('admin.programs.registrations.approve', ['program' => $program->id, 'registrationId' => '__ID__']) }}`.replace('__ID__', registrationId);
                        
                        console.log('Sending approve request:', {
                            url: approveUrl,
                            registrationId: registrationId,
                            programId: {{ $program->id }}
                        });
                        
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
                                console.error('Error response:', xhr);
                                console.error('Status:', xhr.status);
                                console.error('Response JSON:', xhr.responseJSON);
                                
                                let errorMessage = 'مشکلی در تأیید ثبت‌نام پیش آمد.';
                                let errorDetails = '';
                                
                                if (xhr.responseJSON) {
                                    if (xhr.responseJSON.message) {
                                        errorMessage = xhr.responseJSON.message;
                                    }
                                    if (xhr.responseJSON.error_type) {
                                        errorDetails += '\nنوع خطا: ' + xhr.responseJSON.error_type;
                                    }
                                    if (xhr.responseJSON.file) {
                                        errorDetails += '\nفایل: ' + xhr.responseJSON.file;
                                    }
                                    if (xhr.responseJSON.line) {
                                        errorDetails += '\nخط: ' + xhr.responseJSON.line;
                                    }
                                }
                                
                                if (xhr.status === 0) {
                                    errorMessage = 'خطا در اتصال به سرور. لطفاً اتصال اینترنت خود را بررسی کنید.';
                                } else if (xhr.status === 404) {
                                    errorMessage = 'ثبت‌نام یافت نشد.';
                                } else if (xhr.status === 500) {
                                    errorMessage += errorDetails;
                                }
                                
                                Swal.fire({
                                    icon: 'error',
                                    title: 'خطا!',
                                    html: '<div style="text-align: right; direction: rtl;">' + 
                                          '<p>' + errorMessage + '</p>' +
                                          (errorDetails ? '<pre style="text-align: left; direction: ltr; font-size: 10px; background: #f8f9fa; padding: 10px; border-radius: 5px; margin-top: 10px;">' + errorDetails + '</pre>' : '') +
                                          '</div>',
                                    width: '600px'
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
                        const rejectUrl = `{{ route('admin.programs.registrations.reject', ['program' => $program->id, 'registrationId' => '__ID__']) }}`.replace('__ID__', registrationId);
                        
                        console.log('Sending reject request:', {
                            url: rejectUrl,
                            registrationId: registrationId
                        });
                        
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
                        const cancelUrl = `{{ route('admin.programs.registrations.cancel', ['program' => $program->id, 'registrationId' => '__ID__']) }}`.replace('__ID__', registrationId);
                        
                        console.log('Sending cancel request:', {
                            url: cancelUrl,
                            registrationId: registrationId
                        });
                        
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
        });
    </script>
    
    <style>
        /* SweetAlert2 RTL Support */
        .swal2-popup {
            direction: rtl;
            text-align: right;
            font-family: 'Peyda', sans-serif;
        }
    </style>
@endpush

