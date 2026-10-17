<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
  Scale, FileText, Printer, CheckCircle2, 
  AlertTriangle, Clock, Layers, User
} from 'lucide-vue-next';

const props = defineProps({
  logs: Array,
  stats: Object,
});

const formatWeight = (val) => {
  return Number(val || 0).toFixed(3) + ' gr';
};

const formatDate = (dateString) => {
  if (!dateString) return '-';
  const d = new Date(dateString);
  return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
};
</script>

<template>
  <AdminLayout>
    <Head title="Laporan Timbang Baki Toko Emas" />

    <div class="space-y-6">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2 text-xs font-mono text-amber-500 uppercase tracking-widest">
            <Scale class="w-4 h-4" />
            <span>Audit & Kontrol Gramasi Baki</span>
          </div>
          <h2 class="text-2xl font-bold tracking-tight text-white mt-1">
            Laporan Timbang Baki
          </h2>
          <p class="text-sm text-slate-400">
            Rekap hasil timbang fisik baki pembukaan pagi dan penutupan toko sore untuk mencegah susut & kehilangan.
          </p>
        </div>

        <div class="flex items-center gap-3">
          <button 
            @click="window.print()"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-bold text-sm shadow-lg shadow-amber-500/20 cursor-pointer transition-all"
          >
            <Printer class="w-4 h-4" />
            <span>Cetak Rekap Timbang</span>
          </button>
        </div>
      </div>

      <!-- KPI Summary -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-[#1C1C3E] border border-[#2D2D54] rounded-2xl p-4 flex items-center justify-between">
          <div>
            <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Total Sesi Timbang</p>
            <h3 class="text-2xl font-bold text-white mt-1">{{ stats.total_scales }}</h3>
            <span class="text-xs text-slate-400 mt-1">Log tercatat</span>
          </div>
          <div class="w-12 h-12 rounded-xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center">
            <Scale class="w-6 h-6" />
          </div>
        </div>

        <div class="bg-[#1C1C3E] border border-[#2D2D54] rounded-2xl p-4 flex items-center justify-between">
          <div>
            <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Sesuai / Klop (Matched)</p>
            <h3 class="text-2xl font-bold text-emerald-400 mt-1">{{ stats.matched_count }}</h3>
            <span class="text-xs text-slate-400 mt-1">Gramasi akurat</span>
          </div>
          <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center">
            <CheckCircle2 class="w-6 h-6" />
          </div>
        </div>

        <div class="bg-[#1C1C3E] border border-[#2D2D54] rounded-2xl p-4 flex items-center justify-between">
          <div>
            <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Selisih (Discrepancy)</p>
            <h3 class="text-2xl font-bold text-rose-400 mt-1">{{ stats.discrepancy_count }}</h3>
            <span class="text-xs text-slate-400 mt-1">Perlu dicek kasir</span>
          </div>
          <div class="w-12 h-12 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 flex items-center justify-center">
            <AlertTriangle class="w-6 h-6" />
          </div>
        </div>

        <div class="bg-[#1C1C3E] border border-[#2D2D54] rounded-2xl p-4 flex items-center justify-between">
          <div>
            <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Total Akumulasi Selisih</p>
            <h3 class="text-2xl font-bold text-amber-400 mt-1">{{ formatWeight(stats.total_difference) }}</h3>
            <span class="text-xs text-slate-400 mt-1">Net gramasi</span>
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
            <Scale class="w-4 h-4 text-amber-400" />
            <span>Riwayat Seluruh Hasil Penimbangan Baki</span>
          </h3>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left text-sm text-slate-300">
            <thead class="bg-[#15152D]/50 text-xs uppercase font-mono tracking-wider text-slate-400 border-b border-[#2D2D54]">
              <tr>
                <th class="px-5 py-3.5">Tanggal & Sesi</th>
                <th class="px-5 py-3.5">Kode Baki</th>
                <th class="px-5 py-3.5 text-center">Pcs (Sistem / Fisik)</th>
                <th class="px-5 py-3.5 text-right">Berat Sistem</th>
                <th class="px-5 py-3.5 text-right">Berat Fisik Timbang</th>
                <th class="px-5 py-3.5 text-right">Selisih</th>
                <th class="px-5 py-3.5 text-center">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-[#2D2D54]/50">
              <tr v-for="l in logs" :key="l.id" class="hover:bg-[#24244D]/50 transition-colors">
                <td class="px-5 py-4">
                  <div class="font-medium text-slate-200">{{ formatDate(l.scale_date) }}</div>
                  <div class="text-xs text-slate-400 capitalize">Sesi: {{ l.session }}</div>
                </td>
                <td class="px-5 py-4">
                  <div class="font-bold text-amber-400 font-mono">{{ l.tray?.code || '-' }}</div>
                  <div class="text-xs text-slate-400">{{ l.tray?.name || '-' }}</div>
                </td>
                <td class="px-5 py-4 text-center font-mono">
                  <span class="text-slate-300">{{ l.item_count_system }}</span> / 
                  <span class="font-bold text-white">{{ l.item_count_real }}</span>
                </td>
                <td class="px-5 py-4 text-right font-mono text-slate-400">
                  {{ formatWeight(l.weight_system) }}
                </td>
                <td class="px-5 py-4 text-right font-mono font-bold text-white">
                  {{ formatWeight(l.weight_real) }}
                </td>
                <td class="px-5 py-4 text-right font-mono" :class="l.difference_weight != 0 ? 'text-rose-400 font-bold' : 'text-emerald-400'">
                  {{ formatWeight(l.difference_weight) }}
                </td>
                <td class="px-5 py-4 text-center">
                  <span 
                    :class="[
                      l.status === 'matched' ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' : 'bg-rose-500/10 text-rose-400 border-rose-500/20',
                      'px-2.5 py-1 rounded-full text-xs font-semibold uppercase border'
                    ]"
                  >
                    {{ l.status === 'matched' ? 'Cocok' : 'Selisih' }}
                  </span>
                </td>
              </tr>
              <tr v-if="logs.length === 0">
                <td colspan="7" class="px-5 py-10 text-center text-slate-400">
                  Belum ada log timbang baki.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
