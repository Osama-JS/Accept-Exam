<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained('academic_years');
            $table->foreignId('grade_id')->constrained('grades'); // الصف المستهدف
            $table->string('title');
            $table->unsignedInteger('total_marks');   // الدرجة الكلية
            $table->unsignedInteger('pass_marks');    // درجة النجاح
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // إعدادات مصادر الأسئلة لكل اختبار
        Schema::create('exam_subject_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->unsignedInteger('question_count'); // عدد الأسئلة المطلوب سحبها
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_subject_configs');
        Schema::dropIfExists('exams');
    }
};
