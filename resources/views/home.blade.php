{{-- Home page view with featured content and highlights. --}}
@extends('layout')

@section('title', 'خانه')

@section('content')

@php
    use Illuminate\Support\Str;
    $logo = asset('images/logo.png');
    $fallbackImage = asset('images/slider/slide1.jpg');
    $historyItems = [
        ['year' => '۱۳۸۹', 'title' => 'هسته اولیه', 'desc' => 'پیشکسوتان بازنشسته سایپا و ایران‌خودرو با هدف حفظ سلامت و نشاط، هسته کوهنوردی را شکل دادند.'],
        ['year' => '۱۳۸۹', 'title' => 'صعودهای آغازین', 'desc' => 'چند صعود گروهی و تشکیل هیئت مؤسس با حضور مربیان و سرپرستان باسابقه.'],
        ['year' => '۱۳۹۰', 'title' => 'ساختاردهی', 'desc' => 'تشکیل کارگروه‌های فنی، آموزشی، فرهنگی، محیط‌زیست، مالی و روابط عمومی و جذب اعضای جدید.'],
        ['year' => '۱۳۹۴-۱۳۹۵', 'title' => 'مسیر به باشگاه', 'desc' => 'معرفی اعضا به اردوهای هیمالیانوردی و آغاز فرآیند اخذ مجوز رسمی باشگاه.'],
        ['year' => '۱۳۹۶-۱۳۹۷', 'title' => 'ثبت و مجوز', 'desc' => 'ثبت موسسه غیرانتفاعی و دریافت مجوز باشگاه "دوستداران قله‌ها و طبیعت" پس از پیگیری مستمر.'],
        ['year' => '۱۳۹۷', 'title' => 'صعود کارل مارکس', 'desc' => 'برای بزرگداشت احمد نیک‌بیان، تیم کانون نخستین صعود ایرانی قله کارل مارکس تاجیکستان را ثبت کرد.'],
        ['year' => 'اکنون', 'title' => 'تداوم رشد', 'desc' => 'جلسات منظم، آموزش‌های تخصصی، حفاظت محیط‌زیست و توسعه زیرساخت دیجیتال برای خدمت بهتر به اعضا.'],
    ];
@endphp

<div id="heroSlider" class="hero-slider">
    <div class="carousel-container">
        @for ($i = 1; $i <= 10; $i++)
            <div class="carousel-slide {{ $i === 1 ? 'active' : '' }}">
                <img src="{{ asset('images/slider/slide' . $i . '.jpg') }}" 
                     alt="Slide {{ $i }}" 
                     class="slide-image">
            </div>
        @endfor
    </div>

    <div class="hero-overlay"></div>

    <div class="hero-content text-center text-white">
        <img src="{{ asset('images/logo-white.png') }}" class="rounded-circle shadow mb-4" style="width: 120px; height: 120px; object-fit: cover;" alt="کانون کوه">
        <h2 class="fw-bold mb-2 ">باشگاه کوهنوردی دوستداران قله‌ها و طبیعت</h2>
        <h4 class="mb-4">(کانون کوه)</h4>
        <a href="{{ route('conditions') }}" class="btn btn-primary px-4 mt-2 ms-2">شرایط عضویت</a>
        <a href="{{ route('auth.phone') }}" class="btn btn-outline-light mt-2 px-4">ثبت‌نام در باشگاه</a>
    </div>
</div>


{{-- ارزش‌های کلیدی --}}
<section class="py-5 bg-white">
    <div class="container">
        <div class="row g-4 align-items-center">
            <div class="col-lg-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <img src="{{ $logo }}" alt="کانون کوه" class="rounded-circle shadow" style="width:72px;height:72px;object-fit:cover;">
                    <div>
                        <h3 class="fw-bold mb-1">کانون کوه</h3>
                        <p class="text-muted mb-0">خانه‌ای برای یادگیری، تجربه و رفاقت در ارتفاعات.</p>
                    </div>
                </div>
                <p class="text-secondary mb-3">با ترکیب آموزش حرفه‌ای، سفرهای مسئولانه و زیرساخت دیجیتال، تجربه‌ای امن و لذت‌بخش برای کوهنوردان و طبیعت‌گردان فراهم می‌کنیم.</p>
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge bg-primary-subtle text-primary"><i class="bi bi-shield-check ms-1"></i>ایمنی</span>
                    <span class="badge bg-success-subtle text-success"><i class="bi bi-heart-pulse ms-1"></i>سلامت</span>
                    <span class="badge bg-warning-subtle text-warning"><i class="bi bi-people ms-1"></i>همدلی</span>
                    <span class="badge bg-info-subtle text-info"><i class="bi bi-graph-up-arrow ms-1"></i>پیشرفت</span>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="row g-3">
                    <div class="col-6 col-md-3">
                        <div class="mini-card">
                            <i class="bi bi-people fs-3 text-primary"></i>
                            <div class="fw-bold fs-5 mt-2">{{ toPersianNumber($stats['members'] ?? 0) }}</div>
                            <small class="text-muted">اعضای فعال</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="mini-card">
                            <i class="bi bi-calendar-week fs-3 text-success"></i>
                            <div class="fw-bold fs-5 mt-2">{{ toPersianNumber($stats['programs'] ?? 0) }}</div>
                            <small class="text-muted">برنامه‌ها</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="mini-card">
                            <i class="bi bi-mortarboard fs-3 text-warning"></i>
                            <div class="fw-bold fs-5 mt-2">{{ toPersianNumber($stats['courses'] ?? 0) }}</div>
                            <small class="text-muted">دوره‌های آموزشی</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="mini-card">
                            <i class="bi bi-journal-richtext fs-3 text-danger"></i>
                            <div class="fw-bold fs-5 mt-2">{{ toPersianNumber($stats['reports'] ?? 0) }}</div>
                            <small class="text-muted">گزارش برنامه</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- تاریخچه باشگاه (تایم‌لاین) --}}
<section class="py-5 bg-light position-relative overflow-hidden">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-5">
                <h3 class="fw-bold mb-3">روایت مسیر ما</h3>
                <p class="text-secondary mb-3">چکیده‌ای کوتاه از تاریخچه کانون کوه بر اساس مستندات ارائه‌شده؛ برای انتشار رسمی، همین متن را می‌توانید به‌دلخواه ویرایش و تکمیل کنید.</p>
                <div class="d-flex gap-2 flex-wrap">
                    <span class="badge bg-primary text-light"><i class="bi bi-flag ms-1"></i>آغاز راه</span>
                    <span class="badge bg-success text-light"><i class="bi bi-compass ms-1"></i>اکتشاف</span>
                    <span class="badge bg-info text-dark"><i class="bi bi-emoji-smile ms-1"></i>جامعه پویا</span>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="timeline">

                    @foreach($historyItems as $item)
                        <div class="timeline-item">
                            <div class="timeline-year">{{ $item['year'] }}</div>
                            <div class="timeline-dot"></div>
                            <div class="timeline-card shadow-sm">
                                <h6 class="fw-bold mb-1">{{ $item['title'] }}</h6>
                                <p class="text-muted mb-0">{{ $item['desc'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

{{-- آکاردئون محتوای تازه --}}
<section class="py-5 bg-white">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold mb-0">تازه‌ترین محتوا</h3>
            <p class="text-muted mb-0">برنامه‌ها، گزارش‌ها، دوره‌ها و نوشته‌های وبلاگ در یک نگاه</p>
        </div>

        <div class="accordion" id="freshAccordion">
            {{-- برنامه‌های فعال --}}
            <div class="accordion-item shadow-sm mb-3 border-0">
                <h2 class="accordion-header" id="headingPrograms">
                    <button class="accordion-button fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePrograms" aria-expanded="true" aria-controls="collapsePrograms">
                        <i class="bi bi-calendar-event ms-2 text-primary"></i> برنامه‌های در دست اجرا
                    </button>
                </h2>
                <div id="collapsePrograms" class="accordion-collapse collapse show" aria-labelledby="headingPrograms" data-bs-parent="#freshAccordion">
                    <div class="accordion-body">
                        <div class="row g-3">
                            @forelse($latestPrograms as $program)
                                <div class="col-12 col-md-6 col-xl-3">
                                    @php $programImage = optional($program->files->first())->file_path ? asset('storage/'.optional($program->files->first())->file_path) : $fallbackImage; @endphp
                                    <div class="content-card h-100">
                                        <div class="content-cover" style="background-image: linear-gradient(135deg, rgba(0,0,0,.45), rgba(0,0,0,.6)), url('{{ $programImage }}');"></div>
                                        <div class="content-body">
                                            <div class="d-flex justify-content-between text-muted small mb-1">
                                                <span class="d-inline-flex align-items-center gap-1"><i class="bi bi-calendar3"></i>{{ $program->execution_date ? toPersianNumber(jdate($program->execution_date)->format('Y/m/d')) : '-' }}</span>
                                                <span class="d-inline-flex align-items-center gap-1"><i class="bi bi-geo-alt"></i>{{ $program->region_name ?? 'مسیر ویژه' }}</span>
                                            </div>
                                            <h6 class="content-title">{{ $program->name ?? $program->title ?? 'برنامه باشگاه' }}</h6>
                                            <p class="text-muted small mb-2">ارتفاع قله: {{ toPersianNumber($program->peak_height ?? 0) }} متر</p>
                                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                                <span class="badge bg-success-subtle text-success d-inline-flex align-items-center gap-1"><i class="bi bi-cash-coin"></i>{{ toPersianNumber($program->cost_member ?? 0) }} تومان</span>
                                                <a href="{{ route('programs.show', $program->id) }}" class="btn btn-sm btn-outline-primary">ادامه</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="alert alert-info">برنامه‌ای ثبت نشده است.</div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- دوره‌ها --}}
            <div class="accordion-item shadow-sm mb-3 border-0">
                <h2 class="accordion-header" id="headingCourses">
                    <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCourses" aria-expanded="false" aria-controls="collapseCourses">
                        <i class="bi bi-mortarboard ms-2 text-warning"></i> دوره‌های آموزشی
                    </button>
                </h2>
                <div id="collapseCourses" class="accordion-collapse collapse" aria-labelledby="headingCourses" data-bs-parent="#freshAccordion">
                    <div class="accordion-body">
                        <div class="row g-3">
                            @forelse($latestCourses as $course)
                                <div class="col-12 col-md-6 col-xl-3">
                                    <div class="content-card content-card-plain h-100">
                                        <div class="content-body">
                                            <div class="d-flex justify-content-between text-muted small mb-1">
                                                <span class="d-inline-flex align-items-center gap-1"><i class="bi bi-calendar3"></i>{{ $course->start_date ? toPersianNumber(jdate($course->start_date)->format('Y/m/d')) : '-' }}</span>
                                                <span class="d-inline-flex align-items-center gap-1"><i class="bi bi-clock"></i>{{ $course->end_date ? toPersianNumber(jdate($course->end_date)->format('Y/m/d')) : '' }}</span>
                                            </div>
                                            <h6 class="content-title">{{ $course->title }}</h6>
                                            <p class="text-muted small mb-2 line-clamp-2">{{ \Illuminate\Support\Str::limit(strip_tags($course->description), 110, '...') }}</p>
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="badge bg-info-subtle text-info d-inline-flex align-items-center gap-1"><i class="bi bi-cash-coin"></i>{{ toPersianNumber($course->member_cost ?? 0) }} تومان</span>
                                                <span class="badge bg-light text-dark d-inline-flex align-items-center gap-1"><i class="bi bi-people"></i>{{ toPersianNumber($course->capacity ?? 0) }} نفر</span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                                <small class="text-muted d-inline-flex align-items-center gap-1"><i class="bi bi-geo-alt"></i>{{ $course->place ?? 'محل برگزاری متعاقباً' }}</small>
                                                <a href="{{ route('courses.show', $course->id) }}" class="btn btn-sm btn-outline-primary">ادامه</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="alert alert-info">دوره‌ای ثبت نشده است.</div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- گزارش برنامه‌ها --}}
            <div class="accordion-item shadow-sm mb-3 border-0">
                <h2 class="accordion-header" id="headingReports">
                    <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseReports" aria-expanded="false" aria-controls="collapseReports">
                        <i class="bi bi-journal-richtext ms-2 text-danger"></i> گزارش برنامه‌ها
                    </button>
                </h2>
                <div id="collapseReports" class="accordion-collapse collapse" aria-labelledby="headingReports" data-bs-parent="#freshAccordion">
                    <div class="accordion-body">
                        <div class="row g-3">
                            @forelse($latestReports as $report)
                                <div class="col-12 col-md-6 col-xl-3">
                                    @php
                                        $program = $report->program;
                                        $reportImage = $program && $program->files->first() ? asset('storage/'.$program->files->first()->file_path) : $fallbackImage;
                                        $reportLink = auth()->check()
                                            ? (auth()->user()->role === 'admin'
                                                ? route('admin.program_reports.show', $report->id)
                                                : route('dashboard.program_reports.show', $report->id))
                                            : route('auth.login');
                                    @endphp
                                    <div class="content-card h-100">
                                        <div class="content-cover" style="background-image: linear-gradient(135deg, rgba(0,0,0,.55), rgba(0,0,0,.65)), url('{{ $reportImage }}');"></div>
                                        <div class="content-body">
                                            <div class="d-flex justify-content-between text-muted small mb-1">
                                                <span class="d-inline-flex align-items-center gap-1"><i class="bi bi-calendar3"></i>{{ $report->report_date ? toPersianNumber(jdate($report->report_date)->format('Y/m/d')) : '-' }}</span>
                                                <span class="d-inline-flex align-items-center gap-1"><i class="bi bi-geo-alt"></i>{{ $report->report_region_route ?? ($program->region_name ?? 'مسیر کوهستانی') }}</span>
                                            </div>
                                            <h6 class="content-title">{{ $report->report_program_name ?? optional($report->program)->name ?? 'گزارش برنامه' }}</h6>
                                            <p class="text-muted small mb-2">سرپرست: {{ $report->leader_name ?? 'نامشخص' }}</p>
                                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                                <span class="badge bg-light text-dark d-inline-flex align-items-center gap-1"><i class="bi bi-people"></i>{{ toPersianNumber($report->participants_count ?? 0) }} نفر</span>
                                                <a href="{{ $reportLink }}" class="btn btn-sm btn-outline-primary">ادامه</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12"><div class="alert alert-info">گزارشی ثبت نشده است.</div></div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- پست‌های وبلاگ --}}
            <div class="accordion-item shadow-sm mb-3 border-0">
                <h2 class="accordion-header" id="headingPosts">
                    <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePosts" aria-expanded="false" aria-controls="collapsePosts">
                        <i class="bi bi-pencil-square ms-2 text-info"></i> تازه‌ترین پست‌های وبلاگ
                    </button>
                </h2>
                <div id="collapsePosts" class="accordion-collapse collapse" aria-labelledby="headingPosts" data-bs-parent="#freshAccordion">
                    <div class="accordion-body">
                        <div class="row g-3">
                            @forelse($latestPosts as $post)
                                <div class="col-12 col-md-6 col-xl-3">
                                    @php $postImage = $post->featured_image ? asset('storage/'.$post->featured_image) : $fallbackImage; @endphp
                                    <div class="content-card h-100">
                                        <div class="content-cover" style="background-image: linear-gradient(135deg, rgba(0,0,0,.5), rgba(0,0,0,.65)), url('{{ $postImage }}');"></div>
                                        <div class="content-body">
                                            <div class="d-flex justify-content-between text-muted small mb-1">
                                                <span class="d-inline-flex align-items-center gap-1"><i class="bi bi-calendar3"></i>{{ $post->published_at ? toPersianNumber($post->published_at->format('Y/m/d')) : '-' }}</span>
                                                <span class="d-inline-flex align-items-center gap-1"><i class="bi bi-eye"></i>{{ toPersianNumber($post->view_count) }}</span>
                                            </div>
                                            <h6 class="content-title">{{ $post->title }}</h6>
                                            <p class="text-muted small mb-2 line-clamp-2">{{ Str::limit(strip_tags($post->excerpt ?: $post->content), 110) }}</p>
                                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                                <span class="badge bg-primary-subtle text-primary">وبلاگ</span>
                                                <a href="{{ route('blog.show', $post->slug) }}" class="btn btn-sm btn-outline-primary">ادامه</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12"><div class="alert alert-info">پستی ثبت نشده است.</div></div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- بخش اعتماد --}}
<section class="py-5 bg-gradient position-relative text-white" style="background: radial-gradient(circle at 20% 20%, rgba(13,110,253,.35), transparent 35%), radial-gradient(circle at 80% 0%, rgba(25,135,84,.35), transparent 30%), #0b172a;">
    <div class="container">
        <div class="row g-4 align-items-center">
            <div class="col-lg-7">
                <h3 class="fw-bold mb-3">همراهی ایمن در مسیر قله‌ها</h3>
                <p class="mb-4" style="line-height: 1.9;">تیم‌های آموزشی، مربیان مجرب، پشتیبانی پزشکی و برنامه‌ریزی دقیق ما، تجربه‌ای ایمن و الهام‌بخش را برای اعضا رقم می‌زند. برای پیوستن کافیست ثبت‌نام کنید و قدم در مسیر یادگیری و ماجراجویی بگذارید.</p>
                <div class="d-flex flex-wrap gap-3">
                    <div class="check-chip"><i class="bi bi-check-circle"></i> مربیان دارای کارت فدراسیون</div>
                    <div class="check-chip"><i class="bi bi-check-circle"></i> بیمه و اورژانس کوهستان</div>
                    <div class="check-chip"><i class="bi bi-check-circle"></i> آموزش مقدماتی تا پیشرفته</div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card bg-white text-dark border-0 shadow-lg">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">قدم بعدی را بردارید</h5>
                        <div class="d-flex flex-column gap-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="icon-circle bg-primary text-white"><i class="bi bi-person-plus"></i></div>
                                <div>
                                    <div class="fw-bold">عضویت سریع</div>
                                    <small class="text-muted">ثبت‌نام آنلاین و دریافت کد عضویت.</small>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <div class="icon-circle bg-success text-white"><i class="bi bi-book"></i></div>
                                <div>
                                    <div class="fw-bold">انتخاب دوره</div>
                                    <small class="text-muted">از دوره‌های پایه تا کارگاه‌های تخصصی.</small>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <div class="icon-circle bg-warning text-white"><i class="bi bi-mountain"></i></div>
                                <div>
                                    <div class="fw-bold">شرکت در برنامه</div>
                                    <small class="text-muted">همراهی با تیم‌های مجرب در مسیرهای ایمن.</small>
                                </div>
                            </div>
                        </div>
                        <div class="d-grid gap-2 mt-4">
                            <a href="{{ route('auth.phone') }}" class="btn btn-primary">ثبت‌نام عضو جدید</a>
                            <a href="{{ route('programs.archive') }}" class="btn btn-outline-primary">مشاهده برنامه‌ها</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


@push('scripts')
<script>
 document.addEventListener("DOMContentLoaded", function () {
    const slides = document.querySelectorAll('.carousel-slide');
    let currentSlide = 0;
    
    function showSlide(index) {
        slides.forEach(slide => slide.classList.remove('active'));
        slides[index].classList.add('active');
    }
    
    function nextSlide() {
        currentSlide = (currentSlide + 1) % slides.length;
        showSlide(currentSlide);
    }
    
    // شروع اسلایدر
    showSlide(currentSlide);
    setInterval(nextSlide, 6000);
});
</script>

@endpush

@push('styles')
<style>
.hero-slider { position: relative; width: 100%; height: 100vh; overflow: hidden; }
.carousel-container { position: relative; width: 100%; height: 100%; }
.carousel-slide { position: absolute; inset: 0; width: 100%; height: 100%; opacity: 0; transition: opacity 1s ease-in-out; z-index: 1; }
.carousel-slide.active { opacity: 1; z-index: 2; }
.slide-image { width: 100%; height: 100%; object-fit: cover; object-position: center; transform: scale(1.05); transition: transform 8s ease-in-out; }
.carousel-slide.active .slide-image { transform: scale(1.15); }
.hero-overlay { position: absolute; inset: 0; background: rgba(0,0,0,0.5); z-index: 3; }
.hero-content { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 4; width: 90%; max-width: 1200px; text-align: center; color: #fff; }

.mini-card { background:#fff; border-radius: 12px; padding:16px; box-shadow:0 10px 30px rgba(0,0,0,0.05); text-align:center; height:100%; }

.timeline { position: relative; padding-right: 25px; border-right: 2px solid rgba(13,110,253,0.2); }
.timeline-item { position: relative; padding-bottom: 18px; }
.timeline-year { font-weight:700; color:#0d6efd; margin-bottom:4px; }
.timeline-dot { position:absolute; right:-10px; top:6px; width:12px; height:12px; background:#0d6efd; border-radius:50%; box-shadow:0 0 0 6px rgba(13,110,253,0.1); }
.timeline-card { background:#fff; border-radius:10px; padding:12px 14px; }

.content-card { background:#fff; border-radius:14px; overflow:hidden; box-shadow:0 15px 35px rgba(0,0,0,0.08); display:flex; flex-direction:column; transition:transform .25s ease, box-shadow .25s ease; min-height:100%; }
.content-card:hover { transform: translateY(-6px); box-shadow:0 18px 40px rgba(0,0,0,0.12); }
.content-card-plain { border: 1px solid #eef1f5; box-shadow:0 8px 22px rgba(0,0,0,0.05); }
.content-cover { height: 170px; background-size: cover; background-position: center; }
.content-body { padding:14px; display:flex; flex-direction:column; height:100%; }
.content-title { font-weight:700; font-size:16px; margin:10px 0 12px; line-height:1.6; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; min-height:48px; }
.line-clamp-2 { display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
.line-clamp-3 { display:-webkit-box; -webkit-line-clamp:3; -webkit-box-orient:vertical; overflow:hidden; }

.check-chip { background: rgba(255,255,255,0.12); color:#fff; padding:10px 12px; border-radius:12px; display:inline-flex; align-items:center; gap:6px; font-size:14px; }
.icon-circle { width:44px; height:44px; border-radius:50%; display:inline-flex; align-items:center; justify-content:center; font-size:20px; }

.scenic-section { background-size: cover; background-position: center; }
.chip-light { background: rgba(255,255,255,0.14); border:1px solid rgba(255,255,255,0.2); color:#fff; padding:10px 14px; border-radius:999px; font-size:14px; display:inline-flex; align-items:center; gap:6px; }
.mini-visual { border-radius:14px; height:140px; background-size:cover; background-position:center; padding:14px; color:#fff; display:flex; align-items:flex-end; box-shadow:0 18px 38px rgba(0,0,0,0.12); }
.visual-card { border-radius:16px; height:220px; background-size:cover; background-position:center; position:relative; overflow:hidden; box-shadow:0 20px 40px rgba(0,0,0,0.12); }
.visual-content { position:absolute; bottom:14px; right:14px; left:14px; color:#fff; }
.feedback-card { background:#fff; border-radius:14px; padding:16px; box-shadow:0 15px 30px rgba(0,0,0,0.07); }
.avatar-placeholder { width:44px; height:44px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:700; }

.badge i, .content-body i, .chip-light i, .check-chip i { margin-left:6px; }

@media (max-width: 768px) {
    .hero-slider { height: 80vh; }
    .hero-content { padding: 0 20px; }
    .slide-image { object-position: center center; }
    .content-cover { height: 170px; }
    .timeline { border: none; padding-right: 0; }
    .timeline-dot { display: none; }
    .timeline-year { color: #0d6efd; }
}
</style>

@endpush
@endsection
