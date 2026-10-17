<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { 
  Calendar, 
  Coins, 
  Clock, 
  ShoppingBag, 
  Layers, 
  Sparkles,
  ArrowRight,
  TrendingUp,
  Receipt
} from 'lucide-vue-next';

const props = defineProps({
  selectedDate: {
    type: String,
    required: true,
  },
  hourlySales: {
    type: Array,
    required: true,
  },
  byTray: {
    type: Array,
    required: true,
  },
  recentTransactions: {
    type: Array,
    default: () => [],
  },
  stats: {
    type: Object,
    required: true,
  },
});

const date = ref(props.selectedDate);

const handleDateChange = () => {
  router.get(route('admin.sales.daily'), { date: date.value }, { preserveState: true });
};

const formatRupiah = (number) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0,
  }).format(number || 0);
};

const formatDate = (dateString) => {
  if (!dateString) return '-';
  const d = new Date(dateString);
  return d.toLocaleDateString('id-ID', {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
    year: 'numeric',
  });
};
</script>

<template>
  <AdminLayout>
    <Head title="Rekap Penjualan Harian - POS Emas" />

    <div class="space-y-6">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2 text-xs font-mono text-amber-500 uppercase tracking-widest">
            <Calendar class="w-4 h-4" />
            <span>Laporan Harian Kasir</span>
          </div>
          <h2 class="text-2xl font-bold tracking-tight text-white mt-1">
            Rekap Penjualan Emas Harian
          </h2>
          <p class="text-sm text-slate-400">
            Pemantauan omset transaksi real-time per jam dan performa baki etalase untuk tanggal: <span class="text-white font-medium">{{ formatDate(selectedDate) }}</span>.
          </p>
        </div>

        <div class="flex items-center gap-2.5">
          <input
            v-model="date"
            @change="handleDateChange"
            type="date"
            class="px-3.5 py-2 rounded-xl bg-[#1E1E38] border border-slate-700 text-white text-sm focus:outline-none focus:border-amber-500 font-mono"
          />
        </div>
      </div>

      <!-- Quick Summary Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-4 rounded-2xl bg-[#1E1E38] border border-slate-800 flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400 shrink-0">
            <Coins class="w-6 h-6" />
          </div>
          <div>
            <p class="text-xs font-medium text-slate-400">Total Omset Hari Ini</p>
            <p class="text-xl font-bold text-amber-400 mt-0.5">{{ formatRupiah(stats.total_amount) }}</p>
          </div>
        </div>

        <div class="p-4 rounded-2xl bg-[#1E1E38] border border-slate-800 flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 shrink-0">
            <Sparkles class="w-6 h-6" />
          </div>
          <div>
            <p class="text-xs font-medium text-slate-400">Total Gramasi Terjual</p>
            <p class="text-xl font-bold text-emerald-400 mt-0.5">{{ Number(stats.total_weight || 0).toFixed(2) }} Gram</p>
          </div>
        </div>

        <div class="p-4 rounded-2xl bg-[#1E1E38] border border-slate-800 flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400 shrink-0">
            <Receipt class="w-6 h-6" />
          </div>
          <div>
            <p class="text-xs font-medium text-slate-400">Jumlah Transaksi Faktur</p>
            <p class="text-xl font-bold text-white mt-0.5">{{ stats.total_transactions }} Nota</p>
          </div>
        </div>
      </div>

      <!-- 2 Columns: Omset Per Jam & Omset Per Baki -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Distribusi Per Jam -->
        <div class="p-5 rounded-2xl bg-[#1E1E38] border border-slate-800 shadow-xl">
          <h3 class="text-base font-bold text-white mb-4 flex items-center gap-2">
            <Clock class="w-4 h-4 text-amber-400" />
            <span>Aktivitas Jam Transaksi Toko</span>
          </h3>

          <div class="space-y-3">
            <div 
              v-for="hour in hourlySales" 
              :key="hour.hour"
              class="p-3 rounded-xl bg-[#151528] border border-slate-800 flex items-center justify-between"
            >
              <div class="flex items-center gap-3">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>
                <div>
                  <p class="text-xs font-bold text-white font-mono">{{ hour.hour }}</p>
                  <p class="text-[11px] text-slate-400 font-mono">{{ hour.transactions }} Transaksi • {{ hour.weight }} Gr</p>
                </div>
              </div>
              <span class="font-bold font-mono text-xs text-amber-400">{{ formatRupiah(hour.amount) }}</span>
            </div>
          </div>
        </div>

        <!-- Performa Baki & Etalase -->
        <div class="p-5 rounded-2xl bg-[#1E1E38] border border-slate-800 shadow-xl">
          <h3 class="text-base font-bold text-white mb-4 flex items-center gap-2">
            <Layers class="w-4 h-4 text-emerald-400" />
            <span>Penjualan Per Baki Etalase</span>
          </h3>

          <div class="space-y-3">
            <div 
              v-for="tray in byTray" 
              :key="tray.tray_code"
              class="p-3 rounded-xl bg-[#151528] border border-slate-800 flex items-center justify-between"
            >
              <div>
                <p class="text-xs font-bold text-white">{{ tray.tray_name }}</p>
                <p class="text-[11px] text-slate-400 font-mono">{{ tray.tray_code }} • {{ tray.items_sold }} Pcs ({{ tray.weight }} Gr)</p>
              </div>
              <span class="font-bold font-mono text-xs text-emerald-400">{{ formatRupiah(tray.amount) }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
