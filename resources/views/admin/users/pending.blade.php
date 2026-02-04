{{-- Admin view for pending memberships. --}}
@extends('admin.layout')

@section('title', 'عضویت‌های در انتظار تأیید')

@section('content')
<div class="container-fluid py-4 animate__animated animate__fadeIn">

    {{-- عنوان صفحه --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-dark mb-0">
            <i class="bi bi-hourglass-split text-warning me-2"></i> عضویت‌های در انتظار تأیید
        </h4>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-right-circle"></i> بازگشت به لیست کاربران
        </a>
    </div>

    {{-- جدول کاربران در انتظار --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body table-responsive">
            @if($pendingProfiles->count())
                <table id="pendingTable" class="table table-striped align-middle text-center">
                    <thead class="table-light">
                        <tr>
                            <th>شناسه عضویت</th>
                            <th>نام و نام خانوادگی</th>
                            <th>شماره تماس</th>
                            <th>نوع عضویت</th>
                            <th>تاریخ ثبت‌نام</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingProfiles as $profile)
                            <tr id="row-{{ $profile->id }}">
                                <td>{{ toPersianNumber($profile->membership_id) }}</td>
                                <td>{{ $profile->first_name }} {{ $profile->last_name }}</td>
                                <td>{{ toPersianNumber($profile->user->phone) }}</td>
                                <td>{{ $profile->membership_type ?? '-' }}</td>
                                <td>{{ toPersianNumber(jdate($profile->created_at)->format('Y/m/d')) }}</td>
                                <td>
                                    <a href="{{ route('admin.users.show', $profile->user->id) }}" class="btn btn-info btn-sm text-white">
                                        <i class="bi bi-eye-fill"></i> مشاهده
                                    </a>
                                    <button class="btn btn-success btn-sm approve-user" data-id="{{ $profile->id }}">
                                        <i class="bi bi-check-circle-fill"></i> تایید
                                    </button>
                                    <button class="btn btn-danger btn-sm reject-user" data-id="{{ $profile->id }}">
                                        <i class="bi bi-x-circle-fill"></i> رد
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-center text-muted my-4">هیچ عضوی در انتظار تایید نیست 🌿</p>
            @endif
        </div>
    </div>

</div>
@endsection

@push('scripts')
<!--
{{-- DataTables --}}
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

{{-- SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
-->
<script>
$(document).ready(function() {
    $('#pendingTable').DataTable({
        "language": {
            "search": "جستجو:",
            "lengthMenu": "نمایش _MENU_ مورد",
            "info": "نمایش _START_ تا _END_ از _TOTAL_ کاربر در انتظار",
            "paginate": {
                "first": "اول",
                "last": "آخر",
                "next": "بعدی",
                "previous": "قبلی"
            }
        },
        "pageLength": 10,
        "ordering": false
    });

    // تایید عضویت
    $('.approve-user').click(function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'تایید عضویت؟',
            text: 'آیا از تایید این کاربر اطمینان دارید؟',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'بله، تایید شود',
            cancelButtonText: 'انصراف',
            confirmButtonColor: '#198754'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post(`{{ url('admin/users') }}/${id}/approve`, {_token: '{{ csrf_token() }}'}, function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'عضویت تایید شد ✅',
                        showConfirmButton: false,
                        timer: 1800
                    });
                    $(`#row-${id}`).fadeOut();
                }).fail(() => {
                    Swal.fire('خطا', 'مشکلی در تایید عضویت پیش آمد.', 'error');
                });
            }
        });
    });

    // رد عضویت
    $('.reject-user').click(function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'رد عضویت؟',
            text: 'آیا مطمئن هستید که می‌خواهید عضویت این کاربر را رد کنید؟',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'بله، رد شود',
            cancelButtonText: 'انصراف',
            confirmButtonColor: '#dc3545'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post(`{{ url('admin/users') }}/${id}/reject`, {_token: '{{ csrf_token() }}'}, function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'عضویت رد شد ❌',
                        showConfirmButton: false,
                        timer: 1800
                    });
                    $(`#row-${id}`).fadeOut();
                }).fail(() => {
                    Swal.fire('خطا', 'مشکلی در رد عضویت پیش آمد.', 'error');
                });
            }
        });
    });
});
</script>
@endpush
