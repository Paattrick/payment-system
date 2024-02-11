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
            $table->string('name');
            $table->string('email')->unique()->nullable();
            $table->string('last_name');
            $table->string('middle_name');
            $table->string('suffix_name')->nullable();
            $table->string('lrn')->nullable();
            $table->string('birthday')->nullable();
            $table->string('age')->nullable();
            $table->string('contact_number');
            $table->string('gender');
            $table->string('id_number')->nullable();
            $table->string('grade')->nullable();
            $table->string('section')->nullable();
            $table->string('province');
            $table->string('municipality');
            $table->string('barangay');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
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
