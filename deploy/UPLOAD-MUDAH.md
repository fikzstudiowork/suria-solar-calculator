# Upload Auto ke cPanel (Tanpa Browser)

## Step 1 — Dapatkan FTP password

1. Login **cPanel**
2. **FTP Accounts** → cari account untuk subdomain
3. Klik **Configure FTP Client** → catat:
   - Server: `ftp.suriainfiniti.com`
   - Username: contoh `suriainfico`
   - Password: (FTP password anda)

## Step 2 — Buat credentials file (sekali sahaja)

Copy file:
```
deploy\ftp-credentials.example.ps1
```
ke:
```
deploy\ftp-credentials.ps1
```

Edit `ftp-credentials.ps1` — isi `$FtpPass` dan semak `$RemoteRoot`:

```powershell
$RemoteRoot = "calculator.suriainfiniti.com"
```

Kalau path File Manager anda `calculator.suria-infiniti.com`, tukar accordingly.

## Step 3 — Run upload (1 command)

PowerShell:

```powershell
cd "C:\Users\fikzm\OneDrive\Desktop\Suria Infiniti\Solar Calculator"
powershell -ExecutionPolicy Bypass -File deploy\upload-fix.ps1
```

Script akan:
1. Build package baru (folder `next` fix)
2. Upload via FTP: `index.html`, `.htaccess`, folder `next/`
3. **Tak overwrite** `config.php` anda

## Step 4 — Test

- CSS: https://calculator.suriainfiniti.com/next/static/css/057d9b9e0ce93292.css
- Site: https://calculator.suriainfiniti.com (Ctrl+Shift+R)

---

**Nota:** File `ftp-credentials.ps1` tak masuk GitHub (password selamat).
