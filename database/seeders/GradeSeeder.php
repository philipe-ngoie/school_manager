<?php

namespace Database\Seeders;

use App\Models\Grade;
use App\Models\Enrollment;
use Illuminate\Database\Seeder;

class GradeSeeder extends Seeder
{
    public function run(): void
    {
        $enrollments = Enrollment::all();
        
        foreach ($enrollments as $enrollment) {
            // Create 2-4 grades per enrollment
            $gradeTypes = ['quiz', 'assignment', 'midterm', 'final'];
            $randomGradeTypes = collect($gradeTypes)->random(rand(2, 4));
            
            foreach ($randomGradeTypes as $gradeType) {
                Grade::create([
                    'student_id' => $enrollment->student_id,
                    'subject_id' => $enrollment->subject_id,
                    'grade_type' => $gradeType,
                    'grade' => rand(50, 100),
                    'max_grade' => 100,
                    'graded_date' => now()->subDays(rand(1, 90)),
                    'comments' => null,
                ]);
            }
        }
    }
}
