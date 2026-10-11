<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Portfolio;
use App\Models\Post;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    /**
     * Menampilkan Homepage Cahaya Ornamen dengan nuansa editorial & arsitektural.
     */
    public function index(): Response
    {
        // 1. Data Kategori Produk Ornamen
        $categories = Category::where('type', 'product')
            ->where('is_active', true)
            ->orderBy('order')
            ->take(6)
            ->get();

        if ($categories->isEmpty()) {
            $categories = collect([
                [
                    'id' => 1,
                    'name' => 'GRC Krawangan',
                    'slug' => 'grc-krawangan',
                    'description' => 'Panel ventilasi dekoratif motif islami, geometris, & floral dengan ketahanan cuaca tinggi.',
                    'image' => 'https://images.unsplash.com/photo-1541888946425-d0fbb186f5f8?auto=format&fit=crop&w=800&q=80',
                    'count' => '48+ Model'
                ],
                [
                    'id' => 2,
                    'name' => 'Kubah Masjid GRC',
                    'slug' => 'kubah-masjid-grc',
                    'description' => 'Kubah ringan, kuat, kedap air, presisi tinggi dengan rangka baja galvanis anti-karat.',
                    'image' => 'https://images.unsplash.com/photo-1584551246679-0daf3d275d0f?auto=format&fit=crop&w=800&q=80',
                    'count' => '12+ Diameter'
                ],
                [
                    'id' => 3,
                    'name' => 'Pilar & Kolom Klasik',
                    'slug' => 'pilar-kolom-klasik',
                    'description' => 'Pilar gaya Korintus, Doria, & Ionic untuk hunian mewah dan gedung prestisius.',
                    'image' => 'https://images.unsplash.com/photo-1513694203232-719a280e022f?auto=format&fit=crop&w=800&q=80',
                    'count' => '24+ Desain'
                ],
                [
                    'id' => 4,
                    'name' => 'Lis Profil & Cornice',
                    'slug' => 'lis-profil-cornice',
                    'description' => 'Lis pinggir atap, moulding jendela, dan moulding kanopi finishing halus presisi.',
                    'image' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=800&q=80',
                    'count' => '60+ Profil'
                ],
                [
                    'id' => 5,
                    'name' => 'Ornamen Masif & Relief',
                    'slug' => 'ornamen-masif-relief',
                    'description' => 'Mural dinding 3D arsitektural dan ukiran kaligrafi timbul berbahan semen GRC padat.',
                    'image' => 'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=800&q=80',
                    'count' => '35+ Motif'
                ],
                [
                    'id' => 6,
                    'name' => 'Roster & Gazebo Arsitektur',
                    'slug' => 'roster-gazebo-arsitektur',
                    'description' => 'Elemen fasad peredam panas matahari dan gazebo taman outdoor tahan lumut.',
                    'image' => 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=800&q=80',
                    'count' => '16+ Desain'
                ],
            ]);
        }

        // 2. Produk Unggulan (Featured Products)
        $featuredProducts = Product::where('is_featured', true)
            ->where('status', 'published')
            ->with('category')
            ->orderBy('order')
            ->take(6)
            ->get();

        if ($featuredProducts->isEmpty()) {
            $featuredProducts = collect([
                [
                    'id' => 1,
                    'name' => 'Krawangan Fasad Motif Maroko Islamic',
                    'slug' => 'krawangan-fasad-maroko-islamic',
                    'category' => ['name' => 'GRC Krawangan'],
                    'material' => 'GRC Alkali-Resistant Glassfibre',
                    'dimensions' => '120 x 240 cm (Tebal 20mm)',
                    'price_range' => 'Mulai Rp 220.000 / m²',
                    'thumbnail' => 'https://images.unsplash.com/photo-1541888946425-d0fbb186f5f8?auto=format&fit=crop&w=800&q=80',
                    'is_featured' => true,
                    'short_description' => 'Panel ventilasi motif bintang 8 geometri khas Maroko dengan sirkulasi udara optimal dan peredam silau matahari.',
                ],
                [
                    'id' => 2,
                    'name' => 'Pilar Utama Roman Corithian Full Capital',
                    'slug' => 'pilar-roman-corithian',
                    'category' => ['name' => 'Pilar & Kolom Klasik'],
                    'material' => 'Beton Cor GRC Bertulang',
                    'dimensions' => 'Diameter 50cm x Tinggi 4.2m',
                    'price_range' => 'Hubungi Estimator',
                    'thumbnail' => 'https://images.unsplash.com/photo-1513694203232-719a280e022f?auto=format&fit=crop&w=800&q=80',
                    'is_featured' => true,
                    'short_description' => 'Pilar ornamen klasik gaya Eropa dengan ukiran kepala akantus detail tinggi dan batang pilar beralur flute.',
                ],
                [
                    'id' => 3,
                    'name' => 'Kubah Utama GRC Double Skin Motif Enamel',
                    'slug' => 'kubah-utama-grc-double-skin',
                    'category' => ['name' => 'Kubah Masjid GRC'],
                    'material' => 'GRC Composite + Rangka Pipa Galvanis',
                    'dimensions' => 'Diameter 8 Meter (Tinggi 6.5m)',
                    'price_range' => 'Kustom Sesuai Volume',
                    'thumbnail' => 'https://images.unsplash.com/photo-1584551246679-0daf3d275d0f?auto=format&fit=crop&w=800&q=80',
                    'is_featured' => true,
                    'short_description' => 'Struktur kubah modern berbobot ringan, 100% kedap air, tahan gempa, dan dilapisi cat polyurethane anti-UV.',
                ],
                [
                    'id' => 4,
                    'name' => 'Lis Profil Kanopi Baroque Classic',
                    'slug' => 'lis-profil-kanopi-baroque',
                    'category' => ['name' => 'Lis Profil & Cornice'],
                    'material' => 'GRC Cor Cetak Presisi',
                    'dimensions' => 'Panjang 200cm x Lebar 35cm',
                    'price_range' => 'Mulai Rp 95.000 / m lari',
                    'thumbnail' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=800&q=80',
                    'is_featured' => true,
                    'short_description' => 'Moulding arsitektural pinggir dak beton untuk memberi aksen kemewahan garis bayang pada fasad rumah klasik.',
                ],
                [
                    'id' => 5,
                    'name' => 'Panel Dinding Relief Kaligrafi Khot Tsuluts',
                    'slug' => 'panel-relief-kaligrafi-tsuluts',
                    'category' => ['name' => 'Ornamen Masif & Relief'],
                    'material' => 'Semen GRC Padat Finis Marmer Putih',
                    'dimensions' => '150 x 300 cm Panel Modular',
                    'price_range' => 'Mulai Rp 380.000 / m²',
                    'thumbnail' => 'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=800&q=80',
                    'is_featured' => true,
                    'short_description' => 'Karya seni dinding arsitektural mihrab masjid dan foyer mewah dengan ukiran timbul presisi milimeter.',
                ],
                [
                    'id' => 6,
                    'name' => 'Roster Fasad Modern Tropical Shadow Box',
                    'slug' => 'roster-fasad-modern-tropical',
                    'category' => ['name' => 'Roster & Gazebo Arsitektur'],
                    'material' => 'GRC High-Density Waterproof',
                    'dimensions' => '30 x 30 x 10 cm Modular',
                    'price_range' => 'Mulai Rp 45.000 / pcs',
                    'thumbnail' => 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=800&q=80',
                    'is_featured' => true,
                    'short_description' => 'Blok angin arsitektur kontemporer yang menciptakan bayangan artistik di dalam ruangan sepanjang hari.',
                ],
            ]);
        }

        // 3. Portofolio Proyek Terpasang (Bento Grid)
        $portfolios = Portfolio::where('is_featured', true)
            ->where('status', 'published')
            ->orderBy('order')
            ->take(5)
            ->get();

        if ($portfolios->isEmpty()) {
            $portfolios = collect([
                [
                    'id' => 1,
                    'title' => 'Fasad GRC Krawangan Masjid Agung Al-Munawwar',
                    'slug' => 'masjid-agung-al-munawwar',
                    'project_type' => 'Masjid Raya & Landmark',
                    'location' => 'Malang, Jawa Timur',
                    'completion_year' => '2024',
                    'ornament_scope' => 'Fasad Krawangan 450 m², Kubah Utama Ø9m, 4 Menara GRC',
                    'thumbnail' => 'https://images.unsplash.com/photo-1584551246679-0daf3d275d0f?auto=format&fit=crop&w=1200&q=80',
                    'bento_span' => 'col-span-12 lg:col-span-8',
                ],
                [
                    'id' => 2,
                    'title' => 'Kemegahan Pilar & Balustrade Villa Royal Citra',
                    'slug' => 'villa-royal-citra',
                    'project_type' => 'Hunian Klasik Mewah',
                    'location' => 'Surabaya Barat',
                    'completion_year' => '2024',
                    'ornament_scope' => '16 Pilar Korintus H:5.5m, Lis Profil Keliling 380m',
                    'thumbnail' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=800&q=80',
                    'bento_span' => 'col-span-12 sm:col-span-6 lg:col-span-4',
                ],
                [
                    'id' => 3,
                    'title' => 'Secondary Skin Arsitektur Gedung Graha Nusantara',
                    'slug' => 'gedung-graha-nusantara',
                    'project_type' => 'Gedung Komersial',
                    'location' => 'Jakarta Selatan',
                    'completion_year' => '2023',
                    'ornament_scope' => 'GRC Perforated Panel 820 m² Rangka Baja',
                    'thumbnail' => 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=800&q=80',
                    'bento_span' => 'col-span-12 sm:col-span-6 lg:col-span-4',
                ],
                [
                    'id' => 4,
                    'title' => 'Mihrab Kaligrafi & Ornamen Plafon Pesantren Modern',
                    'slug' => 'pesantren-modern-darul-ulum',
                    'project_type' => 'Fasilitas Pendidikan & Ibadah',
                    'location' => 'Yogyakarta',
                    'completion_year' => '2023',
                    'ornament_scope' => 'Relief Kaligrafi 3D, Drop Ceiling GRC Kubah',
                    'thumbnail' => 'https://images.unsplash.com/photo-1541888946425-d0fbb186f5f8?auto=format&fit=crop&w=800&q=80',
                    'bento_span' => 'col-span-12 sm:col-span-6 lg:col-span-4',
                ],
                [
                    'id' => 5,
                    'title' => 'Pagar & Gapura Ornamen Tematik Resort Tropis',
                    'slug' => 'resort-tropis-ubud',
                    'project_type' => 'Hospitality & Resort',
                    'location' => 'Bali',
                    'completion_year' => '2024',
                    'ornament_scope' => 'Gapura Ornamen Semen GRC Cor, Roster Kustom 200 m²',
                    'thumbnail' => 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=800&q=80',
                    'bento_span' => 'col-span-12 sm:col-span-6 lg:col-span-4',
                ],
            ]);
        }

        // 4. Layanan Arsitektural Utama
        $services = [
            [
                'number' => '01',
                'title' => 'Desain Kustom & Gambar Kerja 3D',
                'description' => 'Kami menerjemahkan gambar arsitek menjadi gambar kerja teknis (shop drawing) dan cetakan CNC presisi milimeter sesuai bentuk bangunan Anda.',
                'icon' => 'Compass',
                'tags' => ['Free 3D Mockup', 'CAD / BIM Ready', 'Presisi CNC'],
            ],
            [
                'number' => '02',
                'title' => 'Produksi Cetak GRC Pabrikasi Mandiri',
                'description' => 'Diproduksi langsung di workshop sendiri dengan formula semen khusus, pasir silica murni, dan serat fiberglass tahan alkali (AR-Glass).',
                'icon' => 'Factory',
                'tags' => ['Workshop 2500m²', 'Kontrol Kualitas Ketat', 'Anti-Retak & Ringan'],
            ],
            [
                'number' => '03',
                'title' => 'Pengiriman Aman & Instalasi Lapangan',
                'description' => 'Tim teknisi berpengalaman siap melakukan pemasangan rangka baja, dynabolt, pengelasan struktur, hingga finishing cat weatherproof di seluruh Indonesia.',
                'icon' => 'ShieldCheck',
                'tags' => ['Tim Bersertifikat K3', 'Jangkauan Se-Indonesia', 'Garansi Pemasangan'],
            ],
            [
                'number' => '04',
                'title' => 'Konsultasi Struktur & Estimasi RAB',
                'description' => 'Dapatkan estimasi biaya transparan, rekomendasi beban struktur bangunan, dan sampel fisik material sebelum proyek dimulai.',
                'icon' => 'Calculator',
                'tags' => ['Konsultasi Gratis', 'RAB Transparan', 'Sample Material'],
            ],
        ];

        // 5. Testimoni Klien & Mitra Arsitek
        $testimonials = [
            [
                'quote' => 'Presisi krawangan GRC dari Cahaya Ornamen luar biasa rapi. Sambungan antar panel nyaris tidak terlihat setelah difinishing. Masjid kami terlihat megah dan megapolitan.',
                'author' => 'Ir. Hendra Kusuma, IAI',
                'role' => 'Principal Architect, Studio Reka Ruang',
                'project' => 'Proyek Masjid Malang',
                'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=200&q=80',
            ],
            [
                'quote' => 'Pilar Korintus dan lis profil untuk rumah klasik klien kami di Citraland dikerjakan tepat waktu. Ketebalan material solid, tidak ringkih seperti produk pasaran lain.',
                'author' => 'Bambang Sudarsono',
                'role' => 'Kontraktor Utama, PT Cipta Graha Mandiri',
                'project' => 'Hunian Klasik Citraland',
                'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=200&q=80',
            ],
            [
                'quote' => 'Komunikasi sangat mudah lewat WhatsApp, tim estimator sigap menghitung volume dan memberikan solusi teknis pemasangan di ketinggian 18 meter tanpa kendala.',
                'author' => 'H. Ahmad Fauzi',
                'role' => 'Ketua Panitia Pembangunan Masjid',
                'project' => 'Kubah GRC Jawa Timur',
                'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=200&q=80',
            ],
        ];

        // 6. Artikel / Tips Arsitektur (SEO Blog)
        $articles = Post::where('status', 'published')
            ->with('category')
            ->latest()
            ->take(3)
            ->get();

        if ($articles->isEmpty()) {
            $articles = collect([
                [
                    'id' => 1,
                    'title' => 'Mengenal GRC Krawangan: Solusi Fasad Bernapas Tahan Cuaca Tropis',
                    'slug' => 'mengenal-grc-krawangan-solusi-fasad-tahan-cuaca',
                    'excerpt' => 'Mengapa arsitek modern lebih memilih GRC dibanding beton konvensional untuk fasad berlubang? Simak perbandingan bobot, kekuatan, dan daya tahannya.',
                    'image_url' => 'https://images.unsplash.com/photo-1541888946425-d0fbb186f5f8?auto=format&fit=crop&w=800&q=80',
                    'category' => ['name' => 'Tips Arsitektur'],
                    'reading_time' => 4,
                    'published_at' => '10 Okt 2026',
                ],
                [
                    'id' => 2,
                    'title' => 'Panduan Memilih Ornamen Pilar Klasik: Perbedaan Gaya Doria, Ionic, dan Korintus',
                    'slug' => 'panduan-memilih-ornamen-pilar-klasik',
                    'excerpt' => 'Pilar klasik memberikan nilai prestise tinggi pada fasad bangunan. Ketahui proporsi diameter dan ketinggian ideal agar hunian terlihat proporsional.',
                    'image_url' => 'https://images.unsplash.com/photo-1513694203232-719a280e022f?auto=format&fit=crop&w=800&q=80',
                    'category' => ['name' => 'Desain Klasik'],
                    'reading_time' => 5,
                    'published_at' => '05 Okt 2026',
                ],
                [
                    'id' => 3,
                    'title' => 'Keunggulan Kubah GRC Dibanding Kubah Enamel & Cor Konvensional',
                    'slug' => 'keunggulan-kubah-grc-dibanding-cor-konvensional',
                    'excerpt' => 'Kubah GRC memiliki bobot 80% lebih ringan dibanding cor semen biasa, sehingga menghemat biaya struktur fondasi dan rangka bentang lebar.',
                    'image_url' => 'https://images.unsplash.com/photo-1584551246679-0daf3d275d0f?auto=format&fit=crop&w=800&q=80',
                    'category' => ['name' => 'Konstruksi Masjid'],
                    'reading_time' => 6,
                    'published_at' => '28 Sep 2026',
                ],
            ]);
        }

        // 7. Pengaturan Kontak & Brand Info
        $contactInfo = [
            'brand_name' => 'Cahaya Ornamen',
            'tagline' => 'Spesialis Ornamen Arsitektur Klasik, GRC Krawangan, Pilar, & Kubah',
            'phone' => '+62 813-1605-3870',
            'whatsapp_number' => '6281316053870',
            'whatsapp_message' => 'Halo Cahaya Ornamen, saya ingin berkonsultasi mengenai kebutuhan ornamen arsitektur (GRC/Krawangan/Pilar/Kubah).',
            'email' => 'halo@cahayaornamen.com',
            'workshop_address' => 'Jl. Raya Industri Arsitektur No. 88, Kawasan Workshop GRC, Jawa Timur, Indonesia',
            'working_hours' => 'Senin - Sabtu: 08.00 - 17.00 WIB',
        ];

        return Inertia::render('Homepage', [
            'categories' => $categories,
            'featuredProducts' => $featuredProducts,
            'portfolios' => $portfolios,
            'services' => $services,
            'testimonials' => $testimonials,
            'articles' => $articles,
            'contactInfo' => $contactInfo,
        ]);
    }
}
