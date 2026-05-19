<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->integer('duration_minutes')->default(60)->after('pass_marks');
        });

        Schema::table('exam_subject_configs', function (Blueprint $table) {
            $table->integer('marks_per_question')->default(1)->after('question_count');
            $table->json('difficulties')->nullable()->after('marks_per_question');
            $table->json('types')->nullable()->after('difficulties');
        });
    }

    public function down(): void
    {
        Schema::table('exam_subject_configs', function (Blueprint $table) {
            $table->dropColumn(['marks_per_question', 'difficulties', 'types']);
        });

        Schema::table('exams', function (Blueprint $table) {
            $table->dropColumn('duration_minutes');
        });
    }
};
