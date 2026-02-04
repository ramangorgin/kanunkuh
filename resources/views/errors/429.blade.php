{{-- 429 Too Many Requests error page. --}}
@extends('errors.layout')

@section('code', '۴۲۹')
@section('title', 'درخواست‌های زیاد')
@section('message', 'در مدت کوتاهی درخواست‌های زیادی ارسال شده است. برای جلوگیری از اختلال، چند لحظه صبر کنید و دوباره تلاش کنید.')
@section('icon')
<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16">
  <path d="M8.515 2.291a1 1 0 0 0-1.03 0l-6 3.6A1 1 0 0 0 1 6.733v2.534a1 1 0 0 0 .485.842l6 3.6a1 1 0 0 0 1.03 0l6-3.6A1 1 0 0 0 15 9.267V6.733a1 1 0 0 0-.485-.842l-6-3.6z"/>
</svg>
@endsection
@section('actions')
<a href="{{ url()->previous() }}" class="btn btn-ghost">بازگشت</a>
<a href="{{ url()->current() }}" class="btn btn-primary">تلاش دوباره</a>
@endsection
