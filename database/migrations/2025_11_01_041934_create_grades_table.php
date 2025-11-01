<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->string('grade_type'); // e.g., 'midterm', 'final', 'quiz', 'assignment'
            $table->decimal('grade', 5, 2);
            $table->decimal('max_grade', 5, 2)->default(100);
            $table->date('graded_date');
            $table->text('comments')->nullable();
            $table->timestamps();
            
            $table->index('student_id');
            $table->index('subject_id');
            $table->index('graded_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
