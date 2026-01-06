@php use Illuminate\Support\Str; @endphp

@extends('layout')

@section('title', 'بلاگ')

@section('content')
@php $fallbackImage = asset('images/slider/slide1.jpg'); @endphp
<section class="py-5 bg-light">
    <div class="container">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4">
            <div>
                <h1 class="fw-bold mb-2">بلاگ کانون کوه</h1>
                <p class="text-muted mb-0">صعود، آموزش، طبیعت‌گردی و روایت‌های تازه را اینجا بخوانید.</p>
            </div>
        </div>

        <div class="row g-3 g-md-4">
            @forelse($posts as $post)
                @php $cover = $post->featured_image ? asset('storage/'.$post->featured_image) : $fallbackImage; @endphp
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="content-card h-100">
                        <div class="content-cover" style="background-image: linear-gradient(135deg, rgba(0,0,0,.5), rgba(0,0,0,.65)), url('{{ $cover }}');"></div>
                        <div class="content-body">
                            <div class="d-flex justify-content-between text-muted small mb-1">
                                <span class="d-inline-flex align-items-center gap-1"><i class="bi bi-calendar3"></i>{{ $post->published_at ? toPersianNumber($post->published_at->format('Y/m/d')) : '' }}</span>
                                @if($post->reading_time)
                                    <span class="d-inline-flex align-items-center gap-1"><i class="bi bi-hourglass"></i>{{ toPersianNumber($post->reading_time) }} دقیقه</span>
                                @endif
                            </div>
                            <h6 class="content-title"><a href="{{ route('blog.show', $post->slug) }}" class="text-decoration-none text-dark">{{ $post->title }}</a></h6>
                            <p class="text-muted small mb-3 line-clamp-2">{{ $post->excerpt ? Str::limit($post->excerpt, 140) : Str::limit(strip_tags($post->content), 140) }}</p>
                            <div class="d-flex flex-wrap gap-1 mb-3">
                                @foreach($post->categories as $cat)
                                    <span class="badge bg-light text-dark border">{{ $cat->name }}</span>
                                @endforeach
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                <span class="badge bg-primary-subtle text-primary">وبلاگ</span>
                                <a href="{{ route('blog.show', $post->slug) }}" class="btn btn-sm btn-outline-primary">ادامه</a>
                            </div>
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

@push('styles')
<style>
.content-card { background:#fff; border-radius:14px; overflow:hidden; box-shadow:0 15px 35px rgba(0,0,0,0.08); display:flex; flex-direction:column; transition:transform .25s ease, box-shadow .25s ease; min-height:100%; }
.content-card:hover { transform: translateY(-6px); box-shadow:0 18px 40px rgba(0,0,0,0.12); }
.content-cover { height:190px; background-size:cover; background-position:center; }
.content-body { padding:14px; display:flex; flex-direction:column; height:100%; }
.content-title { font-weight:700; font-size:16px; margin:10px 0 12px; line-height:1.6; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; min-height:48px; }
.line-clamp-2 { display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
.badge i, .content-body i { margin-left:6px; }
</style>
@endpush
