<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Student;
use App\Models\SchoolClass;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_students_index_page_can_be_rendered(): void
    {
        $response = $this->actingAs($this->user)->get('/students');

        $response->assertStatus(200);
    }

    public function test_student_can_be_created(): void
    {
        $class = SchoolClass::factory()->create();

        $studentData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'date_of_birth' => '2010-01-01',
            'gender' => 'male',
            'phone' => '123456789',
            'class_id' => $class->id,
            'enrollment_date' => now()->toDateString(),
            'student_number' => 'STU12345',
        ];

        $response = $this->actingAs($this->user)->post('/students', $studentData);

        $response->assertRedirect('/students');
        $this->assertDatabaseHas('students', ['email' => 'john.doe@example.com']);
    }

    public function test_student_can_be_updated(): void
    {
        $student = Student::factory()->create();

        $updateData = [
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => $student->email,
            'date_of_birth' => $student->date_of_birth->toDateString(),
            'gender' => $student->gender,
            'class_id' => $student->class_id,
            'enrollment_date' => $student->enrollment_date->toDateString(),
            'student_number' => $student->student_number,
        ];

        $response = $this->actingAs($this->user)->put("/students/{$student->id}", $updateData);

        $response->assertRedirect('/students');
        $this->assertDatabaseHas('students', ['id' => $student->id, 'first_name' => 'Jane']);
    }

    public function test_student_can_be_deleted(): void
    {
        $student = Student::factory()->create();

        $response = $this->actingAs($this->user)->delete("/students/{$student->id}");

        $response->assertRedirect('/students');
        $this->assertSoftDeleted('students', ['id' => $student->id]);
    }
}
