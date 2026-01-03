@extends('admin.layout')

@section('title', 'مشاهده گزارش برنامه')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            @if(auth()->check() && auth()->user()->role === 'admin')
                <li class="breadcrumb-item"><a href="{{ route('admin.program_reports.index') }}">گزارش‌های برنامه</a></li>
            @else
                <li class="breadcrumb-item"><a href="{{ route('dashboard.programs.index') }}">برنامه‌های من</a></li>
            @endif
            <li class="breadcrumb-item active" aria-current="page">مشاهده گزارش</li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="report-container">
        @php
            $isAdmin = auth()->check() && auth()->user()->role === 'admin';
            $reportImages = $programReport->program && $programReport->program->files ? $programReport->program->files->where('file_type', 'image') : collect();
            $mapFiles = $programReport->program && $programReport->program->files ? $programReport->program->files->whereIn('file_type', ['map', 'other', 'pdf', 'gps']) : collect();
            $formatNumber = fn($value) => $value !== null ? fa_digits(number_format($value)) : '—';
        @endphp

        {{-- Image Slideshow --}}
        @if($reportImages->count() > 0)
            <div class="card shadow-lg border-0 mb-4">
                <div id="reportImageCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="2000">
                    <div class="carousel-indicators">
                        @foreach($reportImages as $index => $file)
                            <button type="button" data-bs-target="#reportImageCarousel" data-bs-slide-to="{{ $index }}" {{ $index === 0 ? 'class="active" aria-current="true"' : '' }} aria-label="Slide {{ $index + 1 }}"></button>
                        @endforeach
                    </div>
                    <div class="carousel-inner">
                        @foreach($reportImages as $index => $file)
                            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                <img src="{{ Storage::url($file->file_path) }}" class="d-block w-100" alt="گزارش تصویر {{ $index + 1 }}" style="height: 500px; object-fit: cover;">
                            </div>
                        @endforeach
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#reportImageCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">قبلی</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#reportImageCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">بعدی</span>
                    </button>
                </div>
            </div>
        @else
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body text-center py-5">
                    <i class="bi bi-image fs-1 text-muted d-block mb-3"></i>
                    <p class="text-muted mb-0">این گزارش تصویری ندارد</p>
                </div>
            </div>
        @endif

        {{-- Header Section with Actions --}}
        <div class="card shadow-lg border-0 mb-4 report-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 15px;">
            <div class="card-body text-white p-4 p-md-5">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                    <div>
                        <h2 class="mb-2 fw-bold">
                            <i class="bi bi-file-text me-2"></i>
                            گزارش برنامه: {{ $programReport->report_program_name ?? $programReport->program->name ?? '—' }}
                        </h2>
                        <p class="mb-0 opacity-75">
                            <i class="bi bi-calendar3 me-2"></i>
                            تاریخ گزارش: {{ $programReport->report_date ? fa_digits(verta($programReport->report_date)->format('Y/m/d H:i')) : '—' }} |
                            تاریخ ایجاد: {{ fa_digits(verta($programReport->created_at)->format('Y/m/d H:i')) }}
                        </p>
                    </div>
                    <div class="btn-group flex-wrap">
                        @if($isAdmin)
                            <a href="{{ route('admin.program_reports.downloadPdf', $programReport->id) }}" class="btn btn-light btn-sm" target="_blank">
                                <i class="bi bi-file-pdf me-1"></i> دانلود PDF
                            </a>
                            <a href="{{ route('admin.program_reports.edit', $programReport->id) }}" class="btn btn-light btn-sm">
                                <i class="bi bi-pencil me-1"></i> ویرایش
                            </a>
                            <a href="{{ route('admin.program_reports.index') }}" class="btn btn-light btn-sm">
                                <i class="bi bi-arrow-right me-1"></i> بازگشت
                            </a>
                        @else
                            <a href="{{ route('program_reports.downloadPdf', $programReport->id) }}" class="btn btn-light btn-sm" target="_blank">
                                <i class="bi bi-file-pdf me-1"></i> دانلود PDF
                            </a>
                            <a href="{{ route('dashboard.programs.index') }}" class="btn btn-light btn-sm">
                                <i class="bi bi-arrow-right me-1"></i> بازگشت
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- اطلاعات کلی گزارش و برنامه --}}
        @if($programReport->report_date || $programReport->report_program_type || $programReport->report_program_name || $programReport->report_region_route || $programReport->report_start_date || $programReport->report_end_date || $programReport->report_duration)
        <div class="card shadow-sm mb-4 border-start border-4 border-primary">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i> اطلاعات کلی گزارش و برنامه</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @if($programReport->report_date)
                    <div class="col-md-4 col-sm-6">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-calendar-check text-primary fs-4 me-3"></i>
                            <div>
                                <strong class="d-block text-muted small">تاریخ گزارش</strong>
                                <span class="text-dark">{{ fa_digits(verta($programReport->report_date)->format('Y/m/d H:i')) }}</span>
                            </div>
                        </div>
                    </div>
                    @endif
                    @if($programReport->report_program_type)
                    <div class="col-md-4 col-sm-6">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-tag text-success fs-4 me-3"></i>
                            <div>
                                <strong class="d-block text-muted small">نوع برنامه</strong>
                                <span class="badge bg-success mt-1">{{ $programReport->report_program_type }}</span>
                            </div>
                        </div>
                    </div>
                    @endif
                    @if($programReport->report_program_name)
                    <div class="col-md-4 col-sm-6">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-bookmark text-info fs-4 me-3"></i>
                            <div>
                                <strong class="d-block text-muted small">نام برنامه</strong>
                                <span class="text-dark">{{ $programReport->report_program_name }}</span>
                            </div>
                        </div>
                    </div>
                    @endif
                    @if($programReport->report_region_route)
                    <div class="col-md-4 col-sm-6">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-geo-alt text-warning fs-4 me-3"></i>
                            <div>
                                <strong class="d-block text-muted small">منطقه و مسیر</strong>
                                <span class="text-dark">{{ $programReport->report_region_route }}</span>
                            </div>
                        </div>
                    </div>
                    @endif
                    @if($programReport->report_start_date)
                    <div class="col-md-4 col-sm-6">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-calendar-event text-danger fs-4 me-3"></i>
                            <div>
                                <strong class="d-block text-muted small">از تاریخ</strong>
                                <span class="text-dark">{{ fa_digits(verta($programReport->report_start_date)->format('Y/m/d')) }}</span>
                            </div>
                        </div>
                    </div>
                    @endif
                    @if($programReport->report_end_date)
                    <div class="col-md-4 col-sm-6">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-calendar-x text-secondary fs-4 me-3"></i>
                            <div>
                                <strong class="d-block text-muted small">تا تاریخ</strong>
                                <span class="text-dark">{{ fa_digits(verta($programReport->report_end_date)->format('Y/m/d')) }}</span>
                            </div>
                        </div>
                    </div>
                    @endif
                    @if($programReport->report_duration)
                    <div class="col-md-4 col-sm-6">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-clock-history text-primary fs-4 me-3"></i>
                            <div>
                                <strong class="d-block text-muted small">مدت</strong>
                                <span class="text-dark">{{ fa_digits($programReport->report_duration) }}</span>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- مشخصات گزارشگر --}}
        @if($programReport->reporter_name || $programReport->reporter)
        <div class="card shadow-sm mb-4 border-start border-4 border-info">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="bi bi-person-badge me-2"></i> مشخصات گزارشگر</h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <i class="bi bi-person-circle text-info fs-1 me-3"></i>
                    <div>
                        <strong class="d-block">نام گزارشگر</strong>
                        <span class="text-dark fs-5">{{ $programReport->reporter_name ?? ($programReport->reporter ? ($programReport->reporter->full_name ?? $programReport->reporter->phone) : '—') }}</span>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- عوامل اجرایی برنامه --}}
        @if($programReport->program && $programReport->program->userRoles && $programReport->program->userRoles->count() > 0)
        <div class="card shadow-sm mb-4 border-start border-4 border-success">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="bi bi-people me-2"></i> عوامل اجرایی برنامه</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @foreach($programReport->program->userRoles as $role)
                    <div class="col-md-6 col-lg-4">
                        <div class="bg-light p-3 rounded h-100">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-person-check text-success fs-4 me-2"></i>
                                <strong class="text-success">{{ $role->role_title }}</strong>
                            </div>
                            @if($role->user)
                                <div class="d-flex align-items-center">
                                    @if($role->user->profile && $role->user->profile->photo)
                                        <img src="{{ Storage::url($role->user->profile->photo) }}" alt="{{ $role->user->full_name ?? $role->user->phone }}" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                    @else
                                        <i class="bi bi-person-circle text-muted fs-4 me-2"></i>
                                    @endif
                                    <div>
                                        <div class="fw-bold">{{ $role->user->full_name ?? $role->user_name ?? $role->user->phone }}</div>
                                        @if($role->user->phone)
                                            <small class="text-muted">{{ $role->user->phone }}</small>
                                        @endif
                                    </div>
                                </div>
                            @elseif($role->user_name)
                                <div class="text-dark">{{ $role->user_name }}</div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- شرکت‌کنندگان و مهمانان --}}
        @if($programReport->participants_count || ($programReport->participants && count($programReport->participants) > 0))
        <div class="card shadow-sm mb-4 border-start border-4 border-warning">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="bi bi-people-fill me-2"></i> شرکت‌کنندگان</h5>
            </div>
            <div class="card-body">
                @if($programReport->participants_count)
                <div class="mb-3">
                    <strong><i class="bi bi-123 text-warning me-2"></i> تعداد کل:</strong>
                    <span class="badge bg-warning text-dark fs-6">{{ fa_digits($programReport->participants_count) }} نفر</span>
                </div>
                @endif
                @if($programReport->participants && is_array($programReport->participants) && count($programReport->participants) > 0)
                <div class="row g-2">
                    @foreach($programReport->participants as $participantId)
                        @php
                            $participant = \App\Models\User::find($participantId);
                        @endphp
                        @if($participant)
                        <div class="col-md-6 col-lg-4">
                            <div class="bg-light p-2 rounded">
                                <i class="bi bi-person text-warning me-2"></i>
                                <span>{{ $participant->full_name ?? $participant->phone }}</span>
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- اطلاعات ارتفاع و مسیر --}}
        @if($programReport->start_altitude || $programReport->target_altitude || $programReport->start_location_name || $programReport->distance_from_tehran || $programReport->road_type)
        <div class="card shadow-sm mb-4 border-start border-4 border-info">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="bi bi-geo-alt-fill me-2"></i> اطلاعات ارتفاع و مسیر</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @if($programReport->start_altitude)
                    <div class="col-md-4 col-sm-6">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-arrow-down-circle text-info fs-4 me-3"></i>
                            <div>
                                <strong class="d-block text-muted small">ارتفاع شروع</strong>
                                <span class="text-dark">{{ $formatNumber($programReport->start_altitude) }} متر</span>
                            </div>
                        </div>
                    </div>
                    @endif
                    @if($programReport->target_altitude)
                    <div class="col-md-4 col-sm-6">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-arrow-up-circle text-success fs-4 me-3"></i>
                            <div>
                                <strong class="d-block text-muted small">ارتفاع هدف</strong>
                                <span class="text-dark">{{ $formatNumber($programReport->target_altitude) }} متر</span>
                            </div>
                        </div>
                    </div>
                    @endif
                    @if($programReport->start_location_name)
                    <div class="col-md-4 col-sm-6">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-geo-fill text-primary fs-4 me-3"></i>
                            <div>
                                <strong class="d-block text-muted small">محل شروع</strong>
                                <span class="text-dark">{{ $programReport->start_location_name }}</span>
                            </div>
                        </div>
                    </div>
                    @endif
                    @if($programReport->distance_from_tehran)
                    <div class="col-md-4 col-sm-6">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-signpost-split text-warning fs-4 me-3"></i>
                            <div>
                                <strong class="d-block text-muted small">فاصله از تهران</strong>
                                <span class="text-dark">{{ $formatNumber($programReport->distance_from_tehran) }} کیلومتر</span>
                            </div>
                        </div>
                    </div>
                    @endif
                    @if($programReport->road_type)
                    <div class="col-md-4 col-sm-6">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-road text-secondary fs-4 me-3"></i>
                            <div>
                                <strong class="d-block text-muted small">نوع جاده</strong>
                                <span class="badge bg-secondary mt-1">{{ $programReport->road_type }}</span>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- طول و عرض جغرافیایی نقاط --}}
        @if($programReport->geo_points && is_array($programReport->geo_points) && count($programReport->geo_points) > 0)
        <div class="card shadow-sm mb-4 border-start border-4 border-dark">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0"><i class="bi bi-geo me-2"></i> طول و عرض جغرافیایی نقاط</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>نام نقطه</th>
                                <th>عرض جغرافیایی (Latitude)</th>
                                <th>طول جغرافیایی (Longitude)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($programReport->geo_points as $point)
                            <tr>
                                <td><i class="bi bi-geo-alt text-primary me-2"></i>{{ $point['name'] ?? '—' }}</td>
                                <td>{{ isset($point['lat']) ? fa_digits($point['lat']) : '—' }}</td>
                                <td>{{ isset($point['lon']) ? fa_digits($point['lon']) : '—' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        {{-- نوع جاده و حمل‌ونقل --}}
        @if($programReport->road_type || ($programReport->transport_types && is_array($programReport->transport_types) && count($programReport->transport_types) > 0))
        <div class="card shadow-sm mb-4 border-start border-4 border-secondary">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0"><i class="bi bi-road me-2"></i> نوع جاده و حمل‌ونقل</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @if($programReport->road_type)
                    <div class="col-md-6">
                        <strong><i class="bi bi-road text-secondary me-2"></i> نوع جاده:</strong>
                        <span class="badge bg-secondary ms-2">{{ $programReport->road_type }}</span>
                    </div>
                    @endif
                    @if($programReport->transport_types && is_array($programReport->transport_types) && count($programReport->transport_types) > 0)
                    <div class="col-md-6">
                        <strong><i class="bi bi-truck text-info me-2"></i> انواع حمل‌ونقل:</strong>
                        @foreach($programReport->transport_types as $transport)
                            <span class="badge bg-info ms-1">{{ $transport }}</span>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- مشخصات فنی مسیر --}}
        @if($programReport->route_difficulty || $programReport->slope || $programReport->rock_engagement || $programReport->ice_engagement || $programReport->avg_backpack_weight || $programReport->prerequisites || ($programReport->technical_equipments && count($programReport->technical_equipments) > 0))
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="bi bi-tools me-2"></i> مشخصات فنی مسیر</h5>
            </div>
            <div class="card-body">
                @if($programReport->technical_equipments && is_array($programReport->technical_equipments) && count($programReport->technical_equipments) > 0)
                <div class="mb-3">
                    <strong><i class="bi bi-box-seam text-info me-2"></i> وسایل فنی مورد نیاز:</strong>
                    <div class="mt-2">
                        @foreach($programReport->technical_equipments as $equipment)
                            <span class="badge bg-info me-1 mb-1">{{ $equipment }}</span>
                        @endforeach
                    </div>
                </div>
                @endif
                <div class="row g-3">
                    @if($programReport->route_difficulty)
                    <div class="col-md-6 col-lg-3">
                        <div class="info-box bg-light p-3 rounded text-center h-100">
                            <i class="bi bi-signpost-2 text-primary fs-3 mb-2 d-block"></i>
                            <strong class="d-block text-muted small">سختی مسیر</strong>
                            <span class="badge bg-primary mt-1">{{ $programReport->route_difficulty }}</span>
                        </div>
                    </div>
                    @endif
                    @if($programReport->slope)
                    <div class="col-md-6 col-lg-3">
                        <div class="info-box bg-light p-3 rounded text-center h-100">
                            <i class="bi bi-graph-up text-success fs-3 mb-2 d-block"></i>
                            <strong class="d-block text-muted small">شیب</strong>
                            <span class="text-dark mt-1">{{ $programReport->slope }}</span>
                        </div>
                    </div>
                    @endif
                    @if($programReport->rock_engagement)
                    <div class="col-md-6 col-lg-3">
                        <div class="info-box bg-light p-3 rounded text-center h-100">
                            <i class="bi bi-mountain text-danger fs-3 mb-2 d-block"></i>
                            <strong class="d-block text-muted small">درگیری با سنگ</strong>
                            <span class="badge bg-danger mt-1">{{ $programReport->rock_engagement }}</span>
                        </div>
                    </div>
                    @endif
                    @if($programReport->ice_engagement)
                    <div class="col-md-6 col-lg-3">
                        <div class="info-box bg-light p-3 rounded text-center h-100">
                            <i class="bi bi-snow text-info fs-3 mb-2 d-block"></i>
                            <strong class="d-block text-muted small">درگیری با یخ</strong>
                            <span class="badge bg-info mt-1">{{ $programReport->ice_engagement }}</span>
                        </div>
                    </div>
                    @endif
                    @if($programReport->avg_backpack_weight)
                    <div class="col-md-6 col-lg-3">
                        <div class="info-box bg-light p-3 rounded text-center h-100">
                            <i class="bi bi-bag text-warning fs-3 mb-2 d-block"></i>
                            <strong class="d-block text-muted small">میانگین وزن کوله</strong>
                            <span class="text-dark mt-1">{{ $programReport->avg_backpack_weight }} کیلوگرم</span>
                        </div>
                    </div>
                    @endif
                </div>
                @if($programReport->prerequisites)
                <div class="mt-3">
                    <strong><i class="bi bi-list-check me-2"></i> پیش‌نیازها:</strong>
                    <p class="mb-0 mt-2">{{ $programReport->prerequisites }}</p>
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- ویژگی فنی برنامه --}}
        @if($programReport->technical_feature)
        <div class="card shadow-sm mb-4 border-start border-4 border-warning">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="bi bi-star me-2"></i> ویژگی فنی برنامه</h5>
            </div>
            <div class="card-body">
                <span class="badge bg-warning text-dark fs-6">{{ $programReport->technical_feature }}</span>
            </div>
        </div>
        @endif

        {{-- مشخصات طبیعی منطقه --}}
        @if($programReport->vegetation || $programReport->wildlife || $programReport->attractions || $programReport->local_language)
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="card shadow-sm h-100 border-start border-4 border-success">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="bi bi-tree me-2"></i> مشخصات طبیعی</h5>
                    </div>
                    <div class="card-body">
                        @if($programReport->vegetation)
                        <div class="mb-3">
                            <strong><i class="bi bi-flower1 text-success me-2"></i> پوشش گیاهی:</strong>
                            <p class="mb-0 mt-2">{{ $programReport->vegetation }}</p>
                        </div>
                        @endif
                        @if($programReport->wildlife)
                        <div class="mb-3">
                            <strong><i class="bi bi-heart text-danger me-2"></i> حیات وحش:</strong>
                            <p class="mb-0 mt-2">{{ $programReport->wildlife }}</p>
                        </div>
                        @endif
                        @if($programReport->attractions)
                        <div class="mb-3">
                            <strong><i class="bi bi-star text-warning me-2"></i> جاذبه‌ها:</strong>
                            <p class="mb-0 mt-2">{{ $programReport->attractions }}</p>
                        </div>
                        @endif
                        @if($programReport->local_language)
                        <div>
                            <strong><i class="bi bi-translate text-info me-2"></i> زبان محلی:</strong>
                            <span class="badge bg-info mt-1">{{ $programReport->local_language }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm h-100 border-start border-4 border-primary">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-cloud-sun me-2"></i> شرایط آب و هوایی</h5>
                    </div>
                    <div class="card-body">
                        @if($programReport->weather)
                        <div class="mb-3">
                            <strong><i class="bi bi-cloud text-primary me-2"></i> آب و هوا:</strong>
                            <p class="mb-0 mt-2">{{ $programReport->weather }}</p>
                        </div>
                        @endif
                        <div class="row g-3">
                            @if($programReport->wind_speed)
                            <div class="col-6">
                                <div class="bg-light p-3 rounded text-center">
                                    <i class="bi bi-wind text-primary fs-4 mb-2 d-block"></i>
                                    <strong class="d-block text-muted small">سرعت باد</strong>
                                    <span class="text-dark">{{ fa_digits($programReport->wind_speed) }} km/h</span>
                                </div>
                            </div>
                            @endif
                            @if($programReport->temperature)
                            <div class="col-6">
                                <div class="bg-light p-3 rounded text-center">
                                    <i class="bi bi-thermometer-half text-danger fs-4 mb-2 d-block"></i>
                                    <strong class="d-block text-muted small">دما</strong>
                                    <span class="text-dark">{{ fa_digits($programReport->temperature) }} °C</span>
                                </div>
                            </div>
                            @endif
                        </div>
                        @if($programReport->food_supply)
                        <div class="mt-3">
                            <strong><i class="bi bi-cup-hot text-warning me-2"></i> تأمین غذا:</strong>
                            <span class="badge bg-warning text-dark mt-1">{{ $programReport->food_supply }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- اطلاعات محلی --}}
        @if($programReport->local_village_name || $programReport->local_guide_info || $programReport->shelters_info)
        <div class="card shadow-sm mb-4 border-start border-4 border-secondary">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0"><i class="bi bi-geo-alt me-2"></i> اطلاعات محلی</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @if($programReport->local_village_name)
                    <div class="col-md-6">
                        <strong><i class="bi bi-house text-secondary me-2"></i> نام محله یا روستا:</strong>
                        <p class="mb-0 mt-2">{{ $programReport->local_village_name }}</p>
                    </div>
                    @endif
                    @if($programReport->local_guide_info)
                    <div class="col-md-6">
                        <strong><i class="bi bi-person-workspace text-info me-2"></i> راهنمای منطقه:</strong>
                        <p class="mb-0 mt-2">{{ $programReport->local_guide_info }}</p>
                    </div>
                    @endif
                    @if($programReport->shelters_info)
                    <div class="col-12">
                        <strong><i class="bi bi-house-heart text-warning me-2"></i> پناهگاه‌ها / محل‌های اطراق:</strong>
                        <p class="mb-0 mt-2">{{ $programReport->shelters_info }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- پناهگاه‌ها و محل‌های اطراق --}}
        @if($programReport->shelters && is_array($programReport->shelters) && count($programReport->shelters) > 0)
        <div class="card shadow-sm mb-4 border-start border-4 border-warning">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="bi bi-house-heart me-2"></i> پناهگاه‌ها / محل‌های اطراق</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-warning">
                            <tr>
                                <th>نام</th>
                                <th>ارتفاع (متر)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($programReport->shelters as $shelter)
                            <tr>
                                <td><i class="bi bi-buildings text-warning me-2"></i>{{ $shelter['name'] ?? '—' }}</td>
                                <td>{{ isset($shelter['height']) ? fa_digits($shelter['height']) : '—' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        {{-- مشخصات رفاهی منطقه --}}
        @if($programReport->facilities && is_array($programReport->facilities) && count($programReport->facilities) > 0)
        <div class="card shadow-sm mb-4 border-start border-4 border-success">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="bi bi-building me-2"></i> مشخصات رفاهی منطقه</h5>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    @php
                        $facilityLabels = [
                            'piped_water' => 'آب لوله‌کشی',
                            'seasonal_spring' => 'چشمه فصلی',
                            'permanent_spring' => 'چشمه دائم',
                            'school' => 'مدرسه',
                            'phone' => 'تلفن',
                            'electricity' => 'برق',
                            'post' => 'پست',
                            'mobile_coverage' => 'آنتن‌دهی موبایل'
                        ];
                    @endphp
                    @foreach($programReport->facilities as $facility)
                        @if(isset($facilityLabels[$facility]))
                        <div class="col-md-3 col-sm-6">
                            <div class="bg-light p-2 rounded text-center">
                                <i class="bi bi-check-circle text-success me-1"></i>
                                <span>{{ $facilityLabels[$facility] }}</span>
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- زمان‌بندی اجرای برنامه --}}
        @if($programReport->timeline && is_array($programReport->timeline) && count($programReport->timeline) > 0)
        <div class="card shadow-sm mb-4 border-start border-4 border-primary">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-clock me-2"></i> زمان‌بندی اجرای برنامه</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-primary">
                            <tr>
                                <th>نام رویداد</th>
                                <th>تاریخ و ساعت</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($programReport->timeline as $event)
                            <tr>
                                <td><i class="bi bi-calendar-event text-primary me-2"></i>{{ $event['title'] ?? '—' }}</td>
                                <td>
                                    @if(isset($event['datetime']))
                                        @php
                                            try {
                                                $date = \Carbon\Carbon::parse($event['datetime']);
                                                echo fa_digits(verta($date)->format('Y/m/d H:i'));
                                            } catch (\Exception $e) {
                                                echo fa_digits($event['datetime']);
                                            }
                                        @endphp
                                    @else
                                        —
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        {{-- شرح گزارش برنامه --}}
        @if($programReport->report_description)
        <div class="card shadow-sm mb-4 animate-fade-in border-start border-4 border-primary">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i> شرح گزارش برنامه</h5>
            </div>
            <div class="card-body">
                <div class="report-content">
                    {!! $programReport->report_description !!}
                </div>
            </div>
        </div>
        @endif

        {{-- کروکی و نقشه --}}
        @if($mapFiles->count() > 0)
        <div class="card shadow-sm mb-4 border-start border-4 border-danger">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0"><i class="bi bi-map me-2"></i> کروکی و نقشه</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @foreach($mapFiles as $mapFile)
                    <div class="col-md-6">
                        <div class="border rounded p-3 bg-light">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-file-earmark text-danger fs-3 me-3"></i>
                                    <div>
                                        <strong class="d-block">{{ $mapFile->caption ?? 'فایل نقشه' }}</strong>
                                        <small class="text-muted">{{ pathinfo($mapFile->file_path, PATHINFO_EXTENSION) }}</small>
                                    </div>
                                </div>
                                <a href="{{ Storage::url($mapFile->file_path) }}" class="btn btn-danger btn-sm" download>
                                    <i class="bi bi-download me-1"></i> دانلود
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- ملاحظات ضروری --}}
        @if($programReport->important_notes)
        <div class="card shadow-sm mb-4 border-start border-4 border-warning">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="bi bi-exclamation-triangle me-2"></i> ملاحظات ضروری</h5>
            </div>
            <div class="card-body">
                <div class="report-content">
                    {!! $programReport->important_notes !!}
                </div>
            </div>
        </div>
        @endif
    </div>
@endsection

@push('styles')
    <style>
        .report-container {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.6s ease-in;
        }

        .report-header {
            animation: slideDown 0.5s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .info-box {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .info-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
        }

        .report-content {
            line-height: 1.8;
        }

        .report-content img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin: 1rem 0;
        }

        @media (max-width: 768px) {
            .report-header .card-body {
                padding: 1.5rem !important;
            }

            .info-box {
                margin-bottom: 1rem;
            }

            .btn-group {
                width: 100%;
            }

            .btn-group .btn {
                flex: 1;
            }
        }
    </style>
@endpush
