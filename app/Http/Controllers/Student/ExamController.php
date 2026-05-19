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

        // توليد الأسئلة العشوائية (سحب ذكي مع التعويض)
        $questions = collect();
        foreach ($exam->subjectConfigs as $config) {
            $neededTotal = $config->question_count;
            $diffsRaw = $config->difficulties ?? [];
            $diffs = [
                'easy'   => isset($diffsRaw['easy']) && is_array($diffsRaw['easy']) ? (int)($diffsRaw['easy']['count'] ?? 0) : (int)($diffsRaw['easy'] ?? 0),
                'medium' => isset($diffsRaw['medium']) && is_array($diffsRaw['medium']) ? (int)($diffsRaw['medium']['count'] ?? 0) : (int)($diffsRaw['medium'] ?? 0),
                'hard'   => isset($diffsRaw['hard']) && is_array($diffsRaw['hard']) ? (int)($diffsRaw['hard']['count'] ?? 0) : (int)($diffsRaw['hard'] ?? 0),
            ];
            $types = $config->types ?? ['mcq' => 0, 'tf' => 0, 'matching' => 0, 'essay' => 0];

            $subjectQuestions = \App\Models\Question::where('subject_id', $config->subject_id)
                ->where('grade_id', $exam->grade_id)
                ->with('choices')
                ->inRandomOrder()
                ->get();

            $pickedForConfig = collect();
            $neededDiffs = $diffs;
            $neededTypes = $types;
            $rem = $subjectQuestions;

            // المحاولة 1: تطابق تام للصعوبة والنوع
            foreach ($rem as $k => $q) {
                if ($pickedForConfig->count() >= $neededTotal) break;
                if (($neededDiffs[$q->difficulty] ?? 0) > 0 && ($neededTypes[$q->type] ?? 0) > 0) {
                    $pickedForConfig->push($q);
                    $neededDiffs[$q->difficulty]--;
                    $neededTypes[$q->type]--;
                    $rem->forget($k);
                }
            }

            // المحاولة 2: تطابق النوع فقط
            foreach ($rem as $k => $q) {
                if ($pickedForConfig->count() >= $neededTotal) break;
                if (($neededTypes[$q->type] ?? 0) > 0) {
                    $pickedForConfig->push($q);
                    $neededTypes[$q->type]--;
                    $rem->forget($k);
                }
            }

            // المحاولة 3: تطابق الصعوبة فقط
            foreach ($rem as $k => $q) {
                if ($pickedForConfig->count() >= $neededTotal) break;
                if (($neededDiffs[$q->difficulty] ?? 0) > 0) {
                    $pickedForConfig->push($q);
                    $neededDiffs[$q->difficulty]--;
                    $rem->forget($k);
                }
            }

            // المحاولة 4: إكمال النقص (التعويض) عشوائياً
            foreach ($rem as $k => $q) {
                if ($pickedForConfig->count() >= $neededTotal) break;
                $pickedForConfig->push($q);
            }

            $questions = $questions->merge($pickedForConfig);
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

        $exam    = Exam::with('subjectConfigs')->findOrFail($session['exam_id']);
        $student = Student::findOrFail($session['student_id']);
        $qIds    = $session['question_ids'];

        $totalQuestions  = count($qIds);
        $configsMap = $exam->subjectConfigs->keyBy('subject_id');

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
            $questionObj = \App\Models\Question::with('choices')->findOrFail($qId);
            $qType       = $questionObj->type;
            
            // استخراج درجة السؤال من الإعدادات الخاصة بمادته (دعم الدرجة الموحدة أو الدرجات الموزعة حسب الصعوبة)
            $config = $configsMap[$questionObj->subject_id] ?? null;
            $markPerQuestion = 1;
            if ($config) {
                $diffs = $config->difficulties ?? [];
                $qDiff = $questionObj->difficulty; // 'easy', 'medium', 'hard'
                if (isset($diffs[$qDiff]) && is_array($diffs[$qDiff]) && isset($diffs[$qDiff]['marks'])) {
                    $markPerQuestion = (int)$diffs[$qDiff]['marks'];
                } else {
                    $markPerQuestion = (int)$config->marks_per_question;
                }
            }
            
            $isCorrect  = false;
            $chosenId   = null;
            $answerText = null;

            if ($qType === 'essay') {
                $essayInput = $request->input("answers.{$qId}");
                $answerText = $essayInput;
                
                $modelChoice = $questionObj->choices->first();
                $modelAnswer = $modelChoice ? $modelChoice->text : '';
                
                if (!empty($essayInput) && !empty($modelAnswer)) {
                    $cleanInput = preg_replace('/[^\p{L}\p{N}\s]/u', '', mb_strtolower($essayInput));
                    $cleanModel = preg_replace('/[^\p{L}\p{N}\s]/u', '', mb_strtolower($modelAnswer));
                    
                    $inputWords = array_filter(explode(' ', $cleanInput));
                    $modelWords = array_filter(explode(' ', $cleanModel));
                    
                    $intersection = array_intersect($inputWords, $modelWords);
                    
                    if (count($modelWords) > 0) {
                        $matchRatio = count($intersection) / count($modelWords);
                        if ($matchRatio >= 0.4) {
                            $isCorrect = true;
                        }
                    }
                }
            } elseif ($qType === 'matching') {
                $matchingAnswers = $request->input("answers.{$qId}", []);
                $answerText      = json_encode($matchingAnswers, JSON_UNESCAPED_UNICODE);
                
                if (is_array($matchingAnswers) && count($matchingAnswers) > 0) {
                    $perfectMatch = true;
                    $matchedCount = 0;
                    
                    foreach ($questionObj->choices as $choice) {
                        $parts    = explode('|', $choice->text);
                        $leftText = trim($parts[1] ?? '');
                        
                        $studentSelectedLeft = trim($matchingAnswers[$choice->id] ?? '');
                        
                        if ($studentSelectedLeft === $leftText) {
                            $matchedCount++;
                        } else {
                            $perfectMatch = false;
                        }
                    }
                    
                    if ($perfectMatch && $matchedCount === count($questionObj->choices)) {
                        $isCorrect = true;
                    }
                }
            } else {
                $chosenId = $request->input("answers.{$qId}");
                if ($chosenId) {
                    $choice = \App\Models\Choice::where('id', $chosenId)
                        ->where('question_id', $qId)
                        ->where('is_correct', true)
                        ->exists();
                    $isCorrect = $choice;
                }
            }

            if ($isCorrect) {
                $correctCount++;
                $totalScore += $markPerQuestion;
            }

            StudentAnswer::create([
                'student_exam_id'  => $studentExam->id,
                'question_id'      => $qId,
                'chosen_choice_id' => $chosenId ?: null,
                'answer_text'      => $answerText,
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
