<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\SchoolClass;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $classes = SchoolClass::all();
        
        foreach ($classes as $class) {
            Student::factory()->count(10)->create([
                'class_id' => $class->id,
            ]);
        }
    }
}
