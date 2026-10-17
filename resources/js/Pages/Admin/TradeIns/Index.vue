<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
  Repeat, Plus, Search, Calendar, Scale, 
  Coins, CheckCircle2, ArrowRightLeft, FileText, Printer,
  Filter, ArrowUpRight, ArrowDownRight
} from 'lucide-vue-next';

const props = defineProps({
  tradeIns: Object,
  filters: Object,
  stats: Object,
});

const search = ref(props.filters.search || '');
const paymentMethod = ref(props.filters.payment_method || '');

const handleFilter = () => {
  router.get(route('admin.trade-ins.index'), {
    search: search.value,
    payment_method: paymentMethod.value,
  }, { preserveState: true, replace: true });
};

const formatRupiah = (val) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val || 0);
};

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
    <Head title="Data Tukar Tambah Emas" />

    <div class="space-y-6">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2 text-xs font-mono text-amber-500 uppercase tracking-widest">
            <Repeat class="w-4 h-4" />
            <span>Manajemen Transaksi Emas</span>
          </div>
          <h2 class="text-2xl font-bold tracking-tight text-white mt-1">
            Data Tukar Tambah Emas
          </h2>
          <p class="text-sm text-slate-400">
            Daftar seluruh transaksi tukar tambah perhiasan lama pelanggan dengan perhiasan baru toko.
          </p>
        </div>

        <div class="flex items-center gap-3">
          <Link
            :href="route('admin.trade-ins.create')"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-semibold text-sm shadow-lg shadow-amber-500/20 transition-all cursor-pointer"
          >
            <Plus class="w-4 h-4" />
            <span>Tukar Tambah Baru</span>
          </Link>
        </div>
      </div>

      <!-- KPI Summary Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-[#1C1C3E] border border-[#2D2D54] rounded-2xl p-4 flex items-center justify-between">
          <div>
            <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Total Transaksi</p>
            <h3 class="text-2xl font-bold text-white mt-1">{{ stats.total_transactions }}</h3>
            <span class="text-xs text-slate-400 mt-1">Nota tukar tambah tercatat</span>
          </div>
          <div class="w-12 h-12 rounded-xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center">
            <Repeat class="w-6 h-6" />
          </div>
        </div>

        <div class="bg-[#1C1C3E] border border-[#2D2D54] rounded-2xl p-4 flex items-center justify-between">
          <div>
            <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Gram Emas Masuk</p>
            <h3 class="text-2xl font-bold text-emerald-400 mt-1">{{ formatWeight(stats.total_old_weight) }}</h3>
            <span class="text-xs text-slate-400 mt-1">Emas lama diterima toko</span>
          </div>
          <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center">
            <ArrowDownRight class="w-6 h-6" />
          </div>
        </div>

        <div class="bg-[#1C1C3E] border border-[#2D2D54] rounded-2xl p-4 flex items-center justify-between">
          <div>
            <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Gram Emas Keluar</p>
            <h3 class="text-2xl font-bold text-amber-400 mt-1">{{ formatWeight(stats.total_new_weight) }}</h3>
            <span class="text-xs text-slate-400 mt-1">Emas baru diserahkan</span>
          </div>
          <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center">
            <ArrowUpRight class="w-6 h-6" />
          </div>
        </div>

        <div class="bg-[#1C1C3E] border border-[#2D2D54] rounded-2xl p-4 flex items-center justify-between">
          <div>
            <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Kas Masuk (Selisih)</p>
            <h3 class="text-xl font-bold text-cyan-400 mt-1">{{ formatRupiah(stats.total_difference_cash_in) }}</h3>
            <span class="text-xs text-slate-400 mt-1">Total selisih bayar diterima</span>
          </div>
          <div class="w-12 h-12 rounded-xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 flex items-center justify-center">
            <Coins class="w-6 h-6" />
          </div>
        </div>
      </div>

      <!-- Filters & Search -->
      <div class="bg-[#1C1C3E] border border-[#2D2D54] rounded-2xl p-4">
        <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
          <div class="flex-1 w-full relative">
            <Search class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" />
            <input
              v-model="search"
              @keyup.enter="handleFilter"
              type="text"
              placeholder="Cari no nota TT, nama pelanggan, atau nama perhiasan..."
              class="w-full pl-10 pr-4 py-2 bg-[#15152D] border border-[#2D2D54] rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:border-amber-400"
            />
          </div>

          <div class="flex items-center gap-3 w-full md:w-auto">
            <select
              v-model="paymentMethod"
              @change="handleFilter"
              class="bg-[#15152D] border border-[#2D2D54] rounded-xl px-3 py-2 text-sm text-slate-300 focus:outline-none focus:border-amber-400"
            >
              <option value="">Semua Metode Pembayaran</option>
              <option value="cash">Tunai (Cash)</option>
              <option value="transfer">Transfer Bank</option>
              <option value="qris">QRIS Toko</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Data Table -->
      <div class="bg-[#1C1C3E] border border-[#2D2D54] rounded-2xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-sm text-slate-300">
            <thead class="bg-[#15152D] text-xs uppercase font-mono tracking-wider text-slate-400 border-b border-[#2D2D54]">
              <tr>
                <th class="px-5 py-3.5">Nota & Tanggal</th>
                <th class="px-5 py-3.5">Pelanggan</th>
                <th class="px-5 py-3.5">Emas Lama (Diterima)</th>
                <th class="px-5 py-3.5">Emas Baru (Dibeli)</th>
                <th class="px-5 py-3.5 text-right">Selisih Bayar</th>
                <th class="px-5 py-3.5 text-center">Metode</th>
                <th class="px-5 py-3.5 text-center">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-[#2D2D54]/50">
              <tr v-for="item in tradeIns.data" :key="item.id" class="hover:bg-[#24244D]/50 transition-colors">
                <td class="px-5 py-4">
                  <div class="font-bold text-white font-mono text-sm">{{ item.trade_in_number }}</div>
                  <div class="text-xs text-slate-400 mt-0.5">{{ formatDate(item.transaction_date) }}</div>
                </td>
                <td class="px-5 py-4">
                  <div class="font-medium text-slate-200">{{ item.customer_name }}</div>
                  <div class="text-xs text-slate-400">{{ item.customer_phone || '-' }}</div>
                </td>
                <td class="px-5 py-4">
                  <div class="text-slate-200 font-medium">{{ item.old_item_name }}</div>
                  <div class="text-xs text-emerald-400 font-mono">
                    {{ formatWeight(item.old_weight) }} • Nilai Taksir: {{ formatRupiah(item.old_estimated_price) }}
                  </div>
                </td>
                <td class="px-5 py-4">
                  <div class="text-slate-200 font-medium">{{ item.new_item_name }}</div>
                  <div class="text-xs text-amber-400 font-mono">
                    {{ formatWeight(item.new_weight) }} • Harga: {{ formatRupiah(item.new_price) }}
                  </div>
                </td>
                <td class="px-5 py-4 text-right">
                  <span class="font-bold text-white font-mono">
                    {{ formatRupiah(item.difference_amount) }}
                  </span>
                  <div class="text-[11px] text-slate-400">Bayar: {{ formatRupiah(item.amount_paid) }}</div>
                </td>
                <td class="px-5 py-4 text-center">
                  <span 
                    :class="[
                      item.payment_method === 'cash' ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' :
                      item.payment_method === 'transfer' ? 'bg-blue-500/10 text-blue-400 border-blue-500/20' :
                      'bg-purple-500/10 text-purple-400 border-purple-500/20',
                      'px-2.5 py-1 rounded-full text-xs font-semibold uppercase border'
                    ]"
                  >
                    {{ item.payment_method }}
                  </span>
                </td>
                <td class="px-5 py-4 text-center">
                  <button 
                    @click="window.print()"
                    class="p-2 text-slate-400 hover:text-amber-400 hover:bg-[#323265] rounded-lg transition-colors cursor-pointer"
                    title="Cetak Nota Tukar Tambah"
                  >
                    <Printer class="w-4 h-4" />
                  </button>
                </td>
              </tr>
              <tr v-if="!tradeIns.data || tradeIns.data.length === 0">
                <td colspan="7" class="px-5 py-10 text-center text-slate-400">
                  <Repeat class="w-10 h-10 mx-auto text-slate-600 mb-2" />
                  <p>Belum ada data transaksi tukar tambah.</p>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="tradeIns.links && tradeIns.links.length > 3" class="px-5 py-3 border-t border-[#2D2D54] bg-[#15152D] flex items-center justify-between">
          <div class="text-xs text-slate-400">
            Menampilkan {{ tradeIns.from || 0 }} - {{ tradeIns.to || 0 }} dari {{ tradeIns.total || 0 }} data
          </div>
          <div class="flex items-center gap-1">
            <Link
              v-for="(link, i) in tradeIns.links"
              :key="i"
              :href="link.url || '#'"
              v-html="link.label"
              :class="[
                link.active ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-400 hover:bg-[#25254B] hover:text-white',
                !link.url ? 'opacity-40 pointer-events-none' : '',
                'px-3 py-1 rounded-lg text-xs transition-colors'
              ]"
            />
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
