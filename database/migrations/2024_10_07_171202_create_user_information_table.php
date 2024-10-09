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
        Schema::create('user_information', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('gender', ['male', 'female', 'other'])->default('male');
            $table->string('profile');
            $table->longText('address')->nullable();
            $table->string('nrc_front')->unique();
            $table->string('nrc_back')->unique();
            $table->string('nrc_number')->unique();
            $table->datetime('birth_date');
            $table->integer('age')->nullable();
            $table->foreignId('work_id')->constrained('works')->cascadeOnDelete();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_information');
    }
};
