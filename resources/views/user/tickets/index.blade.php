@extends('user.layout')

@section('title', 'تیکت‌های من')

@section('breadcrumb')
    <a href="{{ route('dashboard.index') }}">داشبورد</a> / <span>تیکت‌ها</span>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">تیکت‌های پشتیبانی</h4>
        <a href="{{ route('dashboard.tickets.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> تیکت جدید
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>عنوان</th>
                            <th>وضعیت</th>
                            <th>آخرین تغییر</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tickets as $ticket)
                            <tr>
                                <td>{{ toPersianNumber($ticket->id) }}</td>
                                <td class="fw-semibold">{{ $ticket->subject }}</td>
                                <td>{!! ticket_status_badge($ticket->status, $ticket->last_reply_by) !!}</td>
                                <td>{{ $ticket->updated_at ? toPersianNumber(jdate($ticket->updated_at)->format('Y/m/d H:i')) : '' }}</td>
                                <td>
                                    <a href="{{ route('dashboard.tickets.show', $ticket->id) }}" class="btn btn-sm btn-outline-primary">مشاهده</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted">تیکتی ثبت نکرده‌اید.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $tickets->links() }}</div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
if (!window.ticket_status_badge_helper_loaded) {
    window.ticket_status_badge_helper_loaded = true;
    window.ticket_status_badge = function(status, lastReply){
        const map = {
            open: {text:'باز', cls:'bg-success'},
            waiting_admin: {text:'در انتظار ادمین', cls:'bg-warning text-dark'},
            waiting_user: {text:'در انتظار شما', cls:'bg-info text-dark'},
            closed: {text:'بسته', cls:'bg-secondary'},
        };
        const item = map[status] || map['open'];
        return `<span class="badge ${item.cls}">${item.text}${lastReply?` (${lastReply==='admin'?'ادمین':'کاربر'})`:''}</span>`;
    }
}
</script>
@endpush
