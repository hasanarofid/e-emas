# Cahaya Ornamen — System Architecture & Business Flow

## 1. Ringkasan Eksekutif & Identitas Bisnis
- **Nama Bisnis:** Cahaya Ornamen (`cahayaornamen.com`)
- **Core Industry:** Manufaktur & Kontraktor Ornamen Arsitektur Klasik & Modern (GRC Krawangan, Lis Profil, Pilar Romawi/Korintus, Kubah Masjid, Ornamen Relief, dan Panel Dinding).
- **Core Value Proposition:** Presisi cetakan tinggi, formula GRC tahan cuaca ekstrem, custom design fleksibel, serta layanan end-to-end (desain, produksi pabrikasi, pengiriman, dan instalasi lapangan).

---

## 2. Stack Teknologi
| Layer | Teknologi | Peran & Alasan Pemilihan |
| :--- | :--- | :--- |
| **Backend Framework** | Laravel 11 | Router, Authentication, Media Management, ORM Eloquent, REST/Inertia Controller |
| **Frontend Framework** | Vue 3 (Composition API) | UI Reaktif, modular component-based, state management yang efisien |
| **Monolith Bridge** | Inertia.js v2 | Menghubungkan Laravel & Vue tanpa perlu membangun REST API terpisah, routing terintegrasi |
| **Styling Engine** | Tailwind CSS v3 | Utility-first CSS, custom token warna arsitektur warm & dark green, responsive grid |
| **Animation & Motion** | GSAP & Vue Transitions | Animasi halus, subtle parallax, hover micro-interactions, scroll reveal non-generic |
| **Database** | MySQL 8.x / MariaDB | Relasional database dengan indexing optimal untuk catalog & SEO article |

---

## 3. Alur Bisnis (End-to-End User Journey)

```
[ Pengunjung / Calon Klien ]
             │
             ├──► 1. Homepage (Editorial Vibe, Hero Asimetris, Value Prop)
             │
             ├──► 2. Katalog Produk (Filter: Krawangan, Kubah, Pilar, Lis)
             │         └── Modal / Detail Produk (Spesifikasi, Material GRC)
             │
             ├──► 3. Portofolio Proyek (Bento Grid, Foto Lapangan, Filter Tipe Bangunan)
             │         └── Case Study (Luas area, lingkup ornamen, lokasi)
             │
             ├──► 4. Artikel / Tips Arsitektur (Edukasi GRC, Panduan Desain, SEO)
             │
             └──► 5. Konversi Utama (Lead Capture)
                       ├── WhatsApp Floating / Direct CTA (Aksen #e1e100)
                       └── Form Konsultasi & Estimasi RAB
```

---

## 4. Struktur Direktori Modular

```
ivan-arstiek-sistem/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── HomeController.php          # Homepage Controller
│   │       ├── ProductController.php       # Katalog & Detail Produk
│   │       ├── PortfolioController.php     # Galeri Proyek Lapangan
│   │       ├── ArticleController.php       # Edukasi & Blog Arsitektur
│   │       ├── InquiryController.php       # Form Konsultasi & Kontak
│   │       └── Admin/                      # CMS Admin Panel
│   └── Models/
│       ├── Category.php
│       ├── Product.php
│       ├── ProductImage.php
│       ├── Portfolio.php
│       ├── PortfolioImage.php
│       ├── Post.php
│       ├── Inquiry.php
│       └── Setting.php
├── docs/                                  # Fundamental Rules & Architecture
│   ├── ARCHITECTURE.md
│   ├── DATABASE_SCHEMA.md
│   ├── DESIGN_SYSTEM.md
│   └── CODING_STANDARDS.md
├── resources/
│   ├── js/
│   │   ├── Components/
│   │   │   └── Frontend/                   # Reusable UI Blocks
│   │   │       ├── HeaderNavbar.vue
│   │   │       ├── HeroSection.vue
│   │   │       ├── AboutSection.vue
│   │   │       ├── ServicesSection.vue
│   │   │       ├── FeaturedCatalogSection.vue
│   │   │       ├── ProductCard.vue
│   │   │       ├── PortfolioBentoSection.vue
│   │   │       ├── TestimonialsSection.vue
│   │   │       ├── ArticlePreviewSection.vue
│   │   │       ├── ConsultationCtaSection.vue
│   │   │       ├── FooterSection.vue
│   │   │       └── FloatingWhatsapp.vue
│   │   └── Pages/
│   │       ├── Homepage.vue
│   │       ├── Products/
│   │       ├── Portfolios/
│   │       └── Articles/
└── tailwind.config.js                     # Custom Architectural Palette
```
