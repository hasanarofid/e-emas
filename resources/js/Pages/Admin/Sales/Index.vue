<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { 
  FileText, 
  Search, 
  Printer, 
  Eye, 
  Plus, 
  Coins, 
  Calendar, 
  CreditCard, 
  CheckCircle2, 
  ArrowUpRight,
  Sparkles,
  ShoppingBag,
  Receipt
} from 'lucide-vue-next';

const props = defineProps({
  sales: {
    type: Object,
    required: true,
  },
  filters: {
    type: Object,
    default: () => ({}),
  },
  stats: {
    type: Object,
    required: true,
  },
});

const search = ref(props.filters.search || '');
const paymentFilter = ref(props.filters.payment_method || '');
const dateFrom = ref(props.filters.date_from || '');
const dateTo = ref(props.filters.date_to || '');

const handleFilter = () => {
  router.get(route('admin.sales.index'), {
    search: search.value,
    payment_method: paymentFilter.value,
    date_from: dateFrom.value,
    date_to: dateTo.value,
  }, { preserveState: true, replace: true });
};

const formatRupiah = (number) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0,
  }).format(number || 0);
};

const formatDate = (dateString) => {
  if (!dateString) return '-';
  const d = new Date(dateString);
  return d.toLocaleDateString('id-ID', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
};
</script>

<template>
  <AdminLayout>
    <Head title="Data Faktur Penjualan Emas - POS Emas" />

    <div class="space-y-6">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2 text-xs font-mono text-amber-500 uppercase tracking-widest">
            <ShoppingBag class="w-4 h-4" />
            <span>Faktur & Transaksi Penjualan</span>
          </div>
          <h2 class="text-2xl font-bold tracking-tight text-white mt-1">
            Data Riwayat Penjualan Emas
          </h2>
          <p class="text-sm text-slate-400">
            Daftar seluruh nota faktur transaksi penjualan toko, cetak ulang struk kasir, dan surat perhiasan emas.
          </p>
        </div>

        <Link
          :href="route('admin.pos.index')"
          class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 font-bold text-sm shadow-lg shadow-amber-500/20 transition-all cursor-pointer"
        >
          <Plus class="w-4 h-4" />
          <span>Faktur Penjualan Baru (POS)</span>
        </Link>
      </div>

      <!-- Quick Summary Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-4 rounded-2xl bg-[#1E1E38] border border-slate-800 flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400 shrink-0">
            <Coins class="w-6 h-6" />
          </div>
          <div>
            <p class="text-xs font-medium text-slate-400">Total Omset Penjualan</p>
            <p class="text-lg font-bold text-amber-400 mt-0.5">{{ formatRupiah(stats.total_turnover) }}</p>
          </div>
        </div>

        <div class="p-4 rounded-2xl bg-[#1E1E38] border border-slate-800 flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400 shrink-0">
            <Receipt class="w-6 h-6" />
          </div>
          <div>
            <p class="text-xs font-medium text-slate-400">Total Faktur Penjualan</p>
            <p class="text-xl font-bold text-white mt-0.5">{{ stats.total_sales_count || 0 }} Faktur</p>
          </div>
        </div>

        <div class="p-4 rounded-2xl bg-[#1E1E38] border border-slate-800 flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 shrink-0">
            <Sparkles class="w-6 h-6" />
          </div>
          <div>
            <p class="text-xs font-medium text-slate-400">Gramasi Terjual</p>
            <p class="text-xl font-bold text-emerald-400 mt-0.5">{{ Number(stats.total_weight_sold || 0).toFixed(2) }} Gr</p>
          </div>
        </div>

        <div class="p-4 rounded-2xl bg-[#1E1E38] border border-slate-800 flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-purple-500/10 border border-purple-500/20 flex items-center justify-center text-purple-400 shrink-0">
            <Calendar class="w-6 h-6" />
          </div>
          <div>
            <p class="text-xs font-medium text-slate-400">Penjualan Hari Ini</p>
            <p class="text-lg font-bold text-white mt-0.5">{{ formatRupiah(stats.today_turnover) }}</p>
          </div>
        </div>
      </div>

      <!-- Filter Bar -->
      <div class="p-4 rounded-2xl bg-[#1E1E38] border border-slate-800 flex flex-wrap items-center justify-between gap-4">
        <div class="flex flex-wrap items-center gap-3">
          <div class="relative w-64">
            <Search class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
            <input
              v-model="search"
              @keyup.enter="handleFilter"
              type="text"
              placeholder="Cari nomor nota, nama, HP..."
              class="w-full pl-9 pr-3 py-2 rounded-xl bg-[#151528] border border-slate-700 text-white placeholder-slate-500 text-xs focus:outline-none focus:border-amber-500"
            />
          </div>

          <select
            v-model="paymentFilter"
            @change="handleFilter"
            class="px-3 py-2 rounded-xl bg-[#151528] border border-slate-700 text-slate-200 text-xs focus:outline-none focus:border-amber-500"
          >
            <option value="">Semua Metode Bayar</option>
            <option value="cash">Tunai (Cash)</option>
            <option value="transfer">Transfer Bank</option>
            <option value="qris">QRIS Toko</option>
          </select>

          <input
            v-model="dateFrom"
            type="date"
            @change="handleFilter"
            class="px-3 py-2 rounded-xl bg-[#151528] border border-slate-700 text-slate-200 text-xs focus:outline-none focus:border-amber-500"
          />
          <span class="text-slate-400 text-xs">s/d</span>
          <input
            v-model="dateTo"
            type="date"
            @change="handleFilter"
            class="px-3 py-2 rounded-xl bg-[#151528] border border-slate-700 text-slate-200 text-xs focus:outline-none focus:border-amber-500"
          />
        </div>

        <button
          @click="handleFilter"
          class="px-4 py-2 rounded-xl bg-[#25254B] hover:bg-[#323265] text-slate-200 text-xs font-semibold transition-colors"
        >
          Terapkan Filter
        </button>
      </div>

      <!-- Table Section -->
      <div class="rounded-2xl bg-[#1E1E38] border border-slate-800 overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-sm text-slate-300">
            <thead class="bg-[#151528] text-xs uppercase text-slate-400 font-semibold border-b border-slate-800">
              <tr>
                <th class="px-6 py-4">Nomor Faktur</th>
                <th class="px-6 py-4">Tanggal & Waktu</th>
                <th class="px-6 py-4">Pelanggan</th>
                <th class="px-6 py-4 text-center">Gramasi</th>
                <th class="px-6 py-4 text-center">Metode Bayar</th>
                <th class="px-6 py-4 text-right">Total Transaksi</th>
                <th class="px-6 py-4 text-center">Kasir</th>
                <th class="px-6 py-4 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/80">
              <tr v-if="sales.data.length === 0">
                <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                  Belum ada faktur penjualan tercatat.
                </td>
              </tr>
              <tr
                v-for="sale in sales.data"
                :key="sale.id"
                class="hover:bg-[#25254B]/40 transition-colors"
              >
                <td class="px-6 py-4 font-mono font-bold text-amber-400 text-xs">
                  {{ sale.invoice_number }}
                </td>
                <td class="px-6 py-4 text-xs text-slate-300 font-mono">
                  {{ formatDate(sale.created_at) }}
                </td>
                <td class="px-6 py-4">
                  <div class="font-semibold text-white text-xs">{{ sale.customer_name || 'Pelanggan Umum' }}</div>
                  <div class="text-[11px] text-slate-400 font-mono">{{ sale.customer_phone || '-' }}</div>
                </td>
                <td class="px-6 py-4 text-center font-mono font-medium text-slate-200 text-xs">
                  {{ Number(sale.total_weight || 0).toFixed(2) }} Gr
                </td>
                <td class="px-6 py-4 text-center">
                  <span
                    class="px-2.5 py-1 rounded-full text-[11px] font-semibold uppercase tracking-wider border"
                    :class="[
                      sale.payment_method === 'cash' ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30' :
                      sale.payment_method === 'transfer' ? 'bg-blue-500/20 text-blue-300 border-blue-500/30' :
                      'bg-purple-500/20 text-purple-300 border-purple-500/30'
                    ]"
                  >
                    {{ sale.payment_method }}
                  </span>
                </td>
                <td class="px-6 py-4 text-right font-mono font-bold text-white text-xs">
                  {{ formatRupiah(sale.grand_total) }}
                </td>
                <td class="px-6 py-4 text-center text-xs text-slate-400">
                  {{ sale.cashier ? sale.cashier.name : 'Kasir Toko' }}
                </td>
                <td class="px-6 py-4 text-right">
                  <div class="flex items-center justify-end gap-1.5">
                    <a
                      :href="route('admin.pos.invoice', sale.id)"
                      target="_blank"
                      class="p-2 rounded-lg bg-[#25254B] hover:bg-amber-500/20 hover:text-amber-400 text-slate-300 transition-colors"
                      title="Cetak Nota Struk Kasir"
                    >
                      <Receipt class="w-4 h-4" />
                    </a>
                    <a
                      :href="route('admin.pos.certificate', sale.id)"
                      target="_blank"
                      class="p-2 rounded-lg bg-[#25254B] hover:bg-emerald-500/20 hover:text-emerald-400 text-slate-300 transition-colors"
                      title="Cetak Surat Perhiasan Emas Resmi"
                    >
                      <Printer class="w-4 h-4" />
                    </a>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="sales.links && sales.links.length > 3" class="p-4 border-t border-slate-800 flex items-center justify-between">
          <p class="text-xs text-slate-400">
            Menampilkan {{ sales.from || 0 }} - {{ sales.to || 0 }} dari {{ sales.total || 0 }} faktur
          </p>
          <div class="flex items-center gap-1">
            <button
              v-for="(link, i) in sales.links"
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
