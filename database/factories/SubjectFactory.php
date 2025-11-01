<?php

namespace Database\Factories;

use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubjectFactory extends Factory
{
    protected $model = Subject::class;

    public function definition(): array
    {
        $subjects = [
            ['name' => 'Mathematics', 'code' => 'MATH'],
            ['name' => 'Science', 'code' => 'SCI'],
            ['name' => 'English', 'code' => 'ENG'],
            ['name' => 'History', 'code' => 'HIST'],
            ['name' => 'Geography', 'code' => 'GEO'],
            ['name' => 'Physics', 'code' => 'PHY'],
            ['name' => 'Chemistry', 'code' => 'CHEM'],
            ['name' => 'Biology', 'code' => 'BIO'],
            ['name' => 'Computer Science', 'code' => 'CS'],
            ['name' => 'Physical Education', 'code' => 'PE'],
            ['name' => 'Art', 'code' => 'ART'],
            ['name' => 'Music', 'code' => 'MUS'],
            ['name' => 'French', 'code' => 'FR'],
            ['name' => 'Spanish', 'code' => 'ES'],
            ['name' => 'Economics', 'code' => 'ECON'],
        ];
        
        $subject = fake()->randomElement($subjects);
        
        return [
            'name' => $subject['name'],
            'code' => $subject['code'] . fake()->numberBetween(101, 999),
            'description' => fake()->sentence(),
            'credits' => fake()->numberBetween(1, 4),
        ];
    }
}
