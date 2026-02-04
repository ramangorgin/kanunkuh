{{-- Membership terms and conditions page. --}}
@extends('layout')

@section('title', 'شرایط و قوانین عضویت')

@section('content')
<div class="bg-light py-5">
    <div class="container">
        <div class="row g-4 align-items-start">
            <div class="col-lg-5">
                <h2 class="fw-bold mb-3">شرایط و قوانین عضویت در کانون کوه</h2>
                <p class="text-secondary">برای پیوستن به جمع ماجراجویان، لطفاً موارد زیر را با دقت مطالعه کنید. این متن به‌صورت نمونه تولید شده و می‌توانید مطابق سیاست‌های نهایی باشگاه آن را ویرایش کنید.</p>
                <div class="mt-3">
                    <div class="chip"><i class="bi bi-shield-check"></i> ایمنی و مسئولیت</div>
                    <div class="chip"><i class="bi bi-heart"></i> روحیه تیمی</div>
                    <div class="chip"><i class="bi bi-flag"></i> پایبندی به مسیر</div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4 p-md-5">
                        <div class="mb-4">
                            <h5 class="fw-bold">شرایط عمومی</h5>
                            <ul class="list-unstyled ps-0 text-secondary" style="line-height: 1.9;">
                                <li class="mb-2"><i class="bi bi-check-circle text-success ms-2"></i> تکمیل اطلاعات هویتی و ارائه بیمه ورزشی معتبر سالانه.</li>
                                <li class="mb-2"><i class="bi bi-check-circle text-success ms-2"></i> حداقل سن ۱۸ سال؛ زیر ۱۸ سال با رضایت‌نامه والدین.</li>
                                <li class="mb-2"><i class="bi bi-check-circle text-success ms-2"></i> پرداخت حق عضویت و رعایت قوانین فدراسیون کوهنوردی.</li>
                                <li class="mb-2"><i class="bi bi-check-circle text-success ms-2"></i> شرکت در دوره‌های توجیهی ایمنی و محیط‌زیست باشگاه.</li>
                            </ul>
                        </div>
                        <div class="mb-4">
                            <h5 class="fw-bold">رفتار و مسئولیت</h5>
                            <p class="text-secondary mb-3">متن نمونه: اعضا متعهد می‌شوند با احترام، همکاری و مسئولیت‌پذیری در برنامه‌ها حاضر شوند و از هرگونه رفتار پرخطر یا غیرحرفه‌ای پرهیز کنند.</p>
                            <div class="d-flex flex-wrap gap-2">
                                <span class="badge bg-light text-dark border"><i class="bi bi-tree-fill ms-1 text-success"></i>حفاظت از طبیعت</span>
                                <span class="badge bg-light text-dark border"><i class="bi bi-people-fill ms-1 text-primary"></i>همراهی تیمی</span>
                                <span class="badge bg-light text-dark border"><i class="bi bi-thermometer-snow ms-1 text-info"></i>آمادگی تجهیزات</span>
                            </div>
                        </div>
                        <div>
                            <h5 class="fw-bold">لغو یا تعلیق عضویت</h5>
                            <p class="text-secondary">باشگاه در صورت نقض قوانین یا به‌خطر انداختن ایمنی گروه، می‌تواند عضویت را موقتاً تعلیق یا لغو کند. لطفاً برای نسخه نهایی، متن سیاست رسمی باشگاه را جایگزین این پاراگراف نمونه کنید.</p>
                            <div class="alert alert-warning mt-3 mb-0"><i class="bi bi-info-circle ms-1"></i> این متن به‌صورت «نمونه» تولید شده است؛ برای انتشار عمومی، محتوا را بازبینی و تایید حقوقی کنید.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 14px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.06);
        margin: 6px 6px 0 0;
        color: #0b172a;
    }

    .chip i { color: #0d6efd; }

    @media (max-width: 768px) {
        .chip { width: 100%; justify-content: flex-start; }
    }
</style>
@endpush
@endsection
