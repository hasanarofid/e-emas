<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { 
  ArrowRightLeft, 
  Search, 
  Plus, 
  Scale, 
  Banknote, 
  Receipt, 
  Calendar, 
  User, 
  Flame, 
  Gem,
  Printer,
  ChevronRight
} from 'lucide-vue-next';

const props = defineProps({
  buybacks: {
    type: Object,
    required: true
  },
  stats: {
    type: Object,
    required: true
  },
  filters: {
    type: Object,
    default: () => ({})
  }
});

const search = ref(props.filters.search || '');

let searchDebounce = null;
watch(search, (newVal) => {
  clearTimeout(searchDebounce);
  searchDebounce = setTimeout(() => {
    router.get(route('admin.buybacks.index'), { search: newVal }, {
      preserveState: true,
      replace: true
    });
  }, 300);
});

const formatRupiah = (val) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0
  }).format(val || 0);
};

const formatWeight = (val) => {
  return parseFloat(val || 0).toFixed(3) + ' gr';
};
</script>

<template>
  <Head title="Manajemen Buyback & Beli Kembali Emas - e-Emas" />

  <AdminLayout>
    <div class="space-y-6">
      
      <!-- Top Title & Action Button -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200 dark:border-slate-800">
        <div>
          <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-700 dark:text-amber-300 text-xs font-semibold mb-1">
            <ArrowRightLeft class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400" />
            <span>Operasional Tukar Tambah & Beli Kembali</span>
          </div>
          <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">
            Pembelian Kembali Emas (Buyback)
          </h1>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
            Kelola penerimaan emas lama dari pelanggan, verifikasi susut gramasi nota asli, dan kontrol arus kas keluar.
          </p>
        </div>

        <Link 
          :href="route('admin.buybacks.create')"
          class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-bold text-xs uppercase tracking-wider flex items-center justify-center gap-2 shadow-lg shadow-amber-500/20 transition-all cursor-pointer"
        >
          <Plus class="w-4 h-4" /> Catat Buyback Baru
        </Link>
      </div>

      <!-- Aggregated Metric Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Total Gramasi Dibeli -->
        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
            <Scale class="w-6 h-6" />
          </div>
          <div>
            <span class="text-xs text-slate-500 dark:text-slate-400 block">Total Gramasi Masuk</span>
            <span class="text-xl font-extrabold text-slate-900 dark:text-white font-mono">
              {{ formatWeight(stats.total_weight_bought) }}
            </span>
          </div>
        </div>

        <!-- Card 2: Total Kas Keluar -->
        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
            <Banknote class="w-6 h-6" />
          </div>
          <div>
            <span class="text-xs text-slate-500 dark:text-slate-400 block">Total Pengeluaran Kas</span>
            <span class="text-xl font-extrabold text-rose-600 dark:text-rose-400 font-mono">
              {{ formatRupiah(stats.total_cash_out) }}
            </span>
          </div>
        </div>

        <!-- Card 3: Transaksi Hari Ini -->
        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
            <Calendar class="w-6 h-6" />
          </div>
          <div>
            <span class="text-xs text-slate-500 dark:text-slate-400 block">Kas Keluar Hari Ini</span>
            <span class="text-xl font-extrabold text-slate-900 dark:text-white font-mono">
              {{ formatRupiah(stats.today_cash_out) }}
            </span>
            <span class="text-[10px] text-slate-400 block mt-0.5">({{ stats.today_transactions }} transaksi)</span>
          </div>
        </div>

        <!-- Card 4: Total Rekap -->
        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
            <Receipt class="w-6 h-6" />
          </div>
          <div>
            <span class="text-xs text-slate-500 dark:text-slate-400 block">Total Nota Buyback</span>
            <span class="text-xl font-extrabold text-slate-900 dark:text-white font-mono">
              {{ stats.total_transactions }}
            </span>
          </div>
        </div>
      </div>

      <!-- Filter & Search Toolbar -->
      <div class="flex items-center justify-between gap-4 p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
        <div class="relative w-full max-w-md">
          <Search class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" />
          <input 
            v-model="search"
            type="text" 
            placeholder="Cari No. Buyback, Nama Pelanggan, No. HP, atau No. Nota Lama..."
            class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-xs text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500"
          />
        </div>
      </div>

      <!-- Buyback Data Table -->
      <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs border-collapse">
            <thead>
              <tr class="bg-slate-50 dark:bg-slate-950/60 border-b border-slate-200 dark:border-slate-800 text-slate-500 font-semibold uppercase text-[10px] tracking-wider">
                <th class="p-4">No. Transaksi</th>
                <th class="p-4">Pelanggan</th>
                <th class="p-4">Sumber Emas</th>
                <th class="p-4">Rincian Barang</th>
                <th class="p-4 text-right">Berat Timbang</th>
                <th class="p-4 text-right">Potongan Susut</th>
                <th class="p-4 text-right">Uang Kas Keluar</th>
                <th class="p-4 text-center">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
              <tr 
                v-for="bbk in buybacks.data" 
                :key="bbk.id"
                class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition-colors"
              >
                <!-- Nomor Transaksi & Waktu -->
                <td class="p-4">
                  <span class="font-bold text-slate-900 dark:text-white font-mono block">{{ bbk.buyback_number }}</span>
                  <span class="text-[10px] text-slate-400 font-mono">{{ new Date(bbk.created_at).toLocaleDateString('id-ID') }}</span>
                </td>

                <!-- Pelanggan -->
                <td class="p-4">
                  <p class="font-semibold text-slate-900 dark:text-white">{{ bbk.customer_name }}</p>
                  <p class="text-[10px] text-slate-400 font-mono">{{ bbk.customer_phone || '-' }}</p>
                </td>

                <!-- Sumber Emas (Nota Toko Sendiri vs Emas Luar) -->
                <td class="p-4">
                  <span 
                    v-if="bbk.original_invoice_number"
                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-500/20 text-[10px] font-bold font-mono"
                  >
                    Nota Toko ({{ bbk.original_invoice_number }})
                  </span>
                  <span 
                    v-else
                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-slate-500/10 text-slate-600 dark:text-slate-400 border border-slate-500/20 text-[10px] font-medium"
                  >
                    Emas Luar / Tanpa Nota
                  </span>
                </td>

                <!-- Rincian Barang -->
                <td class="p-4">
                  <div class="space-y-0.5">
                    <p v-for="it in bbk.items" :key="it.id" class="text-xs text-slate-700 dark:text-slate-300">
                      • {{ it.item_name }} <span class="font-bold text-amber-600 dark:text-amber-400 font-mono">({{ it.gold_rate?.code }})</span>
                    </p>
                  </div>
                </td>

                <!-- Berat Timbang -->
                <td class="p-4 text-right font-mono font-bold text-slate-800 dark:text-slate-200">
                  {{ formatWeight(bbk.total_weight_actual) }}
                </td>

                <!-- Potongan -->
                <td class="p-4 text-right font-mono text-rose-500 font-semibold">
                  {{ bbk.total_deduction > 0 ? '-' + formatRupiah(bbk.total_deduction) : 'Rp 0' }}
                </td>

                <!-- Uang Kas Keluar -->
                <td class="p-4 text-right font-mono font-extrabold text-sm text-slate-900 dark:text-white">
                  {{ formatRupiah(bbk.grand_total) }}
                </td>

                <!-- Aksi -->
                <td class="p-4 text-center">
                  <a 
                    :href="route('admin.buybacks.receipt', bbk.id)"
                    target="_blank"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold transition-colors"
                  >
                    <Printer class="w-3.5 h-3.5 text-amber-500" />
                    <span>Kuitansi</span>
                  </a>
                </td>
              </tr>

              <tr v-if="buybacks.data.length === 0">
                <td colspan="8" class="p-12 text-center text-slate-400">
                  <ArrowRightLeft class="w-10 h-10 mx-auto text-slate-300 dark:text-slate-700 mb-2 stroke-[1.5]" />
                  <p class="text-xs font-medium">Belum ada transaksi beli kembali (buyback) yang tercatat.</p>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="buybacks.links && buybacks.links.length > 3" class="p-4 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between text-xs">
          <span class="text-slate-500">
            Menampilkan {{ buybacks.from || 0 }} - {{ buybacks.to || 0 }} dari {{ buybacks.total }} data
          </span>
          <div class="flex items-center gap-1">
            <Link 
              v-for="(link, i) in buybacks.links" 
              :key="i"
              :href="link.url || '#'"
              v-html="link.label"
              :class="[
                link.active ? 'bg-amber-500 text-slate-950 font-bold' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200',
                !link.url ? 'opacity-40 pointer-events-none' : '',
                'px-3 py-1.5 rounded-lg font-medium transition-colors'
              ]"
            />
          </div>
        </div>
      </div>

    </div>
  </AdminLayout>
</template>
