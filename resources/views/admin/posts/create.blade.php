@extends('admin.layout')

@section('title', 'ایجاد پست جدید')

@section('content')
<div class="container-fluid py-4 animate__animated animate__fadeIn">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1"><i class="bi bi-plus-circle text-primary me-2"></i> پست جدید</h4>
            <p class="text-muted mb-0">ایجاد پست وبلاگ با تنظیمات کامل سئو</p>
        </div>
    </div>

    <form action="{{ route('admin.posts.store') }}" method="POST" enctype="multipart/form-data">
        @include('admin.posts._form')
    </form>
</div>
@endsection
