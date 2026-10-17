<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
  Calendar, 
  Coins, 
  CheckCircle2, 
  ArrowUpRight, 
  ArrowDownLeft, 
  ShieldCheck, 
  Clock,
  Download
} from 'lucide-vue-next';

const props = defineProps({
  selectedDate: String,
  newPawns: Array,
  settledPawns: Array,
  allPawns: Array,
  stats: Object,
});

const date = ref(props.selectedDate);

const handleDateChange = () => {
  router.get(route('admin.pawns.daily'), { date: date.value }, { preserveState: true });
};

const formatRupiah = (val) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val || 0);
};

const formatDate = (dateStr) => {
  if (!dateStr) return '-';
  const d = new Date(dateStr);
  return d.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
};
</script>

<template>
  <AdminLayout>
    <Head title="Laporan Gadai Harian - POS Emas" />

    <div class="space-y-6">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2 text-xs font-mono text-amber-500 uppercase tracking-widest">
            <Calendar class="w-4 h-4" />
            <span>Rekap Harian Kasir Gadai</span>
          </div>
          <h2 class="text-2xl font-bold tracking-tight text-white mt-1">
            Laporan Gadai Perhiasan Harian
          </h2>
          <p class="text-sm text-slate-400">
            Arus pencairan pinjaman baru dan pelunasan tebus gadai untuk tanggal: <span class="text-white font-medium">{{ formatDate(selectedDate) }}</span>.
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
          <div class="w-12 h-12 rounded-xl bg-rose-500/10 border border-rose-500/20 flex items-center justify-center text-rose-400 shrink-0">
            <ArrowUpRight class="w-6 h-6" />
          </div>
          <div>
            <p class="text-xs font-medium text-slate-400">Pencairan Pinjaman Baru (Keluar)</p>
            <p class="text-xl font-bold text-rose-400 mt-0.5">{{ formatRupiah(stats.new_loan_amount) }}</p>
            <p class="text-[11px] text-slate-400">{{ stats.new_loan_count }} Transaksi Baru</p>
          </div>
        </div>

        <div class="p-4 rounded-2xl bg-[#1E1E38] border border-slate-800 flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 shrink-0">
            <ArrowDownLeft class="w-6 h-6" />
          </div>
          <div>
            <p class="text-xs font-medium text-slate-400">Pelunasan Tebus (Kas Masuk)</p>
            <p class="text-xl font-bold text-emerald-400 mt-0.5">{{ formatRupiah(stats.settled_amount) }}</p>
            <p class="text-[11px] text-slate-400">{{ stats.settled_count }} Berkas Ditebus</p>
          </div>
        </div>

        <div class="p-4 rounded-2xl bg-[#1E1E38] border border-slate-800 flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-purple-500/10 border border-purple-500/20 flex items-center justify-center text-purple-400 shrink-0">
            <Coins class="w-6 h-6" />
          </div>
          <div>
            <p class="text-xs font-medium text-slate-400">Pendapatan Jasa Titip Masuk</p>
            <p class="text-xl font-bold text-purple-400 mt-0.5">{{ formatRupiah(stats.interest_earned) }}</p>
            <p class="text-[11px] text-slate-400">Bunga Diterima Hari Ini</p>
          </div>
        </div>
      </div>

      <!-- 2 Sections: Pinjaman Baru & Pelunasan -->
      <div class="space-y-6">
        <!-- Tabel Gadai Baru Hari Ini -->
        <div class="rounded-2xl bg-[#1E1E38] border border-slate-800 overflow-hidden shadow-xl">
          <div class="p-4 border-b border-slate-800 flex items-center justify-between">
            <h3 class="text-base font-bold text-white flex items-center gap-2">
              <ArrowUpRight class="w-4 h-4 text-rose-400" />
              <span>Gadai Baru Diterbitkan Hari Ini</span>
            </h3>
            <span class="text-xs text-slate-400 font-mono">{{ newPawns.length }} Berkas</span>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300">
              <thead class="bg-[#151528] text-xs uppercase text-slate-400 font-semibold border-b border-slate-800">
                <tr>
                  <th class="px-6 py-3">No. Gadai</th>
                  <th class="px-6 py-3">Nasabah</th>
                  <th class="px-6 py-3">Barang Jaminan</th>
                  <th class="px-6 py-3 text-right">Nilai Taksiran</th>
                  <th class="px-6 py-3 text-right">Pinjaman Dicairkan</th>
                  <th class="px-6 py-3 text-right">Jasa Titip / Bln</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-800/80">
                <tr v-if="newPawns.length === 0">
                  <td colspan="6" class="px-6 py-6 text-center text-slate-400">
                    Tidak ada transaksi gadai baru pada tanggal ini.
                  </td>
                </tr>
                <tr v-for="pawn in newPawns" :key="pawn.id" class="hover:bg-[#25254B]/40">
                  <td class="px-6 py-3 font-mono font-bold text-amber-400 text-xs">{{ pawn.pawn_number }}</td>
                  <td class="px-6 py-3 font-semibold text-white text-xs">{{ pawn.customer_name }}</td>
                  <td class="px-6 py-3 text-xs">{{ pawn.item_name }} ({{ pawn.gross_weight }} Gr)</td>
                  <td class="px-6 py-3 text-right font-mono text-xs">{{ formatRupiah(pawn.estimated_value) }}</td>
                  <td class="px-6 py-3 text-right font-mono font-bold text-rose-400 text-xs">{{ formatRupiah(pawn.loan_amount) }}</td>
                  <td class="px-6 py-3 text-right font-mono text-purple-300 text-xs">{{ formatRupiah(pawn.monthly_fee) }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Tabel Tebus Gadai Hari Ini -->
        <div class="rounded-2xl bg-[#1E1E38] border border-slate-800 overflow-hidden shadow-xl">
          <div class="p-4 border-b border-slate-800 flex items-center justify-between">
            <h3 class="text-base font-bold text-white flex items-center gap-2">
              <CheckCircle2 class="w-4 h-4 text-emerald-400" />
              <span>Gadai Ditebus Lunas Hari Ini</span>
            </h3>
            <span class="text-xs text-slate-400 font-mono">{{ settledPawns.length }} Berkas</span>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300">
              <thead class="bg-[#151528] text-xs uppercase text-slate-400 font-semibold border-b border-slate-800">
                <tr>
                  <th class="px-6 py-3">No. Gadai</th>
                  <th class="px-6 py-3">Nasabah</th>
                  <th class="px-6 py-3">Barang Dikembalikan</th>
                  <th class="px-6 py-3 text-right">Pokok Pinjaman</th>
                  <th class="px-6 py-3 text-right">Total Uang Tebus</th>
                  <th class="px-6 py-3 text-center">Status</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-800/80">
                <tr v-if="settledPawns.length === 0">
                  <td colspan="6" class="px-6 py-6 text-center text-slate-400">
                    Tidak ada pelunasan tebus gadai pada tanggal ini.
                  </td>
                </tr>
                <tr v-for="pawn in settledPawns" :key="pawn.id" class="hover:bg-[#25254B]/40">
                  <td class="px-6 py-3 font-mono font-bold text-amber-400 text-xs">{{ pawn.pawn_number }}</td>
                  <td class="px-6 py-3 font-semibold text-white text-xs">{{ pawn.customer_name }}</td>
                  <td class="px-6 py-3 text-xs">{{ pawn.item_name }} ({{ pawn.gross_weight }} Gr)</td>
                  <td class="px-6 py-3 text-right font-mono text-xs">{{ formatRupiah(pawn.loan_amount) }}</td>
                  <td class="px-6 py-3 text-right font-mono font-bold text-emerald-400 text-xs">{{ formatRupiah(pawn.settled_amount) }}</td>
                  <td class="px-6 py-3 text-center">
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                      Lunas
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
