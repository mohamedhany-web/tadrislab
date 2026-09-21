<?php

namespace Database\Seeders;

use App\Models\LearningPath;
use App\Models\LearningPathLesson;
use App\Models\LearningPathPractice;
use App\Models\LearningPathUnit;
use App\Models\TeacherTool;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * محتوى جاهز لمسارَين أساسيين — بلغة الممارس التربوي (معلم)، لا «طالب لغة».
 * الدورة: تشخيص → وصول (ممارسة/أداة) → تطوير → قياس أثر في الصف.
 */
class TadrisPathContentSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::hasTable('learning_paths')) {
            $this->command?->warn('learning_paths missing — skip.');

            return;
        }

        DB::transaction(function () {
            $this->seedClassroomManagement();
            $this->seedLessonPlanning();
        });

        $this->command?->info('تم تعبئة محتوى: إدارة الصف + التخطيط للحصة (دروس وممارسات للمعلمين).');
    }

    private function seedClassroomManagement(): void
    {
        $path = LearningPath::query()->updateOrCreate(
            ['slug' => 'classroom-management'],
            [
                'title_ar' => 'إدارة الصف',
                'title_en' => 'Classroom Management',
                'skill_focus_ar' => 'قيادة مناخ الصف وبناء روتين آمن',
                'skill_focus_en' => 'Leading a safe, structured classroom climate',
                'summary_ar' => 'مسار عملي للمعلم يساعده يشخص مناخ الصف، يضع قواعد وروتينًا واضحًا، ويدير التفاعل والسلوك بهدوء وثقة.',
                'summary_en' => 'A practical path for teachers to diagnose classroom climate, set clear routines, and lead interactions calmly.',
                'description_ar' => "هذا المسار موجّه للمعلم والممارس التربوي داخل الصف.\n"
                    ."تركّز وحداته على: تشخيص ما يحدث فعليًا في الحصة، صياغة قواعد يفهمها المتعلمون، بناء روتين يومي، والاستجابة للتحديات السلوكية دون تصعيد.\n"
                    ."كل وحدة تنتهي بممارسة أو قائمة تحقق قابلة للتطبيق في حصتك القادمة.",
                'description_en' => 'Built for classroom teachers: diagnose climate, set routines, and respond to behaviour challenges without escalation.',
                'is_active' => true,
                'is_published' => true,
                'sort_order' => 1,
            ]
        );

        $units = [
            [
                'sort_order' => 1,
                'title_ar' => 'تشخيص مناخ الصف',
                'title_en' => 'Diagnosing classroom climate',
                'summary_ar' => 'اقرأ مؤشرات المناخ خلال أول دقائق من الحصة، وحدّد ما يحتاج تدخّلًا قبل وضع القواعد.',
                'summary_en' => 'Read early climate signals and decide what needs intervention before writing rules.',
                'estimated_minutes' => 45,
                'lessons' => [
                    [
                        'title_ar' => 'ماذا تلاحظ في أول 10 دقائق؟',
                        'title_en' => 'What do you notice in the first 10 minutes?',
                        'body_ar' => "<p>قبل أن تضع قواعد جديدة، راقب صفّك كممارس:</p>
<ul>
<li>كيف يدخل المتعلمون؟ هل هناك فوضى أم انتقال منظم؟</li>
<li>من يتحدث أولًا: المعلم أم المتعلمون؟</li>
<li>هل التعليمات مفهومة من أول مرة أم تتكرر؟</li>
<li>أين يضيع الوقت: تجهيز، انتقال، أو إدارة سلوك؟</li>
</ul>
<p><strong>تشخيص سريع:</strong> اكتب ثلاثة مؤشرات إيجابية وثلاثة تحديات ظهرت في حصتين متتاليتين. هذا أساس خطتك لإدارة الصف — لا انطباع عام.</p>",
                        'body_en' => '<p>Observe entry, talk patterns, clarity of instructions, and where time is lost. List 3 strengths and 3 challenges across two lessons.</p>',
                        'duration_minutes' => 12,
                        'is_preview' => true,
                    ],
                    [
                        'title_ar' => 'من «السيطرة» إلى قيادة مناخ آمن',
                        'title_en' => 'From control to leading a safe climate',
                        'body_ar' => "<p>إدارة الصف ليست صراخًا أو عقوبات متكررة. للمعلم المحترف تعني:</p>
<ul>
<li><strong>وضوح التوقعات</strong> قبل بدء النشاط</li>
<li><strong>اتساق الاستجابة</strong> بين الحصص</li>
<li><strong>علاقة مهنية</strong> تحفظ كرامة المتعلم</li>
</ul>
<p>اسأل نفسك: هل قواعدك مكتوبة بلغة يفهمها صفّك؟ وهل تطبق بنفس الطريقة كل يوم؟ الاتساق أهم من شدة العقوبة.</p>",
                        'body_en' => '<p>Professional classroom leadership = clear expectations, consistent responses, and dignity-preserving relationships.</p>',
                        'duration_minutes' => 14,
                        'is_preview' => false,
                    ],
                ],
                'practices' => [
                    [
                        'practice_type' => 'checklist',
                        'title_ar' => 'قائمة تحقق: تشخيص مناخ الحصة',
                        'title_en' => 'Checklist: classroom climate diagnosis',
                        'summary_ar' => 'طبّقها بعد حصتين — سجّل مؤشرات الدخول، التفاعل، والوقت الضائع.',
                        'body_ar' => "<p>ضع علامة ✓ أو ✗ بعد كل حصة:</p>
<ol>
<li>دخل المتعلمون خلال دقيقتين دون فوضى ظاهرة</li>
<li>وضحت هدف الحصة في جملة واحدة مفهومة</li>
<li>نجحت التعليمات من أول أو ثاني تكرار فقط</li>
<li>الانتقال بين الأنشطة استغرق أقل من دقيقة</li>
<li>استجبت لتحدي سلوكي بهدوء دون تصعيد عام</li>
<li>أغلقْت الحصة بروتين واضح (تلخيص / بطاقة خروج)</li>
</ol>
<p><strong>قياس:</strong> إن نقصت علامتان فأكثر في نفس المؤشر عبر حصتين، اجعله هدف تطويرك الأسبوعي.</p>",
                    ],
                ],
            ],
            [
                'sort_order' => 2,
                'title_ar' => 'قواعد وروتين يومي للحصة',
                'title_en' => 'Rules and daily lesson routines',
                'summary_ar' => 'صغ قواعد قليلة وواضحة، وابنِ روتين دخول وانتقال وخروج يوفّر وقت التعلم.',
                'summary_en' => 'Write few clear rules and build entry, transition, and exit routines that protect learning time.',
                'estimated_minutes' => 50,
                'lessons' => [
                    [
                        'title_ar' => 'صياغة قواعد يفهمها المتعلمون ويطبقونها',
                        'title_en' => 'Rules learners can understand and apply',
                        'body_ar' => "<p>أفضل القواعد للمعلم داخل الصف:</p>
<ul>
<li>عددها قليل (3–5) وليست لائحة طويلة</li>
<li>بصيغة إيجابية: «نستمع لمن يتحدث» بدل «ممنوع الكلام»</li>
<li>تُدرَّب عمليًا في أول أسبوع، لا تُعلّق فقط على الحائط</li>
</ul>
<p>شارك المتعلمين في صياغة مثال واحد على الأقل — يزيد الالتزام دون أن تفقد قيادتك المهنية للصف.</p>",
                        'body_en' => '<p>3–5 positively phrased rules, practised in week one—not only posted on the wall.</p>',
                        'duration_minutes' => 13,
                        'is_preview' => false,
                    ],
                    [
                        'title_ar' => 'روتين الدخول والانتقال والخروج',
                        'title_en' => 'Entry, transition, and exit routines',
                        'body_ar' => "<p>كل دقيقة روتين ناجحة = دقيقة تعلّم إضافية.</p>
<ul>
<li><strong>دخول:</strong> مهمة صامتة لمدة دقيقتين (سؤال، مراجعة، ترتيب دفاتر)</li>
<li><strong>انتقال:</strong> إشارة متفق عليها + عدّ تنازلي أو مؤقت مرئي</li>
<li><strong>خروج:</strong> تلخيص بجملة + جمع الأدوات بنفس الترتيب كل مرة</li>
</ul>
<p>درّب الروتين كمهارة صفية، تمامًا كما تدرّب مهارة محتوى. كرّره حتى يصبح عادة لدى المتعلمين.</p>",
                        'body_en' => '<p>Entry starter, signalled transitions, and a consistent exit close protect instructional minutes.</p>',
                        'duration_minutes' => 12,
                        'is_preview' => false,
                    ],
                ],
                'practices' => [
                    [
                        'practice_type' => 'template',
                        'title_ar' => 'قالب: قواعد الصف + روتين الأسبوع',
                        'title_en' => 'Template: class rules + weekly routine',
                        'summary_ar' => 'عبّئه لصف واحد هذا الأسبوع، ثم راجعه مع زميل أو مشرف.',
                        'body_ar' => "<p><strong>1) قواعد الصف (3–5):</strong></p>
<ol>
<li>…</li>
<li>…</li>
<li>…</li>
</ol>
<p><strong>2) روتين الدخول (دقيقتان):</strong> …</p>
<p><strong>3) إشارة الانتقال:</strong> …</p>
<p><strong>4) روتين الخروج:</strong> …</p>
<p><strong>5) كيف سأدرّب ذلك في أول ثلاث حصص؟</strong> …</p>
<p>بعد أسبوع: ما القاعدة التي التزم بها المتعلمون أكثر؟ وأي روتين ما زال يستهلك وقتًا؟</p>",
                    ],
                    [
                        'practice_type' => 'tool',
                        'title_ar' => 'أداة مرتبطة: قائمة تحقق إدارة الصف',
                        'title_en' => 'Linked tool: classroom management checklist',
                        'summary_ar' => 'استخدم قائمة التحقق من مكتبة الأدوات لمراجعة صفّك أسبوعيًا.',
                        'body_ar' => '<p>بعد تطبيق القالب، افتح أداة «قائمة تحقق لإدارة الصف» من مكتبتك وطبّقها على حصة كاملة. الهدف: قياس أثر القواعد والروتين — لا الاكتفاء بالنية.</p>',
                    ],
                ],
            ],
            [
                'sort_order' => 3,
                'title_ar' => 'التفاعل الإيجابي وإدارة السلوك',
                'title_en' => 'Positive interaction and behaviour leadership',
                'summary_ar' => 'استجب للتحديات بهدوء، وعزّز السلوك المرغوب، وقِس أثر تدخّلك في الحصص التالية.',
                'summary_en' => 'Respond calmly, reinforce desired behaviour, and measure impact in following lessons.',
                'estimated_minutes' => 55,
                'lessons' => [
                    [
                        'title_ar' => 'استجابة هادئة للتحديات السلوكية',
                        'title_en' => 'Calm responses to behaviour challenges',
                        'body_ar' => "<p>عند ظهور سلوك معطّل للتعلم:</p>
<ol>
<li>اقترب بهدوء — لا تحوّل المشهد إلى عرض أمام الصف</li>
<li>ذكّر بالتوقع أو القاعدة بجملة قصيرة</li>
<li>أعد توجيه المتعلم للمهمة فورًا</li>
<li>إن استمر: خطوة متدرجة متفق عليها مسبقًا (تغيير مقعد، حديث قصير بعد الحصة)</li>
</ol>
<p>تجنّب السخرية أو الجدال الطويل داخل الحصة؛ كلاهما يسرق زمن التعلم ويضعف مناخ الأمان.</p>",
                        'body_en' => '<p>Proximity, brief reminder, redirect to task, then a pre-agreed stepped response—avoid public escalation.</p>',
                        'duration_minutes' => 14,
                        'is_preview' => false,
                    ],
                    [
                        'title_ar' => 'تعزيز السلوك المرغوب وقياس الأثر',
                        'title_en' => 'Reinforce desired behaviour and measure impact',
                        'body_ar' => "<p>التعزيز الفعّال للمعلم:</p>
<ul>
<li>محدد: «شكرًا لانتقالك بسرعة عند الإشارة» بدل مدح عام</li>
<li>فوري قدر الإمكان</li>
<li>متناسب مع عمر المتعلمين وثقافة المدرسة</li>
</ul>
<p><strong>قياس التقدّم:</strong> اختر سلوكًا واحدًا تريد زيادته هذا الأسبوع، وعدّ مرات ظهوره في 3 حصص. الرقم أوضح من الشعور.</p>",
                        'body_en' => '<p>Specific, timely reinforcement plus a simple frequency count across three lessons.</p>',
                        'duration_minutes' => 12,
                        'is_preview' => false,
                    ],
                ],
                'practices' => [
                    [
                        'practice_type' => 'application',
                        'title_ar' => 'تحدٍّ تطبيقي: سيناريوهات صفية',
                        'title_en' => 'Applied challenge: classroom scenarios',
                        'summary_ar' => 'اختر سيناريو من صفّك الحقيقي وخطّط استجابتك قبل الحصة القادمة.',
                        'body_ar' => "<p>اختر موقفًا واحدًا واجهته مؤخرًا (مقاطعة متكررة، تأخّر دخول، جدال بين متعلمين…).</p>
<p>اكتب:</p>
<ol>
<li>ما التوقّع الذي سأوضحه قبل النشاط؟</li>
<li>ما جملتي الأولى عند ظهور السلوك؟</li>
<li>ما الخطوة التالية إن استمر؟</li>
<li>كيف سأعزّز من يلتزم؟</li>
</ol>
<p>طبّق الخطة في حصتين، ثم راجع: هل قلّ التكرار؟ هل حافظت على زمن التعلم؟</p>",
                    ],
                    [
                        'practice_type' => 'assessment',
                        'title_ar' => 'بطاقة خروج قصيرة لقياس مناخ الحصة',
                        'title_en' => 'Exit ticket to gauge lesson climate',
                        'summary_ar' => 'دقيقة في نهاية الحصة — تغذية راجعة من المتعلمين دون اختبار رسمي.',
                        'body_ar' => "<p>اطلب من المتعلمين إجابة بجملة أو إشارة:</p>
<ul>
<li>«أشعر أن الحصة كانت… (منظمة / مشوشة)»</li>
<li>«أكثر لحظة ساعدتني أتعلم اليوم…»</li>
</ul>
<p>اجمع 5–10 بطاقات عشوائيًا. إن تكرر وصف «مشوشة»، راجع روتين الانتقال قبل تغيير المحتوى.</p>",
                    ],
                ],
            ],
        ];

        $this->syncPathUnits($path, $units);
        $this->attachTools($path, ['checklists', 'classroom_tools']);
    }

    private function seedLessonPlanning(): void
    {
        $path = LearningPath::query()->updateOrCreate(
            ['slug' => 'lesson-planning'],
            [
                'title_ar' => 'التخطيط للحصة',
                'title_en' => 'Lesson Planning',
                'skill_focus_ar' => 'تصميم حصة قابلة للتنفيذ داخل الصف',
                'skill_focus_en' => 'Designing a teachable classroom lesson',
                'summary_ar' => 'مسار للمعلم يصمّم حصة واضحة: هدف تعلم، أنشطة مرتبطة، ومواد جاهزة قبل الدخول للصف — ثم يقيس هل تحقق الهدف.',
                'summary_en' => 'Help teachers design a clear lesson: learning goal, aligned activities, ready materials, then check goal attainment.',
                'description_ar' => "التخطيط هنا ليس ملء نموذج إداري؛ بل قرار مهني قبل الحصة:\n"
                    ."ماذا سيتعلم المتعلمون؟ بماذا سيتفاعلون؟ وكيف أعرف أن الهدف تحقق؟\n"
                    ."الوحدات تتبع منطق المعلم داخل الصف: أهداف → أنشطة → تجهيز → مراجعة قصيرة بعد التنفيذ.",
                'description_en' => 'Planning as professional decisions: learning goal, aligned activities, materials, and a short post-lesson check.',
                'is_active' => true,
                'is_published' => true,
                'sort_order' => 2,
            ]
        );

        $units = [
            [
                'sort_order' => 1,
                'title_ar' => 'أهداف تعلم واضحة للحصة',
                'title_en' => 'Clear lesson learning goals',
                'summary_ar' => 'صغ هدفًا واحدًا قابلاً للملاحظة بنهاية الحصة — بلغة المتعلم والمعلم معًا.',
                'summary_en' => 'Write one observable end-of-lesson goal in learner- and teacher-friendly language.',
                'estimated_minutes' => 40,
                'lessons' => [
                    [
                        'title_ar' => 'من موضوع الدرس إلى هدف تعلم للحصة',
                        'title_en' => 'From topic to a lesson learning goal',
                        'body_ar' => "<p>«نشرح الوحدة الثالثة» ليس هدفًا. هدف التعلم للمعلم يجيب:</p>
<ul>
<li>ماذا سيكون المتعلم قادرًا أن يفعل / يوضح / يحل بنهاية الحصة؟</li>
<li>كيف سألاحظ ذلك خلال الحصة أو في آخرها؟</li>
</ul>
<p><strong>صيغة مساعدة:</strong> بنهاية الحصة، يستطيع المتعلم أن ________ باستخدام ________.</p>
<p>مثال: «يستطيع المتعلم أن يفسّر قاعدة الصف بصياغته، ويذكر مثالًا على تطبيقها.»</p>",
                        'body_en' => '<p>Move from “cover the unit” to an observable end-of-lesson learner performance.</p>',
                        'duration_minutes' => 12,
                        'is_preview' => true,
                    ],
                    [
                        'title_ar' => 'هدف واحد… أم أهداف كثيرة تُضعِف الحصة؟',
                        'title_en' => 'One sharp goal vs too many goals',
                        'body_ar' => "<p>للحصة الواحدة: هدف رئيس واضح أفضل من قائمة أهداف لا يُكملها الزمن.</p>
<ul>
<li>هدف رئيس واحد</li>
<li>مؤشر نجاح بسيط (سؤال، مهمة، بطاقة خروج)</li>
<li>إن احتجت أهدافًا فرعية: اجعلها خطوات نحو الهدف الرئيس لا مسارات متفرقة</li>
</ul>
<p>اختبر الهدف: هل يمكن لمتعلم أن يشرح لك في 20 ثانية ماذا كان المطلوب اليوم؟</p>",
                        'body_en' => '<p>One primary goal plus a simple success check beats an overloaded objective list.</p>',
                        'duration_minutes' => 10,
                        'is_preview' => false,
                    ],
                ],
                'practices' => [
                    [
                        'practice_type' => 'template',
                        'title_ar' => 'قالب: هدف الحصة + مؤشر النجاح',
                        'title_en' => 'Template: lesson goal + success indicator',
                        'summary_ar' => 'عبّئه قبل حصتك القادمة وشاركه مع زميل إن أمكن.',
                        'body_ar' => "<p><strong>المادة / الصف:</strong> …</p>
<p><strong>موضوع الحصة:</strong> …</p>
<p><strong>هدف التعلم (جملة واحدة):</strong> بنهاية الحصة يستطيع المتعلم أن…</p>
<p><strong>مؤشر النجاح (كيف أعرف؟):</strong> …</p>
<p><strong>مفاهيم يجب ألا أُشتّت بها الهدف:</strong> …</p>
<p>بعد الحصة: هل تحقق المؤشر لـ 70% فأكثر من المتعلمين الحاضرين؟ نعم / لا — ولماذا؟</p>",
                    ],
                ],
            ],
            [
                'sort_order' => 2,
                'title_ar' => 'أنشطة تحقق هدف الحصة',
                'title_en' => 'Activities that serve the lesson goal',
                'summary_ar' => 'اربط التهيئة والنشاط الرئيس والتقويم القصير بنفس الهدف — لا أنشطة للزينة.',
                'summary_en' => 'Align starter, main task, and quick check to the same goal—no decorative activities.',
                'estimated_minutes' => 50,
                'lessons' => [
                    [
                        'title_ar' => 'تهيئة تفتح الحصة على الهدف',
                        'title_en' => 'A starter that opens toward the goal',
                        'body_ar' => "<p>التهيئة الجيدة للمعلم:</p>
<ul>
<li>قصيرة (3–7 دقائق)</li>
<li>تستدعي معرفة سابقة أو تثير سؤالًا مرتبطًا بالهدف</li>
<li>لا تسرق زمن النشاط الرئيس</li>
</ul>
<p>اسأل: إن حذفت هذه التهيئة، هل يضعف وصول المتعلمين للهدف؟ إن كانت الإجابة لا — استبدلها.</p>",
                        'body_en' => '<p>Short starters that activate prior knowledge toward the goal—cut anything that does not serve it.</p>',
                        'duration_minutes' => 11,
                        'is_preview' => false,
                    ],
                    [
                        'title_ar' => 'النشاط الرئيس والتقويم أثناء الحصة',
                        'title_en' => 'Main task and in-lesson checking',
                        'body_ar' => "<p>صمّم النشاط بحيث يمارس المتعلم ما وعد به هدف الحصة.</p>
<ul>
<li>وضّح التعليمات مرة واحدة بجمل قصيرة + مثال</li>
<li>تجوّل أثناء العمل: راقب الفهم لا تنتظر نهاية الحصة فقط</li>
<li>أدرج سؤال تحقق سريع قبل الانتقال للنشاط التالي</li>
</ul>
<p>التقويم التكويني هنا أداة للمعلم ليعدّل المسار داخل الحصة — وليس اختبارًا رسميًا.</p>",
                        'body_en' => '<p>Main task = practice the goal; circulate and use quick checks to adjust mid-lesson.</p>',
                        'duration_minutes' => 14,
                        'is_preview' => false,
                    ],
                ],
                'practices' => [
                    [
                        'practice_type' => 'application',
                        'title_ar' => 'تحدٍ تطبيقي: خطّة حصة من 3 مقاطع',
                        'title_en' => 'Applied challenge: 3-block lesson plan',
                        'summary_ar' => 'تهيئة + نشاط رئيس + إغلاق/تحقق — كل مقطع مربوط بالهدف.',
                        'body_ar' => "<ol>
<li><strong>التهيئة (دقائق):</strong> ماذا يفعل المتعلمون؟ وكيف يرتبط بالهدف؟</li>
<li><strong>النشاط الرئيس:</strong> المهمة، التجميع (فردي/ثنائي/مجموعات)، ودورك كمعلم أثناء العمل</li>
<li><strong>الإغلاق / التحقق:</strong> سؤال، بطاقة خروج، أو عرض سريع للناتج</li>
</ol>
<p>نفّذ الخطة، ثم اكتب جملة واحدة: ما المقطع الذي احتاج تعديلًا زمنيًا؟</p>",
                    ],
                    [
                        'practice_type' => 'tool',
                        'title_ar' => 'أداة مرتبطة: قالب تخطيط درس',
                        'title_en' => 'Linked tool: lesson planning template',
                        'summary_ar' => 'انقل خطتك إلى قالب التخطيط في مكتبة الأدوات واحفظ نسخة لصفّك.',
                        'body_ar' => '<p>استخدم «قالب تخطيط درس» من الأدوات لتعبئة الهدف والأنشطة والمواد في مكان واحد قابل لإعادة الاستخدام عبر الحصص.</p>',
                    ],
                ],
            ],
            [
                'sort_order' => 3,
                'title_ar' => 'تجهيز مواد الحصة ومراجعتها',
                'title_en' => 'Prepare lesson materials and review',
                'summary_ar' => 'جهّز المواد قبل الحصة، وراجع بعد التنفيذ: هل ساعدت المواد الهدف أم شوّشته؟',
                'summary_en' => 'Prepare materials before class, then review whether they served—or distracted from—the goal.',
                'estimated_minutes' => 40,
                'lessons' => [
                    [
                        'title_ar' => 'قائمة تجهيز قبل دخول الصف',
                        'title_en' => 'Pre-class preparation list',
                        'body_ar' => "<p>قبل الحصة بخمس دقائق على الأقل، تأكد:</p>
<ul>
<li>الوسائل والمواد مرتبة حسب ترتيب الأنشطة</li>
<li>التعليمات مكتوبة أو معروضة بجمل قصيرة</li>
<li>بديل جاهز إن تعطّل جهاز أو نقصت نسخ</li>
<li>زمن تقريبي لكل مقطع على ورقتك</li>
</ul>
<p>التجهيز الجيد يقلل ارتجالًا يُربك المتعلمين ويضعف إدارة الصف حتى لو كان المحتوى ممتازًا.</p>",
                        'body_en' => '<p>Materials sequenced, instructions visible, backup plan ready, timing noted.</p>',
                        'duration_minutes' => 10,
                        'is_preview' => false,
                    ],
                    [
                        'title_ar' => 'مراجعة بعد الحصة: هل تحقق الهدف؟',
                        'title_en' => 'After the lesson: did we hit the goal?',
                        'body_ar' => "<p>بعد انصراف المتعلمين، دقيقتان للمعلم:</p>
<ol>
<li>كم تقريبًا حقق مؤشر النجاح؟</li>
<li>أي نشاط أخذ زمنًا أطول من اللازم؟</li>
<li>ما الذي سأبقيه / أحذفه / أعدّله في الحصة المشابهة القادمة؟</li>
</ol>
<p>هذا هو «قياس تقدّم المعلم» على مستوى الحصة الواحدة — أساس التطوير المهني اليومي.</p>",
                        'body_en' => '<p>Two-minute teacher review: success rate, timing, keep/cut/change for next similar lesson.</p>',
                        'duration_minutes' => 10,
                        'is_preview' => false,
                    ],
                ],
                'practices' => [
                    [
                        'practice_type' => 'checklist',
                        'title_ar' => 'قائمة تحقق: جاهزية مواد الحصة',
                        'title_en' => 'Checklist: lesson materials readiness',
                        'summary_ar' => 'مرّ عليها صباح يوم الحصة أو في الليلة السابقة.',
                        'body_ar' => "<ol>
<li>هدف الحصة ومؤشر النجاح مكتوبان على ورقتي</li>
<li>مواد النشاط الرئيس جاهزة بعدد كافٍ</li>
<li>التهيئة لا تحتاج تجهيزًا يرتجل داخل الصف</li>
<li>لدي بديل إن نقصت أداة / تعطل جهاز</li>
<li>زمن المقاطع الثلاثة مقدّر وواقعي</li>
<li>سؤال تحقق أو بطاقة خروج جاهزة</li>
</ol>",
                    ],
                    [
                        'practice_type' => 'tool',
                        'title_ar' => 'أداة مرتبطة: لوحة تخطيط وحدة',
                        'title_en' => 'Linked tool: unit planning board',
                        'summary_ar' => 'اربط حصص الأسبوع بوحدة أوضح باستخدام لوحة التخطيط.',
                        'body_ar' => '<p>إن كنت تخطط سلسلة حصص، استخدم «لوحة تخطيط وحدة» لترى كيف تخدم كل حصة تقدّم المتعلمين عبر الوحدة — لا حصة معزولة كل يوم.</p>',
                    ],
                ],
            ],
        ];

        $this->syncPathUnits($path, $units);
        $this->attachTools($path, ['templates', 'planning_tools']);
    }

    /**
     * @param  list<array<string, mixed>>  $units
     */
    private function syncPathUnits(LearningPath $path, array $units): void
    {
        $keptUnitIds = [];

        foreach ($units as $unitData) {
            $unit = LearningPathUnit::query()->updateOrCreate(
                [
                    'learning_path_id' => $path->id,
                    'sort_order' => $unitData['sort_order'],
                ],
                [
                    'title_ar' => $unitData['title_ar'],
                    'title_en' => $unitData['title_en'],
                    'summary_ar' => $unitData['summary_ar'],
                    'summary_en' => $unitData['summary_en'],
                    'estimated_minutes' => $unitData['estimated_minutes'] ?? null,
                    'is_active' => true,
                ]
            );
            $keptUnitIds[] = $unit->id;

            $unit->lessons()->delete();
            $unit->practices()->delete();

            $lessonOrder = 1;
            foreach ($unitData['lessons'] ?? [] as $lesson) {
                LearningPathLesson::query()->create([
                    'learning_path_unit_id' => $unit->id,
                    'title_ar' => $lesson['title_ar'],
                    'title_en' => $lesson['title_en'] ?? null,
                    'content_type' => 'text',
                    'body_ar' => $lesson['body_ar'],
                    'body_en' => $lesson['body_en'] ?? null,
                    'duration_minutes' => $lesson['duration_minutes'] ?? null,
                    'sort_order' => $lessonOrder++,
                    'is_preview' => (bool) ($lesson['is_preview'] ?? false),
                    'is_active' => true,
                ]);
            }

            $practiceOrder = 1;
            foreach ($unitData['practices'] ?? [] as $practice) {
                LearningPathPractice::query()->create([
                    'learning_path_unit_id' => $unit->id,
                    'title_ar' => $practice['title_ar'],
                    'title_en' => $practice['title_en'] ?? null,
                    'summary_ar' => $practice['summary_ar'] ?? null,
                    'summary_en' => $practice['summary_en'] ?? null,
                    'practice_type' => $practice['practice_type'],
                    'body_ar' => $practice['body_ar'],
                    'body_en' => $practice['body_en'] ?? null,
                    'sort_order' => $practiceOrder++,
                    'is_active' => true,
                ]);
            }
        }

        // Remove leftover skeleton units (e.g. old demo unit with different sort_order titles)
        LearningPathUnit::query()
            ->where('learning_path_id', $path->id)
            ->whereNotIn('id', $keptUnitIds)
            ->each(function (LearningPathUnit $orphan) {
                $orphan->lessons()->delete();
                $orphan->practices()->delete();
                $orphan->delete();
            });
    }

    /**
     * @param  list<string>  $toolTypes
     */
    private function attachTools(LearningPath $path, array $toolTypes): void
    {
        if (! Schema::hasTable('learning_path_teacher_tool') || ! Schema::hasTable('teacher_tools')) {
            return;
        }

        $ids = TeacherTool::query()
            ->whereIn('tool_type', $toolTypes)
            ->pluck('id');

        if ($ids->isNotEmpty()) {
            $path->teacherTools()->syncWithoutDetaching($ids->all());
        }
    }
}
