{{-- Admin users list view. --}}
@extends('admin.layout')

@section('title', 'لیست کاربران')

@section('content')
<div class="container-fluid py-4 animate__animated animate__fadeIn">

    {{-- هدر صفحه --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-dark mb-0">
            <i class="bi bi-people-fill text-primary me-2"></i> لیست کاربران
        </h4>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.memberships.pending') }}" class="btn btn-warning">
                <i class="bi bi-hourglass-split"></i> عضویت‌های در انتظار
            </a>
            <a href="{{ route('admin.users.export') }}" class="btn btn-success">
                <i class="bi bi-file-earmark-excel-fill"></i> خروجی Excel
            </a>
        </div>
    </div>

    {{-- فرم جستجو --}}
    <form method="GET" class="mb-3">
        <div class="input-group shadow-sm" style="max-width: 400px;">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="جستجو بر اساس نام یا شماره تماس">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-search"></i> جستجو
            </button>
        </div>
    </form>

    {{-- جدول کاربران --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body table-responsive">
            <table id="usersTable" class="table table-striped align-middle text-center">
                <thead class="table-light">
                    <tr>
                        <th>شناسه</th>
                        <th>نام و نام خانوادگی</th>
                        <th>شماره تماس</th>
                        <th>وضعیت عضویت</th>
                        <th>تاریخ ثبت‌نام</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr id="row-{{ $user->id }}">
                            <td>{{ toPersianNumber(optional($user->profile)->membership_id ?? '-') }}</td>
                            <td>{{ optional($user->profile)->first_name ?? '-' }} {{ optional($user->profile)->last_name ?? '' }}</td>
                            <td>{{ toPersianNumber($user->phone) }}</td>
                            <td>
                                @if($user->profile && $user->profile->membership_status === 'approved')
                                    <span class="badge bg-success">تایید شده</span>
                                @elseif($user->profile && $user->profile->membership_status === 'pending')
                                    <span class="badge bg-warning text-dark">در انتظار</span>
                                @elseif($user->profile && $user->profile->membership_status === 'rejected')
                                    <span class="badge bg-danger">رد شده</span>
                                @else
                                    <span class="badge bg-secondary">نامشخص</span>
                                @endif
                            </td>
                            <td>{{ $user->created_at ? toPersianNumber(\Hekmatinasser\Verta\Verta::instance($user->created_at)->format('Y/m/d')) : '-' }}</td>
                            <td>
                                <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-sm btn-info text-white">
                                    <i class="bi bi-eye-fill"></i> مشاهده
                                </a>
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-secondary">
                                    <i class="bi bi-pencil-square"></i> ویرایش
                                </a>
                                <button class="btn btn-sm btn-danger delete-user" data-id="{{ $user->id }}">
                                    <i class="bi bi-trash3"></i> حذف
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- صفحه‌بندی --}}
            <div class="mt-3">
                {{ $users->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

</div>
@endsection

@push('styles')
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
    // فعال‌سازی DataTables
    $('#usersTable').DataTable({
        "language": {
            "search": "جستجو:",
            "lengthMenu": "نمایش _MENU_ کاربر",
            "info": "نمایش _START_ تا _END_ از _TOTAL_ کاربر",
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

    // حذف کاربر با SweetAlert
    $('.delete-user').click(function() {
        const userId = $(this).data('id');
        Swal.fire({
            title: 'آیا مطمئن هستید؟',
            text: "این کاربر به طور کامل حذف خواهد شد!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'بله، حذف شود',
            cancelButtonText: 'انصراف',
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/admin/users/${userId}`,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function() {
                        $(`#row-${userId}`).fadeOut();
                        Swal.fire({
                            icon: 'success',
                            title: 'حذف شد!',
                            text: 'کاربر با موفقیت حذف شد.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'خطا!',
                            text: 'مشکلی در حذف کاربر پیش آمد.',
                        });
                    }
                });
            }
        });
    });
});
</script>
@endpush
