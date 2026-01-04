<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Profile;
use Carbon\Carbon;

class ProfilesSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        foreach ($users as $idx => $user) {
            if ($user->profile) {
                continue;
            }

            $membershipId = Profile::generateMembershipId();
            Profile::create([
                'user_id' => $user->id,
                'membership_id' => $membershipId,
                'membership_type' => 'official',
                'membership_status' => 'approved',
                'membership_start' => Carbon::now()->subMonths(3),
                'membership_expiry' => Carbon::now()->addYear(),
                'first_name' => 'کاربر',
                'last_name' => (string)($idx + 1),
                'father_name' => 'پدر',
                'id_number' => '12345' . $idx,
                'id_place' => 'تهران',
                'birth_date' => Carbon::now()->subYears(25 + $idx),
                'national_id' => '00112233' . $idx,
                'photo' => 'photos/raman_photo.jpg',
                'national_card' => 'cards/raman_card.jpg',
                'marital_status' => 'مجرد',
                'emergency_phone' => '0912000000' . $idx,
                'referrer' => 'باشگاه',
                'education' => 'لیسانس',
                'job' => 'کارمند',
                'home_address' => 'ایران، تهران، خیابان نمونه',
                'work_address' => 'ایران، تهران، خیابان نمونه',
            ]);
        }
    }
}
