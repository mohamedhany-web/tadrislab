---
name: tadris-lab-public-pages
description: >-
  Mandatory CX+UI system for TADRIS LAB public/marketing pages (Lasles visual
  language + teacher professional-development journey). Use when editing or
  creating any external page: home, about, courses/paths, pricing, contact,
  instructors, auth login/register, tutor-apply, FAQ, terms, landing CSS/Blade,
  designs/public-pages mirrors, or when the user mentions صفحات خارجية, لاندنج,
  Lasles, تصميم عام, مرآة تصميم, or matching public UI to the new pattern.
  Supersedes sana-public-page-design and sana-landing-blade-implement for new work.
---

# TADRIS LAB — Public Pages (CX + Lasles)

**This skill is mandatory** for every public/marketing/auth-marketing change.
A visual reskin alone is a **failed** task. Every page must deliver a **customer experience** aligned with the product.

Also load: `.cursor/skills/tadris-lab-platform/SKILL.md` (product loop + naming).
Deep page briefs: [pages.md](pages.md) · Visual tokens: [tokens.md](tokens.md)

## Product experience (non-negotiable)

Primary user = **المعلم** (professional learner), not language student / parent / private tutor customer.

Core loop every public page must serve (at least one step, ideally the full journey):

1. **تشخيص** — تحديات الممارسة الصفية  
2. **وصول** — ممارسات + أدوات + تحديات تطبيقية  
3. **تطوير** — الأداء المهني  
4. **قياس** — تقدّم المعلم  

Brand: **TADRIS LAB** / **تدريس لاب**. Never ship Glottical / Sana / Atheer as product brand in visible copy, titles, SEO, or CTAs.

### Forbidden legacy CX (unless user redefines explicitly)

- طالب لغة، ولي أمر، حصة تجريبية، مجموعات خصوصي، placement لغة، توظيف مدرس لغة كمنتج أساسي  
- رحلات سفر/شغل/IELTS كقصة الصفحة  
- «مجرد تغيير ألوان/فونت على نفس نصوص الإرث»

### Required CX outcome

Before coding, state in 3–6 bullets:

1. Who is the visitor (teacher role/context)?  
2. What job are they trying to do on **this** page?  
3. What one primary action? (diagnose / explore practice / choose plan / contact / start account)  
4. What proof/trust reduces doubt?  
5. How does the next step continue the loop?  

If you cannot answer these, **stop and ask** — do not restyle the old page.

## Visual system of truth (Lasles → TADRIS)

| Layer | Path |
|-------|------|
| Figma pull | `designs/laslesvpn-figma/` (frame `643:2318`) |
| Design mirror | `designs/public-pages/01-home.html` + `assets/lasles-theme.css` |
| Production CSS | `public/css/landing/lasles.css` |
| Production images | `public/img/lasles/` |
| Home Blade | `welcome` → `partials/welcome-lasles` → `partials/landing/lasles/*` |

**Classes:** `lasles-*` (extend this system). Do **not** introduce new purple/gold Sana skins or revive Atheer/teal for public pages.

**Legacy:** `sana-*` / `geo-*` HTML+CSS under `designs/public-pages/` and old Blade themes = **arch only** until a page is migrated. When migrating a page, replace with `lasles-*` + new CX copy — do not keep Sana as the target look.

Tokens, type scale, breakpoints: [tokens.md](tokens.md).

## Mandatory workflow

```
Public page task:
- [ ] 1. Read this skill + tadris-lab-platform (+ pages.md for the page type)
- [ ] 2. Write CX brief (5 bullets above) — product first
- [ ] 3. Open production Blade + lang keys (ar/en)
- [ ] 4. Open/create design mirror under designs/public-pages/
- [ ] 5. Rewrite structure + copy for teacher PD journey (lang keys)
- [ ] 6. Implement Blade with lasles-* + shared nav/footer patterns
- [ ] 7. Responsive 320 → 1440 (match tokens.md)
- [ ] 8. Sync mirror ↔ production CSS/classes
- [ ] 9. Done checklist below
```

### Implementation rules

1. **Partials** — split sections like home (`partials/landing/lasles/…`); do not dump a whole page in one mega file when it has >2 sections.  
2. **Copy** — put user-facing strings in `lang/ar` + `lang/en` (prefer `landing.php` / `public.php` / `common.php`). No Glottical/Sana leftovers on the migrated page.  
3. **Laravel** — keep routes, `@csrf`, validation, `old()`, auth gates, Eloquent loops.  
4. **Images** — `asset('img/lasles/…')` or existing media helpers; prefer Figma-exported assets already in `public/img/lasles/`.  
5. **Fonts** — stack: `"IBM Plex Sans Arabic", "Lato", "Rubik"` (Arabic primary → Latin headings → Rubik support).
6. **theme-color** — `#1E4E8C`.  
7. **RTL/LTR** — support `ar`/`en`; mirror spacing with logical properties (`margin-inline`, etc.).  
8. **Mirrors** — update `designs/public-pages/` when changing production visuals so the folder stays the design source for future agents.

### Page mapping (migrate toward Lasles)

| Intent | Design mirror | Typical Blade |
|--------|---------------|---------------|
| Home | `01-home.html` | `welcome.blade.php` + `partials/landing/lasles/*` |
| About | migrate `02-about.html` | `public/about.blade.php` |
| Paths / catalog | migrate `03-courses.html` | `courses.blade.php` (REPURPOSE → مسارات تطوير) |
| Pricing | migrate `04-pricing.html` | `public/pricing.blade.php` |
| Contact | migrate `05-contact.html` | `public/contact.blade.php` |
| Login | migrate `06-auth-login.html` | `auth/login.blade.php` |
| Register | migrate `07-auth-register.html` | `auth/register.blade.php` |
| Join as facilitator (if KEEP) | migrate `08-tutor-apply.html` | `tutor/apply.blade.php` — only if platform scope keeps it |
| Practitioners / mentors | migrate `09-instructors.html` | `instructors/index.blade.php` |

Auth may share Lasles tokens with a tighter form layout; do not keep geo purple/yellow as the long-term target.

## Done checklist (all required)

- [ ] CX brief documented in the reply (or PR notes) — not only CSS  
- [ ] Copy speaks to **المعلم** and the diagnose→access→develop→measure loop  
- [ ] No Glottical / Sana / student-tutoring marketing language on the page  
- [ ] Uses `lasles-*` (or shared extension of that system), fonts `"IBM Plex Sans Arabic", "Lato", "Rubik"`, blue/gold brand (`#1E4E8C` / `#A88050`)
- [ ] Section partials + lang keys updated (ar + en)  
- [ ] Design mirror updated or created  
- [ ] Responsive checked conceptually for ≤479, tablet, ≥1440 type scale  
- [ ] Primary CTA matches the page job; secondary CTA continues the loop  

## Anti-patterns (reject)

| Bad | Good |
|-----|------|
| Swap purple→red, keep «احجز حصة تجريبية» | CTA: ابدأ التشخيص / استكشف الممارسات / انضم كمعلم متعلّم |
| Keep Sana section order + old school story | Rebuild IA for teacher PD jobs |
| Invent a third design system | Extend `lasles.css` tokens/components |
| English-only or AR-only string hardcode | `lang/ar` + `lang/en` |
| Follow `sana-public-page-design` for new work | This skill supersedes it |

## Relationship to other skills

| Skill | Role |
|-------|------|
| `tadris-lab-platform` | What the product is; KEEP/REPURPOSE/DISABLE |
| **`tadris-lab-public-pages`** | How public CX + UI must be built |
| `sana-public-page-design` / `sana-landing-blade-implement` | **Deprecated** for new edits — arch reference only |

## Additional resources

- [pages.md](pages.md) — per-page CX briefs  
- [tokens.md](tokens.md) — colors, type, breakpoints, component patterns  
- [sitemap.md](sitemap.md) — expandable public IA (`config/tadris_nav.php`)  
- `designs/public-pages/README.md`  
- `designs/laslesvpn-figma/README.md`
