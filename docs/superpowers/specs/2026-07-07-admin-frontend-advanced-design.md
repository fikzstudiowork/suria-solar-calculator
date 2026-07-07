# Admin Dashboard + Frontend Calculator — Advanced Redesign

**Date:** 2026-07-07
**Status:** Approved (Phase 1) — Phases 2-5 are roadmap-level, to be brainstormed individually before build.

## Background

The admin dashboard (`backend/admin/`) is a functional but visually basic PHP tool (plain tables, inline CSS, single-page layout). The customer-facing calculator (`frontend/`) is a Next.js 15 wizard that works well but was requested to feel more premium/advanced. Both need incremental improvement without disrupting the live production tool used daily for lead management.

## Goals

- Give the admin dashboard a modern, professional look and a foundation (sidebar nav, design tokens) that later phases (analytics, lead management, multi-user, automation) can build on.
- Give the calculator wizard a premium feel through motion and micro-interactions, without adding heavy dependencies (keeps static export lean).
- Ship in small, independently deployable phases so risk stays low on a live business tool.

## Non-Goals (for this spec / Phase 1)

- No new npm/composer dependencies (no animation libraries, no charting libraries yet).
- No database schema changes.
- No analytics, multi-user roles, automation, or AI roof detection — those are Phase 2+ and will get their own design pass when their turn comes.

## Roadmap

### Admin Dashboard
| Phase | Scope |
|---|---|
| **1 (this spec)** | Visual redesign — sidebar nav, design tokens, cards, table polish, mobile collapse |
| 2 | Analytics — charts for leads over time, conversion by state/roof type, funnel |
| 3 | Lead management — notes per lead, pipeline/Kanban status, follow-up reminders |
| 4 | Multi-user — admin/staff roles, lead assignment |
| 5 | Automation — WhatsApp/email templates, auto-assign, reminder sequences |

### Frontend Calculator
| Phase | Scope |
|---|---|
| **1 (this spec)** | Visual premium — step transitions, count-up results, micro-interactions |
| 2 | Trust content — case studies, before/after bill comparison, FAQ |
| 3 | Conversion optimization — exit-intent, save/resume progress, BM/EN toggle |
| 4 | Mobile app feel — PWA installable, smooth mobile performance |
| 5 | Smart calculator — roof photo upload attached to lead (no AI detection yet) |

Each future phase gets its own brainstorming session and spec before implementation.

---

## Phase 1 Design: Admin Dashboard

### Current structure
- `backend/admin/includes/layout.php` — shared `adminHeader()`/`adminFooter()` with inline `<style>`, top horizontal nav bar.
- `backend/admin/dashboard.php`, `settings.php` — page content between header/footer.
- No JS framework; pure PHP + vanilla CSS + minimal inline `<script>` where needed (e.g. status dropdown auto-submit).

### New layout
- **Sidebar** (240px fixed, `#0C2637` navy background): logo/brand at top, nav links (Dashboard, Settings — with visual slots reserved for Analytics/Pipeline in later phases), logout at bottom.
- **Content area**: right of sidebar, off-white background (`#FAF8F5`), generous padding (32px desktop).
- **Mobile (<768px)**: sidebar collapses off-canvas; a hamburger toggle (`<input type="checkbox">` + CSS `:checked` sibling selector — no JS dependency) slides it in as an overlay.

### Visual system
- Introduce CSS custom properties in `layout.php` `<style>` block for color/spacing tokens (`--si-navy`, `--si-orange`, `--si-bg`, `--si-radius`, `--si-shadow`) to keep the redesign consistent and easy to extend in later phases.
- **Stat cards**: white cards with soft shadow (not hard border), icon glyph + value + label, optional trend line reserved for Phase 2 (no live trend data yet, static layout ready).
- **Leads table**: row hover highlight, status shown as colored pill badge instead of a plain dropdown-only view (dropdown still available inline for changing status), increased row padding for readability.
- **Forms (settings page)**: consistent input styling, clearer section grouping with card headers.

### Responsive & accessibility
- Sidebar nav collapses on mobile as described above.
- Table becomes horizontally scrollable on narrow screens instead of squeezing columns unreadably.
- Maintain sufficient color contrast (navy/orange on white already passes WCAG AA for text sizes used).

### Testing
- Manual verification: log in to `/admin/dashboard.php` and `/admin/settings.php` on desktop and mobile widths (browser devtools + real phone check) after deploy.
- No automated tests exist for admin PHP; this is consistent with current project conventions.

---

## Phase 1 Design: Frontend Calculator

### Current structure
- `frontend/app/page.tsx` — wizard state machine (`wizardStep` 1-6), conditionally renders step components.
- `frontend/components/WizardStepper.tsx` — numbered circle progress indicator.
- `frontend/components/ResultsCard.tsx` / `ResultsPage.tsx` — static results display.
- No animation library in `package.json` (Next 15, React 19, Tailwind only).

### Step transitions
- Track navigation `direction` (`'forward' | 'backward'`) in state, set when `goNext`/`goPrevious` are called.
- Wrap step content in a container that applies a CSS keyframe animation keyed by `wizardStep` (remount via `key={wizardStep}` triggers enter animation): slide-in from right + fade for forward, slide-in from left + fade for backward. Pure Tailwind `@keyframes` + `animate-*` utility classes added to `tailwind.config`, no JS animation library.
- `WizardStepper` progress line animates width via CSS `transition` when `currentStep` changes (already a simple bar/circle system — enhance with a filled connector line that transitions smoothly).

### Results page "wow" moment
- New `useCountUp(target: number, durationMs = 800)` hook (plain `requestAnimationFrame`, ease-out cubic) used for kWp, monthly savings, annual savings, and payback years in `ResultsCard.tsx`.
- Numbers animate from 0 to their final value once, on mount of the results view.

### Micro-interactions
- Buttons (`si-btn-primary` and friends): subtle scale (`active:scale-[0.98]`) + shadow lift on hover, via Tailwind transition utilities already partially present.
- Choice cards (`RoofTypeChoice`, `VerticalChoice`, `ChoiceCards`): smoother border/background color transition on selection (increase `transition` duration/easing consistency across all three components).
- Loading state: while `fetchConfig()` is in flight on initial load, show a lightweight pulse/skeleton placeholder instead of a blank step, so the wizard doesn't feel like it's stalled.

### Testing
- Manual check across the 6 wizard steps + results page on desktop and mobile (Chrome devtools + live site) after deploy: verify transitions feel smooth, no layout shift, count-up animation runs once and settles on correct final value, no console errors.
- Existing dev API (`scripts/dev-api.mjs`) used for local testing before deploy, matching current project workflow.

---

## Rollout Plan

1. Implement Admin Phase 1 (layout.php + dashboard.php + settings.php CSS/markup updates).
2. Implement Frontend Phase 1 (page.tsx, WizardStepper, ResultsCard, choice components, new `useCountUp` hook, Tailwind keyframes).
3. Build frontend (`npm run build`), package backend, deploy both via `deploy/upload-fix.ps1` (or GitHub Actions if secrets configured).
4. Manual smoke test on live site (admin login + dashboard + settings; calculator wizard steps 1-6 + results).
5. Commit and push changes to `main`.
6. Report completion; queue Phase 2 brainstorming (Admin Analytics, Frontend Trust Content) for next session.
