<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
  ArrowDownLeft, Plus, Search, Calendar, CreditCard, 
  Trash2, Tag, Building2, Wallet, FileText, CheckCircle2
} from 'lucide-vue-next';

const props = defineProps({
  expenses: Object,
  categories: Array,
  filters: Object,
  stats: Object,
});

const categoryFilter = ref(props.filters.category || '');
const startDate = ref(props.filters.start_date || '');
const endDate = ref(props.filters.end_date || '');
const currentPeriod = ref(props.filters.period || '');

const handleFilter = () => {
  router.get(route('admin.expenses.index'), {
    category: categoryFilter.value,
    start_date: startDate.value,
    end_date: endDate.value,
    period: currentPeriod.value,
  }, { preserveState: true, replace: true });
};

const setPeriod = (period) => {
  currentPeriod.value = period;
  handleFilter();
};

const deleteExpense = (exp) => {
  if (confirm(`Apakah Anda yakin ingin menghapus catatan pengeluaran "${exp.title}" (${exp.expense_number})? Transaksi buku kas terkait juga akan dihapus (Soft Delete).`)) {
    router.delete(route('admin.expenses.destroy', exp.id));
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
  <AdminLayout title="Pengeluaran Operasional Toko Emas">
    <Head title="Pengeluaran Operasional - POS Emas" />

    <div class="space-y-6">
      <!-- Header -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <h1 class="text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-2.5">
            <ArrowDownLeft class="w-6 h-6 text-rose-500" />
            <span>Pengeluaran & Biaya Operasional Toko</span>
          </h1>
          <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
            Data biaya listrik, kotak perhiasan, snack, perlengkapan tukang lebur/poles emas, dan operasional.
          </p>
        </div>

        <div class="flex items-center gap-2.5">
          <Link
            :href="route('admin.cash-book.index')"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 text-sm font-semibold transition-all shadow-sm"
          >
            <CreditCard class="w-4 h-4 text-blue-500" />
            <span>Buku Kas Toko</span>
          </Link>
          <Link
            :href="route('admin.expenses.create')"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-sm shadow-md shadow-amber-500/20 transition-all cursor-pointer"
          >
            <Plus class="w-4 h-4" />
            <span>Tambah Pengeluaran</span>
          </Link>
        </div>
      </div>

      <!-- KPI Summary Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between">
          <div>
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Beban Kumulatif</p>
            <p class="text-2xl font-bold font-mono text-rose-600 dark:text-rose-400 mt-1">{{ formatRupiah(stats.total_expenses) }}</p>
            <p class="text-xs text-slate-400 mt-0.5">Seluruh arsip pengeluaran</p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-rose-500/10 text-rose-500 flex items-center justify-center">
            <ArrowDownLeft class="w-6 h-6" />
          </div>
        </div>

        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between">
          <div>
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Pengeluaran Hari Ini</p>
            <p class="text-2xl font-bold font-mono text-amber-500 mt-1">{{ formatRupiah(stats.today_expenses) }}</p>
            <p class="text-xs text-slate-400 mt-0.5">Biaya harian toko</p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-500 flex items-center justify-center">
            <Calendar class="w-6 h-6" />
          </div>
        </div>

        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between">
          <div>
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Pengeluaran Bulan Ini</p>
            <p class="text-2xl font-bold font-mono text-blue-500 mt-1">{{ formatRupiah(stats.month_expenses) }}</p>
            <p class="text-xs text-slate-400 mt-0.5">Periode bulan berjalan</p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-blue-500/10 text-blue-500 flex items-center justify-center">
            <Tag class="w-6 h-6" />
          </div>
        </div>
      </div>

      <!-- Quick Period Tabs & Category Filter -->
      <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col md:flex-row gap-3 items-center justify-between">
        <!-- Quick Period Switcher -->
        <div class="flex items-center gap-1.5 p-1 rounded-xl bg-slate-100 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700">
          <button
            type="button"
            @click="setPeriod('')"
            :class="[
              !currentPeriod ? 'bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-sm font-bold' : 'text-slate-600 dark:text-slate-400',
              'px-3 py-1.5 rounded-lg text-xs transition-all'
            ]"
          >
            Semua Periode
          </button>
          <button
            type="button"
            @click="setPeriod('daily')"
            :class="[
              currentPeriod === 'daily' ? 'bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-sm font-bold' : 'text-slate-600 dark:text-slate-400',
              'px-3 py-1.5 rounded-lg text-xs transition-all'
            ]"
          >
            Pengeluaran Harian
          </button>
          <button
            type="button"
            @click="setPeriod('monthly')"
            :class="[
              currentPeriod === 'monthly' ? 'bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-sm font-bold' : 'text-slate-600 dark:text-slate-400',
              'px-3 py-1.5 rounded-lg text-xs transition-all'
            ]"
          >
            Pengeluaran Bulanan
          </button>
        </div>

        <div class="flex items-center gap-3 w-full md:w-auto">
          <select
            v-model="categoryFilter"
            @change="handleFilter"
            class="px-3 py-1.5 text-xs rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500/30"
          >
            <option value="">Semua Kategori</option>
            <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
          </select>
        </div>
      </div>

      <!-- Expense Table -->
      <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse text-sm">
            <thead>
              <tr class="bg-slate-50/75 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800 text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                <th class="py-3.5 px-4 font-semibold">Tgl & No. Bukti</th>
                <th class="py-3.5 px-4 font-semibold">Kategori & Keperluan</th>
                <th class="py-3.5 px-4 font-semibold">Penerima Dana</th>
                <th class="py-3.5 px-4 font-semibold text-right">Nominal Beban</th>
                <th class="py-3.5 px-4 font-semibold text-center">Metode Bayar</th>
                <th class="py-3.5 px-4 font-semibold text-center">Petugas</th>
                <th class="py-3.5 px-4 font-semibold text-center">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
              <tr 
                v-for="exp in expenses.data" 
                :key="exp.id"
                class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors"
              >
                <!-- Tgl & No Bukti -->
                <td class="py-3.5 px-4">
                  <span class="font-mono text-xs font-bold text-amber-500">{{ exp.expense_number }}</span>
                  <p class="text-xs text-slate-400 mt-0.5">{{ formatDate(exp.expense_date) }}</p>
                </td>

                <!-- Kategori & Keperluan -->
                <td class="py-3.5 px-4">
                  <span class="inline-block px-2 py-0.5 rounded text-[10px] font-semibold bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20 mb-1">
                    {{ exp.category }}
                  </span>
                  <div class="font-bold text-slate-900 dark:text-white">{{ exp.title }}</div>
                </td>

                <!-- Penerima Dana -->
                <td class="py-3.5 px-4 text-xs text-slate-600 dark:text-slate-300">
                  {{ exp.recipient || '-' }}
                </td>

                <!-- Nominal Beban -->
                <td class="py-3.5 px-4 text-right">
                  <span class="font-bold font-mono text-rose-600 dark:text-rose-400">
                    {{ formatRupiah(exp.amount) }}
                  </span>
                </td>

                <!-- Metode Bayar -->
                <td class="py-3.5 px-4 text-center text-xs">
                  <span 
                    :class="[
                      exp.payment_method === 'tunai' ? 'bg-amber-500/10 text-amber-600 dark:text-amber-400' : 'bg-blue-500/10 text-blue-600 dark:text-blue-400',
                      'px-2 py-0.5 rounded font-mono uppercase text-[11px] font-bold'
                    ]"
                  >
                    {{ exp.payment_method }}
                  </span>
                  <p v-if="exp.bank_account" class="text-[10px] text-slate-400 mt-0.5">{{ exp.bank_account.bank_name }}</p>
                </td>

                <!-- Petugas -->
                <td class="py-3.5 px-4 text-center text-xs text-slate-500">
                  {{ exp.creator?.name || 'Kasir' }}
                </td>

                <!-- Aksi -->
                <td class="py-3.5 px-4 text-center">
                  <button
                    @click="deleteExpense(exp)"
                    title="Hapus Pengeluaran"
                    class="p-1.5 rounded-lg text-rose-500 hover:bg-rose-500/10 transition-colors"
                  >
                    <Trash2 class="w-4 h-4" />
                  </button>
                </td>
              </tr>

              <tr v-if="expenses.data.length === 0">
                <td colspan="7" class="py-12 text-center text-slate-400">
                  <ArrowDownLeft class="w-12 h-12 mx-auto text-slate-300 dark:text-slate-700 mb-2" />
                  <p class="font-semibold">Belum ada pengeluaran pada periode ini</p>
                  <p class="text-xs mt-1">Gunakan tombol "Tambah Pengeluaran" untuk mencatat pengeluaran operasional.</p>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="expenses.links && expenses.links.length > 3" class="p-4 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between">
          <p class="text-xs text-slate-500">
            Menampilkan {{ expenses.from }} - {{ expenses.to }} dari {{ expenses.total }} data
          </p>
          <div class="flex gap-1">
            <template v-for="(link, i) in expenses.links" :key="i">
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
