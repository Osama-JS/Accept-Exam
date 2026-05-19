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
        // 1. Create pivot table for grades and subjects (Many-to-Many)
        Schema::create('grade_subject', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grade_id')->constrained('grades')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->timestamps();
            
            $table->unique(['grade_id', 'subject_id']);
        });

        // 2. Add grade_id to questions table (Option A)
        Schema::table('questions', function (Blueprint $table) {
            $table->foreignId('grade_id')->after('id')->nullable()->constrained('grades')->cascadeOnDelete();
        });

        // Since it's a development environment, we can drop the old grade_id column from subjects.
        // First drop the foreign key, then the column.
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropForeign(['grade_id']);
            $table->dropColumn('grade_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->foreignId('grade_id')->nullable()->constrained('grades')->cascadeOnDelete();
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->dropForeign(['grade_id']);
            $table->dropColumn('grade_id');
        });

        Schema::dropIfExists('grade_subject');
    }
};
