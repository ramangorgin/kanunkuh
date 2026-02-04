<?php

/**
 * Provides the admin dashboard view with summary metrics and recent activity.
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Profile;
use App\Models\Payment;
use App\Models\Program;
use App\Models\Course;
use App\Models\ProgramRegistration;
use App\Models\CourseRegistration;
use Carbon\Carbon;
use Morilog\Jalali\Jalalian;

/**
 * Builds dashboard statistics, recent records, and chart data for administrators.
 */
class AdminDashboardController extends Controller
{
    /**
     * Renders the admin dashboard with aggregate counts, recent items, and chart series.
     */
    public function index()
    {
        // Aggregate summary metrics for dashboard widgets.
        $totalUsers = User::count();
        $pendingMemberships = Profile::where('membership_status', 'pending')->count();
        $approvedMembers = Profile::where('membership_status', 'approved')->count();
        $rejectedMembers = Profile::where('membership_status', 'rejected')->count();
        $approvedPayments = Payment::where('status', 'approved')->count();
        $totalAmount = Payment::where('status', 'approved')->sum('amount');

        $monthlyAmount = Payment::where('status', 'approved')
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('amount');

        $programCount = Program::count();
        $courseCount = Course::count();
        $programRegistrations = ProgramRegistration::count();
        $courseRegistrations = CourseRegistration::count();

        $stats = [
            'users' => $totalUsers,
            'pending_memberships' => $pendingMemberships,
            'approved_memberships' => $approvedMembers,
            'rejected_memberships' => $rejectedMembers,
            'approved_payments' => $approvedPayments,
            'total_amount' => $totalAmount,
            'monthly_amount' => $monthlyAmount,
            'programs' => $programCount,
            'courses' => $courseCount,
            'program_registrations' => $programRegistrations,
            'course_registrations' => $courseRegistrations,
        ];

        // Recent payment activity for quick overview.
        $latestPayments = Payment::with('user.profile')
            ->latest()
            ->take(5)
            ->get();

        // Recently registered users.
        $latestUsers = User::with('profile')
            ->latest()
            ->take(5)
            ->get();

        // Payment totals for the last 12 months in Jalali labels.
        $months = [];
        $values = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $label = Jalalian::fromCarbon($month)->format('Y/m');

            $sum = Payment::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->where('status', 'approved')
                ->sum('amount');

            $months[] = $label;
            $values[] = (int) $sum;
        }

        $chart = [
            'months' => $months,
            'values' => $values,
        ];

        return view('admin.dashboard', compact('stats', 'latestPayments', 'latestUsers', 'chart'));
    }
}
