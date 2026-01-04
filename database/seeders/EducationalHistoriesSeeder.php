<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EducationalHistory;
use App\Models\User;
use Carbon\Carbon;

class EducationalHistoriesSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::take(3)->get();
        foreach ($users as $idx => $user) {
            EducationalHistory::create([
                'user_id' => $user->id,
                'federation_course_id' => null,
                'custom_course_title' => 'دوره آزمایشی ' . ($idx + 1),
                'issue_date' => Carbon::now()->subMonths($idx + 1)->toDateString(),
                'certificate_file' => null,
            ]);
        }
    }
}
