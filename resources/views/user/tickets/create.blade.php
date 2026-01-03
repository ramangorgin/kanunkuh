@extends('user.layout')

@section('title', 'ایجاد تیکت جدید')

@section('breadcrumb')
    <a href="{{ route('dashboard.index') }}">داشبورد</a> / <a href="{{ route('dashboard.tickets.index') }}">تیکت‌ها</a> / <span>تیکت جدید</span>
@endsection

@push('styles')
<style>
    .attachment-box { border: 1px dashed #d0d7de; border-radius: 12px; padding: 14px; background: #fbfbfb; }
    .attachment-drop { cursor: pointer; border: 1px dashed #c2c7cf; border-radius: 10px; padding: 14px; background: #fff; transition: all 0.2s ease; display: flex; align-items: center; gap: 10px; }
    .attachment-drop:hover { border-color: #0d6efd; background: #f5f8ff; }
    .attachment-drop i { font-size: 1.4rem; color: #0d6efd; }
    .attachment-drop .texts { color: #555; }
    .attachment-list .attachment-item { position: relative; background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; padding: 8px; display: flex; align-items: center; gap: 10px; box-shadow: 0 3px 10px rgba(0,0,0,0.02); }
    .attachment-thumb { width: 56px; height: 56px; border-radius: 8px; background: #f3f4f6; display: flex; align-items: center; justify-content: center; color: #6c757d; font-size: 1.4rem; overflow: hidden; background-size: cover; background-position: center; }
    .attachment-name { font-size: 0.95rem; color: #111; display: flex; flex-direction: column; }
    .attachment-name small { color: #6c757d; }
    .attachment-remove { border: 0; background: transparent; color: #dc3545; font-size: 1.1rem; transition: transform 0.15s ease; }
    .attachment-remove:hover { transform: scale(1.1); }
    .file-error { color: #dc3545; font-size: 0.875rem; margin-top: 0.25rem; }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="mb-3">ثبت تیکت جدید</h5>
                    <form method="POST" action="{{ route('dashboard.tickets.store') }}" enctype="multipart/form-data" class="row g-3" id="ticket-create-form">
                        @csrf
                        <div class="col-12">
                            <label class="form-label">عنوان تیکت <span class="text-danger">*</span></label>
                            <input type="text" name="subject" class="form-control" value="{{ old('subject') }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">متن پیام <span class="text-danger">*</span></label>
                            <textarea name="message" rows="6" class="form-control" required>{{ old('message') }}</textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label">پیوست‌ها (اختیاری - حداکثر ۵ فایل، ۵ مگابایت)</label>
                            <div class="attachment-box" id="ticket-attachments">
                                <input type="file" name="attachments[]" class="d-none" id="ticket-attachment-input" multiple accept="image/*,.pdf,.doc,.docx,.xlsx,.xls,.txt,.zip">
                                <div class="attachment-drop" tabindex="0" role="button" aria-label="اضافه کردن فایل" id="ticket-attachment-drop">
                                    <i class="bi bi-paperclip"></i>
                                    <div class="texts">
                                        <div class="fw-bold">برای افزودن فایل اینجا کلیک کنید</div>
                                        <small class="text-muted">تصویر، PDF یا سند دیگر را انتخاب کنید.</small>
                                    </div>
                                </div>
                                <div class="attachment-list row g-2 mt-2" id="ticket-attachment-list"></div>
                                <div class="file-error"></div>
                                <small class="text-muted d-block mt-1">حداکثر ۵ فایل، هر کدام تا ۵ مگابایت</small>
                            </div>
                        </div>

                        <div class="col-12 text-end">
                            <a href="{{ route('dashboard.tickets.index') }}" class="btn btn-outline-secondary">انصراف</a>
                            <button type="submit" class="btn btn-success px-4">ارسال تیکت</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
(function(){
    const container = document.getElementById('ticket-attachments');
    if (!container) return;
    const input = container.querySelector('#ticket-attachment-input');
    const drop = container.querySelector('#ticket-attachment-drop');
    const list = container.querySelector('#ticket-attachment-list');
    const errorBox = container.querySelector('.file-error');
    const maxFiles = 5;
    const maxSizeMB = 5;
    let selected = [];

    const readableSize = (bytes) => {
        if (bytes >= 1024*1024) return (bytes/1024/1024).toFixed(1) + ' مگابایت';
        return (bytes/1024).toFixed(1) + ' کیلوبایت';
    };

    const refreshInput = () => {
        const dt = new DataTransfer();
        selected.forEach(file => dt.items.add(file));
        input.files = dt.files;
    };

    const renderList = () => {
        list.innerHTML = '';
        if (selected.length === 0) return;
        selected.forEach((file, idx) => {
            const col = document.createElement('div');
            col.className = 'col-12 col-md-6';
            const item = document.createElement('div');
            item.className = 'attachment-item';

            const thumb = document.createElement('div');
            thumb.className = 'attachment-thumb';

            if (file.type && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = (e) => thumb.style.backgroundImage = `url(${e.target.result})`;
                reader.readAsDataURL(file);
            } else {
                const ext = file.name.split('.').pop()?.toUpperCase() || 'FILE';
                thumb.innerHTML = `<div class="text-center w-100"><i class="bi bi-file-earmark-text"></i><div class="small mt-1">${ext}</div></div>`;
            }

            const info = document.createElement('div');
            info.className = 'attachment-name flex-grow-1';
            info.innerHTML = `<span>${file.name}</span><small>${readableSize(file.size)}</small>`;

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'attachment-remove';
            removeBtn.innerHTML = '<i class="bi bi-x-circle"></i>';
            removeBtn.addEventListener('click', () => {
                selected.splice(idx, 1);
                refreshInput();
                renderList();
            });

            item.append(thumb, info, removeBtn);
            col.appendChild(item);
            list.appendChild(col);
        });
    };

    const showError = (msg) => {
        if (errorBox) errorBox.textContent = msg || '';
        if (msg) toastr.error(msg);
    };

    const addFiles = (files) => {
        showError('');
        const incoming = Array.from(files || []);
        if (!incoming.length) return;

        if (selected.length + incoming.length > maxFiles) {
            showError('حداکثر ۵ فایل می‌توانید انتخاب کنید.');
            return;
        }

        const oversize = incoming.find(f => f.size > maxSizeMB*1024*1024);
        if (oversize) {
            showError('حجم هر فایل باید حداکثر ۵ مگابایت باشد.');
            return;
        }

        selected = selected.concat(incoming);
        refreshInput();
        renderList();
    };

    drop.addEventListener('click', () => input.click());
    drop.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            input.click();
        }
    });

    input.addEventListener('change', (e) => addFiles(e.target.files));
})();
</script>
@endpush
