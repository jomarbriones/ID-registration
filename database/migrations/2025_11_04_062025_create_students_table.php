<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::table('students', function (Blueprint $table) {
        // add nullable timestamps if they don't exist
        if (!Schema::hasColumn('students', 'created_at')) {
            $table->timestamp('created_at')->nullable()->after('status');
        }
        if (!Schema::hasColumn('students', 'updated_at')) {
            $table->timestamp('updated_at')->nullable()->after('created_at');
        }
    });
}

   
    public function down()
{
    Schema::table('students', function (Blueprint $table) {
        if (Schema::hasColumn('students', 'updated_at')) {
            $table->dropColumn('updated_at');
        }
        if (Schema::hasColumn('students', 'created_at')) {
            $table->dropColumn('created_at');
        }
    });
}
}