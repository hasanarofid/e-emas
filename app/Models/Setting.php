<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'group',
    ];

    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function getAll(): array
    {
        $defaults = [
            'store_name' => 'Toko Mas Permata Abadi',
            'store_legal_name' => 'UD. Permata Abadi Gold & Jewelry',
            'store_phone' => '0812-3456-7890',
            'store_address' => 'Jl. Malioboro No. 88, Danurejan, Yogyakarta 55213',
            'store_permit_number' => 'SIUP: 510/088/EMAS/DIY/2026',
            'primary_color' => '#D4AF37',
            'gold_buyback_policy' => "Barang dapat dijual kembali (buyback) dengan menunjukkan surat emas resmi ini.\nPotongan harga mengikuti harga pasaran emas hari berjalan dan kondisi fisik barang.\nOngkos bikin tidak dapat diuangkan kembali saat transaksi buyback.",
            'receipt_footer' => 'Terima kasih atas kunjungan dan kepercayaan Anda. Investasi cerdas perhiasan berkualitas bersama Toko Mas Permata Abadi.',
        ];

        $dbSettings = static::pluck('value', 'key')->toArray();

        return array_merge($defaults, $dbSettings);
    }

    public static function set(string $key, mixed $value, string $group = 'general'): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group]
        );
    }
}
