<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
  Award, Users, Coins, Printer, ArrowLeft, 
  TrendingUp, CheckCircle2, FileText
} from 'lucide-vue-next';

const props = defineProps({
  marketers: Array,
  stats: Object,
});

const formatRupiah = (val) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val || 0);
};
</script>

<template>
  <AdminLayout>
    <Head title="Rekapitulasi Fee Marketer & Mitra Agen Emas" />

    <div class="space-y-6">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2 text-xs font-mono text-amber-500 uppercase tracking-widest">
            <Award class="w-4 h-4" />
            <span>Kemitraan & Bagi Hasil Komisi</span>
          </div>
          <h2 class="text-2xl font-bold tracking-tight text-white mt-1">
            Rekapitulasi Fee Marketer
          </h2>
          <p class="text-sm text-slate-400">
            Daftar komisi bagi hasil penjualan perhiasan emas untuk agen marketer, makelar, dan reseller toko.
          </p>
        </div>

        <div class="flex items-center gap-3">
          <button 
            @click="window.print()"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-bold text-sm shadow-lg shadow-amber-500/20 cursor-pointer transition-all"
          >
            <Printer class="w-4 h-4" />
            <span>Cetak Rekap Fee</span>
          </button>
        </div>
      </div>

      <!-- KPI Summary -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-[#1C1C3E] border border-[#2D2D54] rounded-2xl p-5 flex items-center justify-between">
          <div>
            <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Total Agen Marketer</p>
            <h3 class="text-2xl font-bold text-white mt-1">{{ stats.total_marketers }} Orang</h3>
            <span class="text-xs text-slate-400 mt-1">Mitra penjualan toko</span>
          </div>
          <div class="w-12 h-12 rounded-xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center">
            <Users class="w-6 h-6" />
          </div>
        </div>

        <div class="bg-[#1C1C3E] border border-[#2D2D54] rounded-2xl p-5 flex items-center justify-between">
          <div>
            <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Omset Penjualan Marketer</p>
            <h3 class="text-2xl font-bold text-amber-400 mt-1">{{ formatRupiah(stats.total_omset_generated) }}</h3>
            <span class="text-xs text-slate-400 mt-1">Total nilai penjualan emas</span>
          </div>
          <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center">
            <TrendingUp class="w-6 h-6" />
          </div>
        </div>

        <div class="bg-[#1C1C3E] border border-[#2D2D54] rounded-2xl p-5 flex items-center justify-between">
          <div>
            <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Total Alokasi Komisi / Fee</p>
            <h3 class="text-2xl font-bold text-emerald-400 mt-1">{{ formatRupiah(stats.total_commission_fee) }}</h3>
            <span class="text-xs text-slate-400 mt-1">Bagi hasil 1.5%</span>
          </div>
          <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center">
            <Coins class="w-6 h-6" />
          </div>
        </div>
      </div>

      <!-- Data Table -->
      <div class="bg-[#1C1C3E] border border-[#2D2D54] rounded-2xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-sm text-slate-300">
            <thead class="bg-[#15152D] text-xs uppercase font-mono tracking-wider text-slate-400 border-b border-[#2D2D54]">
              <tr>
                <th class="px-5 py-3.5">Kode & Nama Marketer</th>
                <th class="px-5 py-3.5">No Kontak</th>
                <th class="px-5 py-3.5 text-center">Poin Loyalitas</th>
                <th class="px-5 py-3.5 text-right">Total Transaksi Penjualan</th>
                <th class="px-5 py-3.5 text-center">Tarif Fee</th>
                <th class="px-5 py-3.5 text-right">Total Hak Komisi</th>
                <th class="px-5 py-3.5 text-center">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-[#2D2D54]/50">
              <tr v-for="m in marketers" :key="m.id" class="hover:bg-[#24244D]/50 transition-colors">
                <td class="px-5 py-4">
                  <div class="font-bold text-white">{{ m.name }}</div>
                  <div class="text-xs font-mono text-amber-400">{{ m.code }}</div>
                </td>
                <td class="px-5 py-4 text-slate-300 font-mono text-xs">
                  {{ m.phone || '-' }}
                </td>
                <td class="px-5 py-4 text-center">
                  <span class="px-2 py-0.5 rounded-full bg-purple-500/20 text-purple-300 font-mono text-xs font-bold">
                    {{ m.points }} Pts
                  </span>
                </td>
                <td class="px-5 py-4 text-right font-mono font-bold text-slate-200">
                  {{ formatRupiah(m.total_purchased_amount) }}
                </td>
                <td class="px-5 py-4 text-center font-mono text-amber-400 font-semibold text-xs">
                  {{ m.commission_rate }}%
                </td>
                <td class="px-5 py-4 text-right font-mono font-bold text-emerald-400 text-sm">
                  {{ formatRupiah(m.commission_amount) }}
                </td>
                <td class="px-5 py-4 text-center">
                  <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                    <CheckCircle2 class="w-3.5 h-3.5" />
                    <span>Aktif</span>
                  </span>
                </td>
              </tr>
              <tr v-if="marketers.length === 0">
                <td colspan="7" class="px-5 py-10 text-center text-slate-400">
                  Belum ada data marketer.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
