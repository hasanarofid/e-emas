# Cahaya Ornamen — Design System & UI/UX Guidelines

## 1. Visi & Filosofi Desain (Anti-Generic / Anti-AI Look)
Cahaya Ornamen bergerak dalam seni arsitektur berdaya tahan tinggi (GRC, Relief, Pilar Klasik, Krawangan). Desain website **bukan template SaaS kaku atau card serba kotak**, melainkan **editorial, berkelas, asimetris, dan berjiwa arsitektural**.

### Karakteristik Visual Utama:
1. **Asymmetrical Balance:** Hero section dan bento showcase tidak sekadar kolom 50/50. Gunakan tata letak offset, tipografi vertikal/badge elegan, dan overlapping cards.
2. **Overlapping Layers (Depth):** Foto ornamen menembus kontur background atau mengapung di atas card latar belakang (`-translate-y-8` atau border framing khas studio arsitektur).
3. **Architectural Grid & Hairline Dividers:** Garis pemisah tipis (*hairline borders*) warna `#2a7c13` atau `#063b00`/15 yang membangkitkan nuansa cetak biru (*blueprint*) dan presisi teknik.
4. **Natural Motion & Micro-Interactions:** Hover gambar bertransisi lembut (`duration-700 ease-out`), zoom skala halus (`scale-105`), dan scroll reveal bertahap (*staggered fade-up*).

---

## 2. Palet Warna (Color Tokens)

Mapping ke konfigurasi Tailwind CSS:

| Token Name | Hex Code | Deskripsi Penggunaan |
| :--- | :--- | :--- |
| `brand.cream` | `#fff8cf` | **Background Utama:** Warm off-white/cream, lembut di mata, memberi nuansa bahan gypsum/GRC klasik premium. |
| `brand.sand` | `#fbe6c2` | **Surface & Alternate Section:** Digunakan untuk background card produk, bento box, atau section sekunder. |
| `brand.dark` | `#063b00` | **Deepest Text & Headers:** Hijau sangat gelap (hampir hitam hijau pinus), kontras tajam untuk teks judul utama. |
| `brand.deep` | `#266210` | **Footer & Dark Containers:** Dark forest green untuk footer, banner eksklusif, dan penegasan visual. |
| `brand.primary` | `#2a7c13` | **Primary Brand Green:** Garis arsitektur, divider aksen, outline tombol sekunder, dan badge ornamen. |
| `brand.light` | `#76c457` | **Fresh Green:** Sub-aksen, ikon ornamen, hover state elemen sekunder. |
| `brand.leaf` | `#90b800` | **Leaf Green Accent:** Micro-badge keunggulan material (tahan gempa, anti lumut, presisi CNC). |
| `brand.cta` | `#e1e100` | **LIME/YELLOW CTA HIGHLIGHT:** **Eksklusif** untuk tombol aksi utama (*Call to Action* "Hubungi Kami" / "Konsultasi WA"). Sangat kontras dan langsung memandu mata pengunjung. |

---

## 3. Tipografi Editorial

- **Headings (H1, H2, H3):** `'Playfair Display', serif`
  - Karakter: Anggun, klasik, mewah, memberikan bobot estetika seperti majalah arsitektur papan atas.
  - Tracking: `tracking-tight` atau `tracking-normal`.
- **Body & Subtitles:** `'Plus Jakarta Sans', 'DM Sans', sans-serif`
  - Karakter: Modern, geometris bersih, mudah dibaca pada layar perangkat mobile maupun desktop resolusi tinggi.
  - Weights: `font-light` (300) untuk quotes/sub-header, `font-normal` (400) untuk paragraf, `font-semibold` (600) untuk label teknis.

---

## 4. Aturan Radius & Elemen Bentuk
- Hindari radius berbentuk kapsul bulat berlebihan di seluruh card karena mengurangi kesan solid ornamen beton/GRC.
- Standar Card Radius: `rounded-xl` (12px) atau `rounded-2xl` (16px) dengan border halus (`border border-brand-dark/10`).
- Gambar ornamen dibingkai rapi dengan *architectural frame* atau bayangan lembut natural (`shadow-[0_20px_50px_rgba(6,59,0,0.08)]`).
