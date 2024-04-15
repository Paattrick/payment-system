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
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('active_school_year_id')->index()->nullable();
            $table->foreign('active_school_year_id')->references('id')->on('school_years')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
<<<<<<<< HEAD:database/migrations/2024_04_14_044357_remove_id_number_to_users_table.php
            $table->integer("id_number");
========
            $table->dropForeign('active_school_year_id');
>>>>>>>> master:database/migrations/2024_04_14_145849_add_school_year_id_as_foreign_key_in_users_table.php
        });
    }
};
