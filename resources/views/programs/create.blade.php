@extends('admin.layout')

@section('title', 'ایجاد برنامه جدید')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.programs.index') }}">برنامه‌ها</a></li>
            <li class="breadcrumb-item active" aria-current="page">ایجاد برنامه جدید</li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i> ایجاد برنامه جدید</h5>
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

            <form method="POST" action="{{ route('admin.programs.store') }}" enctype="multipart/form-data" id="program-form">
                @csrf

                {{-- 1. مشخصات اولیه --}}
                <h5 class="mb-3 text-primary"><i class="bi bi-info-circle me-2"></i> مشخصات اولیه</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">نام برنامه <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">نوع برنامه <span class="text-danger">*</span></label>
                        @php
                            $defaultProgramTypes = ['کوهنوردی', 'پیمایش‌های سبک', 'سنگ‌نوردی', 'یخ‌نوردی', 'غارنوردی', 'فرهنگی'];
                            $selectedProgramType = old('program_type');
                        @endphp
                        <select name="program_type" class="form-select select2-program-type" required>
                            <option value="">انتخاب کنید</option>
                            @foreach($defaultProgramTypes as $type)
                                <option value="{{ $type }}" {{ $selectedProgramType === $type ? 'selected' : '' }}>{{ $type }}</option>
                            @endforeach
                            @if($selectedProgramType && !in_array($selectedProgramType, $defaultProgramTypes))
                                <option value="{{ $selectedProgramType }}" selected>{{ $selectedProgramType }}</option>
                            @endif
                        </select>
                        <small class="text-muted">می‌توانید نوع جدید را تایپ کنید </small>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">ارتفاع قله (متر)</label>
                        <input type="number" name="peak_height" class="form-control" value="{{ old('peak_height') }}" min="0">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">نام منطقه</label>
                        <input type="text" name="region_name" class="form-control" value="{{ old('region_name') }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">تاریخ اجرا <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" name="execution_date" id="execution_date" class="form-control" data-jdp value="{{ old('execution_date') }}" required autocomplete="off">
                            <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                        </div>
                    </div>
                </div>

                <hr>

                {{-- 2. حمل و نقل --}}
                <h5 class="mb-3 text-primary"><i class="bi bi-truck me-2"></i> حمل و نقل</h5>
                <div class="mb-3">
                    <label class="form-label">آیا برنامه حمل و نقل دارد؟</label>
                    <select name="has_transport" id="has_transport" class="form-select">
                        <option value="1" {{ old('has_transport', '1') == '1' ? 'selected' : '' }}>بله</option>
                        <option value="0" {{ old('has_transport') == '0' ? 'selected' : '' }}>خیر</option>
                    </select>
                </div>

                <div id="transport_section" class="row g-4 mb-4">
                    {{-- تهران --}}
                    <div class="col-md-6">
                        <div class="card border">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0"><i class="bi bi-geo-alt me-2"></i> حرکت از تهران</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">تاریخ و ساعت حرکت</label>
                                    <div class="input-group">
                                        <input type="text" name="departure_datetime_tehran" id="departure_datetime_tehran" class="form-control" data-jdp data-jdp-time="true" value="{{ old('departure_datetime_tehran') }}" autocomplete="off">
                                        <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">محل قرار</label>
                                    <input type="text" name="departure_place_tehran" class="form-control" value="{{ old('departure_place_tehran') }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">موقعیت روی نقشه</label>
                                    <div id="map_tehran" style="height: 250px; border-radius: 8px;"></div>
                                    <input type="hidden" name="departure_lat_tehran" id="lat_tehran">
                                    <input type="hidden" name="departure_lon_tehran" id="lon_tehran">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- کرج --}}
                    <div class="col-md-6">
                        <div class="card border">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0"><i class="bi bi-geo-alt me-2"></i> حرکت از کرج</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">تاریخ و ساعت حرکت</label>
                                    <div class="input-group">
                                        <input type="text" name="departure_datetime_karaj" id="departure_datetime_karaj" class="form-control" data-jdp data-jdp-time="true" value="{{ old('departure_datetime_karaj') }}" autocomplete="off">
                                        <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">محل قرار</label>
                                    <input type="text" name="departure_place_karaj" class="form-control" value="{{ old('departure_place_karaj') }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">موقعیت روی نقشه</label>
                                    <div id="map_karaj" style="height: 250px; border-radius: 8px;"></div>
                                    <input type="hidden" name="departure_lat_karaj" id="lat_karaj">
                                    <input type="hidden" name="departure_lon_karaj" id="lon_karaj">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>

                {{-- 3. مسئولین برنامه --}}
                <h5 class="mb-3 text-primary"><i class="bi bi-people me-2"></i> مسئولین برنامه</h5>
                <div id="roles-wrapper">
                    <div class="role-row mb-3 border p-3 rounded">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label">سمت</label>
                                <input type="text" name="roles[0][role_title]" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">شناسه کاربر</label>
                                <select name="roles[0][user_id]" class="form-select user-select select2-user">
                                    <option value="">— انتخاب کاربر —</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->full_name ?: $user->phone }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">نام فرد (در صورت نبودن اکانت)</label>
                                <input type="text" name="roles[0][user_name]" class="form-control">
                            </div>
                            <div class="col-md-12 mt-2 text-end">
                                <button type="button" class="btn btn-danger btn-sm remove-role">حذف</button>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-outline-primary mt-2 mb-4" id="add-role">افزودن سمت جدید</button>

                <hr>

                {{-- 4. تجهیزات و وعده‌ها --}}
                <h5 class="mb-3 text-primary"><i class="bi bi-tools me-2"></i> تجهیزات و وعده‌ها</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">تجهیزات مورد نیاز</label>
                        <select name="equipments[]" id="equipments" class="form-select select2-tags" multiple>
                            @foreach(['کوله پشتی', 'کیسه خواب', 'باتوم کوهنوردی', 'لباس گرم', 'هدلامپ', 'زیرانداز', 'قمقمه آب', 'کفش کوهنوردی'] as $item)
                                <option value="{{ $item }}" {{ in_array($item, old('equipments', [])) ? 'selected' : '' }}>{{ $item }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">می‌توانید موارد دلخواه خود را اضافه کنید</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">وعده‌های مورد نیاز</label>
                        <select name="meals[]" id="meals" class="form-select select2-tags" multiple>
                            @foreach(['صبحانه', 'ناهار', 'شام', 'میانوعده'] as $meal)
                                <option value="{{ $meal }}" {{ in_array($meal, old('meals', [])) ? 'selected' : '' }}>{{ $meal }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">می‌توانید موارد دلخواه خود را اضافه کنید</small>
                    </div>
                </div>

                <hr>

                {{-- 5. هزینه --}}
                <h5 class="mb-3 text-primary"><i class="bi bi-cash-coin me-2"></i> هزینه</h5>
                <div class="mb-3">
                    <label class="form-label">آیا برنامه رایگان است؟</label>
                    <select name="is_free" id="is_free" class="form-select">
                        <option value="0" {{ old('is_free', '0') == '0' ? 'selected' : '' }}>خیر</option>
                        <option value="1" {{ old('is_free') == '1' ? 'selected' : '' }}>بله</option>
                    </select>
                </div>

                <div id="cost_section" class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">هزینه برای اعضا (ریال) <span class="text-danger">*</span></label>
                        <input type="text" name="cost_member" id="cost_member" class="form-control cost-input" value="{{ old('cost_member') }}" min="0" step="1" required>
                        <small class="text-muted">فقط اعداد انگلیسی مجاز است</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">هزینه برای مهمانان (ریال) <span class="text-danger">*</span></label>
                        <input type="text" name="cost_guest" id="cost_guest" class="form-control cost-input" value="{{ old('cost_guest') }}" min="0" step="1" required>
                        <small class="text-muted">فقط اعداد انگلیسی مجاز است</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">شماره کارت <span class="text-danger">*</span></label>
                        <input type="text" name="card_number" class="form-control" value="{{ old('card_number') }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">شماره شبا</label>
                        <input type="text" name="sheba_number" class="form-control" value="{{ old('sheba_number') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">نام دارنده حساب <span class="text-danger">*</span></label>
                        <input type="text" name="card_holder" class="form-control" value="{{ old('card_holder') }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">نام بانک <span class="text-danger">*</span></label>
                        <input type="text" name="bank_name" class="form-control" value="{{ old('bank_name') }}" required>
                    </div>
                </div>

                <hr>

                {{-- 6. توضیحات --}}
                <h5 class="mb-3 text-primary"><i class="bi bi-file-text me-2"></i> توضیحات</h5>
                <div class="mb-4">
                    <label class="form-label">توضیحات</label>
                    <textarea name="rules" id="rules" class="form-control" rows="10">{{ old('rules') }}</textarea>
                </div>

                <hr>

                {{-- 7. وضعیت و مهلت ثبت‌نام --}}
                <h5 class="mb-3 text-primary"><i class="bi bi-calendar-check me-2"></i> وضعیت و مهلت ثبت‌نام</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">وضعیت <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>پیش‌نویس</option>
                            <option value="open" {{ old('status') == 'open' ? 'selected' : '' }}>باز</option>
                            <option value="closed" {{ old('status') == 'closed' ? 'selected' : '' }}>بسته</option>
                            <option value="done" {{ old('status') == 'done' ? 'selected' : '' }}>انجام شده</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">مهلت ثبت‌نام</label>
                        <div class="input-group">
                            <input type="text" name="register_deadline" id="register_deadline" class="form-control" data-jdp data-jdp-time="true" value="{{ old('register_deadline') }}" autocomplete="off">
                            <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                        </div>
                    </div>
                </div>

                <hr>

                {{-- 8. تصاویر --}}
                <h5 class="mb-3 text-primary"><i class="bi bi-images me-2"></i> آپلود تصاویر برنامه</h5>
                <div class="mb-4">
                    <div class="image-upload-container">
                        <div class="upload-area border rounded p-4 text-center mb-3" id="upload-area" style="cursor: pointer; background: #f8f9fa; transition: all 0.3s;">
                            <i class="bi bi-cloud-upload fs-1 text-primary d-block mb-2"></i>
                            <p class="mb-1 fw-bold">برای آپلود تصویر کلیک کنید</p>
                            <p class="text-muted small mb-0">فرمت‌های مجاز: JPG, PNG, GIF | حداکثر اندازه: 2 مگابایت | حداکثر تعداد: 10 تصویر</p>
                        </div>
                        <input type="file" name="report_photos[]" id="image-input" class="d-none" multiple accept="image/jpeg,image/png,image/gif">
                        <div id="image-preview" class="row g-3"></div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.programs.index') }}" class="btn btn-secondary">انصراف</a>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle me-2"></i> ثبت برنامه
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .image-upload-container .upload-area:hover {
            background: #e9ecef !important;
            border-color: #0d6efd !important;
        }
        .image-preview-item {
            position: relative;
        }
        .image-preview-item img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
        }
        .image-preview-item .remove-btn {
            position: absolute;
            top: 10px;
            left: 10px;
        }
        .select2-container {
            z-index: 9999;
        }
        #map_tehran, #map_karaj {
            z-index: 1;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.ckeditor.com/ckeditor5/41.3.1/classic/ckeditor.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize CKEditor
            ClassicEditor
                .create(document.querySelector('#rules'), {
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

            // Program type with ability to add custom labels
            $('.select2-program-type').select2({
                tags: true,
                dir: "rtl",
                width: '100%',
                theme: 'bootstrap-5',
                placeholder: 'نوع برنامه را انتخاب یا تایپ کنید',
                createTag: function (params) {
                    const term = $.trim(params.term);
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

            // Initialize Select2 with tags for equipments and meals
            $('.select2-tags').select2({
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

            // Toggle transport section
            function toggleTransportSection() {
                const hasTransport = $('#has_transport').val();
                if (hasTransport === '1') {
                    $('#transport_section').show();
                } else {
                    $('#transport_section').hide();
                }
            }
            toggleTransportSection();
            $('#has_transport').on('change', toggleTransportSection);

            // Toggle cost section
            function toggleCostSection() {
                const isFree = $('#is_free').val();
                if (isFree === '0') {
                    $('#cost_section').show();
                    $('#cost_section input[required]').prop('required', true);
                } else {
                    $('#cost_section').hide();
                    $('#cost_section input[required]').prop('required', false);
                }
            }
            toggleCostSection();
            $('#is_free').on('change', toggleCostSection);

            // Number formatting for cost inputs with commas
            // Simple approach: format on blur, allow free typing on input
            $('.cost-input').on('keydown', function(e) {
                // Allow: backspace, delete, tab, escape, enter, and numbers
                if ([46, 8, 9, 27, 13, 110, 190].indexOf(e.keyCode) !== -1 ||
                    // Allow: Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
                    (e.keyCode === 65 && e.ctrlKey === true) ||
                    (e.keyCode === 67 && e.ctrlKey === true) ||
                    (e.keyCode === 86 && e.ctrlKey === true) ||
                    (e.keyCode === 88 && e.ctrlKey === true) ||
                    // Allow: home, end, left, right
                    (e.keyCode >= 35 && e.keyCode <= 39)) {
                    return;
                }
                // Ensure that it is a number and stop the keypress
                if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                    e.preventDefault();
                }
            });

            // Format with commas on input (real-time)
            $('.cost-input').on('input', function() {
                let input = this;
                let value = input.value.replace(/,/g, '');
                
                // Only allow digits
                value = value.replace(/[^0-9]/g, '');
                
                if (value) {
                    // Format with commas
                    let formatted = parseInt(value).toLocaleString('en-US');
                    input.value = formatted;
                } else {
                    input.value = '';
                }
            });

            // Remove commas on focus for easier editing
            $('.cost-input').on('focus', function() {
                let value = $(this).val().replace(/,/g, '');
                $(this).val(value);
            });

            // On form submit, ensure no commas
            $('#program-form').on('submit', function() {
                $('.cost-input').each(function() {
                    $(this).val($(this).val().replace(/,/g, ''));
                });
            });

            // Configure jalalidatepicker for time-enabled fields
            // execution_date should NOT have time picker (only date)
            // Enable time picker only for fields with data-jdp-time="true"
            const timeFields = ['#departure_datetime_tehran', '#departure_datetime_karaj', '#register_deadline'];
            
            timeFields.forEach(function(fieldId) {
                const input = document.querySelector(fieldId);
                if (input) {
                    input.addEventListener('focus', function() {
                        jalaliDatepicker.updateOptions({ time: true, zIndex: 3000 });
                    });
                }
            });
            
            // Disable time for execution_date (date only)
            const executionDateInput = document.querySelector('#execution_date');
            if (executionDateInput) {
                executionDateInput.addEventListener('focus', function() {
                    jalaliDatepicker.updateOptions({ time: false, zIndex: 3000 });
                });
            }

            // Initialize Leaflet maps
            function initMap(divId, latInputId, lonInputId, defaultLat = 35.6892, defaultLon = 51.3890, existingLat = null, existingLon = null) {
                try {
                    const lat = existingLat ? parseFloat(existingLat) : defaultLat;
                    const lon = existingLon ? parseFloat(existingLon) : defaultLon;
                    const map = L.map(divId).setView([lat, lon], existingLat ? 15 : 10);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 18,
                        attribution: '© OpenStreetMap contributors',
                        crossOrigin: true
                    }).addTo(map);

                    let marker = null;
                    if (existingLat && existingLon) {
                        marker = L.marker([lat, lon]).addTo(map);
                        document.getElementById(latInputId).value = lat.toFixed(7);
                        document.getElementById(lonInputId).value = lon.toFixed(7);
                    }
                    
                    map.on('click', function(e) {
                        if (marker) {
                            map.removeLayer(marker);
                        }
                        marker = L.marker(e.latlng).addTo(map);
                        document.getElementById(latInputId).value = e.latlng.lat.toFixed(7);
                        document.getElementById(lonInputId).value = e.latlng.lng.toFixed(7);
                    });
                } catch (error) {
                    console.warn('خطا در بارگذاری نقشه:', error);
                    document.getElementById(divId).innerHTML = '<div class="alert alert-warning">نقشه در دسترس نیست. لطفاً مختصات را به صورت دستی وارد کنید.</div>';
                }
            }

            // Initialize maps when transport section is shown
            let mapsInitialized = false;
            $('#has_transport').on('change', function() {
                if ($(this).val() === '1' && !mapsInitialized) {
                    setTimeout(function() {
                        const tehranLat = $('#lat_tehran').val();
                        const tehranLon = $('#lon_tehran').val();
                        const karajLat = $('#lat_karaj').val();
                        const karajLon = $('#lon_karaj').val();
                        initMap('map_tehran', 'lat_tehran', 'lon_tehran', 35.6892, 51.3890, tehranLat, tehranLon);
                        initMap('map_karaj', 'lat_karaj', 'lon_karaj', 35.8327, 50.9344, karajLat, karajLon);
                        mapsInitialized = true;
                    }, 100);
                }
            });

            // Initialize maps if transport is enabled by default
            if ($('#has_transport').val() === '1') {
                setTimeout(function() {
                    const tehranLat = $('#lat_tehran').val();
                    const tehranLon = $('#lon_tehran').val();
                    const karajLat = $('#lat_karaj').val();
                    const karajLon = $('#lon_karaj').val();
                    initMap('map_tehran', 'lat_tehran', 'lon_tehran', 35.6892, 51.3890, tehranLat, tehranLon);
                    initMap('map_karaj', 'lat_karaj', 'lon_karaj', 35.8327, 50.9344, karajLat, karajLon);
                    mapsInitialized = true;
                }, 500);
            }

            // Image upload handling with removable previews
            const uploadArea = document.getElementById('upload-area');
            const imageInput = document.getElementById('image-input');
            const imagePreview = document.getElementById('image-preview');
            const dt = new DataTransfer();

            uploadArea.addEventListener('click', () => imageInput.click());

            function renderPreviews() {
                imagePreview.innerHTML = '';
                Array.from(dt.files).forEach((file, idx) => {
                    const reader = new FileReader();
                    reader.onload = function(ev) {
                        const div = document.createElement('div');
                        div.className = 'col-md-3 col-sm-6 image-preview-item';
                        div.innerHTML = `
                            <img src="${ev.target.result}" alt="Preview">
                            <button type="button" class="btn btn-danger btn-sm remove-btn remove-image" data-index="${idx}">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        `;
                        imagePreview.appendChild(div);
                    };
                    reader.readAsDataURL(file);
                });
            }

            imageInput.addEventListener('change', function(e) {
                const files = Array.from(e.target.files);

                files.forEach(file => {
                    if (dt.files.length >= 10) {
                        toastr.error('حداکثر 10 تصویر مجاز است');
                        return;
                    }
                    if (file.size > 2 * 1024 * 1024) {
                        toastr.error(`فایل ${file.name} بزرگتر از 2 مگابایت است`);
                        return;
                    }
                    if (!file.type.match('image.*')) {
                        toastr.error(`فایل ${file.name} یک تصویر معتبر نیست`);
                        return;
                    }
                    dt.items.add(file);
                });

                imageInput.files = dt.files;
                renderPreviews();
                imageInput.value = '';
            });

            $(document).on('click', '.remove-image', function() {
                const idx = $(this).data('index');
                dt.items.remove(idx);
                imageInput.files = dt.files;
                renderPreviews();
            });

            // Add role functionality
            let roleIndex = 1;
            $('#add-role').on('click', function() {
                const newRow = $(`
                    <div class="role-row mb-3 border p-3 rounded">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label">سمت</label>
                                <input type="text" name="roles[${roleIndex}][role_title]" class="form-control" >
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">شناسه کاربر</label>
                                <select name="roles[${roleIndex}][user_id]" class="form-select user-select select2-user">
                                    <option value="">— انتخاب کاربر —</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->full_name ?: $user->phone }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">نام فرد (در صورت نبودن اکانت)</label>
                                <input type="text" name="roles[${roleIndex}][user_name]" class="form-control" >
                            </div>
                            <div class="col-md-12 mt-2 text-end">
                                <button type="button" class="btn btn-danger btn-sm remove-role">حذف</button>
                            </div>
                        </div>
                    </div>
                `);
                $('#roles-wrapper').append(newRow);
                
                // Initialize Select2 for new select
                newRow.find('.select2-user').select2({
                    dir: "rtl",
                    width: '100%',
                    theme: 'bootstrap-5'
                });
                
                roleIndex++;
            });

            // Remove role
            $(document).on('click', '.remove-role', function() {
                $(this).closest('.role-row').remove();
            });

            // Toggle user name field
            function toggleUserNameField(selectElement) {
                const userNameInput = $(selectElement).closest('.role-row').find('input[name*="[user_name]"]');
                if ($(selectElement).val()) {
                    userNameInput.prop('disabled', true).val('');
                } else {
                    userNameInput.prop('disabled', false);
                }
            }

            $(document).on('change', '.user-select', function() {
                toggleUserNameField(this);
            });

            // Initialize for existing selects
            $('.user-select').each(function() {
                toggleUserNameField(this);
            });
        });
    </script>
    
    {{-- کد JavaScript برای پر کردن خودکار فرم (فقط برای تست) --}}
    <script>
        // این کد را در کنسول مرورگر اجرا کنید تا فرم به صورت خودکار پر شود
        window.fillFormForTest = function() {
            // مشخصات اولیه
            $('input[name="name"]').val('صعود به قله دماوند');
            $('select[name="program_type"]').val('کوهنوردی').trigger('change');
            $('input[name="peak_height"]').val('5610');
            $('input[name="region_name"]').val('دماوند');
            $('input[name="execution_date"]').val('1404/07/15');
            
            // حمل و نقل
            $('#has_transport').val('1').trigger('change');
            setTimeout(() => {
                $('#departure_datetime_tehran').val('1404/07/14 06:00');
                $('input[name="departure_place_tehran"]').val('میدان آزادی');
                $('#lat_tehran').val('35.6892');
                $('#lon_tehran').val('51.3890');
                
                $('#departure_datetime_karaj').val('1404/07/14 05:30');
                $('input[name="departure_place_karaj"]').val('میدان امام خمینی کرج');
                $('#lat_karaj').val('35.8327');
                $('#lon_karaj').val('50.9344');
            }, 500);
            
            // مسئولین
            $('input[name="roles[0][role_title]"]').val('سرپرست');
            // اگر کاربری وجود دارد، اولین کاربر را انتخاب می‌کند
            if ($('.select2-user option').length > 1) {
                $('.select2-user').val($('.select2-user option:eq(1)').val()).trigger('change');
            } else {
                $('input[name="roles[0][user_name]"]').val('علی رضایی');
            }
            
            // تجهیزات و وعده‌ها
            $('#equipments').val(['کوله پشتی', 'کیسه خواب', 'باتوم کوهنوردی']).trigger('change');
            $('#meals').val(['صبحانه', 'ناهار', 'شام']).trigger('change');
            
            // هزینه
            $('#is_free').val('0').trigger('change');
            setTimeout(() => {
                $('input[name="cost_member"]').val('1500000').trigger('input');
                $('input[name="cost_guest"]').val('2000000').trigger('input');
                $('input[name="card_number"]').val('6037991234567890');
                $('input[name="sheba_number"]').val('IR120620000000000123456789');
                $('input[name="card_holder"]').val('انجمن کوهنوردی');
                $('input[name="bank_name"]').val('ملی');
            }, 300);
            
            // قوانین
            if (typeof ClassicEditor !== 'undefined' && ClassicEditor.instances.rules) {
                ClassicEditor.instances.rules.setData('<p>شرکت در این برنامه نیازمند آمادگی جسمانی مناسب است.</p><p>همه شرکت‌کنندگان باید تجهیزات کامل را همراه داشته باشند.</p>');
            }
            
            // وضعیت و مهلت ثبت‌نام
            $('select[name="status"]').val('open');
            $('#register_deadline').val('1404/07/10 23:59');
            
            console.log('✅ فرم با موفقیت پر شد!');
            toastr.success('فرم به صورت خودکار پر شد. می‌توانید آن را بررسی و ارسال کنید.');
        };
        
        // برای استفاده: در کنسول مرورگر تایپ کنید: fillFormForTest()
        console.log('💡 برای پر کردن خودکار فرم، در کنسول تایپ کنید: fillFormForTest()');
    </script>
@endpush
