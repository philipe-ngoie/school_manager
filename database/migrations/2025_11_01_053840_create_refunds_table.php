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
        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained()->onDelete('cascade');
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
            $table->string('refund_reference')->unique();
            $table->string('provider_refund_id')->nullable()->unique();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->enum('status', ['requested', 'processing', 'completed', 'failed', 'cancelled'])->default('requested');
            $table->text('reason')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->text('provider_response')->nullable();
            $table->timestamps();
            
            $table->index(['payment_id', 'status']);
            $table->index('refund_reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refunds');
    }
};
