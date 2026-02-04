<?php

/**
 * Program administration and public program presentation endpoints.
 */

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Program;
use App\Models\ProgramUserRole;
use App\Models\ProgramFile;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Morilog\Jalali\Jalalian;

/**
 * Manages program CRUD, registration context, and date normalization.
 */
class ProgramController extends Controller
{
    /**
     * Convert Persian/Arabic digits to English digits
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
     * Handles both Jalali (from datepicker) and Gregorian (already in DB) formats
     */
    private function convertJalaliToGregorian(?string $dateInput, bool $withTime = false): ?string
    {
        if (!$dateInput || trim($dateInput) === '') {
            return null;
        }

        // Convert Persian/Arabic digits to English
        $dateInput = $this->toEnglishDigits(trim($dateInput));

        // First, try to detect if it's already in Gregorian format (YYYY-MM-DD or YYYY/MM/DD)
        // Gregorian years are typically 1900-2100, Jalali years are 1300-1500
        if (preg_match('/^(\d{4})[-\/](\d{2})[-\/](\d{2})/', $dateInput, $matches)) {
            $year = (int)$matches[1];
            // If year is in Gregorian range, it's likely already Gregorian
            if ($year >= 1900 && $year <= 2100) {
                try {
                    // Already Gregorian, just format it properly
                    $carbon = \Carbon\Carbon::parse($dateInput);
                    return $withTime 
                        ? $carbon->format('Y-m-d H:i:s')
                        : $carbon->format('Y-m-d H:i:s');
                } catch (\Exception $e) {
                    // Continue to try Jalali parsing
                }
            }
        }

        // Try parsing as Jalali (this is what datepicker sends)
        try {
            if ($withTime) {
                // Format: Y/m/d H:i or Y/m/d H:i:s
                if (preg_match('/^\d{4}\/\d{2}\/\d{2}\s+\d{2}:\d{2}(:\d{2})?$/', $dateInput)) {
                    return Jalalian::fromFormat('Y/m/d H:i', substr($dateInput, 0, 16))->toCarbon()->format('Y-m-d H:i:s');
                }
                // Try without seconds
                if (preg_match('/^\d{4}\/\d{2}\/\d{2}\s+\d{2}:\d{2}$/', $dateInput)) {
                    return Jalalian::fromFormat('Y/m/d H:i', $dateInput)->toCarbon()->format('Y-m-d H:i:s');
                }
            } else {
                // Format: Y/m/d
                if (preg_match('/^\d{4}\/\d{2}\/\d{2}$/', $dateInput)) {
                    return Jalalian::fromFormat('Y/m/d', $dateInput)->toCarbon()->format('Y-m-d H:i:s');
                }
            }
        } catch (\Exception $e) {
            // Jalali parsing failed, try Gregorian as fallback
        }

        // Final fallback: try to parse as Gregorian
        try {
            $carbon = \Carbon\Carbon::parse($dateInput);
            return $carbon->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            // If all parsing fails, return null
            return null;
        }
    }
    /**
     * Show the public archive of programs.
     */
    public function archive()
    {
        $programs = Program::with('files')
            ->orderByDesc('execution_date')
            ->orderByDesc('created_at')
            ->paginate(12);
        return view('programs.archive', compact('programs'));
    }

    /**
     * List programs for the admin index.
     */
    public function index()
    {
        $programs = Program::latest()->get();
        return view('programs.index', compact('programs'));
    }

    /**
     * Show the admin program creation form.
     */
    public function create()
    {
        $users = User::with('profile')->get();
        return view('programs.create' , compact('users'));
    }

    /**
     * Display a program details page by id.
     */
    public function show($id)
    {
        $program = Program::with([
            'report', 
            'userRoles.user.profile',
            'files' => function($query) {
                $query->where('file_type', 'image');
            }
        ])->findOrFail($id);
        $user = auth()->user();

        // Check if user has registered
        $userRegistration = null;
        $userHasParticipated = false;
        if (Auth::check()) {
            $userRegistration = $program->registrations()
                ->where('user_id', Auth::id())
                ->with('payment')
                ->first();
            $userHasParticipated = $userRegistration !== null;
        }

        // Check for guest registration (by phone if not logged in)
        $guestRegistration = null;
        if (!Auth::check() && request()->has('guest_phone')) {
            $guestRegistration = $program->registrations()
                ->where('guest_phone', request('guest_phone'))
                ->with('payment')
                ->first();
        }

        // Check if user has submitted survey (if surveys relationship exists)
        $userHasSubmittedSurvey = false;
        // Note: Survey relationship may not exist, so we check safely

        return view('programs.show', compact(
            'program',
            'userHasParticipated',
            'userHasSubmittedSurvey',
            'userRegistration',
            'guestRegistration'
        ));
    }


    /**
     * Delete a program record.
     */
    public function destroy(Program $program)
    {
        $program->delete();

        return redirect()->route('admin.programs.index')->with('success', 'برنامه با موفقیت حذف شد.');
    }
    /**
     * Show the admin program edit form.
     */
    public function edit(Program $program)
    {
        $users = User::with('profile')->get();
        $program->load('userRoles', 'files');
        return view('programs.edit', compact('program', 'users'));
    }

    /**
     * Store a new program and related files.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'program_type' => 'required|string|max:255',
            'peak_height' => 'nullable|integer|min:0',
            'region_name' => 'nullable|string|max:255',
            'execution_date' => 'required|string',
            'has_transport' => 'nullable|in:0,1',
            'departure_datetime_tehran' => 'nullable|string',
            'departure_place_tehran' => 'nullable|string|max:255',
            'departure_lat_tehran' => 'nullable|numeric',
            'departure_lon_tehran' => 'nullable|numeric',
            'departure_datetime_karaj' => 'nullable|string',
            'departure_place_karaj' => 'nullable|string|max:255',
            'departure_lat_karaj' => 'nullable|numeric',
            'departure_lon_karaj' => 'nullable|numeric',
            'is_free' => 'nullable|in:0,1',
            'cost_member' => 'nullable|required_if:is_free,0|integer|min:0',
            'cost_guest' => 'nullable|required_if:is_free,0|integer|min:0',
            'card_number' => 'nullable|required_if:is_free,0|string|max:255',
            'sheba_number' => 'nullable|string|max:255',
            'card_holder' => 'nullable|required_if:is_free,0|string|max:255',
            'bank_name' => 'nullable|required_if:is_free,0|string|max:255',
            'equipments' => 'nullable|array',
            'meals' => 'nullable|array',
            'conditions' => 'nullable|array',
            'register_deadline' => 'nullable|string',
            'rules' => 'nullable|string',
            'status' => 'required|in:draft,open,closed,done',
            'roles' => 'nullable|array',
            'roles.*.role_title' => 'required|string|max:255',
            'roles.*.user_id' => 'nullable|exists:users,id',
            'roles.*.user_name' => 'nullable|string|max:255',
            'report_photos' => 'nullable',
            'report_photos.*' => 'image|mimes:jpeg,jpg,png,gif|max:2048',
        ], [
            'name.required' => 'لطفاً نام برنامه را وارد کنید.',
            'name.max' => 'نام برنامه نمی‌تواند بیشتر از 255 کاراکتر باشد.',
            'program_type.required' => 'لطفاً نوع برنامه را انتخاب کنید.',
            'program_type.string' => 'نوع برنامه باید متن باشد.',
            'program_type.max' => 'نوع برنامه نمی‌تواند بیشتر از 255 کاراکتر باشد.',
            'peak_height.integer' => 'ارتفاع قله باید یک عدد صحیح باشد.',
            'peak_height.min' => 'ارتفاع قله نمی‌تواند منفی باشد.',
            'region_name.max' => 'نام منطقه نمی‌تواند بیشتر از 255 کاراکتر باشد.',
            'execution_date.required' => 'لطفاً تاریخ اجرای برنامه را وارد کنید.',
            'has_transport.in' => 'مقدار انتخاب شده برای حمل و نقل معتبر نیست.',
            'departure_place_tehran.max' => 'محل قرار تهران نمی‌تواند بیشتر از 255 کاراکتر باشد.',
            'departure_lat_tehran.numeric' => 'عرض جغرافیایی تهران باید یک عدد باشد.',
            'departure_lon_tehran.numeric' => 'طول جغرافیایی تهران باید یک عدد باشد.',
            'departure_place_karaj.max' => 'محل قرار کرج نمی‌تواند بیشتر از 255 کاراکتر باشد.',
            'departure_lat_karaj.numeric' => 'عرض جغرافیایی کرج باید یک عدد باشد.',
            'departure_lon_karaj.numeric' => 'طول جغرافیایی کرج باید یک عدد باشد.',
            'is_free.in' => 'مقدار انتخاب شده برای رایگان بودن برنامه معتبر نیست.',
            'cost_member.required_if' => 'هزینه برای اعضا الزامی است زمانی که برنامه رایگان نیست.',
            'cost_member.integer' => 'هزینه برای اعضا باید یک عدد صحیح باشد.',
            'cost_member.min' => 'هزینه برای اعضا نمی‌تواند منفی باشد.',
            'cost_guest.required_if' => 'هزینه برای مهمانان الزامی است زمانی که برنامه رایگان نیست.',
            'cost_guest.integer' => 'هزینه برای مهمانان باید یک عدد صحیح باشد.',
            'cost_guest.min' => 'هزینه برای مهمانان نمی‌تواند منفی باشد.',
            'card_number.required_if' => 'شماره کارت الزامی است زمانی که برنامه رایگان نیست.',
            'card_number.max' => 'شماره کارت نمی‌تواند بیشتر از 255 کاراکتر باشد.',
            'sheba_number.max' => 'شماره شبا نمی‌تواند بیشتر از 255 کاراکتر باشد.',
            'card_holder.required_if' => 'نام دارنده حساب الزامی است زمانی که برنامه رایگان نیست.',
            'card_holder.max' => 'نام دارنده حساب نمی‌تواند بیشتر از 255 کاراکتر باشد.',
            'bank_name.required_if' => 'نام بانک الزامی است زمانی که برنامه رایگان نیست.',
            'bank_name.max' => 'نام بانک نمی‌تواند بیشتر از 255 کاراکتر باشد.',
            'equipments.array' => 'تجهیزات باید به صورت لیست باشد.',
            'meals.array' => 'وعده‌ها باید به صورت لیست باشد.',
            'conditions.array' => 'شرایط باید به صورت لیست باشد.',
            'status.required' => 'لطفاً وضعیت برنامه را انتخاب کنید.',
            'status.in' => 'وضعیت انتخاب شده معتبر نیست.',
            'roles.array' => 'مسئولین باید به صورت لیست باشد.',
            'roles.*.role_title.required' => 'لطفاً سمت مسئول را وارد کنید.',
            'roles.*.role_title.max' => 'سمت مسئول نمی‌تواند بیشتر از 255 کاراکتر باشد.',
            'roles.*.user_id.exists' => 'کاربر انتخاب شده معتبر نیست.',
            'roles.*.user_name.max' => 'نام فرد نمی‌تواند بیشتر از 255 کاراکتر باشد.',
            'report_photos.*.image' => 'فایل بارگذاری شده باید تصویر باشد.',
            'report_photos.*.mimes' => 'فرمت‌های مجاز: JPG, JPEG, PNG, GIF.',
            'report_photos.*.max' => 'حجم هر تصویر باید کمتر از 2 مگابایت باشد.',
        ]);

        // Enforce max image count (defensive)
        $newFiles = $request->file('report_photos');
        $newFiles = is_array($newFiles) ? $newFiles : ($newFiles ? [$newFiles] : []);
        if (count($newFiles) > 10) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'report_photos' => 'حداکثر 10 تصویر مجاز است.'
            ]);
        }

        DB::transaction(function () use ($validated, $request, $newFiles) {
            // Prepare transport info as JSON for move_from fields
            $moveFromTehran = null;
            $moveFromKaraj = null;
            
            if ($request->input('has_transport') == '1') {
                if ($request->filled('departure_place_tehran') || $request->filled('departure_datetime_tehran')) {
                    // Convert Jalali datetime to Gregorian for storage (keep as string in JSON)
                    $tehranDatetime = $request->input('departure_datetime_tehran');
                    if ($tehranDatetime) {
                        $tehranDatetime = $this->convertJalaliToGregorian($tehranDatetime, true);
                    }
                    $moveFromTehran = json_encode([
                        'datetime' => $tehranDatetime,
                        'place' => $request->input('departure_place_tehran'),
                        'lat' => $request->input('departure_lat_tehran'),
                        'lon' => $request->input('departure_lon_tehran'),
                    ]);
                }
                if ($request->filled('departure_place_karaj') || $request->filled('departure_datetime_karaj')) {
                    // Convert Jalali datetime to Gregorian for storage (keep as string in JSON)
                    $karajDatetime = $request->input('departure_datetime_karaj');
                    if ($karajDatetime) {
                        $karajDatetime = $this->convertJalaliToGregorian($karajDatetime, true);
                    }
                    $moveFromKaraj = json_encode([
                        'datetime' => $karajDatetime,
                        'place' => $request->input('departure_place_karaj'),
                        'lat' => $request->input('departure_lat_karaj'),
                        'lon' => $request->input('departure_lon_karaj'),
                    ]);
                }
            }

            // Prepare payment info
            $paymentInfo = null;
            if ($request->input('is_free') == '0') {
                $paymentInfo = [
                    'card_number' => $request->input('card_number'),
                    'sheba_number' => $request->input('sheba_number'),
                    'card_holder' => $request->input('card_holder'),
                    'bank_name' => $request->input('bank_name'),
                ];
            }

            // Convert Jalali dates to Gregorian
            $executionDate = $this->convertJalaliToGregorian($validated['execution_date'], false);
            $registerDeadline = $validated['register_deadline'] 
                ? $this->convertJalaliToGregorian($validated['register_deadline'], true) 
                : null;

            $program = Program::create([
                'name' => $validated['name'],
                'program_type' => $validated['program_type'],
                'peak_height' => $validated['peak_height'] ?? null,
                'region_name' => $validated['region_name'] ?? null,
                'execution_date' => $executionDate,
                'move_from_karaj' => $moveFromKaraj,
                'move_from_tehran' => $moveFromTehran,
                'cost_member' => $validated['cost_member'] ?? null,
                'cost_guest' => $validated['cost_guest'] ?? null,
                'payment_info' => $paymentInfo,
                'equipments' => $validated['equipments'] ?? null,
                'meals' => $validated['meals'] ?? null,
                'conditions' => $validated['conditions'] ?? null,
                'register_deadline' => $registerDeadline,
                'rules' => $validated['rules'] ?? null,
                'status' => $validated['status'],
            ]);

            // Handle file uploads
            foreach ($newFiles as $file) {
                if ($file->isValid()) {
                    $path = $file->store('programs/images', 'public');
                    ProgramFile::create([
                        'program_id' => $program->id,
                        'file_type' => 'image',
                        'file_path' => $path,
                        'caption' => null,
                    ]);
                }
            }

            // Persist role assignments if provided.
            if ($request->filled('roles')) {
                foreach ($request->input('roles') as $role) {
                    if (empty($role['user_id']) && empty($role['user_name'])) {
                        continue;
                    }

                    ProgramUserRole::create([
                        'program_id' => $program->id,
                        'user_id' => $role['user_id'] ?? null,
                        'user_name' => $role['user_name'] ?? null,
                        'role_title' => $role['role_title'],
                    ]);
                }
            }
        });

        return redirect()->route('admin.programs.index')->with('success', 'برنامه با موفقیت ثبت شد.');
    }

    /**
     * Update a program and related files.
     */
    public function update(Request $request, Program $program)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'program_type' => 'required|string|max:255',
            'peak_height' => 'nullable|integer|min:0',
            'region_name' => 'nullable|string|max:255',
            'execution_date' => 'required|string',
            'has_transport' => 'nullable|in:0,1',
            'departure_datetime_tehran' => 'nullable|string',
            'departure_place_tehran' => 'nullable|string|max:255',
            'departure_lat_tehran' => 'nullable|numeric',
            'departure_lon_tehran' => 'nullable|numeric',
            'departure_datetime_karaj' => 'nullable|string',
            'departure_place_karaj' => 'nullable|string|max:255',
            'departure_lat_karaj' => 'nullable|numeric',
            'departure_lon_karaj' => 'nullable|numeric',
            'is_free' => 'nullable|in:0,1',
            'cost_member' => 'nullable|required_if:is_free,0|integer|min:0',
            'cost_guest' => 'nullable|required_if:is_free,0|integer|min:0',
            'card_number' => 'nullable|required_if:is_free,0|string|max:255',
            'sheba_number' => 'nullable|string|max:255',
            'card_holder' => 'nullable|required_if:is_free,0|string|max:255',
            'bank_name' => 'nullable|required_if:is_free,0|string|max:255',
            'equipments' => 'nullable|array',
            'meals' => 'nullable|array',
            'conditions' => 'nullable|array',
            'register_deadline' => 'nullable|string',
            'rules' => 'nullable|string',
            'status' => 'required|in:draft,open,closed,done',
            'roles' => 'nullable|array',
            'roles.*.role_title' => 'required|string|max:255',
            'roles.*.user_id' => 'nullable|exists:users,id',
            'roles.*.user_name' => 'nullable|string|max:255',
            'report_photos' => 'nullable',
            'report_photos.*' => 'image|mimes:jpeg,jpg,png,gif|max:2048',
            'removed_file_ids' => 'nullable|array',
            'removed_file_ids.*' => 'integer|exists:program_files,id',
        ], [
            'name.required' => 'لطفاً نام برنامه را وارد کنید.',
            'name.max' => 'نام برنامه نمی‌تواند بیشتر از 255 کاراکتر باشد.',
            'program_type.required' => 'لطفاً نوع برنامه را انتخاب کنید.',
            'program_type.string' => 'نوع برنامه باید متن باشد.',
            'program_type.max' => 'نوع برنامه نمی‌تواند بیشتر از 255 کاراکتر باشد.',
            'peak_height.integer' => 'ارتفاع قله باید یک عدد صحیح باشد.',
            'peak_height.min' => 'ارتفاع قله نمی‌تواند منفی باشد.',
            'region_name.max' => 'نام منطقه نمی‌تواند بیشتر از 255 کاراکتر باشد.',
            'execution_date.required' => 'لطفاً تاریخ اجرای برنامه را وارد کنید.',
            'has_transport.in' => 'مقدار انتخاب شده برای حمل و نقل معتبر نیست.',
            'departure_place_tehran.max' => 'محل قرار تهران نمی‌تواند بیشتر از 255 کاراکتر باشد.',
            'departure_lat_tehran.numeric' => 'عرض جغرافیایی تهران باید یک عدد باشد.',
            'departure_lon_tehran.numeric' => 'طول جغرافیایی تهران باید یک عدد باشد.',
            'departure_place_karaj.max' => 'محل قرار کرج نمی‌تواند بیشتر از 255 کاراکتر باشد.',
            'departure_lat_karaj.numeric' => 'عرض جغرافیایی کرج باید یک عدد باشد.',
            'departure_lon_karaj.numeric' => 'طول جغرافیایی کرج باید یک عدد باشد.',
            'is_free.in' => 'مقدار انتخاب شده برای رایگان بودن برنامه معتبر نیست.',
            'cost_member.required_if' => 'هزینه برای اعضا الزامی است زمانی که برنامه رایگان نیست.',
            'cost_member.integer' => 'هزینه برای اعضا باید یک عدد صحیح باشد.',
            'cost_member.min' => 'هزینه برای اعضا نمی‌تواند منفی باشد.',
            'cost_guest.required_if' => 'هزینه برای مهمانان الزامی است زمانی که برنامه رایگان نیست.',
            'cost_guest.integer' => 'هزینه برای مهمانان باید یک عدد صحیح باشد.',
            'cost_guest.min' => 'هزینه برای مهمانان نمی‌تواند منفی باشد.',
            'card_number.required_if' => 'شماره کارت الزامی است زمانی که برنامه رایگان نیست.',
            'card_number.max' => 'شماره کارت نمی‌تواند بیشتر از 255 کاراکتر باشد.',
            'sheba_number.max' => 'شماره شبا نمی‌تواند بیشتر از 255 کاراکتر باشد.',
            'card_holder.required_if' => 'نام دارنده حساب الزامی است زمانی که برنامه رایگان نیست.',
            'card_holder.max' => 'نام دارنده حساب نمی‌تواند بیشتر از 255 کاراکتر باشد.',
            'bank_name.required_if' => 'نام بانک الزامی است زمانی که برنامه رایگان نیست.',
            'bank_name.max' => 'نام بانک نمی‌تواند بیشتر از 255 کاراکتر باشد.',
            'equipments.array' => 'تجهیزات باید به صورت لیست باشد.',
            'meals.array' => 'وعده‌ها باید به صورت لیست باشد.',
            'conditions.array' => 'شرایط باید به صورت لیست باشد.',
            'status.required' => 'لطفاً وضعیت برنامه را انتخاب کنید.',
            'status.in' => 'وضعیت انتخاب شده معتبر نیست.',
            'roles.array' => 'مسئولین باید به صورت لیست باشد.',
            'roles.*.role_title.required' => 'لطفاً سمت مسئول را وارد کنید.',
            'roles.*.role_title.max' => 'سمت مسئول نمی‌تواند بیشتر از 255 کاراکتر باشد.',
            'roles.*.user_id.exists' => 'کاربر انتخاب شده معتبر نیست.',
            'roles.*.user_name.max' => 'نام فرد نمی‌تواند بیشتر از 255 کاراکتر باشد.',
            'report_photos.*.image' => 'فایل بارگذاری شده باید تصویر باشد.',
            'report_photos.*.mimes' => 'فرمت‌های مجاز: JPG, JPEG, PNG, GIF.',
            'report_photos.*.max' => 'حجم هر تصویر باید کمتر از 2 مگابایت باشد.',
            'removed_file_ids.*.exists' => 'تصویر انتخاب‌شده برای حذف یافت نشد.',
        ]);

        $removedFileIds = collect($request->input('removed_file_ids', []))
            ->merge($request->input('deleted_files', []))
            ->filter()
            ->map(fn($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        $existingFilesCount = $program->files()->count();
        $newFiles = $request->file('report_photos');
        $newFiles = is_array($newFiles) ? $newFiles : ($newFiles ? [$newFiles] : []);
        $newFilesCount = count($newFiles);
        $remainingAfterRemoval = max(0, $existingFilesCount - count($removedFileIds));

        if (($remainingAfterRemoval + $newFilesCount) > 10) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'report_photos' => 'پس از حذف و افزودن، تعداد تصاویر نباید بیش از 10 باشد.'
            ]);
        }

        DB::transaction(function () use ($program, $validated, $request, $removedFileIds, $newFiles) {
            // Prepare transport info as JSON for move_from fields
            $moveFromTehran = null;
            $moveFromKaraj = null;
            
            if ($request->input('has_transport') == '1') {
                if ($request->filled('departure_place_tehran') || $request->filled('departure_datetime_tehran')) {
                    // Convert Jalali datetime to Gregorian for storage (keep as string in JSON)
                    $tehranDatetime = $request->input('departure_datetime_tehran');
                    if ($tehranDatetime) {
                        $tehranDatetime = $this->convertJalaliToGregorian($tehranDatetime, true);
                    }
                    $moveFromTehran = json_encode([
                        'datetime' => $tehranDatetime,
                        'place' => $request->input('departure_place_tehran'),
                        'lat' => $request->input('departure_lat_tehran'),
                        'lon' => $request->input('departure_lon_tehran'),
                    ]);
                }
                if ($request->filled('departure_place_karaj') || $request->filled('departure_datetime_karaj')) {
                    // Convert Jalali datetime to Gregorian for storage (keep as string in JSON)
                    $karajDatetime = $request->input('departure_datetime_karaj');
                    if ($karajDatetime) {
                        $karajDatetime = $this->convertJalaliToGregorian($karajDatetime, true);
                    }
                    $moveFromKaraj = json_encode([
                        'datetime' => $karajDatetime,
                        'place' => $request->input('departure_place_karaj'),
                        'lat' => $request->input('departure_lat_karaj'),
                        'lon' => $request->input('departure_lon_karaj'),
                    ]);
                }
            }

            // Prepare payment info
            $paymentInfo = null;
            if ($request->input('is_free') == '0') {
                $paymentInfo = [
                    'card_number' => $request->input('card_number'),
                    'sheba_number' => $request->input('sheba_number'),
                    'card_holder' => $request->input('card_holder'),
                    'bank_name' => $request->input('bank_name'),
                ];
            }

            // Convert Jalali dates to Gregorian
            // Only convert if the date has changed (to avoid re-parsing already converted dates)
            $executionDate = $this->convertJalaliToGregorian($validated['execution_date'], false);
            $registerDeadline = $validated['register_deadline'] 
                ? $this->convertJalaliToGregorian($validated['register_deadline'], true) 
                : null;

            $program->update([
                'name' => $validated['name'],
                'program_type' => $validated['program_type'],
                'peak_height' => $validated['peak_height'] ?? null,
                'region_name' => $validated['region_name'] ?? null,
                'execution_date' => $executionDate,
                'move_from_karaj' => $moveFromKaraj,
                'move_from_tehran' => $moveFromTehran,
                'cost_member' => $validated['cost_member'] ?? null,
                'cost_guest' => $validated['cost_guest'] ?? null,
                'payment_info' => $paymentInfo,
                'equipments' => $validated['equipments'] ?? null,
                'meals' => $validated['meals'] ?? null,
                'conditions' => $validated['conditions'] ?? null,
                'register_deadline' => $registerDeadline,
                'rules' => $validated['rules'] ?? null,
                'status' => $validated['status'],
            ]);

            // Handle file deletions first
            if (!empty($removedFileIds)) {
                $filesToDelete = ProgramFile::whereIn('id', $removedFileIds)
                    ->where('program_id', $program->id)
                    ->get();

                foreach ($filesToDelete as $file) {
                    if (Storage::disk('public')->exists($file->file_path)) {
                        Storage::disk('public')->delete($file->file_path);
                    }
                    $file->delete();
                }
            }

            // Handle file uploads
            foreach ($newFiles as $file) {
                if ($file->isValid()) {
                    $path = $file->store('programs/images', 'public');
                    ProgramFile::create([
                        'program_id' => $program->id,
                        'file_type' => 'image',
                        'file_path' => $path,
                        'caption' => null,
                    ]);
                }
            }

            // Replace existing role assignments.
            $program->userRoles()->delete();

            // Create updated role assignments.
            if ($request->filled('roles')) {
                foreach ($request->input('roles') as $role) {
                    if (empty($role['user_id']) && empty($role['user_name'])) continue;

                    $program->userRoles()->create([
                        'user_id' => $role['user_id'] ?? null,
                        'user_name' => $role['user_name'] ?? null,
                        'role_title' => $role['role_title'],
                    ]);
                }
            }
        });

        return redirect()->route('admin.programs.index')->with('success', 'برنامه با موفقیت ویرایش شد.');
    }


}
