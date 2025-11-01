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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->string('category'); // e.g., utilities, salaries, supplies
            $table->string('description');
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->date('expense_date');
            $table->string('payment_method')->nullable();
            $table->string('receipt_number')->nullable();
            $table->string('vendor_name')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['school_id', 'category']);
            $table->index('expense_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
