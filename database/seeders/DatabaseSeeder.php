<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            FederationCoursesSeeder::class,
            InitialDataSeeder::class,
            NotificationTemplateSeeder::class,
            UsersSeeder::class,
            ProfilesSeeder::class,
            ProgramsSeeder::class,
            CoursesSeeder::class,
            EducationalHistoriesSeeder::class,
            PaymentsSeeder::class,
        ]);
    }
}
