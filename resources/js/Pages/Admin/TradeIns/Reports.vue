<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
  FileText, Repeat, Coins, Scale, Printer, 
  ArrowDownRight, ArrowUpRight, CheckCircle2, PieChart
} from 'lucide-vue-next';

const props = defineProps({
  stats: Object,
  byPayment: Array,
});

const formatRupiah = (val) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val || 0);
};

const formatWeight = (val) => {
  return Number(val || 0).toFixed(3) + ' gr';
};
</script>

<template>
  <AdminLayout>
    <Head title="Laporan Tukar Tambah Emas" />

    <div class="space-y-6">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2 text-xs font-mono text-amber-500 uppercase tracking-widest">
            <FileText class="w-4 h-4" />
            <span>Laporan Analitik Transaksi</span>
          </div>
          <h2 class="text-2xl font-bold tracking-tight text-white mt-1">
            Laporan Tukar Tambah Emas
          </h2>
          <p class="text-sm text-slate-400">
            Laporan akumulasi nilai selisih transaksi kasir dan neraca gramasi emas perhiasan.
          </p>
        </div>

        <div class="flex items-center gap-3">
          <button 
            @click="window.print()"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-bold text-sm shadow-lg shadow-amber-500/20 cursor-pointer transition-all"
          >
            <Printer class="w-4 h-4" />
            <span>Cetak Dokumen Laporan</span>
          </button>
        </div>
      </div>

      <!-- KPI Stats -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-[#1C1C3E] border border-[#2D2D54] rounded-2xl p-5">
          <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Total Transaksi</p>
          <h3 class="text-3xl font-bold text-white mt-2">{{ stats.total_transactions }}</h3>
          <p class="text-xs text-slate-400 mt-1">Seluruh nota tukar tambah</p>
        </div>

        <div class="bg-[#1C1C3E] border border-[#2D2D54] rounded-2xl p-5">
          <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Total Emas Lama Diterima</p>
          <h3 class="text-3xl font-bold text-emerald-400 mt-2">{{ formatWeight(stats.total_old_weight) }}</h3>
          <p class="text-xs text-slate-400 mt-1">Stok emas lebur/re-kondisi</p>
        </div>

        <div class="bg-[#1C1C3E] border border-[#2D2D54] rounded-2xl p-5">
          <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Total Emas Baru Terjual</p>
          <h3 class="text-3xl font-bold text-amber-400 mt-2">{{ formatWeight(stats.total_new_weight) }}</h3>
          <p class="text-xs text-slate-400 mt-1">Stok etalase berkurang</p>
        </div>

        <div class="bg-[#1C1C3E] border border-[#2D2D54] rounded-2xl p-5">
          <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Total Kas Masuk Bersih</p>
          <h3 class="text-2xl font-bold text-cyan-400 mt-2">{{ formatRupiah(stats.total_difference) }}</h3>
          <p class="text-xs text-slate-400 mt-1">Akumulasi selisih kasir</p>
        </div>
      </div>

      <!-- Distribusi Metode Pembayaran & Neraca Gramasi -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Breakdown Metode Pembayaran -->
        <div class="bg-[#1C1C3E] border border-[#2D2D54] rounded-2xl p-6">
          <h3 class="text-base font-semibold text-white flex items-center gap-2 mb-4">
            <Coins class="w-4 h-4 text-amber-400" />
            <span>Realisasi Kas Berdasarkan Kanal Pembayaran</span>
          </h3>

          <div class="space-y-4">
            <div 
              v-for="p in byPayment" 
              :key="p.method"
              class="flex items-center justify-between p-4 bg-[#15152D] border border-[#2D2D54] rounded-xl"
            >
              <div>
                <p class="font-medium text-slate-200">{{ p.method }}</p>
                <p class="text-xs text-slate-400">{{ p.count }} kali transaksi</p>
              </div>
              <div class="text-right">
                <p class="text-base font-bold text-white font-mono">{{ formatRupiah(p.amount) }}</p>
                <p class="text-xs text-emerald-400">Tercatat</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Neraca Arus Fisik Emas -->
        <div class="bg-[#1C1C3E] border border-[#2D2D54] rounded-2xl p-6 flex flex-col justify-between">
          <div>
            <h3 class="text-base font-semibold text-white flex items-center gap-2 mb-4">
              <Scale class="w-4 h-4 text-emerald-400" />
              <span>Neraca Arus Fisik Gramasi Emas</span>
            </h3>

            <p class="text-sm text-slate-400 mb-6">
              Perbandingan antara gramasi emas lama yang masuk ke brankas toko dengan gramasi perhiasan baru yang keluar ke pelanggan.
            </p>

            <div class="space-y-4">
              <div class="p-4 bg-[#15152D] rounded-xl border border-[#2D2D54] flex items-center justify-between">
                <span class="text-sm text-slate-300">Arus Keluar (Emas Baru Terjual)</span>
                <span class="text-base font-bold text-amber-400 font-mono">{{ formatWeight(stats.total_new_weight) }}</span>
              </div>
              <div class="p-4 bg-[#15152D] rounded-xl border border-[#2D2D54] flex items-center justify-between">
                <span class="text-sm text-slate-300">Arus Masuk (Emas Lama Pelanggan)</span>
                <span class="text-base font-bold text-emerald-400 font-mono">{{ formatWeight(stats.total_old_weight) }}</span>
              </div>
              <div class="p-4 bg-[#1E1E3F] rounded-xl border border-indigo-500/30 flex items-center justify-between">
                <span class="text-sm font-semibold text-white">Selisih Bersih (Net Gram Keluar)</span>
                <span class="text-lg font-black text-cyan-400 font-mono">{{ formatWeight(stats.gold_net_flow) }}</span>
              </div>
            </div>
          </div>

          <div class="mt-6 pt-4 border-t border-[#2D2D54] flex items-center justify-between">
            <span class="text-xs text-slate-400">Status Validasi Sistem:</span>
            <span class="inline-flex items-center gap-1.5 text-xs text-emerald-400 font-medium">
              <CheckCircle2 class="w-3.5 h-3.5" />
              <span>Sinkronisasi Data Otomatis</span>
            </span>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
