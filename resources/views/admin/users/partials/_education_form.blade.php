{{-- Partial form for education history fields. --}}
@php
    $educations = $educations ?? collect();
@endphp

<div id="education-wrapper">
    @foreach($educations as $index => $edu)
    <div class="education-item card mb-3 shadow-sm border-0">
        <div class="card-body position-relative">
            <button type="button" class="btn-close position-absolute top-0 end-0 m-3 remove-edu" aria-label="حذف"></button>
            
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">عنوان دوره</label>
                    <select name="educations[{{ $index }}][federation_course_id]" class="form-select select-course">
                        <option value="">انتخاب کنید...</option>
                        @foreach($federationCourses as $course)
                            <option value="{{ $course->id }}" 
                                {{ old("educations.$index.federation_course_id", $edu->federation_course_id ?? '') == $course->id ? 'selected' : '' }}>
                                {{ $course->title }}
                            </option>
                        @endforeach
                        <option value="_custom" {{ (old("educations.$index.federation_course_id") == '_custom' || ($edu->custom_course_title && !$edu->federation_course_id)) ? 'selected' : '' }}>سایر (دوره سفارشی)</option>
                    </select>
                </div>
                
                <div class="col-md-4 custom-course-wrap" style="{{ (old("educations.$index.federation_course_id") == '_custom' || ($edu->custom_course_title && !$edu->federation_course_id)) ? '' : 'display:none;' }}">
                    <label class="form-label">نام دوره سفارشی</label>
                    <input type="text" name="educations[{{ $index }}][custom_course_title]" 
                           class="form-control" 
                           value="{{ old("educations.$index.custom_course_title", $edu->custom_course_title ?? '') }}" 
                           placeholder="نام دوره">
                </div>

                <div class="col-md-4">
                    <label class="form-label">تاریخ صدور مدرک</label>
                    <div class="input-group">
                        <input type="text" name="educations[{{ $index }}][issue_date]" 
                               value="{{ old("educations.$index.issue_date", $edu->issue_date ? \Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::parse($edu->issue_date))->format('Y/m/d') : '') }}" 
                               class="form-control jalali-picker" data-jdp autocomplete="off">
                        <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
                    </div>
                </div>

                <div class="col-12">
                    <label class="form-label">فایل گواهینامه</label>
                    <input type="file" name="educations[{{ $index }}][certificate_file]" class="filepond" accept="image/*,application/pdf" data-label-idle="برای بارگذاری فایل کلیک کنید یا بکشید و رها کنید">
                    @if($edu->certificate_file)
                        @php $certExt = strtolower(pathinfo($edu->certificate_file, PATHINFO_EXTENSION)); @endphp
                        <div class="mt-2">
                            @if(in_array($certExt, ['jpg','jpeg','png','gif','webp']))
                                <img src="{{ asset('storage/'.$edu->certificate_file) }}" alt="گواهینامه" class="img-thumbnail" style="max-height: 140px;">
                            @else
                                <a href="{{ asset('storage/'.$edu->certificate_file) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    دانلود فایل فعلی ({{ basename($edu->certificate_file) }})
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="text-center mt-3">
    <button type="button" id="add-education" class="btn btn-outline-success">
        <i class="bi bi-plus-circle"></i> افزودن دوره جدید
    </button>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const wrapper = document.getElementById('education-wrapper');
        const addBtn = document.getElementById('add-education');
        let eduIndex = {{ $educations->count() }};

        // Initial setup for existing rows
        wrapper.querySelectorAll('.education-item').forEach(item => setupRow(item));

        addBtn.addEventListener('click', function() {
            const template = `
            <div class="education-item card mb-3 shadow-sm border-0 animate__animated animate__fadeIn">
                <div class="card-body position-relative">
                    <button type="button" class="btn-close position-absolute top-0 end-0 m-3 remove-edu" aria-label="حذف"></button>
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">عنوان دوره</label>
                            <select name="educations[${eduIndex}][federation_course_id]" class="form-select select-course">
                                <option value="">انتخاب کنید...</option>
                                @foreach($federationCourses as $course)
                                    <option value="{{ $course->id }}">{{ $course->title }}</option>
                                @endforeach
                                <option value="_custom">سایر (دوره سفارشی)</option>
                            </select>
                        </div>
                        <div class="col-md-4 custom-course-wrap" style="display:none;">
                            <label class="form-label">نام دوره سفارشی</label>
                            <input type="text" name="educations[${eduIndex}][custom_course_title]" class="form-control" placeholder="نام دوره">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">تاریخ صدور مدرک</label>
                            <div class="input-group">
                                <input type="text" name="educations[${eduIndex}][issue_date]" class="form-control jalali-picker" data-jdp autocomplete="off">
                                <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">فایل گواهینامه</label>
                            <input type="file" name="educations[${eduIndex}][certificate_file]" class="filepond" accept="image/*,application/pdf" data-label-idle="برای بارگذاری فایل کلیک کنید یا بکشید و رها کنید">
                        </div>
                    </div>
                </div>
            </div>`;

            // Append new row
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = template;
            const newRow = tempDiv.firstElementChild;
            wrapper.appendChild(newRow);

            // Setup logic for new row
            setupRow(newRow);
            
            // Initialize plugins for new row
            if(window.jalaliDatepicker) jalaliDatepicker.startWatch({ persianDigits: true });
            if(window.FilePond) FilePond.create(newRow.querySelector('.filepond'), {
                labelIdle: 'فایل را بکشید و رها کنید یا <span class="filepond--label-action">انتخاب کنید</span>',
                credits: false,
                storeAsFile: true,
            });

            eduIndex++;
        });

        wrapper.addEventListener('click', function(e) {
            if(e.target.closest('.remove-edu')) {
                const item = e.target.closest('.education-item');
                // Only remove if it's not the only one (optional, or allow removing all)
                // Assuming allow removing all for admin
                item.remove();
            }
        });

        function setupRow(row) {
            const select = row.querySelector('.select-course');
            const customWrap = row.querySelector('.custom-course-wrap');
            
            if(select && customWrap) {
                select.addEventListener('change', function() {
                    if(this.value === '_custom') {
                        customWrap.style.display = 'block';
                    } else {
                        customWrap.style.display = 'none';
                        customWrap.querySelector('input').value = '';
                    }
                });
            }
        }
    });
</script>
