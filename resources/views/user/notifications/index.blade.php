@extends('user.layout')

@section('title', 'اعلانات من')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">اعلانات</h4>
        <form action="{{ route('dashboard.notifications.readAll') }}" method="POST">
            @csrf
            <button class="btn btn-sm btn-outline-primary" type="submit">علامت زدن همه به عنوان خوانده شده</button>
        </form>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>عنوان</th>
                            <th>متن</th>
                            <th>تاریخ</th>
                            <th>وضعیت</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($notifications as $notification)
                            <tr class="{{ $notification->is_read ? '' : 'table-light' }}">
                                <td>{{ toPersianNumber($notification->id) }}</td>
                                <td>{{ $notification->title }}</td>
                                <td>{{ $notification->message }}</td>
                                <td>{{ $notification->created_at ? toPersianNumber($notification->created_at->format('Y/m/d H:i')) : '' }}</td>
                                <td>
                                    @if($notification->is_read)
                                        <span class="badge bg-secondary">خوانده شده</span>
                                    @else
                                        <span class="badge bg-primary">خوانده نشده</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <form action="{{ route('dashboard.notifications.read', $notification) }}" method="POST">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-success" type="submit">خواندن</button>
                                        </form>
                                        <form action="{{ route('dashboard.notifications.destroy', $notification) }}" method="POST" onsubmit="return confirm('حذف اعلان؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" type="submit">حذف</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted">اعلانی وجود ندارد.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $notifications->links() }}</div>
        </div>
    </div>
</div>
@endsection
