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
                    <textarea name="important_notes" id="important_notes" class="form-control" rows="5">{{ old('important_notes', $programReport->important_notes) }}</textarea>
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

                <hr>

                {{-- تصاویر گزارش --}}
                <h5 class="mb-3 text-primary"><i class="bi bi-images me-2"></i> تصاویر گزارش</h5>
                <div class="mb-4">
                    {{-- نمایش تصاویر موجود --}}
                    @if($programReport->program && $programReport->program->files->count() > 0)
                        <div class="mb-3">
                            <label class="form-label">تصاویر موجود:</label>
                            <div class="row g-3" id="existing-images">
                                @foreach($programReport->program->files->where('file_type', 'image') as $file)
                                    <div class="col-md-3 col-sm-6 image-preview-item" data-file-id="{{ $file->id }}">
                                        <img src="{{ Storage::url($file->file_path) }}" alt="Existing image">
                                        <button type="button" class="btn btn-danger btn-sm remove-btn remove-existing-image" data-file-id="{{ $file->id }}">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    
                    {{-- آپلود تصاویر جدید --}}
                    <div class="image-upload-container">
                        <div class="upload-area border rounded p-4 text-center mb-3" id="upload-area" style="cursor: pointer; background: #f8f9fa; transition: all 0.3s;">
                            <i class="bi bi-cloud-upload fs-1 text-primary d-block mb-2"></i>
                            <p class="mb-1 fw-bold">برای آپلود تصویر جدید کلیک کنید</p>
                            <p class="text-muted small mb-0">فرمت‌های مجاز: JPG, PNG, GIF | حداکثر اندازه: 2 مگابایت | حداکثر تعداد: 20 تصویر</p>
                        </div>
                        <input type="file" name="report_images[]" id="image-input" class="d-none" multiple accept="image/jpeg,image/png,image/gif">
                        <div id="image-preview" class="row g-3"></div>
                        <input type="hidden" name="deleted_files" id="deleted-files" value="">
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

@push('styles')
<style>
    .image-upload-container {
        margin-bottom: 20px;
    }
    
    .upload-area:hover {
        background: #e9ecef !important;
        border-color: #667eea !important;
    }
    
    .image-preview-item {
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
    
    .image-preview-item .remove-btn {
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

            // Image upload handling
            const uploadArea = document.getElementById('upload-area');
            const imageInput = document.getElementById('image-input');
            const imagePreview = document.getElementById('image-preview');
            let imageFiles = [];
            let deletedFileIds = [];

            uploadArea.addEventListener('click', () => {
                imageInput.click();
            });

            imageInput.addEventListener('change', function(e) {
                const files = Array.from(e.target.files);
                
                // Check total count (existing + new)
                const existingCount = $('#existing-images .image-preview-item').length;
                if (existingCount - deletedFileIds.length + imageFiles.length + files.length > 20) {
                    toastr.error('حداکثر 20 تصویر مجاز است');
                    e.target.value = '';
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
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const div = document.createElement('div');
                        div.className = 'col-md-3 col-sm-6 image-preview-item';
                        div.innerHTML = `
                            <img src="${e.target.result}" alt="Preview">
                            <button type="button" class="btn btn-danger btn-sm remove-btn remove-image" data-index="${imageFiles.length - 1}">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        `;
                        imagePreview.appendChild(div);
                    };
                    reader.readAsDataURL(file);
                });

                e.target.value = '';
            });

            // Remove new image
            $(document).on('click', '.remove-image', function() {
                const index = $(this).data('index');
                imageFiles.splice(index, 1);
                $(this).closest('.image-preview-item').remove();
                
                // Update indices
                $('.remove-image').each(function(i) {
                    $(this).attr('data-index', i);
                });
            });

            // Remove existing image
            $(document).on('click', '.remove-existing-image', function() {
                const fileId = $(this).data('file-id');
                deletedFileIds.push(fileId);
                $(this).closest('.image-preview-item').remove();
                $('#deleted-files').val(deletedFileIds.join(','));
            });
        });
    </script>
@endpush

