<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::firstOrCreate(
            ['phone' => '09000000001'],
            [
                'role' => 'admin',
                'status' => 'active',
            ]
        );

        // Member users
        for ($i = 2; $i <= 6; $i++) {
            User::firstOrCreate(
                ['phone' => '0900000000' . $i],
                [
                    'role' => 'member',
                    'status' => 'active',
                ]
            );
        }
    }
}
