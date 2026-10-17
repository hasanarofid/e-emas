<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
  Wrench, FileText, Printer, CheckCircle2, 
  Clock, Coins, User, ArrowLeft
} from 'lucide-vue-next';

const props = defineProps({
  services: Array,
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
    <Head title="Laporan Servis Perhiasan Emas" />

    <div class="space-y-6">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2 text-xs font-mono text-amber-500 uppercase tracking-widest">
            <FileText class="w-4 h-4" />
            <span>Laporan Reparasi & Cuci Emas</span>
          </div>
          <h2 class="text-2xl font-bold tracking-tight text-white mt-1">
            Laporan Servis Perhiasan
          </h2>
          <p class="text-sm text-slate-400">
            Rekapitulasi seluruh order jasa servis, reparasi patri, sepuh, dan cuci perhiasan emas.
          </p>
        </div>

        <div class="flex items-center gap-3">
          <button 
            @click="window.print()"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-bold text-sm shadow-lg shadow-amber-500/20 cursor-pointer transition-all"
          >
            <Printer class="w-4 h-4" />
            <span>Cetak Laporan Servis</span>
          </button>
        </div>
      </div>

      <!-- KPI Summary -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-[#1C1C3E] border border-[#2D2D54] rounded-2xl p-4 flex items-center justify-between">
          <div>
            <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Total Order Servis</p>
            <h3 class="text-2xl font-bold text-white mt-1">{{ stats.total_services }}</h3>
            <span class="text-xs text-slate-400 mt-1">Order masuk</span>
          </div>
          <div class="w-12 h-12 rounded-xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center">
            <Wrench class="w-6 h-6" />
          </div>
        </div>

        <div class="bg-[#1C1C3E] border border-[#2D2D54] rounded-2xl p-4 flex items-center justify-between">
          <div>
            <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Total Biaya Jasa</p>
            <h3 class="text-2xl font-bold text-emerald-400 mt-1">{{ formatRupiah(stats.total_fee) }}</h3>
            <span class="text-xs text-slate-400 mt-1">Omset pendapatan servis</span>
          </div>
          <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center">
            <Coins class="w-6 h-6" />
          </div>
        </div>

        <div class="bg-[#1C1C3E] border border-[#2D2D54] rounded-2xl p-4 flex items-center justify-between">
          <div>
            <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Servis Selesai Diambil</p>
            <h3 class="text-2xl font-bold text-cyan-400 mt-1">{{ stats.completed_count }}</h3>
            <span class="text-xs text-slate-400 mt-1">Sudah diserahkan</span>
          </div>
          <div class="w-12 h-12 rounded-xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 flex items-center justify-center">
            <CheckCircle2 class="w-6 h-6" />
          </div>
        </div>

        <div class="bg-[#1C1C3E] border border-[#2D2D54] rounded-2xl p-4 flex items-center justify-between">
          <div>
            <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Sedang Dikerjakan</p>
            <h3 class="text-2xl font-bold text-amber-400 mt-1">{{ stats.in_progress_count }}</h3>
            <span class="text-xs text-slate-400 mt-1">Antrean bengkel</span>
          </div>
          <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center">
            <Clock class="w-6 h-6" />
          </div>
        </div>
      </div>

      <!-- Data Table -->
      <div class="bg-[#1C1C3E] border border-[#2D2D54] rounded-2xl overflow-hidden shadow-xl">
        <div class="p-4 border-b border-[#2D2D54] bg-[#15152D] flex items-center justify-between">
          <h3 class="font-bold text-white text-sm flex items-center gap-2">
            <Wrench class="w-4 h-4 text-amber-400" />
            <span>Rekap Seluruh Riwayat Servis</span>
          </h3>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left text-sm text-slate-300">
            <thead class="bg-[#15152D]/50 text-xs uppercase font-mono tracking-wider text-slate-400 border-b border-[#2D2D54]">
              <tr>
                <th class="px-5 py-3.5">No Servis</th>
                <th class="px-5 py-3.5">Pelanggan</th>
                <th class="px-5 py-3.5">Barang & Jenis Servis</th>
                <th class="px-5 py-3.5">Estimasi Selesai</th>
                <th class="px-5 py-3.5 text-right">Biaya Servis</th>
                <th class="px-5 py-3.5 text-center">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-[#2D2D54]/50">
              <tr v-for="s in services" :key="s.id" class="hover:bg-[#24244D]/50 transition-colors">
                <td class="px-5 py-4 font-mono font-bold text-white">{{ s.service_number }}</td>
                <td class="px-5 py-4">
                  <div class="text-slate-200 font-medium">{{ s.customer_name }}</div>
                  <div class="text-xs text-slate-400">{{ s.customer_phone || '-' }}</div>
                </td>
                <td class="px-5 py-4">
                  <div class="text-slate-200 font-medium">{{ s.item_name }}</div>
                  <div class="text-xs text-amber-400">{{ s.service_type }}</div>
                </td>
                <td class="px-5 py-4 text-slate-300">{{ formatDate(s.estimated_completion_date) }}</td>
                <td class="px-5 py-4 text-right font-mono font-bold text-emerald-400">
                  {{ formatRupiah(s.cost) }}
                </td>
                <td class="px-5 py-4 text-center">
                  <span class="px-2.5 py-1 rounded-full text-xs font-semibold uppercase bg-[#25254B] text-slate-300 border border-[#2D2D54]">
                    {{ s.status }}
                  </span>
                </td>
              </tr>
              <tr v-if="services.length === 0">
                <td colspan="6" class="px-5 py-10 text-center text-slate-400">
                  Belum ada data servis perhiasan.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
