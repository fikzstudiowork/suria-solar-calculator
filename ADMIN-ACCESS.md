# Akses Admin Dashboard

Admin **bukan** di Vercel — ia ada dalam folder **PHP backend**. Vercel hanya host calculator (frontend).

## Production (cPanel)

1. Upload folder `backend/` ke hosting cPanel
2. Import database: `backend/db/schema.sql`
3. Copy `backend/config.example.php` → `backend/config.php` (isi DB password)
4. Jalankan setup admin:
   ```bash
   php backend/setup.php
   ```
5. Login di:
   ```
   https://calculator.suriainfiniti.com/admin/
   ```
   (atau URL backend PHP anda)

### Menu Admin
| Page | URL |
|------|-----|
| Login | `/admin/login.php` |
| Leads Dashboard | `/admin/dashboard.php` |
| Site Settings (logo, email, WhatsApp) | `/admin/settings.php` |

## Tukar Logo / Email / WhatsApp

Login admin → **Site Settings**:
- **Logo Upload** — upload PNG/JPG/SVG
- **Contact Email** — email paparan header
- **WhatsApp Number** — nombor digits (60361505399)
- **WhatsApp Prefill Message** — mesej auto (contoh: solarenergy)
- **Sales Email** — email notifikasi lead baru

Simpan → perubahan reflect di calculator (frontend fetch dari API).

## Local (tanpa cPanel)

Admin PHP perlukan XAMPP/WAMP. Untuk dev calculator sahaja:
```powershell
npm run dev
```
Settings local disimpan dalam `dev-data/settings.json`.
