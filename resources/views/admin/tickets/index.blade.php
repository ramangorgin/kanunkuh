{{-- Admin tickets list view. --}}
@extends('admin.layout')

@section('title', 'تیکت‌ها')

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">مدیریت تیکت‌ها</h4>
        <form class="d-flex gap-2" method="GET" action="{{ route('admin.tickets.index') }}">
            <input type="text" name="q" class="form-control" placeholder="جستجو عنوان/نام/تلفن" value="{{ request('q') }}">
            <select name="status" class="form-select" style="max-width:180px;">
                <option value="">همه وضعیت‌ها</option>
                <option value="open" @selected(request('status')=='open')>باز</option>
                <option value="waiting_admin" @selected(request('status')=='waiting_admin')>در انتظار ادمین</option>
                <option value="waiting_user" @selected(request('status')=='waiting_user')>در انتظار کاربر</option>
                <option value="closed" @selected(request('status')=='closed')>بسته</option>
            </select>
            <input type="text" name="from" class="form-control" placeholder="از تاریخ" data-jdp value="{{ request('from') }}" style="max-width:140px;">
            <input type="text" name="to" class="form-control" placeholder="تا تاریخ" data-jdp value="{{ request('to') }}" style="max-width:140px;">
            <button class="btn btn-primary">فیلتر</button>
        </form>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>کاربر</th>
                            <th>عنوان</th>
                            <th>وضعیت</th>
                            <th>آخرین بروزرسانی</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tickets as $ticket)
                            <tr>
                                <td>{{ toPersianNumber($ticket->id) }}</td>
                                <td>{{ trim(($ticket->user->profile->first_name ?? '') . ' ' . ($ticket->user->profile->last_name ?? '')) ?: $ticket->user->phone }}</td>
                                <td>{{ $ticket->subject }}</td>
                                <td>{!! ticket_status_badge($ticket->status, $ticket->last_reply_by) !!}</td>
                                <td>{{ $ticket->updated_at ? toPersianNumber(jdate($ticket->updated_at)->format('Y/m/d H:i')) : '' }}</td>
                                <td>
                                    <a href="{{ route('admin.tickets.show', $ticket->id) }}" class="btn btn-sm btn-outline-primary">مشاهده</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted">تیکتی یافت نشد.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $tickets->withQueryString()->links() }}</div>
        </div>
    </div>
</div>
@endsection
