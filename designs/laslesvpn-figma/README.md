# LaslesVPN Figma freebie — pulled reference

Source: [Figma community / LaslesVPN Landing](https://www.figma.com/design/k2x0bOJmRyOHX1CRgTwrMD/FREEBIES-Landingpage-LaslesVPN--Community-?node-id=643-2318)

Pulled node: `643:2318` (Landingpage frame)  
Date: 2026-09-09

## Files

- `design-context.tsx` — React+Tailwind reference from Figma MCP (adapt to Blade; do not paste as-is)
- `assets/` — 110 exported PNG/SVG assets (local copies of MCP URLs)
- `assets-manifest.tsv` — const name → filename map
- `tokens.css` — color preset (`#F53838`, `#0B132A`, `#4F5665`, …)

## Production wiring (homepage)

- CSS: `public/css/landing/lasles.css` (+ mirror `designs/public-pages/assets/lasles-theme.css`)
- Images: `public/img/lasles/`
- Blade: `welcome.blade.php` → `partials/welcome-lasles.blade.php` → `partials/landing/lasles/*`
- Design mirror: `designs/public-pages/01-home.html`

## Design tokens (from design)

| Role | Value |
|------|--------|
| Primary red | `#F53838` |
| Dark text | `#0B132A` |
| Muted text | `#4F5665` |
| Soft red / bg tint | `#FFECEC` / `#F53855` accents |
| Card border (active) | `#F53838` |
| Font | Rubik |
| Max content width | ~1140–1200px |

## Page sections (top → bottom)

1. Navbar — logo, About/Features/Pricing/Testimonials/Help, Sign In, Sign Up
2. Hero — headline + Get Started + illustration
3. Stats bar — Users / Locations / Servers
4. Features — illustration + checklist
5. Pricing — Free / Standard / Premium cards
6. Global network map + sponsor logos
7. Testimonials carousel
8. Subscribe CTA strip
9. Footer — logo, Product / Engage / Earn Money, social

## Next step for TADRIS LAB

Adapt layout + tokens into public Blade (`welcome` / landing), rename brand copy to **تدريس لاب / TADRIS LAB**, keep structure and visual language.
