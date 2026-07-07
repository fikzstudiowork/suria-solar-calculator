# Deploy via GitHub — Auto update calculator.suriainfiniti.com

Bila anda push code ke branch `main`, GitHub Actions akan **build + upload** ke cPanel automatically.

## Setup sekali (5 minit)

### 1. Push repo ke GitHub
Repo: `https://github.com/fikzstudiowork/suria-solar-calculator`

```powershell
git add .
git commit -m "your message"
git push origin main
```

### 2. Tambah GitHub Secrets
GitHub repo → **Settings** → **Secrets and variables** → **Actions** → **New repository secret**

| Secret | Value |
|--------|--------|
| `FTP_SERVER` | `suriainfiniti.com` |
| `FTP_USERNAME` | `calculator@calculator.suriainfiniti.com` |
| `FTP_PASSWORD` | (password FTP cPanel anda) |
| `TURNSTILE_SITE_KEY` | (optional — Cloudflare Turnstile site key production) |

### 3. Test deploy
Push satu perubahan kecil → **Actions** tab → lihat workflow **Deploy to cPanel**

---

## Apa yang auto-deploy?

| Deploy | Tidak overwrite |
|--------|-----------------|
| Frontend (`index.html`, `next/`) | `config.php` (DB password) |
| PHP backend (`admin/`, `api/`, `includes/`) | `uploads/` (logo user upload) |
| `.htaccess` | Database data |

---

## Workflow harian

```
Edit code locally → git push → GitHub Actions deploy (~2-3 min) → site updated
```

**Manual deploy** (backup):
```powershell
powershell -ExecutionPolicy Bypass -File deploy\upload-fix.ps1
```

---

## Vercel vs cPanel

| | cPanel (main) | Vercel |
|--|---------------|--------|
| URL | calculator.suriainfiniti.com | suria-solar-calculator.vercel.app |
| Admin `/admin/` | ✅ | ❌ |
| Lead API + MySQL | ✅ | ❌ |
| Auto deploy | GitHub Actions → FTP | GitHub → Vercel |

**Cadangan:** Guna **cPanel** sebagai production utama. Vercel optional untuk preview frontend sahaja.

---

## Logo & images

- **Logo syarikat** — tukar dalam Admin → Site Settings → upload logo (tanpa redeploy)
- **Roof type images** — masih static dalam `frontend/public/images/roofs/` — tukar dalam code, push GitHub, auto-deploy
