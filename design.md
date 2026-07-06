# Design.md — Suria Solar Calculator

Design system for the Suria Solar Calculator plugin UI. Pulled directly from Suria Infiniti's own approved brand assets (`suria-infiniti-case-study-template.html`), so the calculator looks like a native part of suriainfiniti.com — not a bolted-on third-party widget.

---

## 1. Brand Tokens

### 1.1 Colors

| Token | Hex | Usage |
|---|---|---|
| `--si-navy` | `#0C2637` | Primary text, headings, dark surfaces |
| `--si-orange` | `#F47421` | Primary accent — buttons, active states, highlights, progress fill |
| `--si-orange-dark` | `#D9611A` | Hover/pressed state for orange elements |
| `--si-off-white` | `#FAF8F5` | Card backgrounds, section backgrounds |
| `--si-border` | `#ECECEC` | Borders, dividers |
| `--si-muted` | `#9AA3AC` | Secondary text, labels, helper copy |
| `--si-white` | `#FFFFFF` | Base background |
| `--si-success` | `#2E9E5B` *(new, for form validation states)* | Success/confirmation messages |
| `--si-error` | `#E0503A` *(new, for form validation states)* | Error/warning banners (matches the warm red disclaimer style seen in comparable tools) |

> All plugin CSS should declare these as CSS custom properties **scoped to `.si-calc-wrapper`**, not on `:root`, so the plugin never leaks variables into (or collides with) the rest of the Hello Elementor child theme's global styles.

```css
.si-calc-wrapper {
  --si-navy: #0C2637;
  --si-orange: #F47421;
  --si-orange-dark: #D9611A;
  --si-off-white: #FAF8F5;
  --si-border: #ECECEC;
  --si-muted: #9AA3AC;
  --si-success: #2E9E5B;
  --si-error: #E0503A;
  font-family: var(--si-font);
  color: var(--si-navy);
}
```

### 1.2 Typography

- **Font:** Montserrat (400, 500, 600, 700, 800) — same as the case study template.
- Check `wp_style_is('theme-font-handle')` before enqueuing Google Fonts again; if the theme doesn't already load Montserrat, enqueue once via `wp_enqueue_style`.

| Style | Size | Weight | Usage |
|---|---|---|---|
| H1 (tool title) | clamp(24px, 3.2vw, 36px) | 800 | "Estimate Your Solar Savings" |
| H2 (section headline) | clamp(20px, 2.4vw, 26px) | 800 | "Your Personalised Solar Estimate" |
| Eyebrow label | 12.5px | 700, uppercase, letter-spacing 0.08em | Small orange labels above headings |
| Body | 16px | 400–500 | Descriptions, disclaimers |
| Stat value | clamp(22px, 2.4vw, 30px) | 800, orange | kWp / RM output numbers |
| Stat label | 11.5px | 600, uppercase, muted | Labels under stat values |
| Button label | 15–16px | 700 | CTA buttons |

### 1.3 Spacing, radius, shadow

- Border radius: `8px` (inputs, buttons), `14–16px` (cards, hero blocks) — consistent with the case study template's `border-radius: 8px–16px` pattern.
- Card padding: `20–34px` (responsive `clamp()`), matching existing `.si-cs-facts` pattern.
- Shadow (hover only, not resting state): `0 8px 24px rgba(12,38,55,0.08)` — same as `.si-related-card:hover`.
- Max content width: `780–1240px` depending on section, matching the case study template's container widths.

---

## 2. Components

### 2.1 Step Progress Indicator
Numbered circles connected by a line (1 → 2 → 3...), active step filled `--si-orange`, completed steps filled `--si-navy` or a checkmark, upcoming steps outlined `--si-border`/`--si-muted`. Same visual language as a typical multi-step form — but restyle in brand colors, not the blue used by other tools in the market.

### 2.2 Input Slider (Monthly Bill)
- Track: `--si-border`, filled portion `--si-orange`.
- Value pill above the thumb showing "RM 500" live as it's dragged.
- Quick-select chips below (RM200 / RM300 / RM400 / RM500) — outlined pill buttons, active state filled navy text + orange underline.

### 2.3 Choice Cards (Property Type, Roof Exposure, etc.)
- Full-width rectangular cards, radio-style, one per row on mobile / 2-up on desktop where sensible.
- Resting: 1px `--si-border`, white background.
- Selected: 2px `--si-orange` border, faint orange-tinted background (`rgba(244,116,33,0.06)`), filled radio dot.
- Optional icon on the left (outline style, navy stroke) — keep icons simple/geometric, not skeuomorphic illustrations, to match Suria Infiniti's clean corporate tone rather than a cartoonish feel.

### 2.4 Results Summary Card
- Off-white card (`--si-off-white`), `--si-border` outline, `14px` radius.
- 3-column stat grid (kWp / Annual Generation / Annual Savings) exactly like `.si-cs-facts` styling already used in Suria Infiniti's case study template — reuse that pattern 1:1 for visual consistency across the site.
- Divider lines between stat columns: 1px `--si-border`.
- Disclaimer banner: soft warm background (`rgba(224,80,58,0.08)`) with `--si-error` left accent or icon, small text, italic optional — communicates "this is an estimate" without looking alarming.

### 2.5 Buttons

| Variant | Style |
|---|---|
| Primary CTA | Solid `--si-orange` fill, white text, 700 weight, `8px` radius, hover → `--si-orange-dark` |
| Secondary | Outline `--si-navy` border, navy text, transparent fill |
| Text link | Navy or orange underline, no fill |

Matches the existing `.elementor-button` styling already used for "Request a Quote" elsewhere on the site — the calculator's buttons should be visually identical to that, not a new button style.

### 2.6 Lead Capture Form
- Standard vertical form fields, `--si-border` outline inputs, `--si-orange` focus ring (`box-shadow: 0 0 0 3px rgba(244,116,33,0.15)`).
- Consent checkbox with inline link to `/privacy-policy/` — must render as a real clickable link, not just styled text.
- Submit button same as Primary CTA (§2.5).
- Inline validation messages in `--si-error`, success confirmation in `--si-success`.

### 2.7 Closing / WhatsApp Handoff (Phase 2)
- Reuse the site's existing closing CTA band pattern (`.si-cs-cta`): centered heading + subline + button, generous vertical padding (60px).
- WhatsApp button variant: `--si-success`-tinted or WhatsApp's own green is acceptable *only* for the WhatsApp icon/button specifically (universally recognized affordance), everything else on the page stays on-brand orange/navy.

---

## 3. Layout & Responsive Rules

- Mobile-first. Most ad traffic = mobile in-app browsers (Facebook/Instagram/TikTok).
- Breakpoints (align with existing template's media queries):
  - `≤560px` — single column everything, stat grid collapses to 2-up, larger tap targets (min 44px height on all interactive elements).
  - `≤900px` — 2-column where the desktop layout was 3–4 columns (facts grid, related cards).
  - `>900px` — full multi-column layout as designed.
- Calculator should sit inside a `max-width: 780–900px` centered column even on wide desktop screens — avoid stretching a form full-bleed, which hurts conversion.

---

## 4. Accessibility

- Minimum contrast: navy-on-white and white-on-orange both pass WCAG AA for body text (verify orange-on-white for small text — use navy text on orange backgrounds if a contrast check fails, not white).
- All interactive elements keyboard-navigable (native `<input>`, `<button>`, `<label for>` — avoid div-as-button patterns).
- Visible focus states (don't strip the browser's focus ring without replacing it — see §2.6 focus ring spec).
- Form errors announced via `aria-live="polite"` region, not color alone.

---

## 5. Next.js / Standalone-App Implementation Notes

- The tool is a fully standalone app on its own subdomain — no theme/CSS reset to defend against — but still wrap the root layout in one class (`.si-calc-wrapper`) so styles stay predictable and portable if the component set is ever reused elsewhere (e.g. embedded as an `<iframe>` on the main WordPress site, or ported into a future redesign).
- Implement brand tokens as CSS variables on `:root` (or the layout wrapper) via a single `globals.css` — no CSS-in-JS runtime overhead needed for a page this simple; plain CSS Modules or Tailwind (configured with the §1.1 tokens as the theme palette) both work well with Next.js static export.
- Keep the whole app to a handful of routes/screens (calculator → results → lead form → confirmation) rendered client-side after a single static HTML shell loads — this is what makes `next export` possible and keeps load times low for ad traffic.
- Optimize images (hero/illustration assets, if any) as compressed SVG/WebP and served from the same static export — no image CDN dependency required for a page this size.
- If the "Get a Quote" link from the main suriainfiniti.com WordPress site should ever open the calculator inline instead of a new tab, use a simple `<iframe src="https://calculator.suriainfiniti.com">`, not a copy-paste of code into WordPress — keeps the "one codebase" principle from the PRD intact.

---

## 6. Visual Reference Mapping

| Calculator element | Existing Suria Infiniti asset to match |
|---|---|
| Stat grid / key facts | `.si-cs-facts` / `.si-fact` pattern (case study template) |
| Eyebrow labels | `.si-eyebrow` styling (case study template) |
| Closing CTA band | `.si-cs-cta` pattern (case study template) |
| Buttons | `.elementor-button` styling already in use |
| Certification-style pills (if used for trust badges) | `.si-cert-pill` pattern |
| Colors & font | Root CSS variables + Google Fonts import block already defined in the case study template's `<style>` |

Keeping this 1:1 mapping means the calculator will feel like it was designed alongside the rest of the site, not bolted on afterward.
