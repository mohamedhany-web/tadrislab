---
# Feature audit catalogue — inherited Glottical → TADRIS LAB

Starter verdicts are **hypotheses** from the product brief. Confirm with the user before bulk disable. Update this file when verdicts are finalized.

## How to re-scan the codebase

1. Public + auth routes: `routes/web.php`
2. Admin nav: `resources/views/layouts/admin-sidebar.blade.php` + `config/admin_sidebar_role_map.php`
3. Panels: `resources/views/{admin,student,instructor,employee,public,tutor}/`
4. Models: `app/Models/`
5. Controllers: `app/Http/Controllers/{Admin,Student,Instructor,Employee,Public,Community}/`

## Domain catalogue

| Domain | Typical paths / signals | Starter verdict | Notes for TADRIS LAB |
|--------|-------------------------|-----------------|----------------------|
| Auth / users / roles / permissions | `Auth`, `Role`, `Permission`, admin users | **KEEP** | أساس المنصة |
| System settings / activity log / notifications | `Setting`, admin system-settings, notifications | **KEEP** | تشغيل عام |
| Media / secure file / storage | `StorageFileController`, media gallery | **KEEP** | بنية تحتية |
| Public marketing shell (about, contact, FAQ, terms, privacy) | `public.about`, contact, FAQ | **REPURPOSE** | أبقِ الهيكل؛ غيّر النص والعلامة لتدريس لاب |
| Courses / lessons / enrollments / packages | `Course`, `public.courses`, student learn | **REPURPOSE** | قد تصبح مسارات/وحدات تطوير مهني — ليس دروس لغة للطلاب |
| Exams / question bank / assignments | `Exam`, `QuestionBank`, assignments | **REPURPOSE** | مفيد للتشخيص وقياس التقدّم إن أُعيد التوجيه |
| Certificates / badges / achievements / XP | certificates, badges, streaks | **REPURPOSE** | قياس تقدّم المعلم — لا شهادات طالب لغة كما هي |
| Curriculum library / libraries / materials | curriculum-library, libraries | **REPURPOSE** | مكتبة ممارسات/أدوات محتملة |
| Classroom live room / LiveKit / live sessions / recordings | classroom join, live-sessions, LiveKit | **DISABLE** أو **REPURPOSE** بعد قرار | بث حصص خصوصي إرث؛ عطّل للواجهة حتى يتحدد إن كان لتدريس لاب حاجة لغرف تطبيقية |
| Tutoring groups / cohorts / bookings / subscriptions | tutoring-*, `/groups`, one-to-one | **DISABLE** | قلب نموذج Glottical للطلاب |
| Free trial booking | free-trial | **DISABLE** | تسويق حصص تجريبية للغات |
| Parent progress | `public.parent-progress` | **REMOVED** | ولي أمر — خارج نطاق تدريس لاب؛ أُزيل من الواجهات والأدوار والـ seeders؛ المسار 404 |
| Student panel (`role:student`) | `student.*` routes/views | **DISABLE** أو إعادة تسمية لاحقة لدور المعلم-المتعلّم | لا تبنِ UX «طالب» ظاهرًا |
| Instructor panel as language tutor | instructor tutoring, work schedule, withdrawals | **REPURPOSE**/**DISABLE** جزئي | قد يبقى «ميسّر/مدرب محتوى»؛ عطّل جداول الحصص الخصوصية والسحب إن لم تُطلب |
| Tutor apply / hiring / HR recruitment | tutor/apply, hiring-form, Hr* | **DISABLE** | توظيف مدرسي لغة |
| CRM / sales leads / commissions | `admin.crm.*`, sales | **REPURPOSE** | CRM مؤسسات ومعلمين (باقات/برامج) — مُفعّل في `crm_sales` |
| Service packages / custom quotes checkout | service-packages | **DISABLE** | عروض خدمات الإرث |
| Wallets / installments / instructor salaries / payouts | wallets, installments, salaries | **DISABLE** (مالي الإرث) | أبقِ فواتير/مدفوعات عامة فقط إن لزم اشتراكات لاحقًا — أكّد مع المستخدم |
| Payment gateways (Fawaterak/PayPal/Kashier) | webhooks, checkout | **KEEP** بنية؛ **DISABLE** مسارات الشراء غير ذات الصلة | لا تكسر الويب هوكس؛ أخفِ checkout غير المستخدم |
| Coupons / loyalty / referrals | coupons, loyalty, referrals | **DISABLE** | نمو مبيعات الإرث |
| Consultations (student↔instructor) | consultations | **DISABLE** أو **REPURPOSE** | استشارات صفية للمعلم قد تُعاد لاحقًا |
| Community competitions / datasets / models | Community* | **DISABLE** | مجتمع ML/مسابقات الإرث إن وُجد |
| Employee desk / HR / salaries ops | employee/*, Hr* | **DISABLE** أو إبقاء إداري خفيف | أكّد احتياج تشغيل تدريس لاب |
| Arabic alphabet / school games | `arabic_alphabet_game`, `school_game` | **DISABLE** | ألعاب متعلّم لغة |
| WhatsApp / n8n live reports ops | WhatsApp, n8n | **DISABLE** حتى يُطلب للتشغيل | تكاملات تشغيل Glottical |
| Popup ads / marketing widgets | PopupAd, marketing | **DISABLE** | إلا حملات تدريس لاب لاحقًا |
| Placement tests (language) | placement | **DISABLE** أو **REPURPOSE** لتشخيص ممارسة صفية | لا تتركه كـ placement لغة |
| Academic years / subjects / schools (K-12 tutoring structure) | AcademicYear, School, Subject | **REPURPOSE**/**DISABLE** | قد تُستخدم لسياق المعلم المدرسي — لا تفترض نموذج الطالب |
| Support tickets | support | **KEEP** خفيف | دعم مستخدمي المنصة |
| Site services / testimonials / events / partners | site services, testimonials | **REPURPOSE** | محتوى تسويقي يُعاد كتابته |

## Likely first DISABLE batch (UI + routes)

استخدم هذه القائمة كاقتراح أول نقاش مع المستخدم:

1. Free trial + parent progress (**parent مُزال**)  
2. Tutoring groups / one-to-one / tutoring subscriptions / bookings  
3. Public `/groups`, service-packages checkout flows  
4. ~~CRM + sales desk~~ → **مُفعّل كـ REPURPOSE** (مؤسسات ومعلمون)  
5. Tutor apply + hiring hub (إن لم يُعرَّف توظيف لميسّري تدريس لاب)  
6. Student-facing gamification/language games configs  
7. Community competitions (إن ظهرت في السايدبار)  
8. Live tutoring sessions nav (مع الإبقاء على الكود)

## Disable implementation notes (Laravel)

Preferred patterns in this repo:

- Sidebar: wrap blocks in `@if(config('platform.modules.X.enabled'))` after adding keys to `config/platform.php`
- Routes: group surplus routes under the same config check or a small middleware `EnsureModuleEnabled:X`
- Public CTAs in Blade layouts/partials: hide with the same config
- Do **not** drop tables or delete `app/Models/*` in the first pass
- After disable, smoke-check: `/`, login, admin dashboard still load

### Suggested `config/platform.php` module keys

See live file `config/platform.php` — MVP modules are enabled (`learning_paths`, `teacher_assistant`, `schools_institutions`, `consultations`, `packages`, `tools_resources`, `payments`, `whatsapp_comms`, `admin_dashboard`). Inherited Glottical surplus modules default to `enabled => false`. Full brief: [mvp-scope.md](mvp-scope.md).

Only gate routes/UI against keys you actually check; expand as disables/builds land.

## Finalized decisions log

| Date | Domain | Verdict | Applied? | Notes |
|------|--------|---------|----------|-------|
| 2026-09-11 | MVP blueprint | **KEEP** as product SoT | Yes | Saved to `mvp-scope.md` + `config/platform.php` |
| 2026-09-11 | Developer Brief V3 | **KEEP** as functional architecture SoT | Yes | `developer-brief-v3.md` + expanded `config/platform.php` |
| 2026-09-11 | Surplus Glottical batch (§3 gap audit) | **DISABLE** soft | Yes | UI hide + `AbortIfPlatformModuleDisabled` 404; files kept — see `platform-gap-audit.md` |
