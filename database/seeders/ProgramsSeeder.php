<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Program;
use Carbon\Carbon;

class ProgramsSeeder extends Seeder
{
    public function run(): void
    {
        if (Program::count() > 0) {
            return;
        }

        $base = Carbon::now();
        $programs = [
            [
                'name' => 'صعود قله توچال',
                'program_type' => 'کوهنوردی',
                'region_name' => 'توچال تهران',
                'execution_date' => $base->copy()->addDays(7),
                'move_from_karaj' => 'ساعت 5 صبح از میدان کرج',
                'move_from_tehran' => 'ساعت 5:30 صبح از میدان تجریش',
                'cost_member' => 500000,
                'cost_guest' => 700000,
                'status' => 'open',
            ],
            [
                'name' => 'پیاده روی دره گرگان',
                'program_type' => 'طبیعت‌گردی',
                'region_name' => 'گرگان',
                'execution_date' => $base->copy()->addDays(14),
                'move_from_karaj' => 'ترمینال کرج',
                'move_from_tehran' => 'ترمینال شرق',
                'cost_member' => 300000,
                'cost_guest' => 500000,
                'status' => 'open',
            ],
            [
                'name' => 'دیواره نوردی پل خواب',
                'program_type' => 'سنگ‌نوردی',
                'region_name' => 'پل خواب',
                'execution_date' => $base->copy()->addDays(30),
                'move_from_karaj' => 'کرج، پل فردیس',
                'move_from_tehran' => 'تهران، میدان آزادی',
                'cost_member' => 600000,
                'cost_guest' => 800000,
                'status' => 'draft',
            ],
        ];

        foreach ($programs as $p) {
            Program::create($p);
        }
    }
}
