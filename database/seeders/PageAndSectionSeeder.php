<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\Section;
use Illuminate\Database\Seeder;

class PageAndSectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $homePage = Page::updateOrCreate(
            ['slug' => 'home'],
            [
                'title' => 'Home',
                'meta_description' => 'Sistem POS Kasir dan Manajemen Toko Emas & Perhiasan Modern - posemas.web.id',
                'is_active' => true
            ]
        );

        $sections = [
            [
                'key' => 'hero',
                'title' => 'Hero Banner',
                'order' => 1,
                'content' => [
                    'headline' => 'Transformasikan Bisnis Anda Bersama Kami',
                    'subheadline' => 'Membangun ekosistem digital premium dengan teknologi Laravel 11 & Vue 3 Inertia.',
                    'cta_text' => 'Mulai Sekarang',
                    'cta_url' => '/register',
                ]
            ],
            [
                'key' => 'features',
                'title' => 'Fitur Utama',
                'order' => 2,
                'content' => [
                    'title' => 'Kenapa Memilih Kami?',
                    'items' => [
                        [
                            'title' => 'Performa Super Cepat',
                            'description' => 'Didukung oleh optimasi Inertia.js dan caching engine di sisi backend.'
                        ],
                        [
                            'title' => 'Desain Fleksibel',
                            'description' => 'Menggunakan Tailwind CSS untuk kustomisasi tampilan yang responsif.'
                        ],
                        [
                            'title' => 'Arsitektur Bersih',
                            'description' => 'Sistem folder terstruktur rapi memudahkan scaling di kemudian hari.'
                        ]
                    ]
                ]
            ],
            [
                'key' => 'testimonials',
                'title' => 'Testimoni Klien',
                'order' => 3,
                'content' => [
                    'title' => 'Apa Kata Klien Kami',
                    'items' => [
                        [
                            'name' => 'Budi Santoso',
                            'role' => 'Tech Lead TokoKita',
                            'comment' => 'CMS boilerplate ini mempercepat development waktu kami hingga 70%. Kode backend & frontend menyatu dengan sangat indah!'
                        ],
                        [
                            'name' => 'Siti Aminah',
                            'role' => 'Founder HijabStyle',
                            'comment' => 'Tampilan admin panel-nya sangat intuitif dan mudah digunakan oleh tim admin kami.'
                        ]
                    ]
                ]
            ]
        ];

        foreach ($sections as $sec) {
            Section::updateOrCreate(
                [
                    'page_id' => $homePage->id,
                    'key' => $sec['key']
                ],
                [
                    'title' => $sec['title'],
                    'order' => $sec['order'],
                    'content' => $sec['content'],
                    'is_active' => true
                ]
            );
        }
    }
}
