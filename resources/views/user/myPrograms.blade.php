@extends('user.layout')

@section('title', 'برنامه‌های من')

@section('content')
<div class="container py-5" data-aos="fade-up">
    <h2 class="text-center mb-4">
        <i class="bi bi-calendar-event text-primary"></i> برنامه‌های من
    </h2>

    @if($programs->count() > 0)
        <div class="card shadow-lg border-0 animate__animated animate__fadeIn">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle text-center">
                        <thead class="table-light">
                            <tr>
                                <th>نام برنامه</th>
                                <th>نوع برنامه</th>
                                <th>تاریخ اجرا</th>
                                <th>وضعیت</th>
                                <th>گزارش</th>
                                <th>عملیات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($programs as $program)
                                <tr>
                                    <td>
                                        <strong>{{ $program->name }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $program->program_type }}</span>
                                    </td>
                                    <td>
                                        @if($program->execution_date)
                                            <i class="bi bi-calendar-event"></i>
                                            {{ verta($program->execution_date)->format('Y/m/d H:i') }}
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($program->status == 'done')
                                            <span class="badge bg-success">انجام شده</span>
                                        @elseif($program->status == 'closed')
                                            <span class="badge bg-warning">بسته</span>
                                        @elseif($program->status == 'open')
                                            <span class="badge bg-info">باز</span>
                                        @elseif($program->status == 'draft')
                                            <span class="badge bg-secondary">پیش‌نویس</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $program->status }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($program->report)
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle"></i> گزارش موجود
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="bi bi-x-circle"></i> بدون گزارش
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('programs.show', $program->id) }}" 
                                               class="btn btn-info" 
                                               title="مشاهده برنامه">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            @if($program->report)
                                                <a href="{{ route('program_reports.show', $program->report->id) }}" 
                                                   class="btn btn-success" 
                                                   title="مشاهده گزارش">
                                                    <i class="bi bi-file-text"></i>
                                                </a>
                                            @else
                                                @php
                                                    // Check if execution date has passed (including today)
                                                    // execution_date is already a Carbon instance due to $casts in Program model
                                                    $executionDatePassed = $program->execution_date && now()->startOfDay()->gte($program->execution_date->startOfDay());
                                                @endphp
                                                @if($executionDatePassed)
                                                    <a href="{{ route('program_reports.create', $program->id) }}" 
                                                       class="btn btn-primary" 
                                                       title="نوشتن گزارش">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="card shadow-lg border-0 animate__animated animate__fadeIn">
            <div class="card-body text-center py-5">
                <i class="bi bi-calendar-x display-1 text-muted d-block mb-3"></i>
                <h5 class="text-muted">شما هنوز در هیچ برنامه‌ای شرکت نکرده‌اید</h5>
                <p class="text-muted">پس از ثبت‌نام و تأیید در برنامه‌ها، آن‌ها در اینجا نمایش داده می‌شوند.</p>
                <a href="{{ route('programs.index') }}" class="btn btn-primary mt-3">
                    <i class="bi bi-calendar-event me-2"></i> مشاهده برنامه‌های موجود
                </a>
            </div>
        </div>
    @endif
</div>

@push('styles')
<style>
    @media (max-width: 768px) {
        .table-responsive {
            display: block;
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        .table-responsive table {
            min-width: 600px;
            font-size: 0.85rem;
        }
        
        .table-responsive th,
        .table-responsive td {
            padding: 8px 10px !important;
        }
    }
</style>
@endpush
@endsection

