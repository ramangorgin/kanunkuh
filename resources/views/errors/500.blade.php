@extends('errors.layout')

@section('code', '۵۰۰')
@section('title', 'خطای داخلی سرور')
@section('message', 'یک خطای غیرمنتظره رخ داده است. تیم ما به سرعت موضوع را بررسی می‌کند. لطفا چند لحظه بعد دوباره تلاش کنید.')
@section('icon')
<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16">
  <path d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14z"/>
  <path fill="#fff" d="M7 4h2v5H7zM7 10h2v2H7z"/>
</svg>
@endsection
