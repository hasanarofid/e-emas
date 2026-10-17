<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
  DollarSign, Calendar, Percent, Printer, 
  FileText, ShieldCheck, ArrowUpRight
} from 'lucide-vue-next';

const props = defineProps({
  sales: Object,
  year: Number,
  month: Number,
  stats: Object,
});

const selectedYear = ref(props.year || new Date().getFullYear());
const selectedMonth = ref(props.month || new Date().getMonth() + 1);

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
  router.get(route('admin.finance.tax-report'), {
    year: selectedYear.value,
    month: selectedMonth.value,
  }, { preserveState: true, replace: true });
};

const formatRupiah = (val) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val || 0);
};

const formatDate = (dateString) => {
  if (!dateString) return '-';
  const d = new Date(dateString);
  return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
};

const triggerPrint = () => {
  window.print();
};
</script>

<template>
  <AdminLayout title="Rekapitulasi Pajak Emas">
    <Head title="Rekapitulasi Pajak Emas - POS Emas" />

    <div class="space-y-6">
      <!-- Header -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <h1 class="text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-2.5">
            <Percent class="w-6 h-6 text-amber-500" />
            <span>Rekapitulasi Pajak Emas (PPN & PPh 22)</span>
          </h1>
          <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
            Simulasi dan rekonsiliasi perpajakan penyerahan emas perhiasan sesuai ketentuan PMK 48/PMK.03/2023.
          </p>
        </div>

        <div class="flex items-center gap-2.5 print:hidden">
          <button
            @click="triggerPrint"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 text-sm font-semibold transition-all shadow-sm cursor-pointer"
          >
            <Printer class="w-4 h-4 text-amber-500" />
            <span>Cetak Rekap Pajak</span>
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
          Masa Pajak: {{ months.find(m => m.value === selectedMonth)?.label }} {{ selectedYear }}
        </span>
      </div>

      <!-- KPI Tax Summary -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
          <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Dasar Pengenaan Pajak (DPP)</span>
          <p class="text-xl font-bold font-mono text-slate-900 dark:text-white mt-2">{{ formatRupiah(stats.total_omset) }}</p>
          <p class="text-xs text-slate-400 mt-0.5">Total omset penjualan emas</p>
        </div>

        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
          <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Estimasi PPN (1.1%)</span>
          <p class="text-xl font-bold font-mono text-blue-600 dark:text-blue-400 mt-2">{{ formatRupiah(stats.estimated_vat) }}</p>
          <p class="text-xs text-slate-400 mt-0.5">Tarif efektif perhiasan</p>
        </div>

        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
          <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">PPh Pasal 22 (0.25%)</span>
          <p class="text-xl font-bold font-mono text-emerald-600 dark:text-emerald-400 mt-2">{{ formatRupiah(stats.estimated_income_tax) }}</p>
          <p class="text-xs text-slate-400 mt-0.5">PPh badan pedagang emas</p>
        </div>

        <div class="p-5 rounded-2xl bg-gradient-to-br from-amber-500/20 via-amber-500/10 to-transparent border border-amber-500/30 shadow-sm">
          <span class="text-xs font-bold text-amber-600 dark:text-amber-400 uppercase tracking-wider">Total Beban Pajak</span>
          <p class="text-2xl font-bold font-mono text-amber-500 mt-2">{{ formatRupiah(stats.total_tax) }}</p>
          <p class="text-xs text-slate-400 mt-0.5">PPN + PPh Terutang</p>
        </div>
      </div>

      <!-- Transactions Tax Table -->
      <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse text-sm">
            <thead>
              <tr class="bg-slate-50/75 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800 text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                <th class="py-3.5 px-4 font-semibold">No. Nota POS</th>
                <th class="py-3.5 px-4 font-semibold">Tgl Transaksi</th>
                <th class="py-3.5 px-4 font-semibold">Pelanggan Pembeli</th>
                <th class="py-3.5 px-4 font-semibold text-right">Nilai Transaksi (DPP)</th>
                <th class="py-3.5 px-4 font-semibold text-right">PPN 1.1%</th>
                <th class="py-3.5 px-4 font-semibold text-right">PPh 22 (0.25%)</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
              <tr 
                v-for="s in sales.data" 
                :key="s.id"
                class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors"
              >
                <!-- No Nota -->
                <td class="py-3.5 px-4">
                  <span class="font-mono text-xs font-bold text-amber-500">{{ s.invoice_number }}</span>
                </td>

                <!-- Tgl -->
                <td class="py-3.5 px-4 text-xs text-slate-500">
                  {{ formatDate(s.created_at) }}
                </td>

                <!-- Pelanggan -->
                <td class="py-3.5 px-4">
                  <div class="font-bold text-slate-900 dark:text-white">{{ s.customer_name || 'Pelanggan Umum' }}</div>
                  <p class="text-xs text-slate-400">{{ s.customer_phone || '-' }}</p>
                </td>

                <!-- DPP -->
                <td class="py-3.5 px-4 text-right font-mono font-bold text-slate-900 dark:text-white">
                  {{ formatRupiah(s.grand_total) }}
                </td>

                <!-- PPN -->
                <td class="py-3.5 px-4 text-right font-mono text-blue-600 dark:text-blue-400 font-semibold">
                  {{ formatRupiah(round(s.grand_total * 0.011, 2)) }}
                </td>

                <!-- PPh 22 -->
                <td class="py-3.5 px-4 text-right font-mono text-emerald-600 dark:text-emerald-400 font-semibold">
                  {{ formatRupiah(round(s.grand_total * 0.0025, 2)) }}
                </td>
              </tr>

              <tr v-if="sales.data.length === 0">
                <td colspan="6" class="py-12 text-center text-slate-400">
                  <Percent class="w-12 h-12 mx-auto text-slate-300 dark:text-slate-700 mb-2" />
                  <p class="font-semibold">Belum ada transaksi kena pajak pada masa pajak ini</p>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="sales.links && sales.links.length > 3" class="p-4 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between">
          <p class="text-xs text-slate-500">
            Menampilkan {{ sales.from }} - {{ sales.to }} dari {{ sales.total }} data
          </p>
          <div class="flex gap-1">
            <template v-for="(link, i) in sales.links" :key="i">
              <Link
                v-if="link.url"
                :href="link.url"
                v-html="link.label"
                :class="[
                  link.active ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800',
                  'px-3 py-1.5 rounded-lg text-xs font-medium transition-colors'
                ]"
              />
              <span
                v-else
                v-html="link.label"
                class="px-3 py-1.5 rounded-lg text-xs font-medium text-slate-400 dark:text-slate-600 opacity-60"
              />
            </template>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
