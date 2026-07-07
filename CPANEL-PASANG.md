# 🚀 Pasang ke cPanel — calculator.suriainfiniti.com

Folder cPanel anda: **`/home/suriainfiniti/calculator.suriainfiniti.com`**

---

## Step 1 — Build package (di komputer)

Double-click atau run:

```
deploy\build-cpanel.bat
```

Atau PowerShell:

```powershell
cd "C:\Users\fikzm\OneDrive\Desktop\Suria Infiniti\Solar Calculator"
.\deploy\build-cpanel.bat
```

Hasil:
- Folder: `deploy\cpanel-upload\` (semua file siap)
- ZIP: `deploy\suria-calculator-cpanel.zip` (senang upload)

---

## Step 2 — Upload ke cPanel

1. Login cPanel → **File Manager**
2. Buka folder **`calculator.suriainfiniti.com`**
3. **Padam** folder kosong `.well-known` dan `cgi-bin` jika perlu (optional)
4. Klik **Upload**
5. Upload file **`suria-calculator-cpanel.zip`**
6. Selepas upload, **right-click ZIP** → **Extract**
7. Extract ke folder **`calculator.suriainfiniti.com`** (root subdomain)

Selepas extract, folder patut ada:
```
index.html
_next/          ← WAJIB! Tanpa ini page rosak (CSS/JS)
images/
api/
admin/
includes/
.htaccess
config.example.php
setup.php
schema.sql
uploads/
```

> ⚠️ **PENTING:** Folder `_next` mula dengan underscore — dalam File Manager, klik **Settings** (kanan atas) → tick **Show Hidden Files**. Pastikan folder `_next` wujud selepas extract. Kalau tiada, upload manual dari `deploy/cpanel-upload/_next`.

Test: buka `https://calculator.suriainfiniti.com/_next/static/css/` — patut nampak file `.css`, bukan page calculator.

---

## Step 3 — Database (phpMyAdmin)

1. cPanel → **MySQL Databases**
2. **Create Database:** `suria_calc` (atau nama lain)
3. **Create User** + password → **Add User to Database** (ALL PRIVILEGES)
4. cPanel → **phpMyAdmin** → pilih database → **Import**
5. Import file **`schema.sql`** (dari folder subdomain atau upload dari komputer)

---

## Step 4 — Config PHP + PHP extensions

### 4a. Enable PDO MySQL (WAJIB — fix "could not find driver")

1. cPanel → **MultiPHP Manager**
2. Tick subdomain **`calculator.suriainfiniti.com`** → pilih **PHP 8.1** atau **8.2**
3. cPanel → **Select PHP Version** (atau **PHP Extensions**)
4. **Tick / enable:**
   - `pdo`
   - `pdo_mysql`
   - `curl`
   - `mbstring`
   - `json`
5. **Save**

Test: buka `https://calculator.suriainfiniti.com/check-health.php` — PDO MySQL patut **OK**.

### 4b. Edit config.php

1. File Manager → folder `calculator.suriainfiniti.com`
2. **Copy** `config.example.php` → rename copy to **`config.php`**
3. **Edit** `config.php` — isi bahagian `db`:

```php
'db' => [
    'host' => 'localhost',
    'name' => 'suriainfiniti_suria_calc',  // nama DB cPanel (biasanya prefix_account)
    'user' => 'suriainfiniti_dbuser',      // user DB cPanel
    'pass' => 'PASSWORD_ANDA',
    'charset' => 'utf8mb4',
],
```

4. Tukar `'csrf' => ['secret' => '...']` — random string panjang
5. Save

---

## Step 5 — Buat admin user

**Cara mudah (browser):** buka  
`https://calculator.suriainfiniti.com/setup-web.php`  
→ isi username + password → Create Admin User

**Atau Terminal cPanel:**

```bash
cd ~/calculator.suriainfiniti.com
php setup.php
```

**Padam `setup.php` dan `setup-web.php`** selepas siap.

---

## Step 6 — SSL

cPanel → **SSL/TLS Status** → Run **AutoSSL** untuk `calculator.suriainfiniti.com`

---

## Step 7 — Test

| URL | Patut |
|-----|-------|
| https://calculator.suriainfiniti.com | Calculator wizard |
| https://calculator.suriainfiniti.com/admin/ | Admin login |
| https://calculator.suriainfiniti.com/api/site-settings.php | JSON settings |

---

## Tukar logo / email / WhatsApp

Login admin → **Site Settings** → upload logo, edit email `info@suriainfiniti.com`, WhatsApp `60361505399`.

---

## Masalah biasa

| Masalah | Fix |
|---------|-----|
| Blank page | Check `.htaccess` uploaded, enable mod_rewrite |
| 500 error API | Check `config.php` DB credentials |
| Admin login fail | Run `php setup.php` again |
| Calculator tak load API | Pastikan `config.php` betul + database imported |

---

**Nota:** Saya tak boleh login cPanel anda secara langsung — ikut step di atas, hantar screenshot jika stuck di step mana-mana.
