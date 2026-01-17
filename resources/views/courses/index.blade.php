@extends('admin.layout')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.courses.index') }}">دوره‌ها</a></li>
            <li class="breadcrumb-item active" aria-current="page">لیست دوره‌ها</li>
        </ol>
    </nav>
@endsection

@section('content')
    <h3>لیست دوره‌ها</h3>

    <a href="{{ route('admin.courses.create') }}" class="btn btn-success mb-3">+ دوره جدید</a>

    <div class="table-responsive">
        <table class="table table-striped table-bordered" id="coursesTable">
            <thead class="table-light">
                <tr>
                    <th>ردیف</th>
                    <th>عنوان</th>
                    <th>مدرس</th>
                    <th>تاریخ شروع</th>
                    <th>تاریخ پایان</th>
                    <th>ثبت‌نام</th>
                    <th>عملیات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($courses as $index => $course)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $course->title }}</td>
                    <td>{{ $course->teacher }}</td>
                    <td>{{ $course->start_date }}</td>
                    <td>{{ $course->end_date }}</td>
                    <td>{!! $course->is_registration_open ? '<span class="text-success">باز</span>' : '<span class="text-danger">بسته</span>' !!}</td>
                    <td>
                        <a href="{{ route('admin.courses.show', $course->id) }}" class="btn btn-sm btn-info">نمایش</a>
                        <a href="{{ route('admin.courses.edit', $course->id) }}" class="btn btn-sm btn-secondary">ویرایش</a>
                        <a href="{{ route('admin.registrations.show', ['type' => 'course', 'id' => $course->id]) }}" class="btn btn-sm btn-warning">ثبت‌نامی‌ها</a>
                        <form method="POST" action="{{ route('admin.courses.destroy', $course->id) }}" class="d-inline-block" onsubmit="return confirm('آیا مطمئن هستید؟')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">حذف</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

@section('scripts')
<!--
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

    <link rel="stylesheet" href="https://cdn.datatables.net/colreorder/1.6.2/css/colReorder.bootstrap5.min.css">
    <script src="https://cdn.datatables.net/colreorder/1.6.2/js/dataTables.colReorder.min.js"></script>
    
    <link rel="stylesheet" href="https://cdn.datatables.net/searchpanes/2.2.0/css/searchPanes.bootstrap5.min.css">
    <script src="https://cdn.datatables.net/searchpanes/2.2.0/js/dataTables.searchPanes.min.js"></script>
    <script src="https://cdn.datatables.net/searchpanes/2.2.0/js/searchPanes.bootstrap5.min.js"></script>
-->
    <script>
        $(document).ready(function () {
            $('#coursesTable').DataTable({
                responsive: true,
                colReorder: true,
                searchPanes: true,
                dom: 'Bfrtip',
                buttons: [
                    { extend: 'copy', text: 'کپی' },
                    { extend: 'excel', text: 'اکسل' },
                    { extend: 'pdf', text: 'PDF' },
                    { extend: 'print', text: 'چاپ' }
                ],
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
                    "zeroRecords": "موردی پیدا نشد",
                    "buttons": {
                        "copy": "کپی",
                        "excel": "خروجی اکسل",
                        "pdf": "خروجی PDF",
                        "print": "چاپ"
                    }
                },
                order: [[1, 'desc']]
            });
        });
    </script>
@endsection