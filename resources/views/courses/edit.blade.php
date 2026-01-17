@extends('admin.layout')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.courses.index') }}">دوره‌ها</a></li>
            <li class="breadcrumb-item active" aria-current="page">ویرایش دوره</li>
        </ol>
    </nav>
@endsection

@section('content')
    <h3>ویرایش دوره</h3>

    <form method="POST" action="{{ route('admin.courses.update', $course->id) }}">
        @csrf
        @method('PUT')

        <div class="mb-2">
            <label>عنوان دوره</label>
            <input type="text" name="title" class="form-control" value="{{ $course->title }}" required>
        </div>

        <div class="mb-2">
            <label>توضیحات</label>
            <textarea name="description" class="form-control ckeditor">{{ $course->description }}</textarea>
        </div>

        <div class="mb-2">
            <label>نام مدرس</label>
            <input type="text" name="teacher" class="form-control" value="{{ $course->teacher }}">
        </div>

        <div class="row">
            <div class="col-md-6 mb-2">
                <label>تاریخ شروع</label>
                <div class="input-group">
                    <input type="text" name="start_date" class="form-control" data-jdp value="{{ $course->start_date }}">
                    <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                </div>
            </div>
            <div class="col-md-6 mb-2">
                <label>تاریخ پایان</label>
                <div class="input-group">
                    <input type="text" name="end_date" class="form-control" data-jdp value="{{ $course->end_date }}">
                    <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-2">
                <label>ساعت شروع</label>
                <input type="time" name="start_time" class="form-control" value="{{ $course->start_time }}">
            </div>
            <div class="col-md-6 mb-2">
                <label>ساعت پایان</label>
                <input type="time" name="end_time" class="form-control" value="{{ $course->end_time }}">
            </div>
        </div>

        <div class="mb-2">
            <label>محل برگزاری</label>
            <input type="text" name="place" class="form-control" value="{{ $course->place }}">
        </div>

        <div class="mb-2">
            <label>موقعیت روی نقشه</label>
            <div id="map" style="height: 300px;"></div>
            <input type="hidden" name="place-lat" id="lat" value="{{ $course->{'place-lat'} }}">
            <input type="hidden" name="place-lon" id="lon" value="{{ $course->{'place-lon'} }}">
        </div>

        <div class="mb-2">
            <label>ظرفیت</label>
            <input type="number" name="capacity" class="form-control" value="{{ $course->capacity }}">
        </div>

        <div class="mb-2">
            <label>آیا رایگان است؟</label>
            <select name="is_free" id="is_free" class="form-control">
                <option value="1" @if($course->is_free) selected @endif>بله</option>
                <option value="0" @if(!$course->is_free) selected @endif>خیر</option>
            </select>
        </div>

        <div id="cost_section">
            <div class="mb-2">
                <label>هزینه عضو</label>
                <input type="number" name="member_cost" class="form-control" value="{{ $course->member_cost }}">
            </div>

            <div class="mb-2">
                <label>هزینه مهمان</label>
                <input type="number" name="guest_cost" class="form-control" value="{{ $course->guest_cost }}">
            </div>

            <div class="mb-2">
                <label>شماره کارت</label>
                <input type="text" name="card_number" class="form-control" value="{{ $course->card_number }}">
            </div>

            <div class="mb-2">
                <label>شماره شبا</label>
                <input type="text" name="sheba_number" class="form-control" value="{{ $course->sheba_number }}">
            </div>

            <div class="mb-2">
                <label>نام دارنده کارت</label>
                <input type="text" name="card_holder" class="form-control" value="{{ $course->card_holder }}">
            </div>

            <div class="mb-2">
                <label>نام بانک</label>
                <input type="text" name="bank_name" class="form-control" value="{{ $course->bank_name }}">
            </div>
        </div>

        <div class="mb-2">
            <label>ثبت‌نام باز است؟</label>
            <select name="is_registration_open" id="is_registration_open" class="form-control">
                <option value="1" @if($course->is_registration_open) selected @endif>بله</option>
                <option value="0" @if(!$course->is_registration_open) selected @endif>خیر</option>
            </select>
        </div>

        <div class="mb-2" id="deadline_section">
            <label>مهلت ثبت‌نام</label>
            <div class="input-group">
                <input type="text" name="registration_deadline" class="form-control" data-jdp value="{{ $course->registration_deadline }}">
                <span class="input-group-text"><i class="bi bi-calendar"></i></span>
            </div>
        </div>

        <button class="btn btn-primary mt-3" style="width: 100%;">ذخیره تغییرات</button>
    </form>
@endsection

@section('scripts')
<!--
<script src="https://cdn.ckeditor.com/4.20.2/standard/ckeditor.js"></script> -->
<script>
document.addEventListener('DOMContentLoaded', function(){
    if (window.jalaliDatepicker?.startWatch) {
        jalaliDatepicker.startWatch({ persianDigits: true });
    }
});
</script>
<!--
<script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
-->

<script>
    $(document).ready(function () {
        CKEDITOR.replace('description');
        // date pickers handled by jalaliDatepicker

        var lat = {{ $course->{'place-lat'} ?? 35.6892 }};
        var lon = {{ $course->{'place-lon'} ?? 51.3890 }};
        var map = L.map('map').setView([lat, lon], 10);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        var marker = L.marker([lat, lon]).addTo(map);
        map.on('click', function(e) {
            if (marker) map.removeLayer(marker);
            marker = L.marker(e.latlng).addTo(map);
            $('#lat').val(e.latlng.lat);
            $('#lon').val(e.latlng.lng);
        });

        function toggleCostSection() {
            $('#cost_section').toggle($('#is_free').val() == '0');
        }
        $('#is_free').change(toggleCostSection);
        toggleCostSection();

        function toggleDeadlineSection() {
            $('#deadline_section').toggle($('#is_registration_open').val() == '1');
        }
        $('#is_registration_open').change(toggleDeadlineSection);
        toggleDeadlineSection();
    });
</script>
@endsection
