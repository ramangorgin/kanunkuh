@php use Illuminate\Support\Str; @endphp

@extends('layout')

@section('title', 'بلاگ')

@section('content')
<section class="py-5 bg-light">
    <div class="container">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4">
            <div>
                <h1 class="fw-bold mb-2">بلاگ کانون کوه</h1>
                <p class="text-muted mb-0">آخرین مقالات و اخبار را اینجا بخوانید.</p>
            </div>
        </div>

        <div class="row g-4">
            @forelse($posts as $post)
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm">
                        @if($post->featured_image)
                            <a href="{{ route('blog.show', $post->slug) }}">
                                <img src="{{ asset('storage/'.$post->featured_image) }}" class="card-img-top" alt="{{ $post->featured_image_alt }}" style="height: 210px; object-fit: cover;">
                            </a>
                        @endif
                        <div class="card-body d-flex flex-column">
                            <div class="mb-2 text-muted" style="font-size: 13px;">
                                {{ $post->published_at ? toPersianNumber($post->published_at->format('Y/m/d')) : '' }}
                                @if($post->reading_time)
                                    · {{ toPersianNumber($post->reading_time) }} دقیقه مطالعه
                                @endif
                            </div>
                            <h5 class="card-title fw-bold mb-2">
                                <a href="{{ route('blog.show', $post->slug) }}" class="text-decoration-none text-dark">{{ $post->title }}</a>
                            </h5>
                            <p class="text-muted flex-grow-1" style="line-height: 1.8;">
                                {{ $post->excerpt ? Str::limit($post->excerpt, 140) : Str::limit(strip_tags($post->content), 140) }}
                            </p>
                            <div class="d-flex flex-wrap gap-1 mb-3">
                                @foreach($post->categories as $cat)
                                    <span class="badge bg-light text-dark border">{{ $cat->name }}</span>
                                @endforeach
                            </div>
                            <a href="{{ route('blog.show', $post->slug) }}" class="btn btn-outline-primary mt-auto">
                                خواندن مطلب
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info text-center">هنوز مطلبی منتشر نشده است.</div>
                </div>
            @endforelse
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $posts->links() }}
        </div>
    </div>
</section>
@endsection
