# Cahaya Ornamen — Database Schema & Data Dictionary

## 1. Entity Relationship Overview
Sistem ini menggunakan struktur relasional terpadu antara:
- **`categories`**: Mengelompokkan produk, portofolio, dan artikel secara fleksibel menggunakan field `type`.
- **`products` & `product_images`**: Katalog ornamen arsitektur dengan spesifikasi teknis dan multi-gambar.
- **`portfolios` & `portfolio_images`**: Dokumentasi proyek pemasangan di lapangan dengan metadata proyek (klien, lokasi, tahun, lingkup ornamen).
- **`posts` (Articles)**: Artikel tips dan panduan teknis ornamen untuk optimasi SEO on-page.
- **`inquiries`**: Lead capture untuk konsultasi, request katalog, atau estimasi RAB.

---

## 2. Definisi Skema Tabel

### 2.1. Tabel `categories`
Menyimpan taksonomi kategori untuk Produk, Portofolio, atau Artikel.
```sql
CREATE TABLE `categories` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `type` ENUM('product', 'portfolio', 'post') NOT NULL DEFAULT 'product',
  `description` TEXT NULL,
  `image` VARCHAR(255) NULL,
  `order` INT NOT NULL DEFAULT 0,
  `is_active` BOOLEAN NOT NULL DEFAULT TRUE,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  INDEX `idx_categories_type_active` (`type`, `is_active`)
);
```

### 2.2. Tabel `products` & `product_images`
Menyimpan katalog item ornamen (Krawangan GRC, Pilar, Lis Profil, Kubah, dll).
```sql
CREATE TABLE `products` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `category_id` BIGINT UNSIGNED NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `sku` VARCHAR(100) NULL,
  `material` VARCHAR(100) NOT NULL DEFAULT 'GRC (Glassfibre Reinforced Concrete)',
  `dimensions` VARCHAR(255) NULL,
  `short_description` VARCHAR(500) NULL,
  `description` LONGTEXT NULL,
  `specifications` JSON NULL, -- { "ketebalan": "15-20mm", "berat_m2": "30-40kg", "finishing": "Cat Weatherproof / Raw" }
  `thumbnail` VARCHAR(255) NULL,
  `price_range` VARCHAR(150) NULL, -- Misal: "Mulai Rp 180.000 / m²" atau "Hubungi Sales"
  `is_featured` BOOLEAN NOT NULL DEFAULT FALSE,
  `status` ENUM('draft', 'published', 'archived') NOT NULL DEFAULT 'published',
  `order` INT NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE RESTRICT,
  INDEX `idx_products_featured_status` (`is_featured`, `status`),
  INDEX `idx_products_category` (`category_id`)
);

CREATE TABLE `product_images` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `product_id` BIGINT UNSIGNED NOT NULL,
  `image_path` VARCHAR(255) NOT NULL,
  `caption` VARCHAR(255) NULL,
  `order` INT NOT NULL DEFAULT 0,
  `is_primary` BOOLEAN NOT NULL DEFAULT FALSE,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
);
```

### 2.3. Tabel `portfolios` & `portfolio_images`
Menyimpan portofolio hasil pemasangan fisik ornamen arsitektur di proyek riil.
```sql
CREATE TABLE `portfolios` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `category_id` BIGINT UNSIGNED NULL,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `client_name` VARCHAR(255) NULL,
  `project_type` VARCHAR(100) NULL, -- Masjid, Hunian Klasik, Gedung Pemerintah, Villa
  `location` VARCHAR(255) NULL,     -- Surabaya, Jakarta, Medan, dll.
  `completion_year` VARCHAR(10) NULL,
  `ornament_scope` VARCHAR(255) NULL, -- Misal: "Krawangan GRC 240 m², Kubah Ø8m"
  `description` TEXT NULL,
  `thumbnail` VARCHAR(255) NULL,
  `is_featured` BOOLEAN NOT NULL DEFAULT FALSE,
  `status` ENUM('draft', 'published') NOT NULL DEFAULT 'published',
  `order` INT NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL,
  INDEX `idx_portfolios_featured_status` (`is_featured`, `status`)
);

CREATE TABLE `portfolio_images` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `portfolio_id` BIGINT UNSIGNED NOT NULL,
  `image_path` VARCHAR(255) NOT NULL,
  `caption` VARCHAR(255) NULL,
  `order` INT NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  FOREIGN KEY (`portfolio_id`) REFERENCES `portfolios`(`id`) ON DELETE CASCADE
);
```

### 2.4. Tabel `inquiries` (Leads & Konsultasi WA/Form)
Menyimpan leads dari calon klien yang mengisi formulir penawaran / konsultasi ornamen.
```sql
CREATE TABLE `inquiries` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(50) NOT NULL,
  `email` VARCHAR(255) NULL,
  `project_type` VARCHAR(100) NULL,
  `location` VARCHAR(255) NULL,
  `estimated_volume` VARCHAR(100) NULL, -- misal: ~50m², 1 Kubah
  `message` TEXT NOT NULL,
  `status` ENUM('new', 'contacted', 'quoted', 'closed') NOT NULL DEFAULT 'new',
  `notes` TEXT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  INDEX `idx_inquiries_status` (`status`)
);
```
