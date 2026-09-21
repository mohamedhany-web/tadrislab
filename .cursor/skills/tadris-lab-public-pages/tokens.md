# Lasles / TADRIS LAB — public visual tokens

Source CSS: `public/css/landing/lasles.css`  
Brand: official logo blue + gold

## Colors (logo)

| Role | CSS var | Hex |
|------|---------|-----|
| Primary blue | `--lasles-blue` | `#1E4E8C` |
| Blue deep | `--lasles-blue-deep` | `#184888` |
| Blue soft | `--lasles-blue-soft` | `#2A5FA0` |
| Blue tint | `--lasles-blue-tint` | `#E8EEF6` |
| Accent gold | `--lasles-gold` | `#A88050` |
| Gold soft | `--lasles-gold-soft` | `#B8956A` |
| Gold tint | `--lasles-gold-tint` | `#F7F1E8` |
| Navy text | `--lasles-navy` | `#152A4A` |
| Muted | `--lasles-muted` | `#3F4A5A` (readable body/card text) |

`theme-color`: `#1E4E8C`

Primary CTAs / fills → blue. Outlines, kickers, highlights → gold.

## Type (home / marketing)

| Locale | Font | Notes |
|--------|------|-------|
| `ar` (أساسي) | **IBM Plex Sans Arabic** | نصوص عربية، عناوين، وواجهة |
| `en` / Latin | **Lato** | إنجليزي وعناوين لاتينية |
| دعم إضافي | **Rubik** | fallback في الـ stack |

CSS stack:

```css
font-family: "IBM Plex Sans Arabic", "Lato", "Rubik", system-ui, sans-serif;
```

| Token | Desktop target |
|-------|----------------|
| `--fs-body` | ~17–19px |
| `--fs-lead` | ~18–20px |
| `--fs-card` | ~16–17px |
| `--fs-price` | dominant in plan cards (~31–40px) |
| `--lh-body` / `--lh-card` | ~1.68–1.72 (Arabic leads ~1.8) |
| `--lasles-section-y` | ~60–96px; pricing/features get extra breathing room |

## Section rhythm (avoid “card after card”)

- Path / services: lighter surfaces (icon rows, not identical white cards)
- Features / institutions: tinted bands + stronger headlines
- Pricing: white stage, featured plan elevated (~12px) + clear badge
- Final CTA: blue gradient block (not another white card)

## Primary CTA vocabulary (home)

1. **ابدأ رحلتك** / Start your journey — always blue primary  
2. **استكشف البرامج** / Explore programs — always outline secondary  
3. Contact used only where B2B/support needs it
