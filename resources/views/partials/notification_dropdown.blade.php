@php
    $context = $panel ?? 'user';
    $isAdmin = $context === 'admin';
    $panelRoutes = [
        'panel' => $isAdmin ? route('admin.notifications.panel') : route('dashboard.notifications.panel'),
        'index' => $isAdmin ? route('admin.notifications.index') : route('dashboard.notifications.index'),
        'read' => $isAdmin ? route('admin.notifications.read', 0) : route('dashboard.notifications.read', 0),
        'readAll' => $isAdmin ? route('admin.notifications.readAll') : route('dashboard.notifications.readAll'),
        'destroy' => $isAdmin ? route('admin.notifications.destroy', 0) : route('dashboard.notifications.destroy', 0),
    ];
    $prefix = $isAdmin ? 'admin' : 'user';
@endphp

<div class="dropdown notification-dropdown">
    <a href="#" class="header-icon-btn position-relative dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" id="{{$prefix}}-notif-toggle">
        <i class="bi bi-bell"></i>
        <span class="notif-badge position-absolute top-0 start-0 translate-middle" id="{{$prefix}}-notif-count" style="display:none;">0</span>
    </a>
    <div class="dropdown-menu dropdown-menu-end p-0 shadow" style="min-width: 320px;" aria-labelledby="{{$prefix}}-notif-toggle">
        <div class="px-3 py-2 border-bottom d-flex align-items-center justify-content-between">
            <div class="fw-bold mb-0">اعلانات</div>
            <a href="{{ $panelRoutes['index'] }}" class="btn btn-sm btn-outline-primary">همه</a>
        </div>
        <div style="max-height: 360px; overflow-y: auto;" id="{{$prefix}}-notif-list">
            <div class="text-center text-muted py-3">در حال بارگذاری...</div>
        </div>
    </div>
</div>

@push('styles')
<style>
.notification-dropdown .dropdown-toggle::after {
    display: none !important;
    content: none !important;
    border: 0 !important;
}
.notif-badge {
    min-width: 22px;
    height: 22px;
    padding: 0 6px;
    border-radius: 999px;
    background: #dc3545 !important;
    color: #fff !important;
    font-weight: 700;
    font-size: 11px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    animation: pulseBadge 1.4s infinite;
}
@keyframes pulseBadge {
    0% { transform: translate(-50%, -50%) scale(1); box-shadow: 0 0 0 0 rgba(220,53,69,0.6); }
    70% { transform: translate(-50%, -50%) scale(1.05); box-shadow: 0 0 0 8px rgba(220,53,69,0); }
    100% { transform: translate(-50%, -50%) scale(1); box-shadow: 0 0 0 0 rgba(220,53,69,0); }
}
</style>
@endpush

@push('scripts')
<script>
(function(){
    const panelUrl = @json($panelRoutes['panel']);
    const prefix = @json($prefix);
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const toPersianDigits = (val) => String(val ?? '').replace(/[0-9]/g, d => '۰۱۲۳۴۵۶۷۸۹'[d]);

    function buildUrl(template, id){
        return template.replace(/0(?!.*0)/, id);
    }

    async function fetchNotifications(markRead = false){
        try{
            const res = await fetch(panelUrl + (markRead ? '?mark=1' : ''), {headers: {'X-Requested-With':'XMLHttpRequest'}});
            const data = await res.json();
            renderList(data);
        }catch(e){
            console.error('Load notifications failed', e);
        }
    }

    function renderList(data){
        const listEl = document.getElementById(`${prefix}-notif-list`);
        const countEl = document.getElementById(`${prefix}-notif-count`);
        const unread = Number(data.unread_count || 0);
        countEl.textContent = toPersianDigits(unread);
        countEl.style.display = unread > 0 ? 'inline-flex' : 'none';

        if(!data.items || data.items.length === 0){
            listEl.innerHTML = '<div class="text-center text-muted py-3">اعلانی وجود ندارد.</div>';
            return;
        }

        listEl.innerHTML = data.items.map(item => {
            const readClass = item.is_read ? '' : 'bg-light';
            return `
                <div class="px-3 py-2 border-bottom ${readClass}" data-id="${item.id}">
                    <div class="flex-grow-1">
                        <div class="fw-bold mb-1">${item.title ?? ''}</div>
                        <div class="text-muted small mb-1">${item.message ?? ''}</div>
                        <div class="text-secondary small">${toPersianDigits(item.created_at ?? '')}</div>
                    </div>
                </div>
            `;
        }).join('');
    }

    document.addEventListener('shown.bs.dropdown', function(event){
        if(event.target && event.target.id === `${prefix}-notif-toggle`){
            fetchNotifications(true);
        }
    });

    // initial load to show count without opening dropdown
    fetchNotifications(false);
})();
</script>
@endpush
