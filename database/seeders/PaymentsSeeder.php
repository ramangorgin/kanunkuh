<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Payment;
use App\Models\User;
use App\Models\Program;
use App\Models\Course;
use Illuminate\Support\Str;

class PaymentsSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        if (!$user) {
            return;
        }

        $programId = Program::first()?->id;
        $courseId = Course::first()?->id;

        Payment::create([
            'user_id' => $user->id,
            'amount' => 350000,
            'type' => 'membership',
            'year' => now()->year,
            'status' => 'approved',
            'membership_code' => 'M-' . Str::upper(Str::random(6)),
            'transaction_code' => 'T-' . Str::upper(Str::random(6)),
        ]);

        if ($programId) {
            Payment::create([
                'user_id' => $user->id,
                'amount' => 500000,
                'type' => 'program',
                'related_id' => $programId,
                'status' => 'approved',
                'transaction_code' => 'P-' . Str::upper(Str::random(6)),
            ]);
        }

        if ($courseId) {
            Payment::create([
                'user_id' => $user->id,
                'amount' => 450000,
                'type' => 'course',
                'related_id' => $courseId,
                'status' => 'approved',
                'transaction_code' => 'C-' . Str::upper(Str::random(6)),
            ]);
        }
    }
}
