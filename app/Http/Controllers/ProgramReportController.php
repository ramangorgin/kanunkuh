<?php

/**
 * Admin program report management and public report archive.
 */

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProgramReport;
use App\Models\Program;
use Illuminate\Support\Facades\DB;
use Morilog\Jalali\Jalalian;

/**
 * Provides CRUD actions and PDF export for program reports.
 */
class ProgramReportController extends Controller
{
    /**
     * List program reports for the admin view.
     */
    public function index()
    {
        $reports = ProgramReport::with('program.files')->latest()->get();
        return view('program_reports.index', compact('reports'));
    }

    /**
     * Display the public archive of program reports.
     */
    public function publicArchive()
    {
        $reports = ProgramReport::with(['program.files', 'program.userRoles'])
            ->orderByDesc('report_date')
            ->orderByDesc('created_at')
            ->paginate(12);

        return view('program_reports.archive', compact('reports'));
    }

    /**
     * Show the report creation form for eligible programs.
     */
    public function create()
    {
        $programs = Program::whereNotNull('execution_date')
            ->whereRaw('DATE(execution_date) <= DATE(NOW())')
            ->whereDoesntHave('report')
            ->orderBy('execution_date', 'desc')
            ->get();
        
        $users = \App\Models\User::with('profile')->get();
        
        $isAdmin = true;
        $program = null;
        $approvedRegistrations = collect();
        $guestRegistrations = collect();
        
        if (old('program_id')) {
            $program = Program::with('userRoles.user.profile', 'registrations.user.profile')->find(old('program_id'));
            if ($program) {
                $approvedRegistrations = $program->registrations()
                    ->where('status', 'approved')
                    ->whereNotNull('user_id')
                    ->with('user.profile')
                    ->get();
                $guestRegistrations = $program->registrations()
                    ->where('status', 'approved')
                    ->whereNull('user_id')
                    ->whereNotNull('guest_name')
                    ->get();
            }
        }
        
        return view('program_reports.create', compact('programs', 'users', 'isAdmin', 'program', 'approvedRegistrations', 'guestRegistrations'));
    }

    /**
     * Store a newly created program report and attached files.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'program_id' => 'required|exists:programs,id',
            'report_date' => 'nullable|string|max:50',
            'report_program_type' => 'nullable|string|max:255',
            'report_program_name' => 'nullable|string|max:255',
            'report_region_route' => 'nullable|string|max:255',
            'report_start_date' => 'nullable|string|max:50',
            'report_end_date' => 'nullable|string|max:50',
            'report_duration' => 'nullable|string|max:50',
            'reporter_id' => 'nullable|exists:users,id',
            'reporter_name' => 'nullable|string|max:255',
            'report_description' => 'nullable|string',
            'important_notes' => 'nullable|string',
            'map_author' => 'nullable|string|max:255',
            'map_scale' => 'nullable|string|max:255',
            'map_source' => 'nullable|string|max:255',
            'technical_feature' => 'nullable|string|max:255',
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
            'local_village_name' => 'nullable|string|max:255',
            'local_guide_info' => 'nullable|string',
            'shelters_info' => 'nullable|string',
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
            'deleted_files' => 'nullable|array',
            'deleted_files.*' => 'exists:program_files,id',
        ], [
            'report_images.max' => 'حداکثر 20 تصویر مجاز است.',
            'report_images.*.image' => 'فایل باید تصویر باشد.',
            'report_images.*.mimes' => 'فرمت‌های مجاز: jpeg, png, gif.',
            'report_images.*.max' => 'حجم هر تصویر حداکثر 2 مگابایت است.',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $program = Program::findOrFail($validated['program_id']);
            
            if ($program->report) {
                throw new \Exception('گزارش این برنامه قبلاً ثبت شده است.');
            }
            
            $payload = $this->applySanitizers($validated, $request);
            $report = ProgramReport::create($payload);
            
            if ($request->hasFile('report_images')) {
                foreach ($request->file('report_images') as $file) {
                    if ($file->isValid()) {
                        $path = $file->store('program_reports/images', 'public');
                        \App\Models\ProgramFile::create([
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
                \App\Models\ProgramFile::create([
                    'program_id' => $program->id,
                    'file_type' => 'map',
                    'file_path' => $mapPath,
                    'caption' => null,
                ]);
            }
        });

        return redirect()->route('admin.program_reports.index')->with('success', 'گزارش برنامه با موفقیت ثبت شد.');
    }

    /**
     * Display a single program report.
     */
    public function show(ProgramReport $programReport)
    {
        $programReport->load([
            'program.files',
            'program.userRoles.user.profile',
            'reporter.profile'
        ]);
        return view('program_reports.show', compact('programReport'));
    }

    /**
     * Download the report as a PDF file.
     */
    public function downloadPdf(ProgramReport $programReport)
    {
        $programReport->load([
            'program.files',
            'program.userRoles.user.profile',
            'reporter.profile'
        ]);
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('program_reports.pdf', compact('programReport'));
        $filename = 'report_' . $programReport->id . '_' . date('Y-m-d') . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Show the report edit form with related program data.
     */
    public function edit(ProgramReport $programReport)
    {
        $programReport->load([
            'program.files',
            'program.userRoles.user.profile',
            'program.registrations.user.profile',
            'reporter.profile'
        ]);
        
        $program = $programReport->program;
        
        $users = \App\Models\User::with('profile')->get();
        
        $programs = Program::whereNotNull('execution_date')
            ->where('execution_date', '<=', now())
            ->orderBy('execution_date', 'desc')
            ->get();
        
        $approvedRegistrations = $program->registrations()
            ->where('status', 'approved')
            ->whereNotNull('user_id')
            ->with('user.profile')
            ->get();
        
        $guestRegistrations = $program->registrations()
            ->where('status', 'approved')
            ->whereNull('user_id')
            ->whereNotNull('guest_name')
            ->get();
        
        $isAdmin = true;
        
        return view('program_reports.edit', compact(
            'programReport', 
            'programs', 
            'users', 
            'isAdmin', 
            'approvedRegistrations', 
            'guestRegistrations'
        ));
    }

    /**
     * Update a program report and manage file changes.
     */
    public function update(Request $request, ProgramReport $programReport)
    {
        $validated = $request->validate([
            'program_id' => 'required|exists:programs,id',
            'report_date' => 'nullable|string|max:50',
            'report_program_type' => 'nullable|string|max:255',
            'report_program_name' => 'nullable|string|max:255',
            'report_region_route' => 'nullable|string|max:255',
            'report_start_date' => 'nullable|string|max:50',
            'report_end_date' => 'nullable|string|max:50',
            'report_duration' => 'nullable|string|max:50',
            'reporter_id' => 'nullable|exists:users,id',
            'reporter_name' => 'nullable|string|max:255',
            'report_description' => 'nullable|string',
            'important_notes' => 'nullable|string',
            'map_author' => 'nullable|string|max:255',
            'map_scale' => 'nullable|string|max:255',
            'map_source' => 'nullable|string|max:255',
            'technical_feature' => 'nullable|string|max:255',
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
            'local_village_name' => 'nullable|string|max:255',
            'local_guide_info' => 'nullable|string',
            'shelters_info' => 'nullable|string',
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
            'deleted_files' => 'nullable|array',
            'deleted_files.*' => 'exists:program_files,id',
        ], [
            'report_images.max' => 'حداکثر 20 تصویر مجاز است.',
            'report_images.*.image' => 'فایل باید تصویر باشد.',
            'report_images.*.mimes' => 'فرمت‌های مجاز: jpeg, png, gif.',
            'report_images.*.max' => 'حجم هر تصویر حداکثر 2 مگابایت است.',
        ]);

        DB::transaction(function () use ($programReport, $validated, $request) {
            $payload = $this->applySanitizers($validated, $request);
            $programReport->update($payload);
            
            $program = $programReport->program;
            
            if ($request->hasFile('report_images')) {
                foreach ($request->file('report_images') as $file) {
                    if ($file->isValid()) {
                        $path = $file->store('program_reports/images', 'public');
                        \App\Models\ProgramFile::create([
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
                \App\Models\ProgramFile::create([
                    'program_id' => $program->id,
                    'file_type' => 'map',
                    'file_path' => $mapPath,
                    'caption' => null,
                ]);
            }
            
            if ($request->filled('deleted_files')) {
                $deletedFileIds = is_array($request->deleted_files) 
                    ? $request->deleted_files 
                    : explode(',', $request->deleted_files);
                
                foreach ($deletedFileIds as $fileId) {
                    $file = \App\Models\ProgramFile::find($fileId);
                    if ($file && $file->program_id == $program->id) {
                        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($file->file_path)) {
                            \Illuminate\Support\Facades\Storage::disk('public')->delete($file->file_path);
                        }
                        $file->delete();
                    }
                }
            }
        });

        return redirect()->route('admin.program_reports.index')->with('success', 'گزارش برنامه با موفقیت ویرایش شد.');
    }

    /**
     * Delete a program report.
     */
    public function destroy(ProgramReport $programReport)
    {
        $programReport->delete();
        return redirect()->route('admin.program_reports.index')->with('success', 'گزارش برنامه با موفقیت حذف شد.');
    }

    /**
     * Apply sanitizers and derived values to the report payload.
     */
    private function applySanitizers(array $validated, Request $request): array
    {
        $validated['report_date'] = $this->convertJalaliDate($request->input('report_date'));
        $validated['report_start_date'] = $this->convertJalaliDate($request->input('report_start_date'));
        $validated['report_end_date'] = $this->convertJalaliDate($request->input('report_end_date'));
        $validated['geo_points'] = $this->sanitizeGeoPoints($request);
        $validated['timeline'] = $this->sanitizeTimeline($request);
        $validated['shelters'] = $this->sanitizeShelters($request);
        $validated['report_duration'] = $validated['report_duration'] ?? $this->computeDuration($request);

        return $validated;
    }

    /**
     * Normalize geo-point entries.
     */
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

    /**
     * Normalize timeline entries.
     */
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

    /**
     * Normalize shelter entries.
     */
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

    /**
     * Compute report duration from Jalali start/end dates.
     */
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

    /**
     * Convert Persian/Arabic digits to ASCII digits.
     */
    private function toEnglishDigits(string $value): string
    {
        return strtr($value, ['۰'=>'0','۱'=>'1','۲'=>'2','۳'=>'3','۴'=>'4','۵'=>'5','۶'=>'6','۷'=>'7','۸'=>'8','۹'=>'9','٠'=>'0','١'=>'1','٢'=>'2','٣'=>'3','٤'=>'4','٥'=>'5','٦'=>'6','٧'=>'7','٨'=>'8','٩'=>'9']);
    }

    /**
     * Convert a Jalali date string to Carbon, optionally with time.
     */
    private function convertJalaliDate(?string $value, bool $withTime = false)
    {
        if (!$value) {
            return null;
        }

        try {
            $format = $withTime ? 'Y/m/d H:i' : 'Y/m/d';
            $clean = $this->toEnglishDigits($value);
            $jalali = Jalalian::fromFormat($format, $clean);
            return $withTime
                ? $jalali->toCarbon()->setTime($jalali->getHour(), $jalali->getMinute())
                : $jalali->toCarbon()->startOfDay();
        } catch (\Exception $e) {
            return null;
        }
    }
}

