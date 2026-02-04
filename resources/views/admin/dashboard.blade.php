{{-- Admin dashboard view with summary metrics. --}}
@extends('admin.layout')

@section('title', 'داشبورد مدیریت')

@section('content')

<div class="container-fluid py-4 animate__animated animate__fadeIn">

    {{-- عنوان صفحه --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold"><i class="bi bi-speedometer2 text-primary me-2"></i> داشبورد مدیریت</h4>
        <span class="text-muted">{{ toPersianNumber(jdate()->format('Y/m/d')) }}</span>
    </div>

    {{-- کارت‌های آماری بالا --}}
    <div class="row g-4 mb-4">
        {{-- کاربران کل --}}
        <div class="col-xl-3 col-md-6">
            <div class="card h-100 border-0 shadow-sm text-white animate__animated animate__fadeInUp" style="background: linear-gradient(135deg, #0d6efd, #3f8bff);">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-white-50">کاربران کل</small>
                        <h3 class="fw-bold mb-0">{{ toPersianNumber($stats['users'] ?? 0) }}</h3>
                    </div>
                    <i class="bi bi-people-fill fs-1 text-white-50"></i>
                </div>
            </div>
        </div>

        {{-- اعضای تایید شده --}}
        <div class="col-xl-3 col-md-6">
            <div class="card h-100 border-0 shadow-sm text-white animate__animated animate__fadeInUp" style="background: linear-gradient(135deg, #28a745, #58d68d);">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-white-50">اعضای تایید شده</small>
                        <h3 class="fw-bold mb-0">{{ toPersianNumber($stats['approved_memberships'] ?? 0) }}</h3>
                    </div>
                    <i class="bi bi-shield-check fs-1 text-white-50"></i>
                </div>
            </div>
        </div>

        {{-- عضویت‌های در انتظار --}}
        <div class="col-xl-3 col-md-6">
            <div class="card h-100 border-0 shadow-sm text-white animate__animated animate__fadeInUp" style="background: linear-gradient(135deg, #f39c12, #f5b041);">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-white-50">در انتظار بررسی</small>
                        <h3 class="fw-bold mb-0">{{ toPersianNumber($stats['pending_memberships'] ?? 0) }}</h3>
                    </div>
                    <i class="bi bi-hourglass-split fs-1 text-white-50"></i>
                </div>
            </div>
        </div>

        {{-- عضویت‌های رد شده --}}
        <div class="col-xl-3 col-md-6">
            <div class="card h-100 border-0 shadow-sm text-white animate__animated animate__fadeInUp" style="background: linear-gradient(135deg, #dc3545, #e35d6a);">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-white-50">رد شده</small>
                        <h3 class="fw-bold mb-0">{{ toPersianNumber($stats['rejected_memberships'] ?? 0) }}</h3>
                    </div>
                    <i class="bi bi-x-octagon fs-1 text-white-50"></i>
                </div>
            </div>
        </div>

        {{-- پرداخت‌های تایید شده --}}
        <div class="col-xl-3 col-md-6">
            <div class="card h-100 border-0 shadow-sm text-white animate__animated animate__fadeInUp" style="background: linear-gradient(135deg, #17a2b8, #3bc8e7);">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-white-50">تعداد پرداخت‌های تایید شده</small>
                        <h3 class="fw-bold mb-0">{{ toPersianNumber($stats['approved_payments'] ?? 0) }}</h3>
                    </div>
                    <i class="bi bi-credit-card fs-1 text-white-50"></i>
                </div>
            </div>
        </div>

        {{-- کل پرداخت‌ها --}}
        <div class="col-xl-3 col-md-6">
            <div class="card h-100 border-0 shadow-sm text-white animate__animated animate__fadeInUp" style="background: linear-gradient(135deg, #6610f2, #8e44ad);">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-white-50">کل پرداخت‌ها (تومان)</small>
                        <h4 class="fw-bold mb-0">{{ toPersianNumber(number_format($stats['total_amount'] ?? 0)) }}</h4>
                    </div>
                    <i class="bi bi-cash-stack fs-1 text-white-50"></i>
                </div>
            </div>
        </div>

        {{-- پرداخت‌های ماه جاری --}}
        <div class="col-xl-3 col-md-6">
            <div class="card h-100 border-0 shadow-sm text-white animate__animated animate__fadeInUp" style="background: linear-gradient(135deg, #0dcaf0, #20c997);">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-white-50">پرداخت‌های این ماه</small>
                        <h4 class="fw-bold mb-0">{{ toPersianNumber(number_format($stats['monthly_amount'] ?? 0)) }}</h4>
                    </div>
                    <i class="bi bi-calendar2-week fs-1 text-white-50"></i>
                </div>
            </div>
        </div>

        {{-- دوره‌ها و برنامه‌ها --}}
        <div class="col-xl-3 col-md-6">
            <div class="card h-100 border-0 shadow-sm text-white animate__animated animate__fadeInUp" style="background: linear-gradient(135deg, #1abc9c, #16a085);">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <small class="text-white-50">محتوا</small>
                        <i class="bi bi-journal-code fs-5 text-white-50"></i>
                    </div>
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small text-white-50">دوره‌ها</div>
                            <h5 class="fw-bold mb-0">{{ toPersianNumber($stats['courses'] ?? 0) }}</h5>
                        </div>
                        <div class="text-end">
                            <div class="small text-white-50">برنامه‌ها</div>
                            <h5 class="fw-bold mb-0">{{ toPersianNumber($stats['programs'] ?? 0) }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ثبت‌نام‌ها --}}
        <div class="col-xl-3 col-md-6">
            <div class="card h-100 border-0 shadow-sm text-white animate__animated animate__fadeInUp" style="background: linear-gradient(135deg, #ff6f61, #ff9a8b);">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <small class="text-white-50">ثبت‌نام‌ها</small>
                        <i class="bi bi-check2-square fs-5 text-white-50"></i>
                    </div>
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small text-white-50">دوره</div>
                            <h5 class="fw-bold mb-0">{{ toPersianNumber($stats['course_registrations'] ?? 0) }}</h5>
                        </div>
                        <div class="text-end">
                            <div class="small text-white-50">برنامه</div>
                            <h5 class="fw-bold mb-0">{{ toPersianNumber($stats['program_registrations'] ?? 0) }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- نمودار پرداخت‌ها --}}
    <div class="row mb-5">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm animate__animated animate__fadeInUp">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0"><i class="bi bi-bar-chart-line text-primary me-2"></i> روند پرداخت‌ها (ماهانه)</h6>
                </div>
                <div class="card-body">
                    <canvas id="paymentsChart" height="100"></canvas>
                </div>
            </div>
        </div>

        {{-- پرداخت‌های اخیر --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm animate__animated animate__fadeInUp">
                <div class="card-header bg-white border-0">
                    <h6 class="fw-bold mb-0"><i class="bi bi-clock-history text-secondary me-2"></i> پرداخت‌های اخیر</h6>
                </div>
                <ul class="list-group list-group-flush">
                    @forelse($latestPayments as $p)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="bi bi-credit-card-2-front text-success me-2"></i>
                                <span class="fw-bold">{{ toPersianNumber(number_format($p->amount)) }} تومان</span>
                                <div class="text-muted small mt-1">{{ $p->user->profile->first_name ?? '---' }} {{ $p->user->profile->last_name ?? '' }}</div>
                            </div>
                            <span class="badge bg-light text-dark">{{ toPersianNumber(jdate($p->created_at)->format('m/d')) }}</span>
                        </li>
                    @empty
                        <li class="list-group-item text-center text-muted">پرداختی ثبت نشده است</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    {{-- آمار کاربران --}}
    <div class="card border-0 shadow-sm animate__animated animate__fadeInUp">
        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0"><i class="bi bi-people text-primary me-2"></i> کاربران فعال اخیر</h6>
            <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-list"></i> مشاهده همه
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>نام</th>
                        <th>شماره تماس</th>
                        <th>وضعیت عضویت</th>
                        <th>تاریخ عضویت</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($latestUsers as $u)
                        <tr>
                            <td>{{ $u->profile->first_name ?? '' }} {{ $u->profile->last_name ?? '' }}</td>
                            <td>
                                @if($u->profile?->membership_status == 'approved')
                                    <span class="badge bg-success">تایید شده</span>
                                @elseif($u->profile?->membership_status == 'pending')
                                    <span class="badge bg-warning text-dark">در انتظار</span>
                                @elseif($u->profile?->membership_status == 'rejected')
                                    <span class="badge bg-danger">رد شده</span>
                                @else
                                    <span class="badge bg-danger">بدون اطلاعات</span>
                                @endif
                            </td>
                            <td>{{ toPersianNumber(jdate($u->created_at)->format('Y/m/d')) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted">کاربری یافت نشد</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<!--
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
-->
<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('paymentsChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($chart['months'] ?? []) !!},
            datasets: [{
                label: 'مبلغ پرداخت‌ها (تومان)',
                data: {!! json_encode($chart['values'] ?? []) !!},
                backgroundColor: 'rgba(13, 110, 253, 0.5)',
                borderColor: '#0d6efd',
                borderWidth: 1,
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
});
</script>
@endpush
