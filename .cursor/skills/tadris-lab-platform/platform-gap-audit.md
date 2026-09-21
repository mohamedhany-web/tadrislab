# تدقيق المنصة — تدريس لاب vs الإرث (Glottical)

**تاريخ:** 2026-09-17 (حدّث بعد التقليم والربط — الأصل 2026-09-11)
**المرجع الوظيفي:** Developer Brief V3 + `config/platform.php` + `mvp-scope.md`  
**الهدف:** تحديد ما هو زائد عن بزنس تدريس لاب، وما هو موجود ويُعاد توظيفه، وما هو ناقص يجب بناؤه.

---

## 1) نطاق المنتج (مصدر الحقيقة)

### الهوية
- **الاسم:** TADRIS LAB | تدريس لاب  
- **النوع:** منصة مخصصة قابلة للتوسع (Custom Modular Platform)  
- **الجمهور:** المعلمون / الممارسون التربويون (فردي أو تحت جهة)، وليس طلاب لغة أو أولياء أمور

### الدورة الأساسية (غير قابلة للتفاوض)
1. **تشخيص** — تحديات الممارسة الصفية  
2. **وصول** — ممارسات + أدوات + تحديات تطبيقية  
3. **تطوير** — الأداء المهني للمعلم  
4. **قياس** — تقدّم المعلم  

### الركائز التجارية الخمس فقط
| # | EN | AR |
|---|----|----|
| 1 | Learning Paths | المسارات التعليمية |
| 2 | Consultations | الاستشارات |
| 3 | Schools & Institutions | المدارس والمؤسسات (**محور واحد**) |
| 4 | Packages | الباقات |
| 5 | Tools & Resources | الأدوات والموارد |

> لا يُخترع عمود تجاري سادس. **مساعد المعلم (Teacher Assistant)** مرشّح تحت الأدوات/الموارد أو كمنتج مرتبط — بعقد AI-ready — وليس ركيزة منفصلة في V3.

### نواة MVP (Brief V3 §18)
| البند | مطلوب في المرحلة 1 |
|-------|---------------------|
| حسابات + أدوار | نعم |
| مسارات + محتوى | نعم |
| منتجات / باقات | نعم |
| Checkout + دفع + Webhooks | نعم |
| طلبات / معاملات | نعم |
| استشارات + حجز | نعم |
| واتساب / إيميل + نقطة استفسار | نعم |
| مدارس: حساب أساسي + مشاركين + برامج | نعم |
| لوحة إدارة | نعم |
| تقارير متقدمة / أتمتة ثقيلة / تطوير مؤسسي معقّد / تطبيقات موبايل | لاحقًا (بدون إغلاق المعمارية) |

### الأدوار المستهدفة (V3)
| المفتاح | المعنى |
|---------|--------|
| `platform_admin` | مدير المنصة |
| `institution_admin` | مسؤول / منسق مدرسة أو مؤسسة |
| `teacher_participant` | معلم مشارك تحت جهة |
| `individual_user` | مستخدم فردي (معلم/ممارس) — الأساسي |

---

## 2) ملاحظة تشغيلية حرجة — مُحدَّثة (2026-09-17)

`config/platform.php` → `modules.*.enabled` **مربوط بالكود**:

| الطبقة | الحالة |
|--------|--------|
| `AbortIfPlatformModuleDisabled` | soft-close لمسارات الخصوصي / التوظيف / live / إحالات / مالية إرث… |
| سايدبار الأدمن (`$m('…')`) | يخفي مجموعات Glottical عند التعطيل |
| `admin_nav.php` | سطح Brief V3 الأساسي (ركائز + CRM مؤسسات + معلمون/مدربون) |
| `student_ui` / `instructor_ui` | إخفاء إرث طالب اللغة / خصوصي من لوحتي المعلم والمدرب |
| `TadrisRoles` | توجيه login لأدمن / معلم / مدرب فوق أدوار runtime |

**لا حذف Models/Migrations** في هذه المرحلة.

---

## 3) زائد — إرث لا علاقة له ببزنس تدريس لاب

> **حالة التعطيل (2026-09-11):** طُبّق soft-disable — **لا تظهر في السايدبار** و**الروابط تُرجع 404**. الملفات/الجداول باقية. لإعادة التفعيل: اضبط `config/platform.php` → `modules.{key}.enabled = true`.

| المجال | إشارات / مسارات نموذجية | الحكم | طبقة التعطيل | مفتاح `modules` |
|--------|-------------------------|-------|--------------|-----------------|
| حصص خصوصي / مجموعات | `/groups`، حجوزات، اشتراكات tut، `ServicePackage`، باقات الحصص | **DISABLE** | UI + routes 404 | `tutoring` |
| حصة تجريبية | `/free-trial/*` | **DISABLE** | UI + routes 404 | `free_trial` |
| ولي أمر | `/parent-progress` + دور `parent` | **REMOVED** | UI + أدوار + seeders + أوامر التقارير؛ مسار 404 | `parent_progress` (enabled=false) |
| توظيف مدرسي لغة | `/tutor/apply`، لوحة التوظيف، طلبات المدربين | **DISABLE** | UI + routes 404 | `tutor_hiring` |
| CRM المؤسسات والمعلمين | `admin/crm/*`، leads، عمولات، مكتب موظف | **REPURPOSE** | ظاهر في سايدبار Brief + routes مفتوحة | `crm_sales` (enabled=true) |
| لوحة طالب لغة | `student.*` tutoring، إحالات، ألعاب لغة | **DISABLE** / إخفاء UX | Nav + routes جزئي | `student_panel` / `loyalty_referrals` |
| بث LiveKit كمنتج حصص | `/classroom/join`، live-sessions | **DISABLE** | سايدبار + routes 404 | `live_classroom` |
| مالية مدرّب إرث | محافظ، رواتب مدربين، تقسيط طلاب | **DISABLE** | سايدبار + routes 404 | `legacy_finance` |
| تسويق إرث | popup ads، إحالات ولاء | **DISABLE** | سايدبار + routes 404 | `loyalty_referrals` |
| Placement لغة | اختبارات تحديد مستوى لغة | **DISABLE** | UI + routes 404 | `placement` |
| مجتمع مسابقات / ML | `/community*` | **DISABLE** | routes 404 | `community` |
| n8n كتشغيل حصص مباشرة | تقارير بث إرث | **DISABLE** | سايدبار + routes 404 | `live_classroom` |

### مجموعات سايدبار الأدمن — الزائد (مخفي عند التعطيل)

| المجموعة (تقريبًا) | لماذا زائدة |
|--------------------|-------------|
| التشغيل → الحصة المجانية | تسويق حصص لغة |
| التوظيف | توظيف مدرسي لغة |
| الطلاب (حسابات، placement، مجموعات، one-to-one، حضور حصص…) | قلب نموذج Glottical |
| المبيعات / CRM (ما عدا الطلبات العامة إن لزم) | ~~زائدة~~ → **REPURPOSE** كـ CRM مؤسسات ومعلمين |
| المالية → محافظ / رواتب مدربين / تقسيط | تشغيل خصوصي |
| التسويق → popup / إحالات | نمو إرث |
| مدفوع → باقات الحصص (`ServicePackage`) | ليست باقات PD |
| التعليم → بث مباشر / جداول مدرسين خصوصي | إرث حصص |
| الرقابة على طلاب/مدربين لغة | نموذج طالب لغة |

### آلية التنفيذ (لا حذف)

| الطبقة | أين |
|--------|-----|
| Config flags | `config/platform.php` → `modules.*.enabled` |
| Route soft-close | `App\Http\Middleware\AbortIfPlatformModuleDisabled` (خريطة مسارات → وحدة) |
| Helper | `App\Support\PlatformModules::enabled()` + Blade `@module('tutoring')` |
| Admin UI hide | `resources/views/layouts/admin-sidebar.blade.php` عبر `$m('…')` |

---

## 4) موجود ويُعاد توظيفه (KEEP / REPURPOSE)

| الأصل الموجود | يخدم تدريس لاب كـ | ملاحظات |
|---------------|------------------|---------|
| Auth + Users + Roles/Permissions | حسابات وتشغيل | أعد مواءمة الأدوار لـ V3 |
| System settings / activity / notifications | تشغيل عام | KEEP |
| Media / storage | بنية تحتية | KEEP |
| صفحات عامة (about, contact, FAQ, terms) + غلاف Lasles | تسويق | REPURPOSE للنصوص والـ CX |
| `AdvancedCourse` + دروس / أقسام / امتحانات | محتوى المسارات | REPURPOSE — ليس كورس لغة |
| `Package` + `package_type` / جلسات استشارة / مقاعد / أدوات | ركيزة الباقات | حقول V3 جزئيًا موجودة |
| `Order` + بوابات دفع + webhooks | نواة الدفع | أعد الربط لباقات PD + entitlement |
| Contact messages + support tickets | استفسارات ودعم | KEEP خفيف / وسّع لاحقًا لـ Inquiry typed |
| WhatsApp helpers / إعدادات | تواصل تشغيلي | اربط بأحداث المنتج |
| مكتبات / مواد / curriculum library | نواة أدوات وموارد | REPURPOSE لأنواع templates/checklists… |
| Certificates / exams / assignments | قياس تقدّم محتمل | REPURPOSE لمعلم وليس طالب لغة |
| `ConsultationRequest` (حاليًا طالب↔مدرّس) | نواة استشارات | REPURPOSE لرحلة معلم/مؤسسة |
| `School` (K-12 tutoring) | **لا يساوي** حساب مؤسسة V3 | لا تخلطه — ابنِ كيان مؤسسة منفصل أو أعد تعريفه صراحة |

### ما يبقى ظاهرًا في الأدمن كأساس Brief
- Dashboard  
- Users / roles  
- Site content / إعدادات  
- Orders / payments / transactions  
- Packages (TADRIS `Package` وليس service-packages)  
- Advanced courses / مكتبات (كمحتوى مسارات وأدوات)  
- Contact / notifications / WhatsApp settings  

---

## 5) ناقص — يجب بناؤه

### 5.1 حسب الركائز

| الركيزة | الناقص الجوهري |
|---------|----------------|
| **المسارات التعليمية** | كيان `LearningPath` حقيقي (إدارة الصف، تخطيط، استراتيجيات، وقت الحصة، تقويم، تطوير مهني…) مربوط بمحتوى وتشخيص وتقدّم معلم — `AcademicYear` الحالي ≠ مسار معلم |
| **الاستشارات** | أنواع (فردي / متخصص / مؤسسة)، حجز مواعيد، تأكيد، تذكير واتساب، مخرجات للمعلم — الموجود أقرب لطلب طالب لمدرّس لغة |
| **المدارس والمؤسسات** | حساب جهة + منسق + مشاركين تحت الجهة + برامج تدريب أساسية (حالات: inquiry → … → completed) |
| **الباقات** | اكتمال الشراء: اختيار باقة → checkout → دفع → **تفعيل صلاحية وصول (entitlement)** بعد webhook؛ ربط included_paths / tools ككيانات |
| **الأدوات والموارد** | مكتبة منتجات قابلة للإدارة (قوالب، قوائم تحقق، أدوات صف، تخطيط، تقويم، تحميلات…) |

### 5.2 نواة MVP الإضافية

| بند MVP | الحالة الآن |
|---------|-------------|
| حسابات + أدوار | جزئي — Auth + `TadrisRoles`؛ DB ما زال `student` للمعلم |
| مسارات + محتوى | موجود — LearningPath + تقدّم معلم |
| باقات / منتجات | موجود — كتالوج + checkout + entitlement |
| دفع + webhooks + طلبات | مربوط لكتالوج عبر `CatalogOrderFulfillmentService` |
| استشارات + حجز | رحلة معلم + خصم جلسة باقة + دفع أونلاين (PayPal/Kashier) / تحويل عبر Order |
| واتساب / إيميل + استفسارات | موجود + Inquiry typed |
| مدارس: حساب / مشاركين / برامج | أدمن كامل + بوابة منسق `/institution` |
| لوحة إدارة | Brief nav؛ إرث مخفي |
| Teacher Assistant | شاشة + عقد AI-ready (`/my-assistant`) |
| قياس تقدّم المعلم بعد الوصول | موجود على المسارات (شريط % + إكمال عناصر) |

### 5.3 كيانات بيانات مخططة (للتوسّع — ليست كلها إلزام MVP فورًا)
`user` · `school_institution` · `product_service` · `package` · `entitlement` · `order` · `payment_transaction` · `booking` · `training_program` · `inquiry` · `notification` · `progress`

---

## 6) مقارنة سريعة: Module flags vs الواقع

| العلم في `platform.modules` | القيمة النموذجية | مربوط بالكود؟ | الواقع |
|----------------------------|------------------|---------------|--------|
| `tutoring`, `free_trial`, `parent_progress`, `tutor_hiring`, `live_classroom`, `community`, `loyalty_referrals`, `language_games`, `student_panel` | `enabled => false` | **نعم** (middleware + سايدبار + student/instructor UI) | مُعطّل عن السطح |
| `crm_sales` | `enabled => true` | **نعم** | ظاهر كـ CRM مؤسسات ومعلمين |
| ركائز (`learning_paths`, `consultations`, `packages`, `tools`…) | `enabled => true` | entitlement + لوحات معلم/مدرب | مربوط مرحليًا عبر الباقة |
| `teacher_assistant` | `true` | لا منتج كامل | مؤجّل |

---

## 7) أولوية تنفيذ مقترحة

| الترتيب | العمل | النتيجة |
|---------|--------|---------|
| 1 | تعطيل الزائد (UI + soft-close للـ routes + قراءة `platform.modules`) | تجربة نظيفة فورًا بدون حذف جداول |
| 2 | إكمال مسار شراء الباقات (دفع → entitlement) | أول قيمة تجارية قابلة للبيع |
| 3 | مسارات + محتوى (فوق `AdvancedCourse` أو كيان Path جديد) | ركيزة التعلم |
| 4 | استشارات معلم + حساب مؤسسة بسيط | B2C استشارة + B2B أساسي |
| 5 | مكتبة أدوات ثم Teacher Assistant (AI-ready) | وصول تطبيقي داخل الصف |

---

## 8) سياسة القرار (للتذكير)

| الوسم | المعنى | الإجراء |
|-------|--------|--------|
| **KEEP** | يخدم الدورة أو البنية التحتية | يبقى ظاهرًا؛ حدّث التسمية إن لزم |
| **REPURPOSE** | البنية مفيدة لكن الاسم/التدفق إرث | لا تحذف؛ أعد التوجيه؛ أخفِ UX المضلل |
| **DISABLE** | خارج النطاق الحالي | عطّل طبقات — **لا تحذف** إلا بموافقة صريحة |

عند الشك: **DISABLE** مؤقتًا أفضل من إبقاء سطح Glottical/طالب ظاهر.

---

## 9) سجل قرارات مرتبطة

| التاريخ | الموضوع | القرار |
|---------|---------|--------|
| 2026-09-11 | مخطط MVP | حفظ في `mvp-scope.md` + `config/platform.php` |
| 2026-09-11 | Developer Brief V3 | SoT وظيفي في `developer-brief-v3.md` |
| 2026-09-11 | هذا التدقيق | توثيق الزائد / المعاد توظيفه / الناقص في هذا الملف |
| 2026-09-15 | ولي الأمر | **REMOVED** من الواجهات والأدوار والـ seeders؛ `parent_progress` يبقى 404 |
| 2026-09-15 | CRM | **REPURPOSE** → مؤسسات ومعلمون؛ `crm_sales.enabled=true` + قسم في `admin_nav` |
| 2026-09-15 | إدارة المحتوى | بدء تسمية/IA: «إدارة محتوى المسارات» في السايدبار وصفحة المسارات |
| 2026-09-17 | تقليم جراحي + طبقات | `TadrisRoles` + prune nav/middleware؛ الباقة منسّق entitlement (مسارات/أدوات/جلسات)؛ مدرب يرى المسارات/الاستشارات المسندة فقط |
| 2026-09-17 | استهلاك جلسات | حجز استشارة يخصم `consultation_sessions_remaining` عبر `PackageEntitlementService` |
| 2026-09-18 | نواقص MVP | مساعد معلم AI-ready + بوابة جهة `/institution` + شريط تقدّم المسارات + توجيه منسق الجهة |
| 2026-09-18 | دفع كتالوج | `PaymentController` عند completed يفعّل `CatalogOrderFulfillmentService` (سد فجوة اعتماد الدفع بدون مسار Order approve) |
| 2026-09-18 | إغلاق رحلات جزئية | استشارات: انضمام `confirmed/rescheduled` + إكمال عند إنهاء الغرفة + تذكير معاد + تعارض مواعيد + استرجاع جلسة باقة؛ مؤسسات: قبول عرض + تسجيل مشاركين + تقدّم؛ مدرب: قفل tutoring/live عبر `instructor.ui` + كورسات البث المسندة فقط |

---

## 10) ملفات مرتبطة

| الملف | الدور |
|-------|------|
| `.cursor/skills/tadris-lab-platform/SKILL.md` | مهارة النطاق وسياسة التعطيل |
| `.cursor/skills/tadris-lab-platform/developer-brief-v3.md` | الرؤية الوظيفية V3 |
| `.cursor/skills/tadris-lab-platform/mvp-scope.md` | مخطط MVP |
| `.cursor/skills/tadris-lab-platform/feature-audit.md` | كتالوج مجالات KEEP/REPURPOSE/DISABLE |
| `config/platform.php` | الركائز، الأدوار، الـ MVP flags، وحدات التشغيل |
| `app/Support/TadrisRoles.php` | طبقة أدوار المنتج فوق runtime |
| `app/Services/PackageEntitlementService.php` | الباقة كمنسّق وصول |

---

## 11) حالة ما بعد التقليم والربط (مرحلة 1 — 2026-09-17)

| معيار النجاح | الحالة |
|--------------|--------|
| لا سطح عام/أدمن يروّج لخصوصي/ولي أمر/توظيف لغة | **محقّق** عبر modules + middleware + nav flags |
| معلم يشتري باقة → مسارات وأدوات + استشارة من رصيد الباقة | **محقّق** (`PackageEntitlementService` + `/my-packages` + حجز يخصم الجلسة) |
| مدرب يرى فقط ما أُسند إليه | **محقّق جزئيًا** — مسارات عبر `teachingLearningPathIds`؛ استشارات عبر `instructor_id` + منح `consultations` |
| أدمن يدير الكتالوج من nav Brief | **محقّق** — `admin_nav` + تسمية معلمون/مدربون |

### ما يبقى لاحقًا (خارج مرحلة 1)
- ~~بوابة self-service للمؤسسة~~ → **أساسي MVP:** `/institution` للمنسق (أعضاء + برامج + إضافة مشاركين)
- ~~Teacher Assistant كامل~~ → **عقد + شاشة:** `/my-assistant` (template الآن، Gemini اختياري عبر `TADRIS_TA_AI_PROVIDER=gemini`)
- استبدال كل بوابات الدفع (المسار الكتالوج PayPal/Kashier/تحويل يعمل مع entitlement)
- ترحيل دور `student` → `individual_user` في الجداول (التسمية UX جاهزة عبر `TadrisRoles`)
- محرك AI كامل لمساعد المعلم (العقد جاهز)
- تطوير مؤسسي معقّد / تقارير متقدمة

*هذا الملف وثيقة تدقيق منتج — ليس إذنًا بحذف كود. أي تعطيل جماعي يُنفَّذ بعد موافقة صريحة.*
