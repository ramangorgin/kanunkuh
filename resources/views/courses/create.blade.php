@extends('admin.layout')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.courses.index') }}">دوره‌ها</a></li>
            <li class="breadcrumb-item active" aria-current="page">ایجاد دوره جدید</li>
        </ol>
    </nav>
@endsection

@section('content')
    <h3>ایجاد دوره جدید</h3>

    <form method="POST" action="{{ route('admin.courses.store') }}">
        @csrf

        <div class="mb-2">
            <label>عنوان دوره</label>
            <input type="text" name="title" class="form-control" required>
        </div>


        <div class="mb-2">
            <label>نام مدرس</label>
            <input type="text" name="teacher" class="form-control">
        </div>

        <div class="row">
            <div class="col-md-6 mb-2">
                <label>تاریخ شروع</label>
                <input type="text" id="start_date" name="start_date" class="form-control" required>
          </div>
            <div class="col-md-6 mb-2">
                <label>تاریخ پایان</label>
                <input type="text" id="end_date" name="end_date" class="form-control" required>
             </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-2">
                <label>ساعت شروع</label>
                <input type="time" name="start_time" class="form-control">
            </div>
            <div class="col-md-6 mb-2">
                <label>ساعت پایان</label>
                <input type="time" name="end_time" class="form-control">
            </div>
        </div>

        <div class="mb-2">
            <label>محل برگزاری</label>
            <input type="text" name="place" class="form-control">
        </div>

        <div class="mb-2" style="height: 400px;">
            <label>موقعیت روی نقشه</label>
            <div id="map" style="height: 300px;"></div>
            <input type="hidden" name="place-lat" id="lat">
            <input type="hidden" name="place-lon" id="lon">
        </div>

        <div class="mb-2">
            <label>ظرفیت</label>
            <input type="number" name="capacity" class="form-control">
        </div>

        <div>
            <hr>
            <h5 class="mb-3" >هزینه</h5>
             {{-- رایگان بودن --}}
            <div class="mb-2">
                <label>آیا برنامه رایگان است؟</label>
                <select name="is_free" id="is_free" class="form-control">
                    <option value="1">بله</option>
                    <option value="0">خیر</option>
                </select>
            </div>
            <div id="pay_section">
               

                <div class="row">
                    <div class="col-md-6">
                        <label>هزینه برای اعضا</label>
                        <div class="input-group">
                            <input type="number" name="member_price" class="form-control">
                            <div class="input-group-append">
                                <span class="input-group-text">ریال</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label>هزینه برای مهمان</label>
                        <div class="input-group">
                            <input type="number" name="guest_price" class="form-control">
                            <div class="input-group-append">
                                <span class="input-group-text">ریال</span>
                            </div>
                        </div>
                    </div>
                </div>
           
       
       
                <hr>
                <h5>اطلاعات کارت بانکی</h5>
                
                <div class="row mt-3">
                    <div class="col-md-6">
                        <label>شماره کارت</label>
                        <input type="text" name="card_number" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label>شماره شبا</label>
                        <input type="text" name="sheba_number" class="form-control">
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <label>نام دارنده کارت</label>
                        <input type="text" name="card_holder" class="form-control">
                    </div>
                    <div class="col-md-6">
                            <label>نام بانک</label>
                            <input type="text" name="bank_name" class="form-control">
                    </div>
                </div>

            </div>
        </div>

        <div>
            <hr>
            <h5 class="mb-3">ثبت‌نام</h5>
            <div class="row">
                <div class="col-md-6">
                    <label>ثبت‌نام باز است؟</label>
                    <select name="is_registration_open" id="is_registration_open" class="form-control">
                        <option value="1">بله</option>
                        <option value="0">خیر</option>
                    </select>
                </div>

                <div class="col-md-6" id="registration_section">
                    <label>مهلت ثبت‌نام</label>
                    <input id="registration_deadline" name="registration_deadline" class="form-control">
                </div>
            </div>
        </div>
        <div>
            <hr>
            <div class="mb-2">
                <label>توضیحات</label>
                <textarea name="description" id="description" class="form-control" rows="10"></textarea>
            </div>
        </div>

        <button class="btn btn-success mt-3" style="width: 100%;">ثبت دوره</button>
    </form>
    @push('scripts')
<!-- CKEditor 5 Classic -->
 <!--
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script> -->
<script>
    ClassicEditor
        .create(document.querySelector('#description'), {
            language: 'fa',
            alignment: {
                options: ['right', 'left', 'center', 'justify']
            }
        })
        .then(editor => {
            editor.locale.uiLanguageDirection = 'rtl';
        })
        .catch(error => {
            console.error(error);
        });
</script>

<!-- Persian Datepicker -->
<script>
    $(document).ready(function () {
        $("#start_date, #end_date, #registration_deadline").persianDatepicker({
            format: 'YYYY-MM-DD',
            initialValue: false,
            autoClose: true,
        });
    });
</script>

<script>
$(document).ready(function () {

     function toggleCostFields() {
        const value = $('#is_free').val();
        if (value === '1') {
            $('#pay_section').hide();
        } else {
            $('#pay_section').show();
        }
    }

    toggleCostFields();

    $('#is_free').on('change', toggleCostFields);

});
</script>


<!-- Leaflet.js 
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
-->
<script>
    var map = L.map('map').setView([35.6997, 51.3380], 11); // Tehran default

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    var marker;

    function onMapClick(e) {
        if (marker) map.removeLayer(marker);
        marker = L.marker(e.latlng).addTo(map);
        document.getElementById('latitude').value = e.latlng.lat;
        document.getElementById('longitude').value = e.latlng.lng;
    }

    map.on('click', onMapClick);
</script>
@endpush
@endsection