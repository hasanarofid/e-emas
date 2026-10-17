<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { 
  CalendarDays, 
  Coins, 
  TrendingUp, 
  Sparkles, 
  Receipt,
  Download,
  Calendar
} from 'lucide-vue-next';

const props = defineProps({
  year: {
    type: Number,
    required: true,
  },
  month: {
    type: Number,
    required: true,
  },
  calendarDays: {
    type: Array,
    required: true,
  },
  stats: {
    type: Object,
    required: true,
  },
});

const selectedYear = ref(props.year);
const selectedMonth = ref(props.month);

const months = [
  { val: 1, name: 'Januari' },
  { val: 2, name: 'Februari' },
  { val: 3, name: 'Maret' },
  { val: 4, name: 'April' },
  { val: 5, name: 'Mei' },
  { val: 6, name: 'Juni' },
  { val: 7, name: 'Juli' },
  { val: 8, name: 'Agustus' },
  { val: 9, name: 'September' },
  { val: 10, name: 'Oktober' },
  { val: 11, name: 'November' },
  { val: 12, name: 'Desember' },
];

const handleMonthChange = () => {
  router.get(route('admin.sales.monthly'), {
    year: selectedYear.value,
    month: selectedMonth.value,
  }, { preserveState: true });
};

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
    <Head title="Rekap Penjualan Bulanan - POS Emas" />

    <div class="space-y-6">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2 text-xs font-mono text-amber-500 uppercase tracking-widest">
            <CalendarDays class="w-4 h-4" />
            <span>Rekapitulasi Bulanan</span>
          </div>
          <h2 class="text-2xl font-bold tracking-tight text-white mt-1">
            Penjualan Emas Bulanan
          </h2>
          <p class="text-sm text-slate-400">
            Performa omset akumulasi harian selama periode bulan berjalan.
          </p>
        </div>

        <div class="flex items-center gap-2.5">
          <select
            v-model="selectedMonth"
            @change="handleMonthChange"
            class="px-3.5 py-2 rounded-xl bg-[#1E1E38] border border-slate-700 text-white text-sm focus:outline-none focus:border-amber-500"
          >
            <option v-for="m in months" :key="m.val" :value="m.val">{{ m.name }}</option>
          </select>
          <select
            v-model="selectedYear"
            @change="handleMonthChange"
            class="px-3.5 py-2 rounded-xl bg-[#1E1E38] border border-slate-700 text-white text-sm focus:outline-none focus:border-amber-500 font-mono"
          >
            <option :value="2025">2025</option>
            <option :value="2026">2026</option>
          </select>
        </div>
      </div>

      <!-- Quick Summary Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-4 rounded-2xl bg-[#1E1E38] border border-slate-800 flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400 shrink-0">
            <Coins class="w-6 h-6" />
          </div>
          <div>
            <p class="text-xs font-medium text-slate-400">Total Omset Bulan Ini</p>
            <p class="text-lg font-bold text-amber-400 mt-0.5">{{ formatRupiah(stats.total_turnover) }}</p>
          </div>
        </div>

        <div class="p-4 rounded-2xl bg-[#1E1E38] border border-slate-800 flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 shrink-0">
            <Sparkles class="w-6 h-6" />
          </div>
          <div>
            <p class="text-xs font-medium text-slate-400">Total Gramasi Emas</p>
            <p class="text-xl font-bold text-emerald-400 mt-0.5">{{ Number(stats.total_weight || 0).toFixed(2) }} Gr</p>
          </div>
        </div>

        <div class="p-4 rounded-2xl bg-[#1E1E38] border border-slate-800 flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-purple-500/10 border border-purple-500/20 flex items-center justify-center text-purple-400 shrink-0">
            <TrendingUp class="w-6 h-6" />
          </div>
          <div>
            <p class="text-xs font-medium text-slate-400">Pertumbuhan MoM</p>
            <p class="text-xl font-bold text-purple-400 mt-0.5">+{{ stats.growth_mom }}%</p>
          </div>
        </div>

        <div class="p-4 rounded-2xl bg-[#1E1E38] border border-slate-800 flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400 shrink-0">
            <Receipt class="w-6 h-6" />
          </div>
          <div>
            <p class="text-xs font-medium text-slate-400">Rata-rata / Hari</p>
            <p class="text-base font-bold text-white mt-0.5">{{ formatRupiah(stats.avg_daily_turnover) }}</p>
          </div>
        </div>
      </div>

      <!-- Tabel Kalender Harian -->
      <div class="rounded-2xl bg-[#1E1E38] border border-slate-800 overflow-hidden shadow-xl">
        <div class="p-4 border-b border-slate-800 flex items-center justify-between">
          <h3 class="text-base font-bold text-white">Rincian Penjualan Tanggal 1 s/d Akhir Bulan</h3>
          <span class="text-xs text-slate-400 font-mono">{{ calendarDays.length }} Hari Operasional</span>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left text-sm text-slate-300">
            <thead class="bg-[#151528] text-xs uppercase text-slate-400 font-semibold border-b border-slate-800">
              <tr>
                <th class="px-6 py-3.5">Tanggal</th>
                <th class="px-6 py-3.5">Hari</th>
                <th class="px-6 py-3.5 text-center">Faktur Terbit</th>
                <th class="px-6 py-3.5 text-center">Gramasi Terjual</th>
                <th class="px-6 py-3.5 text-right">Omset Harian</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/80">
              <tr
                v-for="d in calendarDays"
                :key="d.date"
                class="hover:bg-[#25254B]/40 transition-colors"
                :class="{ 'bg-amber-500/5': d.is_weekend }"
              >
                <td class="px-6 py-3 font-mono font-bold text-white text-xs">
                  {{ d.date }}
                </td>
                <td class="px-6 py-3 text-xs text-slate-300">
                  <span :class="{ 'text-amber-400 font-bold': d.is_weekend }">{{ d.day_name }}</span>
                </td>
                <td class="px-6 py-3 text-center text-xs font-mono text-slate-200">
                  {{ d.transactions }} Transaksi
                </td>
                <td class="px-6 py-3 text-center text-xs font-mono text-emerald-400">
                  {{ d.weight }} Gr
                </td>
                <td class="px-6 py-3 text-right font-mono font-bold text-amber-400 text-xs">
                  {{ formatRupiah(d.turnover) }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
