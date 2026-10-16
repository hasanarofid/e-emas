# 📖 BUKU PANDUAN PENGGUNA (MANUAL BOOK)
# SISTEM POS & MANAJEMEN TOKO EMAS (e-Emas)

> **Versi:** 1.0.0  
> **Target Aplikasi:** Toko Perhiasan & Logam Mulia  
> **Teknologi:** Laravel 11, Vue 3 (Inertia.js), Tailwind CSS, MySQL  
> **Kompatibilitas Printer:** Printer Thermal 58mm/80mm, Printer Barcode Halter Roll (TSC, Zebra, Xprinter), Printer Dokumen Inkjet/Laser A5  

---

## 📑 DAFTAR ISI
1. [Pendahuluan & Konsep Bisnis](#1-pendahuluan--konsep-bisnis)
2. [Akses Masuk & Kredensial Pengguna](#2-akses-masuk--kredensial-pengguna)
3. [Panduan Modul Operasional](#3-panduan-modul-operasional)
   - [3.1 Kurs Harga Emas Harian](#31-kurs-harga-emas-harian)
   - [3.2 Manajemen Baki & Etalase Toko](#32-manajemen-baki--etalase-toko)
   - [3.3 Inventaris & Cetak Stiker Halter (Dumbbell Tag)](#33-inventaris--cetak-stiker-halter-dumbbell-tag)
   - [3.4 Kasir POS & Penerbitan Surat Emas Resmi](#34-kasir-pos--penerbitan-surat-emas-resmi)
   - [3.5 Pembelian Kembali (Buyback) & Tukar Tambah](#35-pembelian-kembali-buyback--tukar-tambah)
   - [3.6 Stock Opname Harian (Audit Baki Barcode)](#36-stock-opname-harian-audit-baki-barcode)
   - [3.7 Laporan Statistik & Neraca Gramasi](#37-laporan-statistik--neraca-gramasi)
   - [3.8 Pengaturan Identitas Toko & Rekening Bank](#38-pengaturan-identitas-toko--rekening-bank)
4. [Panduan Cetak & Integrasi Perangkat Keras](#4-panduan-cetak--integrasi-perangkat-keras)
5. [Daftar Pintasan Keyboard (Shortcuts)](#5-daftar-pintasan-keyboard-shortcuts)

---

## 1. Pendahuluan & Konsep Bisnis

**e-Emas** dirancang khusus untuk memecahkan tantangan khas manajemen toko emas di Indonesia:
1. **Presisi Gramasi 3 Desimal:** Setiap penimbangan wajib mencatat hingga **0.001 gram** (misal: `3.250 gr`) untuk membedakan berat kotor (*gross weight*), berat batu/permata (*stone weight*), dan berat bersih emas murni (*net gold weight*).
2. **Kalkulasi Dinamis:** Harga jual perhiasan dihitung otomatis dari:  
   $$\text{Harga Jual} = (\text{Berat Bersih} \times \text{Kurs Jual Hari Ini}) + \text{Ongkos Bikin}$$
3. **Pelacakan Fisik Baki (Tray System):** Perhiasan tidak disimpan acak, melainkan terkelompokkan rapi di baki etalase fisik untuk mempermudah audit harian.
4. **Surat Jaminan Emas Sah:** Bukti kepemilikan bernilai legal dengan rincian kadar, foto barang, dan klausul jaminan pembelian kembali (*buyback guarantee*).

---

## 2. Akses Masuk & Kredensial Pengguna

Buka sistem melalui browser di alamat URL toko Anda (Lokal: `http://127.0.0.1:8002/login` atau Produksi: `https://emas.nolimitstraining.id/login`).

Tersedia 2 peran (*Role*) bawaan:
* **Owner (Pemilik Toko):**
  * **Email:** `admin@emas.com`
  * **Password:** `password123`
  * **Hak Akses:** Akses penuh ke seluruh fitur, setting identitas toko, update kurs emas, laporan finansial, audit baki, kasir POS, dan master rekening bank.
* **Kasir (Staf Penjualan):**
  * **Email:** `kasir@emas.com`
  * **Password:** `password123`
  * **Hak Akses:** Terminal kasir POS penjualan, cetak nota/surat emas, pencatatan buyback, dan scan stock opname baki.

*(Tersedia tombol Auto-Fill cepat di halaman login untuk kemudahan staf).*

---

## 3. Panduan Modul Operasional

### 3.1 Kurs Harga Emas Harian
* **Menu:** Navigasi Sidebar -> **Kurs Harga Emas** (`/admin/gold-rates`)
* **Waktu Eksekusi:** Setiap pagi sebelum toko buka (sekitar pukul 08:30 – 09:00 WIB).
* **Langkah Kerja:**
  1. Masukkan harga dasar **Emas Murni 24K (99.9%)**.
  2. Sesuaikan harga jual dan harga beli kembali (*buy price*) untuk kadar lainnya:
     - 22K (91.6% - Emas Tua Arab/Dubai)
     - 18K / 750 (75.0% - Emas Berlian / Rose Gold)
     - 17K / 700 (70.0% - Emas Tua Paling Populer di Indonesia)
     - 16K (65.0% - Emas Sedang)
     - 9K / 375 (37.5% - Emas Muda Ekonomis)
     - 8K / 300 (30.0% - Emas Muda)
  3. Klik tombol **"Simpan Perubahan Harga Emas"**.
  4. **Otomatisasi Sistem:** Seketika itu seluruh harga taksiran ribuan stok perhiasan di etalase dan baki langsung terkalkulasi ulang secara realtime tanpa perlu edit barang satu per satu.

---

### 3.2 Manajemen Baki & Etalase Toko
* **Menu:** Navigasi Sidebar -> **Baki & Etalase** (`/admin/trays`)
* **Fungsi:** Memetakan nampan beludru fisik yang ada di etalase kaca toko.
* **Fitur:**
  * **Visual Slot:** Menampilkan persentase kapasitas baki terisi vs slot kosong.
  * **Detail Denah Baki:** Klik baki untuk melihat visual slot perhiasan yang terpasang di baki tersebut.
  * **Pindah Baki Cepat:** Memindahkan perhiasan dari Baki A ke Baki B cukup dengan scan barcode SKU barang, tanpa bongkar pasang data manual.

---

### 3.3 Inventaris & Cetak Stiker Halter (Dumbbell Tag)
* **Menu:** Navigasi Sidebar -> **Inventaris Perhiasan** (`/admin/items`)
* **Input Perhiasan Baru:**
  1. Klik tombol **"Tambah Perhiasan Baru"**.
  2. Masukkan Kode Barcode / SKU (otomatis di-generate sistem jika dikosongkan).
  3. Pilih Kategori (Cincin, Kalung, Gelang, Anting, Liontin, Logam Mulia).
  4. Pilih Baki Etalase dan Kadar Emas.
  5. Masukkan **Berat Kotor** (timbangan keseluruhan) dan **Berat Batu** (jika ada permata/zircon). Sistem otomatis mengalkulasi **Berat Bersih Emas**.
  6. Masukkan **Ongkos Bikin / Pembuatan**.
  7. Klik Simpan.
* **Cetak Stiker Label Perhiasan (Dumbbell Tag):**
  1. Pada tabel inventaris, klik tombol **"Cetak Tag"** pada barang yang bersangkutan.
  2. Tersedia 2 pilihan format stiker:
     - **Format Dumbbell / Butterfly Tail Tag (Ukuran Roll 2.5" × 1.25"):** Sisi kiri memuat QR Code + Kadar + Berat Bersih, sisi tengah buntut bening non-lem untuk dilingkarkan ke cincin/kalung, sisi kanan memuat Barcode 1D + SKU + Toko + Harga.
     - **Format Halter Lipat Standar 80mm.**
  3. Klik **"Cetak Label Stiker Sekarang"** (`Ctrl + P`).

---

### 3.4 Kasir POS & Penerbitan Surat Emas Resmi
* **Menu:** Navigasi Sidebar -> **Kasir POS Penjualan** (`/admin/pos`)
* **Langkah Transaksi Penjualan:**
  1. **Scan Barcode (`F2`):** Arahkan barcode scanner ke label perhiasan atau ketik SKU perhiasan. Item langsung masuk ke keranjang belanja.
  2. **Penyesuaian Ongkos (Opsional):** Kasir dapat menyesuaikan ongkos bikin secara fleksibel di keranjang jika ada negosiasi promo.
  3. **Identitas Pembeli:** Ketik Nama Pembeli (wajib untuk surat jaminan), Nomor WhatsApp, dan Alamat domisili.
  4. **Buka Modal Bayar (`F8`):**
     - Pilih metode pembayaran: **Tunai**, **Transfer Bank**, **QRIS**, atau **EDC Debit**.
     - Jika non-tunai, pilih Rekening Tujuan Toko (BCA / Mandiri / QRIS Toko).
     - Masukkan jumlah uang tunai yang diterima (tersedia tombol uang pas dan nominal cepat). Sistem otomatis menghitung kembalian.
  5. Klik **"Selesaikan Transaksi"**.
* **Pilihan Output Cetak Transaksi:**
  * **Struk Kasir Thermal (58mm / 80mm):** Struk kertas ringkas untuk dibawa pulang pembeli.
  * **Surat Jaminan Emas Resmi (Kuarto / A5 Landscape):** Sertifikat resmi dengan kop toko, legalitas SIUP, tabel rincian gramasi (kotor, batu, bersih), foto barang, pasal jaminan buyback, dan kolom tanda tangan.
  * **Kirim WhatsApp:** 1-klik untuk mengirim rincian nota digital rapi ke WhatsApp pembeli.

---

### 3.5 Pembelian Kembali (Buyback) & Tukar Tambah
* **Menu:** Navigasi Sidebar -> **Beli Kembali (Buyback)** (`/admin/buybacks`)
* **Langkah Kerja:**
  1. Klik tombol **"Catat Buyback Baru"**.
  2. **Verifikasi Nota Lama:** Ketik nomor nota lama (`INV-EMAS-...`) di kolom pencarian. Klik data nota yang muncul: identitas pembeli dan perhiasan otomatis terisi. *(Jika emas luar tanpa nota, lewati pencarian dan isi manual).*
  3. **Timbang Ulang:** Masukkan berat hasil timbangan air / timbangan presisi hari ini. Sistem otomatis mendeteksi selisih susut fisik dengan nota asli.
  4. **Potongan Kerusakan (Opsional):** Masukkan nominal potongan jika ada permata lepas, pengait patah, atau kotoran berlebih.
  5. **Alokasi Tujuan Emas Bekas:**
     - **Peleburan / Rongsok Emas (*Melt Scrap*):** Untuk emas rusak yang akan dilebur kembali menjadi 24K murni.
     - **Etalase Emas Bekas (*Second-Hand*):** Untuk perhiasan mulus yang siap dijual kembali setelah disepuh.
     - **Servis / Reparasi Toko.**
  6. Pilih metode pengeluaran kas toko (**Kas Tunai** atau **Transfer Rekening**).
  7. Klik **"Selesaikan Transaksi & Cetak Kuitansi"** untuk mencetak Kuitansi Buyback resmi format A5.

---

### 3.6 Stock Opname Harian (Audit Baki Barcode)
* **Menu:** Navigasi Sidebar -> **Stock Opname Baki** (`/admin/stock-audits`)
* **Fungsi:** Memastikan jumlah fisik perhiasan di baki etalase 100% sama dengan catatan database, mencegah barang hilang atau tertukar.
* **Langkah Kerja:**
  1. Klik **"Mulai Audit Baki Baru"**, pilih baki yang ingin diaudit (misal: Baki Cincin `BK-CNC-01`).
  2. Sistem menampilkan daftar seluruh perhiasan yang seharusnya ada di baki tersebut.
  3. Ambil scanner barcode, scan label perhiasan di baki satu per satu berturut-turut.
  4. Sistem otomatis membagi barang ke dalam 3 status:
     - 🟢 **Cocok (*Matched*):** Fisik perhiasan terbukti ada di baki ini.
     - 🔴 **Belum Di-scan / Kurang (*Missing*):** Barang belum ditemukan saat audit.
     - 🟡 **Salah Baki / Nyasar (*Unexpected*):** Fisik ada di baki ini, tapi di sistem milik baki lain!
  5. Setelah selesai, klik **"Selesaikan & Kunci Audit"** dan cetak berita acara audit fisik.

---

### 3.7 Laporan Statistik & Neraca Gramasi
* **Menu:** Navigasi Sidebar -> **Laporan & Statistik** (`/admin/reports`)
* **Laporan yang Tersedia:**
  * **Arus Kas Bersih (*Net Cash Flow*):** Omzet Penjualan dikurangi Pengeluaran Kas Buyback.
  * **Neraca Gramasi Emas:** Total gramasi emas fisik yang keluar (terjual) versus emas fisik yang masuk (buyback).
  * **Grafik Tren Harian:** Memantau hari-hari dengan penjualan atau buyback tertinggi.
  * **Performa Kategori:** Kategori perhiasan paling laris (Cincin vs Kalung vs Gelang vs LM).
  * **Performa Kadar Emas:** Kadar emas paling diminati pelanggan (misal: 17K/700 vs 24K Antam).
  * **Metode Pembayaran:** Rekapitulasi porsi pembayaran Tunai, QRIS, dan Transfer Bank.

---

### 3.8 Pengaturan Identitas Toko & Rekening Bank
* **Menu:** Navigasi Sidebar -> **Pengaturan Toko** (`/admin/settings`)
* **Parameter yang Dapat Diubah:**
  * Nama Toko & Nama Badan Usaha (UD / PT / Toko Mas).
  * Alamat Lengkap & Nomor Kontak Telepon / WhatsApp.
  * Nomor Izin Perdagangan Emas / SIUP resmi (tercetak di kop nota & sertifikat).
  * Kalimat Klausul Jaminan Buyback pada Surat Emas.
  * Master Rekening Bank Kasir (BCA, Mandiri, BRI, QRIS Kasir).

---

## 4. Panduan Cetak & Integrasi Perangkat Keras

### A. Pengaturan Printer Barcode Thermal (Label Halter Perhiasan)
1. Gunakan stiker perhiasan jenis **Jewelry Tag Halter / Dumbbell Tag** ukuran roll 2.5" × 1.25" atau 70mm × 15mm.
2. Saat dialog print browser muncul (`Ctrl + P`):
   - **Destination:** Pilih printer barcode (contoh: *TSC TTP-244 Pro* atau *Xprinter*).
   - **Margins:** Ubah ke **None** (Tanpa Margin).
   - **Options:** Hapus centang pada *Headers and footers*.
3. Hasil cetak akan pas secara presisi di sayap kiri dan sayap kanan stiker.

### B. Pengaturan Printer Kasir Thermal (Struk 58mm / 80mm)
1. Pilih printer kasir thermal (contoh: *Epson TM-T82*, *Xprinter 80mm*).
2. Paper size: **80mm** (atau **58mm**).
3. Margins: **None**.

### C. Pengaturan Printer Surat Emas Resmi (A5 Landscape)
1. Gunakan kertas kuarto / A5 atau kertas sertifikat bertekstur/watermark.
2. Layout: **Landscape**.
3. Paper size: **A5** (210mm × 148mm).
4. Margins: **Default** atau **Minimum**.

---

## 5. Daftar Pintasan Keyboard (Shortcuts)

Untuk mempercepat pelayanan kasir saat toko sedang ramai, gunakan tombol pintasan berikut pada halaman Kasir POS (`/admin/pos`):

| Tombol Shortcut | Fungsi |
| :--- | :--- |
| **`F2`** | Langsung fokuskan kursor ke input **Scan Barcode / Cari Perhiasan** |
| **`F8`** | Langsung membuka **Modal Pembayaran Kasir (Checkout)** |
| **`Enter`** | Memasukkan item yang di-scan ke keranjang belanja |
| **`Esc`** | Menutup modal pembayaran atau drawer riwayat transaksi |

---

*e-Emas POS & Management System © 2026 Toko Mas Permata Abadi. Presisi Gramasi & Transparansi Kadar Emas.*
