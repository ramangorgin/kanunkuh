{{-- User payments history page. --}}
@extends('user.layout')

@section('title', 'پرداخت‌های من')

@section('content')
<div class="container py-5" data-aos="fade-up">
    <h2 class="text-center mb-4"><i class="bi bi-wallet2 text-primary"></i> پرداخت‌های من</h2>

    {{-- فرم پرداخت جدید --}}
    <div class="card shadow-lg border-0 mb-5 animate__animated animate__fadeIn">
        <div class="card-body">
            <h5 class="card-title mb-4">
                <i class="bi bi-plus-circle text-success"></i> ثبت پرداخت جدید
            </h5>

            <form id="paymentForm" method="POST" action="{{ route('dashboard.payments.store') }}">
                @csrf
                <div class="row g-3 align-items-end">

                    {{-- موضوع پرداخت --}}
                    <div class="col-md-4">
                        <label class="form-label">موضوع پرداخت</label>
                        <select name="type" id="payment_type" class="form-select" required>
                            <option value="">انتخاب کنید...</option>
                            <option value="membership">حق عضویت سالانه</option>
                            <option value="program">ثبت‌نام در برنامه</option>
                            <option value="course">ثبت‌نام در دوره</option>
                        </select>
                    </div>

                    {{-- مبلغ --}}
                    <div class="col-md-4">
                        <label class="form-label">مبلغ (تومان)</label>
                        <input type="number" name="amount" class="form-control" placeholder="مثلاً 250000" required>
                    </div>

                    {{-- سال عضویت --}}
                    <div class="col-md-4 d-none" id="membership_year_wrapper">
                        <label class="form-label">سال عضویت</label>
                        <select name="year" id="membership_year" class="form-select">
                            <option value="">انتخاب سال</option>
                            @php
                                $currentYear = jdate()->getYear();
                            @endphp
                            @for($y = $currentYear - 5; $y <= $currentYear + 5; $y++)
                                <option value="{{ $y }}">{{ toPersianNumber($y) }}</option>
                            @endfor
                        </select>
                    </div>

                    {{-- انتخاب برنامه یا دوره --}}
                    <div class="col-md-6 d-none" id="related_item_wrapper">
                        <label id="related_item_label" class="form-label">انتخاب آیتم</label>
                        <select name="related_id" id="related_item" class="form-select selectpicker" data-live-search="true">
                            <option value="">انتخاب کنید...</option>
                        </select>
                    </div>

                    {{-- دکمه ثبت --}}
                    <div class="col-12 text-center mt-3">
                        <button type="submit" class="btn btn-primary px-5">
                            <i class="bi bi-check-circle"></i> ثبت پرداخت
                        </button>
                    </div>
                </div>
            </form>

            {{-- پیام موفقیت --}}
            @if(session('payment_success'))
                <div class="text-center mt-5 animate__animated animate__fadeInDown">
                    <div class="card border-success shadow p-4 mx-auto" style="max-width: 400px;">
                        <i class="bi bi-check-circle text-success display-4"></i>
                        <h5 class="mt-3 text-success">پرداخت با موفقیت ثبت شد</h5>
                        <p class="mb-1">شناسه عضویت: <strong>{{ session('payment_success')['membership_code'] }}</strong></p>
                        <p>شناسه واریز: <strong>{{ session('payment_success')['transaction_code'] }}</strong></p>
                        <p class="text-muted small mt-2">این شناسه‌ها را در قسمت شناسه واریز بانک خود وارد کنید.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- سوابق پرداخت‌ها --}}
    <div class="card shadow border-0 animate__animated animate__fadeInUp" data-aos="fade-up">
        <div class="card-body">
            <h5 class="card-title mb-4"><i class="bi bi-clock-history text-secondary"></i> سوابق پرداخت‌ها</h5>

            @if($payments->count())
                <div class="table-responsive">
                    <table class="table table-striped align-middle text-center">
                        <thead class="table-light">
                            <tr>
                                <th>شناسه عضویت</th>
                                <th>شناسه واریز</th>
                                <th>مبلغ (تومان)</th>
                                <th>نوع پرداخت</th>
                                <th>وضعیت</th>
                                <th>تاریخ ثبت</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payments as $payment)
                                <tr>
                                    <td>{{ $payment->membership_code ?? '-' }}</td>
                                    <td>{{ $payment->transaction_code ?? '-' }}</td>
                                    <td>{{ number_format($payment->amount) }}</td>
                                    <td>
                                        @switch($payment->type)
                                            @case('membership') حق عضویت @break
                                            @case('program') برنامه @break
                                            @case('course') دوره @break
                                        @endswitch
                                    </td>
                                    <td>
                                        @if($payment->approved)
                                            <span class="badge bg-success">تایید شده</span>
                                        @else
                                            <span class="badge bg-warning text-dark">در انتظار بررسی</span>
                                        @endif
                                    </td>
                                    <td>{{ toPersianNumber(jdate($payment->created_at)->format('Y/m/d')) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-center text-muted mt-3">هنوز پرداختی ثبت نکرده‌اید.</p>
            @endif
        </div>
    </div>
</div>


@push('scripts')

{{-- کنترل نمایش فیلدها --}}
<script>
document.addEventListener("DOMContentLoaded", function() {
    const paymentType = document.getElementById("payment_type");
    const membershipWrapper = document.getElementById("membership_year_wrapper");
    const relatedWrapper = document.getElementById("related_item_wrapper");
    const relatedLabel = document.getElementById("related_item_label");
    const relatedSelect = document.getElementById("related_item");

    paymentType.addEventListener("change", function() {
        const type = this.value;
        // پیش‌فرض همه پنهان
        membershipWrapper.classList.add("d-none");
        relatedWrapper.classList.add("d-none");

        if (type === "membership") {
            membershipWrapper.classList.remove("d-none");
        } 
        else if (type === "program" || type === "course") {
            relatedWrapper.classList.remove("d-none");
            relatedLabel.textContent = (type === "program") ? "انتخاب برنامه" : "انتخاب دوره";

            // پاک کردن قبلی‌ها
            relatedSelect.innerHTML = '<option value="">در حال بارگذاری...</option>';
            
            fetch(`/api/${type}s/list`)
                .then(response => {
                    if (!response.ok) throw new Error('خطا در دریافت داده‌ها');
                    return response.json();
                })
                .then(data => {
                    relatedSelect.innerHTML = '<option value="">انتخاب کنید...</option>';
                    data.forEach(item => {
                        const opt = document.createElement("option");
                        opt.value = item.id;
                        opt.textContent = item.name;
                        relatedSelect.appendChild(opt);
                    });
                    $('.selectpicker').selectpicker('refresh');
                })
                .catch(error => {
                    console.error(error);
                    relatedSelect.innerHTML = '<option value="">خطا در دریافت داده‌ها</option>';
                });
        }
    });
});
</script>
@endpush
@endsection
