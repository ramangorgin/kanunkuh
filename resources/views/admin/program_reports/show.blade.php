@extends('admin.layout')

@section('title', 'مشاهده گزارش برنامه')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.program_reports.index') }}">گزارش‌های برنامه</a></li>
            <li class="breadcrumb-item active" aria-current="page">مشاهده گزارش</li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="report-container">
        {{-- Header Section --}}
        <div class="card shadow-lg border-0 mb-4 report-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 15px;">
            <div class="card-body text-white p-4 p-md-5">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                    <div>
                        <h2 class="mb-2 fw-bold">
                            <i class="bi bi-file-text me-2"></i>
                            گزارش برنامه: {{ $programReport->program->name ?? '—' }}
                        </h2>
                        <p class="mb-0 opacity-75">
                            <i class="bi bi-calendar3 me-2"></i>
                            تاریخ ایجاد: {{ \Morilog\Jalali\Jalalian::fromCarbon($programReport->created_at)->format('Y/m/d') }}
                        </p>
                    </div>
                    <div class="btn-group">
                        <a href="{{ route('admin.program_reports.edit', $programReport->id) }}" class="btn btn-light btn-sm">
                            <i class="bi bi-pencil me-1"></i> ویرایش
                        </a>
                        <a href="{{ route('admin.program_reports.index') }}" class="btn btn-light btn-sm">
                            <i class="bi bi-arrow-right me-1"></i> بازگشت
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Report Description --}}
        @if($programReport->report_description)
        <div class="card shadow-sm mb-4 animate-fade-in">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i> شرح گزارش</h5>
            </div>
            <div class="card-body">
                <div class="report-content">
                    {!! $programReport->report_description !!}
                </div>
            </div>
        </div>
        @endif

        {{-- Technical Specifications --}}
        <div class="row g-4 mb-4">
            <div class="col-12">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="bi bi-tools me-2"></i> مشخصات فنی مسیر</h5>
                    </div>
                    <div class="card-body">
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

                            @if($programReport->participants_count)
                            <div class="col-md-6 col-lg-3">
                                <div class="info-box bg-light p-3 rounded text-center h-100">
                                    <i class="bi bi-people text-success fs-3 mb-2 d-block"></i>
                                    <strong class="d-block text-muted small">تعداد شرکت‌کنندگان</strong>
                                    <span class="text-dark mt-1">{{ $programReport->participants_count }} نفر</span>
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
            </div>
        </div>

        {{-- Natural Features --}}
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
                                    <span class="text-dark">{{ $programReport->wind_speed }} km/h</span>
                                </div>
                            </div>
                            @endif

                            @if($programReport->temperature)
                            <div class="col-6">
                                <div class="bg-light p-3 rounded text-center">
                                    <i class="bi bi-thermometer-half text-danger fs-4 mb-2 d-block"></i>
                                    <strong class="d-block text-muted small">دما</strong>
                                    <span class="text-dark">{{ $programReport->temperature }} °C</span>
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

        {{-- Geographic Information --}}
        @if($programReport->start_altitude || $programReport->target_altitude || $programReport->start_location_name || $programReport->distance_from_tehran || $programReport->road_type)
        <div class="card shadow-sm mb-4 border-start border-4 border-info">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="bi bi-geo-alt me-2"></i> اطلاعات جغرافیایی و مسیر</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @if($programReport->start_altitude)
                    <div class="col-md-4 col-sm-6">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-arrow-down-circle text-info fs-4 me-3"></i>
                            <div>
                                <strong class="d-block text-muted small">ارتفاع شروع</strong>
                                <span class="text-dark">{{ number_format($programReport->start_altitude) }} متر</span>
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
                                <span class="text-dark">{{ number_format($programReport->target_altitude) }} متر</span>
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
                                <span class="text-dark">{{ number_format($programReport->distance_from_tehran) }} کیلومتر</span>
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

        {{-- Important Notes --}}
        @if($programReport->important_notes)
        <div class="card shadow-sm mb-4 border-start border-4 border-warning">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="bi bi-exclamation-triangle me-2"></i> یادداشت‌های مهم</h5>
            </div>
            <div class="card-body">
                <p class="mb-0">{{ $programReport->important_notes }}</p>
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
        }
    </style>
@endpush

