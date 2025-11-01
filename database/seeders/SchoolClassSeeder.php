<?php

namespace Database\Seeders;

use App\Models\SchoolClass;
use App\Models\Teacher;
use Illuminate\Database\Seeder;

class SchoolClassSeeder extends Seeder
{
    public function run(): void
    {
        $teachers = Teacher::all();
        
        foreach ($teachers->take(10) as $teacher) {
            SchoolClass::factory()->create([
                'teacher_id' => $teacher->id,
            ]);
        }
    }
}
