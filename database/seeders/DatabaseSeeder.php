<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{School, Teacher, SchoolClass, Student, Subject, Enrollment, FeeType, Invoice, InvoiceLine, Payment, Expense, Transaction, CurrencyRate, User, Team};
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@schoolmanager.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        // Create team for admin
        $team = Team::forceCreate([
            'user_id' => $admin->id,
            'name' => explode(' ', $admin->name, 2)[0]."'s Team",
            'personal_team' => true,
        ]);

        // Create 1 School as specified
        $school = School::create([
            'name' => 'Demo International School',
            'code' => 'DEMO001',
            'address' => '123 Education Street, Knowledge City',
            'phone' => '+1234567890',
            'email' => 'info@demoschool.com',
            'currency' => 'USD',
            'is_active' => true,
        ]);

        // Create 20 Teachers as specified
        $teachers = [];
        for ($i = 1; $i <= 20; $i++) {
            $teachers[] = Teacher::create([
                'school_id' => $school->id,
                'first_name' => fake()->firstName(),
                'last_name' => fake()->lastName(),
                'email' => fake()->unique()->safeEmail(),
                'phone' => fake()->phoneNumber(),
                'employee_id' => 'T' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'hire_date' => fake()->dateTimeBetween('-5 years', '-1 year'),
                'specialization' => fake()->randomElement(['Mathematics', 'Science', 'English', 'History', 'Arts', 'Physical Education']),
                'is_active' => true,
            ]);
        }

        // Create 10 School Classes as specified
        $classes = [];
        $gradeLevels = ['Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6', 'Grade 7', 'Grade 8', 'Grade 9', 'Grade 10'];
        foreach ($gradeLevels as $index => $level) {
            $classes[] = SchoolClass::create([
                'school_id' => $school->id,
                'teacher_id' => $teachers[array_rand($teachers)]->id,
                'name' => $level . ' - Section A',
                'grade_level' => $level,
                'section' => 'A',
                'academic_year' => '2024-2025',
                'capacity' => 30,
                'description' => "Main section for {$level}",
                'is_active' => true,
            ]);
        }

        // Create 15 Subjects as specified
        $subjects = [];
        $subjectNames = ['Mathematics', 'English', 'Science', 'History', 'Geography', 'Physics', 'Chemistry', 'Biology', 'Computer Science', 'Art', 'Music', 'Physical Education', 'French', 'Spanish', 'Economics'];
        foreach ($subjectNames as $index => $name) {
            $subjects[] = Subject::create([
                'school_id' => $school->id,
                'name' => $name,
                'code' => strtoupper(substr($name, 0, 3)) . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'description' => "{$name} curriculum for academic year",
                'credits' => fake()->numberBetween(1, 4),
                'is_active' => true,
            ]);
        }

        // Create 100 Students as specified
        $students = [];
        for ($i = 1; $i <= 100; $i++) {
            $students[] = Student::create([
                'school_id' => $school->id,
                'school_class_id' => $classes[array_rand($classes)]->id,
                'first_name' => fake()->firstName(),
                'last_name' => fake()->lastName(),
                'registration_number' => 'STU' . date('Y') . str_pad($i, 4, '0', STR_PAD_LEFT),
                'date_of_birth' => fake()->dateTimeBetween('-18 years', '-6 years'),
                'gender' => fake()->randomElement(['male', 'female']),
                'address' => fake()->address(),
                'parent_name' => fake()->name(),
                'parent_email' => fake()->unique()->safeEmail(),
                'parent_phone' => fake()->phoneNumber(),
                'enrollment_date' => fake()->dateTimeBetween('-3 years', '-1 month'),
                'is_active' => true,
            ]);
        }

        // Create random enrollments (students enrolled in subjects)
        foreach ($students as $student) {
            $randomSubjects = fake()->randomElements($subjects, fake()->numberBetween(3, 8));
            foreach ($randomSubjects as $subject) {
                Enrollment::create([
                    'student_id' => $student->id,
                    'subject_id' => $subject->id,
                    'school_class_id' => $student->school_class_id,
                    'enrollment_date' => $student->enrollment_date,
                    'academic_year' => '2024-2025',
                    'status' => 'active',
                ]);
            }
        }

        // Create Fee Types
        $feeTypes = [
            ['name' => 'Tuition Fee', 'code' => 'TUI001', 'default_amount' => 1000.00, 'frequency' => 'quarterly'],
            ['name' => 'Registration Fee', 'code' => 'REG001', 'default_amount' => 200.00, 'frequency' => 'one_time'],
            ['name' => 'Lab Fee', 'code' => 'LAB001', 'default_amount' => 150.00, 'frequency' => 'annual'],
            ['name' => 'Library Fee', 'code' => 'LIB001', 'default_amount' => 50.00, 'frequency' => 'annual'],
            ['name' => 'Sports Fee', 'code' => 'SPT001', 'default_amount' => 100.00, 'frequency' => 'annual'],
        ];

        $createdFeeTypes = [];
        foreach ($feeTypes as $feeType) {
            $createdFeeTypes[] = FeeType::create(array_merge($feeType, [
                'school_id' => $school->id,
                'currency' => 'USD',
                'is_active' => true,
            ]));
        }

        // Create Invoices and Payments (50 payments as specified - mix of success/failed/refunded)
        $paymentStatuses = ['completed', 'completed', 'completed', 'completed', 'failed', 'refunded'];
        $paymentMethods = ['cinetpay', 'stripe', 'cash', 'bank_transfer'];

        foreach (array_slice($students, 0, 50) as $student) {
            $invoice = Invoice::create([
                'school_id' => $school->id,
                'student_id' => $student->id,
                'invoice_number' => 'INV-' . date('Y') . '-' . str_pad($student->id, 5, '0', STR_PAD_LEFT),
                'issue_date' => fake()->dateTimeBetween('-6 months', '-1 month'),
                'due_date' => fake()->dateTimeBetween('-1 month', '+1 month'),
                'subtotal' => 1000.00,
                'tax_amount' => 0,
                'total_amount' => 1000.00,
                'paid_amount' => 0,
                'currency' => 'USD',
                'status' => 'pending',
            ]);

            // Add invoice lines
            $randomFees = fake()->randomElements($createdFeeTypes, fake()->numberBetween(1, 3));
            $total = 0;
            foreach ($randomFees as $feeType) {
                $amount = $feeType->default_amount;
                InvoiceLine::create([
                    'invoice_id' => $invoice->id,
                    'fee_type_id' => $feeType->id,
                    'description' => $feeType->name,
                    'quantity' => 1,
                    'unit_price' => $amount,
                    'amount' => $amount,
                ]);
                $total += $amount;
            }

            $invoice->update(['subtotal' => $total, 'total_amount' => $total]);

            // Create payment
            $status = $paymentStatuses[array_rand($paymentStatuses)];
            $payment = Payment::create([
                'invoice_id' => $invoice->id,
                'student_id' => $student->id,
                'payment_reference' => 'PAY-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 8)),
                'provider_payment_id' => 'PROV-' . strtoupper(substr(md5(uniqid()), 0, 12)),
                'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                'amount' => $total,
                'currency' => 'USD',
                'status' => $status,
                'paid_at' => $status === 'completed' ? fake()->dateTimeBetween('-3 months', 'now') : null,
            ]);

            if ($status === 'completed') {
                $invoice->update(['paid_amount' => $total, 'status' => 'paid']);

                Transaction::create([
                    'payment_id' => $payment->id,
                    'school_id' => $school->id,
                    'transaction_id' => 'TXN-' . strtoupper(substr(md5(uniqid()), 0, 16)),
                    'type' => 'payment',
                    'amount' => $total,
                    'currency' => 'USD',
                    'description' => "Payment for invoice {$invoice->invoice_number}",
                    'transaction_date' => $payment->paid_at,
                ]);
            } elseif ($status === 'refunded') {
                $invoice->update(['paid_amount' => 0, 'status' => 'pending']);
            }
        }

        // Create Expenses
        $expenseCategories = ['utilities', 'salaries', 'supplies', 'maintenance', 'transportation', 'marketing'];
        for ($i = 0; $i < 30; $i++) {
            Expense::create([
                'school_id' => $school->id,
                'category' => $expenseCategories[array_rand($expenseCategories)],
                'description' => fake()->sentence(),
                'amount' => fake()->randomFloat(2, 50, 5000),
                'currency' => 'USD',
                'expense_date' => fake()->dateTimeBetween('-6 months', 'now'),
                'payment_method' => fake()->randomElement(['cash', 'bank_transfer', 'credit_card']),
                'receipt_number' => 'REC-' . strtoupper(fake()->bothify('????###')),
                'vendor_name' => fake()->company(),
            ]);
        }

        // Create Currency Rates
        $currencies = [
            ['from' => 'USD', 'to' => 'XOF', 'rate' => 600.00],
            ['from' => 'USD', 'to' => 'EUR', 'rate' => 0.92],
            ['from' => 'EUR', 'to' => 'USD', 'rate' => 1.09],
            ['from' => 'EUR', 'to' => 'XOF', 'rate' => 655.96],
            ['from' => 'XOF', 'to' => 'USD', 'rate' => 0.0017],
            ['from' => 'XOF', 'to' => 'EUR', 'rate' => 0.0015],
        ];

        foreach ($currencies as $currency) {
            CurrencyRate::create([
                'from_currency' => $currency['from'],
                'to_currency' => $currency['to'],
                'rate' => $currency['rate'],
                'effective_date' => now(),
            ]);
        }

        $this->command->info('Database seeded successfully!');
        $this->command->info('Admin email: admin@schoolmanager.com');
        $this->command->info('Admin password: password');
    }
}
