@extends('layout')

@push('styles')
<!--
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
-->
<style>
    .image-slideshow {
        position: relative;
        width: 100%;
        height: 500px;
        overflow: hidden;
        border-radius: 12px;
        margin-bottom: 30px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .image-slideshow img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        position: absolute;
        top: 0;
        left: 0;
        opacity: 0;
        transition: opacity 1s ease-in-out;
    }
    
    .image-slideshow img.active {
        opacity: 1;
        z-index: 2;
    }
    
    .image-slideshow .slide-indicators {
        position: absolute;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 10;
        display: flex;
        gap: 10px;
    }
    
    .image-slideshow .indicator {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: rgba(255,255,255,0.5);
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .image-slideshow .indicator.active {
        background: white;
        transform: scale(1.2);
    }
    
    .manager-card {
        transition: transform 0.3s, box-shadow 0.3s;
        border-radius: 12px;
        overflow: hidden;
    }
    
    .manager-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.15) !important;
    }
    
    .manager-avatar {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 50%;
        border: 3px solid #fff;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .transport-map {
        height: 300px;
        border-radius: 8px;
        margin-top: 15px;
    }
    
    .transport-info-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
    }
    
    .transport-info-card h6 {
        color: white;
        margin-bottom: 15px;
        font-weight: 600;
    }
    
    .transport-detail {
        background: rgba(255,255,255,0.15);
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 10px;
        backdrop-filter: blur(10px);
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

    {{-- Image Slideshow --}}
    @php
        $programImages = $program->files->where('file_type', 'image');
    @endphp
    
    @if($programImages->count() > 0)
        <div class="image-slideshow">
            @foreach($programImages as $index => $image)
                @php
                    $imagePath = $image->file_path;
                    $storageExists = \Illuminate\Support\Facades\Storage::disk('public')->exists($imagePath);
                    $fullSrc = $storageExists
                        ? asset('storage/' . $imagePath)
                        : (filter_var($imagePath, FILTER_VALIDATE_URL) ? $imagePath : asset($imagePath));
                @endphp
                <img src="{{ $fullSrc }}" 
                     alt="{{ $image->caption ?? 'تصویر برنامه' }}"
                     class="{{ $index === 0 ? 'active' : '' }}"
                     data-index="{{ $index }}">
            @endforeach
            <div class="slide-indicators">
                @foreach($programImages as $index => $image)
                    <div class="indicator {{ $index === 0 ? 'active' : '' }}" data-slide="{{ $index }}"></div>
                @endforeach
            </div>
        </div>
    @else
        <div class="alert alert-info text-center py-4 mb-4" style="border-radius: 12px;">
            <i class="bi bi-image display-4 text-muted d-block mb-3"></i>
            <p class="mb-0 text-muted">این برنامه تصویری ندارد</p>
        </div>
    @endif

    <div class="card shadow-lg mb-4">
        <div class="card-body p-4">
            <h2 class="mb-4">{{ $program->name }}</h2>
            
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-tag-fill text-primary fs-4 me-3"></i>
                        <div>
                            <strong>نوع برنامه:</strong>
                            <span class="badge bg-info ms-2">{{ $program->program_type }}</span>
                        </div>
                                </div>
                    
                    @if($program->peak_height)
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-arrow-up-circle-fill text-success fs-4 me-3"></i>
                        <div>
                            <strong>ارتفاع قله:</strong>
                            <span class="ms-2">{{ number_format($program->peak_height) }} متر</span>
                        </div>
                    </div>
                @endif

                    @if($program->region_name)
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-geo-alt-fill text-danger fs-4 me-3"></i>
                        <div>
                            <strong>منطقه:</strong>
                            <span class="ms-2">{{ $program->region_name }}</span>
            </div>
                    </div>
                @endif
                </div>

                <div class="col-md-6">
                    <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-calendar-event-fill text-primary fs-4 me-3"></i>
                            <div>
                            <strong>تاریخ اجرا:</strong>
                                <div class="text-muted">
                                @if($program->execution_date)
                                    {{ verta($program->execution_date)->format('Y/m/d H:i') }}
                                @else
                                    —
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($program->register_deadline)
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-clock-history text-warning fs-4 me-3"></i>
                            <div>
                            <strong>مهلت ثبت‌نام:</strong>
                                <div class="text-muted">
                                @php
                                    $deadlinePassed = now()->gt($program->register_deadline);
                                @endphp
                                {{ verta($program->register_deadline)->format('Y/m/d H:i') }}
                                @if($deadlinePassed)
                                    <span class="badge bg-danger ms-2">گذشته</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            @if($program->rules)
            <hr>
            <h5 class="mb-3">توضیحات</h5>
            <div class="bg-light p-4 rounded">
                {!! $program->rules !!}
        </div>        
            @endif

            @if($program->equipments && count($program->equipments) > 0)
            <hr>
            <h5 class="mb-3">تجهیزات مورد نیاز</h5>
            <div class="d-flex flex-wrap gap-2">
                @foreach($program->equipments as $equipment)
                    <span class="badge bg-secondary fs-6">{{ $equipment }}</span>
            @endforeach
        </div>
    @endif

            @if($program->meals && count($program->meals) > 0)
            <hr>
            <h5 class="mb-3">وعده‌های مورد نیاز</h5>
            <div class="d-flex flex-wrap gap-2">
                @foreach($program->meals as $meal)
                    <span class="badge bg-info text-dark fs-6">{{ $meal }}</span>
                @endforeach
                    </div>
                        @endif

            @if($program->cost_member || $program->cost_guest)
            <hr>
            <h5 class="mb-3">هزینه‌ها</h5>
            <div class="row">
                @if($program->cost_member)
                <div class="col-md-6 mb-2">
                    <strong>هزینه عضو:</strong> {{ number_format($program->cost_member) }} ریال
                </div>
                        @endif
                @if($program->cost_guest)
                <div class="col-md-6 mb-2">
                    <strong>هزینه مهمان:</strong> {{ number_format($program->cost_guest) }} ریال
                </div>
                @endif
            </div>
    @endif

            {{-- Transport Information --}}
            @php
                $transportTehran = $program->move_from_tehran ? json_decode($program->move_from_tehran, true) : null;
                $transportKaraj = $program->move_from_karaj ? json_decode($program->move_from_karaj, true) : null;
            @endphp
            
            @if($transportTehran || $transportKaraj)
            <hr>
            <h5 class="mb-3"><i class="bi bi-truck me-2"></i> اطلاعات حرکت</h5>
            
            @if($transportTehran)
            <div class="transport-info-card">
                <h6><i class="bi bi-geo-alt-fill me-2"></i> حرکت از تهران</h6>
                
                <div class="transport-detail">
                    <i class="bi bi-calendar-event me-2"></i>
                    <strong>تاریخ و ساعت:</strong>
                    @if(!empty($transportTehran['datetime']))
                        @php
                            try {
                                $tehranDate = \Carbon\Carbon::parse($transportTehran['datetime']);
                                echo verta($tehranDate)->format('Y/m/d H:i');
                            } catch (\Exception $e) {
                                echo $transportTehran['datetime'];
                            }
                    @endphp
                    @else
                        <span class="text-muted">تعیین نشده</span>
                    @endif
                </div>
                
                @if(!empty($transportTehran['place']))
                <div class="transport-detail">
                    <i class="bi bi-geo-fill me-2"></i>
                    <strong>محل قرار:</strong> {{ $transportTehran['place'] }}
            </div>
                @endif
                
                @if(!empty($transportTehran['lat']) && !empty($transportTehran['lon']))
                <div class="transport-detail">
                    <strong><i class="bi bi-map me-2"></i> موقعیت روی نقشه:</strong>
                    <div id="map-tehran" class="transport-map"></div>
                </div>
                @endif
            </div>
            @endif

            @if($transportKaraj)
            <div class="transport-info-card">
                <h6><i class="bi bi-geo-alt-fill me-2"></i> حرکت از کرج</h6>
                
                <div class="transport-detail">
                    <i class="bi bi-calendar-event me-2"></i>
                    <strong>تاریخ و ساعت:</strong>
                    @if(!empty($transportKaraj['datetime']))
                        @php
                            try {
                                $karajDate = \Carbon\Carbon::parse($transportKaraj['datetime']);
                                echo verta($karajDate)->format('Y/m/d H:i');
                            } catch (\Exception $e) {
                                echo $transportKaraj['datetime'];
                            }
                    @endphp
                    @else
                        <span class="text-muted">تعیین نشده</span>
                    @endif
        </div>

                @if(!empty($transportKaraj['place']))
                <div class="transport-detail">
                    <i class="bi bi-geo-fill me-2"></i>
                    <strong>محل قرار:</strong> {{ $transportKaraj['place'] }}
    </div>
                @endif
                
                @if(!empty($transportKaraj['lat']) && !empty($transportKaraj['lon']))
                <div class="transport-detail">
                    <strong><i class="bi bi-map me-2"></i> موقعیت روی نقشه:</strong>
                    <div id="map-karaj" class="transport-map"></div>
                </div>
                @endif
            </div>
            @endif
            @endif

            {{-- Program Managers --}}
            @if($program->userRoles->count() > 0)
            <hr>
            <h5 class="mb-3"><i class="bi bi-people-fill me-2"></i> مسئولین برنامه</h5>
            <div class="row g-4">
                @foreach($program->userRoles as $role)
                    <div class="col-md-4 col-sm-6">
                        <div class="card border-0 shadow-sm manager-card h-100">
                            <div class="card-body text-center p-4">
                                @if($role->user && $role->user->profile)
                                    @php
                                        $userPhoto = asset('images/default-avatar.png');
                                        if (!empty($role->user->profile->photo)) {
                                            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($role->user->profile->photo)) {
                                                $userPhoto = asset('storage/' . $role->user->profile->photo);
                                            } elseif (file_exists(public_path($role->user->profile->photo))) {
                                                $userPhoto = asset($role->user->profile->photo);
                                            }
                                        }
                                    @endphp
                                    <img src="{{ $userPhoto }}" 
                                         alt="{{ $role->user->full_name ?? $role->user->phone }}"
                                         class="manager-avatar mb-3">
                                    <h6 class="card-title fw-bold mb-2">{{ $role->role_title }}</h6>
                                    <p class="card-text mb-1">
                                        <strong>{{ $role->user->full_name ?? 'نامشخص' }}</strong>
                                    </p>
                                    <p class="text-muted small mb-0">
                                        <i class="bi bi-telephone me-1"></i>
                                        {{ $role->user->phone }}
                                    </p>
                                @elseif($role->user_name)
                                    <img src="{{ asset('images/default-avatar.png') }}" 
                                         alt="{{ $role->user_name }}"
                                         class="manager-avatar mb-3">
                                    <h6 class="card-title fw-bold mb-2">{{ $role->role_title }}</h6>
                                    <p class="card-text mb-0">{{ $role->user_name }}</p>
                                @else
                                    <img src="{{ asset('images/default-avatar.png') }}" 
                                         alt="مسئول"
                                         class="manager-avatar mb-3">
                                    <h6 class="card-title fw-bold mb-2">{{ $role->role_title }}</h6>
                                    <p class="card-text mb-0 text-muted">—</p>
                                @endif
                            </div>
                    </div>
                    </div>
                @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- منطق ویژه برای نمایش دکمه‌ها --}}
    @php
        $now = now();
        $registerDeadlinePassed = $program->register_deadline && $now->gt($program->register_deadline);
        $executionDatePassed = $program->execution_date && $now->gt($program->execution_date);
        $hasReport = $program->report !== null;
        
        // Get current user's registration status
        $currentRegistration = $userRegistration ?? $guestRegistration;
        $registrationStatus = $currentRegistration ? $currentRegistration->status : null;
        $paymentStatus = $currentRegistration && $currentRegistration->payment ? $currentRegistration->payment->status : null;
    @endphp

    <div class="text-center my-5">
        {{-- Registration Status Display --}}
        @if($currentRegistration)
            <div class="card border-0 shadow-sm mb-4" style="max-width: 600px; margin: 0 auto;">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="bi bi-info-circle-fill me-2"></i> وضعیت ثبت‌نام شما
                    </h5>
                    
                    @if($registrationStatus == 'approved')
                        <div class="alert alert-success mb-0">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            <strong>ثبت‌نام شما تأیید شده است</strong>
                            <p class="mb-0 mt-2">شما در این برنامه ثبت‌نام کرده‌اید و آماده شرکت هستید.</p>
                        </div>
                    @elseif($registrationStatus == 'paid')
                        <div class="alert alert-info mb-0">
                            <i class="bi bi-credit-card-fill me-2"></i>
                            <strong>پرداخت تأیید شده - در انتظار تأیید ثبت‌نام</strong>
                            <p class="mb-0 mt-2">پرداخت شما تأیید شده است. ثبت‌نام شما در حال بررسی نهایی است.</p>
                            @if($currentRegistration->payment)
                            <p class="mb-0 mt-2">
                                <small>کد پیگیری: <strong>{{ $currentRegistration->payment->transaction_code }}</strong></small>
                            </p>
                            @endif
                        </div>
                    @elseif($registrationStatus == 'pending' && $paymentStatus == 'pending')
                        <div class="alert alert-warning mb-0">
                            <i class="bi bi-clock-history me-2"></i>
                            <strong>در انتظار تأیید پرداخت</strong>
                            <p class="mb-0 mt-2">پرداخت شما در حال بررسی است. پس از تأیید پرداخت، ثبت‌نام شما فعال خواهد شد.</p>
                            @if($currentRegistration->payment)
                            <p class="mb-0 mt-2">
                                <small>کد پیگیری: <strong>{{ $currentRegistration->payment->transaction_code }}</strong></small>
                            </p>
                            @endif
                        </div>
                    @elseif($registrationStatus == 'pending')
                        <div class="alert alert-info mb-0">
                            <i class="bi bi-hourglass-split me-2"></i>
                            <strong>در انتظار بررسی</strong>
                            <p class="mb-0 mt-2">ثبت‌نام شما در حال بررسی است.</p>
                        </div>
                    @elseif($registrationStatus == 'rejected')
                        <div class="alert alert-danger mb-0">
                            <i class="bi bi-x-circle-fill me-2"></i>
                            <strong>ثبت‌نام رد شده</strong>
                            <p class="mb-0 mt-2">متأسفانه ثبت‌نام شما رد شده است.</p>
                            @if($currentRegistration->payment && $currentRegistration->payment->status == 'approved')
                                <div class="alert alert-warning mt-2 mb-0">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                    <strong>توجه:</strong> پرداخت شما تأیید شده است اما ثبت‌نام رد شده. برای استرداد وجه با امور مالی کانون کوه تماس بگیرید.
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- Registration Button / Deadline Message --}}
        @if(!$currentRegistration)
            @if($program->register_deadline && !$registerDeadlinePassed)
                <div class="alert alert-success mb-4 animate__animated animate__fadeInDown">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <strong>ثبت‌نام باز است</strong>
                    <p class="mb-0 mt-2">مهلت ثبت‌نام تا {{ verta($program->register_deadline)->format('Y/m/d H:i') }} می‌باشد.</p>
        </div>
                <a href="{{ route('programs.register.create', $program->id) }}" 
                   class="btn btn-primary btn-lg px-5 animate__animated animate__pulse animate__infinite">
                    <i class="bi bi-pencil-square me-2"></i> ثبت‌نام در برنامه
                </a>
            @elseif($registerDeadlinePassed)
                <div class="alert alert-warning mb-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>مهلت ثبت‌نام به پایان رسیده است</strong>
                </div>
            @endif
        @endif

        {{-- Program Report Link --}}
        @if($executionDatePassed && $hasReport)
            <div class="mt-4">
                <a href="{{ route('program_reports.show', $program->report->id) }}" 
                   class="btn btn-success btn-lg px-5">
                    <i class="bi bi-file-text me-2"></i> مشاهده گزارش برنامه
                </a>
        </div>
    @endif
</div>

</div>
@endsection

@push('scripts')

    <script>
        const dataKaraj = @json($transportKaraj);
        const neshanMapKaraj = new L.Map("map-karaj", {
                key: "web.34d371d6df614e62afe2604d5ee25b1f",
                maptype: "neshan",
                poi: true,
                traffic: true,
                center: [dataKaraj.lat, dataKaraj.lon],
                zoom: 15,
            });

        if (dataKaraj && dataKaraj.lat && dataKaraj.lon) {
            let markerKaraj = L.marker([dataKaraj.lat, dataKaraj.lon]).addTo(neshanMapKaraj);
        }
    </script>

    <script>
        const dataTehran = @json($transportTehran);
        const neshanMapTehran = new L.Map("map-tehran", {
            key: "web.34d371d6df614e62afe2604d5ee25b1f",
            maptype: "neshan",
            poi: true,
            traffic: true,
            center: [dataTehran.lat, dataTehran.lon],
            zoom: 15,
        });

        if (dataTehran && dataTehran.lat && dataTehran.lon) {
            let markerTehran = L.marker([dataTehran.lat, dataTehran.lon]).addTo(neshanMapTehran);
        }
    </script>
<!--
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
                                    -->
<script>
    // Image Slideshow
    @if($programImages->count() > 0)
    (function() {
        const images = document.querySelectorAll('.image-slideshow img');
        const indicators = document.querySelectorAll('.image-slideshow .indicator');
        let currentIndex = 0;
        const slideInterval = 3000; // 3 seconds
        
        function showSlide(index) {
            // Remove active class from all images and indicators
            images.forEach(img => img.classList.remove('active'));
            indicators.forEach(ind => ind.classList.remove('active'));
            
            // Add active class to current slide
            if (images[index]) {
                images[index].classList.add('active');
            }
            if (indicators[index]) {
                indicators[index].classList.add('active');
            }
        }
        
        function nextSlide() {
            currentIndex = (currentIndex + 1) % images.length;
            showSlide(currentIndex);
        }
        
        let timer = setInterval(nextSlide, slideInterval);

        // Pause on hover
        const slideshow = document.querySelector('.image-slideshow');
        if (slideshow) {
            slideshow.addEventListener('mouseenter', () => clearInterval(timer));
            slideshow.addEventListener('mouseleave', () => {
                timer = setInterval(nextSlide, slideInterval);
            });
        }
        
        // Click on indicator to jump to slide
        indicators.forEach((ind, index) => {
            ind.addEventListener('click', () => {
                currentIndex = index;
                showSlide(currentIndex);
            });
        });
    })();
    @endif
    


    /*
    @if($transportTehran && !empty($transportTehran['lat']) && !empty($transportTehran['lon']))
    (function() {
        const mapTehran = L.map('map-tehran').setView([{{ $transportTehran['lat'] }}, {{ $transportTehran['lon'] }}], 15);
        
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '',
            maxZoom: 19
            }).addTo(mapTehran);
        
        L.marker([{{ $transportTehran['lat'] }}, {{ $transportTehran['lon'] }}])
            .addTo(mapTehran)
            .bindPopup('{{ $transportTehran['place'] ?? "محل حرکت از تهران" }}');
    })();
        @endif

    @if($transportKaraj && !empty($transportKaraj['lat']) && !empty($transportKaraj['lon']))
    (function() {
        const mapKaraj = L.map('map-karaj').setView([{{ $transportKaraj['lat'] }}, {{ $transportKaraj['lon'] }}], 15);
        
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '',
            maxZoom: 19
            }).addTo(mapKaraj);
        
        L.marker([{{ $transportKaraj['lat'] }}, {{ $transportKaraj['lon'] }}])
            .addTo(mapKaraj)
            .bindPopup('{{ $transportKaraj['place'] ?? "محل حرکت از کرج" }}');
    })();
        @endif
    */    
</script>
@endpush
