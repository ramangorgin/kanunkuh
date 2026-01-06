@extends('layout')

@section('content')
<section class="py-5 bg-light">
    <div class="container">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start mb-4">
            <div>
                <h2 class="fw-bold mb-2">دوره‌های آموزشی</h2>
                <p class="text-muted mb-0">کارگاه‌ها و دوره‌های تخصصی بدون نیاز به تصویر، با جزئیات مهم.</p>
            </div>
            <a href="{{ route('auth.phone') }}" class="btn btn-outline-primary">ثبت‌نام سریع</a>
        </div>

        <div class="row g-3 g-md-4">
            @forelse($courses as $course)
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="course-card h-100">
                        <div class="course-body">
                            <div class="d-flex justify-content-between text-muted small mb-2">
                                <span class="d-inline-flex align-items-center gap-1"><i class="bi bi-calendar3"></i>{{ $course->start_date ? toPersianNumber(jdate($course->start_date)->format('Y/m/d')) : '-' }}</span>
                                <span class="d-inline-flex align-items-center gap-1"><i class="bi bi-clock"></i>{{ $course->end_date ? toPersianNumber(jdate($course->end_date)->format('Y/m/d')) : '' }}</span>
                            </div>
                            <h6 class="course-title">{{ $course->title }}</h6>
                            <p class="text-muted small line-clamp-3 mb-3">{{ \Illuminate\Support\Str::limit(strip_tags($course->description), 140, '...') }}</p>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-info-subtle text-info d-inline-flex align-items-center gap-1"><i class="bi bi-cash-coin"></i>{{ toPersianNumber($course->member_cost ?? 0) }} تومان</span>
                                <span class="badge bg-light text-dark d-inline-flex align-items-center gap-1"><i class="bi bi-people"></i>{{ toPersianNumber($course->capacity ?? 0) }} نفر</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                <small class="text-muted d-inline-flex align-items-center gap-1"><i class="bi bi-geo-alt"></i>{{ $course->place ?? 'مکان متعاقباً اعلام می‌شود' }}</small>
                                <a href="{{ route('courses.show', $course->id) }}" class="btn btn-sm btn-outline-primary">جزئیات</a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-secondary">هیچ دوره‌ای در حال حاضر موجود نیست.</div>
                </div>
            @endforelse
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $courses->links() }}
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
.course-card { background:#fff; border-radius:14px; box-shadow:0 15px 35px rgba(0,0,0,0.08); padding:14px; display:flex; flex-direction:column; transition:transform .25s ease, box-shadow .25s ease; min-height:100%; }
.course-card:hover { transform: translateY(-6px); box-shadow:0 18px 40px rgba(0,0,0,0.12); }
.course-body { display:flex; flex-direction:column; height:100%; }
.course-title { font-weight:700; font-size:16px; margin:6px 0 12px; line-height:1.6; min-height:48px; }
.line-clamp-3 { display:-webkit-box; -webkit-line-clamp:3; -webkit-box-orient:vertical; overflow:hidden; }
.badge i, .course-body i { margin-left:6px; }
</style>
@endpush
