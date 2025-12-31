@extends('admin.layout')

@section('title', 'ویرایش گزارش برنامه')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.program_reports.index') }}">گزارش‌های برنامه</a></li>
            <li class="breadcrumb-item active" aria-current="page">ویرایش گزارش</li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="card shadow-sm">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i> ویرایش گزارش برنامه</h5>
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

            <form method="POST" action="{{ route('admin.program_reports.update', $programReport) }}">
                @csrf
                @method('PUT')

                <div class="row g-3 mb-4">
                    <div class="col-md-12">
                        <label class="form-label">برنامه <span class="text-danger">*</span></label>
                        <select name="program_id" class="form-select" required>
                            <option value="">انتخاب برنامه</option>
                            @foreach($programs as $prog)
                                <option value="{{ $prog->id }}" {{ old('program_id', $programReport->program_id) == $prog->id ? 'selected' : '' }}>
                                    {{ $prog->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <hr>

                <h5 class="mb-3">شرح گزارش</h5>
                <div class="mb-4">
                    <label class="form-label">توضیحات گزارش</label>
                    <textarea name="report_description" id="report_description" class="form-control" rows="10">{{ old('report_description', $programReport->report_description) }}</textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label">یادداشت‌های مهم</label>
                    <textarea name="important_notes" class="form-control" rows="5">{{ old('important_notes', $programReport->important_notes) }}</textarea>
                </div>

                <hr>

                <h5 class="mb-3">مشخصات فنی مسیر</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">سختی مسیر</label>
                        <select name="route_difficulty" class="form-select">
                            <option value="">انتخاب کنید</option>
                            <option value="آسان" {{ old('route_difficulty', $programReport->route_difficulty) == 'آسان' ? 'selected' : '' }}>آسان</option>
                            <option value="متوسط" {{ old('route_difficulty', $programReport->route_difficulty) == 'متوسط' ? 'selected' : '' }}>متوسط</option>
                            <option value="سخت" {{ old('route_difficulty', $programReport->route_difficulty) == 'سخت' ? 'selected' : '' }}>سخت</option>
                            <option value="بسیار سخت" {{ old('route_difficulty', $programReport->route_difficulty) == 'بسیار سخت' ? 'selected' : '' }}>بسیار سخت</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">شیب</label>
                        <input type="text" name="slope" class="form-control" value="{{ old('slope', $programReport->slope) }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">درگیری با سنگ</label>
                        <select name="rock_engagement" class="form-select">
                            <option value="">انتخاب کنید</option>
                            <option value="کم" {{ old('rock_engagement', $programReport->rock_engagement) == 'کم' ? 'selected' : '' }}>کم</option>
                            <option value="متوسط" {{ old('rock_engagement', $programReport->rock_engagement) == 'متوسط' ? 'selected' : '' }}>متوسط</option>
                            <option value="زیاد" {{ old('rock_engagement', $programReport->rock_engagement) == 'زیاد' ? 'selected' : '' }}>زیاد</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">درگیری با یخ</label>
                        <select name="ice_engagement" class="form-select">
                            <option value="">انتخاب کنید</option>
                            <option value="ندارد" {{ old('ice_engagement', $programReport->ice_engagement) == 'ندارد' ? 'selected' : '' }}>ندارد</option>
                            <option value="کم" {{ old('ice_engagement', $programReport->ice_engagement) == 'کم' ? 'selected' : '' }}>کم</option>
                            <option value="زیاد" {{ old('ice_engagement', $programReport->ice_engagement) == 'زیاد' ? 'selected' : '' }}>زیاد</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">میانگین وزن کوله (کیلوگرم)</label>
                        <input type="number" name="avg_backpack_weight" class="form-control" value="{{ old('avg_backpack_weight', $programReport->avg_backpack_weight) }}" step="0.1" min="0">
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">پیش‌نیازها</label>
                        <textarea name="prerequisites" class="form-control" rows="3">{{ old('prerequisites', $programReport->prerequisites) }}</textarea>
                    </div>
                </div>

                <hr>

                <h5 class="mb-3">مشخصات طبیعی</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">پوشش گیاهی</label>
                        <textarea name="vegetation" class="form-control" rows="3">{{ old('vegetation', $programReport->vegetation) }}</textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">حیات وحش</label>
                        <textarea name="wildlife" class="form-control" rows="3">{{ old('wildlife', $programReport->wildlife) }}</textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">آب و هوا</label>
                        <textarea name="weather" class="form-control" rows="3">{{ old('weather', $programReport->weather) }}</textarea>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">سرعت باد (km/h)</label>
                        <input type="number" name="wind_speed" class="form-control" value="{{ old('wind_speed', $programReport->wind_speed) }}" min="0">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">دما (°C)</label>
                        <input type="number" name="temperature" class="form-control" value="{{ old('temperature', $programReport->temperature) }}" step="0.1">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">زبان محلی</label>
                        <input type="text" name="local_language" class="form-control" value="{{ old('local_language', $programReport->local_language) }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">جاذبه‌ها</label>
                        <textarea name="attractions" class="form-control" rows="3">{{ old('attractions', $programReport->attractions) }}</textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">تأمین غذا</label>
                        <select name="food_supply" class="form-select">
                            <option value="">انتخاب کنید</option>
                            <option value="دارد" {{ old('food_supply', $programReport->food_supply) == 'دارد' ? 'selected' : '' }}>دارد</option>
                            <option value="ندارد" {{ old('food_supply', $programReport->food_supply) == 'ندارد' ? 'selected' : '' }}>ندارد</option>
                            <option value="محدود" {{ old('food_supply', $programReport->food_supply) == 'محدود' ? 'selected' : '' }}>محدود</option>
                        </select>
                    </div>
                </div>

                <hr>

                <h5 class="mb-3">اطلاعات جغرافیایی و مسیر</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label">ارتفاع شروع (متر)</label>
                        <input type="number" name="start_altitude" class="form-control" value="{{ old('start_altitude', $programReport->start_altitude) }}" min="0">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">ارتفاع هدف (متر)</label>
                        <input type="number" name="target_altitude" class="form-control" value="{{ old('target_altitude', $programReport->target_altitude) }}" min="0">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">نام محل شروع</label>
                        <input type="text" name="start_location_name" class="form-control" value="{{ old('start_location_name', $programReport->start_location_name) }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">فاصله از تهران (کیلومتر)</label>
                        <input type="number" name="distance_from_tehran" class="form-control" value="{{ old('distance_from_tehran', $programReport->distance_from_tehran) }}" min="0">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">نوع جاده</label>
                        <select name="road_type" class="form-select">
                            <option value="">انتخاب کنید</option>
                            <option value="آسفالت" {{ old('road_type', $programReport->road_type) == 'آسفالت' ? 'selected' : '' }}>آسفالت</option>
                            <option value="خاکی" {{ old('road_type', $programReport->road_type) == 'خاکی' ? 'selected' : '' }}>خاکی</option>
                            <option value="ترکیبی" {{ old('road_type', $programReport->road_type) == 'ترکیبی' ? 'selected' : '' }}>ترکیبی</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">تعداد شرکت‌کنندگان</label>
                        <input type="number" name="participants_count" class="form-control" value="{{ old('participants_count', $programReport->participants_count) }}" min="0">
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.program_reports.index') }}" class="btn btn-secondary">انصراف</a>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle me-2"></i> ذخیره تغییرات
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.ckeditor.com/ckeditor5/41.3.1/classic/ckeditor.js"></script>
    <script>
        ClassicEditor
            .create(document.querySelector('#report_description'), {
                language: 'fa'
            })
            .catch(error => {
                console.error(error);
            });
    </script>
@endpush

