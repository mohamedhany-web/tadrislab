# تصميمات الصفحات العامة — TADRIS LAB

مجلد **مرآة CSS+HTML** لصفحات اللاندنج الإنتاجية. ليس جزءاً من تشغيل Laravel ولا يُربط بالمسارات.

**افتح أولاً للرئيسية:** [01-home.html](01-home.html) — تصميم Lasles (Rubik + ألوان اللوجو أزرق/ذهبي)

**مرجع Figma المسحوب:** `../laslesvpn-figma/`

**قاعدة المحتوى:** النصوص هنا مرآة لما يظهر في الإنتاج (Blade) قدر الإمكان — راجع أيضاً skill `tadris-lab-platform`.

## الصفحات

| ملف | الصفحة | CSS | مصدر Blade |
|-----|--------|-----|------------|
| [01-home.html](01-home.html) | الرئيسية | `assets/lasles-theme.css` | `welcome` + `partials/landing/lasles/*` |
| [02-about.html](02-about.html) | من نحن | `../../public/css/landing/lasles.css` | `public/about` + `about-*` partials |
| [03-learning-paths.html](03-learning-paths.html) | المسارات التعليمية | `../../public/css/landing/lasles.css` | `public/learning-paths/index` + `paths-*` |
| tools | `/tools` | production CSS | `public/tools/index` + `page-art--tools` |
| consultations | `/educational-consultations` | production CSS | `public/consultations` + `page-art--map` |
| institutional | `/institutional` | production CSS | `public/institutions/index` + `page-art--map` |
| [10-path.html](10-path.html) | المسار (دورة المعلم) | `assets/lasles-theme.css` | `public/path` + route `/path` |
| [06-auth-login.html](06-auth-login.html) | تسجيل الدخول | `assets/lasles-auth-theme.css` | `auth/login` + `layouts/auth-landing` |
| [07-auth-register.html](07-auth-register.html) | إنشاء حساب معلم | `assets/lasles-auth-theme.css` | `auth/register` + `partials/auth/lasles/aside` |
| 03–05, 08–09 | صفحات عامة أخرى | بنية قديمة محفوظة كـ arch | تُحدَّث تدريجياً لنفس نظام الرئيسية |

## الرئيسية — تقسيم الإنتاج

```
resources/views/welcome.blade.php
resources/views/partials/welcome-lasles.blade.php   # orchestrator
resources/views/partials/landing/lasles/
  nav | hero | stats | features | pricing
  network | testimonials | subscribe | footer | scripts
public/css/landing/lasles.css
public/img/lasles/
```

## Responsive (CSS)

| نطاق | الهدف |
|------|--------|
| ≤479 | موبايل صغير |
| 480–767 | موبايل كبير |
| 768–1023 | تابلت |
| 960+ | نافبار ديسكتوب |
| 1024–1439 | لابتوب |
| ≥1440 | نسب Figma الديسكتوب (hero ~50px، section ~35px، body 16px) |

## Tokens

| Role | Hex |
|------|-----|
| Primary blue | `#1E4E8C` |
| Accent gold | `#A88050` |
| Blue tint | `#E8EEF6` |
| Gold tint | `#F7F1E8` |
| Navy | `#152A4A` |
| Muted | `#5A6472` |
| Max content | `1140px` |
| Font | Rubik |

## Skills (إلزامي للصفحات الخارجية)

- `.cursor/skills/tadris-lab-public-pages/SKILL.md` — **CX + Lasles** (مجبر: تجربة معلم/تطوير مهني، مش تغيير شكل فقط)
- `.cursor/skills/tadris-lab-platform/SKILL.md` — نطاق المنتج KEEP/DISABLE
- `designs/laslesvpn-figma/README.md` — سحب Figma والأصول
- `sana-*` skills = **deprecated / arch** فقط

> ملاحظة: ملفات CSS/HTML القديمة بأسماء تاريخية في هذا المجلد تبقى كـ **arch** فقط لحين ترحيل بقية الصفحات — لا تعتمد عليها للرئيسية.
