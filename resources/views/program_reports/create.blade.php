{{-- Program report creation form. --}}
@php
    $isAdmin = $isAdmin ?? (auth()->check() && auth()->user()->role === 'admin');
    $currentUser = auth()->user();
@endphp

@extends($isAdmin ? 'admin.layout' : 'user.layout')

@section('title', 'ایجاد گزارش برنامه')

@if($isAdmin)
@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.program_reports.index') }}">گزارش‌های برنامه</a></li>
            <li class="breadcrumb-item active" aria-current="page">ایجاد گزارش جدید</li>
        </ol>
    </nav>
@endsection
@endif

@section('content')
    <div class="card shadow-sm">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i> ایجاد گزارش برنامه</h5>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ $isAdmin ? route('admin.program_reports.store') : route('program_reports.store', $program->id) }}" enctype="multipart/form-data" id="report-form">
                @csrf

                {{-- 1. اطلاعات کلی گزارش --}}
                <h5 class="mb-3 text-primary"><i class="bi bi-calendar-event me-2"></i> اطلاعات کلی گزارش</h5>
                
                @if(isset($program))
                    {{-- User creating report for specific program --}}
                    <input type="hidden" name="program_id" value="{{ $program->id }}">
                    <div class="alert alert-info mb-4">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>برنامه:</strong> {{ $program->name }}
                    </div>
                @else
                    {{-- Admin selecting program --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-12">
                            <label class="form-label">برنامه <span class="text-danger">*</span></label>
                            <select name="program_id" id="program-select" class="form-select" required>
                                <option value="">انتخاب برنامه</option>
                                @if(isset($programs) && $programs->count() > 0)
                                    @foreach($programs as $prog)
                                        <option value="{{ $prog->id }}" {{ old('program_id') == $prog->id ? 'selected' : '' }}>
                                            {{ $prog->name }} - {{ $prog->execution_date ? verta($prog->execution_date)->format('Y/m/d') : 'بدون تاریخ' }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @if(!isset($programs) || $programs->count() == 0)
                                <div class="alert alert-warning mt-2">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    هیچ برنامه‌ای با تاریخ اجرای گذشته یافت نشد.
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">تاریخ گزارش <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" name="report_date" id="report_date" class="form-control" data-jdp value="{{ old('report_date', verta()->format('Y/m/d')) }}" required autocomplete="off">
                            <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                        </div>
                    </div>
                </div>

                <hr>

                {{-- اطلاعات کلی برنامه (در گزارش) --}}
                <h5 class="mb-3 text-primary"><i class="bi bi-info-circle me-2"></i> اطلاعات کلی برنامه (در گزارش)</h5>
                <div class="row g-3 mb-4">
                    @php
                        $reportProgramType = old('report_program_type', isset($program) ? $program->program_type : '');
                        $reportProgramName = old('report_program_name', isset($program) ? $program->name : '');
                        $reportRegionRoute = old('report_region_route', isset($program) ? $program->region_name : '');
                        $reportStartDate = old('report_start_date', isset($program) && $program->execution_date ? verta($program->execution_date)->format('Y/m/d') : '');
                        $reportEndDate = old('report_end_date', isset($program) && $program->execution_date ? verta($program->execution_date)->format('Y/m/d') : '');
                    @endphp
                    <div class="col-md-4">
                        <label class="form-label">نوع برنامه</label>
                        <select name="report_program_type" class="form-select">
                            <option value="">انتخاب کنید</option>
                            <option value="کوهنوردی" {{ $reportProgramType == 'کوهنوردی' ? 'selected' : '' }}>کوهنوردی</option>
                            <option value="کوهپیمایی" {{ $reportProgramType == 'کوهپیمایی' ? 'selected' : '' }}>کوهپیمایی</option>
                            <option value="صخره‌نوردی" {{ $reportProgramType == 'صخره‌نوردی' ? 'selected' : '' }}>صخره‌نوردی</option>
                            <option value="یخ‌نوردی" {{ $reportProgramType == 'یخ‌نوردی' ? 'selected' : '' }}>یخ‌نوردی</option>
                            <option value="غارنوردی" {{ $reportProgramType == 'غارنوردی' ? 'selected' : '' }}>غارنوردی</option>
                            <option value="دیگر" {{ $reportProgramType == 'دیگر' ? 'selected' : '' }}>دیگر</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">نام برنامه</label>
                        <input type="text" name="report_program_name" class="form-control" value="{{ $reportProgramName }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">منطقه و مسیر برنامه</label>
                        <input type="text" name="report_region_route" class="form-control" value="{{ $reportRegionRoute }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">از تاریخ</label>
                        <div class="input-group">
                            <input type="text" name="report_start_date" id="report_start_date" class="form-control" data-jdp value="{{ $reportStartDate }}" autocomplete="off">
                            <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">تا تاریخ</label>
                        <div class="input-group">
                            <input type="text" name="report_end_date" id="report_end_date" class="form-control" data-jdp value="{{ $reportEndDate }}" autocomplete="off">
                            <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">مدت</label>
                        <input type="text" name="report_duration" id="report_duration" class="form-control" value="{{ old('report_duration') }}" placeholder="مثلاً: 3 روز" readonly>
                    </div>
                </div>

                <hr>

                {{-- مشخصات گزارشگر --}}
                <h5 class="mb-3 text-primary"><i class="bi bi-person-badge me-2"></i> مشخصات گزارشگر</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">نام و نام خانوادگی گزارشگر <span class="text-danger">*</span></label>
                        @if($isAdmin)
                            {{-- Admin can select user or enter manually --}}
                            <div class="mb-2">
                                <select name="reporter_id" id="reporter-select" class="form-select select2-user">
                                    <option value="">— انتخاب کاربر از سیستم —</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('reporter_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->full_name ?: $user->phone }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mt-2">
                                <label class="form-label small">یا وارد کردن دستی:</label>
                                <input type="text" name="reporter_name" id="reporter-name" class="form-control" value="{{ old('reporter_name') }}" placeholder="نام و نام خانوادگی گزارشگر">
                            </div>
                        @else
                            {{-- User: auto-fill with current user --}}
                            <input type="hidden" name="reporter_id" value="{{ $currentUser->id }}">
                            <input type="text" class="form-control" value="{{ $currentUser->full_name ?: $currentUser->phone }}" readonly disabled>
                            <small class="text-muted">گزارشگر: شما</small>
                        @endif
                    </div>
                </div>

                <hr>

                {{-- عوامل اجرایی برنامه --}}
                <h5 class="mb-3 text-primary"><i class="bi bi-people me-2"></i> عوامل اجرایی برنامه</h5>
                <div id="executive-roles-wrapper">
                    @php
                        // Get initial executive roles from program or old input
                        $initialExecutiveRoles = old('executive_roles', []);
                        
                        // If program is selected and no old input, use program's userRoles
                        if (isset($program) && $program && $program->userRoles && empty($initialExecutiveRoles)) {
                            $initialExecutiveRoles = $program->userRoles->map(function($role) {
                                return [
                                    'role_title' => $role->role_title,
                                    'user_id' => $role->user_id,
                                    'user_name' => $role->user_name,
                                ];
                            })->toArray();
                        }
                        
                        // If no roles exist, create one empty row
                        if (empty($initialExecutiveRoles)) {
                            $initialExecutiveRoles = [['role_title' => '', 'user_id' => '', 'user_name' => '']];
                        }
                    @endphp
                    @foreach($initialExecutiveRoles as $index => $role)
                        <div class="executive-role-row mb-3 border p-3 rounded">
                            <div class="row g-2 align-items-end">
                                <div class="col-md-4">
                                    <label class="form-label">سمت</label>
                                    <input type="text" name="executive_roles[{{ $index }}][role_title]" class="form-control" value="{{ $role['role_title'] ?? '' }}" placeholder="مثلاً: سرپرست یا پزشک تیم">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">نام و نام خانوادگی</label>
                                    <select name="executive_roles[{{ $index }}][user_id]" class="form-select user-select select2-user">
                                        <option value="">— انتخاب کاربر —</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ ($role['user_id'] ?? '') == $user->id ? 'selected' : '' }}>{{ $user->full_name ?: $user->phone }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">نام دستی (در صورت نبودن اکانت)</label>
                                    <input type="text" name="executive_roles[{{ $index }}][user_name]" class="form-control" value="{{ $role['user_name'] ?? '' }}" placeholder="مثلاً: علی رضایی">
                                </div>
                                <div class="col-md-12 mt-2 text-end">
                                    <button type="button" class="btn btn-danger btn-sm remove-executive-role">حذف</button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <button type="button" class="btn btn-outline-primary mt-2 mb-4" id="add-executive-role">افزودن عامل اجرایی</button>

                <hr>

                {{-- شرکت‌کنندگان --}}
                <h5 class="mb-3 text-primary"><i class="bi bi-people me-2"></i> شرکت‌کنندگان</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">تعداد نفرات شرکت‌کننده</label>
                        @php
                            $initialParticipantsCount = old('participants_count', '');
                            if ($initialParticipantsCount === '' && isset($approvedRegistrations)) {
                                $initialParticipantsCount = $approvedRegistrations->count();
                            }
                        @endphp
                        <input type="number" name="participants_count" class="form-control" value="{{ $initialParticipantsCount }}" min="0">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">اعضای شرکت‌کننده</label>
                        @php
                            $initialParticipantIds = old('participants', []);
                            if (empty($initialParticipantIds) && isset($approvedRegistrations) && $approvedRegistrations->count() > 0) {
                                $initialParticipantIds = $approvedRegistrations->pluck('user_id')->filter()->toArray();
                            }
                        @endphp
                        <select name="participants[]" id="participants-select" class="form-select select2-user" multiple>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ in_array($user->id, $initialParticipantIds) ? 'selected' : '' }}>
                                    {{ $user->full_name ?: $user->phone }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">می‌توانید چند عضو را انتخاب کنید</small>
                    </div>
                </div>

                <hr>

                {{-- مهمانان شرکت‌کننده --}}
                <h5 class="mb-3 text-primary"><i class="bi bi-person-plus me-2"></i> مهمانان شرکت‌کننده</h5>
                <div id="guests-wrapper">
                    @php
                        $initialGuests = old('guests', []);
                        if (empty($initialGuests) && isset($guestRegistrations) && $guestRegistrations->count() > 0) {
                            $initialGuests = $guestRegistrations->map(function($reg) {
                                return [
                                    'name' => $reg->guest_name,
                                    'phone' => $reg->guest_phone,
                                ];
                            })->toArray();
                        }
                    @endphp
                    @foreach($initialGuests as $index => $guest)
                        <div class="guest-row mb-3 border p-3 rounded">
                            <div class="row g-2 align-items-end">
                                <div class="col-md-5">
                                    <label class="form-label">نام مهمان</label>
                                    <input type="text" name="guests[{{ $index }}][name]" class="form-control" value="{{ $guest['name'] ?? '' }}" placeholder="نام و نام خانوادگی مهمان">
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label">شماره تماس مهمان</label>
                                    <input type="text" name="guests[{{ $index }}][phone]" class="form-control" value="{{ $guest['phone'] ?? '' }}" placeholder="مثلاً: 09123456789">
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-danger btn-sm remove-guest">حذف</button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <button type="button" class="btn btn-outline-primary mt-2 mb-4" id="add-guest">افزودن مهمان</button>

                <hr>

                {{-- اطلاعات ارتفاع و مسیر --}}
                <h5 class="mb-3 text-primary"><i class="bi bi-arrow-up-circle me-2"></i> اطلاعات ارتفاع و مسیر</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label">ارتفاع مبدا شروع برنامه (متر)</label>
                        <input type="number" name="start_altitude" class="form-control" value="{{ old('start_altitude') }}" min="0">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">ارتفاع قله یا منطقه موردنظر (متر)</label>
                        <input type="number" name="target_altitude" class="form-control" value="{{ old('target_altitude') }}" min="0">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">نام مبدا برنامه</label>
                        <input type="text" name="start_location_name" class="form-control" value="{{ old('start_location_name') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">مسافت از تهران تا مبدا (کیلومتر)</label>
                        <input type="number" name="distance_from_tehran" class="form-control" value="{{ old('distance_from_tehran') }}" min="0">
                    </div>
                </div>

                <hr>

                {{-- طول و عرض جغرافیایی نقاط --}}
                <h5 class="mb-3 text-primary"><i class="bi bi-geo me-2"></i> طول و عرض جغرافیایی نقاط</h5>
                <div id="geo-points-wrapper">
                    <div class="geo-point-row mb-3 border p-3 rounded">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label">نام نقطه</label>
                                <input type="text" name="geo_points[0][name]" class="form-control" placeholder="مثلاً: قله دماوند">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">عرض جغرافیایی (Latitude)</label>
                                <input type="text" name="geo_points[0][lat]" class="form-control" placeholder="35.9519">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">طول جغرافیایی (Longitude)</label>
                                <input type="text" name="geo_points[0][lon]" class="form-control" placeholder="52.1096">
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger btn-sm remove-geo-point">حذف</button>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-outline-primary mt-2 mb-4" id="add-geo-point">افزودن نقطه جغرافیایی</button>

                <hr>

                {{-- نوع جاده و حمل‌ونقل --}}
                <h5 class="mb-3 text-primary"><i class="bi bi-road me-2"></i> نوع جاده و حمل‌ونقل</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">نوع جاده</label>
                        <select name="road_type" class="form-select">
                            <option value="">انتخاب کنید</option>
                            <option value="آسفالت" {{ old('road_type') == 'آسفالت' ? 'selected' : '' }}>آسفالت</option>
                            <option value="خاکی" {{ old('road_type') == 'خاکی' ? 'selected' : '' }}>خاکی</option>
                            <option value="ترکیبی" {{ old('road_type') == 'ترکیبی' ? 'selected' : '' }}>ترکیبی</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">وضعیت حمل‌ونقل</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="transport_types[]" value="اتوبوس" id="transport-bus" {{ in_array('اتوبوس', old('transport_types', [])) ? 'checked' : '' }}>
                            <label class="form-check-label" for="transport-bus">اتوبوس</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="transport_types[]" value="مینی‌بوس" id="transport-minibus" {{ in_array('مینی‌بوس', old('transport_types', [])) ? 'checked' : '' }}>
                            <label class="form-check-label" for="transport-minibus">مینی‌بوس</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="transport_types[]" value="سواری" id="transport-car" {{ in_array('سواری', old('transport_types', [])) ? 'checked' : '' }}>
                            <label class="form-check-label" for="transport-car">سواری</label>
                        </div>
                    </div>
                </div>

                <hr>

                {{-- مشخصات فنی مسیر --}}
                <h5 class="mb-3 text-primary"><i class="bi bi-tools me-2"></i> مشخصات فنی مسیر</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-12">
                        <label class="form-label">وسایل فنی مورد نیاز</label>
                        @php
                            $initialTechnicalEquipments = old('technical_equipments', []);
                            if (empty($initialTechnicalEquipments) && isset($program) && $program->equipments) {
                                $initialTechnicalEquipments = $program->equipments;
                            }
                        $defaultTechnicalEquipments = [
                            'کرامپون',
                            'تبر یخ',
                            'هارنس',
                            'کلاه ایمنی',
                            'ریسمان',
                            'کارابین',
                            'طناب کمکی',
                            'کلنگ کوهستان',
                        ];
                        $technicalEquipmentOptions = collect($defaultTechnicalEquipments)
                            ->merge($initialTechnicalEquipments)
                            ->unique()
                            ->values();
                        @endphp
                        <select name="technical_equipments[]" id="technical-equipments" class="form-select select2-tags" multiple>
                            @foreach($technicalEquipmentOptions as $eq)
                                <option value="{{ $eq }}" {{ in_array($eq, $initialTechnicalEquipments) ? 'selected' : '' }}>{{ $eq }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">چند مورد پرکاربرد اضافه شد؛ می‌توانید موارد جدید بنویسید</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">سختی مسیر</label>
                        <select name="route_difficulty" class="form-select">
                            <option value="">انتخاب کنید</option>
                            <option value="آسان" {{ old('route_difficulty') == 'آسان' ? 'selected' : '' }}>آسان</option>
                            <option value="متوسط" {{ old('route_difficulty') == 'متوسط' ? 'selected' : '' }}>متوسط</option>
                            <option value="سخت" {{ old('route_difficulty') == 'سخت' ? 'selected' : '' }}>سخت</option>
                            <option value="بسیار سخت" {{ old('route_difficulty') == 'بسیار سخت' ? 'selected' : '' }}>بسیار سخت</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">شیب</label>
                        <input type="text" name="slope" class="form-control" value="{{ old('slope') }}" maxlength="50">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">درگیری با سنگ</label>
                        <select name="rock_engagement" class="form-select">
                            <option value="">انتخاب کنید</option>
                            <option value="کم" {{ old('rock_engagement') == 'کم' ? 'selected' : '' }}>کم</option>
                            <option value="متوسط" {{ old('rock_engagement') == 'متوسط' ? 'selected' : '' }}>متوسط</option>
                            <option value="زیاد" {{ old('rock_engagement') == 'زیاد' ? 'selected' : '' }}>زیاد</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">درگیری با یخ و یخچال</label>
                        <select name="ice_engagement" class="form-select">
                            <option value="">انتخاب کنید</option>
                            <option value="ندارد" {{ old('ice_engagement') == 'ندارد' ? 'selected' : '' }}>ندارد</option>
                            <option value="کم" {{ old('ice_engagement') == 'کم' ? 'selected' : '' }}>کم</option>
                            <option value="زیاد" {{ old('ice_engagement') == 'زیاد' ? 'selected' : '' }}>زیاد</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">میانگین وزن کوله‌ها (کیلوگرم)</label>
                        <input type="number" name="avg_backpack_weight" class="form-control" value="{{ old('avg_backpack_weight') }}" step="0.1" min="0" max="100">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">پیش‌نیاز برنامه</label>
                        <textarea name="prerequisites" class="form-control" rows="3">{{ old('prerequisites') }}</textarea>
                    </div>
                </div>

                <hr>

                {{-- ویژگی فنی برنامه --}}
                <h5 class="mb-3 text-primary"><i class="bi bi-star me-2"></i> ویژگی فنی برنامه</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">ویژگی فنی</label>
                        <select name="technical_feature" class="form-select">
                            <option value="">انتخاب کنید</option>
                            <option value="عمومی" {{ old('technical_feature') == 'عمومی' ? 'selected' : '' }}>عمومی</option>
                            <option value="تخصصی" {{ old('technical_feature') == 'تخصصی' ? 'selected' : '' }}>تخصصی</option>
                            <option value="خانوادگی" {{ old('technical_feature') == 'خانوادگی' ? 'selected' : '' }}>خانوادگی</option>
                        </select>
                    </div>
                </div>

                <hr>

                {{-- مشخصات طبیعی منطقه --}}
                <h5 class="mb-3 text-primary"><i class="bi bi-tree me-2"></i> مشخصات طبیعی منطقه</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">پوشش گیاهی</label>
                        <textarea name="vegetation" class="form-control" rows="3">{{ old('vegetation') }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">پوشش جانوری</label>
                        <textarea name="wildlife" class="form-control" rows="3">{{ old('wildlife') }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">آب و هوای منطقه</label>
                        <textarea name="weather" class="form-control" rows="3">{{ old('weather') }}</textarea>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">سرعت باد (km/h)</label>
                        <input type="number" name="wind_speed" class="form-control" value="{{ old('wind_speed') }}" min="0">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">دمای هوا (°C)</label>
                        <input type="number" name="temperature" class="form-control" value="{{ old('temperature') }}" step="0.1">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">زبان محلی</label>
                        <input type="text" name="local_language" class="form-control" value="{{ old('local_language') }}" maxlength="100">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">آثار باستانی و دیدنی‌ها</label>
                        <textarea name="attractions" class="form-control" rows="3">{{ old('attractions') }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">امکان تأمین مواد غذایی در منطقه</label>
                        <select name="food_supply" class="form-select">
                            <option value="">انتخاب کنید</option>
                            <option value="دارد" {{ old('food_supply') == 'دارد' ? 'selected' : '' }}>دارد</option>
                            <option value="ندارد" {{ old('food_supply') == 'ندارد' ? 'selected' : '' }}>ندارد</option>
                            <option value="محدود" {{ old('food_supply') == 'محدود' ? 'selected' : '' }}>محدود</option>
                        </select>
                    </div>
                </div>

                <hr>

                {{-- اطلاعات محلی --}}
                <h5 class="mb-3 text-primary"><i class="bi bi-geo-alt me-2"></i> اطلاعات محلی</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">نام محله یا روستا</label>
                        <input type="text" name="local_village_name" class="form-control" value="{{ old('local_village_name') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">نام راهنمای منطقه، آدرس و تلفن</label>
                        <textarea name="local_guide_info" class="form-control" rows="3">{{ old('local_guide_info') }}</textarea>
                    </div>
                    @php
                        $initialShelters = old('shelters', []);
                        if (empty($initialShelters)) {
                            $initialShelters = [['name' => '', 'height' => '']];
                        }
                    @endphp
                    <div class="col-md-12">
                        <label class="form-label d-flex justify-content-between align-items-center">
                            <span>اسامی پناهگاه‌ها / محل‌های اطراق + ارتفاع</span>
                            <button type="button" class="btn btn-outline-primary btn-sm" id="add-shelter">افزودن محل</button>
                        </label>
                        <div id="shelters-wrapper">
                            @foreach($initialShelters as $index => $shelter)
                                <div class="shelter-row mb-3 border p-3 rounded">
                                    <div class="row g-2 align-items-end">
                                        <div class="col-md-6">
                                            <label class="form-label">نام پناهگاه / محل</label>
                                            <input type="text" name="shelters[{{ $index }}][name]" class="form-control" value="{{ $shelter['name'] ?? '' }}" placeholder="مثلاً: پناهگاه بارگاه سوم">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">ارتفاع (متر)</label>
                                            <input type="number" name="shelters[{{ $index }}][height]" class="form-control" value="{{ $shelter['height'] ?? '' }}" min="0" placeholder="مثلاً: 4150">
                                        </div>
                                        <div class="col-md-2 text-end">
                                            <button type="button" class="btn btn-danger btn-sm remove-shelter">حذف</button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <hr>

                {{-- مشخصات رفاهی منطقه --}}
                <h5 class="mb-3 text-primary"><i class="bi bi-building me-2"></i> مشخصات رفاهی منطقه</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-12">
                        <label class="form-label">امکانات موجود</label>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="facilities[]" value="piped_water" id="facility-piped-water" {{ in_array('piped_water', old('facilities', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="facility-piped-water">آب لوله‌کشی</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="facilities[]" value="seasonal_spring" id="facility-seasonal-spring" {{ in_array('seasonal_spring', old('facilities', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="facility-seasonal-spring">چشمه فصلی</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="facilities[]" value="permanent_spring" id="facility-permanent-spring" {{ in_array('permanent_spring', old('facilities', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="facility-permanent-spring">چشمه دائم</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="facilities[]" value="school" id="facility-school" {{ in_array('school', old('facilities', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="facility-school">مدرسه</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="facilities[]" value="phone" id="facility-phone" {{ in_array('phone', old('facilities', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="facility-phone">تلفن</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="facilities[]" value="electricity" id="facility-electricity" {{ in_array('electricity', old('facilities', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="facility-electricity">برق</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="facilities[]" value="post" id="facility-post" {{ in_array('post', old('facilities', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="facility-post">پست</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="facilities[]" value="mobile_coverage" id="facility-mobile" {{ in_array('mobile_coverage', old('facilities', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="facility-mobile">آنتن‌دهی موبایل</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>

                {{-- زمان‌بندی اجرای برنامه --}}
                <h5 class="mb-3 text-primary"><i class="bi bi-clock me-2"></i> زمان‌بندی اجرای برنامه</h5>
                <div id="timeline-wrapper">
                    <div class="timeline-row mb-3 border p-3 rounded">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label">نام رویداد</label>
                                <input type="text" name="timeline[0][title]" class="form-control" placeholder="مثلاً: حرکت از تهران">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">تاریخ و ساعت</label>
                                <div class="input-group">
                                    <input type="text" name="timeline[0][datetime]" class="form-control timeline-datetime" data-jdp data-jdp-time="true" placeholder="1404/10/11 06:00" autocomplete="off">
                                    <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger btn-sm remove-timeline">حذف</button>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-outline-primary mt-2 mb-4" id="add-timeline">افزودن رویداد زمانی</button>

                <hr>

                {{-- شرح گزارش برنامه --}}
                <h5 class="mb-3 text-primary"><i class="bi bi-file-text me-2"></i> شرح گزارش برنامه</h5>
                <div class="mb-4">
                    <label class="form-label">شرح گزارش برنامه</label>
                    <textarea name="report_description" id="report_description" class="form-control" rows="10">{{ old('report_description') }}</textarea>
                </div>

                <hr>

                {{-- کروکی و نقشه --}}
                <h5 class="mb-3 text-primary"><i class="bi bi-map me-2"></i> کروکی و نقشه</h5>
                <div class="mb-4">
                    <div class="map-upload-container">
                        <div class="upload-area border rounded p-4 text-center mb-3" id="map-upload-area" style="cursor: pointer; background: #f8f9fa; transition: all 0.3s;">
                            <i class="bi bi-cloud-upload fs-1 text-primary d-block mb-2"></i>
                            <p class="mb-1 fw-bold">برای آپلود فایل کروکی / نقشه کلیک کنید</p>
                            <p class="text-muted small mb-0">هر فرمت فایل قابل قبول است</p>
                        </div>
                        <input type="file" name="map_file" id="map-file-input" class="d-none" accept="*/*">
                        <div id="map-file-preview" class="row g-3"></div>
                    </div>
                </div>

                <hr>

                {{-- ملاحظات ضروری --}}
                <h5 class="mb-3 text-primary"><i class="bi bi-exclamation-triangle me-2"></i> ملاحظات ضروری</h5>
                <div class="mb-4">
                    <label class="form-label">ملاحظات و توضیحات ضروری</label>
                    <textarea name="important_notes" id="important_notes" class="form-control" rows="5">{{ old('important_notes') }}</textarea>
                </div>

                <hr>

                {{-- تصاویر گزارش --}}
                <h5 class="mb-3 text-primary"><i class="bi bi-images me-2"></i> آپلود تصاویر گزارش</h5>
                <div class="mb-4">
                    <div class="image-upload-container">
                        <div class="upload-area border rounded p-4 text-center mb-3" id="upload-area" style="cursor: pointer; background: #f8f9fa; transition: all 0.3s;">
                            <i class="bi bi-cloud-upload fs-1 text-primary d-block mb-2"></i>
                            <p class="mb-1 fw-bold">برای آپلود تصویر کلیک کنید</p>
                            <p class="text-muted small mb-0">فرمت‌های مجاز: JPG, PNG, GIF | حداکثر اندازه: 2 مگابایت | حداکثر تعداد: 20 تصویر</p>
                        </div>
                        <input type="file" name="report_images[]" id="image-input" class="d-none" multiple accept="image/jpeg,image/png,image/gif">
                        <div id="image-preview" class="row g-3"></div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    @if($isAdmin)
                        <a href="{{ route('admin.program_reports.index') }}" class="btn btn-secondary">انصراف</a>
                    @else
                        <a href="{{ route('programs.index') }}" class="btn btn-secondary">انصراف</a>
                    @endif
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle me-2"></i> ثبت گزارش
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .image-upload-container, .map-upload-container {
        margin-bottom: 20px;
    }
    
    .upload-area:hover {
        background: #e9ecef !important;
        border-color: #667eea !important;
    }
    
    .image-preview-item, .map-preview-item {
        position: relative;
        margin-bottom: 15px;
    }
    
    .image-preview-item img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 8px;
        border: 2px solid #dee2e6;
    }
    
    .map-preview-item {
        padding: 15px;
        background: #f8f9fa;
        border: 2px solid #dee2e6;
        border-radius: 8px;
    }
    
    .map-preview-item .file-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .map-preview-item .file-icon {
        font-size: 2rem;
        color: #667eea;
    }
    
    .image-preview-item .remove-btn, .map-preview-item .remove-btn {
        position: absolute;
        top: 5px;
        left: 5px;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
    }
    
    @media (max-width: 768px) {
        .image-preview-item img {
            height: 150px;
        }
    }
</style>
@endpush

@push('scripts')
    <script src="https://cdn.ckeditor.com/ckeditor5/41.3.1/classic/ckeditor.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize CKEditor for report_description
            ClassicEditor
                .create(document.querySelector('#report_description'), {
                    language: 'fa'
                })
                .catch(error => {
                    console.error('CKEditor error:', error);
                });
            
            // Initialize CKEditor for important_notes
            ClassicEditor
                .create(document.querySelector('#important_notes'), {
                    language: 'fa'
                })
                .catch(error => {
                    console.error('CKEditor error:', error);
                });

            // Initialize Select2 for user selects
            $('.select2-user').select2({
                dir: "rtl",
                width: '100%',
                theme: 'bootstrap-5'
            });

            // Initialize Select2 with tags for technical equipments
            $('#technical-equipments').select2({
                tags: true,
                dir: "rtl",
                width: '100%',
                theme: 'bootstrap-5',
                tokenSeparators: [',', ' '],
                createTag: function (params) {
                    var term = $.trim(params.term);
                    if (term === '') {
                        return null;
                    }
                    return {
                        id: term,
                        text: term,
                        newTag: true
                    };
                }
            });

            // Toggle reporter name field based on selection
            @if($isAdmin)
            $('#reporter-select').on('change', function() {
                if ($(this).val()) {
                    $('#reporter-name').prop('disabled', true).val('');
                } else {
                    $('#reporter-name').prop('disabled', false);
                }
            });
            @endif

            // Add executive role
            @php
                $initialExecutiveRolesCount = isset($initialExecutiveRoles) ? count($initialExecutiveRoles) : 1;
            @endphp
            let executiveRoleIndex = {{ $initialExecutiveRolesCount }};
            $('#add-executive-role').on('click', function() {
                const newRow = $(`
                    <div class="executive-role-row mb-3 border p-3 rounded">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label">سمت</label>
                                <input type="text" name="executive_roles[${executiveRoleIndex}][role_title]" class="form-control" placeholder="مثلاً: سرپرست یا پزشک تیم">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">نام و نام خانوادگی</label>
                                <select name="executive_roles[${executiveRoleIndex}][user_id]" class="form-select user-select select2-user">
                                    <option value="">— انتخاب کاربر —</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->full_name ?: $user->phone }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">نام دستی (در صورت نبودن اکانت)</label>
                                <input type="text" name="executive_roles[${executiveRoleIndex}][user_name]" class="form-control" placeholder="مثلاً: علی رضایی">
                            </div>
                            <div class="col-md-12 mt-2 text-end">
                                <button type="button" class="btn btn-danger btn-sm remove-executive-role">حذف</button>
                            </div>
                        </div>
                    </div>
                `);
                $('#executive-roles-wrapper').append(newRow);
                
                // Initialize Select2 for new select
                newRow.find('.select2-user').select2({
                    dir: "rtl",
                    width: '100%',
                    theme: 'bootstrap-5'
                });
                
                executiveRoleIndex++;
            });

            // Remove executive role
            $(document).on('click', '.remove-executive-role', function() {
                $(this).closest('.executive-role-row').remove();
            });

            // Add geo point
            let geoPointIndex = 1;
            $('#add-geo-point').on('click', function() {
                const newRow = $(`
                    <div class="geo-point-row mb-3 border p-3 rounded">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label">نام نقطه</label>
                                <input type="text" name="geo_points[${geoPointIndex}][name]" class="form-control" placeholder="مثلاً: قله دماوند">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">عرض جغرافیایی (Latitude)</label>
                                <input type="text" name="geo_points[${geoPointIndex}][lat]" class="form-control" placeholder="35.9519">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">طول جغرافیایی (Longitude)</label>
                                <input type="text" name="geo_points[${geoPointIndex}][lon]" class="form-control" placeholder="52.1096">
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger btn-sm remove-geo-point">حذف</button>
                            </div>
                        </div>
                    </div>
                `);
                $('#geo-points-wrapper').append(newRow);
                geoPointIndex++;
            });

            // Remove geo point
            $(document).on('click', '.remove-geo-point', function() {
                $(this).closest('.geo-point-row').remove();
            });

            // Add timeline
            let timelineIndex = 1;
            $('#add-timeline').on('click', function() {
                const newRow = $(`
                    <div class="timeline-row mb-3 border p-3 rounded">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label">نام رویداد</label>
                                <input type="text" name="timeline[${timelineIndex}][title]" class="form-control" placeholder="مثلاً: حرکت از تهران">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">تاریخ و ساعت</label>
                                <div class="input-group">
                                    <input type="text" name="timeline[${timelineIndex}][datetime]" class="form-control timeline-datetime" data-jdp data-jdp-time="true" placeholder="1404/10/11 06:00" autocomplete="off">
                                    <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger btn-sm remove-timeline">حذف</button>
                            </div>
                        </div>
                    </div>
                `);
                $('#timeline-wrapper').append(newRow);
                
                // Initialize jalali datepicker for new timeline field
                jalaliDatepicker.startWatch();
                
                timelineIndex++;
            });

            // Remove timeline
            $(document).on('click', '.remove-timeline', function() {
                $(this).closest('.timeline-row').remove();
            });

            // Add shelter row
            let shelterIndex = $('#shelters-wrapper .shelter-row').length;
            $('#add-shelter').on('click', function() {
                const newRow = $(`
                    <div class="shelter-row mb-3 border p-3 rounded">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-6">
                                <label class="form-label">نام پناهگاه / محل</label>
                                <input type="text" name="shelters[${shelterIndex}][name]" class="form-control" placeholder="مثلاً: پناهگاه بارگاه سوم">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">ارتفاع (متر)</label>
                                <input type="number" name="shelters[${shelterIndex}][height]" class="form-control" min="0" placeholder="مثلاً: 4150">
                            </div>
                            <div class="col-md-2 text-end">
                                <button type="button" class="btn btn-danger btn-sm remove-shelter">حذف</button>
                            </div>
                        </div>
                    </div>
                `);
                $('#shelters-wrapper').append(newRow);
                shelterIndex++;
            });

            // Remove shelter row
            $(document).on('click', '.remove-shelter', function() {
                $(this).closest('.shelter-row').remove();
            });

            // Auto-calc duration based on start/end dates
            function toEnglishDigits(str) {
                return str
                    .replace(/[۰-۹]/g, d => '۰۱۲۳۴۵۶۷۸۹'.indexOf(d))
                    .replace(/[٠-٩]/g, d => '٠١٢٣٤٥٦٧٨٩'.indexOf(d));
            }

            function jalaliToGregorian(jy, jm, jd) {
                const gy = jy + 621;
                const days = [0,31,59,90,120,151,181,212,243,273,304,334];
                const gy2 = gy + 1;
                let doyJ = (jm <= 6) ? ((jm - 1) * 31 + jd) : (days[jm - 1] + jd + (jm - 7) * 30);
                const leapJ = (jy % 33 === 1 || jy % 33 === 5 || jy % 33 === 9 || jy % 33 === 13 || jy % 33 === 17 || jy % 33 === 22 || jy % 33 === 26 || jy % 33 === 30);
                const march = leapJ ? 20 : 21;
                let dG = doyJ + march - 1;
                const leapG = (gy % 4 === 0 && gy % 100 !== 0) || (gy % 400 === 0);
                const leapG2 = (gy2 % 4 === 0 && gy2 % 100 !== 0) || (gy2 % 400 === 0);
                const monthsG = [31, leapG ? 29 : 28,31,30,31,30,31,31,30,31,30,31];
                let gm = 0;
                while (gm < 12 && dG >= monthsG[gm]) {
                    dG -= monthsG[gm];
                    gm++;
                }
                const gd = dG + 1;
                return new Date(gy, gm, gd);
            }

            function jalaliStringToDate(value) {
                if (!value) return null;
                const clean = toEnglishDigits(value.trim());
                const parts = clean.split('/');
                if (parts.length !== 3) return null;
                const [jy, jm, jd] = parts.map(Number);
                if (isNaN(jy) || isNaN(jm) || isNaN(jd)) return null;
                return jalaliToGregorian(jy, jm, jd);
            }

            function updateDuration() {
                const startVal = $('#report_start_date').val();
                const endVal = $('#report_end_date').val();
                const startDate = jalaliStringToDate(startVal);
                const endDate = jalaliStringToDate(endVal);
                if (!startDate || !endDate || endDate < startDate) {
                    $('#report_duration').val('');
                    return;
                }
                const diffDays = Math.floor((endDate - startDate) / (1000 * 60 * 60 * 24)) + 1;
                $('#report_duration').val(diffDays + ' روز');
            }

            $('#report_start_date, #report_end_date').on('change blur', updateDuration);
            updateDuration();

            // Add guest
            @php
                $initialGuestsCount = isset($initialGuests) ? count($initialGuests) : 0;
            @endphp
            let guestIndex = {{ $initialGuestsCount }};
            $('#add-guest').on('click', function() {
                const newRow = $(`
                    <div class="guest-row mb-3 border p-3 rounded">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-5">
                                <label class="form-label">نام مهمان</label>
                                <input type="text" name="guests[${guestIndex}][name]" class="form-control" placeholder="نام و نام خانوادگی مهمان">
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">شماره تماس مهمان</label>
                                <input type="text" name="guests[${guestIndex}][phone]" class="form-control" placeholder="مثلاً: 09123456789">
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger btn-sm remove-guest">حذف</button>
                            </div>
                        </div>
                    </div>
                `);
                $('#guests-wrapper').append(newRow);
                guestIndex++;
            });

            // Remove guest
            $(document).on('click', '.remove-guest', function() {
                $(this).closest('.guest-row').remove();
            });

            // Map file upload handling (single file)
            const mapUploadArea = document.getElementById('map-upload-area');
            const mapFileInput = document.getElementById('map-file-input');
            const mapFilePreview = document.getElementById('map-file-preview');
            let mapFile = null;

            mapUploadArea.addEventListener('click', () => {
                mapFileInput.click();
            });

            mapFileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                
                if (!file) {
                    return;
                }

                mapFile = file;
                
                // Clear previous preview
                mapFilePreview.innerHTML = '';
                
                // Create preview
                const div = document.createElement('div');
                div.className = 'col-md-6 map-preview-item';
                div.innerHTML = `
                    <div class="file-info">
                        <i class="bi ${getFileIcon(file.name)} file-icon"></i>
                        <div class="flex-grow-1">
                            <p class="mb-1 fw-bold">${file.name}</p>
                            <p class="mb-0 text-muted small">${formatFileSize(file.size)}</p>
                        </div>
                        <button type="button" class="btn btn-danger btn-sm remove-btn remove-map-file">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                `;
                mapFilePreview.appendChild(div);

                const dt = new DataTransfer();
                dt.items.add(file);
                mapFileInput.files = dt.files;
            });

            // Remove map file
            $(document).on('click', '.remove-map-file', function() {
                mapFile = null;
                mapFileInput.value = '';
                mapFilePreview.innerHTML = '';
            });

            // Helper function to get file icon based on extension
            function getFileIcon(filename) {
                const ext = filename.split('.').pop().toLowerCase();
                const iconMap = {
                    'pdf': 'bi-file-pdf-fill text-danger',
                    'doc': 'bi-file-word-fill text-primary',
                    'docx': 'bi-file-word-fill text-primary',
                    'xls': 'bi-file-excel-fill text-success',
                    'xlsx': 'bi-file-excel-fill text-success',
                    'jpg': 'bi-file-image-fill text-info',
                    'jpeg': 'bi-file-image-fill text-info',
                    'png': 'bi-file-image-fill text-info',
                    'gif': 'bi-file-image-fill text-info',
                    'zip': 'bi-file-zip-fill text-warning',
                    'rar': 'bi-file-zip-fill text-warning'
                };
                return iconMap[ext] || 'bi-file-earmark text-secondary';
            }

            // Helper function to format file size
            function formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
            }

            // Image upload handling
            const uploadArea = document.getElementById('upload-area');
            const imageInput = document.getElementById('image-input');
            const imagePreview = document.getElementById('image-preview');
            let imageFiles = [];

            uploadArea.addEventListener('click', () => {
                imageInput.click();
            });

            imageInput.addEventListener('change', function(e) {
                const files = Array.from(e.target.files);
                
                // Check total count
                if (imageFiles.length + files.length > 20) {
                    toastr.error('حداکثر 20 تصویر مجاز است');
                    return;
                }

                files.forEach(file => {
                    // Check file size (2MB)
                    if (file.size > 2 * 1024 * 1024) {
                        toastr.error(`فایل ${file.name} بزرگتر از 2 مگابایت است`);
                        return;
                    }

                    // Check file type
                    if (!file.type.match('image.*')) {
                        toastr.error(`فایل ${file.name} یک تصویر معتبر نیست`);
                        return;
                    }

                    imageFiles.push(file);
                });

                rebuildImagePreview();
            });

            // Remove image
            $(document).on('click', '.remove-image', function() {
                const index = $(this).data('index');
                imageFiles.splice(index, 1);
                rebuildImagePreview();
            });

            function rebuildImagePreview() {
                imagePreview.innerHTML = '';
                imageFiles.forEach((file, index) => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const div = document.createElement('div');
                        div.className = 'col-md-3 col-sm-6 image-preview-item';
                        div.innerHTML = `
                            <img src="${e.target.result}" alt="Preview">
                            <button type="button" class="btn btn-danger btn-sm remove-btn remove-image" data-index="${index}">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        `;
                        imagePreview.appendChild(div);
                    };
                    reader.readAsDataURL(file);
                });

                const dt = new DataTransfer();
                imageFiles.forEach(file => dt.items.add(file));
                imageInput.files = dt.files;
            }

            // Jalali datepicker options: only timeline uses time picker
            $('#report_date, #report_start_date, #report_end_date').on('focus', function() {
                jalaliDatepicker.updateOptions({ time: false, zIndex: 3000 });
            });

            $(document).on('focus', '.timeline-datetime', function() {
                jalaliDatepicker.updateOptions({ time: true, zIndex: 3000 });
            });
        });
    </script>
@endpush
