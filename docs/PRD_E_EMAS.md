# Product Requirement Document (PRD): e-Emas POS & Management System

> **Versi:** 1.0.0  
> **Target Rilis:** Produksi (`https://emas.nolimitstraining.id`)  
> **Repository GitHub:** `https://github.com/hasanarofid/e-emas.git`  
> **Server Hosting:** aaPanel Linux (PHP 8.3, Nginx, MySQL) @ `43.134.77.167`  
> **Tech Stack:** Laravel 11, Vue 3 (Inertia.js), Tailwind CSS, MySQL, Pinia, Lucide Icons

---

## 1. Ringkasan Eksekutif & Visi Produk

**e-Emas** adalah sistem manajemen operasional dan kasir (Point of Sale / POS) modern berbasis web yang dirancang khusus untuk toko perhiasan dan logam mulia di Indonesia. Terinspirasi dari sistem operasional perhiasan seperti `software-toko-emas.id`, aplikasi ini mengatasi problem nyata toko emas:
1. **Fluktuasi Harga Harian:** Perhitungan harga perhiasan dinamis berdasarkan kadar/karat dan harga pasaran hari ini.
2. **Gramasi & Presisi Berat:** Perhitungan berat kotor, berat permata/batu, dan berat bersih emas hingga 3 angka desimal (0.001 gram).
3. **Pelacakan Fisik (Baki / Nampan):** Manajemen inventaris berbasis baki perhiasan di etalase toko untuk audit stok harian cepat.
4. **Surat / Nota Emas Otomatis:** Pembuatan surat emas lengkap dengan foto barang, rincian kadar, berat, ongkos bikin, serta klausul buyback resmi.
5. **Transaksi Buyback & Tukar Tambah:** Penanganan pembelian kembali emas dari pelanggan dengan perhitungan susut dan potongan harga transparan.

---

## 2. Filosofi Desain UI/UX (Non-AI Slop)

Menghindari tampilan generic AI yang monoton, e-Emas mengusung gaya **"Modern Jewelry Atelier"**:
* **Color Palette Eksklusif:**
  - *Primary Gold Accent:* `#D4AF37` / `#C5A059` (Warm Champagne Gold) & `#E5C158` (Hover state).
  - *Base Surface (Dark Mode):* `#0F172A` (Slate 900) & `#1E293B` (Slate 800) dengan border halus `#334155`.
  - *Light Mode / Print Paper:* `#FAFAF9` (Stone 50) dengan kontras teks tajam `#1C1917` (Stone 900).
* **Tipografi:** Google Font *Plus Jakarta Sans* untuk UI modern dan *Cinzel* / *Playfair Display* halus untuk header nota surat emas.
* **Ergonomi Kasir (POS):**
  - Input autofocus untuk Barcode Scanner.
  - Hotkey keyboard (misal: `F2` Cari Barang, `F4` Baki, `F8` Bayar, `F9` Cetak Nota).
  - Desain nota ukuran standar Thermal 80mm & Surat Emas Format Kuarto/A5 landscape.

---

## 3. Matriks Roadmap & Checklist Implementasi

### FASE 1: Inisialisasi Repository & Fondasi Database Lokal
- [x] **1.1 Migrasi Git Remote**
  - Ganti origin git lokal ke `https://github.com/hasanarofid/e-emas.git`.
  - Buat branch utama `main` dan pastikan `.gitignore` bersih dari kredensial sensitif.
- [x] **1.2 Skema Database Master Data Emas**
  - `gold_rates`: Master kadar (24K/99.9%, 22K/91.6%, 18K/750, 17K/700, 16K, 9K/375, 8K/300, dsb.) beserta harga jual & beli harian per gram.
  - `categories`: Kategori perhiasan (Cincin, Kalung, Gelang, Anting, Liontin, Logam Mulia/LM).
  - `trays`: Baki / Nampan etalase (Kode baki, nama, kapasitas maksimal, status aktif).
  - `items`: Master perhiasan (Kode SKU / Barcode, nama item, foto, kategori, baki_id, kadar_id, berat kotor, berat batu, berat bersih emas, ongkos bikin, status: *Tersedia / Terjual / Buyback / Servis*).
- [x] **1.3 Skema Database Transaksi & Toko**
  - `sales` & `sale_items`: Transaksi kasir penjualan perhiasan, nomor faktur/nota, total berat, total ongkos, diskon, metode pembayaran.
  - `buybacks` & `buyback_items`: Transaksi pembelian kembali emas dari pelanggan (asal toko sendiri atau toko luar, potongan kondisi, berat susut).
  - `stock_audits` & `stock_audit_items`: Pencatatan stock opname berkala per baki.
  - `settings`: Pengaturan nama toko, logo, alamat, telepon, nomor izin, teks footer nota, warna tema dinamis.
- [x] **1.4 Seeder Data Awal & Pengujian Endpoint**
  - Akun login default: Owner (`admin@emas.com`) & Kasir (`kasir@emas.com`).
  - Halaman landing & live gold rates terverifikasi HTTP 200 OK di `http://127.0.0.1:8002/`.
- [x] **1.5 Fitur Dark Mode & Light Mode (Dynamic Switcher)**
  - Composable `useTheme.js` dengan persistensi ke `localStorage`.
  - Komponen `ThemeToggle.vue` terintegrasi pada Landing Page, Login, dan Admin Layout.
  - Palet warna eksklusif adaptif: Dark Obsidian Slate & Clean Ivory Gold.

---

### FASE 2: Modul Admin & Master Data (Backoffice)
- [x] **2.1 Pengaturan Toko & Harga Emas Harian (Live Gold Price)**
  - UI Input Update Harga Emas Harian cepat per kadar dalam 1 layar (`admin.gold-rates.index`).
  - Riwayat log perubahan harga emas untuk tracking margin keuntungan.
  - Pengaturan branding (Logo toko, legalitas SIUP, kontak WhatsApp, klausul buyback surat emas).
- [x] **2.2 Manajemen Baki & Etalase (Tray Management)**
  - Tampilan visual rak & baki toko (menampilkan kapasitas slot terisi vs kosong).
  - Detail visual denah nampan/slot fisik perhiasan (`admin.trays.show`).
  - Fitur pindah baki cepat (memindahkan perhiasan dari Baki A ke Baki B via scan barcode).
- [x] **2.3 Manajemen Inventaris & Barcode Generator**
  - Form tambah perhiasan dengan kalkulasi otomatis berat bersih (`berat_kotor - berat_batu`) dan estimasi harga jual harian.
  - Filter inventaris berdasarkan pencarian barcode/nama, baki, kadar, kategori, dan status.
- [x] **2.4 Modul Cetak Stiker Perhiasan Halter / Dumbbell Tag (Request Client)**
  - Dukungan cetak stiker roll label khusus perhiasan (Ukuran 2.5" x 1.25" / 70x15mm / Dumbbell Butterfly Tag).
  - Desain dua sisi (Flap Kiri: QR Code + Kadar + Berat Bersih; Flap Kanan: Barcode 1D Code128 + SKU + Ongkos + Nama Toko; Tengah: Ekor bening non-adhesive untuk cincin/kalung).
  - Pilihan: Cetak Satuan (`Single Tag Print`) dan Cetak Massal Sekaligus per Baki (`Batch Tray Tag Print`).
  - Kompatibel dengan printer barcode thermal (TSC, Zebra, Xprinter, Sato).
- [x] **2.5 Master Akun Bank & Metode Pembayaran Kasir (Ref: SITOKMAS)**
  - Manajemen rekening bank penerima toko (BCA, Mandiri, BRI, BNI, QRIS Static/Dynamic, Mesin EDC).
  - Integrasi pemilihan rekening tujuan pada POS kasir saat menerima pembayaran non-tunai.

---

### FASE 3: POS Kasir Penjualan & Cetak Surat Emas
- [x] **3.1 Tampilan Kasir (POS Terminal)**
  - Antarmuka kasir ultra-responsif (Split screen: Daftar Keranjang & Scan Barcode).
  - Kalkulasi otomatis total harga per gram real-time berdasarkan kadar hari ini + ongkos kerja.
  - Multi-payment support: Tunai, Transfer Bank, QRIS, Kartu Debit/Kredit, dan Split Payment (Sebagian Tunai + Sebagian Transfer).
  - Input identitas pembeli (Nama, No HP/WhatsApp, Alamat untuk surat jaminan emas).
- [x] **3.2 Generator Nota / Surat Emas Resmi & Multi-Format Cetak (Ref: SITOKMAS)**
  - **Format 1: Struk Kasir Thermal (58mm & 80mm):** Struk cepat untuk pembeli bawa pulang ringkas.
  - **Format 2: Surat Jaminan Emas Resmi (Kuarto / A5 Landscape):** Memuat kop toko resmi, foto perhiasan, rincian karat, gramasi netto/bruto, ongkos bikin, serta pasal jaminan buyback toko.
  - **Format 3: Invoice Digital WhatsApp:** Tombol 1-klik untuk kirim link nota/surat emas via WhatsApp Web ke nomor pembeli.
- [ ] **3.3 Log Aktivitas & Audit Trail Kasir (Ref: SITOKMAS)**
  - Mencatat setiap transaksi penjualan, pembatalan/void, dan diskon dengan ID kasir bertugas.

---

### FASE 4: Modul Buyback (Beli Kembali) & Tukar Tambah
- [ ] **4.1 Pencarian Nota Penjualan Lama**
  - Kasir mencari nomor nota atau scan QR surat emas pelanggan.
  - Verifikasi keaslian data (berat awal di nota vs berat aktual saat ditimbang ulang).
- [ ] **4.2 Form Hitung Potongan & Arus Kas Buyback**
  - Otomatisasi hitung potongan ongkos, susut berat, atau batu lepas.
  - Cetak Bukti Pembelian Kembali (Kuitansi Buyback).
  - Alokasi stok buyback: masuk ke Baki Emas Bekas (Second-hand) atau Gudang Peleburan.

---

### FASE 5: Laporan Akuntansi, Statistik & Stock Opname (Ref: SITOKMAS)
- [ ] **5.1 Audit Stok Baki (Stock Opname Harian)**
  - Mode audit: Kasir cukup scan barcode berturut-turut pada satu baki.
  - Sistem menampilkan rekonsiliasi instan (Barang Cocok, Barang Kurang / Selisih, Barang Salah Baki).
- [ ] **5.2 Laporan Statistik & Grafik Penjualan Realtime**
  - Grafik tren penjualan harian, mingguan, dan bulanan (Omzet Rupiah & Gramasi Emas).
  - Rekap total gramasi emas masuk dan keluar harian/bulanan.
  - Laporan margin laba kotor & laporan arus kas per rekening bank.

---

### FASE 6: Pengujian Lokal & Persiapan Rilis
- [ ] **6.1 Seeder Data Demo Realistis**
  - Seeder mencakup: 1 Toko Emas default, 5 Kadar (24K, 22K, 17K/700, 16K, 375), 6 Baki Etalase, 20+ item perhiasan contoh dengan barcode siap scan.
- [ ] **6.2 Testing Alur End-to-End di Lingkungan Lokal**
  - Uji alur: Input Item -> Cek Baki -> POS Kasir -> Cetak Surat Emas -> Buyback -> Stock Opname.
  - Build aset frontend: `npm run build` dan validasi bundle bebas error.

---

### FASE 7: Setup Server aaPanel & Konfigurasi SSH Key Auto-Deploy
- [ ] **7.1 Konfigurasi Nginx & Website di aaPanel (`emas.nolimitstraining.id`)**
  - Root directory diset ke: `/www/wwwroot/emas.nolimitstraining.id/public`.
  - Pasang SSL / Let's Encrypt gratis via panel aaPanel.
  - Set Nginx rewrite rules untuk Laravel:
    ```nginx
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    ```
- [ ] **7.2 Pembuatan SSH Key & Konfigurasi Deploy Key di Server**
  - Generate SSH Keypair di server untuk akses GitHub:
    ```bash
    ssh-keygen -t ed25519 -C "deploy@emas.nolimitstraining.id" -f ~/.ssh/id_ed25519_emas
    ```
  - Masukkan Public Key (`~/.ssh/id_ed25519_emas.pub`) ke **GitHub Repo -> Settings -> Deploy Keys** (centang *Allow write access* jika dibutuhkan).
- [ ] **7.3 Otomatisasi CI/CD Auto-Deploy via GitHub Actions**
  - Pasang GitHub Secrets: `SERVER_HOST`, `SERVER_USER`, `SSH_PRIVATE_KEY`, `SERVER_PORT`.
  - Workflow otomatis `git pull`, `composer install --no-dev`, `php artisan migrate --force`, `php artisan optimize`.

---

## 4. Panduan Lengkap Setup SSH Key & Auto-Deploy

### A. Konfigurasi SSH di Server aaPanel (IP: 43.134.77.167)
1. Login ke server via Terminal aaPanel atau SSH root:
   ```bash
   ssh root@43.134.77.167
   ```
2. Buat SSH key khusus untuk repository `e-emas`:
   ```bash
   ssh-keygen -t ed25519 -C "github-deploy-emas" -f /root/.ssh/id_ed25519_emas
   ```
3. Tambahkan konfigurasi di `/root/.ssh/config`:
   ```ssh
   Host github.com
       HostName github.com
       User git
       IdentityFile /root/.ssh/id_ed25519_emas
       IdentitiesOnly yes
   ```
4. Salin public key yang dihasilkan:
   ```bash
   cat /root/.ssh/id_ed25519_emas.pub
   ```
5. Buka GitHub: `https://github.com/hasanarofid/e-emas/settings/keys`, klik **Add deploy key**, tempelkan isi public key tadi.
6. Masuk ke direktori web aaPanel dan clone repository:
   ```bash
   cd /www/wwwroot/emas.nolimitstraining.id
   git clone git@github.com:hasanarofid/e-emas.git .
   chown -R www:www /www/wwwroot/emas.nolimitstraining.id
   chmod -R 775 storage bootstrap/cache
   ```

### B. Otomatisasi Deploy via GitHub Actions (`.github/workflows/deploy.yml`)
Workflow ini akan otomatis berjalan setiap ada `push` ke branch `main`:
```yaml
name: Deploy to aaPanel Production

on:
  push:
    branches: [ main ]

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - name: Checkout Repository
        uses: actions/checkout@v4

      - name: Deploy to Server via SSH
        uses: appleboy/ssh-action@v1.0.3
        with:
          host: ${{ secrets.SERVER_HOST }}
          username: ${{ secrets.SERVER_USER }}
          key: ${{ secrets.SSH_PRIVATE_KEY }}
          port: ${{ secrets.SERVER_PORT || 22 }}
          script: |
            cd /www/wwwroot/emas.nolimitstraining.id
            git pull origin main
            composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction
            php artisan migrate --force
            php artisan optimize:clear
            php artisan config:cache
            php artisan route:cache
            php artisan view:cache
            chown -R www:www /www/wwwroot/emas.nolimitstraining.id/storage /www/wwwroot/emas.nolimitstraining.id/bootstrap/cache
```

### C. Alternatif: Auto-Deploy via Webhook URL (Instant tanpa GitHub Runner)
File webhook deployment independen juga disiapkan di `public/deploy.php` sehingga deployment bisa dipicu langsung melalui Webhook aaPanel atau GitHub Webhooks dalam hitungan 2 detik.

---

## 5. Rencana Kerja Selanjutnya
Setelah PRD ini ditinjau dan disetujui:
1. Menghubungkan lokal git ke repository baru `https://github.com/hasanarofid/e-emas.git`.
2. Melaksanakan implementasi Fase 1 (Migrasi database master toko emas & seeder awal).
3. Melanjutkan checklist bertahap sesuai PRD hingga siap uji lokal dan auto-deploy ke server.
