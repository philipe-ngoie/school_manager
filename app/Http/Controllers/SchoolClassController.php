<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SchoolClassController extends Controller
{
    public function index()
    {
        $classes = SchoolClass::with('teacher')
            ->withCount('students')
            ->paginate(20);

        return Inertia::render('Classes/Index', [
            'classes' => $classes,
        ]);
    }

    public function create()
    {
        $teachers = Teacher::all();

        return Inertia::render('Classes/Create', [
            'teachers' => $teachers,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'grade_level' => 'required|string|max:255',
            'section' => 'nullable|string|max:255',
            'teacher_id' => 'nullable|exists:teachers,id',
            'room_number' => 'nullable|string|max:255',
            'capacity' => 'required|integer|min:1',
            'academic_year' => 'required|string|max:255',
        ]);

        SchoolClass::create($validated);

        return redirect()->route('classes.index')
            ->with('success', 'Class created successfully.');
    }

    public function show(SchoolClass $class)
    {
        $class->load(['teacher', 'students']);

        return Inertia::render('Classes/Show', [
            'class' => $class,
        ]);
    }

    public function edit(SchoolClass $class)
    {
        $teachers = Teacher::all();

        return Inertia::render('Classes/Edit', [
            'class' => $class,
            'teachers' => $teachers,
        ]);
    }

    public function update(Request $request, SchoolClass $class)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'grade_level' => 'required|string|max:255',
            'section' => 'nullable|string|max:255',
            'teacher_id' => 'nullable|exists:teachers,id',
            'room_number' => 'nullable|string|max:255',
            'capacity' => 'required|integer|min:1',
            'academic_year' => 'required|string|max:255',
        ]);

        $class->update($validated);

        return redirect()->route('classes.index')
            ->with('success', 'Class updated successfully.');
    }

    public function destroy(SchoolClass $class)
    {
        $class->delete();

        return redirect()->route('classes.index')
            ->with('success', 'Class deleted successfully.');
    }
}
