<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->decimal('amount', 10, 2);
            $table->enum('type', ['income', 'expense', 'transfer']);
            $table->date('date');
            $table->text('description')->nullable();
            $table->foreignId('account_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();

            // Para transferências entre contas
            $table->foreignId('to_account_id')->nullable()->constrained('accounts')->onDelete('set null');
            $table->decimal('converted_amount', 10, 2)->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};
