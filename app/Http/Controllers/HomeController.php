<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\Course;
use App\Models\Post;
use App\Models\ProgramReport;
use App\Models\User;

class HomeController extends Controller
{
    public function index()
    {
        return view('home', [
            'latestPrograms' => Program::with('files')->latest('execution_date')->take(4)->get(),
            'latestCourses' => Course::with('files')->latest('start_date')->take(4)->get(),
            'latestPosts' => Post::published()->latest('published_at')->take(4)->get(),
            'latestReports' => ProgramReport::with('program')->latest('report_date')->take(4)->get(),
            'stats' => [
                'members' => User::count(),
                'programs' => Program::count(),
                'courses' => Course::count(),
                'reports' => ProgramReport::count(),
            ],
        ]);
    }
}
