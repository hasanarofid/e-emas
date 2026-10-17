<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\GoldRate;
use App\Models\JewelryService;
use App\Models\PawnTransaction;
use App\Models\Tray;
use App\Models\TrayScaleLog;
use App\Models\User;
use Illuminate\Database\Seeder;

class SpecialOperationsSeeder extends Seeder
{
    /**
     * Seed initial demo data for Pawnbroking, Services, and Tray Scales
     */
    public function run(): void
    {
        $customer = Customer::first();
        $goldRate = GoldRate::first();
        $user = User::first();
        $tray = Tray::first();

        if ($customer && $goldRate) {
            // Demo Gadai
            PawnTransaction::updateOrCreate(
                ['pawn_number' => 'GD-202610-0001'],
                [
                    'customer_id' => $customer->id,
                    'customer_name' => $customer->name,
                    'customer_phone' => $customer->phone,
                    'item_name' => 'Kalung Rantai Emas Kuning 22K',
                    'gold_rate_id' => $goldRate->id,
                    'gross_weight' => 5.450,
                    'estimated_value' => 6431000,
                    'loan_amount' => 5000000,
                    'monthly_interest_rate' => 2.00,
                    'monthly_fee' => 100000,
                    'start_date' => now()->toDateString(),
                    'due_date' => now()->addMonth()->toDateString(),
                    'status' => 'active',
                    'cashier_id' => $user?->id,
                    'notes' => 'Kondisi mulus, tanpa surat pembelian asli toko.',
                ]
            );

            // Demo Servis
            JewelryService::updateOrCreate(
                ['service_number' => 'SRV-202610-0001'],
                [
                    'customer_id' => $customer->id,
                    'customer_name' => $customer->name,
                    'customer_phone' => $customer->phone,
                    'item_name' => 'Cincin Solitaire Berlian',
                    'weight_before' => 3.200,
                    'service_type' => 'Pasang Permata & Cuci Sepuh',
                    'damage_description' => 'Mata zirkon lepas 1 butir, kusam perlu disepuh ulang emas kuning',
                    'estimated_cost' => 150000,
                    'down_payment' => 50000,
                    'remaining_cost' => 100000,
                    'received_date' => now()->toDateString(),
                    'completion_target_date' => now()->addDays(3)->toDateString(),
                    'status' => 'in_progress',
                    'cashier_id' => $user?->id,
                    'notes' => 'Permata sintetis zircon diganti baru kualitas Swiss',
                ]
            );
        }

        if ($tray && $user) {
            TrayScaleLog::updateOrCreate(
                [
                    'tray_id' => $tray->id,
                    'scale_date' => now()->toDateString(),
                    'session' => 'morning',
                ],
                [
                    'item_count_system' => $tray->items()->where('status', 'available')->count(),
                    'weight_system' => (float) $tray->items()->where('status', 'available')->sum('gross_weight'),
                    'item_count_real' => $tray->items()->where('status', 'available')->count(),
                    'weight_real' => (float) $tray->items()->where('status', 'available')->sum('gross_weight'),
                    'difference_weight' => 0.000,
                    'status' => 'matched',
                    'user_id' => $user->id,
                    'notes' => 'Timbang pagi pembukaan kasir etalase. Cocok 100%.',
                ]
            );
        }
    }
}
