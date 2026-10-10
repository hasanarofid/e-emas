# Cahaya Ornamen — Panduan Deployment Shared Hosting & Analisis CI/CD

Dokumen ini menjelaskan strategi deployment untuk proyek **Cahaya Ornamen** di lingkungan **Shared Hosting** (cPanel / DirectAdmin), perbandingan metode, serta panduan praktis.

---

## 1. Strategi Aset Frontend (Mengapa `public/build` Tidak di-Gitignore)

Di shared hosting, resource server (CPU & RAM) sangat terbatas. Menjalankan `npm run build` di terminal shared hosting sering kali memicu:
- **Out of Memory (OOM) error** atau proses di-*kill* oleh hosting provider.
- Node.js versi lama atau tidak tersedianya npm di shared hosting.

**Solusi Standar Industri:**
1. Lakukan kompilasi aset di lokal sebelum push:
   ```bash
   npm run build
   ```
2. Hapus `/public/build` dari `.gitignore` (sudah dilakukan).
3. File bundle hasil build di `public/build/` ikut ter-commit ke repositori Git.
4. Di shared hosting, server tinggal menjalankan `git pull` tanpa perlu Node.js sama sekali.

---

## 2. Cara Kerja `https://cahayaornamen.com/deploy.php`

File [public/deploy.php](file:///home/hasanarofid/Documents/hasanarofid/ivan/ivan-arstiek-sistem/public/deploy.php) telah disediakan sebagai deployment webhook independen.

### A. Konfigurasi Token Rahasia
Buka file `public/deploy.php` dan pastikan Anda menentukan token rahasia:
```php
define('DEPLOY_SECRET_TOKEN', 'cahaya_ornamen_deploy_2026_xyz');
define('DEPLOY_BRANCH', 'main');
```

### B. Cara Trigger Manual via Browser
Setiap kali Anda selesai push ke repository GitHub:
1. Buka URL:
   ```
   https://cahayaornamen.com/deploy.php?token=cahaya_ornamen_deploy_2026_xyz
   ```
2. Halaman akan menampilkan konsol proses:
   - `git pull origin main`
   - `php artisan migrate --force`
   - `php artisan optimize:clear`
   - `php artisan config:cache` & `route:cache` & `view:cache`
3. Selesai dalam hitungan 2–5 detik.

### C. Cara Otomatis via GitHub Webhook (Auto-Deploy saat Push)
1. Buka Repositori GitHub Anda > **Settings** > **Webhooks** > **Add webhook**.
2. Masukkan URL:
   ```
   https://cahayaornamen.com/deploy.php?token=cahaya_ornamen_deploy_2026_xyz
   ```
3. Content type: `application/json`.
4. Trigger events: `Just the push event`.
5. Klik **Add webhook**.
6. Sekarang, setiap kali Anda `git push origin main`, GitHub akan otomatis memanggil link tersebut dan server hosting Anda langsung update secara real-time!

---

## 3. Analisa: Apakah Perlu GitHub Actions CI/CD?

### Perbandingan: `deploy.php` Webhook vs GitHub Actions CI/CD

| Parameter | Opsi 1: Webhook `deploy.php` (Direkomendasikan Awal) | Opsi 2: GitHub Actions CI/CD |
| :--- | :--- | :--- |
| **Kemudahan Setup** | **Sangat Mudah (5 Menit)** — Cukup upload file & pasang webhook. | **Sedang** — Butuh konfigurasi YAML workflow & GitHub Secrets. |
| **Kecepatan Deploy** | **Sangat Cepat (2–4 detik)** — Server langsung pull delta perubahan. | **1–3 Menit** — Menunggu runner VM cloud GitHub boot, build, dan transfer file. |
| **Beban Server Hosting** | **Sangat Ringan** — Hanya menjalankan git fetch/merge & artisan cache. | **Nol di hosting**, proses build terjadi di cloud GitHub. |
| **Ketergantungan Fitur Hosting** | Butuh fungsi `exec()` aktif di cPanel & Git terpasang di cPanel. | **Bisa pakai FTP/SSH** tanpa perlu Git di hosting. |
| **Kebutuhan Kuota** | Gratis tanpa batas. | Memakai kuota gratis 2000 menit/bulan GitHub Actions. |

### Kesimpulan & Rekomendasi:
1. **Gunakan Opsi 1 (`deploy.php`):**
   - Jika cPanel hosting Anda mengizinkan `exec()` / `shell_exec` dan Anda sudah melakukan `git clone` repository di hosting.
   - Ini adalah alur kerja paling praktis, instan, dan minim konfigurasi untuk proyek company profile.

2. **Gunakan Opsi 2 (GitHub Actions CI/CD):**
   - HANYA JIKA shared hosting Anda **mengunci mati terminal SSH/Git** atau **mematikan fungsi `exec()`**.
   - Atau jika Anda ingin `npm run build` otomatis dikompilasi oleh GitHub Actions sehingga Anda tidak perlu build manual di laptop.

---

## 4. Template Opsional: GitHub Actions CI/CD (Jika Dibutuhkan)

Jika Anda memutuskan menggunakan GitHub Actions, buat file `.github/workflows/deploy.yml`:

```yaml
name: Deploy to Shared Hosting

on:
  push:
    branches: [ main ]

jobs:
  build-and-deploy:
    runs-on: ubuntu-latest

    steps:
    - name: Checkout Code
      uses: actions/checkout@v4

    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.2'

    - name: Setup Node.js
      uses: actions/setup-node@v4
      with:
        node-version: 20
        cache: 'npm'

    - name: Install Frontend Dependencies & Build
      run: |
        npm ci
        npm run build

    - name: Trigger Webhook Deploy
      run: |
        curl -s "https://cahayaornamen.com/deploy.php?token=${{ secrets.DEPLOY_TOKEN }}"
```
