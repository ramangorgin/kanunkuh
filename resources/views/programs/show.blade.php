@extends('layout')

@section('content')
<div class="container my-5">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
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
                        <i class="bi bi-mountain text-success fs-4 me-3"></i>
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
                                    {{ \Morilog\Jalali\Jalalian::fromCarbon($program->execution_date)->format('Y/m/d H:i') }}
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
                                {{ \Morilog\Jalali\Jalalian::fromCarbon($program->register_deadline)->format('Y/m/d H:i') }}
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            @if($program->rules)
            <hr>
            <h5 class="mb-3">قوانین و شرایط</h5>
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

            @if($program->move_from_tehran || $program->move_from_karaj)
            <hr>
            <h5 class="mb-3">اطلاعات حرکت</h5>
            @if($program->move_from_tehran)
            <div class="mb-2">
                <strong>حرکت از تهران:</strong> {{ $program->move_from_tehran }}
            </div>
            @endif
            @if($program->move_from_karaj)
            <div class="mb-2">
                <strong>حرکت از کرج:</strong> {{ $program->move_from_karaj }}
            </div>
            @endif
            @endif

            @if($program->userRoles->count() > 0)
            <hr>
            <h5 class="mb-3">مسئولین برنامه</h5>
            <div class="row g-3">
                @foreach($program->userRoles as $role)
                    <div class="col-md-4 col-sm-6">
                        <div class="card border">
                            <div class="card-body text-center">
                                <h6 class="card-title">{{ $role->role_title }}</h6>
                                <p class="card-text mb-0">
                                    @if($role->user)
                                        {{ $role->user->phone }}
                                    @elseif($role->user_name)
                                        {{ $role->user_name }}
                                    @else
                                        —
                                    @endif
                                </p>
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
    @endphp

    <div class="text-center my-5">
        @if($program->register_deadline && !$registerDeadlinePassed)
            <div class="alert alert-success mb-4">
                <i class="bi bi-check-circle-fill me-2"></i>
                <strong>ثبت‌نام باز است</strong>
                <p class="mb-0 mt-2">مهلت ثبت‌نام تا {{ \Morilog\Jalali\Jalalian::fromCarbon($program->register_deadline)->format('Y/m/d H:i') }} می‌باشد.</p>
            </div>
            <a href="#" class="btn btn-primary btn-lg">
                <i class="bi bi-pencil-square me-2"></i> ثبت‌نام در برنامه
            </a>
        @elseif($registerDeadlinePassed)
            <div class="alert alert-warning mb-4">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <strong>مهلت ثبت‌نام به پایان رسیده است</strong>
            </div>
        @endif

        @if($executionDatePassed && $hasReport)
            <a href="{{ route('admin.program_reports.show', $program->report->id) }}" class="btn btn-success btn-lg">
                <i class="bi bi-file-text me-2"></i> مشاهده گزارش برنامه
            </a>
        @endif
    </div>

    @auth
        @if($userHasParticipated && !$userHasSubmittedSurvey)
            <div class="text-center mt-4">
                <a href="{{ route('surveys.program.form', ['program' => $program->id]) }}" class="btn btn-primary">
                    تکمیل فرم نظرسنجی برنامه
                </a>
            </div>
        @elseif($userHasSubmittedSurvey)
            <div class="alert alert-success text-center mt-4">
                شما قبلاً در این نظرسنجی شرکت کرده‌اید. با تشکر!
            </div>
        @endif
    @endauth
</div>
@endsection
