@extends('layout')

@section('title', 'آرشیو گزارش برنامه‌ها')

@section('content')
@php $fallbackImage = asset('images/slider/slide1.jpg'); @endphp
<section class="py-5 bg-light">
    <div class="container">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start mb-4">
            <div>
                <h2 class="fw-bold mb-2">آرشیو گزارش برنامه‌ها</h2>
                <p class="text-muted mb-0">مرور تجربه‌های ثبت‌شده، مسیرها، تیم‌ها و نتایج برنامه‌های کانون کوه.</p>
            </div>
            <a href="{{ route('programs.archive') }}" class="btn btn-outline-primary">مشاهده برنامه‌ها</a>
        </div>

        <div class="row g-3 g-md-4">
            @forelse($reports as $report)
                @php
                    $program = $report->program;
                    $programImage = $program && $program->files->first() ? asset('storage/'.$program->files->first()->file_path) : $fallbackImage;
                    $reportLink = auth()->check()
                        ? (auth()->user()->role === 'admin'
                            ? route('admin.program_reports.show', $report->id)
                            : route('dashboard.program_reports.show', $report->id))
                        : route('auth.login');
                @endphp
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="content-card h-100">
                        <div class="content-cover" style="background-image: linear-gradient(135deg, rgba(0,0,0,.5), rgba(0,0,0,.6)), url('{{ $programImage }}');"></div>
                        <div class="content-body">
                            <div class="d-flex justify-content-between align-items-center text-muted small mb-1">
                                <span class="d-inline-flex align-items-center gap-1"><i class="bi bi-calendar3"></i>{{ $report->report_date ? toPersianNumber(jdate($report->report_date)->format('Y/m/d')) : toPersianNumber(jdate($report->created_at)->format('Y/m/d')) }}</span>
                                <span class="d-inline-flex align-items-center gap-1"><i class="bi bi-geo-alt"></i>{{ $report->report_region_route ?? ($program->region_name ?? 'مسیر کوهستانی') }}</span>
                            </div>
                            <h6 class="content-title">{{ $report->report_program_name ?? ($program->name ?? 'گزارش برنامه') }}</h6>
                            <p class="text-muted small mb-3 line-clamp-2">سرپرست: {{ optional($program->userRoles->first())->user_name ?? 'نامشخص' }}</p>
                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                <span class="badge bg-light text-dark d-inline-flex align-items-center gap-1"><i class="bi bi-people"></i>{{ toPersianNumber($report->participants_count ?? 0) }} نفر</span>
                                <a href="{{ $reportLink }}" class="btn btn-sm btn-outline-primary">مشاهده</a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info">هنوز گزارشی در دسترس نیست.</div>
                </div>
            @endforelse
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $reports->links() }}
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
.content-card { background:#fff; border-radius:14px; overflow:hidden; box-shadow:0 15px 35px rgba(0,0,0,0.08); display:flex; flex-direction:column; transition:transform .25s ease, box-shadow .25s ease; min-height:100%; }
.content-card:hover { transform: translateY(-6px); box-shadow:0 18px 40px rgba(0,0,0,0.12); }
.content-cover { height:170px; background-size:cover; background-position:center; }
.content-body { padding:14px; display:flex; flex-direction:column; height:100%; }
.content-title { font-weight:700; font-size:16px; margin:8px 0 10px; line-height:1.6; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; min-height:48px; }
.line-clamp-2 { display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
.badge i, .content-body i { margin-left:6px; }
</style>
@endpush
