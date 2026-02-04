{{-- 503 Service Unavailable error page. --}}
@extends('errors.layout')

@section('code', '۵۰۳')
@section('title', 'سرویس موقتا در دسترس نیست')
@section('message', 'برای لحظاتی در حال به‌روزرسانی یا انجام نگهداری سیستم هستیم. کمی بعد دوباره سر بزنید. بابت این وقفه پوزش می‌خواهیم.')
@section('icon')
<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16">
  <path d="M7 1a1 1 0 0 0-1 1v1H4.5A2.5 2.5 0 0 0 2 5.5v5A2.5 2.5 0 0 0 4.5 13H6v1a1 1 0 0 0 1.707.707l3.586-3.586A2 2 0 0 0 12 9.586V2a1 1 0 0 0-1-1H7z"/>
</svg>
@endsection
