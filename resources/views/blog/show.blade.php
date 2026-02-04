{{-- Blog post detail view. --}}
@php
    use Illuminate\Support\Str;
    $metaDescription = $post->seo_description ?? Str::limit(strip_tags($post->content), 155, '');
    $robots = ($post->is_indexable ? 'index' : 'noindex') . ', ' . ($post->is_followable ? 'follow' : 'nofollow');
@endphp

@extends('layout')

@section('title', $post->seo_title ?? $post->title)

@push('styles')
    <meta name="description" content="{{ $metaDescription }}">
    @if($post->seo_keywords)
        <meta name="keywords" content="{{ $post->seo_keywords }}">
    @endif
    <link rel="canonical" href="{{ $post->canonical_url }}">
    <meta name="robots" content="{{ $robots }}">
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "BlogPosting",
        "headline": "{{ $post->title }}",
        "name": "{{ $post->seo_title ?? $post->title }}",
        "description": "{{ $metaDescription }}",
        "url": "{{ $post->canonical_url }}",
        "datePublished": "{{ optional($post->published_at)->toIso8601String() }}",
        "dateModified": "{{ optional($post->updated_at)->toIso8601String() }}",
        "image": "{{ $post->featured_image ? asset('storage/' . $post->featured_image) : '' }}",
        "author": {
            "@type": "Person",
            "name": "{{ $post->user->name ?? 'مدیر' }}"
        },
        "mainEntityOfPage": {
            "@type": "WebPage",
            "@id": "{{ $post->canonical_url }}"
        }
    }
    </script>
@endpush

@section('content')
<section class="py-5 bg-light">
    <div class="container">
        <div class="row g-4 justify-content-center">
            <div class="col-lg-9">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4 p-md-5">
                        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-3 text-muted" style="font-size: 14px;">
                            <div class="mb-2 mb-md-0">
                                <i class="bi bi-calendar3 ms-1"></i>
                                {{ $post->published_at ? toPersianNumber($post->published_at->format('Y/m/d H:i')) : '' }}
                                @if($post->reading_time)
                                    <span class="mx-2">·</span>
                                    {{ toPersianNumber($post->reading_time) }} دقیقه مطالعه
                                @endif
                            </div>
                            <div class="d-flex flex-wrap gap-1">
                                @foreach($post->categories as $cat)
                                    <span class="badge bg-light text-dark border ms-1">{{ $cat->name }}</span>
                                @endforeach
                            </div>
                        </div>

                        <h1 class="fw-bold mb-3">{{ $post->title }}</h1>

                        @if($post->featured_image)
                            <figure class="mb-4 text-center">
                                <img src="{{ asset('storage/'.$post->featured_image) }}" alt="{{ $post->featured_image_alt }}" class="img-fluid rounded w-100" style="max-height: 460px; object-fit: cover;">
                            </figure>
                        @endif

                        <article class="prose" style="line-height: 2; font-size: 16px;">
                            {!! $post->content !!}
                        </article>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
