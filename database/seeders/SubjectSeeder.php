<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        $subjects = [
            ['name' => 'Mathematics', 'code' => 'MATH101', 'credits' => 4],
            ['name' => 'Science', 'code' => 'SCI101', 'credits' => 4],
            ['name' => 'English', 'code' => 'ENG101', 'credits' => 3],
            ['name' => 'History', 'code' => 'HIST101', 'credits' => 3],
            ['name' => 'Geography', 'code' => 'GEO101', 'credits' => 3],
            ['name' => 'Physics', 'code' => 'PHY101', 'credits' => 4],
            ['name' => 'Chemistry', 'code' => 'CHEM101', 'credits' => 4],
            ['name' => 'Biology', 'code' => 'BIO101', 'credits' => 4],
            ['name' => 'Computer Science', 'code' => 'CS101', 'credits' => 3],
            ['name' => 'Physical Education', 'code' => 'PE101', 'credits' => 2],
            ['name' => 'Art', 'code' => 'ART101', 'credits' => 2],
            ['name' => 'Music', 'code' => 'MUS101', 'credits' => 2],
            ['name' => 'French', 'code' => 'FR101', 'credits' => 3],
            ['name' => 'Spanish', 'code' => 'ES101', 'credits' => 3],
            ['name' => 'Economics', 'code' => 'ECON101', 'credits' => 3],
        ];

        foreach ($subjects as $subject) {
            Subject::create([
                'name' => $subject['name'],
                'code' => $subject['code'],
                'description' => 'Introduction to ' . $subject['name'],
                'credits' => $subject['credits'],
            ]);
        }
    }
}
