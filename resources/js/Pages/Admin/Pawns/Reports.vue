<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
  CreditCard, 
  Search, 
  Calendar, 
  Coins, 
  CheckCircle2, 
  Clock, 
  AlertCircle, 
  Download, 
  ShieldCheck, 
  Scale, 
  ArrowUpRight,
  TrendingUp,
  Percent
} from 'lucide-vue-next';

const props = defineProps({
  pawns: Object,
  filters: Object,
  stats: Object,
});

const search = ref(props.filters.search || '');
const statusFilter = ref(props.filters.status || '');

const handleFilter = () => {
  router.get(route('admin.pawns.reports'), {
    search: search.value,
    status: statusFilter.value,
  }, { preserveState: true, replace: true });
};

const formatRupiah = (val) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val || 0);
};

const formatDate = (dateStr) => {
  if (!dateStr) return '-';
  const d = new Date(dateStr);
  return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
};

const getStatusBadge = (status) => {
  switch (status) {
    case 'active':
      return { label: 'Aktif Berjalan', color: 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' };
    case 'redeemed':
      return { label: 'Ditebus Lunas', color: 'bg-blue-500/10 text-blue-400 border-blue-500/20' };
    case 'extended':
      return { label: 'Diperpanjang', color: 'bg-amber-500/10 text-amber-400 border-amber-500/20' };
    case 'auctioned':
      return { label: 'Siap Lelang', color: 'bg-rose-500/10 text-rose-400 border-rose-500/20' };
    default:
      return { label: status, color: 'bg-slate-500/10 text-slate-400 border-slate-500/20' };
  }
};
</script>

<template>
  <AdminLayout>
    <Head title="Laporan Gadai Perhiasan Emas - POS Emas" />

    <div class="space-y-6">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2 text-xs font-mono text-amber-500 uppercase tracking-widest">
            <CreditCard class="w-4 h-4" />
            <span>Laporan & Portofolio Gadai</span>
          </div>
          <h2 class="text-2xl font-bold tracking-tight text-white mt-1">
            Laporan Transaksi Gadai Perhiasan
          </h2>
          <p class="text-sm text-slate-400">
            Rekapitulasi portofolio dana pinjaman, pendapatan jasa titip bulanan, dan jaminan fisik emas di brankas toko.
          </p>
        </div>

        <div class="flex items-center gap-2.5">
          <Link
            :href="route('admin.pawns.create')"
            class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 font-bold text-sm shadow-lg shadow-amber-500/20 transition-all"
          >
            Gadai Baru
          </Link>
          <button
            onclick="window.print()"
            class="px-4 py-2.5 rounded-xl bg-[#25254B] hover:bg-[#323265] text-slate-200 text-sm font-semibold transition-colors flex items-center gap-2"
          >
            <Download class="w-4 h-4" />
            <span>Cetak / Ekspor</span>
          </button>
        </div>
      </div>

      <!-- Quick Summary Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-4 rounded-2xl bg-[#1E1E38] border border-slate-800 flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400 shrink-0">
            <Coins class="w-6 h-6" />
          </div>
          <div>
            <p class="text-xs font-medium text-slate-400">Pinjaman Aktif Beredar</p>
            <p class="text-lg font-bold text-amber-400 mt-0.5">{{ formatRupiah(stats.active_loans) }}</p>
          </div>
        </div>

        <div class="p-4 rounded-2xl bg-[#1E1E38] border border-slate-800 flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 shrink-0">
            <ShieldCheck class="w-6 h-6" />
          </div>
          <div>
            <p class="text-xs font-medium text-slate-400">Nilai Taksiran Jaminan</p>
            <p class="text-lg font-bold text-emerald-400 mt-0.5">{{ formatRupiah(stats.estimated_collateral) }}</p>
          </div>
        </div>

        <div class="p-4 rounded-2xl bg-[#1E1E38] border border-slate-800 flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-purple-500/10 border border-purple-500/20 flex items-center justify-center text-purple-400 shrink-0">
            <Percent class="w-6 h-6" />
          </div>
          <div>
            <p class="text-xs font-medium text-slate-400">Potensi Jasa Titip / Bln</p>
            <p class="text-lg font-bold text-purple-400 mt-0.5">{{ formatRupiah(stats.monthly_interest_revenue) }}</p>
          </div>
        </div>

        <div class="p-4 rounded-2xl bg-[#1E1E38] border border-slate-800 flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400 shrink-0">
            <CheckCircle2 class="w-6 h-6" />
          </div>
          <div>
            <p class="text-xs font-medium text-slate-400">Telah Ditebus Lunas</p>
            <p class="text-xl font-bold text-white mt-0.5">{{ stats.redeemed_count }} Berkas</p>
          </div>
        </div>
      </div>

      <!-- Filter Bar -->
      <div class="p-4 rounded-2xl bg-[#1E1E38] border border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex flex-wrap items-center gap-3">
          <div class="relative w-64">
            <Search class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
            <input
              v-model="search"
              @keyup.enter="handleFilter"
              type="text"
              placeholder="Cari no gadai, nasabah, barang..."
              class="w-full pl-9 pr-3 py-2 rounded-xl bg-[#151528] border border-slate-700 text-white placeholder-slate-500 text-xs focus:outline-none focus:border-amber-500"
            />
          </div>

          <select
            v-model="statusFilter"
            @change="handleFilter"
            class="px-3 py-2 rounded-xl bg-[#151528] border border-slate-700 text-slate-200 text-xs focus:outline-none focus:border-amber-500"
          >
            <option value="">Semua Status</option>
            <option value="active">Aktif Berjalan</option>
            <option value="redeemed">Ditebus Lunas</option>
            <option value="auctioned">Siap Lelang</option>
          </select>
        </div>

        <button
          @click="handleFilter"
          class="px-4 py-2 rounded-xl bg-[#25254B] hover:bg-[#323265] text-slate-200 text-xs font-semibold transition-colors"
        >
          Filter
        </button>
      </div>

      <!-- Table Section -->
      <div class="rounded-2xl bg-[#1E1E38] border border-slate-800 overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-sm text-slate-300">
            <thead class="bg-[#151528] text-xs uppercase text-slate-400 font-semibold border-b border-slate-800">
              <tr>
                <th class="px-6 py-4">Nomor Gadai</th>
                <th class="px-6 py-4">Nasabah</th>
                <th class="px-6 py-4">Barang Jaminan</th>
                <th class="px-6 py-4 text-right">Nilai Taksiran</th>
                <th class="px-6 py-4 text-right">Pinjaman Dicairkan</th>
                <th class="px-6 py-4 text-right">Jasa Titip / Bln</th>
                <th class="px-6 py-4 text-center">Jatuh Tempo</th>
                <th class="px-6 py-4 text-center">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/80">
              <tr v-if="pawns.data.length === 0">
                <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                  Tidak ada data gadai ditemukan.
                </td>
              </tr>
              <tr
                v-for="pawn in pawns.data"
                :key="pawn.id"
                class="hover:bg-[#25254B]/40 transition-colors"
              >
                <td class="px-6 py-4 font-mono font-bold text-amber-400 text-xs">
                  {{ pawn.pawn_number }}
                </td>
                <td class="px-6 py-4">
                  <div class="font-semibold text-white text-xs">{{ pawn.customer_name }}</div>
                  <div class="text-[11px] text-slate-400 font-mono">{{ pawn.customer_phone || '-' }}</div>
                </td>
                <td class="px-6 py-4">
                  <div class="font-medium text-slate-200 text-xs">{{ pawn.item_name }}</div>
                  <div class="text-[11px] text-amber-400 font-mono">
                    {{ pawn.gold_rate ? pawn.gold_rate.code : 'Emas' }} • {{ pawn.gross_weight }} Gr
                  </div>
                </td>
                <td class="px-6 py-4 text-right font-mono text-xs text-slate-300">
                  {{ formatRupiah(pawn.estimated_value) }}
                </td>
                <td class="px-6 py-4 text-right font-mono font-bold text-white text-xs">
                  {{ formatRupiah(pawn.loan_amount) }}
                </td>
                <td class="px-6 py-4 text-right font-mono text-xs text-purple-300">
                  {{ formatRupiah(pawn.monthly_fee) }}
                  <span class="text-[10px] text-slate-400 block font-normal">({{ pawn.monthly_interest_rate }}%)</span>
                </td>
                <td class="px-6 py-4 text-center font-mono text-xs text-slate-300">
                  {{ formatDate(pawn.due_date) }}
                </td>
                <td class="px-6 py-4 text-center">
                  <span
                    class="px-2.5 py-1 rounded-full text-[11px] font-semibold border"
                    :class="getStatusBadge(pawn.status).color"
                  >
                    {{ getStatusBadge(pawn.status).label }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="pawns.links && pawns.links.length > 3" class="p-4 border-t border-slate-800 flex items-center justify-between">
          <p class="text-xs text-slate-400">
            Menampilkan {{ pawns.from || 0 }} - {{ pawns.to || 0 }} dari {{ pawns.total || 0 }} transaksi
          </p>
          <div class="flex items-center gap-1">
            <button
              v-for="(link, i) in pawns.links"
              :key="i"
              :disabled="!link.url || link.active"
              @click="router.get(link.url, {}, { preserveState: true })"
              v-html="link.label"
              class="px-3 py-1.5 rounded-lg text-xs font-medium transition-colors"
              :class="[
                link.active 
                  ? 'bg-amber-500 text-slate-950 font-bold' 
                  : link.url 
                    ? 'bg-[#25254B] text-slate-300 hover:bg-[#323265] hover:text-white' 
                    : 'opacity-40 bg-[#151528] text-slate-500 cursor-not-allowed'
              ]"
            ></button>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
