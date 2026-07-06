# PRD — Suria Solar Calculator (Standalone Subdomain App)

**Product name:** Suria Solar Calculator
**Client / Brand:** Suria Infiniti Sdn Bhd
**Platform:** Standalone web app, deployed to **calculator.suriainfiniti.com** on shared cPanel hosting — independent of the main WordPress (Hello Elementor child theme) site
**Tech stack:** Next.js (static export) frontend + PHP/MySQL API backend (see §5.5 for rationale)
**Prepared for:** Development in Cursor
**Status:** Draft v2.0 — standalone architecture
**Related docs:** `Suria-Infiniti-Website-Copywriting-Sitemap.docx` (Section 10 — Calculator Page), `design.md` (this project)

---

## 1. Background

Suria Infiniti's approved website copy already scopes a `/calculator/` page (see sitemap Section 10) with a simple 3-input, 4-output solar savings estimator. This PRD scopes it as a **standalone web app**, deployed on its own subdomain (`calculator.suriainfiniti.com`), separate from the main WordPress site — so it can:

- Run as a dedicated **ad-landing tool** — cold traffic from paid ads lands directly on the subdomain, isolated from the main WordPress site (no shared attack surface, no theme conflicts, no WordPress overhead slowing it down).
- Be built and deployed entirely from Cursor as a standalone codebase, uploaded to cPanel hosting.
- Capture leads (name, contact, estimate details) two ways: **email notification** to the sales team, and **persisted storage** in a database on the same hosting — so the data is available later for reporting/analysis, not just a one-off email that can get lost.
- Optionally grow into a multi-step wizard (inspired by competitor UX patterns observed in the market — flow only, not their copy/code) without a rebuild.

This is **Architecture Option A** (standalone app + subdomain), chosen over embedding the calculator as a WordPress plugin on the main domain (Option B), because:

| | Standalone subdomain (chosen) | WordPress plugin on main site |
|---|---|---|
| Speed | Static frontend, no CMS bootstrap — fastest possible for ad traffic | Depends on WordPress/theme overhead |
| Risk isolation | Ad traffic/bot attacks hit an isolated app, not the main brand site | Shared risk with main site |
| Maintenance | One extra codebase to maintain | Zero extra codebase, but tied to theme/plugin ecosystem |
| Hosting requirement | Works on any cPanel with PHP + MySQL (standard on virtually all shared hosting) | Requires WordPress specifically |

**Naming:** All references to third-party tools (e.g. "GetSolar") are for internal UX inspiration only. The shipped product is fully rebranded:

- Plugin name: **Suria Solar Calculator**
- Public-facing tool name: **Suria Solar Savings Calculator**
- No third-party logos, copy, or code are reused.

---

## 2. Goals

| # | Goal | Metric |
|---|------|--------|
| G1 | Let visitors self-estimate solar system size & savings in Malaysia | Completion rate of calculator ≥ 40% of sessions that start it |
| G2 | Convert calculator sessions into contactable leads | Lead form submit rate ≥ 15% of completions |
| G3 | Fast, isolated ad-landing experience | Loads in < 2s on 4G mobile; zero shared risk with main WordPress site |
| G4 | Safe to run behind paid ads traffic | Passes security checklist in §7 before launch |
| G5 | Leads never get lost | Every submission both emailed **and** stored in MySQL — dashboard/export available even if an email bounces |

## 3. Non-Goals (Out of Scope v1)

- Real satellite roof-drawing tool (like Google Maps roof tracing) — flagged as **Phase 3 / optional**, needs paid Maps API and is not required for MVP.
- Full CRM. We integrate with email/WhatsApp/CSV, not build a CRM.
- Multi-country support. Malaysia only (Solar ATAP rules), matching Suria Infiniti's current market.
- Payment processing.

---

## 4. Users

| Persona | Need |
|---|---|
| Homeowner (Residential) clicking a Facebook/Google ad | Quick, trustworthy estimate of savings & cost, low-friction way to enquire |
| Suria Infiniti sales team | Clean list of leads with the estimate context attached, so follow-up is informed; instant email alert per new lead |
| Suria Infiniti / owner | View, filter, and export all leads later for research/reporting (e.g. which state or property type converts best), without asking a developer for a DB export each time |

---

## 5. Scope — MVP (v1.0)

Matches the approved copywriting doc's Calculator page, upgraded from "static tool" to "lead-capturing tool."

### 5.1 Calculator flow (single page, no reload)

**Step 1 — Inputs** *(as per approved sitemap, kept intact)*
- Average Monthly Electricity Bill — slider, RM100–RM5,000
- Property / Site Type — dropdown: Terrace House / Semi-Detached / Small Commercial / Factory-Warehouse
- Roof Sun Exposure — dropdown: Excellent / Good / Moderate

**Step 2 — Results (auto-calculated, no page reload)**
- Recommended System Size (kWp)
- Estimated Monthly Savings (RM)
- Estimated Annual Savings (RM)
- Estimated Payback Period (years)
- Disclaimer (exact copy from sitemap): *"This calculator provides a general estimate only, based on typical Malaysian irradiance and Solar ATAP assumptions. Actual system size, cost and savings depend on your roof, consumption pattern and site assessment. Not a formal quotation."*
- CTA: **"Get My Exact Quote"**

**Step 3 — Lead capture (triggered by the CTA above, not before)**
- Full Name
- Phone / WhatsApp Number
- Email
- State (Malaysia dropdown)
- Consent checkbox: *"I agree to be contacted and have read the [Privacy Policy]"* (links to `/privacy-policy/`)
- Submit → shows confirmation + (optional) opens a pre-filled WhatsApp message to Suria Infiniti's sales number with a summary of their result.

> Note on legal accuracy: unlike older market tools that still reference "NEM 3.0," Suria Infiniti's calculator must reference **Solar ATAP** (effective 1 Jan 2026), matching the approved blog copy and FAQ.

### 5.2 Calculation logic (v1, editable by admin)

Simple, transparent formula-based estimate (no AI/ML needed):

```
1. Estimated kWh/month = Monthly Bill (RM) / Tariff Rate (RM/kWh)  [admin-configurable, default RM0.571/kWh]
2. Recommended System Size (kWp) = Estimated kWh/month × Sun Exposure Factor / (Avg Daily Sun Hours × 30 × Panel Derate)
   - Sun Exposure Factor: Excellent = 1.0, Good = 1.15, Moderate = 1.3
3. Estimated Annual Generation (kWh) = System Size (kWp) × Avg Daily Sun Hours × 365 × Panel Derate
4. Estimated Monthly Savings (RM) = min(Monthly Bill, Estimated Generation Value × Offset % )
5. Estimated Annual Savings (RM) = Monthly Savings × 12
6. Estimated System Cost (RM) = System Size (kWp) × Cost per kWp [admin-configurable, default RM 4,200/kWp]
7. Payback Period (years) = Estimated System Cost / Estimated Annual Savings
```

All constants (tariff rate, cost/kWp, sun hours, derate %, offset %) live in a small **config table / settings endpoint** on the backend, not hardcoded in the frontend build — so Suria Infiniti's ops team can adjust as TNB tariffs or costs change without a code redeploy. v1 can start as a single `config.php` (edited by the developer) and graduate to a simple protected settings screen in Phase 2 if the team wants self-service editing.

### 5.3 Admin / internal side

- **Leads viewer** (`/admin/` on the same subdomain, password/login-protected — see §7) — table of all submissions (name, contact, estimate summary, date, status), with:
  - Search/filter by date, state, property type
  - CSV export (for research/analysis in Excel/Sheets)
  - Mark lead status (New / Contacted / Quoted / Closed)
- **Email notification** — sales team gets an email per new lead, sent via SMTP (cPanel account email or a transactional service like Brevo/SendGrid free tier for better deliverability than raw PHP `mail()`).
- **Database backup** — every lead is written to MySQL *before* the email send is attempted, so a failed/delayed email never means a lost lead. Email failure is logged but does not block the lead from being saved.

### 5.4 Delivery / integration

- Single-page app at `calculator.suriainfiniti.com`, standalone from WordPress.
- Optional: a "Get a Quote" button on the main suriainfiniti.com site (WordPress) that simply links to the subdomain — no plugin/embed needed on the WordPress side at all.
- API endpoint (`/api/lead-submit`) is the only bridge between frontend and backend; no direct DB access from the browser.

### 5.5 Tech stack decision

| Layer | Choice | Why |
|---|---|---|
| Frontend | **Next.js**, built with `next export` (static HTML/CSS/JS) | Fast (pure static files, cacheable/CDN-able), and — critically — works on **any** cPanel hosting via simple file upload, with zero dependency on the host supporting a Node.js server. If the cPanel later turns out to support Node ("Setup Node.js App" in cPanel/WHM), the same Next.js project can instead run its own API routes and this splits into a simpler single-stack setup — but that's an upgrade path, not a v1 requirement. |
| Backend/API | **PHP + MySQL** | Every shared cPanel hosting account includes PHP and MySQL by default — no special hosting feature needs to be confirmed before starting. One small script validates input, writes to MySQL, and sends the email. |
| Hosting | cPanel (subdomain `calculator.suriainfiniti.com`, its own document root, own free SSL via AutoSSL/Let's Encrypt through cPanel) | Matches what's available to you right now; no new hosting purchase needed. |

> If, once you check, your cPanel *does* offer Node.js app support, tell me and I'll adjust this doc — it would let the whole app run as one Next.js codebase (frontend + API routes) instead of splitting Next.js + PHP. Both are fine; PHP+MySQL is simply the "guaranteed to work everywhere" default.

---

## 6. Scope — Phase 2 (optional, post-MVP)

Inspired by multi-step wizard UX patterns seen in the market (flow only):

- Convert Step 1 into a **multi-step wizard**: rooftop type → house storeys → meter phase (single/3-phase) → usage pattern (morning/evening vs. throughout the day) → bill amount → contact form. Each step = higher completion psychology (small commitments) but more build effort.
- **WhatsApp click-to-chat handoff** with a pre-filled message summarizing the visitor's estimate and their preference (Rent-to-Own vs Upfront), sent to Suria Infiniti's real WhatsApp Business number.
- **Rent-to-Own vs Upfront Purchase toggle** on results, if Suria Infiniti decides to offer financing (currently not confirmed in copywriting doc — flag as an open question, see §11).
- Optional roof-drawing map tool (Google Maps + Places Autocomplete) for a more visual estimate — needs a paid Google Maps API key and quota planning; treat as Phase 3.
- Optional webhook/Zapier/Make integration to push leads into Google Sheets or a CRM (e.g. HubSpot) automatically.

---

## 7. Security Requirements (mandatory before running paid ads traffic)

This tool will be public-facing and pushed via ads, so it is a spam/bot/attack target. Non-negotiable requirements:

1. **CSRF/replay protection on the API** — issue a short-lived token when the frontend loads the form (e.g. a signed token embedded at page load) and verify it server-side on submit; reject requests without a valid token.
2. **Server-side validation & sanitization** — never trust client input. Re-validate every field's type/format/range (e.g. bill amount 100–5,000, valid email format, phone format) in PHP even though the Next.js UI already restricts it — client-side checks are only a UX convenience, not security.
3. **Output escaping** — `htmlspecialchars()` on every value rendered back into HTML anywhere (leads viewer, admin screens) to prevent stored XSS from a malicious submission.
4. **Prepared statements only** — every MySQL query via PDO/mysqli **prepared statements** with bound parameters; never string-concatenate user input into SQL.
5. **CORS locked down** — the `/api/*` endpoints only accept requests with `Origin: https://calculator.suriainfiniti.com` (and `https://suriainfiniti.com` if the main site links to it); reject everything else, so no other site can silently call your API.
6. **Admin/leads-viewer authentication** — the leads page is not public. Protect it with either HTTP Basic Auth at the web-server level (`.htaccess`/cPanel password-protect directories) **or** a proper login (session + hashed password), not a "secret URL" alone.
7. **Spam/bot protection on the lead form** — honeypot field **and** Google reCAPTCHA v3/Cloudflare Turnstile (free). Reject submissions failing either check before writing to the DB or sending email.
8. **Rate limiting** — throttle lead submissions per IP (e.g. a rolling counter table or in-memory limiter) to blunt scripted abuse, independent of CAPTCHA.
9. **HTTPS-only** — enforce SSL on the subdomain (cPanel AutoSSL/Let's Encrypt is free); redirect any HTTP request to HTTPS at the server level.
10. **PDPA-aligned consent record** — store consent timestamp + a privacy-policy version string alongside each lead. Since this subdomain is separate from the main site, either mirror a short privacy notice on the calculator itself or link out to `suriainfiniti.com/privacy-policy/` — do not collect more fields than are disclosed there.
11. **No sensitive data exposure** — don't log full form payloads to error logs; if IP is stored for spam-scoring, store a hash rather than the raw IP where the raw value isn't specifically needed.
12. **`.env`/credentials never in the public web root** — DB credentials and API keys live outside the publicly-served directory (or in a `.env` file with server rules blocking direct HTTP access to it); never hardcode secrets into frontend JS bundles (anything in the Next.js frontend is publicly visible in the browser).
13. **Dependency hygiene** — keep the Next.js/npm dependencies and any PHP libraries current; avoid unmaintained packages with known CVEs.
14. **Backups** — since leads now live only in this app's own MySQL DB (not WordPress's), set up a recurring cPanel backup (cPanel's built-in Backup Wizard, or a cron `mysqldump`) so lead data isn't a single point of failure.

---

## 8. Hosting & Compatibility Requirements (cPanel + subdomain)

1. **Subdomain setup**: create `calculator.suriainfiniti.com` via cPanel → Domains/Subdomains, pointing to its own document root (e.g. `public_html/calculator/`), fully separate from the main WordPress install's folder.
2. **Static frontend deploy**: `next build && next export` produces a static `out/` folder — upload its contents to the subdomain's document root via File Manager, FTP, or (better) a small deploy script/Git pull if the host supports SSH.
3. **PHP API folder**: place the PHP API scripts in a subfolder (e.g. `/api/`) under the same document root, or a sibling folder outside static export if you prefer — either works since it's plain PHP served by cPanel's normal Apache/LiteSpeed + PHP handler, no special config needed.
4. **MySQL database**: create via cPanel → MySQL Databases (a DB, a DB user, and grant privileges) — standard cPanel flow, no root/shell access required.
5. **SSL**: enable AutoSSL (or install Let's Encrypt) for the subdomain so it serves HTTPS by default — required before any ad traffic is sent to it (§7.9).
6. Fully responsive (mobile-first) — this is where most ad traffic (Facebook/Instagram/TikTok in-app browsers) lands.
7. Zero browser console errors; PHP errors logged server-side only, never displayed to visitors (`display_errors = Off` in production).
8. Lighthouse/PageSpeed check before launch — static Next.js export should comfortably score well on mobile performance, which matters directly for ad quality score/cost.

---

## 9. Data Model

Single MySQL table, created directly on the calculator's own database (created via cPanel MySQL Databases, §8.4):

```sql
CREATE TABLE suria_calc_leads (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  created_at DATETIME NOT NULL,
  full_name VARCHAR(120) NOT NULL,
  email VARCHAR(191) NOT NULL,
  phone VARCHAR(30) NOT NULL,
  state VARCHAR(60) NULL,
  property_type VARCHAR(60) NULL,
  monthly_bill DECIMAL(10,2) NULL,
  roof_exposure VARCHAR(30) NULL,
  recommended_kwp DECIMAL(6,2) NULL,
  est_monthly_savings DECIMAL(10,2) NULL,
  est_annual_savings DECIMAL(10,2) NULL,
  payback_years DECIMAL(5,2) NULL,
  consent_at DATETIME NOT NULL,
  privacy_policy_version VARCHAR(20) NULL,
  utm_source VARCHAR(100) NULL,
  utm_campaign VARCHAR(100) NULL,
  status VARCHAR(20) DEFAULT 'new',
  ip_hash VARCHAR(64) NULL
);
```

---

## 10. Local Development → cPanel Deployment Workflow

**Recommended path — develop the exact same project you'll upload, no separate "port later" step:**

1. **Project structure** (built and run entirely from Cursor):
   ```
   suria-solar-calculator/
   ├── frontend/                 (Next.js app)
   │   ├── app/ or pages/        (calculator steps, results, lead form UI)
   │   ├── public/
   │   └── next.config.js        (output: 'export')
   ├── backend/                  (plain PHP, no framework needed for this scope)
   │   ├── api/
   │   │   ├── calculate.php     (optional: server-side recompute for validation)
   │   │   └── lead-submit.php   (validate → save to MySQL → send email)
   │   ├── config.php            (DB credentials, constants — NOT web-accessible, see §7.12)
   │   └── db/
   │       └── schema.sql        (§9 table definition)
   └── README.md                 (deploy steps, env setup)
   ```
2. **Local run:**
   - Frontend: `npm run dev` inside `frontend/` (Next.js dev server on `localhost:3000`) for fast UI iteration.
   - Backend: run PHP locally with `php -S localhost:8000 -t backend/` (PHP's built-in server — no need for full XAMPP/MAMP for a project this small, though either works if you prefer a GUI + local MySQL bundled together).
   - Local MySQL: XAMPP/MAMP's bundled MySQL, or a lightweight standalone MySQL/MariaDB install, just for the `suria_calc_leads` table during development.
   - Point the Next.js frontend's API calls at `http://localhost:8000/api/...` during local dev (via an environment variable), and at `https://calculator.suriainfiniti.com/api/...` in production — one config value to change, not code changes.
3. **Test the full loop locally**: fill the calculator → submit → confirm a row appears in local MySQL **and** a local test email is received (use Mailtrap or a similar dev inbox catcher so you're not spamming a real inbox while testing).
4. **Build for deploy:** `next build && next export` → produces `frontend/out/` (pure static files).
5. **Upload to cPanel:**
   - Contents of `frontend/out/` → subdomain's document root (e.g. `public_html/calculator/`).
   - Contents of `backend/api/` (and `config.php`, kept outside the public web root if the hosting layout allows, or protected via `.htaccess` deny-all if it must sit inside) → same subdomain, `/api/` path.
   - Import `schema.sql` into the MySQL database created via cPanel → phpMyAdmin.
   - Update `config.php` with the live DB credentials and live email settings.
6. **Version control (git) from commit #1** — even solo, this makes re-deploys and rollbacks trivial, and lets Cursor's AI features work with full project history/context.
7. Run the full §7 + §8 checklist once more on the live subdomain (SSL active, form submits correctly, email arrives, lead appears in DB, CORS locked to the real domain) **before** turning on ad spend.

---

## 11. Open Questions (need Suria Infiniti's confirmation)

- Does Suria Infiniti actually offer **Rent-to-Own / financing**, or only upfront purchase? (Copywriting doc doesn't mention Rent-to-Own; a competitor tool does — don't add it unless confirmed, to avoid promising something the business doesn't offer.)
- Confirm current TNB tariff rate to use as the default constant, and Suria Infiniti's typical **RM/kWp installed cost** (needed for the payback formula, §5.2) — these should come from the ops/sales team, not guessed.
- Confirm the WhatsApp Business number to use for lead handoff (sitemap lists `+603 6150 5399` as general phone; confirm if this is also the WhatsApp number).
- Confirm whether leads should also email the sales team, get logged in a CRM, or both.
- Check whether the cPanel hosting offers "Setup Node.js App" (visible in the cPanel dashboard) — if yes, the stack in §5.5 can be simplified to a single Next.js codebase instead of Next.js + separate PHP API. Not required to proceed with v1 either way.
- Confirm how "Get a Quote" should link between the main WordPress site and this subdomain (simple hyperlink is enough for v1 — no technical integration required between the two).
