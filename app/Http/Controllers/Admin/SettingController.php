<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            'school_name'         => Setting::get('school_name', 'مدرسة التميز الذكية'),
            'system_enabled'      => Setting::get('system_enabled', '1'),
            'school_logo'         => Setting::get('school_logo'),
            'home_hero_image'     => Setting::get('home_hero_image'),
            'contact_email'       => Setting::get('contact_email'),
            'contact_phone'       => Setting::get('contact_phone'),
            'welcome_message'     => Setting::get('welcome_message', 'مرحباً بك في نظام امتحانات القبول'),
            'show_results_instantly' => Setting::get('show_results_instantly', '1'),
            
            // About Us Settings
            'about_description'    => Setting::get('about_description', 'مؤسسة تعليمية رائدة تكرس جهودها لبناء جيل مسلح بالعلم، ومستمسك بالقيم الأخلاقية، وقادر على الابتكار والتميز والمنافسة محلياً وعالمياً.'),
            'about_vision'         => Setting::get('about_vision', 'الريادة في تقديم تعليم إبداعي ذي جودة عالية يدمج بين القيم والتميز الأكاديمي، لنكون الخيار الأول في صناعة قادة المستقبل وتنمية مجتمع المعرفة.'),
            'about_mission'        => Setting::get('about_mission', 'توفير بيئة تعليمية ذكية، ورعاية طلابية شاملة تعزز التفكير النقدي والابتكار وتصقل المواهب، من خلال كادر أكاديمي محترف وشراكة مجتمعية فاعلة ومناهج مواكبة.'),
            'about_values'         => Setting::get('about_values', 'القيم هي أساس هويتنا، ونلتزم بغرسها وتنميتها في نفوس طلابنا: التميز الأكاديمي، النزاهة والأمانة، الابتكار المستمر، الاحترام المتبادل، والمسؤولية المجتمعية.'),
            'about_history'        => Setting::get('about_history', 'تأسست مدارس القيم الأهلية لتقدم نموذجاً تعليمياً متكاملاً يتجاوز مجرد تلقين المعلومات إلى بناء الإنسان. نؤمن بأن المعرفة الحقيقية هي تلك التي ترافقها قيم نبيلة تحكم السلوك وتوجه القدرات نحو البناء والعطاء. نعتمد في مدارسنا على أحدث الوسائل التكنولوجية والمنظومات الرقمية في التعليم وإدارة التقييمات، كما نسعى باستمرار لتطوير مهارات القرن الحادي والعشرين لدى طلابنا عبر المناهج الأكاديمية والأنشطة اللامنهجية الإبداعية.'),
            'about_stat_years'     => Setting::get('about_stat_years', '+15'),
            'about_stat_graduates' => Setting::get('about_stat_graduates', '+2,500'),
            'about_stat_teachers'  => Setting::get('about_stat_teachers', '+120'),
            
            // Why Choose Us settings
            'about_features_intro' => Setting::get('about_features_intro', 'نقدم بيئة ومقومات تعليمية استثنائية تجعلنا الخيار الأمثل لمستقبل أبنائكم الأكاديمي والمهني'),
            'about_feature1_title' => Setting::get('about_feature1_title', 'كادر أكاديمي نخبوي'),
            'about_feature1_desc'  => Setting::get('about_feature1_desc', 'نخبة من المعلمين والتربويين ذوي الخبرة العالية والمؤهلين لتوجيه الطلاب وتنمية مهارات التفكير العليا والابتكار.'),
            'about_feature2_title' => Setting::get('about_feature2_title', 'بيئة تعليمية ذكية'),
            'about_feature2_desc'  => Setting::get('about_feature2_desc', 'فصول مجهزة بأحدث التقنيات التفاعلية، ومختبرات علمية وحاسوبية متكاملة، ونظام إلكتروني شامل لإدارة القبول والامتحانات.'),
            'about_feature3_title' => Setting::get('about_feature3_title', 'مناهج ريادية تفاعلية'),
            'about_feature3_desc'  => Setting::get('about_feature3_desc', 'نقدم خططاً دراسية متميزة ومساندة تعزز الفهم والتجربة والبحث العملي، وتركز على بناء القدرات العلمية والتحليلية.'),
            'about_feature4_title' => Setting::get('about_feature4_title', 'رعاية ومتابعة شاملة'),
            'about_feature4_desc'  => Setting::get('about_feature4_desc', 'رعاية سلوكية وصحية متكاملة، وقنوات اتصال مباشرة ومستمرة مع أولياء الأمور لمتابعة مستويات أداء الطلاب ودعم مسيرتهم.'),
            
            // Journey Steps Settings
            'steps_section_title'  => Setting::get('steps_section_title', 'خطوات رحلة القبول الإلكتروني'),
            'steps_section_desc'   => Setting::get('steps_section_desc', 'قمنا بتبسيط وتأمين خطوات التقديم لتضمن مقعدك الدراسي في بضع دقائق ومن أي جهاز'),
            'step1_icon'           => Setting::get('step1_icon', '🎓'),
            'step1_title'          => Setting::get('step1_title', '1. اختر الصف الدراسي'),
            'step1_desc'           => Setting::get('step1_desc', 'تصفح الصفوف الأكاديمية النشطة بالأسفل، وحدد الصف الدراسي الذي ترغب في التسجيل والالتحاق به.'),
            'step2_icon'           => Setting::get('step2_icon', '📋'),
            'step2_title'          => Setting::get('step2_title', '2. سجل بياناتك الأساسية'),
            'step2_desc'           => Setting::get('step2_desc', 'أدخل اسم الطالب رباعياً، ورقم هاتف ولي الأمر لتأمين طلب الالتحاق وإثبات الحضور والجلسة.'),
            'step3_icon'           => Setting::get('step3_icon', '⏱️'),
            'step3_title'          => Setting::get('step3_title', '3. أدّ اختبار القبول'),
            'step3_desc'           => Setting::get('step3_desc', 'ابدأ الإجابة عن الأسئلة التفاعلية المتنوعة بكل سهولة وبشكل مؤمن داخل الوقت الزمني المخصص.'),
            'step4_icon'           => Setting::get('step4_icon', '🏆'),
            'step4_title'          => Setting::get('step4_title', '4. احصل على نتيجتك'),
            'step4_desc'           => Setting::get('step4_desc', 'فور الانتهاء من تأدية الامتحان، يصدر النظام تقرير أداء فوري ومؤشرات النجاح والقبول للطلب.'),
            
            // FAQ Settings
            'faq_section_title'    => Setting::get('faq_section_title', 'الأسئلة الشائعة حول القبول'),
            'faq_section_desc'     => Setting::get('faq_section_desc', 'كل ما تود معرفته عن امتحانات القبول الإلكترونية بمدارس القيم الأهلية'),
            'faq1_question'        => Setting::get('faq1_question', 'ما هي مدة الاختبار المحددة لقبول الطلاب؟'),
            'faq1_answer'          => Setting::get('faq1_answer', 'تختلف مدة الاختبار حسب الصف الدراسي، وتتراوح عادةً بين 30 دقيقة إلى 60 دقيقة. يعرض النظام مؤقتاً تنازلياً دقيقاً في أعلى شاشة الامتحان لتنبيه الطالب.'),
            'faq2_question'        => Setting::get('faq2_question', 'هل تظهر نتيجة الاختبار للطالب فور الانتهاء؟'),
            'faq2_answer'          => Setting::get('faq2_answer', 'نعم، تظهر النتيجة وتفاصيل الأداء التقييمي للطالب بشكل فوري وتلقائي بمجرد النقر على زر "تسليم الإجابات"، مع إمكانية طباعة التقرير أو الاحتفاظ بالرابط.'),
            'faq3_question'        => Setting::get('faq3_question', 'ماذا يحدث إذا انقطع الاتصال بالإنترنت أثناء تأدية الامتحان؟'),
            'faq3_answer'          => Setting::get('faq3_answer', 'يقوم النظام بحفظ آخر إجابات مدخلة للطالب بشكل تلقائي. وفي حال انقطاع الخدمة، يرجى إعادة تحميل الصفحة بعد التحقق من الشبكة لمواصلة حل الامتحان من حيث توقفت.'),
            'faqs'                 => []
        ];

        $faqsJson = Setting::get('faqs');
        if ($faqsJson) {
            $settings['faqs'] = json_decode($faqsJson, true);
        } else {
            $settings['faqs'] = [
                [
                    'question' => $settings['faq1_question'],
                    'answer' => $settings['faq1_answer'],
                ],
                [
                    'question' => $settings['faq2_question'],
                    'answer' => $settings['faq2_answer'],
                ],
                [
                    'question' => $settings['faq3_question'],
                    'answer' => $settings['faq3_answer'],
                ],
            ];
        }

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'school_name'     => 'required|string|max:200',
            'system_enabled'  => 'required|boolean',
            'school_logo'     => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'home_hero_image' => 'nullable|image|mimes:jpeg,png,jpg,svg,webp|max:4096',
            'contact_email'   => 'nullable|email',
            'contact_phone'   => 'nullable|string',
            'welcome_message' => 'nullable|string',
            'show_results_instantly' => 'required|boolean',
            
            // About Us validation
            'about_description'    => 'nullable|string',
            'about_vision'         => 'nullable|string',
            'about_mission'        => 'nullable|string',
            'about_values'         => 'nullable|string',
            'about_history'        => 'nullable|string',
            'about_stat_years'     => 'nullable|string|max:50',
            'about_stat_graduates' => 'nullable|string|max:50',
            'about_stat_teachers'  => 'nullable|string|max:50',
            
            // Why Choose Us validation
            'about_features_intro' => 'nullable|string',
            'about_feature1_title' => 'nullable|string|max:200',
            'about_feature1_desc'  => 'nullable|string',
            'about_feature2_title' => 'nullable|string|max:200',
            'about_feature2_desc'  => 'nullable|string',
            'about_feature3_title' => 'nullable|string|max:200',
            'about_feature3_desc'  => 'nullable|string',
            'about_feature4_title' => 'nullable|string|max:200',
            'about_feature4_desc'  => 'nullable|string',
            
            // Journey Steps validation
            'steps_section_title'  => 'nullable|string|max:200',
            'steps_section_desc'   => 'nullable|string',
            'step1_icon'           => 'nullable|string|max:50',
            'step1_title'          => 'nullable|string|max:200',
            'step1_desc'           => 'nullable|string',
            'step2_icon'           => 'nullable|string|max:50',
            'step2_title'          => 'nullable|string|max:200',
            'step2_desc'           => 'nullable|string',
            'step3_icon'           => 'nullable|string|max:50',
            'step3_title'          => 'nullable|string|max:200',
            'step3_desc'           => 'nullable|string',
            'step4_icon'           => 'nullable|string|max:50',
            'step4_title'          => 'nullable|string|max:200',
            'step4_desc'           => 'nullable|string',
            
            // FAQ validation
            'faq_section_title'    => 'nullable|string|max:200',
            'faq_section_desc'     => 'nullable|string',
            'faqs'                 => 'nullable|array',
            'faqs.*.question'      => 'required|string|max:255',
            'faqs.*.answer'        => 'required|string',
        ]);

        Setting::set('school_name', $request->school_name);
        Setting::set('system_enabled', $request->system_enabled);
        Setting::set('contact_email', $request->contact_email);
        Setting::set('contact_phone', $request->contact_phone);
        Setting::set('welcome_message', $request->welcome_message);
        Setting::set('show_results_instantly', $request->show_results_instantly);
        
        // Save About Us settings
        Setting::set('about_description', $request->about_description);
        Setting::set('about_vision', $request->about_vision);
        Setting::set('about_mission', $request->about_mission);
        Setting::set('about_values', $request->about_values);
        Setting::set('about_history', $request->about_history);
        Setting::set('about_stat_years', $request->about_stat_years);
        Setting::set('about_stat_graduates', $request->about_stat_graduates);
        Setting::set('about_stat_teachers', $request->about_stat_teachers);
        
        // Save Why Choose Us settings
        Setting::set('about_features_intro', $request->about_features_intro);
        Setting::set('about_feature1_title', $request->about_feature1_title);
        Setting::set('about_feature1_desc', $request->about_feature1_desc);
        Setting::set('about_feature2_title', $request->about_feature2_title);
        Setting::set('about_feature2_desc', $request->about_feature2_desc);
        Setting::set('about_feature3_title', $request->about_feature3_title);
        Setting::set('about_feature3_desc', $request->about_feature3_desc);
        Setting::set('about_feature4_title', $request->about_feature4_title);
        Setting::set('about_feature4_desc', $request->about_feature4_desc);
        
        // Save Journey Steps settings
        Setting::set('steps_section_title', $request->steps_section_title);
        Setting::set('steps_section_desc', $request->steps_section_desc);
        Setting::set('step1_icon', $request->step1_icon);
        Setting::set('step1_title', $request->step1_title);
        Setting::set('step1_desc', $request->step1_desc);
        Setting::set('step2_icon', $request->step2_icon);
        Setting::set('step2_title', $request->step2_title);
        Setting::set('step2_desc', $request->step2_desc);
        Setting::set('step3_icon', $request->step3_icon);
        Setting::set('step3_title', $request->step3_title);
        Setting::set('step3_desc', $request->step3_desc);
        Setting::set('step4_icon', $request->step4_icon);
        Setting::set('step4_title', $request->step4_title);
        Setting::set('step4_desc', $request->step4_desc);
        
        // Save FAQ settings
        Setting::set('faq_section_title', $request->faq_section_title);
        Setting::set('faq_section_desc', $request->faq_section_desc);

        $faqs = [];
        if ($request->has('faqs') && is_array($request->faqs)) {
            foreach ($request->faqs as $item) {
                if (!empty($item['question']) && !empty($item['answer'])) {
                    $faqs[] = [
                        'question' => trim($item['question']),
                        'answer' => trim($item['answer']),
                    ];
                }
            }
        }
        Setting::set('faqs', json_encode($faqs, JSON_UNESCAPED_UNICODE));

        // Save legacy columns for fallback and backwards compatibility
        Setting::set('faq1_question', $faqs[0]['question'] ?? '');
        Setting::set('faq1_answer', $faqs[0]['answer'] ?? '');
        Setting::set('faq2_question', $faqs[1]['question'] ?? '');
        Setting::set('faq2_answer', $faqs[1]['answer'] ?? '');
        Setting::set('faq3_question', $faqs[2]['question'] ?? '');
        Setting::set('faq3_answer', $faqs[2]['answer'] ?? '');

        if ($request->hasFile('school_logo')) {
            // Delete old logo if exists
            $oldLogo = Setting::get('school_logo');
            if ($oldLogo) {
                Storage::disk('public')->delete($oldLogo);
            }

            $path = $request->file('school_logo')->store('settings', 'public');
            Setting::set('school_logo', $path);
        }

        if ($request->hasFile('home_hero_image')) {
            // Delete old hero if exists
            $oldHero = Setting::get('home_hero_image');
            if ($oldHero) {
                Storage::disk('public')->delete($oldHero);
            }

            $path = $request->file('home_hero_image')->store('settings', 'public');
            Setting::set('home_hero_image', $path);
        }

        return back()->with('success', 'تم تحديث إعدادات النظام بنجاح.');
    }
}
