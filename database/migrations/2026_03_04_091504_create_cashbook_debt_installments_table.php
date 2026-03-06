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
        Schema::create('cashbook_debt_installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('debt_id')->constrained('cashbook_debts')->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->date('payment_date');
            $table->text('notes')->nullable();
            $table->foreignId('transaction_id')->nullable()->constrained('cashbook_transactions')->nullOnDelete()->comment('Linked to overall cashbook actual transactions');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cashbook_debt_installments');
    }
};
