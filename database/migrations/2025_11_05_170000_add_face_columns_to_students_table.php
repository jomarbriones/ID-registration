<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'face_embedding')) {
                $table->longText('face_embedding')->nullable()->after('picture_path');
            }
            if (!Schema::hasColumn('students', 'face_embedding_updated_at')) {
                $table->timestamp('face_embedding_updated_at')->nullable()->after('face_embedding');
            }
            if (!Schema::hasColumn('students', 'face_last_similarity')) {
                $table->decimal('face_last_similarity', 6, 4)->nullable()->after('face_embedding_updated_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'face_last_similarity')) {
                $table->dropColumn('face_last_similarity');
            }
            if (Schema::hasColumn('students', 'face_embedding_updated_at')) {
                $table->dropColumn('face_embedding_updated_at');
            }
            if (Schema::hasColumn('students', 'face_embedding')) {
                $table->dropColumn('face_embedding');
            }
        });
    }
};
