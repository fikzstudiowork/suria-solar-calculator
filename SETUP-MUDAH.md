# Panduan Setup — Suria Solar Calculator

Panduan mudah untuk test di komputer anda (Windows).

---

## Langkah 1: Buka Terminal

Dalam Cursor, tekan **Ctrl + `** (backtick) untuk buka terminal.

---

## Langkah 2: Install & Jalankan (satu command)

```powershell
cd "C:\Users\fikzm\OneDrive\Desktop\Suria Infiniti\Solar Calculator"
npm install
npm run dev
```

Tunggu sehingga nampak:
- `Suria Solar Calculator — DEV API` → http://localhost:8000
- `Ready` → http://localhost:3000

---

## Langkah 3: Buka Calculator

Buka browser, pergi ke:

**http://localhost:3000**

Anda boleh:
1. Adjust bill slider
2. Pilih property type & roof exposure
3. Klik **Calculate My Savings**
4. Klik **Get My Exact Quote** → isi borang → submit
5. Lihat confirmation + butang WhatsApp

---

## Lead disimpan di mana?

Semasa test local, lead disimpan dalam fail:

`dev-data/leads.json`

Buka fail tu untuk lihat semua submission.

---

## Admin Dashboard (Superadmin)

**URL (production):** `https://calculator.suriainfiniti.com/admin/`

| Page | URL | Fungsi |
|---|---|---|
| Login | `/admin/login.php` | Sign in |
| Leads | `/admin/dashboard.php` | Lihat leads, filter, status, WhatsApp chat, export CSV |
| Settings | `/admin/settings.php` | Tukar logo, email, phone, WhatsApp, company profile, stats |

Setup admin password:
```bash
cd backend
php setup.php
```

**Local dev:** Admin PHP perlu XAMPP. Untuk test calculator sahaja, guna `npm run dev` — leads disimpan dalam `dev-data/leads.json`.

**Site settings local:** Edit `dev-data/settings.json` untuk tukar contact/WhatsApp semasa dev.

---

## Deploy ke cPanel (nanti)

Bila ready untuk live:

1. **Build frontend:**
   ```powershell
   cd frontend
   npm run build
   ```
   Upload isi folder `frontend/out/` ke subdomain calculator.

2. **Upload backend PHP** ke `/api/` dan `/admin/` di hosting.

3. **Import database** `backend/db/schema.sql` via phpMyAdmin.

4. **Edit** `backend/config.php` dengan DB credentials hosting.

5. **Setup admin:** `php setup.php` (sekali sahaja, then padam fail tu).

6. **Daftar Cloudflare Turnstile** keys untuk production.

---

## Masalah biasa

| Masalah | Penyelesaian |
|---|---|
| Port 3000 sudah digunakan | Tutup app lain, atau restart terminal |
| `npm run dev` error | Pastikan Node.js installed (`node -v`) |
| Form submit gagal | Pastikan dev API jalan (localhost:8000) |
| Page blank | Refresh browser, check terminal untuk error |

---

## Perlukan bantuan deploy?

Bila anda ada akses cPanel Suria Infiniti, beritahu saya — saya guide step-by-step upload ke `calculator.suriainfiniti.com`.
