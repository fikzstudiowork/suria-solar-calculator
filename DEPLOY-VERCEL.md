# Deploy Frontend ke Vercel + Backend ke cPanel

## Apa yang deploy di mana?

| Bahagian | Platform | URL |
|----------|----------|-----|
| **Frontend** (calculator UI) | Vercel | `https://calculator.suriainfiniti.com` |
| **Backend** (PHP API + Admin) | cPanel | `https://calculator.suriainfiniti.com/api/` |

Frontend di Vercel panggil API PHP di cPanel melalui `NEXT_PUBLIC_API_URL`.

---

## 1. GitHub (siap selepas push)

Repo: **https://github.com/fikzstudiowork/suria-solar-calculator**

---

## 2. Vercel — Import dari GitHub

1. Pergi [vercel.com/new](https://vercel.com/new) → login dengan GitHub
2. **Import** repo `fikzstudiowork/suria-solar-calculator`
3. **Root Directory** → klik Edit → pilih folder **`frontend`**
4. **Environment Variables** (wajib):

   | Name | Value |
   |------|-------|
   | `NEXT_PUBLIC_API_URL` | `https://calculator.suriainfiniti.com` |
   | `NEXT_PUBLIC_TURNSTILE_SITE_KEY` | Site key Cloudflare Turnstile (production) |

5. Klik **Deploy**

Selepas deploy, Vercel beri URL preview (contoh: `suria-solar-calculator.vercel.app`).

---

## 3. Custom domain

Dalam Vercel project → **Settings → Domains**:
- Tambah `calculator.suriainfiniti.com`
- Ikut arahan DNS (CNAME ke `cname.vercel-dns.com`)

Backend PHP kekal di subdomain yang sama atau subfolder — setup DNS mengikut hosting cPanel anda.

---

## 4. Backend PHP (cPanel)

Upload folder `backend/` ke cPanel, import `backend/db/schema.sql`, copy `config.example.php` → `config.php`, jalankan `php setup.php`.

Pastikan CORS dalam `config.php` include domain Vercel:

```php
'allowed_origins' => [
    'https://calculator.suriainfiniti.com',
    'https://suria-solar-calculator.vercel.app',
],
```

---

## Auto-deploy

Setiap push ke branch `main` → Vercel auto-rebuild frontend.
