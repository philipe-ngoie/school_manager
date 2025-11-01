<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class StudentApiTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_api_students_can_be_listed(): void
    {
        Sanctum::actingAs($this->user);

        Student::factory()->count(5)->create();

        $response = $this->getJson('/api/students');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'first_name',
                        'last_name',
                        'email',
                    ],
                ],
            ]);
    }

    public function test_api_student_can_be_retrieved(): void
    {
        Sanctum::actingAs($this->user);

        $student = Student::factory()->create();

        $response = $this->getJson("/api/students/{$student->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $student->id,
                    'email' => $student->email,
                ],
            ]);
    }

    public function test_api_requires_authentication(): void
    {
        $response = $this->getJson('/api/students');

        $response->assertStatus(401);
    }
}
