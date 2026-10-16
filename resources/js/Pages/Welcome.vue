<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { 
  Sparkles, 
  CircleDot, 
  Gem, 
  Layers, 
  ShieldCheck, 
  Scale, 
  QrCode, 
  ArrowUpRight, 
  Coins, 
  Store, 
  Phone, 
  MapPin, 
  Clock, 
  ChevronRight,
  TrendingUp,
  LogIn
} from 'lucide-vue-next';
import ThemeToggle from '@/Components/ThemeToggle.vue';

const props = defineProps({
  goldRates: {
    type: Array,
    default: () => []
  },
  categories: {
    type: Array,
    default: () => []
  },
  featuredItems: {
    type: Array,
    default: () => []
  },
  trays: {
    type: Array,
    default: () => []
  },
  storeSettings: {
    type: Object,
    default: () => ({})
  },
  auth: {
    type: Object,
    default: () => ({})
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
  <Head :title="storeSettings.store_name || 'POS Emas - Sistem Kasir & Manajemen Toko Emas'" />

  <div class="min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 font-sans selection:bg-amber-500 selection:text-slate-950 transition-colors duration-300">
    <!-- Live Gold Rates Ticker Bar (Auto-Sliding Marquee) -->
    <div class="bg-amber-500/10 dark:bg-slate-950 border-b border-amber-500/20 py-2 px-4 text-xs overflow-hidden relative z-50 flex items-center transition-colors duration-300">
      <!-- Fixed Left Label with Gradient Fade -->
      <div class="flex items-center gap-2 text-amber-700 dark:text-amber-400 font-bold uppercase tracking-wider shrink-0 z-20 bg-amber-50/90 dark:bg-slate-950/95 pr-4 py-0.5">
        <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
        <span class="text-[11px] sm:text-xs">Harga Emas Hari Ini:</span>
      </div>

      <!-- Marquee Track Wrapper -->
      <div class="relative w-full overflow-hidden flex items-center select-none marquee-mask">
        <!-- Inner Sliding Strip (Double list for seamless infinite loop) -->
        <div class="flex items-center gap-4 animate-marquee whitespace-nowrap shrink-0 hover:pause cursor-pointer">
          <div 
            v-for="(rate, idx) in [...goldRates, ...goldRates, ...goldRates]" 
            :key="`${rate.id}-${idx}`"
            class="flex items-center gap-2 bg-white dark:bg-slate-900 px-3.5 py-1 rounded-full border border-amber-500/20 shadow-sm text-xs shrink-0"
          >
            <span class="font-bold text-amber-700 dark:text-amber-300">{{ rate.code }}</span>
            <span class="text-slate-500 dark:text-slate-400">Jual:</span>
            <span class="font-semibold text-slate-900 dark:text-slate-100">{{ formatRupiah(rate.sell_price_per_gram) }}</span>
            <span class="text-slate-300 dark:text-slate-600">|</span>
            <span class="text-slate-500 dark:text-slate-400">Beli:</span>
            <span class="font-semibold text-amber-700 dark:text-amber-200">{{ formatRupiah(rate.buy_price_per_gram) }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Navigation Header -->
    <header class="sticky top-0 z-40 bg-white/80 dark:bg-slate-950/80 backdrop-blur-md border-b border-slate-200 dark:border-slate-800/80 transition-colors duration-300">
      <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
        <!-- Logo & Store Name -->
        <div class="flex items-center gap-3">
          <div class="w-11 h-11 rounded-xl bg-gradient-to-tr from-amber-600 via-amber-400 to-yellow-200 p-0.5 shadow-lg shadow-amber-500/20">
            <div class="w-full h-full bg-slate-950 rounded-[10px] flex items-center justify-center">
              <Gem class="w-5 h-5 text-amber-400" />
            </div>
          </div>
          <div>
            <h1 class="font-bold text-base sm:text-lg tracking-tight text-slate-900 dark:text-white flex items-center gap-2">
              {{ storeSettings.store_name }}
              <span class="text-[10px] bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-500/20 px-2 py-0.5 rounded-full font-mono uppercase tracking-wider">POS v1.0</span>
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-mono">{{ storeSettings.store_legal_name }}</p>
          </div>
        </div>

        <!-- Right Quick Actions: ThemeToggle & Login/Backoffice -->
        <div class="flex items-center gap-3">
          <ThemeToggle />

          <template v-if="auth.user">
            <Link 
              href="/admin/dashboard" 
              class="inline-flex items-center gap-2 px-4 py-2 sm:px-5 sm:py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 text-slate-950 font-bold text-xs sm:text-sm shadow-md shadow-amber-500/20 hover:brightness-110 transition-all"
            >
              <Store class="w-4 h-4" />
              <span>Buka Backoffice</span>
            </Link>
          </template>
          <template v-else>
            <Link 
              href="/login" 
              class="inline-flex items-center gap-2 px-4 py-2 sm:px-5 sm:py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 text-slate-950 font-bold text-xs sm:text-sm shadow-md shadow-amber-500/20 hover:brightness-110 transition-all"
            >
              <LogIn class="w-4 h-4" />
              <span>Masuk Kasir</span>
            </Link>
          </template>
        </div>
      </div>
    </header>

    <!-- Hero Section -->
    <section class="relative py-16 sm:py-20 px-6 border-b border-slate-200 dark:border-slate-800/80 overflow-hidden">
      <div class="max-w-7xl mx-auto relative z-10 grid lg:grid-cols-12 gap-12 items-center">
        <div class="lg:col-span-7 space-y-6">
          <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-amber-500/10 border border-amber-500/30 text-amber-800 dark:text-amber-300 text-xs font-semibold">
            <Sparkles class="w-3.5 h-3.5 text-amber-500" />
            <span>Sistem Operasional Kasir & Perhiasan Emas Presisi Tinggi</span>
          </div>

          <h2 class="text-4xl sm:text-5xl lg:text-6xl font-bold tracking-tight text-slate-900 dark:text-white leading-tight">
            Transparansi Kadar, <br />
            <span class="bg-gradient-to-r from-amber-600 via-amber-500 to-yellow-500 dark:from-amber-300 dark:via-amber-200 dark:to-yellow-400 bg-clip-text text-transparent">
              Presisi Gramasi 0.001 gr
            </span>
          </h2>

          <p class="text-slate-600 dark:text-slate-300 text-base sm:text-lg max-w-2xl leading-relaxed">
            Didukung fitur manajemen baki etalase, pelacakan barcode perhiasan, live update harga emas harian, kalkulasi buyback transparan, serta cetak surat jaminan emas resmi.
          </p>

          <div class="flex flex-wrap items-center gap-4 pt-2">
            <a 
              href="#gold-rates-table" 
              class="px-6 py-3.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-bold text-sm transition-all shadow-md shadow-amber-500/20 flex items-center gap-2"
            >
              <TrendingUp class="w-4 h-4" />
              <span>Pantau Kurs Emas Hari Ini</span>
            </a>
            <a 
              href="#trays-overview" 
              class="px-6 py-3.5 rounded-xl bg-white dark:bg-slate-900 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-800 dark:text-slate-200 border border-slate-300 dark:border-slate-700/80 font-medium text-sm transition-all flex items-center gap-2 shadow-sm"
            >
              <Layers class="w-4 h-4 text-amber-600 dark:text-amber-400" />
              <span>Lihat Status Baki Etalase</span>
            </a>
          </div>

          <!-- Quick Feature Badges -->
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-6 border-t border-slate-200 dark:border-slate-800/80 text-xs">
            <div class="flex items-center gap-2 text-slate-600 dark:text-slate-300">
              <Scale class="w-4 h-4 text-amber-600 dark:text-amber-400 shrink-0" />
              <span>Timbangan Bersih (Gross - Stone)</span>
            </div>
            <div class="flex items-center gap-2 text-slate-600 dark:text-slate-300">
              <QrCode class="w-4 h-4 text-amber-600 dark:text-amber-400 shrink-0" />
              <span>Scan Barcode Baki Cepat</span>
            </div>
            <div class="flex items-center gap-2 text-slate-600 dark:text-slate-300">
              <ShieldCheck class="w-4 h-4 text-amber-600 dark:text-amber-400 shrink-0" />
              <span>Surat Jaminan Emas Sah</span>
            </div>
          </div>
        </div>

        <!-- Hero Card / Live Rates Summary -->
        <div class="lg:col-span-5">
          <div class="bg-white dark:bg-gradient-to-b dark:from-slate-900 dark:to-slate-950 p-6 rounded-2xl border border-amber-500/30 shadow-xl dark:shadow-2xl relative overflow-hidden transition-colors duration-300">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
              <div>
                <p class="text-xs text-amber-700 dark:text-amber-400 font-semibold uppercase tracking-wider">Live Rates Toko</p>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Kurs Beli & Jual Hari Ini</h3>
              </div>
              <span class="text-xs text-slate-500 dark:text-slate-400 font-mono">{{ new Date().toLocaleDateString('id-ID', { dateStyle: 'medium' }) }}</span>
            </div>

            <div class="divide-y divide-slate-100 dark:divide-slate-800/80 mt-2">
              <div 
                v-for="rate in goldRates.slice(0, 5)" 
                :key="rate.id"
                class="py-3 flex items-center justify-between text-sm"
              >
                <div>
                  <p class="font-bold text-slate-900 dark:text-white flex items-center gap-1.5">
                    {{ rate.name }}
                    <span class="text-[10px] text-amber-700 dark:text-amber-400/90 font-mono font-normal">({{ rate.percentage }}%)</span>
                  </p>
                  <p class="text-[11px] text-slate-500 dark:text-slate-400">Buyback: <span class="text-amber-700 dark:text-amber-300 font-semibold">{{ formatRupiah(rate.buy_price_per_gram) }}/gr</span></p>
                </div>
                <div class="text-right">
                  <span class="text-base font-extrabold text-amber-600 dark:text-amber-400">{{ formatRupiah(rate.sell_price_per_gram) }}</span>
                  <p class="text-[10px] text-slate-400 dark:text-slate-500">per gram dasar</p>
                </div>
              </div>
            </div>

            <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
              <span>* Harga dasar belum termasuk ongkos bikin</span>
              <a href="#gold-rates-table" class="text-amber-600 dark:text-amber-400 font-semibold hover:underline flex items-center gap-1">
                Selengkapnya <ChevronRight class="w-3.5 h-3.5" />
              </a>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Section 1: Tabel Lengkap Kurs Emas Harian -->
    <section id="gold-rates-table" class="py-16 px-6 max-w-7xl mx-auto">
      <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
        <div>
          <span class="text-xs font-semibold text-amber-700 dark:text-amber-400 tracking-wider uppercase">Daftar Kadar & Kemurnian</span>
          <h3 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white mt-1">Harga Patokan Emas Harian</h3>
          <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Digunakan secara otomatis di kasir POS untuk perhitungan harga jual dan buyback.</p>
        </div>
      </div>

      <div class="overflow-x-auto rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/60 shadow-sm backdrop-blur transition-colors duration-300">
        <table class="w-full text-left text-sm">
          <thead class="bg-slate-50 dark:bg-slate-950/80 text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
            <tr>
              <th class="py-4 px-6">Kadar / Karat</th>
              <th class="py-4 px-6">Persentase Emas</th>
              <th class="py-4 px-6 text-right">Harga Beli (Buyback) / gr</th>
              <th class="py-4 px-6 text-right">Harga Jual Dasar / gr</th>
              <th class="py-4 px-6 text-center">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 text-slate-700 dark:text-slate-200">
            <tr v-for="rate in goldRates" :key="rate.id" class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
              <td class="py-4 px-6 font-semibold text-slate-900 dark:text-white">
                <div class="flex items-center gap-2">
                  <div class="w-7 h-7 rounded-lg bg-amber-500/10 border border-amber-500/20 text-amber-700 dark:text-amber-400 flex items-center justify-center font-bold text-xs">
                    {{ rate.code }}
                  </div>
                  <span>{{ rate.name }}</span>
                </div>
              </td>
              <td class="py-4 px-6 text-slate-600 dark:text-slate-300 font-mono">{{ rate.percentage }}%</td>
              <td class="py-4 px-6 text-right font-semibold text-amber-700 dark:text-amber-300">{{ formatRupiah(rate.buy_price_per_gram) }}</td>
              <td class="py-4 px-6 text-right font-bold text-slate-900 dark:text-white">{{ formatRupiah(rate.sell_price_per_gram) }}</td>
              <td class="py-4 px-6 text-center">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-500/20">
                  <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>

    <!-- Section 2: Ringkasan Baki & Etalase Toko -->
    <section id="trays-overview" class="py-16 px-6 bg-slate-100/60 dark:bg-slate-900/30 border-y border-slate-200 dark:border-slate-800/80 transition-colors duration-300">
      <div class="max-w-7xl mx-auto">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
          <div>
            <span class="text-xs font-semibold text-amber-700 dark:text-amber-400 tracking-wider uppercase">Manajemen Etalase</span>
            <h3 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white mt-1">Status Baki & Nampan Emas</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Pantau kapasitas terisi dan total gramasi fisik di setiap baki etalase.</p>
          </div>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <div 
            v-for="tray in trays" 
            :key="tray.id"
            class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:border-amber-500/50 rounded-2xl p-5 shadow-sm transition-all group"
          >
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
              <span class="font-mono text-xs font-bold text-amber-700 dark:text-amber-400 bg-amber-500/10 px-2.5 py-1 rounded-md border border-amber-500/20">
                {{ tray.code }}
              </span>
              <span class="text-xs text-slate-500 dark:text-slate-400">{{ tray.showcase_location || 'Etalase' }}</span>
            </div>

            <h4 class="font-bold text-slate-900 dark:text-white text-base mt-3 group-hover:text-amber-600 dark:group-hover:text-amber-300 transition-colors">
              {{ tray.name }}
            </h4>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
              Kategori: <span class="text-slate-800 dark:text-slate-200 font-medium">{{ tray.category ? tray.category.name : 'Campuran' }}</span>
            </p>

            <div class="grid grid-cols-2 gap-3 mt-4 pt-3 border-t border-slate-100 dark:border-slate-800/80 text-xs">
              <div class="bg-slate-50 dark:bg-slate-950/60 p-2.5 rounded-xl border border-slate-200 dark:border-slate-800">
                <span class="text-slate-500 dark:text-slate-400 text-[11px] block">Isi Perhiasan:</span>
                <span class="font-bold text-slate-900 dark:text-white text-sm">{{ tray.total_items }} / {{ tray.capacity }} item</span>
              </div>
              <div class="bg-slate-50 dark:bg-slate-950/60 p-2.5 rounded-xl border border-slate-200 dark:border-slate-800">
                <span class="text-slate-500 dark:text-slate-400 text-[11px] block">Total Bobot:</span>
                <span class="font-bold text-amber-700 dark:text-amber-400 text-sm">{{ formatWeight(tray.total_weight) }}</span>
              </div>
            </div>

            <!-- Capacity Progress Bar -->
            <div class="mt-4">
              <div class="flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400 mb-1">
                <span>Kapasitas Slot</span>
                <span>{{ Math.round((tray.total_items / (tray.capacity || 1)) * 100) }}%</span>
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
      </div>
    </section>

    <!-- Section 3: Sampel Perhiasan Siap Jual -->
    <section class="py-16 px-6 max-w-7xl mx-auto">
      <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
        <div>
          <span class="text-xs font-semibold text-amber-700 dark:text-amber-400 tracking-wider uppercase">Inventaris Siap Jual</span>
          <h3 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white mt-1">Koleksi Perhiasan di Etalase</h3>
          <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Setiap barang memiliki kode SKU/Barcode unik untuk scan transaksi kasir.</p>
        </div>
      </div>

      <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div 
          v-for="item in featuredItems" 
          :key="item.id"
          class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:border-amber-500/50 rounded-2xl p-5 shadow-sm transition-all flex flex-col justify-between"
        >
          <div>
            <div class="flex items-center justify-between text-xs mb-3">
              <span class="font-mono text-amber-700 dark:text-amber-400 bg-amber-500/10 px-2 py-0.5 rounded border border-amber-500/20">{{ item.sku }}</span>
              <span class="text-slate-500 dark:text-slate-400 font-semibold">{{ item.gold_rate ? item.gold_rate.code : '' }}</span>
            </div>

            <h4 class="font-bold text-slate-900 dark:text-white text-sm line-clamp-2 leading-snug">
              {{ item.name }}
            </h4>

            <div class="mt-3 space-y-1 text-xs text-slate-600 dark:text-slate-300">
              <div class="flex justify-between">
                <span class="text-slate-400">Berat Kotor:</span>
                <span class="font-mono font-medium">{{ formatWeight(item.gross_weight) }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-slate-400">Berat Bersih:</span>
                <span class="font-mono font-bold text-amber-700 dark:text-amber-300">{{ formatWeight(item.net_weight) }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-slate-400">Baki:</span>
                <span class="text-slate-700 dark:text-slate-300">{{ item.tray ? item.tray.code : '-' }}</span>
              </div>
            </div>
          </div>

          <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between">
            <div>
              <span class="text-[10px] text-slate-400 block">Estimasi Harga:</span>
              <span class="text-base font-extrabold text-amber-700 dark:text-amber-400">{{ formatRupiah(item.total_price) }}</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white dark:bg-slate-950 border-t border-slate-200 dark:border-slate-800/80 py-12 px-6 text-slate-500 dark:text-slate-400 text-xs transition-colors duration-300">
      <div class="max-w-7xl mx-auto grid md:grid-cols-3 gap-8">
        <div>
          <h5 class="text-slate-900 dark:text-white font-bold text-sm mb-2">{{ storeSettings.store_name }}</h5>
          <p class="leading-relaxed">{{ storeSettings.store_legal_name }}</p>
          <p class="text-amber-700 dark:text-amber-400/80 font-mono mt-1">{{ storeSettings.store_permit_number }}</p>
        </div>
        <div>
          <h5 class="text-slate-900 dark:text-white font-bold text-sm mb-2">Lokasi & Kontak Toko</h5>
          <p class="flex items-start gap-2">
            <MapPin class="w-4 h-4 text-amber-600 dark:text-amber-400 shrink-0 mt-0.5" />
            <span>{{ storeSettings.store_address }}</span>
          </p>
          <p class="flex items-center gap-2 mt-2">
            <Phone class="w-4 h-4 text-amber-600 dark:text-amber-400 shrink-0" />
            <span>{{ storeSettings.store_phone }}</span>
          </p>
        </div>
        <div>
          <h5 class="text-slate-900 dark:text-white font-bold text-sm mb-2">Sistem Operasional POS Emas</h5>
          <p class="leading-relaxed">
            Platform modern kasir (POS), inventaris per baki, live gold rates, dan cetak surat emas jaminan.
          </p>
          <div class="mt-3">
            <Link href="/login" class="text-amber-700 dark:text-amber-400 hover:underline flex items-center gap-1 font-semibold">
              Buka Login Kasir / Admin <ArrowUpRight class="w-3.5 h-3.5" />
            </Link>
          </div>
        </div>
      </div>

      <div class="max-w-7xl mx-auto mt-8 pt-6 border-t border-slate-100 dark:border-slate-900 text-center text-slate-400 dark:text-slate-500">
        &copy; 2026 {{ storeSettings.store_name }}. Seluruh hak cipta dilindungi undang-undang.
      </div>
    </footer>
  </div>
</template>

<style scoped>
@keyframes marquee-scroll {
  0% {
    transform: translateX(0%);
  }
  100% {
    transform: translateX(-33.333333%);
  }
}

.animate-marquee {
  animation: marquee-scroll 28s linear infinite;
  will-change: transform;
}

.hover\:pause:hover {
  animation-play-state: paused;
}

.marquee-mask {
  mask-image: linear-gradient(to right, transparent 0%, black 20px, black calc(100% - 30px), transparent 100%);
  -webkit-mask-image: linear-gradient(to right, transparent 0%, black 20px, black calc(100% - 30px), transparent 100%);
}
</style>
