<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { 
  BarChart3, 
  Calendar, 
  Coins, 
  Banknote, 
  Scale, 
  ArrowUpRight, 
  ArrowDownLeft, 
  TrendingUp, 
  PieChart, 
  Printer, 
  CreditCard,
  Gem,
  Layers
} from 'lucide-vue-next';

const props = defineProps({
  metrics: {
    type: Object,
    required: true
  },
  dailyTrends: {
    type: Array,
    default: () => []
  },
  paymentBreakdown: {
    type: Array,
    default: () => []
  },
  categoryBreakdown: {
    type: Array,
    default: () => []
  },
  karatBreakdown: {
    type: Array,
    default: () => []
  },
  period: {
    type: String,
    default: 'this_month'
  },
  startDate: {
    type: String,
    default: ''
  },
  endDate: {
    type: String,
    default: ''
  },
  storeSettings: {
    type: Object,
    default: () => ({})
  }
});

const selectedPeriod = ref(props.period);

const changePeriod = (p) => {
  selectedPeriod.value = p;
  router.get(route('admin.reports.index'), { period: p }, {
    preserveState: true,
    replace: true
  });
};

const formatRupiah = (val) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0
  }).format(val || 0);
};

const formatWeight = (val) => {
  return parseFloat(val || 0).toFixed(3) + ' gr';
};

// Calculate max value for trend bar height scaling
const maxDailyVal = Math.max(
  ...props.dailyTrends.map(d => Math.max(d.sales, d.buybacks)),
  1000000
);
</script>

<template>
  <Head title="Laporan & Statistik Penjualan Emas - e-Emas" />

  <AdminLayout>
    <div class="space-y-6">
      
      <!-- Top Title Bar & Period Filter -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200 dark:border-slate-800">
        <div>
          <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-700 dark:text-amber-300 text-xs font-semibold mb-1">
            <BarChart3 class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400" />
            <span>Analisis Finansial & Gramasi (Ref: SITOKMAS)</span>
          </div>
          <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">
            Laporan & Statistik Real-Time
          </h1>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
            Pantau arus kas, laba kotor, perputaran gramasi emas, dan performa kasir secara terperinci.
          </p>
        </div>

        <div class="flex items-center gap-2">
          <!-- Period Selector Pills -->
          <div class="flex items-center gap-1 p-1 bg-slate-100 dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 text-xs">
            <button 
              @click="changePeriod('today')"
              :class="[selectedPeriod === 'today' ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 font-medium', 'px-3 py-1.5 rounded-lg transition-colors cursor-pointer']"
            >
              Hari Ini
            </button>
            <button 
              @click="changePeriod('7_days')"
              :class="[selectedPeriod === '7_days' ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 font-medium', 'px-3 py-1.5 rounded-lg transition-colors cursor-pointer']"
            >
              7 Hari
            </button>
            <button 
              @click="changePeriod('this_month')"
              :class="[selectedPeriod === 'this_month' ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 font-medium', 'px-3 py-1.5 rounded-lg transition-colors cursor-pointer']"
            >
              Bulan Ini
            </button>
            <button 
              @click="changePeriod('this_year')"
              :class="[selectedPeriod === 'this_year' ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 font-medium', 'px-3 py-1.5 rounded-lg transition-colors cursor-pointer']"
            >
              Tahun Ini
            </button>
          </div>

          <button 
            onclick="window.print()"
            class="px-3.5 py-2 rounded-xl bg-slate-200 dark:bg-slate-800 hover:bg-slate-300 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold flex items-center gap-1.5 transition-colors cursor-pointer"
          >
            <Printer class="w-3.5 h-3.5" />
          </button>
        </div>
      </div>

      <!-- Core Financial Metrics Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Omzet Penjualan -->
        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
            <ArrowUpRight class="w-6 h-6" />
          </div>
          <div>
            <span class="text-xs text-slate-500 dark:text-slate-400 block">Omzet Penjualan</span>
            <span class="text-xl font-extrabold text-emerald-600 dark:text-emerald-400 font-mono">
              {{ formatRupiah(metrics.total_revenue) }}
            </span>
            <span class="text-[10px] text-slate-400 block font-mono">
              {{ metrics.total_sales_count }} transaksi • {{ formatWeight(metrics.total_sales_weight) }}
            </span>
          </div>
        </div>

        <!-- 2. Pengeluaran Buyback -->
        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
            <ArrowDownLeft class="w-6 h-6" />
          </div>
          <div>
            <span class="text-xs text-slate-500 dark:text-slate-400 block">Pengeluaran Buyback</span>
            <span class="text-xl font-extrabold text-rose-600 dark:text-rose-400 font-mono">
              {{ formatRupiah(metrics.total_buyback_expense) }}
            </span>
            <span class="text-[10px] text-slate-400 block font-mono">
              {{ metrics.total_buyback_count }} transaksi • {{ formatWeight(metrics.total_buyback_weight) }}
            </span>
          </div>
        </div>

        <!-- 3. Arus Kas Bersih (Net Cash Flow) -->
        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
            <Banknote class="w-6 h-6" />
          </div>
          <div>
            <span class="text-xs text-slate-500 dark:text-slate-400 block">Arus Kas Bersih (Net)</span>
            <span class="text-xl font-extrabold text-slate-900 dark:text-white font-mono">
              {{ formatRupiah(metrics.net_cash_flow) }}
            </span>
            <span class="text-[10px] text-slate-400 block">Omzet - Kas Buyback</span>
          </div>
        </div>

        <!-- 4. Neraca Gramasi Emas (Net Weight Flow) -->
        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
            <Scale class="w-6 h-6" />
          </div>
          <div>
            <span class="text-xs text-slate-500 dark:text-slate-400 block">Net Gramasi Keluar</span>
            <span class="text-xl font-extrabold text-blue-600 dark:text-blue-400 font-mono">
              {{ formatWeight(metrics.net_weight_flow) }}
            </span>
            <span class="text-[10px] text-slate-400 block">Selisih Fisik Terjual vs Beli</span>
          </div>
        </div>
      </div>

      <!-- Trend Chart: Daily Sales vs Buyback -->
      <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
        <div class="flex items-center justify-between">
          <h3 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider font-mono flex items-center gap-2">
            <TrendingUp class="w-4 h-4 text-amber-500" />
            Tren Penjualan & Pembelian Harian
          </h3>

          <div class="flex items-center gap-4 text-[11px]">
            <div class="flex items-center gap-1.5">
              <span class="w-3 h-3 rounded bg-emerald-500"></span>
              <span class="text-slate-600 dark:text-slate-400">Penjualan (Omzet)</span>
            </div>
            <div class="flex items-center gap-1.5">
              <span class="w-3 h-3 rounded bg-rose-500"></span>
              <span class="text-slate-600 dark:text-slate-400">Buyback (Kas Keluar)</span>
            </div>
          </div>
        </div>

        <!-- CSS Bar Chart Representation -->
        <div class="pt-6 h-48 flex items-end gap-2 overflow-x-auto border-b border-slate-200 dark:border-slate-800 pb-2">
          <div 
            v-for="(day, i) in dailyTrends" 
            :key="i"
            class="flex-1 min-w-[28px] flex flex-col items-center gap-1 group relative h-full justify-end"
          >
            <!-- Tooltip Hover -->
            <div class="absolute bottom-full mb-2 hidden group-hover:block z-20 p-2 rounded-xl bg-slate-900 text-white text-[10px] font-mono shadow-xl whitespace-nowrap">
              <p class="font-bold text-amber-400">{{ day.full_date }}</p>
              <p class="text-emerald-400">Jual: {{ formatRupiah(day.sales) }}</p>
              <p class="text-rose-400">Beli: {{ formatRupiah(day.buybacks) }}</p>
              <p class="text-slate-300">Berat: {{ formatWeight(day.weight) }}</p>
            </div>

            <!-- Double Bars -->
            <div class="w-full flex items-end justify-center gap-0.5 h-full">
              <!-- Sales Bar -->
              <div 
                :style="{ height: Math.max(4, (day.sales / maxDailyVal) * 100) + '%' }" 
                class="w-2.5 bg-emerald-500 rounded-t transition-all group-hover:brightness-110"
              ></div>
              <!-- Buyback Bar -->
              <div 
                :style="{ height: Math.max(4, (day.buybacks / maxDailyVal) * 100) + '%' }" 
                class="w-2.5 bg-rose-500 rounded-t transition-all group-hover:brightness-110"
              ></div>
            </div>

            <!-- Date Label -->
            <span class="text-[9px] text-slate-400 font-mono mt-1 block truncate max-w-full">
              {{ day.date }}
            </span>
          </div>
        </div>
      </div>

      <!-- Grid 3 Cards: Category Breakdown, Karat Breakdown, Payment Methods -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Breakdown 1: Kategori Perhiasan -->
        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-3">
          <h3 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider font-mono flex items-center gap-2">
            <Gem class="w-4 h-4 text-amber-500" />
            Penjualan per Kategori
          </h3>

          <div class="space-y-2.5 max-h-60 overflow-y-auto pr-1 text-xs">
            <div 
              v-for="cat in categoryBreakdown" 
              :key="cat.category_name"
              class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 flex items-center justify-between"
            >
              <div>
                <p class="font-bold text-slate-900 dark:text-white">{{ cat.category_name }}</p>
                <p class="text-[10px] text-slate-400 font-mono">{{ cat.count }} pcs • {{ formatWeight(cat.total_weight) }}</p>
              </div>
              <span class="font-bold text-amber-600 dark:text-amber-400 font-mono">
                {{ formatRupiah(cat.total) }}
              </span>
            </div>

            <div v-if="categoryBreakdown.length === 0" class="py-8 text-center text-slate-400 text-xs">
              Belum ada penjualan pada periode ini.
            </div>
          </div>
        </div>

        <!-- Breakdown 2: Kadar Emas (Karat) -->
        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-3">
          <h3 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider font-mono flex items-center gap-2">
            <Coins class="w-4 h-4 text-amber-500" />
            Penjualan per Kadar Emas
          </h3>

          <div class="space-y-2.5 max-h-60 overflow-y-auto pr-1 text-xs">
            <div 
              v-for="k in karatBreakdown" 
              :key="k.gold_rate_name"
              class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 flex items-center justify-between"
            >
              <div>
                <p class="font-bold text-slate-900 dark:text-white">{{ k.gold_rate_name }}</p>
                <p class="text-[10px] text-slate-400 font-mono">{{ k.count }} pcs • {{ formatWeight(k.total_weight) }}</p>
              </div>
              <span class="font-bold text-amber-600 dark:text-amber-400 font-mono">
                {{ formatRupiah(k.total) }}
              </span>
            </div>

            <div v-if="karatBreakdown.length === 0" class="py-8 text-center text-slate-400 text-xs">
              Belum ada penjualan pada periode ini.
            </div>
          </div>
        </div>

        <!-- Breakdown 3: Metode Pembayaran Kasir -->
        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-3">
          <h3 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider font-mono flex items-center gap-2">
            <CreditCard class="w-4 h-4 text-amber-500" />
            Metode Pembayaran Masuk
          </h3>

          <div class="space-y-2.5 max-h-60 overflow-y-auto pr-1 text-xs">
            <div 
              v-for="pay in paymentBreakdown" 
              :key="pay.payment_method"
              class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 flex items-center justify-between"
            >
              <div>
                <p class="font-bold text-slate-900 dark:text-white uppercase">{{ pay.payment_method }}</p>
                <p class="text-[10px] text-slate-400 font-mono">{{ pay.count }} transaksi</p>
              </div>
              <span class="font-bold text-emerald-600 dark:text-emerald-400 font-mono">
                {{ formatRupiah(pay.total) }}
              </span>
            </div>

            <div v-if="paymentBreakdown.length === 0" class="py-8 text-center text-slate-400 text-xs">
              Belum ada pembayaran pada periode ini.
            </div>
          </div>
        </div>

      </div>

    </div>
  </AdminLayout>
</template>
