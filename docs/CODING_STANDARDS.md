# Cahaya Ornamen — Coding Standards & Development Rules

## 1. Prinsip Umum
- **Clean & Maintainable:** Mengutamakan keterbacaan, pemisahan tanggung jawab (Separation of Concerns), dan prinsip DRY (Don't Repeat Yourself).
- **Type Safety & Strict Validation:** Gunakan Form Requests di Laravel untuk semua validasi input.
- **Modular Component Design:** Hindari file Vue yang terlalu membengkak (monolithic). Pecah setiap section atau kartu yang reusable menjadi komponen terpisah di `resources/js/Components/Frontend/`.

---

## 2. Laravel 11 Backend Standards

### 2.1. Controller & Inertia Responses
- Controller harus tipis (*Skinny Controller*). Logika bisnis berat ditempatkan pada Services atau Query Scopes di Model.
- Semua return Inertia harus menyertakan tipe data yang jelas dan menghindari `Model::all()` mentah tanpa pagination atau pembatasan field (`select`).
```php
public function index(): \Inertia\Response
{
    $featuredProducts = Product::query()
        ->where('is_featured', true)
        ->where('status', 'published')
        ->with('category:id,name,slug')
        ->orderBy('order')
        ->take(6)
        ->get();

    return Inertia::render('Homepage', [
        'featuredProducts' => $featuredProducts,
    ]);
}
```

### 2.2. Eloquent Model Best Practices
- Selalu deklarasikan `$fillable` atau `$guarded`.
- Definisikan `$casts` untuk field JSON, boolean, atau tanggal.
- Buat accessor `image_url` untuk kemudahan rendering gambar di frontend jika menggunakan upload lokal / S3.

---

## 3. Vue 3 + Inertia.js Frontend Standards

### 3.1. Composition API dengan `<script setup>`
- Gunakan `<script setup>` secara konsisten.
- Gunakan `defineProps<{ ... }>()` dengan TypeScript atau Object Props yang memiliki `default` dan `required`.
- Manfaatkan Composable atau GSAP lifecycle hooks di `onMounted` dan bersihkan listener di `onUnmounted`.

### 3.2. Penamaan Komponen & File
- Komponen Vue menggunakan PascalCase: `ProductCard.vue`, `HeroSection.vue`, `FloatingWhatsapp.vue`.
- Folder komponen dikelompokkan berdasarkan domain:
  - `resources/js/Components/Frontend/` (Public Facing)
  - `resources/js/Components/Admin/` (CMS Management)
  - `resources/js/Components/UI/` (Button, Modal, Input Atoms)

### 3.3. Animasi & Interaksi
- Hindari animasi berlebihan yang mengorbankan performa mobile (LCP/CLS).
- Gunakan transisi CSS native Tailwind untuk hover (`transition-all duration-300`).
- Gunakan GSAP atau `IntersectionObserver` ringan untuk *fade-up* saat elemen mulai terlihat di viewport.
