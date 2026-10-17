# 📖 BUKU PANDUAN PENGGUNA (MANUAL BOOK)
# SISTEM POS & MANAJEMEN TOKO EMAS (POS Emas)

> **Versi:** 2.0.0 (Sitokmas Gold Standard Edition)  
> **Target Aplikasi:** Toko Perhiasan, Logam Mulia, Gadai, Servis, & Finansial Toko Emas  
> **Teknologi:** Laravel 11, Vue 3 (Inertia.js), Tailwind CSS, MySQL  
> **Live Demo:** [https://posemas.web.id](https://posemas.web.id)  
> **Kompatibilitas Hardware:** Printer Thermal 58mm/80mm, Printer Barcode Halter/Dumbbell Tag (TSC, Zebra, Xprinter), Timbangan Digital Presisi 3 Desimal, Printer Dokumen A5/Kuarto  

---

## 📑 DAFTAR ISI LENGKAP
1. [Pendahuluan & Konsep Bisnis Toko Emas](#1-pendahuluan--konsep-bisnis-toko-emas)
2. [Akses Masuk & Kredensial Pengguna](#2-akses-masuk--kredensial-pengguna)
3. [Dashboard Utama & Statistik Realtime](#3-dashboard-utama--statistik-realtime)
4. [Modul Utilitas & Pengaturan Toko](#4-modul-utilitas--pengaturan-toko)
   - [4.1 Pengaturan Identitas Toko & Kop Surat Resmi](#41-pengaturan-identitas-toko--kop-surat-resmi)
   - [4.2 Master Rekening Bank & QRIS Kasir](#42-master-rekening-bank--qris-kasir)
5. [Modul Persediaan & Inventaris Emas](#5-modul-persediaan--inventaris-emas)
   - [5.1 Brand & Merk Emas](#51-brand--merk-emas)
   - [5.2 Satuan Barang (UoM)](#52-satuan-barang-uom)
   - [5.3 Gudang & Brankas Penyimpanan](#53-gudang--brankas-penyimpanan)
   - [5.4 Kadar & Kurs Harga Emas Harian (Live Gold Rate)](#54-kadar--kurs-harga-emas-harian-live-gold-rate)
   - [5.5 Data Barang & Cetak Label Halter (Dumbbell Tag)](#55-data-barang--cetak-label-halter-dumbbell-tag)
   - [5.6 Baki & Etalase Visual](#56-baki--etalase-visual)
   - [5.7 Mutasi Baki Barang](#57-mutasi-baki-barang)
   - [5.8 Stok Opname Baki (Audit Barcode Scanner)](#58-stok-opname-baki-audit-barcode-scanner)
   - [5.9 Keluar Masuk Barang (Penerimaan Pabrik & Peleburan)](#59-keluar-masuk-barang-penerimaan-pabrik--peleburan)
   - [5.10 Laporan Persediaan & Rekap Gramasi](#510-laporan-persediaan--rekap-gramasi)
6. [Modul Pelanggan & CRM (Loyalty Point)](#6-modul-pelanggan--crm-loyalty-point)
7. [Modul Pembelian (Supplier / Pabrik Emas)](#7-modul-pembelian-supplier--pabrik-emas)
   - [7.1 Data Supplier & Pengrajin Emas](#71-data-supplier--pengrajin-emas)
   - [7.2 Faktur Pembelian Supplier & Stok Masuk](#72-faktur-pembelian-supplier--stok-masuk)
8. [Modul Pembelian Cepat (Buyback Pelanggan)](#8-modul-pembelian-cepat-buyback-pelanggan)
   - [8.1 Transaksi Buyback / Beli Kembali Emas](#81-transaksi-buyback--beli-kembali-emas)
   - [8.2 Master Potongan Pembelian Cepat](#82-master-potongan-pembelian-cepat)
9. [Modul Penjualan (Kasir POS & Surat Emas)](#9-modul-penjualan-kasir-pos--surat-emas)
   - [9.1 Terminal Kasir POS Modern & Multi-Metode Bayar](#91-terminal-kasir-pos-modern--multi-metode-bayar)
   - [9.2 Surat Jaminan Emas Resmi (Format A5 Landscape)](#92-surat-jaminan-emas-resmi-format-a5-landscape)
10. [Modul Gadai Perhiasan (Pawn System)](#10-modul-gadai-perhiasan-pawn-system)
11. [Modul Servis Perhiasan (Reparasi, Cuci & Sepuh)](#11-modul-servis-perhiasan-reparasi-cuci--sepuh)
12. [Modul Timbang Baki Harian (Rekap Gramasi Buka/Tutup)](#12-modul-timbang-baki-harian-rekap-gramasi-bukatutup)
13. [Modul Pegawai & Penggajian (Payroll & Komisi)](#13-modul-pegawai--penggajian-payroll--komisi)
14. [Modul Keuangan & Akuntansi Toko Emas](#14-modul-keuangan--akuntansi-toko-emas)
    - [14.1 Buku Kas & Mutasi Kasir Harian](#141-buku-kas--mutasi-kasir-harian)
    - [14.2 Biaya Operasional Toko (Expenses)](#142-biaya-operasional-toko-expenses)
    - [14.3 Buku Hutang Supplier & Jatuh Tempo](#143-buku-hutang-supplier--jatuh-tempo)
    - [14.4 Laporan Laba Rugi Komprehensif & Laporan Pajak](#144-laporan-laba-rugi-komprehensif--laporan-pajak)
15. [Panduan Cetak & Integrasi Hardware Printer](#15-panduan-cetak--integrasi-hardware-printer)
16. [Daftar Pintasan Keyboard (Shortcuts)](#16-daftar-pintasan-keyboard-shortcuts)

---

## 1. Pendahuluan & Konsep Bisnis Toko Emas

**POS Emas** dibangun khusus dengan mengadopsi standar operasional terbaik industri retail perhiasan emas di Indonesia:
1. **Presisi Gramasi 3 Desimal (Milligram Precision):** Setiap perhiasan wajib ditimbang hingga `0.001 gram` (contoh: `4.125 gr`). Sistem secara ketat memisahkan antara **Berat Kotor (Gross Weight)**, **Berat Batu/Mutiara/Zircon (Stone Weight)**, dan **Berat Bersih Emas (Net Weight)**.
2. **Kalkulasi Nilai Dinamis Terpusat:** Seluruh harga taksiran perhiasan di etalase toko terkoneksi langsung dengan kurs emas harian:  
   $$\text{Harga Jual} = (\text{Berat Bersih} \times \text{Kurs Kadar Hari Ini}) + \text{Ongkos Bikin/Pembuatan}$$
3. **Sistem Baki & Etalase Fisik (Tray Management):** Barang tidak diletakkan secara acak, melainkan terpetakan pada baki-baki fisik (contoh: `BK-CCN-01` Baki Cincin Dewasa) dengan pelacakan kapasitas dan rekonsiliasi timbang baki buka/tutup toko.
4. **Surat Jaminan Emas Resmi Bergaransi:** Bukti kepemilikan sah berkekuatan hukum dengan spesifikasi kadar, berat akurat, foto perhiasan, dan klausul jaminan pembelian kembali (*buyback guarantee*).

---

## 2. Akses Masuk & Kredensial Pengguna

Akses portal aplikasi dilakukan melalui peramban (browser) di URL: **`https://posemas.web.id/login`**.

| Peran (Role) | Email Login | Password | Cakupan Akses |
| :--- | :--- | :--- | :--- |
| **Owner / Admin** | `admin@emas.com` | `password` | Akses 100% penuh: Kurs Emas, Seluruh Modul Persediaan, Keuangan, Laba Rugi, Pajak, Penggajian, Gadai, Servis, dan Pengaturan Identitas Toko. |
| **Kasir Toko** | `kasir@emas.com` | `password` | Terminal Penjualan POS, Cetak Surat Emas, Pembelian Cepat (Buyback), Timbang Baki, dan Pencatatan Biaya Kasir Harian. |

---

## 3. Dashboard Utama & Statistik Realtime

Halaman Dashboard (`/admin/dashboard`) menyajikan indikator bisnis terpenting secara seketika (realtime):
* **Ringkasan Finansial:** Total Omzet Penjualan Hari Ini, Total Pembelian/Buyback, dan Laba Kotor Berjalan.
* **Neraca Gramasi:** Akumulasi gramasi emas keluar (terjual) versus gramasi emas masuk (buyback/supplier).
* **Peringatan Stok & Baki:** Monitoring baki etalase yang mendekati kapasitas maksimum atau butuh pengisian ulang.
* **Widget Kurs Live:** Menampilkan fluktuasi harga emas terkini untuk memudahkan negosiasi kasir.

---

## 4. Modul Utilitas & Pengaturan Toko

### 4.1 Pengaturan Identitas Toko & Kop Surat Resmi
* **Rute:** Sidebar -> **Utilitas** -> **Pengaturan Toko** (`/admin/settings`)
* **Fungsi:** Mengelola data legalitas yang tercetak pada Kop Nota Kasir dan Surat Emas Resmi.
* **Kolom Konfigurasi:**
  * Nama Toko (contoh: *Toko Mas Permata Abadi*) dan Nama Perusahaan/CV/UD.
  * Alamat Lengkap, Kota, dan Nomor Telepon/WhatsApp Layanan Pelanggan.
  * Nomor Izin Usaha / SIUP Emas Resmi.
  * Teks Syarat & Ketentuan Buyback (klausul potongan nota, kondisi barang rusak, dsb).

### 4.2 Master Rekening Bank & QRIS Kasir
* **Fungsi:** Menampung daftar rekening bank operasional kasir untuk menerima pembayaran non-tunai (BCA, Mandiri, BRI, BNI) serta kode statis/dinamis QRIS toko.
* Setiap transaksi transfer atau QRIS di kasir POS akan langsung tercatat mutasinya ke akun rekening yang bersangkutan.

---

## 5. Modul Persediaan & Inventaris Emas

### 5.1 Brand & Merk Emas
* **Rute:** Sidebar -> **Persediaan** -> **Brand & Merk** (`/admin/brands`)
* **Fungsi:** Mengelola katalog produsen/pabrikan emas ternama (contoh: *UBS Gold*, *Antam LM*, *Lotus Archi*, *King Halim*, *Hala Gold*, *HWT*). Memudahkan pengelompokan produk dan analisa brand terlaris.

### 5.2 Satuan Barang (UoM)
* **Rute:** Sidebar -> **Persediaan** -> **Satuan Barang** (`/admin/units`)
* **Fungsi:** Mengelola satuan ukuran inventaris:
  * `gr` (Gram) - Satuan bobot baku perhiasan dan emas batangan.
  * `pcs` (Pieces) - Satuan unit barang jadi atau certicard.
  * `btr` (Butir) - Satuan permata, berlian, atau batu zircon.
  * `set` (Set / Pasang) - Satuan perhiasan couple (cincin kawin, anting sepasang).

### 5.3 Gudang & Brankas Penyimpanan
* **Rute:** Sidebar -> **Persediaan** -> **Gudang & Brankas** (`/admin/warehouses`)
* **Fungsi:** Memetakan lokasi fisik penyimpanan emas di toko:
  * **Gudang Utama & Brankas Khazanah (`GD-UTAMA`):** Ruang brankas lantai 2 untuk menyimpan stok baru, cadangan logam mulia, dan barang belum pajang.
  * **Etalase Toko Depan (`ETL-DPN`):** Area showcase kasir tempat baki-baki perhiasan aktif dipamerkan ke pelanggan.
  * **Brankas Siap Lebur / Cuci (`BK-LEBUR`):** Brankas penampungan perhiasan bekas hasil buyback yang menunggu proses cuci sepuh atau dilebur kembali.

### 5.4 Kadar & Kurs Harga Emas Harian (Live Gold Rate)
* **Rute:** Sidebar -> **Persediaan** -> **Kadar & Kurs Emas** (`/admin/gold-rates`)
* **Waktu Update:** Setiap pagi hari sebelum toko beroperasi (pukul 08:30 WIB).
* **Kadar Emas yang Didukung:**
  * `24K (99.9%)` - Emas Murni Batangan / Logam Mulia.
  * `22K (91.6%)` - Emas Tua Kadar Tinggi (Model Dubai/Arab/Kendari).
  * `17K / 700 (70.0%)` - Kadar terpopuler perhiasan di Indonesia (warna awet, kuat, berkilau).
  * `16K (65.0%)` - Emas Sedang.
  * `9K / 375 (37.5%)` - Emas Muda Ekonomis.
  * `UBS / Antam Certicard` - Kurs khusus certieye batangan.
* **Fitur Unggulan:** Sekali klik **"Perbarui Kurs Emas"**, taksiran harga jual seluruh ribuan inventaris perhiasan di etalase langsung terupdate secara otomatis dan akurat.

### 5.5 Data Barang & Cetak Label Halter (Dumbbell Tag)
* **Rute:** Sidebar -> **Persediaan** -> **Data Barang & Barcode** (`/admin/items`)
* **Langkah Tambah Perhiasan Baru:**
  1. Klik tombol **"Tambah Perhiasan Baru"**.
  2. Barcode SKU terbuat otomatis atau bisa diisi manual (contoh: `CCN-UBS-17K-001`).
  3. Pilih Kategori (Cincin, Gelang, Kalung, Anting, Liontin, Logam Mulia).
  4. Pilih Baki Etalase, Brand, dan Kadar Emas.
  5. Isi **Berat Kotor** (total timbangan) dan **Berat Batu/Zircon**. Sistem otomatis menghasilkan **Berat Bersih Emas**.
  6. Masukkan **Ongkos Bikin**. Taksiran harga jual langsung dihitung otomatis.
  7. Upload foto perhiasan untuk dicetak pada sertifikat surat emas.
* **Cetak Label Halter (Dumbbell Jewelry Tag):**
  * Klik icon barcode **"Cetak Tag"**.
  * Pilih ukuran: **Format Dumbbell Butterfly Tail (2.5" × 1.25")** atau **Halter Roll 80mm**.
  * Label sayap kiri memuat QR Code + Kadar + Gramasi Bersih, ekor tengah melingkar di batang cincin, sayap kanan memuat Barcode 1D + SKU + Harga.

### 5.6 Baki & Etalase Visual
* **Rute:** Sidebar -> **Persediaan** -> **Baki & Etalase** (`/admin/trays`)
* **Fungsi:** Representasi digital dari nampan beludru pajangan toko. Menampilkan kapasitas total slot, jumlah perhiasan terisi, dan akumulasi gramasi baki.

### 5.7 Mutasi Baki Barang
* **Rute:** Sidebar -> **Persediaan** -> **Mutasi Baki Barang** (`/admin/inventory/mutations`)
* **Fungsi:** Mencatat pemindahan perhiasan dari Baki A ke Baki B (contoh: penataan ulang showcase atau pemindahan barang display ke baki promo). Cukup scan barcode perhiasan dan pilih baki tujuan.

### 5.8 Stok Opname Baki (Audit Barcode Scanner)
* **Rute:** Sidebar -> **Persediaan** -> **Stok Opname Baki** (`/admin/stock-audits`)
* **Langkah Audit:**
  1. Pilih baki yang akan diaudit (misal: Baki Gelang `BK-GLG-01`).
  2. Ambil scanner barcode, scan seluruh perhiasan yang ada di baki tersebut satu per satu.
  3. Sistem memetakan hasil audit secara instan:
     * 🟢 **Matched (Cocok):** Fisik barang ada dan tepat berada di baki ini.
     * 🔴 **Missing (Kurang / Hilang):** Tercatat di sistem tapi tidak ditemukan fisiknya saat audit.
     * 🟡 **Mismatched (Nyasar):** Barang fisik ada di baki ini, padahal di database terdaftar di baki lain.
  4. Kunci sesi audit dan cetak Berita Acara Rekonsiliasi Opname.

### 5.9 Keluar Masuk Barang (Penerimaan Pabrik & Peleburan)
* **Rute:** Sidebar -> **Persediaan** -> **Keluar Masuk Barang** (`/admin/inventory/movements`)
* **Fungsi:** Buku log mutasi keluar masuk perhiasan non-penjualan:
  * `purchase_supplier`: Penerimaan stok perhiasan baru dari pengrajin/pabrik.
  * `melted`: Pengeluaran emas model lama untuk dilebur kembali menjadi emas batangan.
  * `repair_out` / `repair_in`: Pengeluaran/penerimaan barang dari tukang reparasi & sepuh.
  * `damaged_loss`: Pencatatan susut fisik perhiasan.

### 5.10 Laporan Persediaan & Rekap Gramasi
* **Rute:** Sidebar -> **Persediaan** -> **Laporan Persediaan** (`/admin/reports`)
* Menampilkan neraca stok, valuasi aset emas berjalan, dan rasio perputaran barang per kategori.

---

## 6. Modul Pelanggan & CRM (Loyalty Point)

* **Rute:** Sidebar -> **Pelanggan** (`/admin/customers`)
* **Fitur Utama:**
  * **Tingkatan Member (Membership Tier):** Pengelompokan pelanggan menjadi *Silver*, *Gold*, *Platinum*, dan *VIP Priority*.
  * **Sistem Poin Belanja:** Setiap transaksi penjualan di kasir otomatis menambahkan poin belanja ke akun member pelanggan.
  * **Redeem Reward & Diskon:** Poin yang terkumpul dapat ditukar dengan souvenir kotak perhiasan, potongan ongkos bikin, atau voucher belanja.
  * **Cetak Kartu Member:** Cetak ID card member pelanggan lengkap dengan barcode ID untuk scan instan di kasir POS.

---

## 7. Modul Pembelian (Supplier / Pabrik Emas)

### 7.1 Data Supplier & Pengrajin Emas
* **Rute:** Sidebar -> **Pembelian** -> **Data Supplier** (`/admin/suppliers`)
* **Fungsi:** Mengelola direktori supplier pabrik emas (PT UBS, PT Antam, CV Pengrajin Lokal), kontak person, nomor rekening pembayaran, dan termin pembayaran hutang dagang.

### 7.2 Faktur Pembelian Supplier & Stok Masuk
* **Rute:** Sidebar -> **Pembelian** -> **Pembelian Supplier** (`/admin/purchases`)
* **Langkah Input Faktur:**
  1. Pilih Supplier dan masukkan Nomor Invoice Pabrik.
  2. Input item perhiasan atau emas batangan yang dibeli, kadar emas, berat bersih, dan harga per gram.
  3. Pilih metode pembayaran: **Tunai Lunas** atau **Termin Hutang (Tempo)**.
  4. Jika tempo, sistem otomatis membuat tagihan di modul **Buku Hutang (Debts)** beserta tanggal jatuh tempo pembayaran.

---

## 8. Modul Pembelian Cepat (Buyback Pelanggan)

### 8.1 Transaksi Buyback / Beli Kembali Emas
* **Rute:** Sidebar -> **Pembelian Cepat** -> **Data Pembelian Cepat** (`/admin/buybacks`)
* **Langkah Kerja Kasir:**
  1. Klik **"Catat Buyback Baru"**.
  2. **Pencarian Nota Asli:** Ketik nomor nota lama toko (`INV-2026...`). Data kadar, berat asal, dan nama pelanggan otomatis terpanggil. *(Jika perhiasan dari toko lain tanpa surat, kasir dapat memilih opsi Emas Luar Toko).*
  3. **Uji Timbang & Kadar Ulang:** Masukkan berat timbangan air/presisi saat ini. Sistem otomatis mendeteksi selisih gramasi susut dengan nota asli.
  4. **Pilih Potongan Kerusakan:** Pilih parameter potongan (surat hilang, kait patah, permata copot).
  5. **Alokasi Emas Bekas:**
     * `stock_secondary`: Perhiasan masih mulus, dialokasikan ke etalase second setelah dicuci.
     * `melt_scrap`: Emas rusak/patah, dialokasikan ke brankas penampungan lebur.
  6. Pilih kas pengeluaran (Tunai Kasir / Transfer Bank Toko) dan cetak **Kuitansi Pembelian Cepat A5**.

### 8.2 Master Potongan Pembelian Cepat
* **Rute:** Sidebar -> **Pembelian Cepat** -> **Potongan Pembelian Cepat** (`/admin/buyback-deductions`)
* **Fungsi:** Mengatur 25 parameter potongan standar toko agar kalkulasi kasir seragam dan tidak terjadi perdebatan dengan pelanggan (contoh: Nota Asli Hilang Potong Rp 25.000, Pengait Gelang Patah Potong 2%, Permata Lepas Potong Rp 15.000).

---

## 9. Modul Penjualan (Kasir POS & Surat Emas)

### 9.1 Terminal Kasir POS Modern & Multi-Metode Bayar
* **Rute:** Sidebar -> **Penjualan (POS)** -> **Kasir POS** (`/admin/pos`)
* **Alur Transaksi Cepat:**
  1. Tekan tombol shortcut **`F2`** untuk langsung fokus pada input barcode.
  2. Arahkan barcode scanner ke label halter perhiasan atau pilih dari etalase. Item langsung masuk ke keranjang kasir.
  3. Pilih Nama Pelanggan (ketik nama untuk auto-complete member agar mendapatkan poin).
  4. Sesuaikan ongkos bikin jika ada diskon promo yang disetujui.
  5. Tekan tombol shortcut **`F8`** untuk membuka modal checkout pembayaran.
  6. Pilih metode pembayaran (Tunai, QRIS, Transfer Bank, Debit EDC). Masukkan nominal uang yang diterima.
  7. Klik **"Selesaikan Transaksi"**.

### 9.2 Surat Jaminan Emas Resmi (Format A5 Landscape)
* Setiap transaksi penjualan otomatis menyediakan 2 pilihan cetak:
  * **Struk Thermal (58mm / 80mm):** Struk ringkas bukti pembayaran kasir.
  * **Surat Emas Resmi (A5 Landscape):** Sertifikat eksklusif dengan Kop Toko, Nomor Nota Legal, Tabel Rincian Berat (Kotor, Batu, Bersih), Foto Barang, Barcode Nota, dan Klausul Garansi Pembelian Kembali.

---

## 10. Modul Gadai Perhiasan (Pawn System)

* **Rute:** Sidebar -> **Gadai Perhiasan** (`/admin/pawns`)
* **Fungsi:** Memberikan fasilitas dana talangan / pinjaman tunai dengan agunan perhiasan emas.
* **Langkah Transaksi Gadai:**
  1. Klik **"Terima Gadai Baru"**.
  2. Pilih Pelanggan, masukkan deskripsi barang agunan, kadar emas, dan berat kotor.
  3. Sistem otomatis menghitung **Taksiran Nilai Emas** hari ini berdasarkan kurs berlaku.
  4. Tentukan **Plafon Pinjaman Dana** (maksimal 80-85% dari nilai taksiran).
  5. Tentukan **Jasa Simpan Bulanan (%)** (contoh: 1.5% per bulan) dan Tanggal Jatuh Tempo pinjaman (30 hari / 60 hari).
  6. Cetak **Surat Bukti Gadai Resmi** rangkap 2 (untuk toko dan nasabah).
  7. Saat nasabah menebus, klik tombol **"Tebus Gadai"**. Sistem menghitung total pelunasan pokok + akumulasi jasa simpan.

---

## 11. Modul Servis Perhiasan (Reparasi, Cuci & Sepuh)

* **Rute:** Sidebar -> **Servis Perhiasan** (`/admin/services`)
* **Fungsi:** Mengelola penerimaan pesanan perbaikan perhiasan pelanggan:
  * Jenis Servis: *Cuci Kilap Emas*, *Sepuh Ulang Kuning/Rose Gold/Putih*, *Patri Sambung Putus*, *Pasang Permata Lepas*, *Resize Ukuran Cincin*.
* **Alur Pelayanan:**
  1. Input nama pelanggan, foto barang sebelum diservis, dan berat awal saat diterima toko.
  2. Masukkan Estimasi Biaya Servis dan Uang Muka (DP) yang dibayarkan pelanggan.
  3. Cetak **Surat Tanda Terima Servis** dengan estimasi tanggal selesai.
  4. Kasir dapat memperbarui status: *Diterima -> Dikerjakan -> Selesai -> Diambil Pelanggan*.

---

## 12. Modul Timbang Baki Harian (Rekap Gramasi Buka/Tutup)

* **Rute:** Sidebar -> **Timbang Baki** (`/admin/tray-scales`)
* **Prosedur Standar Operasional (SOP):**
  1. **Sesi Pagi (Buka Toko):** Sebelum etalase dibuka, letakkan seluruh baki satu per satu ke timbangan digital. Input gramasi riil timbangan ke sistem. Sistem memastikan total gramasi 100% klop (matched) dengan data penutupan kemarin.
  2. **Sesi Malam (Tutup Toko):** Sebelum baki dimasukkan ke brankas, lakukan penimbangan ulang seluruh baki. Sistem membandingkan gramasi fisik vs akumulasi transaksi kasir hari ini. Jika ada selisih (discrepancy) sekecil apa pun, sistem langsung memberikan peringatan warna merah.

---

## 13. Modul Pegawai & Penggajian (Payroll & Komisi)

* **Rute:** Sidebar -> **Pegawai** -> **Data Pegawai** (`/admin/employees`) & **Penggajian** (`/admin/payrolls`)
* **Fitur Utama:**
  * Pencatatan NIK, Jabatan (Kepala Toko, Kasir Utama, Staff Penimbangan, Satpam Brankas), Gaji Pokok, dan Tunjangan.
  * **Kalkulasi Komisi Penjualan:** Perhitungan bonus komisi otomatis berdasarkan persentase gramasi emas yang berhasil dijual kasir.
  * **Generate Slip Gaji:** Penerbitan slip gaji bulanan resmi siap cetak format A5.

---

## 14. Modul Keuangan & Akuntansi Toko Emas

### 14.1 Buku Kas & Mutasi Kasir Harian
* **Rute:** Sidebar -> **Keuangan** -> **Buku Kas** (`/admin/cash-book`)
* **Fungsi:** Mencatat seluruh arus uang tunai dan rekening bank toko: penyediaan modal awal kasir pagi hari, penerimaan penjualan kasir, pengeluaran buyback, dan setor uang ke brankas pusat.

### 14.2 Biaya Operasional Toko (Expenses)
* **Rute:** Sidebar -> **Keuangan** -> **Biaya Operasional** (`/admin/expenses`)
* **Fungsi:** Mencatat pengeluaran rutin toko: tagihan listrik showcase lampu LED, air utilitas, pembelian box beludru/dompet perhiasan souvenir, dan perawatan alat timbangan air.

### 14.3 Buku Hutang Supplier & Jatuh Tempo
* **Rute:** Sidebar -> **Keuangan** -> **Buku Hutang** (`/admin/debts`)
* **Fungsi:** Rekapitulasi tagihan pembelian emas tempo ke supplier pabrik. Menyajikan jadwal jatuh tempo, riwayat cicilan termin, sisa hutang, dan status lunas.

### 14.4 Laporan Laba Rugi Komprehensif & Laporan Pajak
* **Rute:** Sidebar -> **Keuangan** -> **Laporan Laba Rugi** (`/admin/finance/profit-loss`) & **Laporan Pajak** (`/admin/finance/tax-report`)
* **Analisa Laba Rugi:** Omzet Penjualan Emas + Pendapatan Ongkos Bikin + Bunga Gadai dikurangi HPP Pembelian Emas & Beban Operasional = **Laba Bersih Toko**.
* **Laporan Pajak:** Rekapitulasi PPh 22 dan PPN transaksi emas sesuai regulasi perpajakan yang berlaku.

---

## 15. Panduan Cetak & Integrasi Hardware Printer

| Perangkat / Format | Jenis Kertas | Pengaturan Dialog Print (Ctrl + P) |
| :--- | :--- | :--- |
| **Label Halter Perhiasan** | Dumbbell Roll 2.5" × 1.25" | Printer: *Barcode Thermal*, Margins: **None**, Hapus centang *Headers/Footers*. |
| **Struk Kasir** | Thermal Roll 58mm / 80mm | Printer: *Thermal POS Printer*, Paper: **80mm / 58mm**, Margins: **None**. |
| **Surat Emas Resmi** | Kertas A5 / Kuarto Bertekstur | Printer: *Inkjet / Laser*, Layout: **Landscape**, Paper Size: **A5**. |
| **Slip Gaji & Kuitansi** | Kertas A5 / HVS Half | Printer: *Inkjet / Laser*, Layout: **Portrait / Landscape**, Paper Size: **A5**. |

---

## 16. Daftar Pintasan Keyboard (Shortcuts)

Didesain untuk kecepatan kasir saat melayani antrean pelanggan toko yang padat:

| Pintasan (Shortcut) | Halaman | Fungsi Aksi |
| :--- | :--- | :--- |
| **`F2`** | Kasir POS | Fokus kursor langsung ke kolom **Scan Barcode / Cari Perhiasan**. |
| **`F8`** | Kasir POS | Membuka jendela pop-up **Pembayaran (Checkout Modal)**. |
| **`Enter`** | Semua Modul | Konfirmasi input scan atau submit formulir pencarian. |
| **`Esc`** | Semua Modul | Menutup modal dialog / drawer riwayat transaksi secara instan. |

---

*POS Emas - Enterprise Gold & Jewelry Management System © 2026 Toko Mas Permata Abadi. Presisi Gramasi & Transparansi Kadar Emas Indonesia.*
