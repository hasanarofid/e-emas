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

const calcVat = (val) => Math.round((parseFloat(val) || 0) * 0.011);
const calcIncomeTax = (val) => Math.round((parseFloat(val) || 0) * 0.0025);

const triggerPrint = () => {
  window.print();
};
</script>

<template>
  <AdminLayout>
    <Head title="Rekapitulasi Pajak Emas - POS Emas" />

    <div class="space-y-6">
      <!-- Header -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2 text-xs font-mono text-amber-500 uppercase tracking-widest">
            <Percent class="w-4 h-4" />
            <span>Pajak Penjualan Emas Perhiasan</span>
          </div>
          <h2 class="text-2xl font-bold tracking-tight text-white mt-1">
            Rekapitulasi Pajak Emas (PPN & PPh 22)
          </h2>
          <p class="text-sm text-slate-400 mt-1">
            Simulasi dan rekonsiliasi perpajakan penyerahan emas perhiasan sesuai ketentuan PMK 48/PMK.03/2023.
          </p>
        </div>

        <div class="flex items-center gap-2.5 print:hidden">
          <button
            @click="triggerPrint"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 text-sm font-bold shadow-lg shadow-amber-500/20 transition-all cursor-pointer"
          >
            <Printer class="w-4 h-4" />
            <span>Cetak Rekap Pajak</span>
          </button>
        </div>
      </div>

      <!-- Filter Periode -->
      <div class="p-4 rounded-2xl bg-[#1C1C3E] border border-[#2D2D54] shadow-sm flex flex-wrap gap-3 items-center justify-between print:hidden">
        <div class="flex items-center gap-3">
          <select
            v-model="selectedMonth"
            @change="handleFilter"
            class="px-3.5 py-2 text-sm rounded-xl bg-[#15152D] border border-[#2D2D54] text-white font-medium focus:border-amber-400 focus:outline-none"
          >
            <option v-for="m in months" :key="m.value" :value="m.value" class="bg-[#1C1C3E]">{{ m.label }}</option>
          </select>

          <select
            v-model="selectedYear"
            @change="handleFilter"
            class="px-3.5 py-2 text-sm rounded-xl bg-[#15152D] border border-[#2D2D54] text-white font-medium focus:border-amber-400 focus:outline-none"
          >
            <option value="2025" class="bg-[#1C1C3E]">Tahun 2025</option>
            <option value="2026" class="bg-[#1C1C3E]">Tahun 2026</option>
            <option value="2027" class="bg-[#1C1C3E]">Tahun 2027</option>
          </select>
        </div>

        <span class="text-xs text-slate-400 font-medium">
          Masa Pajak: {{ months.find(m => m.value === selectedMonth)?.label }} {{ selectedYear }}
        </span>
      </div>

      <!-- KPI Tax Summary -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-5 rounded-2xl bg-[#1C1C3E] border border-[#2D2D54] shadow-sm">
          <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Dasar Pengenaan Pajak (DPP)</span>
          <p class="text-xl font-bold font-mono text-white mt-2">{{ formatRupiah(stats?.total_omset) }}</p>
          <p class="text-xs text-slate-400 mt-0.5">Total omset penjualan emas</p>
        </div>

        <div class="p-5 rounded-2xl bg-[#1C1C3E] border border-[#2D2D54] shadow-sm">
          <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Estimasi PPN (1.1%)</span>
          <p class="text-xl font-bold font-mono text-blue-400 mt-2">{{ formatRupiah(stats?.estimated_vat) }}</p>
          <p class="text-xs text-slate-400 mt-0.5">Tarif efektif perhiasan</p>
        </div>

        <div class="p-5 rounded-2xl bg-[#1C1C3E] border border-[#2D2D54] shadow-sm">
          <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">PPh Pasal 22 (0.25%)</span>
          <p class="text-xl font-bold font-mono text-emerald-400 mt-2">{{ formatRupiah(stats?.estimated_income_tax) }}</p>
          <p class="text-xs text-slate-400 mt-0.5">PPh badan pedagang emas</p>
        </div>

        <div class="p-5 rounded-2xl bg-[#1C1C3E] border border-amber-500/30 shadow-sm">
          <span class="text-xs font-bold text-amber-400 uppercase tracking-wider">Total Beban Pajak</span>
          <p class="text-2xl font-bold font-mono text-amber-400 mt-2">{{ formatRupiah(stats?.total_tax) }}</p>
          <p class="text-xs text-slate-400 mt-0.5">PPN + PPh Terutang</p>
        </div>
      </div>

      <!-- Transactions Tax Table -->
      <div class="rounded-2xl bg-[#1C1C3E] border border-[#2D2D54] shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse text-sm text-slate-300">
            <thead>
              <tr class="bg-[#15152D] border-b border-[#2D2D54] text-xs text-slate-400 uppercase tracking-wider font-mono">
                <th class="py-3.5 px-4 font-semibold">No. Nota POS</th>
                <th class="py-3.5 px-4 font-semibold">Tgl Transaksi</th>
                <th class="py-3.5 px-4 font-semibold">Pelanggan Pembeli</th>
                <th class="py-3.5 px-4 font-semibold text-right">Nilai Transaksi (DPP)</th>
                <th class="py-3.5 px-4 font-semibold text-right">PPN 1.1%</th>
                <th class="py-3.5 px-4 font-semibold text-right">PPh 22 (0.25%)</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-[#2D2D54]/50">
              <tr 
                v-for="s in (sales?.data || [])" 
                :key="s.id"
                class="hover:bg-[#24244D]/50 transition-colors"
              >
                <!-- No Nota -->
                <td class="py-3.5 px-4">
                  <span class="font-mono text-xs font-bold text-amber-400">{{ s.invoice_number }}</span>
                </td>

                <!-- Tgl -->
                <td class="py-3.5 px-4 text-xs text-slate-400">
                  {{ formatDate(s.created_at) }}
                </td>

                <!-- Pelanggan -->
                <td class="py-3.5 px-4">
                  <div class="font-bold text-white">{{ s.customer_name || 'Pelanggan Umum' }}</div>
                  <p class="text-xs text-slate-400">{{ s.customer_phone || '-' }}</p>
                </td>

                <!-- DPP -->
                <td class="py-3.5 px-4 text-right font-mono font-bold text-white">
                  {{ formatRupiah(s.grand_total) }}
                </td>

                <!-- PPN -->
                <td class="py-3.5 px-4 text-right font-mono text-blue-400 font-semibold">
                  {{ formatRupiah(calcVat(s.grand_total)) }}
                </td>

                <!-- PPh 22 -->
                <td class="py-3.5 px-4 text-right font-mono text-emerald-400 font-semibold">
                  {{ formatRupiah(calcIncomeTax(s.grand_total)) }}
                </td>
              </tr>

              <tr v-if="!sales?.data || sales.data.length === 0">
                <td colspan="6" class="py-12 text-center text-slate-400">
                  <Percent class="w-12 h-12 mx-auto text-slate-600 mb-2" />
                  <p class="font-semibold">Belum ada transaksi kena pajak pada masa pajak ini</p>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="sales?.links && sales.links.length > 3" class="p-4 border-t border-[#2D2D54] bg-[#15152D] flex items-center justify-between">
          <p class="text-xs text-slate-400">
            Menampilkan {{ sales.from || 0 }} - {{ sales.to || 0 }} dari {{ sales.total || 0 }} data
          </p>
          <div class="flex gap-1">
            <template v-for="(link, i) in sales.links" :key="i">
              <Link
                v-if="link.url"
                :href="link.url"
                v-html="link.label"
                :class="[
                  link.active ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-300 hover:bg-[#25254B] hover:text-white',
                  'px-3 py-1.5 rounded-lg text-xs font-medium transition-colors'
                ]"
              />
              <span
                v-else
                v-html="link.label"
                class="px-3 py-1.5 rounded-lg text-xs font-medium text-slate-600 opacity-60"
              />
            </template>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
