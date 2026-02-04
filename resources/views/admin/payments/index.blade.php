{{-- Admin payments management view. --}}
@extends('admin.layout')

@section('title', 'مدیریت پرداخت‌ها')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-primary">
            <i class="bi bi-credit-card"></i> مدیریت پرداخت‌ها
        </h4>
        <a href="{{ url('admin/payments/export') }}" class="btn btn-success">
            <i class="bi bi-file-earmark-excel"></i> خروجی اکسل
        </a>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body">
            <div class="table-responsive">
                <table id="paymentsTable" class="table table-hover align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th>#</th>
                            <th>کاربر</th>
                            <th>نوع پرداخت</th>
                            <th>مبلغ (تومان)</th>
                            <th>شناسه واریز</th>
                            <th>تاریخ ثبت</th>
                            <th>وضعیت</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $index => $payment)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <a href="{{ route('admin.users.show', $payment->user->id) }}" class="text-decoration-none text-dark fw-bold">
                                        {{ $payment->user->profile->first_name ?? '' }} {{ $payment->user->profile->last_name ?? '' }}
                                    </a>
                                </td>
                                <td>
                                    @if($payment->type == 'membership')
                                        <span class="badge bg-info">حق عضویت</span>
                                    @elseif($payment->type == 'program')
                                        <a href="{{ route('admin.programs.show', $payment->related_id) }}" class="badge bg-success text-decoration-none">برنامه</a>
                                    @else
                                        <a href="{{ route('admin.courses.show', $payment->related_id) }}" class="badge bg-warning text-decoration-none">دوره</a>
                                    @endif
                                </td>
                                <td>{{ toPersianNumber(number_format($payment->amount)) }}</td>
                                <td><code>{{ $payment->transaction_code }}</code></td>
                                <td>{{ toPersianNumber(jdate($payment->created_at)->format('Y/m/d H:i')) }}</td>
                                <td>
                                    @if($payment->status == 'pending')
                                        <span class="badge bg-secondary">در انتظار</span>
                                    @elseif($payment->status == 'approved')
                                        <span class="badge bg-success">تأیید شده</span>
                                    @else
                                        <span class="badge bg-danger">رد شده</span>
                                    @endif
                                </td>
                                <td>
                                    @if($payment->status == 'pending')
                                        <button class="btn btn-sm btn-outline-success approve-btn" data-id="{{ $payment->id }}">
                                            <i class="bi bi-check-circle"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger reject-btn" data-id="{{ $payment->id }}">
                                            <i class="bi bi-x-circle"></i>
                                        </button>

                                    @endif
                                    <button class="btn btn-sm btn-outline-info details-btn" 
                                            data-id="{{ $payment->id }}">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal جزئیات پرداخت -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content shadow-lg border-0 rounded-4 glass-modal">
      <div class="modal-header border-0">
        <h5 class="modal-title fw-bold text-primary">
          <i class="bi bi-info-circle"></i> جزئیات پرداخت
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="بستن"></button>
      </div>
      <div class="modal-body">
        <div id="paymentDetails" class="p-2 text-center text-muted">در حال بارگذاری...</div>
      </div>
      <div class="modal-footer border-0">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">بستن</button>
      </div>
    </div>
  </div>
</div>
@push('styles')
<style>
.glass-modal {
  backdrop-filter: blur(15px);
  background: rgba(255, 255, 255, 0.8);
  border: 1px solid rgba(255, 255, 255, 0.2);
}
.glass-modal .modal-header, .glass-modal .modal-footer {
  background: transparent;
}
</style>
<style>
/* رفع فاصله اضافی بین ستون‌ها در DataTables RTL */
table.dataTable > thead > tr > th,
table.dataTable > tbody > tr > td {
  padding-right: 8px !important;
  padding-left: 8px !important;
  text-align: center;
}

/* حذف margin اضافی‌ای که گاهی Bootstrap در Responsive Table میده */
.dataTables_wrapper .row > div {
  margin: 0 !important;
  padding: 0 !important;
}

/* جدول فشرده‌تر و متناسب‌تر */
table.dataTable {
  border-collapse: collapse !important;
  width: 100% !important;
}

/* هماهنگی ظاهر header با بدنه */
table.dataTable thead th {
  vertical-align: middle;
  white-space: nowrap;
}

/* راست‌چین بودن کامل */
.table.dataTable {
  direction: rtl !important;
}
</style>

@endpush
@endsection

@push('scripts')

<!-- 
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

 SweetAlert 
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
-->
<script>
$(document).on('click', '.details-btn', function() {
    let id = $(this).data('id');
    
    console.log("🔹 Payment ID:", id);
    $('#paymentDetails').html('<div class="spinner-border text-primary" role="status"></div><p>در حال بارگذاری...</p>');
    $('#paymentModal').modal('show');

    $.get(`{{ url('admin/payments') }}/${id}`, function(data) {

        $('#paymentDetails').html(`
            <div class="text-start">
                <h5 class="fw-bold mb-3 text-primary">اطلاعات پرداخت</h5>
                <ul class="list-group mb-3">
                    <li class="list-group-item"><strong>شناسه پرداخت:</strong> ${data.transaction_code}</li>
                    <li class="list-group-item"><strong>مبلغ:</strong> ${new Intl.NumberFormat().format(data.amount)} تومان</li>
                    <li class="list-group-item"><strong>نوع پرداخت:</strong> ${data.type_fa}</li>
                    <li class="list-group-item"><strong>تاریخ ثبت:</strong> ${data.date}</li>
                    <li class="list-group-item"><strong>وضعیت:</strong> <span class="badge bg-${data.status_color}">${data.status_text}</span></li>
                </ul>

                <h5 class="fw-bold mb-3 text-primary">اطلاعات کاربر</h5>
                <ul class="list-group">
                    <li class="list-group-item"><strong>نام:</strong> ${data.user_name}</li>
                    <li class="list-group-item"><strong>شماره تماس:</strong> ${data.user_phone}</li>
                    <li class="list-group-item"><strong>کد عضویت:</strong> ${data.membership_code ?? '-'}</li>
                </ul>

                <div class="mt-3 text-center">
                    <a href="/admin/users/${data.user_id}" class="btn btn-outline-primary">
                        <i class="bi bi-person-badge"></i> مشاهده پروفایل
                    </a>
                    ${data.related_link ?? ''}
                </div>
            </div>
        `);
    }).fail(() => {
        $('#paymentDetails').html('<p class="text-danger">خطا در بارگذاری جزئیات پرداخت</p>');
    });
});
</script>
<script>
$(document).ready(function() {
    $('#paymentsTable').DataTable({
        language: {
            search: "جستجو:",
            lengthMenu: "نمایش _MENU_ رکورد",
            info: "نمایش _START_ تا _END_ از _TOTAL_ پرداخت",
            paginate: { next: "بعدی", previous: "قبلی" }
        },
        order: [[5, 'desc']],
        responsive: true
    });

    // تایید پرداخت
    $('.approve-btn').click(function() {
        let id = $(this).data('id');
        Swal.fire({
            title: 'تأیید پرداخت؟',
            text: "آیا از تأیید این پرداخت اطمینان دارید؟",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'بله، تأیید کن!',
            cancelButtonText: 'خیر',
            confirmButtonColor: '#28a745'
        }).then(result => {
            if(result.isConfirmed) {
                $.post(`{{ url('admin/payments') }}/${id}/approve`, {_token: '{{ csrf_token() }}'}, function() {
                    Swal.fire('انجام شد', 'پرداخت تأیید شد ✅', 'success').then(() => location.reload());
                });

            }
        });
    });

    // رد پرداخت
    $('.reject-btn').click(function() {
        let id = $(this).data('id');
        Swal.fire({
            title: 'رد پرداخت؟',
            text: "آیا از رد این پرداخت اطمینان دارید؟",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'بله، رد کن!',
            cancelButtonText: 'خیر',
            confirmButtonColor: '#dc3545'
        }).then(result => {
            if(result.isConfirmed) {
                $.post(`{{ url('admin/payments') }}/${id}/reject`, {_token: '{{ csrf_token() }}'}, function() {
                    Swal.fire('انجام شد', 'پرداخت رد شد ❌', 'success').then(() => location.reload());
                });
            }
        });
    });
});
</script>
@endpush
