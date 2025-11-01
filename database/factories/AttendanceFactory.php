<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\Student;
use App\Models\SchoolClass;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;

    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'class_id' => SchoolClass::factory(),
            'date' => fake()->date('Y-m-d', '-1 month'),
            'status' => fake()->randomElement(['present', 'absent', 'late', 'excused']),
            'remarks' => fake()->optional()->sentence(),
        ];
    }
}
