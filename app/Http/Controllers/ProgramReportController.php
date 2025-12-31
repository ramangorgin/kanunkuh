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
        $reports = ProgramReport::with('program')->latest()->get();
        return view('admin.program_reports.index', compact('reports'));
    }

    public function create()
    {
        $programs = Program::where('status', 'done')->orWhere('status', 'closed')->get();
        return view('admin.program_reports.create', compact('programs'));
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
        ]);

        DB::transaction(function () use ($validated) {
            ProgramReport::create($validated);
        });

        return redirect()->route('admin.program_reports.index')->with('success', 'گزارش برنامه با موفقیت ثبت شد.');
    }

    public function show(ProgramReport $programReport)
    {
        $programReport->load('program');
        return view('admin.program_reports.show', compact('programReport'));
    }

    public function edit(ProgramReport $programReport)
    {
        $programs = Program::where('status', 'done')->orWhere('status', 'closed')->get();
        $programReport->load('program');
        return view('admin.program_reports.edit', compact('programReport', 'programs'));
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
        ]);

        DB::transaction(function () use ($programReport, $validated) {
            $programReport->update($validated);
        });

        return redirect()->route('admin.program_reports.index')->with('success', 'گزارش برنامه با موفقیت ویرایش شد.');
    }

    public function destroy(ProgramReport $programReport)
    {
        $programReport->delete();
        return redirect()->route('admin.program_reports.index')->with('success', 'گزارش برنامه با موفقیت حذف شد.');
    }
}

