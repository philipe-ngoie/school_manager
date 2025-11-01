import AppLayout from '@/Layouts/AppLayout';
import { Head, Link } from '@inertiajs/react';

export default function Dashboard({ stats }) {
    return (
        <AppLayout
            header={<h2 className="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Dashboard</h2>}
        >
            <Head title="Dashboard" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                            <div className="bg-blue-500 dark:bg-blue-600 rounded-lg shadow-lg p-6 text-white">
                                <div className="flex items-center justify-between">
                                    <div>
                                        <p className="text-sm font-medium uppercase">Students</p>
                                        <p className="text-3xl font-bold">{stats?.students || 0}</p>
                                    </div>
                                    <svg className="w-12 h-12 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </div>
                                <Link href={route('students.index')} className="text-sm mt-2 inline-block hover:underline">
                                    View all students →
                                </Link>
                            </div>

                            <div className="bg-green-500 dark:bg-green-600 rounded-lg shadow-lg p-6 text-white">
                                <div className="flex items-center justify-between">
                                    <div>
                                        <p className="text-sm font-medium uppercase">Teachers</p>
                                        <p className="text-3xl font-bold">{stats?.teachers || 0}</p>
                                    </div>
                                    <svg className="w-12 h-12 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <Link href={route('teachers.index')} className="text-sm mt-2 inline-block hover:underline">
                                    View all teachers →
                                </Link>
                            </div>

                            <div className="bg-purple-500 dark:bg-purple-600 rounded-lg shadow-lg p-6 text-white">
                                <div className="flex items-center justify-between">
                                    <div>
                                        <p className="text-sm font-medium uppercase">Classes</p>
                                        <p className="text-3xl font-bold">{stats?.classes || 0}</p>
                                    </div>
                                    <svg className="w-12 h-12 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                                <Link href={route('classes.index')} className="text-sm mt-2 inline-block hover:underline">
                                    View all classes →
                                </Link>
                            </div>

                            <div className="bg-orange-500 dark:bg-orange-600 rounded-lg shadow-lg p-6 text-white">
                                <div className="flex items-center justify-between">
                                    <div>
                                        <p className="text-sm font-medium uppercase">Subjects</p>
                                        <p className="text-3xl font-bold">{stats?.subjects || 0}</p>
                                    </div>
                                    <svg className="w-12 h-12 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                </div>
                                <Link href={route('subjects.index')} className="text-sm mt-2 inline-block hover:underline">
                                    View all subjects →
                                </Link>
                            </div>
                        </div>

                        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div className="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                                <h3 className="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">
                                    Recent Enrollments
                                </h3>
                                <p className="text-gray-600 dark:text-gray-400">
                                    {stats?.recentEnrollments || 0} students enrolled this week
                                </p>
                            </div>

                            <div className="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                                <h3 className="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">
                                    Attendance Summary
                                </h3>
                                <p className="text-gray-600 dark:text-gray-400">
                                    Average attendance: {stats?.attendanceRate || 0}%
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
