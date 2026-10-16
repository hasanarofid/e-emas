<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'site_name',
                'value' => 'POS Emas',
                'type' => 'text',
            ],
            [
                'key' => 'site_description',
                'value' => 'Sistem Kasir (POS) & Manajemen Toko Perhiasan Emas Modern - posemas.web.id',
                'type' => 'textarea',
            ],
            [
                'key' => 'site_logo',
                'value' => null,
                'type' => 'image',
            ],
            [
                'key' => 'whatsapp_number',
                'value' => '6281234567890',
                'type' => 'text',
            ],
            [
                'key' => 'playstore_link',
                'value' => 'https://posemas.web.id',
                'type' => 'url',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
