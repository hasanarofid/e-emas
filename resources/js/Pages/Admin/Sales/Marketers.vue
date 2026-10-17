<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { 
  Users, 
  Search, 
  Award, 
  TrendingUp, 
  Coins, 
  DollarSign, 
  Gift, 
  CheckCircle2, 
  Copy, 
  Check, 
  Plus, 
  Briefcase 
} from 'lucide-vue-next';

const props = defineProps({
  marketers: {
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
const copiedCode = ref(null);

const handleSearch = () => {
  router.get(route('admin.sales.marketers'), { search: search.value }, { preserveState: true, replace: true });
};

const copyReferral = (code) => {
  navigator.clipboard.writeText(code);
  copiedCode.value = code;
  setTimeout(() => {
    copiedCode.value = null;
  }, 2000);
};

const formatRupiah = (number) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0,
  }).format(number || 0);
};
</script>

<template>
  <AdminLayout>
    <Head title="Pelanggan Marketer & Agen Emas - POS Emas" />

    <div class="space-y-6">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2 text-xs font-mono text-amber-500 uppercase tracking-widest">
            <Briefcase class="w-4 h-4" />
            <span>Kemitraan & Penjualan Marketer</span>
          </div>
          <h2 class="text-2xl font-bold tracking-tight text-white mt-1">
            Data Pelanggan Marketer & Reseller Emas
          </h2>
          <p class="text-sm text-slate-400">
            Kelola agen mitra, reseller, makelar perhiasan emas, bagi hasil komisi penjualan, dan target gramasi bulanan.
          </p>
        </div>

        <Link
          :href="route('admin.customers.create')"
          class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 font-bold text-sm shadow-lg shadow-amber-500/20 transition-all cursor-pointer"
        >
          <Plus class="w-4 h-4" />
          <span>Daftar Marketer Baru</span>
        </Link>
      </div>

      <!-- Quick Summary Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-4 rounded-2xl bg-[#1E1E38] border border-slate-800 flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-purple-500/10 border border-purple-500/20 flex items-center justify-center text-purple-400 shrink-0">
            <Users class="w-6 h-6" />
          </div>
          <div>
            <p class="text-xs font-medium text-slate-400">Total Marketer & Agen</p>
            <p class="text-xl font-bold text-white mt-0.5">{{ stats.total_marketers || 0 }} Mitra</p>
          </div>
        </div>

        <div class="p-4 rounded-2xl bg-[#1E1E38] border border-slate-800 flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400 shrink-0">
            <Coins class="w-6 h-6" />
          </div>
          <div>
            <p class="text-xs font-medium text-slate-400">Estimasi Komisi Berjalan</p>
            <p class="text-lg font-bold text-amber-400 mt-0.5">{{ formatRupiah(stats.total_commission_paid) }}</p>
          </div>
        </div>

        <div class="p-4 rounded-2xl bg-[#1E1E38] border border-slate-800 flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 shrink-0">
            <Award class="w-6 h-6" />
          </div>
          <div>
            <p class="text-xs font-medium text-slate-400">Marketer Teratas</p>
            <p class="text-sm font-bold text-emerald-400 mt-0.5 truncate max-w-[160px]">
              {{ stats.top_marketer ? stats.top_marketer.name : '-' }}
            </p>
          </div>
        </div>

        <div class="p-4 rounded-2xl bg-[#1E1E38] border border-slate-800 flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400 shrink-0">
            <CheckCircle2 class="w-6 h-6" />
          </div>
          <div>
            <p class="text-xs font-medium text-slate-400">Reseller Aktif</p>
            <p class="text-xl font-bold text-white mt-0.5">{{ stats.active_resellers || 0 }} Orang</p>
          </div>
        </div>
      </div>

      <!-- Table Section -->
      <div class="rounded-2xl bg-[#1E1E38] border border-slate-800 overflow-hidden shadow-xl">
        <!-- Search Bar -->
        <div class="p-4 border-b border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
          <div class="relative flex-1 max-w-md">
            <Search class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" />
            <input
              v-model="search"
              @keyup.enter="handleSearch"
              type="text"
              placeholder="Cari kode, nama marketer, no HP, kota..."
              class="w-full pl-10 pr-4 py-2 rounded-xl bg-[#151528] border border-slate-700 text-white placeholder-slate-400 text-sm focus:outline-none focus:border-amber-500"
            />
          </div>
          <button
            @click="handleSearch"
            class="px-4 py-2 rounded-xl bg-[#25254B] hover:bg-[#323265] text-slate-200 text-sm font-medium transition-colors cursor-pointer"
          >
            Filter
          </button>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
          <table class="w-full text-left text-sm text-slate-300">
            <thead class="bg-[#151528] text-xs uppercase text-slate-400 font-semibold border-b border-slate-800">
              <tr>
                <th class="px-6 py-4">Kode & Nama Marketer</th>
                <th class="px-6 py-4">Kontak / Kota</th>
                <th class="px-6 py-4 text-center">Kode Referral</th>
                <th class="px-6 py-4 text-center">Realisasi Target</th>
                <th class="px-6 py-4 text-right">Total Omset Penjualan</th>
                <th class="px-6 py-4 text-right">Bagi Hasil / Komisi</th>
                <th class="px-6 py-4 text-center">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/80">
              <tr v-if="marketers.data.length === 0">
                <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                  Belum ada data pelanggan marketer.
                </td>
              </tr>
              <tr
                v-for="marketer in marketers.data"
                :key="marketer.id"
                class="hover:bg-[#25254B]/40 transition-colors"
              >
                <td class="px-6 py-4">
                  <div class="font-bold text-white text-xs">{{ marketer.name }}</div>
                  <div class="text-[11px] font-mono text-amber-400">{{ marketer.code }}</div>
                </td>
                <td class="px-6 py-4 text-xs text-slate-300">
                  <div class="font-mono text-slate-200">{{ marketer.phone }}</div>
                  <div class="text-slate-400 text-[11px]">{{ marketer.city || '-' }}</div>
                </td>
                <td class="px-6 py-4 text-center">
                  <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-[#151528] border border-slate-700 font-mono text-xs font-bold text-amber-300">
                    <span>{{ marketer.referral_code }}</span>
                    <button
                      @click="copyReferral(marketer.referral_code)"
                      class="hover:text-white transition-colors"
                      title="Salin kode referral"
                    >
                      <Check v-if="copiedCode === marketer.referral_code" class="w-3.5 h-3.5 text-emerald-400" />
                      <Copy v-else class="w-3.5 h-3.5 text-slate-400" />
                    </button>
                  </div>
                </td>
                <td class="px-6 py-4 text-center">
                  <div class="w-32 mx-auto">
                    <div class="flex items-center justify-between text-[11px] mb-1">
                      <span class="text-slate-300 font-mono">{{ marketer.achieved_gram }} Gr</span>
                      <span class="text-amber-400 font-bold">{{ marketer.achievement_pct }}%</span>
                    </div>
                    <div class="w-full h-1.5 bg-slate-700 rounded-full overflow-hidden">
                      <div
                        class="h-full bg-gradient-to-r from-amber-500 to-amber-400 rounded-full"
                        :style="{ width: `${marketer.achievement_pct}%` }"
                      ></div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 text-right font-mono font-bold text-slate-200 text-xs">
                  {{ formatRupiah(marketer.total_purchased_amount) }}
                </td>
                <td class="px-6 py-4 text-right font-mono font-bold text-emerald-400 text-xs">
                  {{ formatRupiah(marketer.total_commission) }}
                  <span class="text-[10px] text-slate-400 block font-normal">Rate: {{ marketer.commission_rate }}%</span>
                </td>
                <td class="px-6 py-4 text-center">
                  <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                    Aktif
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="marketers.links && marketers.links.length > 3" class="p-4 border-t border-slate-800 flex items-center justify-between">
          <p class="text-xs text-slate-400">
            Menampilkan {{ marketers.from || 0 }} - {{ marketers.to || 0 }} dari {{ marketers.total || 0 }} marketer
          </p>
          <div class="flex items-center gap-1">
            <button
              v-for="(link, i) in marketers.links"
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
