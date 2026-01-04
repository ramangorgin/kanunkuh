@php
    $medical = $medical ?? new \App\Models\MedicalRecord();
@endphp

<div class="row g-3">
    {{-- Physical Stats --}}
    <div class="col-md-2">
        <label class="form-label">گروه خونی</label>
        <select name="blood_type" class="form-select">
            <option value="">انتخاب...</option>
            @foreach(['O+','O-','A+','A-','B+','B-','AB+','AB-'] as $type)
                <option value="{{ $type }}" {{ old('blood_type', $medical->blood_type ?? '') == $type ? 'selected' : '' }}>{{ $type }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <label class="form-label">قد (cm)</label>
        <input type="number" name="height" value="{{ old('height', $medical->height ?? '') }}" class="form-control">
    </div>
    <div class="col-md-2">
        <label class="form-label">وزن (kg)</label>
        <input type="number" name="weight" value="{{ old('weight', $medical->weight ?? '') }}" class="form-control">
    </div>

    {{-- Insurance Info --}}
    <div class="col-md-3">
        <label class="form-label">تاریخ صدور بیمه</label>
        <div class="input-group">
            <input type="text" name="insurance_issue_date" 
                   value="{{ old('insurance_issue_date', $medical->insurance_issue_date ? \Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::parse($medical->insurance_issue_date))->format('Y/m/d') : '') }}" 
                   class="form-control jalali-picker" data-jdp autocomplete="off">
            <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
        </div>
    </div>
    <div class="col-md-3">
        <label class="form-label">تاریخ انقضای بیمه</label>
        <div class="input-group">
            <input type="text" name="insurance_expiry_date" 
                   value="{{ old('insurance_expiry_date', $medical->insurance_expiry_date ? \Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::parse($medical->insurance_expiry_date))->format('Y/m/d') : '') }}" 
                   class="form-control jalali-picker" data-jdp autocomplete="off">
            <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
        </div>
    </div>
    <div class="col-12">
        <label class="form-label">فایل بیمه ورزشی</label>
        <input type="file" name="insurance_file" class="filepond" accept="image/*,application/pdf" data-label-idle="برای بارگذاری فایل کلیک کنید یا بکشید و رها کنید">
        @if($medical->insurance_file)
            <div class="mt-2">
                <a href="{{ asset('storage/'.$medical->insurance_file) }}" target="_blank" class="btn btn-sm btn-outline-primary">مشاهده فایل فعلی</a>
            </div>
        @endif
    </div>

    <hr class="mt-4 mb-2">

    {{-- Medical Questions --}}
    <h6 class="fw-bold text-primary mb-3"><i class="bi bi-heart-pulse"></i> سوابق بیماری و وضعیت جسمانی</h6>

    @php
        // Matching user panel questions + comprehensive list for admin
        $questions = [
            'head_injury' => 'سابقه ضربه مغزی یا آسیب سر',
            'eye_ear_problems' => 'مشکلات چشم و گوش',
            'seizures' => 'تشنج یا غش',
            'respiratory' => 'بیماری‌های تنفسی (آسم و...)',
            'heart' => 'مشکلات قلبی',
            // Extra fields for Admin view (if DB supports them)
            'blood_pressure' => 'فشار خون',
            'diabetes_hepatitis' => 'دیابت / هپاتیت',
            'kidney' => 'مشکلات کلیوی',
            'surgery' => 'سابقه جراحی',
            'bone_joint' => 'مشکلات استخوانی و مفصلی',
            'medications' => 'مصرف داروی خاص',
        ];
    @endphp

    @foreach(array_chunk($questions, 2, true) as $chunk)
        <div class="row">
            @foreach($chunk as $field => $label)
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">{{ $label }}</label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input medical-toggle" type="radio" name="{{ $field }}" 
                                   id="{{ $field }}_yes" value="1" data-target="#{{ $field }}_details"
                                   {{ old($field, $medical->$field ?? null) == 1 ? 'checked' : '' }}>
                            <label class="form-check-label" for="{{ $field }}_yes">بله</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input medical-toggle" type="radio" name="{{ $field }}" 
                                   id="{{ $field }}_no" value="0" data-target="#{{ $field }}_details"
                                   {{ old($field, $medical->$field ?? null) == 0 ? 'checked' : '' }}>
                            <label class="form-check-label" for="{{ $field }}_no">خیر</label>
                        </div>
                    </div>
                    <div id="{{ $field }}_details" class="mt-2" style="{{ old($field, $medical->$field ?? null) == 1 ? '' : 'display:none;' }}">
                        <textarea name="{{ $field }}_details" class="form-control" placeholder="توضیحات تکمیلی..." rows="2">{{ old($field.'_details', $medical->{$field.'_details'} ?? '') }}</textarea>
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach

    <div class="col-12 mt-3">
        <label class="form-label">سایر توضیحات پزشکی</label>
        <textarea name="other_conditions" class="form-control" rows="3">{{ old('other_conditions', $medical->other_conditions ?? '') }}</textarea>
    </div>
    
    <div class="col-12 mt-2">
        <div class="form-check">
            <input type="checkbox" class="form-check-input" name="commitment_signed" value="1" id="commitment_signed" 
                {{ old('commitment_signed', $medical->commitment_signed ?? false) ? 'checked' : '' }}>
            <label class="form-check-label" for="commitment_signed">تعهدنامه پزشکی امضا شده است</label>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function(){
        const toggles = document.querySelectorAll('.medical-toggle');
        toggles.forEach(toggle => {
            toggle.addEventListener('change', function(){
                const targetId = this.dataset.target;
                const target = document.querySelector(targetId);
                if(target && this.value == '1'){
                    target.style.display = 'block';
                } else if(target && this.value == '0'){
                    target.style.display = 'none';
                    target.querySelector('textarea').value = '';
                }
            });
        });
    });
</script>
