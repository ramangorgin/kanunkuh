@extends('layout')

@section('content')
@php $fallbackImage = asset('images/slider/slide1.jpg'); @endphp
<section class="py-5 bg-light">
    <div class="container">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start mb-4">
            <div>
                <h2 class="fw-bold mb-2">برنامه‌های کانون کوه</h2>
                <p class="text-muted mb-0">آرشیو برنامه‌ها با تصاویر، تاریخ اجرا، ارتفاع قله و هزینه اعضا.</p>
            </div>
            <a href="{{ route('auth.phone') }}" class="btn btn-outline-primary">ثبت‌نام برای برنامه‌ها</a>
        </div>

        <div class="row g-3 g-md-4">
            @forelse($programs as $program)
                @php
                    $programImage = optional($program->files->first())->file_path ? asset('storage/'.optional($program->files->first())->file_path) : $fallbackImage;
                @endphp
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="content-card h-100">
                        <div class="content-cover" style="background-image: linear-gradient(135deg, rgba(0,0,0,.5), rgba(0,0,0,.6)), url('{{ $programImage }}');"></div>
                        <div class="content-body">
                            <div class="d-flex justify-content-between text-muted small mb-1">
                                <span class="d-inline-flex align-items-center gap-1"><i class="bi bi-calendar-event"></i>{{ $program->execution_date ? toPersianNumber(jdate($program->execution_date)->format('Y/m/d')) : '-' }}</span>
                                <span class="d-inline-flex align-items-center gap-1"><i class="bi bi-geo-alt"></i>{{ $program->region_name ?? 'مسیر ویژه' }}</span>
                            </div>
                            <h6 class="content-title">{{ $program->name ?? $program->title ?? 'برنامه باشگاه' }}</h6>
                            <p class="text-muted small mb-2">ارتفاع قله: {{ toPersianNumber($program->peak_height ?? 0) }} متر</p>
                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                <span class="badge bg-success-subtle text-success d-inline-flex align-items-center gap-1"><i class="bi bi-cash-coin"></i>{{ toPersianNumber($program->cost_member ?? 0) }} تومان</span>
                                <a href="{{ route('programs.show', $program->id) }}" class="btn btn-sm btn-outline-primary">جزئیات</a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-secondary">هیچ برنامه‌ای در حال حاضر موجود نیست.</div>
                </div>
            @endforelse
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $programs->links() }}
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
.content-title { font-weight:700; font-size:16px; margin:10px 0 12px; line-height:1.6; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; min-height:48px; }
.badge i, .content-body i { margin-left:6px; }
</style>
@endpush
