<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { 
  Award, 
  Coins, 
  Gift, 
  Search, 
  CreditCard, 
  Printer, 
  ArrowUpRight, 
  ArrowDownLeft, 
  Clock, 
  CheckCircle2, 
  Sparkles, 
  X, 
  Check, 
  ShieldCheck, 
  Crown, 
  Medal, 
  ChevronRight,
  TrendingUp,
  UserCheck
} from 'lucide-vue-next';

const props = defineProps({
  customers: {
    type: Object,
    required: true,
  },
  pointLogs: {
    type: Array,
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
  topMembers: {
    type: Array,
    default: () => [],
  },
  rewardCatalog: {
    type: Array,
    default: () => [],
  },
});

const search = ref(props.filters.search || '');
const tierFilter = ref(props.filters.tier || '');

const handleFilter = () => {
  router.get(route('admin.customers.points'), {
    search: search.value,
    tier: tierFilter.value,
  }, { preserveState: true, replace: true });
};

// Modal Redeem Hadiah
const isRedeemModalOpen = ref(false);
const selectedCustomer = ref(null);

const redeemForm = useForm({
  points: 50,
  reward_name: '',
  notes: '',
});

const openRedeemModal = (customer, defaultReward = null) => {
  selectedCustomer.value = customer;
  if (defaultReward) {
    redeemForm.reward_name = defaultReward.name;
    redeemForm.points = defaultReward.points_required;
    redeemForm.notes = `Penukaran ${defaultReward.name} (Katalog Poin)`;
  } else {
    redeemForm.reward_name = 'Souvenir Pouch & Kotak Perhiasan Eksklusif';
    redeemForm.points = 50;
    redeemForm.notes = 'Klaim reward poin loyalitas member emas';
  }
  isRedeemModalOpen.value = true;
};

const closeRedeemModal = () => {
  isRedeemModalOpen.value = false;
  selectedCustomer.value = null;
  redeemForm.reset();
};

const submitRedeem = () => {
  if (!selectedCustomer.value) return;
  redeemForm.post(route('admin.customers.redeem-reward', selectedCustomer.value.id), {
    preserveScroll: true,
    onSuccess: () => {
      closeRedeemModal();
    },
  });
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

const getTierBadge = (points) => {
  const pts = Number(points || 0);
  if (pts >= 500) {
    return { name: 'Platinum VIP', color: 'bg-purple-500/20 text-purple-300 border-purple-500/30', icon: Crown };
  } else if (pts >= 200) {
    return { name: 'Gold Member', color: 'bg-amber-500/20 text-amber-300 border-amber-500/30', icon: Medal };
  } else if (pts >= 50) {
    return { name: 'Silver Member', color: 'bg-slate-300/20 text-slate-200 border-slate-400/30', icon: Award };
  } else {
    return { name: 'Bronze Member', color: 'bg-orange-600/20 text-orange-300 border-orange-600/30', icon: ShieldCheck };
  }
};
</script>

<template>
  <AdminLayout>
    <Head title="Point & Member Loyalitas - POS Emas" />

    <div class="space-y-6">
      <!-- Header Banner -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2 text-xs font-mono text-amber-500 uppercase tracking-widest">
            <Coins class="w-4 h-4" />
            <span>Program Loyalitas & Membership</span>
          </div>
          <h2 class="text-2xl font-bold tracking-tight text-white mt-1">
            Point & Member Toko Emas
          </h2>
          <p class="text-sm text-slate-400">
            Apresiasi pelanggan setia toko emas dengan sistem perolehan poin otomatis, kenaikan tier member, dan penukaran souvenir eksklusif.
          </p>
        </div>

        <div class="flex items-center gap-2.5">
          <Link
            :href="route('admin.customers.index')"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#25254B] hover:bg-[#323265] text-slate-200 text-sm font-semibold transition-colors"
          >
            <UserCheck class="w-4 h-4" />
            <span>Data Pelanggan</span>
          </Link>
          <Link
            :href="route('admin.customers.create')"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 text-sm font-bold shadow-lg shadow-amber-500/20 transition-all"
          >
            <CreditCard class="w-4 h-4" />
            <span>Daftar Member Baru</span>
          </Link>
        </div>
      </div>

      <!-- 4 Quick Stats -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-4 rounded-2xl bg-[#1E1E38] border border-slate-800 flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400 shrink-0">
            <Coins class="w-6 h-6" />
          </div>
          <div>
            <p class="text-xs font-medium text-slate-400">Total Poin Aktif</p>
            <p class="text-xl font-bold text-amber-400 mt-0.5">
              {{ (stats.total_active_points || 0).toLocaleString('id-ID') }} <span class="text-xs text-slate-400">Pts</span>
            </p>
          </div>
        </div>

        <div class="p-4 rounded-2xl bg-[#1E1E38] border border-slate-800 flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 shrink-0">
            <Gift class="w-6 h-6" />
          </div>
          <div>
            <p class="text-xs font-medium text-slate-400">Poin Ditukarkan</p>
            <p class="text-xl font-bold text-emerald-400 mt-0.5">
              {{ (stats.total_redeemed_points || 0).toLocaleString('id-ID') }} <span class="text-xs text-slate-400">Pts</span>
            </p>
          </div>
        </div>

        <div class="p-4 rounded-2xl bg-[#1E1E38] border border-slate-800 flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-purple-500/10 border border-purple-500/20 flex items-center justify-center text-purple-400 shrink-0">
            <Crown class="w-6 h-6" />
          </div>
          <div>
            <p class="text-xs font-medium text-slate-400">Member Terdaftar</p>
            <p class="text-xl font-bold text-white mt-0.5">
              {{ stats.total_members || 0 }} <span class="text-xs text-slate-400">Pelanggan</span>
            </p>
          </div>
        </div>

        <div class="p-4 rounded-2xl bg-[#1E1E38] border border-slate-800 flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400 shrink-0">
            <TrendingUp class="w-6 h-6" />
          </div>
          <div>
            <p class="text-xs font-medium text-slate-400">Akumulasi Belanja Member</p>
            <p class="text-base font-bold text-white mt-0.5">
              {{ formatRupiah(stats.total_purchases_amount) }}
            </p>
          </div>
        </div>
      </div>

      <!-- Membership Tiers Overview -->
      <div class="p-5 rounded-2xl bg-[#1E1E38] border border-slate-800">
        <div class="flex items-center justify-between mb-4">
          <div class="flex items-center gap-2">
            <Award class="w-5 h-5 text-amber-400" />
            <h3 class="text-base font-bold text-white">Tingkatan Level Membership Emas</h3>
          </div>
          <span class="text-xs text-slate-400">Otomatis ter-upgrade berdasarkan saldo poin belanja</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <!-- Bronze -->
          <div class="p-4 rounded-xl bg-[#151528] border border-orange-500/20 hover:border-orange-500/40 transition-all">
            <div class="flex items-center justify-between">
              <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-orange-500/20 text-orange-300 border border-orange-500/30">
                0 - 49 PTS
              </span>
              <ShieldCheck class="w-5 h-5 text-orange-400" />
            </div>
            <h4 class="text-sm font-bold text-white mt-2">Bronze Member</h4>
            <p class="text-xs text-slate-400 mt-1">Tier awal untuk setiap pelanggan baru.</p>
            <ul class="text-[11px] text-slate-300 mt-3 space-y-1">
              <li class="flex items-center gap-1.5">
                <Check class="w-3 h-3 text-emerald-400" /> Diskon jasa cuci 5%
              </li>
              <li class="flex items-center gap-1.5">
                <Check class="w-3 h-3 text-emerald-400" /> Ucapan & promo ulang tahun
              </li>
            </ul>
          </div>

          <!-- Silver -->
          <div class="p-4 rounded-xl bg-[#151528] border border-slate-400/20 hover:border-slate-400/40 transition-all">
            <div class="flex items-center justify-between">
              <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-slate-400/20 text-slate-200 border border-slate-400/30">
                50 - 199 PTS
              </span>
              <Award class="w-5 h-5 text-slate-300" />
            </div>
            <h4 class="text-sm font-bold text-white mt-2">Silver Member</h4>
            <p class="text-xs text-slate-400 mt-1">Pelanggan rutin perhiasan toko.</p>
            <ul class="text-[11px] text-slate-300 mt-3 space-y-1">
              <li class="flex items-center gap-1.5">
                <Check class="w-3 h-3 text-emerald-400" /> Diskon ongkos bikin 10%
              </li>
              <li class="flex items-center gap-1.5">
                <Check class="w-3 h-3 text-emerald-400" /> Souvenir pouch eksklusif
              </li>
            </ul>
          </div>

          <!-- Gold -->
          <div class="p-4 rounded-xl bg-[#151528] border border-amber-500/30 hover:border-amber-500/60 bg-gradient-to-b from-[#151528] to-amber-950/10 transition-all">
            <div class="flex items-center justify-between">
              <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                200 - 499 PTS
              </span>
              <Medal class="w-5 h-5 text-amber-400" />
            </div>
            <h4 class="text-sm font-bold text-amber-300 mt-2">Gold Member</h4>
            <p class="text-xs text-slate-400 mt-1">Kolektor emas & langganan setia.</p>
            <ul class="text-[11px] text-slate-300 mt-3 space-y-1">
              <li class="flex items-center gap-1.5">
                <Check class="w-3 h-3 text-amber-400" /> Diskon ongkos bikin 20%
              </li>
              <li class="flex items-center gap-1.5">
                <Check class="w-3 h-3 text-amber-400" /> Prioritas booking etalase baru
              </li>
            </ul>
          </div>

          <!-- Platinum VIP -->
          <div class="p-4 rounded-xl bg-[#151528] border border-purple-500/30 hover:border-purple-500/60 bg-gradient-to-b from-[#151528] to-purple-950/20 transition-all">
            <div class="flex items-center justify-between">
              <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-purple-500/20 text-purple-300 border border-purple-500/30">
                500+ PTS
              </span>
              <Crown class="w-5 h-5 text-purple-400" />
            </div>
            <h4 class="text-sm font-bold text-purple-300 mt-2">Platinum VIP</h4>
            <p class="text-xs text-slate-400 mt-1">Investor & pelanggan premium.</p>
            <ul class="text-[11px] text-slate-300 mt-3 space-y-1">
              <li class="flex items-center gap-1.5">
                <Check class="w-3 h-3 text-purple-400" /> Ruang santai transaksi VIP
              </li>
              <li class="flex items-center gap-1.5">
                <Check class="w-3 h-3 text-purple-400" /> Reward Logam Mulia Emas
              </li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Katalog Penukaran Hadiah -->
      <div class="p-5 rounded-2xl bg-[#1E1E38] border border-slate-800">
        <div class="flex items-center justify-between mb-4">
          <div class="flex items-center gap-2">
            <Gift class="w-5 h-5 text-emerald-400" />
            <h3 class="text-base font-bold text-white">Katalog Hadiah & Penukaran Souvenir</h3>
          </div>
          <span class="text-xs text-slate-400">Poin dapat ditukarkan di kasir saat transaksi</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5">
          <div
            v-for="item in rewardCatalog"
            :key="item.id"
            class="p-4 rounded-xl bg-[#151528] border border-slate-800 flex flex-col justify-between hover:border-slate-700 transition-all"
          >
            <div>
              <div class="flex items-center justify-between">
                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                  {{ item.category }}
                </span>
                <span class="text-[11px] text-slate-400">Stok: {{ item.stock }}</span>
              </div>
              <h4 class="text-xs font-bold text-white mt-2.5 line-clamp-2">
                {{ item.name }}
              </h4>
              <p class="text-[11px] text-slate-400 mt-1 line-clamp-2">
                {{ item.description }}
              </p>
            </div>

            <div class="mt-4 pt-3 border-t border-slate-800 flex items-center justify-between">
              <span class="text-xs font-bold text-amber-400 font-mono">
                {{ item.points_required }} Pts
              </span>
              <button
                @click="openRedeemModal(customers.data[0] || null, item)"
                class="px-2.5 py-1 rounded-lg bg-[#25254B] hover:bg-emerald-500/20 hover:text-emerald-300 text-slate-300 text-xs font-semibold transition-colors cursor-pointer"
              >
                Tukar
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Leaderboard / Tabel Member & Saldo Poin -->
      <div class="rounded-2xl bg-[#1E1E38] border border-slate-800 overflow-hidden shadow-xl">
        <div class="p-4 border-b border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
          <div>
            <h3 class="text-base font-bold text-white">Daftar Poin Member Toko Emas</h3>
            <p class="text-xs text-slate-400">Peringkat member berdasarkan akumulasi saldo poin loyalitas.</p>
          </div>

          <div class="flex flex-wrap items-center gap-3">
            <!-- Filter Tier -->
            <select
              v-model="tierFilter"
              @change="handleFilter"
              class="px-3 py-2 rounded-xl bg-[#151528] border border-slate-700 text-slate-200 text-xs focus:outline-none focus:border-amber-500"
            >
              <option value="">Semua Tier</option>
              <option value="platinum">Platinum VIP (>= 500 Pts)</option>
              <option value="gold">Gold (200 - 499 Pts)</option>
              <option value="silver">Silver (50 - 199 Pts)</option>
              <option value="bronze">Bronze (< 50 Pts)</option>
            </select>

            <!-- Search -->
            <div class="relative w-64">
              <Search class="w-3.5 h-3.5 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" />
              <input
                v-model="search"
                @keyup.enter="handleFilter"
                type="text"
                placeholder="Cari kode, nama, no HP..."
                class="w-full pl-9 pr-3 py-2 rounded-xl bg-[#151528] border border-slate-700 text-white placeholder-slate-500 text-xs focus:outline-none focus:border-amber-500"
              />
            </div>
          </div>
        </div>

        <!-- Tabel Member -->
        <div class="overflow-x-auto">
          <table class="w-full text-left text-sm text-slate-300">
            <thead class="bg-[#151528] text-xs uppercase text-slate-400 font-semibold border-b border-slate-800">
              <tr>
                <th class="px-6 py-4">Kode Member</th>
                <th class="px-6 py-4">Nama Pelanggan</th>
                <th class="px-6 py-4">No. HP / Kota</th>
                <th class="px-6 py-4 text-center">Tier Member</th>
                <th class="px-6 py-4 text-right">Total Belanja</th>
                <th class="px-6 py-4 text-center">Saldo Poin</th>
                <th class="px-6 py-4 text-right">Aksi Cepat</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/80">
              <tr v-if="customers.data.length === 0">
                <td colspan="7" class="px-6 py-10 text-center text-slate-400">
                  Tidak ada member ditemukan.
                </td>
              </tr>
              <tr
                v-for="customer in customers.data"
                :key="customer.id"
                class="hover:bg-[#25254B]/40 transition-colors"
              >
                <td class="px-6 py-4 font-mono font-bold text-amber-400 text-xs">
                  {{ customer.code }}
                </td>
                <td class="px-6 py-4 font-semibold text-white">
                  {{ customer.name }}
                </td>
                <td class="px-6 py-4 text-xs text-slate-300">
                  <div class="font-mono text-slate-200">{{ customer.phone }}</div>
                  <div class="text-slate-400 text-[11px]">{{ customer.city || '-' }}</div>
                </td>
                <td class="px-6 py-4 text-center">
                  <span
                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold border"
                    :class="getTierBadge(customer.points).color"
                  >
                    <component :is="getTierBadge(customer.points).icon" class="w-3.5 h-3.5" />
                    <span>{{ getTierBadge(customer.points).name }}</span>
                  </span>
                </td>
                <td class="px-6 py-4 text-right font-mono font-medium text-slate-200 text-xs">
                  {{ formatRupiah(customer.total_purchased_amount) }}
                </td>
                <td class="px-6 py-4 text-center">
                  <span class="inline-flex items-center justify-center min-w-[70px] px-3 py-1 rounded-lg bg-amber-500/20 text-amber-400 border border-amber-500/30 font-bold font-mono text-xs">
                    {{ customer.points || 0 }} Pts
                  </span>
                </td>
                <td class="px-6 py-4 text-right">
                  <div class="flex items-center justify-end gap-2">
                    <button
                      @click="openRedeemModal(customer)"
                      class="px-2.5 py-1.5 rounded-lg bg-[#25254B] hover:bg-emerald-500/20 hover:text-emerald-300 text-slate-300 text-xs font-semibold transition-colors flex items-center gap-1.5"
                      title="Tukar Hadiah Member"
                    >
                      <Gift class="w-3.5 h-3.5" />
                      <span>Tukar</span>
                    </button>
                    <a
                      :href="route('admin.customers.print-card', customer.id)"
                      target="_blank"
                      class="p-1.5 rounded-lg bg-[#25254B] hover:bg-amber-500/20 hover:text-amber-400 text-slate-300 transition-colors"
                      title="Cetak Kartu Member"
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
        <div v-if="customers.links && customers.links.length > 3" class="p-4 border-t border-slate-800 flex items-center justify-between">
          <p class="text-xs text-slate-400">
            Menampilkan {{ customers.from || 0 }} - {{ customers.to || 0 }} dari {{ customers.total || 0 }} member
          </p>
          <div class="flex items-center gap-1">
            <button
              v-for="(link, i) in customers.links"
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

      <!-- Riwayat Mutasi Poin Terakhir (Point Logs) -->
      <div class="rounded-2xl bg-[#1E1E38] border border-slate-800 p-5 shadow-xl">
        <div class="flex items-center justify-between mb-4">
          <div class="flex items-center gap-2">
            <Clock class="w-5 h-5 text-amber-400" />
            <h3 class="text-base font-bold text-white">Riwayat Mutasi Poin Terkini</h3>
          </div>
          <span class="text-xs text-slate-400">15 Transaksi perolehan & penukaran poin terbaru</span>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs text-slate-300">
            <thead class="bg-[#151528] uppercase text-slate-400 font-semibold border-b border-slate-800">
              <tr>
                <th class="px-4 py-3">Waktu</th>
                <th class="px-4 py-3">Member Pelanggan</th>
                <th class="px-4 py-3 text-center">Tipe Transaksi</th>
                <th class="px-4 py-3 text-center">Mutasi Poin</th>
                <th class="px-4 py-3">Keterangan / Hadiah</th>
                <th class="px-4 py-3">Petugas</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/80">
              <tr v-if="pointLogs.length === 0">
                <td colspan="6" class="px-4 py-6 text-center text-slate-500">
                  Belum ada log perolehan atau penukaran poin.
                </td>
              </tr>
              <tr
                v-for="log in pointLogs"
                :key="log.id"
                class="hover:bg-[#25254B]/30 transition-colors"
              >
                <td class="px-4 py-3 font-mono text-slate-400">
                  {{ formatDate(log.created_at) }}
                </td>
                <td class="px-4 py-3 font-semibold text-white">
                  {{ log.customer ? log.customer.name : 'Umum' }}
                  <span v-if="log.customer" class="text-slate-500 font-mono text-[10px] block">
                    {{ log.customer.code }}
                  </span>
                </td>
                <td class="px-4 py-3 text-center">
                  <span
                    v-if="log.type === 'earn'"
                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20"
                  >
                    <ArrowDownLeft class="w-3 h-3" />
                    Perolehan Belanja
                  </span>
                  <span
                    v-else
                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-semibold bg-rose-500/10 text-rose-400 border border-rose-500/20"
                  >
                    <ArrowUpRight class="w-3 h-3" />
                    Penukaran Hadiah
                  </span>
                </td>
                <td class="px-4 py-3 text-center font-mono font-bold">
                  <span v-if="log.points > 0" class="text-emerald-400">
                    +{{ log.points }} Pts
                  </span>
                  <span v-else class="text-rose-400">
                    {{ log.points }} Pts
                  </span>
                </td>
                <td class="px-4 py-3 text-slate-300">
                  {{ log.reward_name ? log.reward_name + ' - ' : '' }}{{ log.notes }}
                </td>
                <td class="px-4 py-3 text-slate-400">
                  {{ log.user ? log.user.name : 'Sistem POS' }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Modal Form Penukaran Hadiah (Redeem) -->
    <div
      v-if="isRedeemModalOpen"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm"
    >
      <div class="w-full max-w-lg rounded-2xl bg-[#1E1E38] border border-slate-700 shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
        <div class="p-5 border-b border-slate-800 flex items-center justify-between">
          <div class="flex items-center gap-2.5">
            <div class="w-9 h-9 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400">
              <Gift class="w-5 h-5" />
            </div>
            <div>
              <h3 class="text-base font-bold text-white">Tukar Poin Hadiah Member</h3>
              <p class="text-xs text-slate-400">
                Pemberian souvenir atau potongan voucher untuk pelanggan setia.
              </p>
            </div>
          </div>
          <button
            @click="closeRedeemModal"
            class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-[#25254B] transition-colors"
          >
            <X class="w-5 h-5" />
          </button>
        </div>

        <form @submit.prevent="submitRedeem" class="p-5 space-y-4">
          <!-- Info Member Terpilih -->
          <div v-if="selectedCustomer" class="p-3.5 rounded-xl bg-[#151528] border border-slate-800 flex items-center justify-between">
            <div>
              <p class="text-xs text-slate-400">Member Pelanggan:</p>
              <p class="text-sm font-bold text-white">{{ selectedCustomer.name }} ({{ selectedCustomer.code }})</p>
            </div>
            <div class="text-right">
              <p class="text-xs text-slate-400">Saldo Poin:</p>
              <p class="text-base font-bold text-amber-400 font-mono">{{ selectedCustomer.points || 0 }} Pts</p>
            </div>
          </div>

          <!-- Pilihan Hadiah -->
          <div>
            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
              Nama Hadiah / Souvenir <span class="text-rose-500">*</span>
            </label>
            <input
              v-model="redeemForm.reward_name"
              type="text"
              class="w-full px-3.5 py-2.5 rounded-xl bg-[#151528] border border-slate-700 text-white placeholder-slate-500 text-sm focus:outline-none focus:border-amber-500"
              placeholder="Contoh: Souvenir Pouch Beludru Emas"
              required
            />
            <p v-if="redeemForm.errors.reward_name" class="text-xs text-rose-400 mt-1">
              {{ redeemForm.errors.reward_name }}
            </p>
          </div>

          <!-- Jumlah Poin yang Dipotong -->
          <div>
            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
              Poin yang Ditukarkan <span class="text-rose-500">*</span>
            </label>
            <input
              v-model.number="redeemForm.points"
              type="number"
              min="1"
              :max="selectedCustomer ? selectedCustomer.points : 99999"
              class="w-full px-3.5 py-2.5 rounded-xl bg-[#151528] border border-slate-700 text-white placeholder-slate-500 text-sm font-mono focus:outline-none focus:border-amber-500"
              required
            />
            <p v-if="selectedCustomer && redeemForm.points > selectedCustomer.points" class="text-xs text-rose-400 mt-1">
              Poin melebihi saldo member saat ini ({{ selectedCustomer.points }} Pts).
            </p>
            <p v-if="redeemForm.errors.points" class="text-xs text-rose-400 mt-1">
              {{ redeemForm.errors.points }}
            </p>
          </div>

          <!-- Catatan / Petugas -->
          <div>
            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
              Catatan Penyerahan Hadiah <span class="text-rose-500">*</span>
            </label>
            <textarea
              v-model="redeemForm.notes"
              rows="2"
              class="w-full px-3.5 py-2.5 rounded-xl bg-[#151528] border border-slate-700 text-white placeholder-slate-500 text-sm focus:outline-none focus:border-amber-500"
              placeholder="Contoh: Diserahkan langsung ke pelanggan di kasir utama."
              required
            ></textarea>
            <p v-if="redeemForm.errors.notes" class="text-xs text-rose-400 mt-1">
              {{ redeemForm.errors.notes }}
            </p>
          </div>

          <!-- Modal Actions -->
          <div class="pt-4 border-t border-slate-800 flex items-center justify-end gap-3">
            <button
              type="button"
              @click="closeRedeemModal"
              class="px-4 py-2.5 rounded-xl bg-[#25254B] hover:bg-[#323265] text-slate-300 text-sm font-medium transition-colors"
            >
              Batal
            </button>
            <button
              type="submit"
              :disabled="redeemForm.processing || (selectedCustomer && redeemForm.points > selectedCustomer.points)"
              class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-slate-950 font-semibold text-sm shadow-lg shadow-emerald-500/20 transition-all disabled:opacity-50 cursor-pointer"
            >
              <Check class="w-4 h-4" />
              <span>Proses Penukaran Hadiah</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>
