<?php

namespace Database\Seeders;

use App\Models\School;
use Illuminate\Database\Seeder;

class SchoolSeeder extends Seeder
{
    public function run(): void
    {
        School::factory()->create([
            'name' => 'Demo High School',
            'email' => 'info@demohighschool.edu',
        ]);
    }
}
