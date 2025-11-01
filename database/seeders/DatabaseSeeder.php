<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SchoolSeeder::class,
            TeacherSeeder::class,
            SchoolClassSeeder::class,
            SubjectSeeder::class,
            StudentSeeder::class,
            EnrollmentSeeder::class,
            GradeSeeder::class,
            AttendanceSeeder::class,
        ]);
    }
}
