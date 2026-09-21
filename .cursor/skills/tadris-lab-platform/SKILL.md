---
name: tadris-lab-platform
description: >-
  Defines TADRIS LAB (تدريس لاب) product scope and audits the inherited Glottical
  codebase for features to keep, repurpose, or disable. Use when pruning modules,
  disabling surplus features, renaming roles/copy, scoping roadmap, reviewing
  routes/sidebar, or when the user mentions تدريس لاب, tadris, نطاق المنصة,
  تعطيل ميزات, or ما مش هنحتاجه.
---

# TADRIS LAB | تدريس لاب — Platform Scope

## Product definition (source of truth)

**TADRIS LAB | تدريس لاب**

منصة مخصصة قابلة للتوسع (**Custom Modular Platform**) لتطوير الممارسات المهنية للمعلمين — نواة MVP واضحة دون بناء منصة ضخمة في المرحلة الأولى، مع Architecture لا تقيّد النمو.

**Functional architecture (Developer Brief V3):**  
→ **[developer-brief-v3.md](developer-brief-v3.md)** ← primary internal functional vision  
→ Runtime: `config/platform.php` (`pillars`, `roles`, `learning_paths`, `consultations`, `schools_institutions`, `packages`, `mvp`, `data_model`, `open_decisions`, …)  
→ Expandable data model map: [data-model.md](data-model.md) (`mvp_required` vs deferred — do not force full entity build in MVP)  
→ Expandability checklist (Brief §16): [expandability.md](expandability.md) + `config/expandability.php`  
→ User journeys map: [user-journeys.md](user-journeys.md) + `config/journeys.php`  
→ Earlier product save / TA notes: [mvp-scope.md](mvp-scope.md)  
→ Gap audit (surplus vs missing vs repurpose): [platform-gap-audit.md](platform-gap-audit.md)

### Five commercial pillars (do not invent parallel axes)

1. **Learning Paths** — المسارات التعليمية  
2. **Consultations** — الاستشارات (قسم مستقل عن المسارات)  
3. **Schools & Institutions** — المدارس والمؤسسات (**محور واحد**؛ لا فرع منفصل مدارس vs مؤسسات)  
4. **Packages** — الباقات (admin-managed، ليست hard-coded)  
5. **Tools & Resources** — الأدوات والموارد  

### Core loop (must stay aligned)

1. **تشخيص** — التحديات في الممارسة الصفية  
2. **وصول** — ممارسات + أدوات + تحديات تطبيقية  
3. **تطوير** — الأداء المهني للمعلم  
4. **قياس** — تقدّم المعلم  

### Phase-1 / MVP nucleus (Brief V3 §18)

Accounts+Roles · Paths+Content · Products/Packages · Checkout+Payment+Webhooks · Orders · Consultations+Booking · WhatsApp/Email core + inquiry entry · Schools basic account/participants/programs · Admin Dashboard.

Defer: advanced reports, heavy automation, complex institutional development, apps — **without blocking architecture**.

### Technical principles

- لا hard-code لمسارات/باقات/منتجات/خدمات  
- Modular + content separated from system logic  
- Payment = real backend + webhooks + entitlement activation  
- WhatsApp = operational (payments, bookings, institutions, inquiries)  
- Admin manages catalogue without code changes  
- Teacher Assistant (إن بُني) = تحت الأدوات أو منتج مرتبط؛ **ليس** عمودًا تجاريًا سادسًا في V3؛ عقد AI-ready  

### Primary actors

- **Individual User** — معلم/ممارس شخصي  
- **Institution user** — تحت حساب الجهة وصلاحيات المنسق  
- Roles: `platform_admin` · `institution_admin` · `teacher_participant` · `individual_user`  

### Inherited codebase

هذا الريبو أصله **Glottical**. الهدف: توجيه المنتج لـ TADRIS LAB وفق Brief V3 **بدون حذف كود واسع** — تعطيل ما خارج النطاق، وإعادة توظيف ما يخدم الركائز الخمس والـ MVP.

## When to use this skill

- تدقيق ميزات زائدة وتعطيلها
- قرار KEEP / REPURPOSE / DISABLE
- تعديل سايدبار، routes، صفحات عامة، أدوار
- أي نقاش «هل ده تبع تدريس لاب؟»

Load [feature-audit.md](feature-audit.md) for the domain catalogue and disable checklist.

## Decision labels

| Label | Meaning | Action now |
|-------|---------|------------|
| **KEEP** | يخدم التشخيص / الممارسات / الأدوات / التحديات / قياس التقدّم مباشرة أو بنية تحتية ضرورية (auth, RBAC, settings, media) | ابقِ ظاهرًا ومتاحًا؛ حدّث التسمية إن لزم |
| **REPURPOSE** | البنية مفيدة لكن المسمى/التدفق إرث (مثلاً courses → مسارات تطوير مهني) | لا تحذف؛ خطط لإعادة تسمية/توجيه لاحقًا؛ أخفِ الـ UX القديم إن كان مضللاً |
| **DISABLE** | خارج نطاق تدريس لاب حاليًا | عطّل من النظام (انظر سياسة التعطيل) — **لا تحذف** Models/Migrations/Controllers إلا بطلب صريح |

عند الشك: **DISABLE** مؤقتًا أفضل من الإبقاء الظاهر بعلامة Glottical/طالب.

## Disable policy (تعطيل — ليس حذف)

التعطيل الافتراضي طبقات بالترتيب (طبّق الأدنى الكافي):

1. **UI hide** — إزالة/إخفاء روابط السايدبار والقوائم العامة والـ CTAs
2. **Route soft-close** — إرجاع 404 أو redirect آمن للمسارات العامة/اللوحة دون كسر auth العام
3. **Config gate** — سجّل الوحدة في `config/platform.php` تحت مفتاح مثل `modules.{key}.enabled` (أضفه عند أول تعطيل منهجي) واقرأ الـ gate في middleware أو Blade
4. **Permissions** — اسحب صلاحيات الوحدة من أدوار غير الإدارة إن وُجدت
5. **لا تحذف** جداول، موديلات، أو ملفات كبيرة إلا بموافقة صريحة لاحقة

عند التعطيل أبلغ المستخدم بقائمة مختصرة: الوحدة → الطبقة المطبّقة → ما بقي ظاهرًا.

## Audit workflow

عند طلب تدقيق أو تعطيل:

```
Audit progress:
- [ ] 1. Confirm product loop still matches user brief
- [ ] 2. Inventory touched area (routes / sidebar / views / models)
- [ ] 3. Label each domain KEEP | REPURPOSE | DISABLE
- [ ] 4. Propose disable layers (no delete)
- [ ] 5. Wait for user OK before applying — unless they already said «عطّل»
- [ ] 6. Apply + list what changed
```

### Output format (audits)

```markdown
## نطاق مختصر
[جملة تربط القرار بدورة التشخيص→الوصول→التطوير→القياس]

## قرارات
| Domain | Verdict | Why | Disable layer |
|--------|---------|-----|---------------|
| ... | DISABLE | ... | UI + routes |

## مقترح التنفيذ
- [ ] ...
```

## Naming & copy

- العلامة: **TADRIS LAB** / **تدريس لاب** — لا تُبقِ Glottical / Atheer / Sana كعلامة منتج في واجهات المستخدم النهائية.
- صفحات عامة / لاندنج: skill إلزامي **`tadris-lab-public-pages`** (تجربة عميل + Lasles) — ليس إعادة تلوين فقط.
- Skills التصميم القديمة (Sana) = arch فقط؛ **هذا الـ skill** يحكم نطاق المنتج والتعطيل.
- تجنّب مصطلحات الإرث الظاهرة: طالب لغة، حصة تجريبية، مجموعات خصوصي — **ولي أمر مُزال بالكامل من المنتج**.
- CRM = **KEEP/REPURPOSE** لمؤسسات ومعلمين (باقات/برامج) — ليس مبيعات أكاديمية لغة.

## Out of scope until explicitly requested

- حذف واسع للكود أو migrations drop
- بناء ميزات جديدة كاملة قبل إغلاق تدقيق الإرث المتفق عليه
- تغيير بوابة دفع / LiveKit / بنية السيرفر إلا إذا طُلب صراحة

## Additional resources

- **Developer Brief V3 (functional architecture):** [developer-brief-v3.md](developer-brief-v3.md)
- Earlier MVP / Teacher Assistant notes: [mvp-scope.md](mvp-scope.md)
- Runtime product data: `config/platform.php`
- Expandability (Brief §16): [expandability.md](expandability.md) · `config/expandability.php`
- User journeys: [user-journeys.md](user-journeys.md) · `config/journeys.php`
- Domain catalogue + starter verdicts: [feature-audit.md](feature-audit.md)
- Public CX + Lasles UI: `.cursor/skills/tadris-lab-public-pages/SKILL.md`
