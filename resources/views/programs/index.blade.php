@extends('admin.layout')

@section('title', 'لیست برنامه‌ها')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.programs.index') }}">برنامه‌ها</a></li>
            <li class="breadcrumb-item active" aria-current="page">لیست برنامه‌ها</li>
        </ol>
    </nav>
@endsection

@push('styles')
<style>
    .programs-table-wrapper {
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
    
    #programs-table {
        font-size: 0.875rem;
        margin: 0;
    }
    
    #programs-table thead th {
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
    
    #programs-table tbody td {
        padding: 12px 15px;
        vertical-align: middle;
        border-bottom: 1px solid #f0f0f0;
    }
    
    #programs-table tbody tr:hover {
        background-color: #f8f9fa;
        transition: background-color 0.2s;
    }
    
    .program-name-link {
        color: #495057;
        font-weight: 600;
        text-decoration: none;
        transition: color 0.2s;
        font-size: 0.9rem;
    }
    
    .program-name-link:hover {
        color: #667eea;
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
    
    .action-btn i {
        font-size: 0.9rem;
    }
    
    .btn-view {
        background: #17a2b8;
        color: white;
    }
    
    .btn-view:hover {
        background: #138496;
        color: white;
    }
    
    .btn-edit {
        background: #0d6efd;
        color: white;
    }
    
    .btn-edit:hover {
        background: #0b5ed7;
        color: white;
    }
    
    .btn-delete {
        background: #dc3545;
        color: white;
    }
    
    .btn-delete:hover {
        background: #bb2d3b;
        color: white;
    }
    
    .status-badge {
        font-size: 0.75rem;
        padding: 4px 10px;
        border-radius: 12px;
        font-weight: 500;
    }
    
    .date-cell {
        font-size: 0.85rem;
        color: #6c757d;
    }
    
    .date-cell i {
        margin-left: 5px;
    }
    
    .cost-cell {
        font-size: 0.85rem;
        font-weight: 600;
    }
    
    .dataTables_wrapper {
        padding: 20px;
    }
    
    .dataTables_filter input {
        border-radius: 6px;
        border: 1px solid #dee2e6;
        padding: 6px 12px;
        font-size: 0.875rem;
    }
    
    .dataTables_length select {
        border-radius: 6px;
        border: 1px solid #dee2e6;
        padding: 4px 8px;
        font-size: 0.875rem;
    }
</style>
@endpush

@section('content')
    <div class="programs-table-wrapper">
        <div class="table-header-section">
            <h5><i class="bi bi-list-ul me-2"></i> لیست برنامه‌ها</h5>
            <a href="{{ route('admin.programs.create') }}" class="btn btn-light btn-sm" style="font-size: 0.875rem;">
                <i class="bi bi-plus-circle me-1"></i> برنامه جدید
            </a>
        </div>
        <div class="dataTables_wrapper">
            <table id="programs-table" class="table table-hover">
                <thead>
                    <tr>
                        <th>نام برنامه</th>
                        <th>نوع برنامه</th>
                        <th>تاریخ اجرا</th>
                        <th>وضعیت</th>
                        <th>هزینه عضو</th>
                        <th style="text-align: center; width: 120px;">عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($programs as $program)
                        <tr>
                            <td>
                                <a href="{{ route('admin.programs.show', $program->id) }}" class="program-name-link">
                                    {{ $program->name }}
                                </a>
                            </td>
                            <td>
                                <span class="badge bg-info status-badge">{{ $program->program_type }}</span>
                            </td>
                            <td class="date-cell">
                                @if($program->execution_date)
                                    <i class="bi bi-calendar-event"></i>
                                    {{ verta($program->execution_date)->format('Y/m/d H:i') }}
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($program->status == 'draft')
                                    <span class="badge bg-secondary status-badge">پیش‌نویس</span>
                                @elseif($program->status == 'open')
                                    <span class="badge bg-success status-badge">باز</span>
                                @elseif($program->status == 'closed')
                                    <span class="badge bg-warning status-badge">بسته</span>
                                @elseif($program->status == 'done')
                                    <span class="badge bg-primary status-badge">انجام شده</span>
                                @endif
                            </td>
                            <td class="cost-cell">
                                @if($program->cost_member)
                                    <span class="text-success">{{ number_format($program->cost_member) }} ریال</span>
                                @else
                                    <span class="text-muted">رایگان</span>
                                @endif
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('admin.programs.show', $program->id) }}" 
                                       class="btn action-btn btn-view" 
                                       title="مشاهده">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.programs.edit', $program->id) }}" 
                                       class="btn action-btn btn-edit" 
                                       title="ویرایش">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.programs.destroy', $program->id) }}" 
                                          method="POST" 
                                          class="d-inline delete-form"
                                          data-program-name="{{ $program->name }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" 
                                                class="btn action-btn btn-delete delete-btn" 
                                                title="حذف">
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
            // Initialize DataTable
            $('#programs-table').DataTable({
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
                order: [[2, 'desc']],
                pageLength: 15,
                lengthMenu: [[10, 15, 25, 50, -1], [10, 15, 25, 50, "همه"]],
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
                drawCallback: function() {
                    // Re-initialize delete buttons after DataTable redraw
                    initDeleteButtons();
                }
            });

            // Initialize delete buttons
            function initDeleteButtons() {
                $('.delete-btn').off('click').on('click', function(e) {
                    e.preventDefault();
                    const form = $(this).closest('form');
                    const programName = form.data('program-name');
                    
                    Swal.fire({
                        title: 'آیا مطمئن هستید؟',
                        html: `<p style="font-size: 0.95rem; margin-bottom: 10px;">آیا از حذف برنامه <strong>"${programName}"</strong> اطمینان دارید؟</p><p style="font-size: 0.85rem; color: #dc3545;">این عمل غیرقابل بازگشت است!</p>`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: '<i class="bi bi-check-circle me-1"></i> بله، حذف شود',
                        cancelButtonText: '<i class="bi bi-x-circle me-1"></i> انصراف',
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        reverseButtons: true,
                        customClass: {
                            popup: 'rtl-popup',
                            confirmButton: 'swal2-confirm-custom',
                            cancelButton: 'swal2-cancel-custom'
                        },
                        buttonsStyling: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Show loading
                            Swal.fire({
                                title: 'در حال حذف...',
                                text: 'لطفاً صبر کنید',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            // Submit form
                            form.submit();
                        }
                    });
                });
            }

            // Initialize on page load
            initDeleteButtons();
        });
    </script>
    
    <style>
        /* SweetAlert2 RTL Support */
        .swal2-popup {
            direction: rtl;
            text-align: right;
            font-family: 'Peyda', sans-serif;
        }
        
        .swal2-title {
            font-size: 1.25rem;
            font-weight: 600;
        }
        
        .swal2-html-container {
            font-size: 0.95rem;
        }
        
        .swal2-confirm-custom,
        .swal2-cancel-custom {
            font-family: 'Peyda', sans-serif;
            font-size: 0.9rem;
            padding: 10px 20px;
            border-radius: 6px;
        }
        
        .swal2-actions {
            gap: 10px;
        }
    </style>
@endpush
