<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\ProgramReport;
use App\Models\ProgramFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Morilog\Jalali\Jalalian;

class UserProgramReportController extends Controller
{
    /**
     * Show the form for creating a new report
     */
    public function create(Program $program)
    {
        // Check if user has participated in this program
        $userRegistration = $program->registrations()
            ->where('user_id', Auth::id())
            ->where('status', 'approved')
            ->first();
        
        if (!$userRegistration) {
            return redirect()
                ->route('programs.index')
                ->with('error', 'شما در این برنامه شرکت نکرده‌اید.');
        }
        
        // Check if execution date has passed (including today)
        // execution_date is already a Carbon instance due to $casts in Program model
        if (!$program->execution_date || now()->startOfDay()->lt($program->execution_date->copy()->startOfDay())) {
            return redirect()
                ->route('programs.index')
                ->with('error', 'هنوز زمان نوشتن گزارش فرا نرسیده است.');
        }
        
        // Load program data for form
        $program->load('userRoles.user.profile', 'registrations.user.profile');
        
        // Get approved registrations (members)
        $approvedRegistrations = $program->registrations()
            ->where('status', 'approved')
            ->whereNotNull('user_id')
            ->with('user.profile')
            ->get();
        
        // Get guest registrations
        $guestRegistrations = $program->registrations()
            ->where('status', 'approved')
            ->whereNull('user_id')
            ->whereNotNull('guest_name')
            ->get();
        
        // Check if report already exists
        if ($program->report) {
            return redirect()
                ->route('program_reports.show', $program->report->id)
                ->with('info', 'گزارش این برنامه قبلاً نوشته شده است.');
        }
        
        // Get users for reporter/leader selection (for admin, but user can't change reporter)
        $users = \App\Models\User::with('profile')->get();
        $isAdmin = false;
        return view('program_reports.create', compact('program', 'users', 'isAdmin', 'approvedRegistrations', 'guestRegistrations'));
    }

    /**
     * Store a newly created report
     */
    public function store(Request $request, Program $program)
    {
        // Check if user has participated
        $userRegistration = $program->registrations()
            ->where('user_id', Auth::id())
            ->where('status', 'approved')
            ->first();
        
        if (!$userRegistration) {
            return back()->with('error', 'شما در این برنامه شرکت نکرده‌اید.');
        }
        
        // Validate
        $validated = $this->validateReport($request);
        
        DB::transaction(function () use ($validated, $program, $request) {
            // Convert dates
            $validated = $this->convertDates($validated);
            $validated = $this->applySanitizers($validated, $request);
            
            // Create report
            $report = ProgramReport::create($validated);
            
            // Handle file uploads
            if ($request->hasFile('report_images')) {
                foreach ($request->file('report_images') as $file) {
                    if ($file->isValid()) {
                        $path = $file->store('program_reports/images', 'public');
                        ProgramFile::create([
                            'program_id' => $program->id,
                            'file_type' => 'image',
                            'file_path' => $path,
                            'caption' => null,
                        ]);
                    }
                }
            }

            if ($request->hasFile('map_file') && $request->file('map_file')->isValid()) {
                $mapPath = $request->file('map_file')->store('program_reports/maps', 'public');
                ProgramFile::create([
                    'program_id' => $program->id,
                    'file_type' => 'map',
                    'file_path' => $mapPath,
                    'caption' => null,
                ]);
            }
        });
        
        return redirect()
            ->route('programs.index')
            ->with('success', 'گزارش شما با موفقیت ثبت شد و در انتظار بررسی است.');
    }
    
    /**
     * Validate report data
     */
    private function validateReport(Request $request)
    {
        return $request->validate([
            'report_description' => 'nullable|string',
            'important_notes' => 'nullable|string',
            'map_author' => 'nullable|string|max:255',
            'map_scale' => 'nullable|string|max:255',
            'map_source' => 'nullable|string|max:255',
            'technical_equipments' => 'nullable|array',
            'route_difficulty' => 'nullable|in:آسان,متوسط,سخت,بسیار سخت',
            'slope' => 'nullable|string|max:255',
            'rock_engagement' => 'nullable|in:کم,متوسط,زیاد',
            'ice_engagement' => 'nullable|in:ندارد,کم,زیاد',
            'avg_backpack_weight' => 'nullable|numeric|min:0|max:100',
            'prerequisites' => 'nullable|string',
            'vegetation' => 'nullable|string',
            'wildlife' => 'nullable|string',
            'weather' => 'nullable|string',
            'wind_speed' => 'nullable|integer|min:0',
            'temperature' => 'nullable|numeric',
            'local_language' => 'nullable|string|max:255',
            'attractions' => 'nullable|string',
            'food_supply' => 'nullable|in:دارد,ندارد,محدود',
            'start_altitude' => 'nullable|integer|min:0',
            'target_altitude' => 'nullable|integer|min:0',
            'start_location_name' => 'nullable|string|max:255',
            'distance_from_tehran' => 'nullable|integer|min:0',
            'road_type' => 'nullable|in:آسفالت,خاکی,ترکیبی',
            'transport_types' => 'nullable|array',
            'facilities' => 'nullable|array',
            'geo_points' => 'nullable|array',
            'timeline' => 'nullable|array',
            'participants' => 'nullable|array',
            'participants_count' => 'nullable|integer|min:0',
            'shelters' => 'nullable|array',
            'shelters.*.name' => 'nullable|string|max:255',
            'shelters.*.height' => 'nullable|integer|min:0',
            'map_file' => 'nullable|file|max:5120',
            'report_images' => 'nullable|array|max:20',
            'report_images.*' => 'image|mimes:jpeg,png,gif|max:2048',
        ], [
            'report_images.max' => 'حداکثر 20 تصویر مجاز است.',
            'report_images.*.image' => 'فایل باید تصویر باشد.',
            'report_images.*.mimes' => 'فرمت‌های مجاز: jpeg, png, gif.',
            'report_images.*.max' => 'حجم هر تصویر حداکثر 2 مگابایت است.',
        ]);
    }
    
    /**
     * Convert Jalali dates to Gregorian
     */
    private function convertDates(array $data)
    {
        // No date fields in program_reports table that need conversion
        // All dates are stored as execution_date from program
        return $data;
    }
    
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

    private function applySanitizers(array $validated, Request $request): array
    {
        $validated['geo_points'] = $this->sanitizeGeoPoints($request);
        $validated['timeline'] = $this->sanitizeTimeline($request);
        $validated['shelters'] = $this->sanitizeShelters($request);
        $validated['program_id'] = $request->route('program')->id;
        $validated['report_duration'] = $validated['report_duration'] ?? $this->computeDuration($request);

        return $validated;
    }

    private function sanitizeGeoPoints(Request $request): ?array
    {
        $points = collect($request->input('geo_points', []))
            ->map(function ($point) {
                return [
                    'name' => $point['name'] ?? null,
                    'lat' => $point['lat'] ?? null,
                    'lon' => $point['lon'] ?? null,
                ];
            })
            ->filter(function ($point) {
                return !empty($point['name']) || !empty($point['lat']) || !empty($point['lon']);
            })
            ->values()
            ->toArray();

        return empty($points) ? null : $points;
    }

    private function sanitizeTimeline(Request $request): ?array
    {
        $timeline = collect($request->input('timeline', []))
            ->map(function ($row) {
                return [
                    'title' => $row['title'] ?? null,
                    'datetime' => $row['datetime'] ?? null,
                ];
            })
            ->filter(function ($row) {
                return !empty($row['title']) || !empty($row['datetime']);
            })
            ->values()
            ->toArray();

        return empty($timeline) ? null : $timeline;
    }

    private function sanitizeShelters(Request $request): ?array
    {
        $shelters = collect($request->input('shelters', []))
            ->map(function ($row) {
                return [
                    'name' => $row['name'] ?? null,
                    'height' => $row['height'] ?? null,
                ];
            })
            ->filter(function ($row) {
                return !empty($row['name']) || !empty($row['height']);
            })
            ->values()
            ->toArray();

        return empty($shelters) ? null : $shelters;
    }

    private function computeDuration(Request $request): ?string
    {
        $start = $request->input('report_start_date');
        $end = $request->input('report_end_date');
        if (!$start || !$end) {
            return null;
        }

        try {
            $startDate = Jalalian::fromFormat('Y/m/d', $this->toEnglishDigits($start))->toCarbon();
            $endDate = Jalalian::fromFormat('Y/m/d', $this->toEnglishDigits($end))->toCarbon();
        } catch (\Exception $e) {
            return null;
        }

        if ($endDate->lt($startDate)) {
            return null;
        }

        $days = $startDate->diffInDays($endDate) + 1;
        return $days . ' روز';
    }
}

