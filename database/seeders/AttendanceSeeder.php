<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Student;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::all();
        $statuses = ['present', 'present', 'present', 'present', 'absent', 'late']; // More present than absent
        
        foreach ($students as $student) {
            // Create attendance records for the last 30 days
            for ($i = 0; $i < 30; $i++) {
                Attendance::create([
                    'student_id' => $student->id,
                    'class_id' => $student->class_id,
                    'date' => now()->subDays($i),
                    'status' => $statuses[array_rand($statuses)],
                    'remarks' => null,
                ]);
            }
        }
    }
}
