{{-- Admin user edit view. --}}
@extends('admin.layout')

@section('title', 'ویرایش کاربر')

@section('content')
<div class="container py-4 animate__animated animate__fadeIn">
    <h4 class="fw-bold mb-4">
        <i class="bi bi-pencil-square text-secondary"></i> ویرایش اطلاعات کاربر
    </h4>

    <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="card p-4 shadow-sm border-0" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.users._form', ['user' => $user, 'jalali' => $jalali])

        <div class="mt-4 text-end">
            <button class="btn btn-success">بروزرسانی</button>
        </div>
    </form>
</div>

@endsection
