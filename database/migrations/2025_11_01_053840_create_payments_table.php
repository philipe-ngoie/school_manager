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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->string('payment_reference')->unique();
            $table->string('provider_payment_id')->unique()->nullable();
            $table->string('payment_method'); // e.g., cinetpay, stripe, cash, bank_transfer
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'refunded', 'cancelled'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->text('provider_response')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['invoice_id', 'status']);
            $table->index(['student_id', 'status']);
            $table->index('payment_reference');
            $table->index('provider_payment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
