<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\AcademicYear;
use App\Models\Grade;
use App\Models\Setting;
use App\Models\Subject;
use App\Models\Question;
use App\Models\Choice;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Admin ───
        $this->call(AdminSeeder::class);

        // ─── Settings ───
        $this->call(SettingSeeder::class);

        // ─── Academic Years ───
        $year = AcademicYear::create(['name' => '2024-2025', 'is_current' => true]);
        AcademicYear::create(['name' => '2023-2024', 'is_current' => false]);

        Setting::set('current_academic_year_id', $year->id);

        // ─── Grades ───
        $gradesData = [
            ['name' => 'الصف الأول الابتدائي',   'order' => 1],
            ['name' => 'الصف الثاني الابتدائي',  'order' => 2],
            ['name' => 'الصف الثالث الابتدائي',  'order' => 3],
            ['name' => 'الصف الرابع الابتدائي',  'order' => 4],
            ['name' => 'الصف الخامس الابتدائي',  'order' => 5],
            ['name' => 'الصف السادس الابتدائي',  'order' => 6],
            ['name' => 'الصف الأول المتوسط',     'order' => 7],
            ['name' => 'الصف الثاني المتوسط',    'order' => 8],
            ['name' => 'الصف الثالث المتوسط',    'order' => 9],
            ['name' => 'الصف الأول الثانوي',     'order' => 10],
            ['name' => 'الصف الثاني الثانوي',    'order' => 11],
            ['name' => 'الصف الثالث الثانوي',    'order' => 12],
        ];

        $grades = collect($gradesData)->map(fn($g) => Grade::create($g));

        // ─── Subjects ───
        $subjectsData = [
            ['name' => 'الرياضيات', 'icon' => '🔢'],
            ['name' => 'اللغة العربية', 'icon' => '📖'],
            ['name' => 'العلوم', 'icon' => '🔬'],
        ];

        foreach ($subjectsData as $sd) {
            $subject = Subject::create([
                'name'     => $sd['name'],
                'icon'     => $sd['icon'],
            ]);
            $subject->grades()->attach($grades[0]->id);
        }

        // ─── Call Question Bank Seeder for Real Questions ───
        $this->call(QuestionBankSeeder::class);
    }
}
