<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
  FileText, Calendar, TrendingUp, TrendingDown, 
  DollarSign, ArrowUpRight, ArrowDownLeft, Printer,
  Building2, Percent
} from 'lucide-vue-next';

const props = defineProps({
  year: Number,
  month: Number,
  data: Object,
});

const selectedYear = ref(props.year || new Date().getFullYear());
const selectedMonth = ref(props.month || '');

const months = [
  { value: 1, label: 'Januari' },
  { value: 2, label: 'Februari' },
  { value: 3, label: 'Maret' },
  { value: 4, label: 'April' },
  { value: 5, label: 'Mei' },
  { value: 6, label: 'Juni' },
  { value: 7, label: 'Juli' },
  { value: 8, label: 'Agustus' },
  { value: 9, label: 'September' },
  { value: 10, label: 'Oktober' },
  { value: 11, label: 'November' },
  { value: 12, label: 'Desember' },
];

const handleFilter = () => {
  router.get(route('admin.finance.profit-loss'), {
    year: selectedYear.value,
    month: selectedMonth.value || '',
  }, { preserveState: true, replace: true });
};

const formatRupiah = (val) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val || 0);
};

const triggerPrint = () => {
  window.print();
};
</script>

<template>
  <AdminLayout title="Rekapitulasi Laba Rugi Toko Emas">
    <Head title="Rekapitulasi Laba Rugi - POS Emas" />

    <div class="space-y-6">
      <!-- Header -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <h1 class="text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-2.5">
            <TrendingUp class="w-6 h-6 text-emerald-500" />
            <span>Laporan Rekapitulasi Laba Rugi Toko Emas</span>
          </h1>
          <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
            Analisis pendapatan kotor, ongkos bikin emas, biaya pembelian buyback, dan margin laba bersih toko.
          </p>
        </div>

        <div class="flex items-center gap-2.5 print:hidden">
          <button
            @click="triggerPrint"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 text-sm font-semibold transition-all shadow-sm cursor-pointer"
          >
            <Printer class="w-4 h-4 text-amber-500" />
            <span>Cetak Laporan</span>
          </button>
        </div>
      </div>

      <!-- Filter Periode -->
      <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-wrap gap-3 items-center justify-between print:hidden">
        <div class="flex items-center gap-3">
          <select
            v-model="selectedMonth"
            @change="handleFilter"
            class="px-3.5 py-2 text-sm rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-amber-500/30"
          >
            <option value="">Semua Bulan (Setahun Penuh)</option>
            <option v-for="m in months" :key="m.value" :value="m.value">{{ m.label }}</option>
          </select>

          <select
            v-model="selectedYear"
            @change="handleFilter"
            class="px-3.5 py-2 text-sm rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-amber-500/30"
          >
            <option value="2025">Tahun 2025</option>
            <option value="2026">Tahun 2026</option>
            <option value="2027">Tahun 2027</option>
          </select>
        </div>

        <span class="text-xs text-slate-400 font-medium">
          Menampilkan periode: {{ selectedMonth ? months.find(m => m.value === selectedMonth)?.label : 'Tahunan' }} {{ selectedYear }}
        </span>
      </div>

      <!-- KPI Cards Summary -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Pendapatan Kotor -->
        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
          <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Pendapatan (Omset)</span>
          <p class="text-xl font-bold font-mono text-emerald-600 dark:text-emerald-400 mt-2">{{ formatRupiah(data.total_revenue) }}</p>
          <p class="text-xs text-slate-400 mt-0.5">Penjualan emas & jasa servis</p>
        </div>

        <!-- HPP Emas -->
        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
          <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">HPP & Pengadaan Emas</span>
          <p class="text-xl font-bold font-mono text-slate-700 dark:text-slate-300 mt-2">{{ formatRupiah(data.total_cogs) }}</p>
          <p class="text-xs text-slate-400 mt-0.5">Faktur supplier + buyback</p>
        </div>

        <!-- Beban Operasional & Gaji -->
        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
          <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Beban Operasional</span>
          <p class="text-xl font-bold font-mono text-rose-600 dark:text-rose-400 mt-2">{{ formatRupiah(data.total_expenses) }}</p>
          <p class="text-xs text-slate-400 mt-0.5">Operasional toko + gaji pegawai</p>
        </div>

        <!-- Laba Bersih -->
        <div class="p-5 rounded-2xl bg-gradient-to-br from-amber-500/20 via-amber-500/10 to-transparent border border-amber-500/30 shadow-sm">
          <span class="text-xs font-bold text-amber-600 dark:text-amber-400 uppercase tracking-wider">Laba Bersih Berjalan</span>
          <p class="text-2xl font-bold font-mono text-amber-500 mt-2">{{ formatRupiah(data.net_profit) }}</p>
          <p class="text-xs text-slate-400 mt-0.5">Net Profit Toko Emas</p>
        </div>
      </div>

      <!-- Detailed Statement Table -->
      <div class="p-8 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-6">
        <div class="flex items-center justify-between pb-4 border-b border-slate-200 dark:border-slate-800">
          <div>
            <h2 class="text-lg font-bold text-slate-900 dark:text-white">Laporan Laba Rugi Komprehensif</h2>
            <p class="text-xs text-slate-400">Toko Mas Permata Abadi - Periode {{ selectedMonth ? months.find(m => m.value === selectedMonth)?.label : 'Tahunan' }} {{ selectedYear }}</p>
          </div>
          <span class="text-xs px-2.5 py-1 rounded-full bg-emerald-500/10 text-emerald-500 font-bold border border-emerald-500/20 font-mono">
            REKAP RESMI
          </span>
        </div>

        <div class="space-y-6 text-sm">
          <!-- 1. PENDAPATAN USAHA -->
          <div>
            <h3 class="font-bold text-slate-900 dark:text-white uppercase tracking-wider text-xs pb-2 border-b border-slate-100 dark:border-slate-800 text-amber-500">
              I. PENDAPATAN USAHA (REVENUE)
            </h3>
            <div class="divide-y divide-slate-100 dark:divide-slate-800/60 mt-2 text-xs">
              <div class="py-2.5 flex justify-between">
                <span class="text-slate-600 dark:text-slate-400">Penjualan Perhiasan Emas Kasir (POS)</span>
                <span class="font-mono font-semibold text-slate-900 dark:text-white">{{ formatRupiah(data.total_sales) }}</span>
              </div>
              <div class="py-2.5 flex justify-between">
                <span class="text-slate-600 dark:text-slate-400">Termasuk: Pendapatan Ongkos Bikin Emas</span>
                <span class="font-mono text-slate-500">({{ formatRupiah(data.total_crafting_fee) }})</span>
              </div>
              <div class="py-2.5 flex justify-between">
                <span class="text-slate-600 dark:text-slate-400">Pendapatan Servis & Reparasi Emas Pelanggan</span>
                <span class="font-mono font-semibold text-slate-900 dark:text-white">{{ formatRupiah(data.total_services) }}</span>
              </div>
              <div class="py-2.5 flex justify-between font-bold bg-slate-50 dark:bg-slate-800/50 px-2 rounded-lg">
                <span class="text-slate-900 dark:text-white uppercase">Total Pendapatan Usaha</span>
                <span class="font-mono text-emerald-600 dark:text-emerald-400">{{ formatRupiah(data.total_revenue) }}</span>
              </div>
            </div>
          </div>

          <!-- 2. BEBAN POKOK PENJUALAN (HPP) -->
          <div>
            <h3 class="font-bold text-slate-900 dark:text-white uppercase tracking-wider text-xs pb-2 border-b border-slate-100 dark:border-slate-800 text-amber-500">
              II. BEBAN POKOK PENJUALAN (COGS)
            </h3>
            <div class="divide-y divide-slate-100 dark:divide-slate-800/60 mt-2 text-xs">
              <div class="py-2.5 flex justify-between">
                <span class="text-slate-600 dark:text-slate-400">Pembelian Emas Baru dari Supplier Pabrik</span>
                <span class="font-mono font-semibold text-slate-900 dark:text-white">{{ formatRupiah(data.total_supplier_purchases) }}</span>
              </div>
              <div class="py-2.5 flex justify-between">
                <span class="text-slate-600 dark:text-slate-400">Pengeluaran Beli Kembali Emas Pelanggan (Buyback)</span>
                <span class="font-mono font-semibold text-slate-900 dark:text-white">{{ formatRupiah(data.total_buyback_cost) }}</span>
              </div>
              <div class="py-2.5 flex justify-between font-bold bg-slate-50 dark:bg-slate-800/50 px-2 rounded-lg">
                <span class="text-slate-900 dark:text-white uppercase">Total Beban Pokok Penjualan</span>
                <span class="font-mono text-slate-700 dark:text-slate-300">{{ formatRupiah(data.total_cogs) }}</span>
              </div>
            </div>
          </div>

          <!-- LABA KOTOR -->
          <div class="p-3.5 rounded-xl bg-slate-100 dark:bg-slate-800 flex justify-between items-center font-bold">
            <span class="text-slate-900 dark:text-white uppercase text-xs">LABA KOTOR (GROSS PROFIT)</span>
            <span class="font-mono text-emerald-600 dark:text-emerald-400 text-base">{{ formatRupiah(data.gross_profit) }}</span>
          </div>

          <!-- 3. BEBAN OPERASIONAL -->
          <div>
            <h3 class="font-bold text-slate-900 dark:text-white uppercase tracking-wider text-xs pb-2 border-b border-slate-100 dark:border-slate-800 text-amber-500">
              III. BEBAN OPERASIONAL & GAJI PEGAWAI
            </h3>
            <div class="divide-y divide-slate-100 dark:divide-slate-800/60 mt-2 text-xs">
              <div class="py-2.5 flex justify-between">
                <span class="text-slate-600 dark:text-slate-400">Beban Operasional Toko (Listrik, Kemasan Box, Snack)</span>
                <span class="font-mono font-semibold text-slate-900 dark:text-white">{{ formatRupiah(data.total_operational_expenses) }}</span>
              </div>
              <div class="py-2.5 flex justify-between">
                <span class="text-slate-600 dark:text-slate-400">Beban Gaji Pokok & Komisi Pegawai Toko</span>
                <span class="font-mono font-semibold text-slate-900 dark:text-white">{{ formatRupiah(data.total_payroll_expenses) }}</span>
              </div>
              <div class="py-2.5 flex justify-between font-bold bg-slate-50 dark:bg-slate-800/50 px-2 rounded-lg">
                <span class="text-slate-900 dark:text-white uppercase">Total Beban Operasional</span>
                <span class="font-mono text-rose-600 dark:text-rose-400">{{ formatRupiah(data.total_expenses) }}</span>
              </div>
            </div>
          </div>

          <!-- LABA BERSIH BERJALAN -->
          <div class="p-5 rounded-2xl bg-amber-500/10 border-2 border-amber-500/30 flex justify-between items-center font-black">
            <div>
              <span class="text-slate-900 dark:text-white uppercase text-sm block">LABA BERSIH OPERASIONAL (NET PROFIT)</span>
              <span class="text-[11px] text-slate-400 font-normal">Setelah memperhitungkan seluruh omset, pengadaan, beban dan gaji</span>
            </div>
            <span class="font-mono text-amber-500 text-2xl">{{ formatRupiah(data.net_profit) }}</span>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
