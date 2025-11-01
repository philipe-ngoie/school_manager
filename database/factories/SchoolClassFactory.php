<?php

namespace Database\Factories;

use App\Models\SchoolClass;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;

class SchoolClassFactory extends Factory
{
    protected $model = SchoolClass::class;

    public function definition(): array
    {
        $gradeLevel = fake()->randomElement(['Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6', 'Grade 7', 'Grade 8', 'Grade 9', 'Grade 10']);
        $section = fake()->randomElement(['A', 'B', 'C', 'D']);
        
        return [
            'name' => $gradeLevel . ' - Section ' . $section,
            'grade_level' => $gradeLevel,
            'section' => $section,
            'teacher_id' => Teacher::factory(),
            'room_number' => 'Room ' . fake()->numberBetween(101, 399),
            'capacity' => fake()->numberBetween(25, 40),
            'academic_year' => '2024-2025',
        ];
    }
}
