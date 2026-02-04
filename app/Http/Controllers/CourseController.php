<?php

/**
 * Course administration and public course presentation endpoints.
 */

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Teacher;
use App\Models\FederationCourse;
use App\Models\CoursePrerequisite;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

/**
 * Manages course CRUD, registration context, and date normalization.
 */
class CourseController extends Controller
{
    /**
     * Convert Persian/Arabic digits to English
     */
    private function toEnglishDigits(string $str): string
    {
        $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $arabic = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        $english = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        
        $str = str_replace($persian, $english, $str);
        $str = str_replace($arabic, $english, $str);
        
        return $str;
    }

    /**
     * Convert Jalali date string to Gregorian Carbon instance
     */
    private function convertJalaliToGregorian(?string $dateInput, bool $withTime = false): ?string
    {
        if (!$dateInput || trim($dateInput) === '') {
            return null;
        }

        $dateInput = $this->toEnglishDigits(trim($dateInput));

        // Try to detect if it's already in Gregorian format
        if (preg_match('/^(\d{4})[-\/](\d{2})[-\/](\d{2})/', $dateInput, $matches)) {
            $year = (int)$matches[1];
            if ($year >= 1900 && $year <= 2100) {
                try {
                    $carbon = \Carbon\Carbon::parse($dateInput);
                    return $withTime 
                        ? $carbon->format('Y-m-d H:i:s')
                        : $carbon->format('Y-m-d');
                } catch (\Exception $e) {
                    // Continue to try Jalali parsing
                }
            }
        }

        // Parse as Jalali
        try {
            if ($withTime && preg_match('/(\d{4})\/(\d{2})\/(\d{2})\s+(\d{2}):(\d{2})/', $dateInput, $matches)) {
                $year = (int)$matches[1];
                $month = (int)$matches[2];
                $day = (int)$matches[3];
                $hour = (int)$matches[4];
                $minute = (int)$matches[5];
                
                $jalali = new \Morilog\Jalali\Jalalian($year, $month, $day, $hour, $minute);
                $gregorian = $jalali->toCarbon();
                return $gregorian->format('Y-m-d H:i:s');
            } elseif (preg_match('/(\d{4})\/(\d{2})\/(\d{2})/', $dateInput, $matches)) {
                $year = (int)$matches[1];
                $month = (int)$matches[2];
                $day = (int)$matches[3];
                
                $jalali = new \Morilog\Jalali\Jalalian($year, $month, $day);
                $gregorian = $jalali->toCarbon();
                return $gregorian->format('Y-m-d');
            }
        } catch (\Exception $e) {
            \Log::error('Date conversion error', ['input' => $dateInput, 'error' => $e->getMessage()]);
        }

        return null;
    }

    /**
     * Show the public archive of courses.
     */
    public function archive()
    {
        $courses = Course::with('teacher', 'federationCourse')->latest()->paginate(10);
        return view('courses.archive', compact('courses'));
    }

    /**
     * List courses for the admin index.
     */
    public function index()
    {
        $courses = Course::with('teacher', 'federationCourse')
            ->orderBy('start_date', 'desc')
            ->get();
        return view('admin.courses.index', compact('courses'));
    }

    /**
     * Show the admin course creation form.
     */
    public function create()
    {
        $teachers = Teacher::orderBy('last_name')->get();
        $federationCourses = FederationCourse::orderBy('title')->get();
        
        return view('admin.courses.create', compact('teachers', 'federationCourses'));
    }

    /**
     * Store a new course and related prerequisites.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'federation_course_id' => 'nullable|exists:federation_courses,id',
            'is_federation_course' => 'nullable|in:0,1',
            'teacher_id' => 'nullable|exists:teachers,id',
            'create_new_teacher' => 'nullable|in:0,1',
            'teacher_first_name' => 'nullable|required_if:create_new_teacher,1|string|max:255',
            'teacher_last_name' => 'nullable|required_if:create_new_teacher,1|string|max:255',
            'teacher_birth_date' => 'nullable|string',
            'teacher_biography' => 'nullable|string',
            'teacher_profile_image' => 'nullable|image|mimes:jpeg,png,gif|max:2048',
            'teacher_skills' => 'nullable|array',
            'teacher_skills.*' => 'string|max:255',
            'teacher_certificates' => 'nullable|array',
            'teacher_certificates.*' => 'string|max:255',
            'start_date' => 'required|string',
            'end_date' => 'required|string',
            'start_time' => 'nullable|string',
            'end_time' => 'nullable|string',
            'duration' => 'nullable|integer|min:0',
            'place' => 'nullable|string|max:255',
            'place_address' => 'nullable|string|max:500',
            'place_lat' => 'nullable|numeric|between:-90,90',
            'place_lon' => 'nullable|numeric|between:-180,180',
            'capacity' => 'nullable|integer|min:1',
            'is_free' => 'required|in:0,1',
            'member_cost' => 'nullable|required_if:is_free,0|string',
            'guest_cost' => 'nullable|required_if:is_free,0|string',
            'card_number' => 'nullable|required_if:is_free,0|string|max:255',
            'sheba_number' => 'nullable|required_if:is_free,0|string|max:255',
            'card_holder' => 'nullable|required_if:is_free,0|string|max:255',
            'bank_name' => 'nullable|required_if:is_free,0|string|max:255',
            'is_registration_open' => 'required|in:0,1',
            'registration_deadline' => 'nullable|string',
            'status' => 'required|in:draft,published,completed,canceled',
            'prerequisites' => 'nullable|array',
            'prerequisites.*' => 'exists:federation_courses,id',
        ], [
            'title.required' => 'لطفاً عنوان دوره را وارد کنید.',
            'federation_course_id.exists' => 'دوره فدراسیون انتخاب شده معتبر نیست.',
            'teacher_id.exists' => 'مدرس انتخاب شده معتبر نیست.',
            'teacher_first_name.required_if' => 'لطفاً نام مدرس را وارد کنید.',
            'teacher_last_name.required_if' => 'لطفاً نام خانوادگی مدرس را وارد کنید.',
            'start_date.required' => 'لطفاً تاریخ شروع دوره را وارد کنید.',
            'end_date.required' => 'لطفاً تاریخ پایان دوره را وارد کنید.',
            'is_free.required' => 'لطفاً مشخص کنید که دوره رایگان است یا خیر.',
            'member_cost.required_if' => 'هزینه برای اعضا الزامی است.',
            'guest_cost.required_if' => 'هزینه برای مهمانان الزامی است.',
            'card_number.required_if' => 'شماره کارت الزامی است.',
            'sheba_number.required_if' => 'شماره شبا الزامی است.',
            'card_holder.required_if' => 'نام دارنده حساب الزامی است.',
            'bank_name.required_if' => 'نام بانک الزامی است.',
            'is_registration_open.required' => 'لطفاً وضعیت ثبت‌نام را مشخص کنید.',
            'status.required' => 'لطفاً وضعیت دوره را انتخاب کنید.',
            'status.in' => 'وضعیت انتخاب شده معتبر نیست.',
            'prerequisites.*.exists' => 'پیش‌نیاز انتخاب شده معتبر نیست.',
        ]);

        DB::transaction(function () use ($validated, $request) {
            // Handle teacher creation or selection
            $teacherId = null;
            if ($request->input('create_new_teacher') == '1') {
                $teacherBirthDate = null;
                if ($request->filled('teacher_birth_date')) {
                    $teacherBirthDate = $this->convertJalaliToGregorian($request->input('teacher_birth_date'), false);
                }
                
                // Handle profile image upload
                $profileImagePath = null;
                if ($request->hasFile('teacher_profile_image')) {
                    $profileImagePath = $request->file('teacher_profile_image')->store('teachers', 'public');
                }
                
                // Handle skills and certificates as JSON arrays
                $skills = null;
                $certificates = null;
                if ($request->filled('teacher_skills')) {
                    $skills = json_encode($request->input('teacher_skills'), JSON_UNESCAPED_UNICODE);
                }
                if ($request->filled('teacher_certificates')) {
                    $certificates = json_encode($request->input('teacher_certificates'), JSON_UNESCAPED_UNICODE);
                }
                
                $teacher = Teacher::create([
                    'first_name' => $validated['teacher_first_name'],
                    'last_name' => $validated['teacher_last_name'],
                    'birth_date' => $teacherBirthDate,
                    'biography' => $validated['teacher_biography'] ?? null,
                    'profile_image' => $profileImagePath,
                    'skills' => $skills,
                    'certificates' => $certificates,
                ]);
                $teacherId = $teacher->id;
            } elseif ($request->filled('teacher_id')) {
                $teacherId = $validated['teacher_id'];
            }

            // Convert dates
            $startDate = $this->convertJalaliToGregorian($validated['start_date'], false);
            $endDate = $this->convertJalaliToGregorian($validated['end_date'], false);
            $registrationDeadline = $validated['registration_deadline'] 
                ? $this->convertJalaliToGregorian($validated['registration_deadline'], true) 
                : null;

            // Handle costs (remove commas)
            $memberCost = null;
            $guestCost = null;
            if ($request->input('is_free') == '0') {
                $memberCost = $validated['member_cost'] 
                    ? (int) str_replace(',', '', $this->toEnglishDigits($validated['member_cost']))
                    : null;
                $guestCost = $validated['guest_cost'] 
                    ? (int) str_replace(',', '', $this->toEnglishDigits($validated['guest_cost']))
                    : null;
            }

            // Determine federation_course_id
            $federationCourseId = null;
            if ($request->input('is_federation_course') == '1' && $request->filled('federation_course_id')) {
                $federationCourseId = $validated['federation_course_id'];
            }

            // Create course
            $course = Course::create([
                'federation_course_id' => $federationCourseId,
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'teacher_id' => $teacherId,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'start_time' => $validated['start_time'] ?? null,
                'end_time' => $validated['end_time'] ?? null,
                'duration' => $validated['duration'] ?? null,
                'place' => $validated['place'] ?? null,
                'place_address' => $validated['place_address'] ?? null,
                'place_lat' => $validated['place_lat'] ?? null,
                'place_lon' => $validated['place_lon'] ?? null,
                'capacity' => $validated['capacity'] ?? null,
                'is_free' => $request->input('is_free') == '1',
                'member_cost' => $memberCost,
                'guest_cost' => $guestCost,
                'card_number' => $validated['card_number'] ?? null,
                'sheba_number' => $validated['sheba_number'] ?? null,
                'card_holder' => $validated['card_holder'] ?? null,
                'bank_name' => $validated['bank_name'] ?? null,
                'is_registration_open' => $request->input('is_registration_open') == '1',
                'registration_deadline' => $registrationDeadline,
                'status' => $validated['status'],
            ]);

            // Handle prerequisites (only if federation course is selected)
            if ($federationCourseId && $request->filled('prerequisites')) {
                foreach ($request->input('prerequisites') as $prerequisiteId) {
                    CoursePrerequisite::create([
                        'course_id' => $federationCourseId,
                        'prerequisite_id' => $prerequisiteId,
                    ]);
                }
            }
        });

        return redirect()->route('admin.courses.index')
            ->with('success', 'دوره با موفقیت ایجاد شد.');
    }

    /**
     * Display a course with registration context for the current user.
     */
    public function show(Course $course)
    {
        $course->load('teacher', 'federationCourse', 'registrations.user.profile');
        
        $user = auth()->user();
        $userRegistration = null;
        $canRegister = false;
        $registrationMessage = null;

        if ($user) {
            $userRegistration = $course->registrations()
                ->where('user_id', $user->id)
                ->first();
            
            // Check if user can register
            if (!$userRegistration) {
                // Check registration deadline
                $deadlinePassed = $course->registration_deadline && now()->gt($course->registration_deadline);
                
                // Check capacity
                $currentRegistrations = $course->registrations()
                    ->where('status', 'approved')
                    ->count();
                $capacityFull = $course->capacity && $currentRegistrations >= $course->capacity;
                
                // Check prerequisites (only for authenticated users)
                $prerequisitesMet = $course->userHasCompletedPrerequisites($user->id);
                
                if ($deadlinePassed) {
                    $registrationMessage = 'مهلت ثبت‌نام به پایان رسیده است.';
                } elseif ($capacityFull) {
                    $registrationMessage = 'ظرفیت دوره تکمیل شده است.';
                } elseif (!$prerequisitesMet) {
                    $registrationMessage = 'شما پیش‌نیازهای این دوره را تکمیل نکرده‌اید.';
                } else {
                    $canRegister = true;
                }
            }
        } else {
            // For guests, check only deadline and capacity
            if ($course->is_registration_open) {
                $deadlinePassed = $course->registration_deadline && now()->gt($course->registration_deadline);
                $currentRegistrations = $course->registrations()
                    ->where('status', 'approved')
                    ->count();
                $capacityFull = $course->capacity && $currentRegistrations >= $course->capacity;
                
                if ($deadlinePassed) {
                    $registrationMessage = 'مهلت ثبت‌نام به پایان رسیده است.';
                } elseif ($capacityFull) {
                    $registrationMessage = 'ظرفیت دوره تکمیل شده است.';
                } else {
                    $canRegister = true;
                }
            } else {
                $registrationMessage = 'ثبت‌نام برای این دوره بسته است.';
            }
        }

        return view('courses.show', compact(
            'course',
            'userRegistration',
            'canRegister',
            'registrationMessage'
        ));
    }

    /**
     * Show the admin course edit form.
     */
    public function edit(Course $course)
    {
        $course->load('federationCourse', 'teacher');
        $teachers = Teacher::orderBy('last_name')->get();
        $federationCourses = FederationCourse::orderBy('title')->get();
        
        // Get current prerequisites
        $currentPrerequisites = [];
        if ($course->federation_course_id) {
            $currentPrerequisites = CoursePrerequisite::where('course_id', $course->federation_course_id)
                ->pluck('prerequisite_id')
                ->toArray();
        }
        
        return view('admin.courses.edit', compact('course', 'teachers', 'federationCourses', 'currentPrerequisites'));
    }

    /**
     * Update a course and related prerequisites.
     */
    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'federation_course_id' => 'nullable|exists:federation_courses,id',
            'is_federation_course' => 'nullable|in:0,1',
            'teacher_id' => 'nullable|exists:teachers,id',
            'create_new_teacher' => 'nullable|in:0,1',
            'teacher_first_name' => 'nullable|required_if:create_new_teacher,1|string|max:255',
            'teacher_last_name' => 'nullable|required_if:create_new_teacher,1|string|max:255',
            'teacher_birth_date' => 'nullable|string',
            'teacher_biography' => 'nullable|string',
            'teacher_profile_image' => 'nullable|image|mimes:jpeg,png,gif|max:2048',
            'teacher_skills' => 'nullable|array',
            'teacher_skills.*' => 'string|max:255',
            'teacher_certificates' => 'nullable|array',
            'teacher_certificates.*' => 'string|max:255',
            'start_date' => 'required|string',
            'end_date' => 'required|string',
            'start_time' => 'nullable|string',
            'end_time' => 'nullable|string',
            'duration' => 'nullable|integer|min:0',
            'place' => 'nullable|string|max:255',
            'place_address' => 'nullable|string|max:500',
            'place_lat' => 'nullable|numeric|between:-90,90',
            'place_lon' => 'nullable|numeric|between:-180,180',
            'capacity' => 'nullable|integer|min:1',
            'is_free' => 'required|in:0,1',
            'member_cost' => 'nullable|required_if:is_free,0|string',
            'guest_cost' => 'nullable|required_if:is_free,0|string',
            'card_number' => 'nullable|required_if:is_free,0|string|max:255',
            'sheba_number' => 'nullable|required_if:is_free,0|string|max:255',
            'card_holder' => 'nullable|required_if:is_free,0|string|max:255',
            'bank_name' => 'nullable|required_if:is_free,0|string|max:255',
            'is_registration_open' => 'required|in:0,1',
            'registration_deadline' => 'nullable|string',
            'status' => 'required|in:draft,published,completed,canceled',
            'prerequisites' => 'nullable|array',
            'prerequisites.*' => 'exists:federation_courses,id',
        ], [
            'title.required' => 'لطفاً عنوان دوره را وارد کنید.',
            'federation_course_id.exists' => 'دوره فدراسیون انتخاب شده معتبر نیست.',
            'teacher_id.exists' => 'مدرس انتخاب شده معتبر نیست.',
            'teacher_first_name.required_if' => 'لطفاً نام مدرس را وارد کنید.',
            'teacher_last_name.required_if' => 'لطفاً نام خانوادگی مدرس را وارد کنید.',
            'start_date.required' => 'لطفاً تاریخ شروع دوره را وارد کنید.',
            'end_date.required' => 'لطفاً تاریخ پایان دوره را وارد کنید.',
            'is_free.required' => 'لطفاً مشخص کنید که دوره رایگان است یا خیر.',
            'member_cost.required_if' => 'هزینه برای اعضا الزامی است.',
            'guest_cost.required_if' => 'هزینه برای مهمانان الزامی است.',
            'card_number.required_if' => 'شماره کارت الزامی است.',
            'sheba_number.required_if' => 'شماره شبا الزامی است.',
            'card_holder.required_if' => 'نام دارنده حساب الزامی است.',
            'bank_name.required_if' => 'نام بانک الزامی است.',
            'is_registration_open.required' => 'لطفاً وضعیت ثبت‌نام را مشخص کنید.',
            'status.required' => 'لطفاً وضعیت دوره را انتخاب کنید.',
            'status.in' => 'وضعیت انتخاب شده معتبر نیست.',
            'prerequisites.*.exists' => 'پیش‌نیاز انتخاب شده معتبر نیست.',
        ]);

        DB::transaction(function () use ($validated, $request, $course) {
            // Handle teacher creation or selection
            $teacherId = $course->teacher_id; // Keep existing if not changed
            if ($request->input('create_new_teacher') == '1') {
                $teacherBirthDate = null;
                if ($request->filled('teacher_birth_date')) {
                    $teacherBirthDate = $this->convertJalaliToGregorian($request->input('teacher_birth_date'), false);
                }
                
                // Handle profile image upload
                $profileImagePath = null;
                if ($request->hasFile('teacher_profile_image')) {
                    $profileImagePath = $request->file('teacher_profile_image')->store('teachers', 'public');
                }
                
                // Handle skills and certificates as JSON arrays
                $skills = null;
                $certificates = null;
                if ($request->filled('teacher_skills')) {
                    $skills = json_encode($request->input('teacher_skills'), JSON_UNESCAPED_UNICODE);
                }
                if ($request->filled('teacher_certificates')) {
                    $certificates = json_encode($request->input('teacher_certificates'), JSON_UNESCAPED_UNICODE);
                }
                
                $teacher = Teacher::create([
                    'first_name' => $validated['teacher_first_name'],
                    'last_name' => $validated['teacher_last_name'],
                    'birth_date' => $teacherBirthDate,
                    'biography' => $validated['teacher_biography'] ?? null,
                    'profile_image' => $profileImagePath,
                    'skills' => $skills,
                    'certificates' => $certificates,
                ]);
                $teacherId = $teacher->id;
            } elseif ($request->filled('teacher_id')) {
                $teacherId = $validated['teacher_id'];
            }

            // Convert dates
            $startDate = $this->convertJalaliToGregorian($validated['start_date'], false);
            $endDate = $this->convertJalaliToGregorian($validated['end_date'], false);
            $registrationDeadline = $validated['registration_deadline'] 
                ? $this->convertJalaliToGregorian($validated['registration_deadline'], true) 
                : null;

            // Handle costs (remove commas)
            $memberCost = null;
            $guestCost = null;
            if ($request->input('is_free') == '0') {
                $memberCost = $validated['member_cost'] 
                    ? (int) str_replace(',', '', $this->toEnglishDigits($validated['member_cost']))
                    : null;
                $guestCost = $validated['guest_cost'] 
                    ? (int) str_replace(',', '', $this->toEnglishDigits($validated['guest_cost']))
                    : null;
            }

            // Determine federation_course_id
            $federationCourseId = null;
            if ($request->input('is_federation_course') == '1' && $request->filled('federation_course_id')) {
                $federationCourseId = $validated['federation_course_id'];
            }

            // Update course
            $course->update([
                'federation_course_id' => $federationCourseId,
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'teacher_id' => $teacherId,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'start_time' => $validated['start_time'] ?? null,
                'end_time' => $validated['end_time'] ?? null,
                'duration' => $validated['duration'] ?? null,
                'place' => $validated['place'] ?? null,
                'place_address' => $validated['place_address'] ?? null,
                'place_lat' => $validated['place_lat'] ?? null,
                'place_lon' => $validated['place_lon'] ?? null,
                'capacity' => $validated['capacity'] ?? null,
                'is_free' => $request->input('is_free') == '1',
                'member_cost' => $memberCost,
                'guest_cost' => $guestCost,
                'card_number' => $validated['card_number'] ?? null,
                'sheba_number' => $validated['sheba_number'] ?? null,
                'card_holder' => $validated['card_holder'] ?? null,
                'bank_name' => $validated['bank_name'] ?? null,
                'is_registration_open' => $request->input('is_registration_open') == '1',
                'registration_deadline' => $registrationDeadline,
                'status' => $validated['status'],
            ]);

            // Handle prerequisites (only if federation course is selected)
            if ($federationCourseId) {
                // Delete old prerequisites
                CoursePrerequisite::where('course_id', $federationCourseId)->delete();
                
                // Add new prerequisites
                if ($request->filled('prerequisites')) {
                    foreach ($request->input('prerequisites') as $prerequisiteId) {
                        CoursePrerequisite::create([
                            'course_id' => $federationCourseId,
                            'prerequisite_id' => $prerequisiteId,
                        ]);
                    }
                }
            } else {
                // If no federation course, remove all prerequisites
                if ($course->federation_course_id) {
                    CoursePrerequisite::where('course_id', $course->federation_course_id)->delete();
                }
            }
        });

        return redirect()->route('admin.courses.index')
            ->with('success', 'دوره با موفقیت به‌روزرسانی شد.');
    }

    /**
     * Delete a course record.
     */
    public function destroy(Course $course)
    {
        $course->delete();
        return redirect()->route('admin.courses.index')
            ->with('success', 'دوره با موفقیت حذف شد.');
    }
}
