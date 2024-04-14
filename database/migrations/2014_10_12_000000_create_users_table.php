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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('last_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('suffix_name')->nullable();
            $table->string('lrn')->nullable();
            $table->string('birthday')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('gender')->nullable();
            $table->string('id_number')->nullable();
            $table->string('grade')->nullable();
            $table->string('section')->nullable();
            $table->json('address')->nullable();
            $table->string('password')->nullable();
            $table->string('status')->nullable();
            $table->json('student_fees')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
