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
                'value' => 'Cahaya Ornamen',
                'type' => 'text',
            ],
            [
                'key' => 'site_description',
                'value' => 'Spesialis Ornamen Arsitektur Klasik, GRC Krawangan, Pilar, & Kubah dengan presisi cetakan tinggi.',
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
                'value' => 'https://play.google.com/store/apps/details?id=com.mycms.app',
                'type' => 'url',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
