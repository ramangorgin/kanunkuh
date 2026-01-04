<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use Carbon\Carbon;

class CoursesSeeder extends Seeder
{
    public function run(): void
    {
        if (Course::count() > 0) {
            return;
        }

        $today = Carbon::today();
        $courses = [
            [
                'title' => 'دوره مقدماتی کوهپیمایی',
                'start_date' => $today->copy()->addDays(10),
                'end_date' => $today->copy()->addDays(12),
                'place' => 'درکه',
                'capacity' => 20,
                'member_cost' => 400000,
                'guest_cost' => 600000,
                'status' => 'published',
                'is_registration_open' => true,
            ],
            [
                'title' => 'دوره امداد و نجات',
                'start_date' => $today->copy()->addDays(20),
                'end_date' => $today->copy()->addDays(22),
                'place' => 'توچال',
                'capacity' => 15,
                'member_cost' => 500000,
                'guest_cost' => 700000,
                'status' => 'published',
                'is_registration_open' => true,
            ],
            [
                'title' => 'دوره هواشناسی کوهستان',
                'start_date' => $today->copy()->addDays(5),
                'end_date' => $today->copy()->addDays(5),
                'place' => 'دفتر باشگاه',
                'capacity' => 25,
                'member_cost' => 300000,
                'guest_cost' => 500000,
                'status' => 'published',
                'is_registration_open' => true,
            ],
        ];

        foreach ($courses as $c) {
            Course::create($c);
        }
    }
}
