@extends('user.layout')

@section('title', 'دوره‌های من')

@section('content')
<div class="container py-5" data-aos="fade-up">
    <h2 class="text-center mb-4">
        <i class="bi bi-book text-primary"></i> دوره‌های من
    </h2>

    @if($registrations->count() > 0)
        <div class="card shadow-lg border-0 animate__animated animate__fadeIn">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle text-center">
                        <thead class="table-light">
                            <tr>
                                <th>نام دوره</th>
                                <th>مدرس</th>
                                <th>تاریخ شروع</th>
                                <th>تاریخ پایان</th>
                                <th>کد دوره</th>
                                <th>گواهینامه</th>
                                <th>عملیات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($registrations as $registration)
                                <tr>
                                    <td>
                                        <strong>{{ $registration->course->title }}</strong>
                                        @if($registration->course->federationCourse)
                                            <br><small class="text-muted">{{ $registration->course->federationCourse->title }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($registration->course->teacher)
                                            {{ $registration->course->teacher->first_name }} {{ $registration->course->teacher->last_name }}
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($registration->course->start_date)
                                            <i class="bi bi-calendar-event"></i>
                                            {{ verta($registration->course->start_date)->format('Y/m/d') }}
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($registration->course->end_date)
                                            <i class="bi bi-calendar-check"></i>
                                            {{ verta($registration->course->end_date)->format('Y/m/d') }}
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <code>{{ $registration->course->id }}</code>
                                    </td>
                                    <td>
                                        @if($registration->certificate_file)
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle"></i> موجود
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="bi bi-x-circle"></i> موجود نیست
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('courses.show', $registration->course->id) }}" 
                                               class="btn btn-info" 
                                               title="مشاهده دوره">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            @if($registration->certificate_file)
                                                <a href="{{ route('dashboard.courses.downloadCertificate', $registration->id) }}" 
                                                   class="btn btn-success" 
                                                   title="دانلود گواهینامه"
                                                   download>
                                                    <i class="bi bi-download"></i>
                                                </a>
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
                <i class="bi bi-book-x display-1 text-muted d-block mb-3"></i>
                <h5 class="text-muted">شما هنوز در هیچ دوره‌ای شرکت نکرده‌اید</h5>
                <p class="text-muted">پس از ثبت‌نام و تأیید در دوره‌ها، آن‌ها در اینجا نمایش داده می‌شوند.</p>
                <a href="{{ route('courses.archive') }}" class="btn btn-primary mt-3">
                    <i class="bi bi-book me-2"></i> مشاهده دوره‌های موجود
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
            min-width: 700px;
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

