<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProgramReport;
use App\Models\Program;
use Illuminate\Support\Facades\DB;

class ProgramReportController extends Controller
{
    public function index()
    {
        $reports = ProgramReport::with('program.files')->latest()->get();
        return view('program_reports.index', compact('reports'));
    }

    public function create()
    {
        // Get programs where execution date has passed (including today)
        $programs = Program::whereNotNull('execution_date')
            ->whereRaw('DATE(execution_date) <= DATE(NOW())')
            ->whereDoesntHave('report') // Programs without existing report
            ->orderBy('execution_date', 'desc')
            ->get();
        
        // Get users for reporter/leader selection
        $users = \App\Models\User::with('profile')->get();
        
        $isAdmin = true;
        $program = null;
        $approvedRegistrations = collect();
        $guestRegistrations = collect();
        
        // If a program is selected in old input, load it
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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'program_id' => 'required|exists:programs,id',
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
            
            // Check if report already exists
            if ($program->report) {
                throw new \Exception('گزارش این برنامه قبلاً ثبت شده است.');
            }
            
            $report = ProgramReport::create($validated);
            
            // Handle file uploads
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
        });

        return redirect()->route('admin.program_reports.index')->with('success', 'گزارش برنامه با موفقیت ثبت شد.');
    }

    public function show(ProgramReport $programReport)
    {
        $programReport->load([
            'program.files',
            'program.userRoles.user.profile',
            'reporter.profile'
        ]);
        return view('program_reports.show', compact('programReport'));
    }

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

    public function edit(ProgramReport $programReport)
    {
        // Load program with necessary relationships
        $programReport->load([
            'program.files',
            'program.userRoles.user.profile',
            'program.registrations.user.profile',
            'reporter.profile'
        ]);
        
        $program = $programReport->program;
        
        // Get users for reporter/leader selection
        $users = \App\Models\User::with('profile')->get();
        
        // Get programs list (for admin to change program if needed)
        $programs = Program::whereNotNull('execution_date')
            ->where('execution_date', '<=', now())
            ->orderBy('execution_date', 'desc')
            ->get();
        
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

    public function update(Request $request, ProgramReport $programReport)
    {
        $validated = $request->validate([
            'program_id' => 'required|exists:programs,id',
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
            $programReport->update($validated);
            
            $program = $programReport->program;
            
            // Handle file uploads
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
            
            // Handle file deletions
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

    public function destroy(ProgramReport $programReport)
    {
        $programReport->delete();
        return redirect()->route('admin.program_reports.index')->with('success', 'گزارش برنامه با موفقیت حذف شد.');
    }
}

