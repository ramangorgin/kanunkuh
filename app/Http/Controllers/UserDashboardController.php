<?php

/**
 * User dashboard pages and related profile views.
 */

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

/**
 * Renders user-facing dashboard sections and related listings.
 */
class UserDashboardController extends Controller
{
    /**
     * Display the main user dashboard with profile and medical record data.
     */
    public function index()
    {
        $user = Auth::user()->load([
            'profile',
            'medicalRecord',
        ]);

        return view('user.myDashboard', [
            'user' => $user,
            'profile' => $user->profile,
            'medicalRecord' => $user->medicalRecord,
        ]);
    }

    /**
     * Show the user profile page.
     */
    public function profile()
    {
        return view('user.myProfile');
    }

    /**
     * Show the user insurance page.
     */
    public function insurance()
    {
        return view('user.myInsurance');
    }

    /**
     * Show the user payments page.
     */
    public function payments()
    {
        return view('user.myPayments');
    }

    /**
     * List approved course registrations for the current user.
     */
    public function courses()
    {
        $user = Auth::user();
        $registrations = \App\Models\CourseRegistration::where('user_id', $user->id)
            ->where('status', 'approved')
            ->with(['course.teacher', 'course.federationCourse'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('user.myCourses', compact('registrations'));
    }

    /**
     * List approved program participations for the current user.
     */
    public function programs()
    {
        $user = Auth::user();
        $programs = \App\Models\Program::whereHas('registrations', function($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->where('status', 'approved');
        })
        ->with(['report', 'files' => function($query) {
            $query->where('file_type', 'image');
        }])
        ->orderBy('execution_date', 'desc')
        ->get();
        
        return view('user.myPrograms', compact('programs'));
    }

}
