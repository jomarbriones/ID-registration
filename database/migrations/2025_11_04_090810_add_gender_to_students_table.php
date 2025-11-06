<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('students', function (Blueprint $table) {
        if (!Schema::hasColumn('students', 'gender')) {
            $table->char('gender', 1)->nullable()->after('guardian_contact');
        }
    });
}

public function down()
{
    Schema::table('students', function (Blueprint $table) {
        if (Schema::hasColumn('students', 'gender')) {
            $table->dropColumn('gender');
        }
    });
}
};
