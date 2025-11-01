<?php

namespace Database\Seeders;

use App\Models\Enrollment;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Database\Seeder;

class EnrollmentSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::all();
        $subjects = Subject::all();
        
        foreach ($students as $student) {
            // Enroll each student in 3-6 random subjects
            $randomSubjects = $subjects->random(rand(3, 6));
            
            foreach ($randomSubjects as $subject) {
                Enrollment::create([
                    'student_id' => $student->id,
                    'subject_id' => $subject->id,
                    'enrollment_date' => now()->subDays(rand(30, 180)),
                    'academic_year' => '2024-2025',
                    'status' => 'active',
                ]);
            }
        }
    }
}
