@extends('admin.layout')

@section('title', 'لیست گزارش‌های برنامه')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active" aria-current="page">لیست گزارش‌های برنامه</li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="card shadow-sm">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-file-text me-2"></i> لیست گزارش‌های برنامه</h5>
            <a href="{{ route('admin.program_reports.create') }}" class="btn btn-light btn-sm">
                <i class="bi bi-plus-circle me-1"></i> گزارش جدید
            </a>
        </div>
        <div class="card-body">
            <table id="reports-table" class="table table-bordered table-hover nowrap">
                <thead class="table-light">
                    <tr>
                        <th>برنامه</th>
                        <th>تعداد شرکت‌کنندگان</th>
                        <th>تاریخ ایجاد</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reports as $report)
                        <tr>
                            <td>
                                <a href="{{ route('admin.program_reports.show', $report->id) }}" class="text-decoration-none">
                                    {{ $report->program->name ?? '—' }}
                                </a>
                            </td>
                            <td>
                                {{ $report->participants_count ?? 0 }} نفر
                            </td>
                            <td>
                                {{ \Morilog\Jalali\Jalalian::fromCarbon($report->created_at)->format('Y/m/d') }}
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('admin.program_reports.show', $report->id) }}" class="btn btn-info" title="مشاهده">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.program_reports.edit', $report->id) }}" class="btn btn-primary" title="ویرایش">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.program_reports.destroy', $report->id) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('آیا از حذف این گزارش مطمئن هستید؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" title="حذف">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('#reports-table').DataTable({
                responsive: true,
                language: {
                    "search": "جستجو:",
                    "lengthMenu": "نمایش _MENU_ مورد",
                    "info": "نمایش _START_ تا _END_ از _TOTAL_ مورد",
                    "paginate": {
                        "first": "اول",
                        "last": "آخر",
                        "next": "بعدی",
                        "previous": "قبلی"
                    },
                    "zeroRecords": "موردی پیدا نشد"
                },
                order: [[2, 'desc']]
            });
        });
    </script>
@endpush

