# Cahaya Ornamen CMS

Sistem Manajemen Konten (CMS) modular berkinerja tinggi yang dirancang khusus untuk bisnis perusahaan arsitektur & ornamen GRC **Cahaya Ornamen**. Sistem ini dibangun menggunakan **Laravel 11**, **Vue 3 (Inertia.js)**, **Tailwind CSS**, dan **Spatie Permission**. Didesain dengan alur kerja seperti WordPress namun dioptimalkan untuk kebutuhan bisnis ornamen arsitektur.

---

## ⚡ Fitur Utama
- **Kustomisasi Tema Dinamis**: Ubah logo dan warna utama (primary color) website langsung dari panel admin tanpa menyentuh kode.
- **Manajemen Halaman Depan (Dynamic Panels)**: Kontrol penuh atas seksi halaman depan (Hero, Layanan, Portofolio Desain, Testimoni) yang dapat diaktifkan, dimatikan, dan disesuaikan isinya secara dinamis.
- **Portofolio & Proyek**: Manajemen katalog desain arsitektur yang mudah digunakan.
- **Role & Permission Management**: Integrasi Spatie Permission (Admin, Editor, Client).
- **Global Settings**: Konfigurasi nama situs, kontak, sosial media, dan identitas brand.
- **Premium UI Theme**: 
  - **Frontend**: Tampilan default elegan, profesional, dan responsif.
  - **Backend**: Panel admin modern berbasis Tailwind CSS.

---

## 🚀 Panduan Memulai Lokal

### 1. Kloning Repository
```bash
git clone git@github.com:hasanarofid/ivan-arstiek-sistem.git
cd ivan-arsitek
```

### 2. Install Dependensi
```bash
composer install
npm install
```

### 3. Setup Environment
Copy file `.env.example` menjadi `.env` lalu sesuaikan konfigurasi database Anda.
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Migrasi & Seed Database
Jalankan migrasi untuk membuat tabel dan mengisi data awal (termasuk template default).
```bash
php artisan migrate:fresh --seed
```

### 5. Hubungkan Storage Link
```bash
php artisan storage:link
```

### 6. Kompilasi & Jalankan Dev Server
```bash
# Terminal 1: Compile Asset Frontend
npm run dev

# Terminal 2: Serve Backend Laravel
php artisan serve
```

### 🔑 Kredensial Pengguna (Default Seeder)
- **Admin**: `admin@cms.com` / `password`
- **Editor**: `editor@cms.com` / `password`
- **Client**: `client@cms.com` / `password`

---

## 👨💻 Owner & Creator
Dikembangkan oleh:
- **Username**: [@hasanarofid.site](https://hasanarofid.site)
- **Website Resmi**: [https://hasanarofid.site](https://hasanarofid.site)
