{{-- Contact page and form view. --}}
@extends('layout')

@section('title', 'تماس با کانون کوه')

@section('content')
<section class="contact-hero text-white position-relative">
    <div class="overlay"></div>
    <div class="container position-relative py-5">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <h1 class="fw-bold mb-3">با ما در ارتباط باشید</h1>
                <p class="text-white-75 mb-4">سؤالات درباره برنامه‌ها، عضویت یا همکاری دارید؟ تیم ما آماده پاسخ‌گویی است. می‌توانید فرم زیر را پر کنید یا از راه‌های تماس مستقیم استفاده کنید.</p>
                <div class="d-flex flex-wrap gap-3">
                    <span class="chip"><i class="bi bi-telegram me-1"></i>t.me/kanoonkooh</span>
                    <span class="chip"><i class="bi bi-instagram me-1"></i>@kanoonkooh</span>
                    <span class="chip"><i class="bi bi-envelope me-1"></i>info@kanoonkooh.ir</span>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="contact-card shadow-lg bg-white text-dark">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-circle bg-primary text-white me-3"><i class="bi bi-geo-alt"></i></div>
                        <div>
                            <div class="fw-bold">نشانی دفتر</div>
                            <small class="text-muted">کرج، گلشهر، بلوار گلزار غربی، خیابان یاس، ساختمان سینا، طبقه سوم، واحد شش</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-circle bg-success text-white me-3"><i class="bi bi-telephone"></i></div>
                        <div>
                            <div class="fw-bold">تلفن</div>
                            <small class="text-muted">۰۲۶۳۳۵۰۸۰۱۸</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="icon-circle bg-info text-white me-3"><i class="bi bi-envelope"></i></div>
                        <div>
                            <div class="fw-bold">ایمیل</div>
                            <small class="text-muted">info@kanoonkooh.ir</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-3">فرم تماس</h4>
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        <form method="POST" action="{{ route('contact.submit') }}" class="row g-3">
                            @csrf
                            <div class="col-12 col-md-6">
                                <label class="form-label">نام و نام خانوادگی</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                                @error('name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">ایمیل</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                                @error('email')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">شماره تماس</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                                @error('phone')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">موضوع</label>
                                <input type="text" name="subject" class="form-control" value="{{ old('subject') }}">
                                @error('subject')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">متن پیام</label>
                                <textarea name="message" class="form-control" rows="5" required>{{ old('message') }}</textarea>
                                @error('message')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12 d-grid">
                                <button type="submit" class="btn btn-primary py-3">ارسال پیام</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-0">
                        <div id="contact-map" style="height: 400px; width: 100%;"></div>
                        <div class="p-4">
                            <h6 class="fw-bold mb-2">زمان پاسخ‌گویی</h6>
                            <p class="text-muted mb-1">شنبه تا چهارشنبه: ۹ تا ۱۸</p>
                            <p class="text-muted mb-0">پنجشنبه: ۹ تا ۱۴</p>
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
.contact-hero { background-image: linear-gradient(135deg, rgba(0,0,0,.55), rgba(0,0,0,.7)), url('{{ asset('images/contact/hero.jpg') }}'); background-size: cover; background-position: center; }
.contact-hero .overlay { position:absolute; inset:0; background: linear-gradient(180deg, rgba(0,0,0,.45), rgba(0,0,0,.65)); }
.chip { background: rgba(255,255,255,0.14); border:1px solid rgba(255,255,255,0.2); color:#fff; padding:10px 14px; border-radius:999px; font-size:14px; display:inline-flex; align-items:center; gap:6px; }
.contact-card { border-radius:14px; padding:20px; }
.icon-circle { width:48px; height:48px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:20px; }
@media (max-width: 768px) {
    .contact-card { margin-top: 12px; }
}
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (!window.L) return;
        const map = L.map('contact-map').setView([35.8232941, 50.9331318], 16);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);
        L.marker([35.8232941, 50.9331318]).addTo(map).bindPopup('باشگاه کانون کوه');
    });
</script>
@endpush
