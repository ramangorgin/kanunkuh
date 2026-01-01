<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminCourseRegistrationController extends Controller
{
    /**
     * Show all registrations for a specific course
     */
    public function index(Course $course)
    {
        $registrations = CourseRegistration::with(['user.profile', 'payment'])
            ->where('course_id', $course->id)
            ->latest()
            ->get();
            
        return view('admin.course_registrations.index', compact('course', 'registrations'));
    }

    /**
     * Approve a registration (independent of payment)
     */
    public function approve(Course $course, $registrationId)
    {
        try {
            \Log::info('Approve course registration called', [
                'course_id' => $course->id,
                'registration_id' => $registrationId
            ]);
            
            $registration = CourseRegistration::where('id', $registrationId)
                ->where('course_id', $course->id)
                ->first();
            
            if (!$registration) {
                \Log::error('Course registration not found', [
                    'course_id' => $course->id,
                    'registration_id' => $registrationId
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'ثبت‌نام یافت نشد.'
                ], 404);
            }
            
            $registration->status = 'approved';
            $saved = $registration->save();
            
            \Log::info('Course registration saved', ['saved' => $saved, 'status' => $registration->status]);
            
            return response()->json([
                'success' => true, 
                'message' => 'ثبت‌نام تأیید شد.',
                'data' => [
                    'id' => $registration->id,
                    'status' => $registration->status
                ]
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('Database error in approve course registration', [
                'error' => $e->getMessage(),
                'sql' => $e->getSql(),
                'bindings' => $e->getBindings()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'خطای دیتابیس: ' . $e->getMessage(),
                'error_type' => 'database'
            ], 500);
        } catch (\Exception $e) {
            \Log::error('Error in approve course registration', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'خطا: ' . $e->getMessage(),
                'error_type' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    /**
     * Reject a registration
     */
    public function reject(Course $course, $registrationId)
    {
        try {
            \Log::info('Reject course registration called', [
                'course_id' => $course->id,
                'registration_id' => $registrationId
            ]);
            
            $registration = CourseRegistration::where('id', $registrationId)
                ->where('course_id', $course->id)
                ->first();
            
            if (!$registration) {
                \Log::error('Course registration not found for reject', [
                    'course_id' => $course->id,
                    'registration_id' => $registrationId
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'ثبت‌نام یافت نشد.'
                ], 404);
            }
            
            $registration->status = 'rejected';
            $registration->save();
            
            return response()->json([
                'success' => true, 
                'message' => 'ثبت‌نام رد شد.',
                'data' => [
                    'id' => $registration->id,
                    'status' => $registration->status
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in reject course registration', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'خطا: ' . $e->getMessage(),
                'error_type' => get_class($e)
            ], 500);
        }
    }

    /**
     * Cancel a registration
     */
    public function cancel(Course $course, $registrationId)
    {
        try {
            \Log::info('Cancel course registration called', [
                'course_id' => $course->id,
                'registration_id' => $registrationId
            ]);
            
            $registration = CourseRegistration::where('id', $registrationId)
                ->where('course_id', $course->id)
                ->first();
            
            if (!$registration) {
                return response()->json([
                    'success' => false,
                    'message' => 'ثبت‌نام یافت نشد.'
                ], 404);
            }
            
            $registration->status = 'cancelled';
            $registration->save();
            
            return response()->json([
                'success' => true, 
                'message' => 'ثبت‌نام لغو شد.',
                'data' => [
                    'id' => $registration->id,
                    'status' => $registration->status
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in cancel course registration', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'خطا: ' . $e->getMessage(),
                'error_type' => get_class($e)
            ], 500);
        }
    }

    /**
     * Upload certificate file for a registration
     */
    public function uploadCertificate(Course $course, $registrationId, Request $request)
    {
        try {
            $request->validate([
                'certificate_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB max
            ], [
                'certificate_file.required' => 'لطفاً فایل گواهینامه را انتخاب کنید.',
                'certificate_file.file' => 'فایل انتخاب شده معتبر نیست.',
                'certificate_file.mimes' => 'فرمت‌های مجاز: PDF, JPG, JPEG, PNG',
                'certificate_file.max' => 'حجم فایل حداکثر 10 مگابایت است.',
            ]);

            $registration = CourseRegistration::where('id', $registrationId)
                ->where('course_id', $course->id)
                ->first();
            
            if (!$registration) {
                return response()->json([
                    'success' => false,
                    'message' => 'ثبت‌نام یافت نشد.'
                ], 404);
            }

            // Delete old certificate if exists
            if ($registration->certificate_file) {
                Storage::disk('public')->delete($registration->certificate_file);
            }

            // Store new certificate
            $certificatePath = $request->file('certificate_file')->store('course_certificates', 'public');
            $registration->certificate_file = $certificatePath;
            $registration->save();

            return response()->json([
                'success' => true,
                'message' => 'گواهینامه با موفقیت آپلود شد.',
                'data' => [
                    'certificate_file' => $certificatePath,
                    'certificate_url' => Storage::url($certificatePath)
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطا در اعتبارسنجی: ' . implode(', ', $e->errors()['certificate_file'] ?? []),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error in upload certificate', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'خطا: ' . $e->getMessage(),
                'error_type' => get_class($e)
            ], 500);
        }
    }
}

