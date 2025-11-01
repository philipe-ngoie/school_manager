<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Enrollment;
use App\Models\Attendance;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'students' => Student::count(),
            'teachers' => Teacher::count(),
            'classes' => SchoolClass::count(),
            'subjects' => Subject::count(),
            'recentEnrollments' => Enrollment::where('created_at', '>=', now()->subWeek())->count(),
            'attendanceRate' => $this->calculateAttendanceRate(),
        ];

        return Inertia::render('Dashboard', [
            'stats' => $stats,
        ]);
    }

    private function calculateAttendanceRate()
    {
        $totalAttendances = Attendance::where('date', '>=', now()->subMonth())->count();
        if ($totalAttendances === 0) {
            return 0;
        }
        $presentAttendances = Attendance::where('date', '>=', now()->subMonth())
            ->where('status', 'present')
            ->count();
        
        return round(($presentAttendances / $totalAttendances) * 100, 2);
    }
}
