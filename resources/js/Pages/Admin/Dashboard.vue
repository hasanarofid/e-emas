<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { 
  Scale, 
  Coins, 
  Layers, 
  ShoppingBag, 
  TrendingUp, 
  Gem, 
  Store, 
  Clock, 
  CheckCircle2, 
  ArrowUpRight,
  Sparkles,
  QrCode
} from 'lucide-vue-next';

const props = defineProps({
  stats: {
    type: Object,
    required: true
  },
  goldRates: {
    type: Array,
    default: () => []
  },
  trays: {
    type: Array,
    default: () => []
  },
  recentSales: {
    type: Array,
    default: () => []
  }
});

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
</script>

<template>
  <Head title="Dashboard Manajemen - e-Emas" />

  <AdminLayout>
    <div class="space-y-8">
      <!-- Title Page & Welcome Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200 dark:border-slate-800">
        <div>
          <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-700 dark:text-amber-300 text-xs font-semibold mb-1">
            <Sparkles class="w-3.5 h-3.5 text-amber-500 dark:text-amber-400" />
            <span>Toko Mas Permata Abadi</span>
          </div>
          <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">Ringkasan Operasional Toko Emas</h2>
          <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">Pantau pergerakan gramasi fisik, valuasi aset, etalase baki, dan transaksi kasir secara real-time.</p>
        </div>

        <div class="flex items-center gap-2">
          <Link 
            href="/" 
            target="_blank"
            class="px-4 py-2.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 hover:border-amber-500 text-slate-800 dark:text-slate-200 text-xs font-semibold shadow-sm transition-all flex items-center gap-1.5"
          >
            <Store class="w-4 h-4 text-amber-600 dark:text-amber-400" />
            <span>Katalog Toko</span>
          </Link>
        </div>
      </div>

      <!-- 4 Core Gold Metrics Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- 1. Total Bobot Emas Toko -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:border-amber-500/50 rounded-2xl p-5 relative overflow-hidden transition-all group shadow-sm">
          <div class="flex items-center justify-between">
            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Gramasi Emas</span>
            <div class="p-2.5 bg-amber-500/10 text-amber-700 dark:text-amber-400 rounded-xl border border-amber-500/20">
              <Scale class="w-5 h-5" />
            </div>
          </div>
          <div class="mt-3">
            <h3 class="text-2xl sm:text-3xl font-black text-amber-700 dark:text-amber-300 font-mono">
              {{ formatWeight(stats.total_gold_weight) }}
            </h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Stok fisik tersedia di etalase</p>
          </div>
        </div>

        <!-- 2. Valuasi Inventaris -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:border-amber-500/50 rounded-2xl p-5 relative overflow-hidden transition-all group shadow-sm">
          <div class="flex items-center justify-between">
            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Valuasi Stok Toko</span>
            <div class="p-2.5 bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 rounded-xl border border-emerald-500/20">
              <Coins class="w-5 h-5" />
            </div>
          </div>
          <div class="mt-3">
            <h3 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white">
              {{ formatRupiah(stats.total_gold_valuation) }}
            </h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Estimasi nilai {{ stats.available_items_count }} item perhiasan</p>
          </div>
        </div>

        <!-- 3. Baki Etalase Aktif -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:border-amber-500/50 rounded-2xl p-5 relative overflow-hidden transition-all group shadow-sm">
          <div class="flex items-center justify-between">
            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Baki Etalase</span>
            <div class="p-2.5 bg-sky-500/10 text-sky-700 dark:text-sky-400 rounded-xl border border-sky-500/20">
              <Layers class="w-5 h-5" />
            </div>
          </div>
          <div class="mt-3">
            <h3 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white">
              {{ stats.active_trays_count }} <span class="text-base font-normal text-slate-400">Baki</span>
            </h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Nampan pajang aktif di etalase</p>
          </div>
        </div>

        <!-- 4. Omzet Kasir Hari Ini -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:border-amber-500/50 rounded-2xl p-5 relative overflow-hidden transition-all group shadow-sm">
          <div class="flex items-center justify-between">
            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Omzet Kasir Hari Ini</span>
            <div class="p-2.5 bg-purple-500/10 text-purple-700 dark:text-purple-400 rounded-xl border border-purple-500/20">
              <ShoppingBag class="w-5 h-5" />
            </div>
          </div>
          <div class="mt-3">
            <h3 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white">
              {{ formatRupiah(stats.today_sales_revenue) }}
            </h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ stats.today_sales_count }} transaksi penjualan hari ini</p>
          </div>
        </div>
      </div>

      <!-- 2 Column Section: Live Gold Rates & Tray Status -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Live Gold Rates Table -->
        <div class="lg:col-span-7 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm transition-colors duration-300">
          <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
            <div>
              <span class="text-xs text-amber-700 dark:text-amber-400 font-semibold uppercase tracking-wider">Patokan Kasir</span>
              <h3 class="text-lg font-bold text-slate-900 dark:text-white">Kurs Harga Emas Hari Ini</h3>
            </div>
            <span class="text-xs text-slate-500 dark:text-slate-400 font-mono">{{ new Date().toLocaleDateString('id-ID', { dateStyle: 'medium' }) }}</span>
          </div>

          <div class="overflow-x-auto mt-4">
            <table class="w-full text-left text-xs">
              <thead class="text-slate-500 dark:text-slate-400 border-b border-slate-100 dark:border-slate-800 uppercase tracking-wider text-[10px]">
                <tr>
                  <th class="py-2.5 px-3">Kadar</th>
                  <th class="py-2.5 px-3">Karat / %</th>
                  <th class="py-2.5 px-3 text-right">Harga Buyback / gr</th>
                  <th class="py-2.5 px-3 text-right">Harga Jual / gr</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 text-slate-700 dark:text-slate-200">
                <tr v-for="rate in goldRates" :key="rate.id" class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                  <td class="py-3 px-3 font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <span class="w-6 h-6 rounded bg-amber-500/10 border border-amber-500/20 text-amber-700 dark:text-amber-400 flex items-center justify-center font-mono text-[10px]">
                      {{ rate.code }}
                    </span>
                    <span>{{ rate.name }}</span>
                  </td>
                  <td class="py-3 px-3 font-mono text-slate-500 dark:text-slate-300">{{ rate.percentage }}%</td>
                  <td class="py-3 px-3 text-right font-semibold text-amber-700 dark:text-amber-300">{{ formatRupiah(rate.buy_price_per_gram) }}</td>
                  <td class="py-3 px-3 text-right font-bold text-slate-900 dark:text-white">{{ formatRupiah(rate.sell_price_per_gram) }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Trays Mini Overview -->
        <div class="lg:col-span-5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm flex flex-col justify-between transition-colors duration-300">
          <div>
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
              <div>
                <span class="text-xs text-amber-700 dark:text-amber-400 font-semibold uppercase tracking-wider">Etalase Toko</span>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Status Baki & Nampan</h3>
              </div>
              <span class="text-xs font-mono text-slate-500 dark:text-slate-400">{{ trays.length }} Baki</span>
            </div>

            <div class="divide-y divide-slate-100 dark:divide-slate-800/80 mt-2 space-y-2">
              <div v-for="tray in trays.slice(0, 4)" :key="tray.id" class="pt-3 pb-1">
                <div class="flex items-center justify-between text-xs mb-1">
                  <div class="flex items-center gap-2">
                    <span class="font-mono text-[11px] font-bold text-amber-700 dark:text-amber-400 bg-amber-500/10 px-2 py-0.5 rounded border border-amber-500/20">
                      {{ tray.code }}
                    </span>
                    <span class="font-semibold text-slate-900 dark:text-white truncate max-w-[150px]">{{ tray.name }}</span>
                  </div>
                  <span class="font-mono text-slate-700 dark:text-slate-300 font-semibold">{{ formatWeight(tray.total_weight) }}</span>
                </div>
                <div class="flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400 mb-1">
                  <span>{{ tray.showcase_location || 'Etalase' }}</span>
                  <span>{{ tray.total_items }} / {{ tray.capacity }} slot ({{ Math.round((tray.total_items / (tray.capacity || 1)) * 100) }}%)</span>
                </div>
                <div class="w-full bg-slate-100 dark:bg-slate-950 rounded-full h-1.5 overflow-hidden">
                  <div 
                    class="bg-gradient-to-r from-amber-500 to-yellow-400 h-1.5 rounded-full" 
                    :style="{ width: Math.min(100, Math.round((tray.total_items / (tray.capacity || 1)) * 100)) + '%' }"
                  ></div>
                </div>
              </div>
            </div>
          </div>

          <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
            <span>Presisi gramasi fisik terpantau</span>
            <span class="text-emerald-600 dark:text-emerald-400 font-semibold flex items-center gap-1">
              <CheckCircle2 class="w-3.5 h-3.5" /> Baki Sinkron
            </span>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
