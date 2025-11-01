<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Teacher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_teachers_index_page_can_be_rendered(): void
    {
        $response = $this->actingAs($this->user)->get('/teachers');

        $response->assertStatus(200);
    }

    public function test_teacher_can_be_created(): void
    {
        $teacherData = [
            'first_name' => 'Alice',
            'last_name' => 'Johnson',
            'email' => 'alice.johnson@example.com',
            'phone' => '987654321',
            'subject_specialization' => 'Mathematics',
            'gender' => 'female',
        ];

        $response = $this->actingAs($this->user)->post('/teachers', $teacherData);

        $response->assertRedirect('/teachers');
        $this->assertDatabaseHas('teachers', ['email' => 'alice.johnson@example.com']);
    }

    public function test_teacher_can_be_deleted(): void
    {
        $teacher = Teacher::factory()->create();

        $response = $this->actingAs($this->user)->delete("/teachers/{$teacher->id}");

        $response->assertRedirect('/teachers');
        $this->assertSoftDeleted('teachers', ['id' => $teacher->id]);
    }
}
