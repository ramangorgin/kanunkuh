@extends('admin.layout')

@section('title', 'ویرایش پست')

@section('content')
<div class="container-fluid py-4 animate__animated animate__fadeIn">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1"><i class="bi bi-pencil-square text-primary me-2"></i> ویرایش پست</h4>
            <p class="text-muted mb-0">ویرایش و بروزرسانی جزئیات پست</p>
        </div>
        <a href="{{ route('admin.posts.index') }}" class="btn btn-light border"><i class="bi bi-arrow-right"></i> بازگشت</a>
    </div>

    <form action="{{ route('admin.posts.update', $post) }}" method="POST" enctype="multipart/form-data">
        @method('PUT')
        @include('admin.posts._form')
    </form>
</div>
@endsection
