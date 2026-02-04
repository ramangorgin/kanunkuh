{{-- Admin posts list view. --}}
@extends('admin.layout')

@section('title', 'مدیریت پست‌ها')

@section('content')
<div class="container-fluid py-4 animate__animated animate__fadeIn">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1"><i class="bi bi-journal-text text-primary me-2"></i> پست‌ها</h4>
            <p class="text-muted mb-0">ایجاد، ویرایش و مدیریت پست‌های وبلاگ</p>
        </div>
        <a href="{{ route('admin.posts.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> پست جدید</a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-hover align-middle text-center">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>عنوان</th>
                        <th>دسته‌ها</th>
                        <th>وضعیت</th>
                        <th>تاریخ انتشار</th>
                        <th>بازدید</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($posts as $post)
                        <tr>
                            <td>{{ toPersianNumber($post->id) }}</td>
                            <td class="text-start">{{ $post->title }}</td>
                            <td>
                                @foreach($post->categories as $cat)
                                    <span class="badge bg-light text-dark border">{{ $cat->name }}</span>
                                @endforeach
                            </td>
                            <td>
                                @if($post->status === 'published')
                                    <span class="badge bg-success">منتشر شده</span>
                                @else
                                    <span class="badge bg-secondary">پیش‌نویس</span>
                                @endif
                            </td>
                            <td>{{ $post->published_at ? toPersianNumber($post->published_at->format('Y/m/d H:i')) : '-' }}</td>
                            <td>{{ toPersianNumber($post->view_count) }}</td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('admin.posts.edit', $post) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                    @if($post->status !== 'published')
                                        <form action="{{ route('admin.posts.publish', $post) }}" method="POST" onsubmit="return confirm('پست منتشر شود؟');">
                                            @csrf
                                            <button class="btn btn-sm btn-success" type="submit"><i class="bi bi-check2-circle"></i></button>
                                        </form>
                                    @endif
                                    <form action="{{ route('admin.posts.destroy', $post) }}" method="POST" onsubmit="return confirm('حذف این پست قطعی است؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" type="submit"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-muted">پستی ثبت نشده است.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-3">
                {{ $posts->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
