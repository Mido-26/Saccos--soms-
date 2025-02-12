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
        Schema::create('loan_repayments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained('loans')->onDelete('cascade'); // Loan reference
            $table->decimal('amount', 10, 2); // Installment amount
            $table->date('due_date'); // Due date of the installment
            $table->date('paid_at')->nullable(); // When the installment was paid
            $table->enum('status', ['pending', 'paid', 'overdue'])->default('pending'); // Installment status
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_repayments');
    }
};
