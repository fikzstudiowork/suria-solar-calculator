# Fix: calculator.suriainfiniti.com Tak Buka (DNS NXDOMAIN)

## Apa masalahnya?

Error **`DNS_PROBE_FINISHED_NXDOMAIN`** = subdomain **`calculator.suriainfiniti.com` belum wujud dalam DNS**.

- Domain utama `suriainfiniti.com` ✅ wujud (hosting cPanel)
- Subdomain `calculator` ❌ **belum dicipta**

Calculator **sudah live** di Vercel:  
👉 **https://suria-solar-calculator.vercel.app**

Admin **`/admin/`** perlukan **PHP + MySQL di cPanel** — Vercel **tak boleh** run admin.

---

## Pilihan A — Guna Vercel URL dulu (paling cepat, 0 minit)

Buka terus: **https://suria-solar-calculator.vercel.app**

Calculator berfungsi. Admin **belum** boleh guna sehingga backend PHP deploy (Pilihan B/C).

---

## Pilihan B — Custom domain + Admin (recommended)

Deploy **semua** ke cPanel subdomain `calculator` — frontend + backend + admin dalam satu tempat.

### Langkah 1: Cipta subdomain dalam cPanel

1. Login **cPanel** hosting `suriainfiniti.com`
2. Pergi **Domains → Subdomains** (atau **Subdomain**)
3. Cipta subdomain:
   - **Subdomain:** `calculator`
   - **Domain:** `suriainfiniti.com`
   - **Document Root:** `public_html/calculator` (auto)
4. Klik **Create**

Ini auto-cipta DNS record untuk `calculator.suriainfiniti.com`.

### Langkah 2: Build frontend (di komputer anda)

```powershell
cd "C:\Users\fikzm\OneDrive\Desktop\Suria Infiniti\Solar Calculator\frontend"
npm install
npm run build
```

Output ada dalam folder `frontend/out/`

### Langkah 3: Upload ke cPanel

Via **File Manager** atau **FTP**, upload ke `public_html/calculator/`:

| Upload dari | Ke folder cPanel |
|-------------|------------------|
| `frontend/out/*` | `public_html/calculator/` (root subdomain) |
| `backend/api/` | `public_html/calculator/api/` |
| `backend/admin/` | `public_html/calculator/admin/` |
| `backend/includes/` | `public_html/calculator/includes/` |
| `backend/.htaccess` | `public_html/calculator/` |
| `backend/db/schema.sql` | import via phpMyAdmin |

### Langkah 4: Database + config

1. **phpMyAdmin** → buat database `suria_calc` → import `schema.sql`
2. Copy `backend/config.example.php` → `config.php` (isi DB user/password)
3. Upload `config.php` **luar** public folder jika boleh, atau protect dengan `.htaccess`

### Langkah 5: Setup admin user

Via **Terminal cPanel** atau SSH:

```bash
cd ~/public_html/calculator
php setup.php
```

Buat username + password admin. **Padam `setup.php`** selepas siap.

### Langkah 6: SSL

cPanel → **SSL/TLS Status** → Run **AutoSSL** untuk `calculator.suriainfiniti.com`

### Selepas siap, buka:

| Page | URL |
|------|-----|
| Calculator | https://calculator.suriainfiniti.com |
| Admin login | https://calculator.suriainfiniti.com/admin/ |
| API | https://calculator.suriainfiniti.com/api/ |

Tunggu **5–30 minit** selepas cipta subdomain untuk DNS propagate.

---

## Pilihan C — Vercel frontend + cPanel admin (split)

Kalau nak kekal guna Vercel untuk calculator:

### 1. DNS untuk calculator (frontend Vercel)

cPanel → **Zone Editor** → Add Record:

| Type | Name | Value |
|------|------|-------|
| **CNAME** | `calculator` | `cname.vercel-dns.com` |

Vercel → Project → **Settings → Domains** → add `calculator.suriainfiniti.com`

### 2. Admin di subdomain berasingan

cPanel → cipta subdomain **`admin.suriainfiniti.com`** → upload folder `backend/` sahaja.

Admin URL jadi: **https://admin.suriainfiniti.com/admin/**

⚠️ **`calculator.suriainfiniti.com/admin/` tak akan jalan** jika domain point ke Vercel — Vercel static sahaja, tiada PHP.

---

## Checklist cepat

- [ ] Subdomain `calculator` dicipta dalam cPanel
- [ ] File frontend + backend uploaded
- [ ] MySQL database imported
- [ ] `config.php` configured
- [ ] `php setup.php` run + admin password set
- [ ] AutoSSL enabled
- [ ] Test: https://calculator.suriainfiniti.com
- [ ] Test: https://calculator.suriainfiniti.com/admin/

---

## Perlukan bantuan?

Hantar screenshot:
1. cPanel → Subdomains (senarai subdomain)
2. cPanel → Zone Editor (DNS records untuk calculator)

Saya boleh guide step seterusnya.
