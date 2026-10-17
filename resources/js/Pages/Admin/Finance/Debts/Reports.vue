<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
  Calendar, Coins, Printer, CheckCircle2, 
  FileText, ArrowLeft, ArrowUpRight, ArrowDownRight
} from 'lucide-vue-next';

const props = defineProps({
  debts: Array,
  stats: Object,
});

const formatRupiah = (val) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val || 0);
};

const formatDate = (dateString) => {
  if (!dateString) return '-';
  const d = new Date(dateString);
  return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
};
</script>

<template>
  <AdminLayout>
    <Head title="Laporan Utang Piutang Supplier Emas" />

    <div class="space-y-6">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2 text-xs font-mono text-amber-500 uppercase tracking-widest">
            <FileText class="w-4 h-4" />
            <span>Laporan Akuntansi Toko</span>
          </div>
          <h2 class="text-2xl font-bold tracking-tight text-white mt-1">
            Laporan Utang Piutang
          </h2>
          <p class="text-sm text-slate-400">
            Rekapitulasi total tagihan, pelunasan utang supplier emas, dan sisa kewajiban toko.
          </p>
        </div>

        <div class="flex items-center gap-3">
          <button 
            @click="window.print()"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-bold text-sm shadow-lg shadow-amber-500/20 cursor-pointer transition-all"
          >
            <Printer class="w-4 h-4" />
            <span>Cetak Laporan Utang</span>
          </button>
        </div>
      </div>

      <!-- KPI Summary -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-[#1C1C3E] border border-[#2D2D54] rounded-2xl p-5 flex items-center justify-between">
          <div>
            <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Total Nilai Faktur Utang</p>
            <h3 class="text-2xl font-bold text-white mt-1">{{ formatRupiah(stats.total_debts) }}</h3>
            <span class="text-xs text-slate-400 mt-1">{{ stats.count }} transaksi faktur</span>
          </div>
          <div class="w-12 h-12 rounded-xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center">
            <FileText class="w-6 h-6" />
          </div>
        </div>

        <div class="bg-[#1C1C3E] border border-[#2D2D54] rounded-2xl p-5 flex items-center justify-between">
          <div>
            <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Total Sudah Dibayar</p>
            <h3 class="text-2xl font-bold text-emerald-400 mt-1">{{ formatRupiah(stats.total_paid) }}</h3>
            <span class="text-xs text-slate-400 mt-1">Realisasi kas keluar</span>
          </div>
          <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center">
            <ArrowDownRight class="w-6 h-6" />
          </div>
        </div>

        <div class="bg-[#1C1C3E] border border-[#2D2D54] rounded-2xl p-5 flex items-center justify-between">
          <div>
            <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Sisa Saldo Utang Toko</p>
            <h3 class="text-2xl font-bold text-amber-400 mt-1">{{ formatRupiah(stats.total_remaining) }}</h3>
            <span class="text-xs text-slate-400 mt-1">Kewajiban tersisa</span>
          </div>
          <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center">
            <Coins class="w-6 h-6" />
          </div>
        </div>
      </div>

      <!-- Data Table -->
      <div class="bg-[#1C1C3E] border border-[#2D2D54] rounded-2xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-sm text-slate-300">
            <thead class="bg-[#15152D] text-xs uppercase font-mono tracking-wider text-slate-400 border-b border-[#2D2D54]">
              <tr>
                <th class="px-5 py-3.5">Kode Utang</th>
                <th class="px-5 py-3.5">Supplier</th>
                <th class="px-5 py-3.5">Jatuh Tempo</th>
                <th class="px-5 py-3.5 text-right">Total Utang</th>
                <th class="px-5 py-3.5 text-right">Dibayar</th>
                <th class="px-5 py-3.5 text-right">Sisa Utang</th>
                <th class="px-5 py-3.5 text-center">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-[#2D2D54]/50">
              <tr v-for="d in debts" :key="d.id" class="hover:bg-[#24244D]/50 transition-colors">
                <td class="px-5 py-4 font-bold text-white font-mono">{{ d.payable_code }}</td>
                <td class="px-5 py-4 font-medium text-slate-200">{{ d.supplier?.name }}</td>
                <td class="px-5 py-4 text-xs font-mono text-slate-400">{{ formatDate(d.due_date) }}</td>
                <td class="px-5 py-4 text-right font-mono text-slate-300">{{ formatRupiah(d.total_amount) }}</td>
                <td class="px-5 py-4 text-right font-mono text-emerald-400 font-semibold">{{ formatRupiah(d.paid_amount) }}</td>
                <td class="px-5 py-4 text-right font-mono text-amber-400 font-bold">{{ formatRupiah(d.remaining_amount) }}</td>
                <td class="px-5 py-4 text-center">
                  <span 
                    :class="[
                      d.status === 'paid' ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' :
                      d.status === 'partial' ? 'bg-amber-500/10 text-amber-400 border-amber-500/20' :
                      'bg-rose-500/10 text-rose-400 border-rose-500/20',
                      'px-2.5 py-1 rounded-full text-xs font-semibold uppercase border'
                    ]"
                  >
                    {{ d.status === 'paid' ? 'Lunas' : d.status === 'partial' ? 'Dicicil' : 'Belum Bayar' }}
                  </span>
                </td>
              </tr>
              <tr v-if="debts.length === 0">
                <td colspan="7" class="px-5 py-10 text-center text-slate-400">
                  Belum ada data utang piutang.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
