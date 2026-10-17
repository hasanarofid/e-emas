<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { 
  BarChart3, 
  Coins, 
  Calendar, 
  Download, 
  TrendingUp, 
  PieChart, 
  Sparkles,
  ShoppingBag,
  CreditCard
} from 'lucide-vue-next';

const props = defineProps({
  stats: {
    type: Object,
    required: true,
  },
  byCarat: {
    type: Array,
    required: true,
  },
  byPayment: {
    type: Array,
    required: true,
  },
  monthlyTrends: {
    type: Array,
    required: true,
  },
});

const formatRupiah = (number) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0,
  }).format(number || 0);
};
</script>

<template>
  <AdminLayout>
    <Head title="Laporan & Analitik Penjualan - POS Emas" />

    <div class="space-y-6">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2 text-xs font-mono text-amber-500 uppercase tracking-widest">
            <BarChart3 class="w-4 h-4" />
            <span>Laporan & Analitik Toko Emas</span>
          </div>
          <h2 class="text-2xl font-bold tracking-tight text-white mt-1">
            Laporan Kinerja Penjualan Emas
          </h2>
          <p class="text-sm text-slate-400">
            Analisis omset transaksi, distribusi per kadar emas, saluran pembayaran kasir, dan tren bulanan.
          </p>
        </div>

        <button
          onclick="window.print()"
          class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-[#25254B] hover:bg-[#323265] text-slate-200 font-semibold text-sm transition-colors cursor-pointer"
        >
          <Download class="w-4 h-4" />
          <span>Cetak / Ekspor Laporan</span>
        </button>
      </div>

      <!-- Quick Summary Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-4 rounded-2xl bg-[#1E1E38] border border-slate-800 flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400 shrink-0">
            <Coins class="w-6 h-6" />
          </div>
          <div>
            <p class="text-xs font-medium text-slate-400">Total Omset Keseluruhan</p>
            <p class="text-lg font-bold text-amber-400 mt-0.5">{{ formatRupiah(stats.total_turnover) }}</p>
          </div>
        </div>

        <div class="p-4 rounded-2xl bg-[#1E1E38] border border-slate-800 flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 shrink-0">
            <Sparkles class="w-6 h-6" />
          </div>
          <div>
            <p class="text-xs font-medium text-slate-400">Total Gramasi Terjual</p>
            <p class="text-xl font-bold text-emerald-400 mt-0.5">{{ Number(stats.total_weight || 0).toFixed(2) }} Gr</p>
          </div>
        </div>

        <div class="p-4 rounded-2xl bg-[#1E1E38] border border-slate-800 flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400 shrink-0">
            <ShoppingBag class="w-6 h-6" />
          </div>
          <div>
            <p class="text-xs font-medium text-slate-400">Total Transaksi Kasir</p>
            <p class="text-xl font-bold text-white mt-0.5">{{ stats.total_transactions }} Nota</p>
          </div>
        </div>

        <div class="p-4 rounded-2xl bg-[#1E1E38] border border-slate-800 flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-purple-500/10 border border-purple-500/20 flex items-center justify-center text-purple-400 shrink-0">
            <TrendingUp class="w-6 h-6" />
          </div>
          <div>
            <p class="text-xs font-medium text-slate-400">Rata-rata Nilai / Faktur</p>
            <p class="text-base font-bold text-white mt-0.5">{{ formatRupiah(stats.avg_basket_size) }}</p>
          </div>
        </div>
      </div>

      <!-- 2 Columns: Breakdown Kadar & Metode Pembayaran -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Breakdown Kadar Emas -->
        <div class="p-5 rounded-2xl bg-[#1E1E38] border border-slate-800 shadow-xl">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-bold text-white flex items-center gap-2">
              <Sparkles class="w-4 h-4 text-amber-400" />
              <span>Penjualan Berdasarkan Kadar Emas</span>
            </h3>
            <span class="text-xs text-slate-400">Porsi Omset</span>
          </div>

          <div class="space-y-4">
            <div v-for="carat in byCarat" :key="carat.carat" class="p-3.5 rounded-xl bg-[#151528] border border-slate-800">
              <div class="flex items-center justify-between mb-1.5">
                <div>
                  <span class="font-bold text-white text-sm">{{ carat.carat }}</span>
                  <span class="text-xs text-slate-400 block font-mono">{{ carat.weight }} Gram Terjual</span>
                </div>
                <div class="text-right">
                  <span class="font-bold text-amber-400 font-mono text-sm">{{ formatRupiah(carat.sales) }}</span>
                  <span class="text-xs text-slate-400 block font-bold">{{ carat.percentage }}%</span>
                </div>
              </div>
              <div class="w-full h-2 bg-slate-700/80 rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-r from-amber-500 to-amber-400 rounded-full" :style="{ width: `${carat.percentage}%` }"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Breakdown Metode Bayar -->
        <div class="p-5 rounded-2xl bg-[#1E1E38] border border-slate-800 shadow-xl">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-bold text-white flex items-center gap-2">
              <CreditCard class="w-4 h-4 text-emerald-400" />
              <span>Saluran Pembayaran di Kasir</span>
            </h3>
            <span class="text-xs text-slate-400">Tunai vs Non-Tunai</span>
          </div>

          <div class="space-y-4">
            <div v-for="pay in byPayment" :key="pay.method" class="p-3.5 rounded-xl bg-[#151528] border border-slate-800">
              <div class="flex items-center justify-between mb-1.5">
                <div>
                  <span class="font-bold text-white text-sm">{{ pay.method }}</span>
                  <span class="text-xs text-slate-400 block font-mono">{{ pay.count }} Transaksi</span>
                </div>
                <div class="text-right">
                  <span class="font-bold text-emerald-400 font-mono text-sm">{{ formatRupiah(pay.amount) }}</span>
                  <span class="text-xs text-slate-400 block font-bold">{{ pay.percentage }}%</span>
                </div>
              </div>
              <div class="w-full h-2 bg-slate-700/80 rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-r from-emerald-500 to-teal-400 rounded-full" :style="{ width: `${pay.percentage}%` }"></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Tren Omset 6 Bulan Terakhir -->
      <div class="p-5 rounded-2xl bg-[#1E1E38] border border-slate-800 shadow-xl">
        <h3 class="text-base font-bold text-white mb-4 flex items-center gap-2">
          <TrendingUp class="w-4 h-4 text-blue-400" />
          <span>Tren Omset Penjualan 6 Bulan Terakhir</span>
        </h3>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
          <div 
            v-for="t in monthlyTrends" 
            :key="t.month"
            class="p-4 rounded-xl bg-[#151528] border border-slate-800 text-center"
          >
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">{{ t.month }} 2026</p>
            <p class="text-sm font-bold text-white mt-1.5 font-mono">{{ formatRupiah(t.turnover) }}</p>
            <p class="text-xs text-amber-400 font-mono mt-0.5">{{ t.weight }} Gr</p>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
