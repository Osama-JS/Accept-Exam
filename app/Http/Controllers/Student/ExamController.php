<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Student;
use App\Models\StudentAnswer;
use App\Models\StudentExam;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ExamController extends Controller
{
    // صفحة إدخال بيانات الطالب
    public function register(Exam $exam)
    {
        abort_if(!$exam->is_active, 403, 'هذا الاختبار غير متاح حالياً.');
        $exam->load('grade', 'subjectConfigs');
        return view('student.exam.register', compact('exam'));
    }

    // بدء الاختبار وحفظ بيانات الطالب
    public function start(Request $request, Exam $exam)
    {
        abort_if(!$exam->is_active, 403, 'هذا الاختبار غير متاح حالياً.');

        $data = $request->validate([
            'name'               => 'required|string|max:100',
            'previous_school'    => 'required|string|max:200',
            'last_grade_average' => 'required|numeric|min:0|max:100',
            'guardian_name'      => 'required|string|max:100',
            'guardian_phone'     => 'required|string|max:20',
        ], [
            'name.required'               => 'اسم الطالب مطلوب.',
            'previous_school.required'    => 'اسم المدرسة السابقة مطلوب.',
            'last_grade_average.required' => 'المعدل مطلوب.',
            'last_grade_average.numeric'  => 'المعدل يجب أن يكون رقماً.',
            'guardian_name.required'      => 'اسم ولي الأمر مطلوب.',
            'guardian_phone.required'     => 'رقم هاتف ولي الأمر مطلوب.',
        ]);

        // إنشاء بيانات الطالب
        $student = Student::create([
            'name'               => $data['name'],
            'applying_grade_id'  => $exam->grade_id,
            'previous_school'    => $data['previous_school'],
            'last_grade_average' => $data['last_grade_average'],
            'guardian_name'      => $data['guardian_name'],
            'guardian_phone'     => $data['guardian_phone'],
        ]);

        // توليد الأسئلة العشوائية
        $questions = collect();
        foreach ($exam->subjectConfigs as $config) {
            $picked = $config->subject->questions()
                ->inRandomOrder()
                ->take($config->question_count)
                ->with('choices')
                ->get();
            $questions = $questions->merge($picked);
        }
        $questions = $questions->shuffle();

        // حفظ بيانات الجلسة
        session([
            'exam_session' => [
                'exam_id'     => $exam->id,
                'student_id'  => $student->id,
                'question_ids'=> $questions->pluck('id')->toArray(),
                'started_at'  => now()->toISOString(),
            ],
        ]);

        // حفظ الأسئلة في الجلسة لعرضها
        session(['exam_questions' => $questions->toArray()]);

        return redirect()->route('exam.take');
    }

    // صفحة الاختبار
    public function take()
    {
        $session = session('exam_session');
        if (!$session) {
            return redirect()->route('home')->with('error', 'انتهت جلسة الاختبار. يرجى البدء من جديد.');
        }

        $exam      = Exam::findOrFail($session['exam_id']);
        $questions = collect(session('exam_questions'));

        return view('student.exam.take', compact('exam', 'questions'));
    }

    // تسليم الاختبار
    public function submit(Request $request)
    {
        $session = session('exam_session');
        if (!$session) {
            return redirect()->route('home')->with('error', 'انتهت جلسة الاختبار.');
        }

        $exam    = Exam::findOrFail($session['exam_id']);
        $student = Student::findOrFail($session['student_id']);
        $qIds    = $session['question_ids'];

        $totalQuestions  = count($qIds);
        $markPerQuestion = $totalQuestions > 0 ? $exam->total_marks / $totalQuestions : 0;

        // إنشاء سجل الاختبار
        $token       = Str::uuid()->toString();
        $studentExam = StudentExam::create([
            'student_id'      => $student->id,
            'exam_id'         => $exam->id,
            'result_token'    => $token,
            'score'           => 0,
            'total_marks'     => $exam->total_marks,
            'pass_marks'      => $exam->pass_marks,
            'total_questions' => $totalQuestions,
            'started_at'      => $session['started_at'],
            'submitted_at'    => now(),
        ]);

        // تصحيح الإجابات
        $correctCount = 0;
        $totalScore   = 0;

        foreach ($qIds as $qId) {
            $chosenId  = $request->input("answers.{$qId}");
            $isCorrect = false;

            if ($chosenId) {
                $choice    = \App\Models\Choice::where('id', $chosenId)
                    ->where('question_id', $qId)
                    ->where('is_correct', true)
                    ->exists();
                $isCorrect = $choice;
            }

            if ($isCorrect) {
                $correctCount++;
                $totalScore += $markPerQuestion;
            }

            StudentAnswer::create([
                'student_exam_id'  => $studentExam->id,
                'question_id'      => $qId,
                'chosen_choice_id' => $chosenId ?: null,
                'is_correct'       => $isCorrect,
            ]);
        }

        $finalScore = round($totalScore);
        $status     = $finalScore >= $exam->pass_marks ? 'pass' : 'fail';

        $studentExam->update([
            'score'           => $finalScore,
            'correct_answers' => $correctCount,
            'status'          => $status,
        ]);

        // مسح جلسة الاختبار
        session()->forget(['exam_session', 'exam_questions']);

        return redirect()->route('exam.result', $token);
    }

    // صفحة النتيجة
    public function result(string $token)
    {
        $studentExam = StudentExam::where('result_token', $token)
            ->with(['student', 'exam.grade', 'exam.academicYear'])
            ->firstOrFail();

        return view('student.exam.result', compact('studentExam'));
    }
}
