<?php

namespace Database\Factories;

use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

class SchoolFactory extends Factory
{
    protected $model = School::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company() . ' School',
            'address' => fake()->address(),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->unique()->safeEmail(),
            'principal_name' => fake()->name(),
        ];
    }
}
