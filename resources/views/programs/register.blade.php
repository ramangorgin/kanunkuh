{{-- Program registration form view. --}}
@extends('layout')

@section('title', 'ثبت‌نام در برنامه: ' . $program->name)

@push('styles')
<style>
    .registration-container {
        max-width: 900px;
        margin: 0 auto;
    }
    
    .payment-instruction-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 30px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .payment-instruction-card h4 {
        color: white;
        margin-bottom: 20px;
        font-weight: 600;
    }
    
    .payment-info-box {
        background: rgba(255,255,255,0.15);
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        backdrop-filter: blur(10px);
    }
    
    .payment-info-box strong {
        display: block;
        margin-bottom: 5px;
        font-size: 0.9rem;
        opacity: 0.9;
    }
    
    .payment-info-box .value {
        font-size: 1.2rem;
        font-weight: 700;
        letter-spacing: 1px;
    }
    
    .transaction-code-display {
        background: rgba(255,255,255,0.25);
        border: 2px dashed rgba(255,255,255,0.5);
        border-radius: 8px;
        padding: 20px;
        text-align: center;
        margin: 20px 0;
    }
    
    .transaction-code-display .code {
        font-size: 2rem;
        font-weight: 700;
        letter-spacing: 3px;
        font-family: 'Courier New', monospace;
    }
    
    .step-indicator {
        display: flex;
        justify-content: space-between;
        margin-bottom: 30px;
        position: relative;
    }
    
    .step-indicator::before {
        content: '';
        position: absolute;
        top: 20px;
        right: 0;
        left: 0;
        height: 2px;
        background: #dee2e6;
        z-index: 0;
    }
    
    .step {
        background: white;
        border: 3px solid #dee2e6;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        position: relative;
        z-index: 1;
    }
    
    .step.active {
        border-color: #667eea;
        background: #667eea;
        color: white;
    }
    
    .step.completed {
        border-color: #28a745;
        background: #28a745;
        color: white;
    }
    
    .form-section {
        background: white;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    
    .form-section h5 {
        color: #495057;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #e9ecef;
    }
</style>
@endpush

@section('content')
<div class="container my-5">
    <div class="registration-container">
        <div class="text-center mb-4">
            <h2 class="mb-2">
                <i class="bi bi-pencil-square text-primary me-2"></i>
                ثبت‌نام در برنامه
            </h2>
            <h4 class="text-muted">{{ $program->name }}</h4>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                {{ session('error') }}
            </div>
        @endif

        {{-- Step Indicator --}}
        <div class="step-indicator">
            <div class="step @if(!$isFree) active @else completed @endif">
                <i class="bi bi-1-circle"></i>
            </div>
            <div class="step @if(!$isFree) @else active @endif">
                <i class="bi bi-2-circle"></i>
            </div>
        </div>

        {{-- Payment Instructions (if not free) --}}
        @if(!$isFree)
        <div class="payment-instruction-card">
            <h4><i class="bi bi-info-circle me-2"></i> دستورالعمل پرداخت</h4>
            
            <div class="payment-info-box">
                <strong>مبلغ قابل پرداخت:</strong>
                <span class="value">{{ number_format($amount) }} ریال</span>
            </div>
            
            <div class="payment-info-box">
                <strong>شناسه عضویت / مهمان:</strong>
                <span class="value">{{ $membershipCode }}</span>
            </div>
            
            <div class="transaction-code-display">
                <strong style="display: block; margin-bottom: 10px; font-size: 0.9rem;">کد پیگیری پرداخت (این کد را در قسمت شناسه واریز بانک خود وارد کنید):</strong>
                <div class="code" id="transaction-code">{{ $transactionCode }}</div>
                <button type="button" class="btn btn-light btn-sm mt-3" onclick="copyTransactionCode()">
                    <i class="bi bi-clipboard me-1"></i> کپی کد
                </button>
            </div>
            
            @if(!empty($paymentInfo))
            <div class="mt-4" style="background: rgba(255,255,255,0.1); border-radius: 8px; padding: 15px;">
                <h6 class="mb-3"><i class="bi bi-bank me-2"></i> مشخصات حساب بانکی:</h6>
                <div class="row g-2">
                    @if(!empty($paymentInfo['card_number']))
                    <div class="col-md-6">
                        <strong>شماره کارت:</strong> {{ $paymentInfo['card_number'] }}
                    </div>
                    @endif
                    @if(!empty($paymentInfo['sheba_number']))
                    <div class="col-md-6">
                        <strong>شماره شبا:</strong> {{ $paymentInfo['sheba_number'] }}
                    </div>
                    @endif
                    @if(!empty($paymentInfo['card_holder']))
                    <div class="col-md-6">
                        <strong>نام دارنده حساب:</strong> {{ $paymentInfo['card_holder'] }}
                    </div>
                    @endif
                    @if(!empty($paymentInfo['bank_name']))
                    <div class="col-md-6">
                        <strong>نام بانک:</strong> {{ $paymentInfo['bank_name'] }}
                    </div>
                    @endif
                </div>
            </div>
            @endif
            
            <div class="alert alert-light mt-4 mb-0" style="background: rgba(255,255,255,0.2); border: none;">
                <i class="bi bi-exclamation-circle me-2"></i>
                <strong>توجه:</strong> پس از واریز مبلغ، کد پیگیری پرداخت را در فرم زیر وارد کنید.
            </div>
        </div>
        @endif

        {{-- Registration Form --}}
        <form method="POST" action="{{ route('programs.register.store', $program->id) }}" id="registration-form">
            @csrf
            
            {{-- Hidden transaction code for free programs --}}
            @if($isFree)
                <input type="hidden" name="transaction_code" value="">
            @else
                <input type="hidden" name="transaction_code" value="{{ $transactionCode }}" id="hidden-transaction-code">
            @endif

            {{-- Guest Information (if not logged in) --}}
            @guest
            <div class="form-section">
                <h5><i class="bi bi-person me-2"></i> اطلاعات مهمان</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">نام و نام خانوادگی <span class="text-danger">*</span></label>
                        <input type="text" name="guest_name" class="form-control" value="{{ old('guest_name') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">شماره تماس <span class="text-danger">*</span></label>
                        <input type="text" name="guest_phone" class="form-control" value="{{ old('guest_phone') }}" required>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">کد ملی <span class="text-danger">*</span></label>
                        <input type="text" name="guest_national_id" class="form-control" value="{{ old('guest_national_id') }}" required>
                    </div>
                </div>
            </div>
            @endguest

            {{-- Transport Selection --}}
            @if($hasTransport)
            <div class="form-section">
                <h5><i class="bi bi-truck me-2"></i> انتخاب محل سوار شدن</h5>
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label">محل سوار شدن <span class="text-danger">*</span></label>
                        <select name="pickup_location" class="form-select" required>
                            <option value="">انتخاب کنید</option>
                            @if($program->move_from_tehran)
                            <option value="tehran" {{ old('pickup_location') == 'tehran' ? 'selected' : '' }}>
                                تهران
                            </option>
                            @endif
                            @if($program->move_from_karaj)
                            <option value="karaj" {{ old('pickup_location') == 'karaj' ? 'selected' : '' }}>
                                کرج
                            </option>
                            @endif
                        </select>
                    </div>
                </div>
            </div>
            @endif

            {{-- Payment Confirmation (if not free) --}}
            @if(!$isFree)
            <div class="form-section">
                <h5><i class="bi bi-check-circle me-2"></i> تأیید پرداخت</h5>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    پس از واریز مبلغ با کد پیگیری نمایش داده شده در بالا، فرم زیر را تأیید و ارسال کنید.
                </div>
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label">کد پیگیری پرداخت (10 رقم) <span class="text-danger">*</span></label>
                        <input type="text" 
                               name="transaction_code" 
                               id="transaction-code-input"
                               class="form-control text-center" 
                               value="{{ old('transaction_code', $transactionCode) }}" 
                               maxlength="10"
                               pattern="[0-9]{10}"
                               required
                               readonly
                               style="font-size: 1.2rem; letter-spacing: 2px; font-family: 'Courier New', monospace; background-color: #f8f9fa;"
                               title="این کد به صورت خودکار از کد نمایش داده شده در بالا پر شده است">
                        <small class="text-muted">کد پیگیری به صورت خودکار از کد نمایش داده شده در بالا پر شده است</small>
                    </div>
                </div>
            </div>
            @endif

            {{-- Submit Button --}}
            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary btn-lg px-5">
                    <i class="bi bi-check-circle me-2"></i>
                    @if($isFree)
                        ثبت‌نام
                    @else
                        تأیید و ثبت‌نام
                    @endif
                </button>
                <a href="{{ route('programs.show', $program->id) }}" class="btn btn-outline-secondary btn-lg px-5 ms-2">
                    <i class="bi bi-x-circle me-2"></i> انصراف
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function copyTransactionCode() {
        const code = document.getElementById('transaction-code').textContent.trim();
        navigator.clipboard.writeText(code).then(function() {
            // Show toast notification
            const btn = event.target.closest('button');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="bi bi-check-circle me-1"></i> کپی شد!';
            btn.classList.add('btn-success');
            btn.classList.remove('btn-light');
            
            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.classList.remove('btn-success');
                btn.classList.add('btn-light');
            }, 2000);
        });
    }

    // Restrict transaction code input to numbers only
    document.getElementById('transaction-code-input')?.addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '');
        if (this.value.length > 10) {
            this.value = this.value.substring(0, 10);
        }
    });

    // Form validation
    document.getElementById('registration-form')?.addEventListener('submit', function(e) {
        @if(!$isFree)
        const codeInput = document.getElementById('transaction-code-input');
        if (codeInput && codeInput.value.length !== 10) {
            e.preventDefault();
            alert('لطفاً کد پیگیری پرداخت را به درستی وارد کنید (10 رقم)');
            codeInput.focus();
            return false;
        }
        @endif
    });
</script>
@endpush

