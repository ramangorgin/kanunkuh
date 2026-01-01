<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

class UserDashboardController extends Controller
{

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

    public function profile()
    {
        return view('user.myProfile');
    }

    public function insurance()
    {
        return view('user.myInsurance');
    }

    public function payments()
    {
        return view('user.myPayments');
    }

    public function courses()
    {
        $user = Auth::user();
        
        // Get courses where user has participated (approved registrations)
        $registrations = \App\Models\CourseRegistration::where('user_id', $user->id)
            ->where('status', 'approved')
            ->with(['course.teacher', 'course.federationCourse'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('user.myCourses', compact('registrations'));
    }

    public function programs()
    {
        $user = Auth::user();
        
        // Get programs where user has participated (approved registrations)
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
