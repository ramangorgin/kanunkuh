{{-- About page content view. --}}
@extends('layout')

@section('title', 'درباره کانون کوه')

@section('content')
<section class="about-hero position-relative text-white">
    <div class="overlay"></div>
    <div class="container position-relative">
        <div class="row align-items-center py-5">
            <div class="col-lg-7">
                <h1 class="fw-bold mb-3">درباره باشگاه کوهنوردی دوستداران قله‌ها و طبیعت</h1>
                <p class="lead mb-4">از هسته‌ای کوچک در سال‌های ۱۳۸۹ تا باشگاهی پویا که امروز با آموزش، برنامه‌ریزی حرفه‌ای و فرهنگ صعود ایمن شناخته می‌شود.</p>
                <div class="d-flex flex-wrap gap-3">
                    <span class="chip"><i class="bi bi-mountain me-1"></i>صعود مسئولانه</span>
                    <span class="chip"><i class="bi bi-shield-check me-1"></i>ایمنی و سلامت</span>
                    <span class="chip"><i class="bi bi-people me-1"></i>جامعه صمیمی</span>
                </div>
            </div>
            <div class="col-lg-5 d-none d-lg-block text-end">
                <div class="hero-card shadow-lg">
                    <h5 class="fw-bold mb-3">چرا کانون کوه؟</h5>
                    <ul class="list-unstyled text-white-50 mb-0 small">
                        <li class="mb-2"><i class="bi bi-check2-circle text-success me-2"></i>مربیان دارای کارت فدراسیون و تجربه هیمالیانوردی</li>
                        <li class="mb-2"><i class="bi bi-check2-circle text-success me-2"></i>برنامه‌ریزی دقیق، پوشش بیمه و پشتیبانی پزشکی</li>
                        <li class="mb-2"><i class="bi bi-check2-circle text-success me-2"></i>فرهنگ محیط‌زیستی و صعود پاک</li>
                        <li><i class="bi bi-check2-circle text-success me-2"></i>زیرساخت دیجیتال برای ارتباط و ثبت‌نام سریع</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-white">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-icon bg-primary text-white"><i class="bi bi-people"></i></div>
                    <div class="stat-value">۳۵۰۰+</div>
                    <div class="stat-label">صعود گروهی</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-icon bg-success text-white"><i class="bi bi-mortarboard"></i></div>
                    <div class="stat-value">۱۸۰+</div>
                    <div class="stat-label">دوره آموزشی</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-icon bg-warning text-white"><i class="bi bi-hospital"></i></div>
                    <div class="stat-value">پشتیبانی</div>
                    <div class="stat-label">پزشکی و امداد</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-icon bg-info text-white"><i class="bi bi-globe2"></i></div>
                    <div class="stat-value">۲ قله</div>
                    <div class="stat-label">اکسپدیشن بین‌المللی</div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-5">
                <h3 class="fw-bold mb-3">روایت رشد و یادگیری</h3>
                <p class="text-secondary">مسیر ما از جمعی دوستانه آغاز شد و با ایجاد کارگروه‌های فنی، آموزشی و محیط‌زیست، امروز میزبان نسل‌های تازه‌ای از کوهنوردان هستیم.</p>
                <div class="d-flex flex-column gap-3 mt-3">
                    <div class="d-flex align-items-start gap-3">
                        <div class="badge bg-primary text-white rounded-pill px-3 py-2">۱۳۸۹</div>
                        <div>
                            <div class="fw-bold">شروع هسته اولیه</div>
                            <small class="text-muted">صعودهای تمرینی و تشکیل هیئت مؤسس.</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-start gap-3">
                        <div class="badge bg-success text-white rounded-pill px-3 py-2">۱۳۹۶</div>
                        <div>
                            <div class="fw-bold">مجوز رسمی باشگاه</div>
                            <small class="text-muted">ثبت موسسه و آغاز فعالیت رسمی.</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-start gap-3">
                        <div class="badge bg-warning text-dark rounded-pill px-3 py-2">۱۳۹۷</div>
                        <div>
                            <div class="fw-bold">صعود کارل مارکس</div>
                            <small class="text-muted">ثبت نخستین صعود ایرانی این قله.</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="story-card" style="background-image: linear-gradient(135deg, rgba(0,0,0,.55), rgba(0,0,0,.7)), url('{{ asset('images/about/story.jpg') }}');">
                    <div class="text-white">
                        <h5 class="fw-bold mb-2">باشگاه امروز</h5>
                        <p class="mb-0 text-white-75">کلاس‌های منظم، اردوی ارتفاع، کارگاه‌های یخ‌نوردی، و تیمی که هر هفته مسیرهای تازه را تجربه می‌کند. ما به صعود پاک و حفاظت از محیط‌زیست پایبندیم.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-white">
    <div class="container">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start mb-4">
            <div>
                <h3 class="fw-bold mb-2">تیم مربیان و راهنمایان</h3>
                <p class="text-secondary mb-0">شناخت، تجربه و استانداردهای فدراسیون؛ همراهی امن در کوهستان.</p>
            </div>
            <a href="{{ route('programs.archive') }}" class="btn btn-outline-primary">مشاهده برنامه‌ها</a>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="team-card">
                    <div class="team-avatar" style="background-image: url('{{ asset('images/about/coach1.jpg') }}');"></div>
                    <div class="p-3">
                        <h6 class="fw-bold mb-1">مریم بهرامی</h6>
                        <small class="text-muted">مربی سنگ‌نوردی و کارگاه‌های ایمنی</small>
                        <div class="d-flex flex-wrap gap-2 mt-3">
                            <span class="badge bg-light text-dark">امداد کوهستان</span>
                            <span class="badge bg-light text-dark">آموزش پایه</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="team-card">
                    <div class="team-avatar" style="background-image: url('{{ asset('images/about/coach2.jpg') }}');"></div>
                    <div class="p-3">
                        <h6 class="fw-bold mb-1">سامان نیک‌پی</h6>
                        <small class="text-muted">سرپرست برنامه‌های ارتفاع و زمستان</small>
                        <div class="d-flex flex-wrap gap-2 mt-3">
                            <span class="badge bg-light text-dark">راهنمایی فدراسیون</span>
                            <span class="badge bg-light text-dark">بهمن‌شناسی</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="team-card">
                    <div class="team-avatar" style="background-image: url('{{ asset('images/about/coach3.jpg') }}');"></div>
                    <div class="p-3">
                        <h6 class="fw-bold mb-1">الهه کرمی</h6>
                        <small class="text-muted">مربی یخ و برف، سرپرست بانوان</small>
                        <div class="d-flex flex-wrap gap-2 mt-3">
                            <span class="badge bg-light text-dark">یخ‌نوردی</span>
                            <span class="badge bg-light text-dark">صعود پاک</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-gradient text-white position-relative" style="background: radial-gradient(circle at 20% 20%, rgba(13,110,253,.35), transparent 35%), radial-gradient(circle at 80% 0%, rgba(25,135,84,.35), transparent 30%), #0b172a;">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <h3 class="fw-bold mb-3">به ما بپیوندید</h3>
                <p class="text-white-75 mb-4" style="line-height: 1.9;">ثبت‌نام در باشگاه، دسترسی به کارگاه‌ها، اردوهای آموزشی، برنامه‌های فنی و جامعه‌ای که در آن رشد می‌کنید. از مسیرهای کلاسیک تا قله‌های کمتر شناخته‌شده همراهتان هستیم.</p>
                <div class="d-flex flex-wrap gap-3">
                    <div class="chip chip-dark"><i class="bi bi-emoji-smile me-1"></i>جامعه صمیمی</div>
                    <div class="chip chip-dark"><i class="bi bi-card-checklist me-1"></i>برنامه‌های منظم</div>
                    <div class="chip chip-dark"><i class="bi bi-heart-pulse me-1"></i>پزشک و ایمنی</div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card bg-white text-dark border-0 shadow-lg">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">اولین قدم</h5>
                        <p class="text-muted mb-4">ثبت‌نام آنلاین و انتخاب مسیر یادگیری.</p>
                        <div class="d-grid gap-2">
                            <a href="{{ route('auth.phone') }}" class="btn btn-primary">ثبت‌نام عضو جدید</a>
                            <a href="{{ route('courses.archive') }}" class="btn btn-outline-primary">دوره‌های آموزشی</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
.about-hero {
    background-image: linear-gradient(135deg, rgba(0,0,0,.55), rgba(0,0,0,.7)), url('{{ asset('images/about/hero.jpg') }}');
    background-size: cover;
    background-position: center;
    min-height: 60vh;
}
.about-hero .overlay { position:absolute; inset:0; background: linear-gradient(180deg, rgba(0,0,0,.45), rgba(0,0,0,.6)); }
.chip { background: rgba(255,255,255,0.16); border: 1px solid rgba(255,255,255,0.2); color:#fff; padding: 10px 14px; border-radius: 999px; font-size: 14px; display: inline-flex; align-items: center; gap:6px; }
.hero-card { backdrop-filter: blur(4px); background: rgba(255,255,255,0.08); border-radius: 12px; padding: 20px; }
.stat-card { background:#f8f9fb; border-radius:12px; padding:18px; text-align:center; box-shadow:0 20px 40px rgba(0,0,0,0.06); }
.stat-icon { width:48px; height:48px; border-radius:14px; display:flex; align-items:center; justify-content:center; font-size:20px; margin:0 auto 10px; }
.stat-value { font-size:22px; font-weight:700; }
.stat-label { color:#6c757d; font-size:14px; }
.story-card { border-radius:16px; padding:28px; min-height:240px; background-size:cover; background-position:center; box-shadow:0 25px 45px rgba(0,0,0,0.2); }
.team-card { background:#fff; border-radius:14px; overflow:hidden; box-shadow:0 18px 38px rgba(0,0,0,0.08); height:100%; display:flex; flex-direction:column; }
.team-avatar { height:190px; background-size:cover; background-position:center; }
.chip-dark { background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.2); }
@media (max-width: 768px) {
    .about-hero { min-height: 70vh; }
    .hero-card { display:none; }
}
</style>
@endpush
