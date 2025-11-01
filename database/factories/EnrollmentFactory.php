<?php

namespace Database\Factories;

use App\Models\Enrollment;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

class EnrollmentFactory extends Factory
{
    protected $model = Enrollment::class;

    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'subject_id' => Subject::factory(),
            'enrollment_date' => fake()->date('Y-m-d', '-6 months'),
            'academic_year' => '2024-2025',
            'status' => fake()->randomElement(['active', 'completed', 'dropped']),
        ];
    }
}
