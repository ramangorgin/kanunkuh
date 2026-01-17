@extends('layout')

@section('title', $course->title)

@push('styles')
<!--
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
-->
<style>
    .course-header-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 12px;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .info-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        transition: transform 0.3s, box-shadow 0.3s;
    }
    
    .info-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.15);
    }
    
    .info-card h5 {
        color: #495057;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid #e9ecef;
    }
    
    .info-item {
        display: flex;
        align-items: center;
        margin-bottom: 12px;
    }
    
    .info-item i {
        width: 30px;
        color: #667eea;
        font-size: 1.2rem;
    }
    
    .teacher-card {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
    }
    
    .location-map {
        height: 300px;
        border-radius: 8px;
        margin-top: 15px;
    }
    
    .register-button-container {
        text-align: center;
        margin: 40px 0;
    }
    
    .btn-register {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        padding: 15px 40px;
        font-size: 1.1rem;
        font-weight: 600;
        border-radius: 50px;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        transition: all 0.3s;
    }
    
    .btn-register:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
        color: white;
    }
    
    .status-badge {
        font-size: 0.9rem;
        padding: 8px 16px;
        border-radius: 20px;
    }
</style>
@endpush

@section('content')
<div class="container my-5">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="bi bi-info-circle-fill me-2"></i>
            {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Course Header --}}
    <div class="course-header-card">
        <h1 class="mb-3">{{ $course->title }}</h1>
        @if($course->federationCourse)
            <span class="badge bg-light text-dark me-2">{{ $course->federationCourse->title }}</span>
        @endif
        @if($course->status == 'published')
            <span class="badge bg-success status-badge">منتشر شده</span>
        @elseif($course->status == 'completed')
            <span class="badge bg-primary status-badge">تکمیل شده</span>
        @elseif($course->status == 'canceled')
            <span class="badge bg-danger status-badge">لغو شده</span>
        @endif
    </div>

    {{-- Registration Status --}}
    @if($userRegistration)
        <div class="alert alert-info">
            <i class="bi bi-info-circle-fill me-2"></i>
            <strong>وضعیت ثبت‌نام شما:</strong>
            @if($userRegistration->status == 'approved')
                <span class="badge bg-success ms-2">تأیید شده</span>
            @elseif($userRegistration->status == 'paid')
                <span class="badge bg-info ms-2">پرداخت شده - در انتظار تأیید ثبت‌نام</span>
            @elseif($userRegistration->status == 'pending')
                <span class="badge bg-warning ms-2">در انتظار تأیید</span>
            @elseif($userRegistration->status == 'rejected')
                <span class="badge bg-danger ms-2">رد شده</span>
                @if($userRegistration->payment && $userRegistration->payment->status == 'approved')
                    <div class="alert alert-warning mt-2 mb-0">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <strong>توجه:</strong> پرداخت شما تأیید شده است اما ثبت‌نام رد شده. برای استرداد وجه با امور مالی کانون کوه تماس بگیرید.
                    </div>
                @endif
            @endif
        </div>
    @endif

    {{-- Registration Button or Message --}}
    <div class="register-button-container">
        @if($canRegister)
            <a href="{{ route('courses.register.create', $course->id) }}" class="btn btn-register btn-lg">
                <i class="bi bi-pencil-square me-2"></i>
                ثبت‌نام در دوره
            </a>
        @elseif($registrationMessage)
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                {{ $registrationMessage }}
            </div>
        @endif
    </div>

    <div class="row">
        {{-- Left Column --}}
        <div class="col-lg-8">
            {{-- Course Description --}}
            @if($course->description)
            <div class="info-card">
                <h5><i class="bi bi-file-text me-2"></i> توضیحات دوره</h5>
                <div>{!! $course->description !!}</div>
            </div>
            @endif
        </div>

        {{-- Right Column --}}
        <div class="col-lg-4">
            {{-- Course Details --}}
            <div class="info-card">
                <h5><i class="bi bi-info-circle me-2"></i> اطلاعات دوره</h5>
                
                <div class="info-item">
                    <i class="bi bi-calendar-event"></i>
                    <div>
                        <strong>تاریخ شروع:</strong><br>
                        <span class="text-muted">
                            @if($course->start_date)
                                {{ verta($course->start_date)->format('Y/m/d') }}
                            @else
                                —
                            @endif
                        </span>
                    </div>
                </div>

                <div class="info-item">
                    <i class="bi bi-calendar-check"></i>
                    <div>
                        <strong>تاریخ پایان:</strong><br>
                        <span class="text-muted">
                            @if($course->end_date)
                                {{ verta($course->end_date)->format('Y/m/d') }}
                            @else
                                —
                            @endif
                        </span>
                    </div>
                </div>

                @if($course->start_time || $course->end_time)
                <div class="info-item">
                    <i class="bi bi-clock"></i>
                    <div>
                        <strong>ساعات:</strong><br>
                        <span class="text-muted">
                            @if($course->start_time)
                                {{ \Carbon\Carbon::parse($course->start_time)->format('H:i') }}
                            @endif
                            @if($course->start_time && $course->end_time)
                                تا
                            @endif
                            @if($course->end_time)
                                {{ \Carbon\Carbon::parse($course->end_time)->format('H:i') }}
                            @endif
                        </span>
                    </div>
                </div>
                @endif

                @if($course->duration)
                <div class="info-item">
                    <i class="bi bi-calendar-range"></i>
                    <div>
                        <strong>مدت دوره:</strong>
                        <span class="text-muted ms-2">{{ $course->duration }} روز</span>
                    </div>
                </div>
                @endif

                @if($course->capacity)
                <div class="info-item">
                    <i class="bi bi-people"></i>
                    <div>
                        <strong>ظرفیت:</strong>
                        <span class="text-muted ms-2">{{ $course->capacity }} نفر</span>
                    </div>
                </div>
                @endif

                <div class="info-item">
                    <i class="bi bi-door-open"></i>
                    <div>
                        <strong>وضعیت ثبت‌نام:</strong>
                        @if($course->is_registration_open)
                            <span class="badge bg-success ms-2">باز</span>
                        @else
                            <span class="badge bg-danger ms-2">بسته</span>
                        @endif
                    </div>
                </div>

                @if($course->registration_deadline)
                <div class="info-item">
                    <i class="bi bi-clock-history"></i>
                    <div>
                        <strong>مهلت ثبت‌نام:</strong><br>
                        <span class="text-muted">
                            {{ verta($course->registration_deadline)->format('Y/m/d H:i') }}
                            @if(now()->gt($course->registration_deadline))
                                <span class="badge bg-danger ms-2">گذشته</span>
                            @endif
                        </span>
                    </div>
                </div>
                @endif
            </div>

            {{-- Teacher --}}
            @if($course->teacher)
            <div class="teacher-card">
                <h5 class="mb-3"><i class="bi bi-person-badge me-2"></i> مدرس دوره</h5>
                <div class="d-flex align-items-center">
                    @if($course->teacher->profile_image)
                        <img src="{{ asset('storage/' . $course->teacher->profile_image) }}" 
                             class="rounded-circle me-3" 
                             width="70" 
                             height="70" 
                             alt="مدرس"
                             style="object-fit: cover;">
                    @else
                        <div class="rounded-circle bg-white text-dark d-flex align-items-center justify-content-center me-3" 
                             style="width: 70px; height: 70px; font-size: 1.5rem;">
                            <i class="bi bi-person"></i>
                        </div>
                    @endif
                    <div>
                        <div class="fw-bold fs-5">{{ $course->teacher->first_name }} {{ $course->teacher->last_name }}</div>
                        @if($course->teacher->biography)
                            <div class="mt-2" style="font-size: 0.9rem; opacity: 0.9;">
                                {{ Str::limit($course->teacher->biography, 100) }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            {{-- Location --}}
            @if($course->place || ($course->place_lat && $course->place_lon))
            <div class="info-card">
                <h5><i class="bi bi-geo-alt me-2"></i> محل برگزاری</h5>
                
                @if($course->place)
                <div class="info-item">
                    <i class="bi bi-geo-alt-fill"></i>
                    <div>
                        <strong>نام محل:</strong>
                        <span class="text-muted ms-2">{{ $course->place }}</span>
                    </div>
                </div>
                @endif

                @if($course->place_address)
                <div class="info-item">
                    <i class="bi bi-map"></i>
                    <div>
                        <strong>آدرس:</strong>
                        <span class="text-muted ms-2">{{ $course->place_address }}</span>
                    </div>
                </div>
                @endif

                @if($course->place_lat && $course->place_lon)
                <div id="course_location_map" class="location-map"></div>
                @endif
            </div>
            @endif

            {{-- Cost Information --}}
            <div class="info-card">
                <h5><i class="bi bi-cash-coin me-2"></i> هزینه دوره</h5>
                
                @if($course->is_free)
                    <div class="alert alert-success mb-0">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        این دوره رایگان است.
                    </div>
                @else
                    <div class="info-item">
                        <i class="bi bi-person"></i>
                        <div>
                            <strong>هزینه اعضا:</strong>
                            <span class="text-success ms-2">{{ number_format($course->member_cost) }} ریال</span>
                        </div>
                    </div>

                    <div class="info-item">
                        <i class="bi bi-person-plus"></i>
                        <div>
                            <strong>هزینه مهمانان:</strong>
                            <span class="text-success ms-2">{{ number_format($course->guest_cost) }} ریال</span>
                        </div>
                    </div>

                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!--
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
-->
<script>
    @if($course->place_lat && $course->place_lon)
    (function() {
        const map = L.map('course_location_map').setView([{{ $course->place_lat }}, {{ $course->place_lon }}], 15);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '',
            maxZoom: 19
        }).addTo(map);
        
        L.marker([{{ $course->place_lat }}, {{ $course->place_lon }}])
            .addTo(map)
            .bindPopup('{{ $course->place ?? "محل برگزاری دوره" }}');
    })();
    @endif
</script>
@endpush
