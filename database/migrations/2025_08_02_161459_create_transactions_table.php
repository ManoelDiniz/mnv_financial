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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['income', 'expense', 'transfer'])->default('expense');
            $table->decimal('amount', 12, 2);
            $table->string('description');
            $table->text('notes')->nullable();
            $table->string('category')->nullable();
            $table->date('transaction_date');
            $table->boolean('is_recurring')->default(false);
            $table->string('recurring_frequency')->nullable(); // monthly, weekly, yearly
            $table->timestamps();

            $table->index(['account_id', 'transaction_date']);
            $table->index(['type', 'transaction_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
