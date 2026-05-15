<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // بيانات الطالب المتقدم
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('applying_grade_id')->constrained('grades'); // الصف المتقدم إليه
            $table->string('previous_school');
            $table->decimal('last_grade_average', 5, 2); // المعدل
            $table->string('guardian_name');
            $table->string('guardian_phone', 20);
            $table->timestamps();
        });

        // سجل تقديم الطالب للاختبار (النتائج)
        Schema::create('student_exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('exam_id')->constrained('exams')->cascadeOnDelete();
            $table->string('result_token')->unique(); // رمز فريد لصفحة النتيجة
            $table->unsignedInteger('score')->default(0);       // الدرجة المحققة
            $table->unsignedInteger('total_marks');             // الدرجة الكلية
            $table->unsignedInteger('pass_marks');              // درجة النجاح
            $table->unsignedInteger('correct_answers')->default(0);
            $table->unsignedInteger('total_questions')->default(0);
            $table->enum('status', ['pass', 'fail'])->default('fail');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });

        // سجل إجابات الطالب التفصيلي
        Schema::create('student_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_exam_id')->constrained('student_exams')->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('questions')->cascadeOnDelete();
            $table->foreignId('chosen_choice_id')->nullable()->constrained('choices')->nullOnDelete();
            $table->boolean('is_correct')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_answers');
        Schema::dropIfExists('student_exams');
        Schema::dropIfExists('students');
    }
};
