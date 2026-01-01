<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CourseRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserCourseController extends Controller
{
    /**
     * Download certificate for a course registration
     */
    public function downloadCertificate($registrationId)
    {
        $user = Auth::user();
        
        $registration = CourseRegistration::where('id', $registrationId)
            ->where('user_id', $user->id)
            ->where('status', 'approved')
            ->first();
        
        if (!$registration) {
            abort(404, 'ثبت‌نام یافت نشد.');
        }
        
        if (!$registration->certificate_file) {
            abort(404, 'گواهینامه برای این دوره موجود نیست.');
        }
        
        $filePath = storage_path('app/public/' . $registration->certificate_file);
        
        if (!file_exists($filePath)) {
            abort(404, 'فایل گواهینامه یافت نشد.');
        }
        
        return response()->download($filePath);
    }
}

