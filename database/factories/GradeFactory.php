<?php

namespace Database\Factories;

use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

class GradeFactory extends Factory
{
    protected $model = Grade::class;

    public function definition(): array
    {
        $maxGrade = 100;
        return [
            'student_id' => Student::factory(),
            'subject_id' => Subject::factory(),
            'grade_type' => fake()->randomElement(['midterm', 'final', 'quiz', 'assignment', 'project']),
            'grade' => fake()->randomFloat(2, 50, 100),
            'max_grade' => $maxGrade,
            'graded_date' => fake()->date('Y-m-d', '-3 months'),
            'comments' => fake()->optional()->sentence(),
        ];
    }
}
