@extends('errors.layout')

@section('code', '۴۱۹')
@section('title', 'نشست شما منقضی شد')
@section('message', 'زمان اعتبار نشست شما به پایان رسیده است. لطفا صفحه را تازه‌سازی کنید و دوباره تلاش کنید. اگر فرم را پر کرده بودید، ممکن است نیاز باشد دوباره ارسال کنید.')
@section('icon')
<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16">
  <path d="M8 3.5a.5.5 0 0 1 .5.5v4H11a.5.5 0 0 1 0 1H7.5a.5.5 0 0 1-.5-.5V4a.5.5 0 0 1 .5-.5z"/>
  <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm0-1A7 7 0 1 1 8 1a7 7 0 0 1 0 14z"/>
</svg>
@endsection
@section('actions')
<a href="{{ url()->current() }}" class="btn btn-primary">تازه‌سازی صفحه</a>
@endsection
