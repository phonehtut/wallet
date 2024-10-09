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
            $table->foreignId('pay_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('recipient_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->decimal('amount', 8, 2)->nullable()->default(0);
            $table->string('transaction_number')->unique();
            $table->dateTime('transaction_date')->nullable();
            $table->decimal('service_charge', 8, 2)->nullable()->default(0);
            $table->text('remarks')->nullable();
            $table->decimal('total_amount', 8, 2)->nullable()->default(0);
            $table->timestamps();
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
