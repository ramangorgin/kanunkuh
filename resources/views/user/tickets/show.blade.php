@extends('user.layout')

@section('title', 'جزئیات تیکت')

@section('breadcrumb')
    <a href="{{ route('dashboard.index') }}">داشبورد</a> / <a href="{{ route('dashboard.tickets.index') }}">تیکت‌ها</a> / <span>جزئیات</span>
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
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h5 class="mb-1">{{ $ticket->subject }}</h5>
            {!! ticket_status_badge($ticket->status, $ticket->last_reply_by) !!}
        </div>
        <div class="d-flex gap-2">
            @if($ticket->status !== 'closed')
                <form method="POST" action="{{ route('dashboard.tickets.close', $ticket->id) }}">
                    @csrf
                    <button class="btn btn-outline-danger btn-sm" type="submit">بستن تیکت</button>
                </form>
            @else
                <form method="POST" action="{{ route('dashboard.tickets.reopen', $ticket->id) }}">
                    @csrf
                    <button class="btn btn-outline-success btn-sm" type="submit">بازگشایی</button>
                </form>
            @endif
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="fw-semibold">گفتگو</span>
                <small class="text-muted">شماره تیکت: {{ toPersianNumber($ticket->id) }}</small>
            </div>

            <div class="vstack gap-3">
                @foreach($ticket->messages as $msg)
                    <div class="p-3 rounded border" style="background: {{ $msg->sender_role === 'admin' ? '#f8f9fa' : '#ffffff' }};">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="fw-bold">
                                {{ $msg->sender_role === 'admin' ? 'پاسخ ادمین' : 'شما' }}
                            </div>
                            <small class="text-muted">{{ $msg->created_at ? toPersianNumber(jdate($msg->created_at)->format('Y/m/d H:i')) : '' }}</small>
                        </div>
                        <div class="text-justify">{!! nl2br(e($msg->message)) !!}</div>
                        @if($msg->attachments->count())
                            <div class="mt-2">
                                <div class="fw-semibold small mb-1">پیوست‌ها:</div>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($msg->attachments as $att)
                                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('dashboard.tickets.attachments.download', $att->id) }}">
                                            <i class="bi bi-paperclip"></i> {{ $att->original_name }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    @if($ticket->status !== 'closed')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h6 class="mb-3">ارسال پاسخ</h6>
            <form method="POST" action="{{ route('dashboard.tickets.reply', $ticket->id) }}" enctype="multipart/form-data" id="ticket-reply-form">
                @csrf
                <div class="mb-3">
                    <label class="form-label">متن پیام</label>
                    <textarea name="message" rows="5" class="form-control" required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">پیوست‌ها (حداکثر ۵ فایل، ۵ مگابایت)</label>
                    <div class="attachment-box" id="ticket-reply-attachments">
                        <input type="file" name="attachments[]" class="d-none" id="ticket-reply-attachment-input" multiple accept="image/*,.pdf,.doc,.docx,.xlsx,.xls,.txt,.zip">
                        <div class="attachment-drop" tabindex="0" role="button" aria-label="اضافه کردن فایل" id="ticket-reply-attachment-drop">
                            <i class="bi bi-paperclip"></i>
                            <div class="texts">
                                <div class="fw-bold">برای افزودن فایل اینجا کلیک کنید</div>
                                <small class="text-muted">تصویر، PDF یا سند دیگر را انتخاب کنید.</small>
                            </div>
                        </div>
                        <div class="attachment-list row g-2 mt-2" id="ticket-reply-attachment-list"></div>
                        <div class="file-error"></div>
                        <small class="text-muted d-block mt-1">حداکثر ۵ فایل، هر کدام تا ۵ مگابایت</small>
                    </div>
                </div>
                <div class="text-end">
                    <button type="submit" class="btn btn-primary px-4">ارسال پاسخ</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<!--
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
-->
<script>
(function(){
    const container = document.getElementById('ticket-reply-attachments');
    if (!container) return;
    const input = container.querySelector('#ticket-reply-attachment-input');
    const drop = container.querySelector('#ticket-reply-attachment-drop');
    const list = container.querySelector('#ticket-reply-attachment-list');
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
