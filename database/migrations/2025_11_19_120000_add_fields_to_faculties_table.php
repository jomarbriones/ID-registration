<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('faculties', function (Blueprint $table) {
            $table->string('address')->nullable()->after('blood_type');
            $table->string('emergency_contact_address')->nullable()->after('address');
            $table->string('tin_number', 32)->nullable()->after('sss_number');
            $table->string('photo_path')->nullable()->after('birthday');
            $table->string('id_path')->nullable()->after('photo_path');
        });
    }

    public function down(): void
    {
        Schema::table('faculties', function (Blueprint $table) {
            $table->dropColumn([
                'address',
                'emergency_contact_address',
                'tin_number',
                'photo_path',
                'id_path',
            ]);
        });
    }
};
