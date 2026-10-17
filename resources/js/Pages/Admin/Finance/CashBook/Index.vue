<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
  Calculator, Plus, Search, Calendar, CreditCard, 
  ArrowDownLeft, ArrowUpRight, Wallet, Building2, 
  Trash2, Filter, DollarSign
} from 'lucide-vue-next';

const props = defineProps({
  transactions: Object,
  bankAccounts: Array,
  filters: Object,
  stats: Object,
});

const startDate = ref(props.filters.start_date || '');
const endDate = ref(props.filters.end_date || '');
const typeFilter = ref(props.filters.type || '');
const accountFilter = ref(props.filters.bank_account_id || '');

const handleFilter = () => {
  router.get(route('admin.cash-book.index'), {
    start_date: startDate.value,
    end_date: endDate.value,
    type: typeFilter.value,
    bank_account_id: accountFilter.value,
  }, { preserveState: true, replace: true });
};

const deleteTransaction = (trx) => {
  if (confirm(`Apakah Anda yakin ingin menghapus catatan kas "${trx.transaction_number}"? Data akan diarsipkan aman (Soft Delete).`)) {
    router.delete(route('admin.cash-book.destroy', trx.id));
  }
};

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
  <AdminLayout title="Buku Kas Toko Emas">
    <Head title="Buku Kas & Saldo - POS Emas" />

    <div class="space-y-6">
      <!-- Header -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <h1 class="text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-2.5">
            <Calculator class="w-6 h-6 text-amber-500" />
            <span>Buku Kas Toko & Mutasi Saldo</span>
          </h1>
          <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
            Pencatatan arus kas masuk & keluar, rekap saldo laci kasir tunai, dan rekening operasional toko emas.
          </p>
        </div>

        <div class="flex items-center gap-2.5">
          <Link
            :href="route('admin.expenses.index')"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 text-sm font-semibold transition-all shadow-sm"
          >
            <ArrowDownLeft class="w-4 h-4 text-rose-500" />
            <span>Data Pengeluaran</span>
          </Link>
          <Link
            :href="route('admin.cash-book.create')"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-sm shadow-md shadow-amber-500/20 transition-all cursor-pointer"
          >
            <Plus class="w-4 h-4" />
            <span>Tambah Arus Kas</span>
          </Link>
        </div>
      </div>

      <!-- KPI Summary Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
          <div class="flex items-center justify-between">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Kas Masuk</span>
            <div class="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center">
              <ArrowDownLeft class="w-4 h-4" />
            </div>
          </div>
          <p class="text-xl font-bold font-mono text-emerald-600 dark:text-emerald-400 mt-2">{{ formatRupiah(stats.total_in) }}</p>
          <p class="text-xs text-slate-400 mt-0.5">Penjualan, modal, dll</p>
        </div>

        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
          <div class="flex items-center justify-between">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Kas Keluar</span>
            <div class="w-9 h-9 rounded-xl bg-rose-500/10 text-rose-500 flex items-center justify-center">
              <ArrowUpRight class="w-4 h-4" />
            </div>
          </div>
          <p class="text-xl font-bold font-mono text-rose-600 dark:text-rose-400 mt-2">{{ formatRupiah(stats.total_out) }}</p>
          <p class="text-xs text-slate-400 mt-0.5">Buyback, beban, gaji</p>
        </div>

        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
          <div class="flex items-center justify-between">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Saldo Laci Kasir (Tunai)</span>
            <div class="w-9 h-9 rounded-xl bg-amber-500/10 text-amber-500 flex items-center justify-center">
              <Wallet class="w-4 h-4" />
            </div>
          </div>
          <p class="text-xl font-bold font-mono text-amber-500 mt-2">{{ formatRupiah(stats.cash_drawer_balance) }}</p>
          <p class="text-xs text-slate-400 mt-0.5">Fisik kas tunai di toko</p>
        </div>

        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
          <div class="flex items-center justify-between">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Saldo Total Berjalan</span>
            <div class="w-9 h-9 rounded-xl bg-blue-500/10 text-blue-500 flex items-center justify-center">
              <Building2 class="w-4 h-4" />
            </div>
          </div>
          <p class="text-xl font-bold font-mono text-slate-900 dark:text-white mt-2">{{ formatRupiah(stats.current_balance) }}</p>
          <p class="text-xs text-slate-400 mt-0.5">Tunai + Bank operasional</p>
        </div>
      </div>

      <!-- Filters & Period Selectors -->
      <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col md:flex-row gap-3 items-center justify-between">
        <div class="flex flex-wrap items-center gap-3 w-full">
          <div class="flex items-center gap-2 text-xs text-slate-500 font-semibold">
            <Filter class="w-3.5 h-3.5 text-amber-500" />
            <span>Filter:</span>
          </div>

          <input
            v-model="startDate"
            @change="handleFilter"
            type="date"
            class="px-3 py-1.5 text-xs rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500/30"
          />
          <span class="text-xs text-slate-400">s/d</span>
          <input
            v-model="endDate"
            @change="handleFilter"
            type="date"
            class="px-3 py-1.5 text-xs rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500/30"
          />

          <select
            v-model="typeFilter"
            @change="handleFilter"
            class="px-3 py-1.5 text-xs rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500/30"
          >
            <option value="">Semua Arus Kas</option>
            <option value="in">Kas Masuk (Inflow)</option>
            <option value="out">Kas Keluar (Outflow)</option>
          </select>

          <select
            v-model="accountFilter"
            @change="handleFilter"
            class="px-3 py-1.5 text-xs rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500/30"
          >
            <option value="">Semua Akun Kas</option>
            <option value="cash">💵 Kasir Tunai Toko</option>
            <option v-for="b in bankAccounts" :key="b.id" :value="b.id">
              🏦 {{ b.bank_name }} ({{ b.account_number }})
            </option>
          </select>
        </div>
      </div>

      <!-- Cash Transactions Table -->
      <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse text-sm">
            <thead>
              <tr class="bg-slate-50/75 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800 text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                <th class="py-3.5 px-4 font-semibold">Tgl & No. Mutasi</th>
                <th class="py-3.5 px-4 font-semibold">Deskripsi & Kategori</th>
                <th class="py-3.5 px-4 font-semibold">Sumber Akun Kas</th>
                <th class="py-3.5 px-4 font-semibold text-right">Kas Masuk (IN)</th>
                <th class="py-3.5 px-4 font-semibold text-right">Kas Keluar (OUT)</th>
                <th class="py-3.5 px-4 font-semibold text-center">Petugas</th>
                <th class="py-3.5 px-4 font-semibold text-center">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
              <tr 
                v-for="trx in transactions.data" 
                :key="trx.id"
                class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors"
              >
                <!-- Tgl & No Mutasi -->
                <td class="py-3.5 px-4">
                  <span class="font-mono text-xs font-bold text-amber-500">{{ trx.transaction_number }}</span>
                  <p class="text-xs text-slate-400 mt-0.5">{{ formatDate(trx.transaction_date) }}</p>
                </td>

                <!-- Deskripsi & Kategori -->
                <td class="py-3.5 px-4">
                  <div class="font-bold text-slate-900 dark:text-white">{{ trx.description }}</div>
                  <span class="inline-block px-1.5 py-0.5 rounded text-[10px] uppercase font-mono bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 mt-0.5">
                    {{ trx.category.replace('_', ' ') }}
                  </span>
                </td>

                <!-- Akun Kas -->
                <td class="py-3.5 px-4 text-xs">
                  <span v-if="trx.bank_account" class="inline-flex items-center gap-1 font-semibold text-blue-600 dark:text-blue-400">
                    <Building2 class="w-3.5 h-3.5" />
                    {{ trx.bank_account.bank_name }}
                  </span>
                  <span v-else class="inline-flex items-center gap-1 font-semibold text-amber-600 dark:text-amber-400">
                    <Wallet class="w-3.5 h-3.5" />
                    Kasir Tunai
                  </span>
                </td>

                <!-- Kas Masuk -->
                <td class="py-3.5 px-4 text-right">
                  <span v-if="trx.type === 'in'" class="font-bold font-mono text-emerald-600 dark:text-emerald-400">
                    +{{ formatRupiah(trx.amount) }}
                  </span>
                  <span v-else class="text-slate-300 dark:text-slate-700">-</span>
                </td>

                <!-- Kas Keluar -->
                <td class="py-3.5 px-4 text-right">
                  <span v-if="trx.type === 'out'" class="font-bold font-mono text-rose-600 dark:text-rose-400">
                    -{{ formatRupiah(trx.amount) }}
                  </span>
                  <span v-else class="text-slate-300 dark:text-slate-700">-</span>
                </td>

                <!-- Petugas -->
                <td class="py-3.5 px-4 text-center text-xs text-slate-500">
                  {{ trx.creator?.name || 'Kasir' }}
                </td>

                <!-- Aksi -->
                <td class="py-3.5 px-4 text-center">
                  <button
                    @click="deleteTransaction(trx)"
                    title="Hapus / Arsipkan Transaksi"
                    class="p-1.5 rounded-lg text-rose-500 hover:bg-rose-500/10 transition-colors"
                  >
                    <Trash2 class="w-4 h-4" />
                  </button>
                </td>
              </tr>

              <tr v-if="transactions.data.length === 0">
                <td colspan="7" class="py-12 text-center text-slate-400">
                  <Calculator class="w-12 h-12 mx-auto text-slate-300 dark:text-slate-700 mb-2" />
                  <p class="font-semibold">Belum ada transaksi pada periode ini</p>
                  <p class="text-xs mt-1">Gunakan tombol "Tambah Arus Kas" untuk mencatat mutasi baru.</p>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="transactions.links && transactions.links.length > 3" class="p-4 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between">
          <p class="text-xs text-slate-500">
            Menampilkan {{ transactions.from }} - {{ transactions.to }} dari {{ transactions.total }} data
          </p>
          <div class="flex gap-1">
            <template v-for="(link, i) in transactions.links" :key="i">
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
