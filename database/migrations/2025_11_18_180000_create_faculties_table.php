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
        Schema::create('faculties', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('position')->nullable();
            $table->string('gender', 12)->nullable();
            $table->string('blood_type', 8)->nullable();
            $table->string('address')->nullable();
            $table->string('emergency_contact_address')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_number', 32)->nullable();
            $table->string('civil_status', 20)->nullable();
            $table->string('gsis_number', 32)->nullable();
            $table->string('sss_number', 32)->nullable();
            $table->string('tin_number', 32)->nullable();
            $table->date('birthday')->nullable();
            $table->string('photo_path')->nullable();
            $table->string('id_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faculties');
    }
};
