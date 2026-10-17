<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { 
  Users, 
  Plus, 
  Search, 
  Download, 
  Gift, 
  Award, 
  CreditCard, 
  Edit3, 
  Trash2, 
  FileText, 
  Printer, 
  X, 
  Check, 
  Calendar, 
  DollarSign, 
  Package, 
  Coins, 
  UserCheck 
} from 'lucide-vue-next';

const props = defineProps({
  customers: {
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
const levelFilter = ref(props.filters.level || '');

const handleFilter = () => {
  router.get(route('admin.customers.index'), {
    search: search.value,
    level: levelFilter.value,
  }, { preserveState: true, replace: true });
};

// Modal Edit Pelanggan
const isEditModalOpen = ref(false);
const editingCustomerId = ref(null);
const editForm = useForm({
  name: '',
  birth_date: '',
  identity_number: '',
  phone: '',
  telephone: '',
  email: '',
  city: '',
  address: '',
  level: 'pelanggan',
  notes: '',
});

const openEditModal = (c) => {
  editingCustomerId.value = c.id;
  editForm.name = c.name;
  editForm.birth_date = c.birth_date || '';
  editForm.identity_number = c.identity_number || '';
  editForm.phone = c.phone || '';
  editForm.telephone = c.telephone || '';
  editForm.email = c.email || '';
  editForm.city = c.city;
  editForm.address = c.address;
  editForm.level = c.level;
  editForm.notes = c.notes || '';
  isEditModalOpen.value = true;
};

const submitEdit = () => {
  editForm.put(route('admin.customers.update', editingCustomerId.value), {
    onSuccess: () => { isEditModalOpen.value = false; },
  });
};

// Soft Delete Pelanggan
const deleteCustomer = (c) => {
  if (confirm(`Hapus pelanggan "${c.name}"? Data akan diarsipkan secara aman (soft delete).`)) {
    router.delete(route('admin.customers.destroy', c.id));
  }
};

// Modal Detail Transaksi Pelanggan (Tab Pembelian, Penjualan, Retur, Point, Detail Hadiah)
const isDetailModalOpen = ref(false);
const activeTab = ref('penjualan');
const selectedCustomer = ref(null);
const customerSales = ref([]);
const customerPoints = ref([]);
const isLoadingTransactions = ref(false);

const openDetailModal = async (c) => {
  selectedCustomer.value = c;
  activeTab.value = 'penjualan';
  isDetailModalOpen.value = true;
  isLoadingTransactions.value = true;

  try {
    const res = await fetch(route('admin.customers.transactions', c.id));
    const data = await res.json();
    customerSales.value = data.sales || [];
    customerPoints.value = data.point_logs || [];
  } catch (err) {
    console.error('Gagal mengambil transaksi:', err);
  } finally {
    isLoadingTransactions.value = false;
  }
};

// Modal Ambil Hadiah / Tukar Poin Manual
const isRewardModalOpen = ref(false);
const rewardTargetCustomer = ref(null);
const rewardForm = useForm({
  points: 10,
  reward_name: 'Peralatan Masak / Payung Eksklusif Toko Mas',
  notes: 'Penukaran poin hadiah loyalitas pelanggan',
});

const openRewardModal = (c) => {
  rewardTargetCustomer.value = c;
  rewardForm.reset();
  rewardForm.points = Math.min(10, c.points);
  rewardForm.reward_name = 'Souvenir / Hadiah Loyalitas Toko Mas';
  rewardForm.notes = 'Penukaran poin langsung di kasir';
  isRewardModalOpen.value = true;
};

const submitReward = () => {
  if (!rewardTargetCustomer.value) return;
  rewardForm.post(route('admin.customers.redeem-reward', rewardTargetCustomer.value.id), {
    onSuccess: () => { isRewardModalOpen.value = false; },
  });
};

const formatRupiah = (val) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val || 0);
};
</script>

<template>
  <AdminLayout>
    <Head title="Data Pelanggan & Loyalty Point - POS Emas" />

    <div class="space-y-6">
      <!-- Header Section & Quick Stats -->
      <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2 text-xs font-mono text-amber-600 dark:text-amber-400 uppercase tracking-widest">
            <Users class="w-4 h-4" />
            <span>Master Pelanggan</span>
          </div>
          <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white mt-1">
            Data Pelanggan & Member Emas
          </h2>
          <p class="text-sm text-slate-500 dark:text-slate-400">
            Kelola data pembeli, kartu barcode member, akumulasi gramasi, serta program loyalty poin & hadiah.
          </p>
        </div>

        <!-- Action Buttons Top Bar (Sitokmas Style) -->
        <div class="flex flex-wrap items-center gap-2.5">
          <Link 
            :href="route('admin.customers.create')"
            class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs shadow-sm shadow-amber-500/20 transition-all cursor-pointer"
          >
            <Plus class="w-4 h-4" />
            <span>Tambah Pelanggan</span>
          </Link>

          <a 
            :href="route('admin.customers.export')"
            class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-sky-500/10 hover:bg-sky-500/20 text-sky-600 dark:text-sky-400 border border-sky-500/20 text-xs font-semibold transition-colors"
          >
            <Download class="w-4 h-4" />
            <span>Unduh Data (CSV)</span>
          </a>
        </div>
      </div>

      <!-- Quick Summary Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
            <Users class="w-6 h-6" />
          </div>
          <div>
            <p class="text-xs text-slate-500 dark:text-slate-400">Total Pelanggan Terdaftar</p>
            <h4 class="text-xl font-bold text-slate-900 dark:text-white mt-0.5">{{ stats.total_customers }} Orang</h4>
          </div>
        </div>

        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
            <Coins class="w-6 h-6" />
          </div>
          <div>
            <p class="text-xs text-slate-500 dark:text-slate-400">Total Akumulasi Poin Aktif</p>
            <h4 class="text-xl font-bold text-slate-900 dark:text-white mt-0.5">{{ stats.total_points }} Pts</h4>
          </div>
        </div>

        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-yellow-500/10 text-yellow-600 dark:text-yellow-400 flex items-center justify-center shrink-0">
            <DollarSign class="w-6 h-6" />
          </div>
          <div>
            <p class="text-xs text-slate-500 dark:text-slate-400">Total Akumulasi Pembelian</p>
            <h4 class="text-xl font-bold text-slate-900 dark:text-white mt-0.5">{{ formatRupiah(stats.total_purchases) }}</h4>
          </div>
        </div>
      </div>

      <!-- Filter & Search Bar -->
      <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="relative flex-1 w-full max-w-md">
          <Search class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
          <input 
            v-model="search"
            @keyup.enter="handleFilter"
            type="text" 
            placeholder="Cari kode pelanggan, nama, no HP, kota..."
            class="w-full pl-10 pr-4 py-2 bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-colors"
          />
        </div>

        <div class="flex items-center gap-3 w-full sm:w-auto">
          <select 
            v-model="levelFilter"
            @change="handleFilter"
            class="px-3.5 py-2 bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500"
          >
            <option value="">Semua Level Pelanggan</option>
            <option value="pelanggan">Pelanggan Reguler</option>
            <option value="agen">Agen</option>
            <option value="vip">VIP Member</option>
            <option value="marketer">Marketer</option>
          </select>
        </div>
      </div>

      <!-- Tabel Data Pelanggan (Sitokmas View) -->
      <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs">
            <thead class="bg-slate-50 dark:bg-slate-950/50 text-slate-500 dark:text-slate-400 text-[11px] uppercase font-mono tracking-wider border-b border-slate-200 dark:border-slate-800">
              <tr>
                <th class="px-4 py-3.5">Kode Pelanggan</th>
                <th class="px-4 py-3.5">Nama Pelanggan</th>
                <th class="px-4 py-3.5">No. HP / WA</th>
                <th class="px-4 py-3.5">Kota / Alamat</th>
                <th class="px-4 py-3.5 text-center">Level</th>
                <th class="px-4 py-3.5 text-center">Tot. Item</th>
                <th class="px-4 py-3.5 text-right">Tot. Pembelian</th>
                <th class="px-4 py-3.5 text-center">Poin</th>
                <th class="px-4 py-3.5 text-center">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-800/80">
              <tr v-for="c in customers.data" :key="c.id" class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition-colors">
                <!-- Kode Pelanggan -->
                <td class="px-4 py-3 font-mono font-bold text-amber-700 dark:text-amber-400">
                  {{ c.code }}
                </td>

                <!-- Nama -->
                <td class="px-4 py-3 font-semibold text-slate-900 dark:text-white">
                  {{ c.name }}
                  <div v-if="c.email" class="text-[10px] text-slate-400 font-normal truncate max-w-[150px]">
                    {{ c.email }}
                  </div>
                </td>

                <!-- No HP -->
                <td class="px-4 py-3 font-mono text-slate-700 dark:text-slate-300">
                  {{ c.phone || '-' }}
                </td>

                <!-- Alamat & Kota -->
                <td class="px-4 py-3 max-w-[200px] text-slate-600 dark:text-slate-300">
                  <span class="font-medium text-amber-600 dark:text-amber-400 font-mono text-[10px] uppercase">[{{ c.city }}]</span>
                  <p class="truncate text-[11px]">{{ c.address }}</p>
                </td>

                <!-- Level -->
                <td class="px-4 py-3 text-center">
                  <span 
                    :class="[
                      c.level === 'vip' ? 'bg-amber-500/10 text-amber-700 dark:text-amber-300 border-amber-500/20' : '',
                      c.level === 'agen' ? 'bg-purple-500/10 text-purple-700 dark:text-purple-300 border-purple-500/20' : '',
                      c.level === 'marketer' ? 'bg-sky-500/10 text-sky-700 dark:text-sky-300 border-sky-500/20' : '',
                      c.level === 'pelanggan' ? 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border-slate-200 dark:border-slate-700' : '',
                      'inline-block px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase tracking-wider border'
                    ]"
                  >
                    {{ c.level }}
                  </span>
                </td>

                <!-- Tot Item -->
                <td class="px-4 py-3 text-center font-mono font-semibold text-slate-800 dark:text-slate-200">
                  {{ c.total_items_count }} Pcs
                </td>

                <!-- Tot Pembelian -->
                <td class="px-4 py-3 text-right font-mono font-bold text-slate-900 dark:text-white">
                  {{ formatRupiah(c.total_purchased_amount) }}
                </td>

                <!-- Poin Loyalty Badge Button -->
                <td class="px-4 py-3 text-center">
                  <button 
                    @click="openRewardModal(c)"
                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-500 text-slate-950 font-bold text-[11px] hover:bg-amber-400 transition-colors shadow-sm cursor-pointer"
                    title="Klik untuk tukar hadiah poin"
                  >
                    <Coins class="w-3 h-3" />
                    <span>{{ c.points }} Pts</span>
                  </button>
                </td>

                <!-- 5 Aksi (Sitokmas Buttons: Edit, Hapus, Lihat Transaksi, Ambil Hadiah, Cetak Kartu) -->
                <td class="px-4 py-3 text-center">
                  <div class="flex items-center justify-center gap-1.5">
                    <!-- 1. Edit -->
                    <button 
                      @click="openEditModal(c)"
                      class="p-1.5 rounded-lg bg-amber-500/10 hover:bg-amber-500/20 text-amber-600 dark:text-amber-400 transition-colors cursor-pointer"
                      title="Edit Pelanggan"
                    >
                      <Edit3 class="w-3.5 h-3.5" />
                    </button>

                    <!-- 2. Hapus (Soft Delete) -->
                    <button 
                      @click="deleteCustomer(c)"
                      class="p-1.5 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 transition-colors cursor-pointer"
                      title="Hapus (Soft Delete)"
                    >
                      <Trash2 class="w-3.5 h-3.5" />
                    </button>

                    <!-- 3. Lihat Transaksi -->
                    <button 
                      @click="openDetailModal(c)"
                      class="p-1.5 rounded-lg bg-purple-500/10 hover:bg-purple-500/20 text-purple-600 dark:text-purple-400 transition-colors cursor-pointer"
                      title="Lihat Riwayat Transaksi"
                    >
                      <FileText class="w-3.5 h-3.5" />
                    </button>

                    <!-- 4. Ambil Hadiah -->
                    <button 
                      @click="openRewardModal(c)"
                      class="p-1.5 rounded-lg bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 transition-colors cursor-pointer"
                      title="Ambil Hadiah (Tukar Poin)"
                    >
                      <Gift class="w-3.5 h-3.5" />
                    </button>

                    <!-- 5. Cetak Kartu Pelanggan -->
                    <Link 
                      :href="route('admin.customers.print-card', c.id)"
                      target="_blank"
                      class="p-1.5 rounded-lg bg-sky-500/10 hover:bg-sky-500/20 text-sky-600 dark:text-sky-400 transition-colors cursor-pointer"
                      title="Cetak Kartu Member Barcode"
                    >
                      <Printer class="w-3.5 h-3.5" />
                    </Link>
                  </div>
                </td>
              </tr>

              <tr v-if="customers.data.length === 0">
                <td colspan="9" class="px-6 py-12 text-center text-slate-400">
                  Belum ada data pelanggan yang cocok dengan pencarian.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Modal 1: Edit Pelanggan -->
    <div v-if="isEditModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
      <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl max-w-lg w-full p-6 shadow-xl space-y-5">
        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-4">
          <h3 class="text-lg font-bold text-slate-900 dark:text-white">Edit Identitas Pelanggan</h3>
          <button @click="isEditModalOpen = false" class="p-1.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-lg">
            <X class="w-5 h-5" />
          </button>
        </div>

        <form @submit.prevent="submitEdit" class="space-y-4">
          <div>
            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase mb-1">Nama Lengkap *</label>
            <input v-model="editForm.name" type="text" required class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm" />
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase mb-1">No. HP / WA</label>
              <input v-model="editForm.phone" type="text" class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm font-mono" />
            </div>
            <div>
              <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase mb-1">Kota *</label>
              <input v-model="editForm.city" type="text" required class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm" />
            </div>
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase mb-1">Alamat Lengkap *</label>
            <textarea v-model="editForm.address" rows="2" required class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm"></textarea>
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase mb-1">Level Pelanggan *</label>
            <select v-model="editForm.level" class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm font-semibold">
              <option value="pelanggan">Pelanggan Reguler</option>
              <option value="agen">Agen</option>
              <option value="vip">VIP</option>
              <option value="marketer">Marketer</option>
            </select>
          </div>

          <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200 dark:border-slate-800">
            <button type="button" @click="isEditModalOpen = false" class="px-4 py-2 text-sm text-slate-600 dark:text-slate-300">Batal</button>
            <button type="submit" class="px-5 py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-sm rounded-xl">Simpan Perubahan</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal 2: Detail Transaksi Pelanggan (Tab Pembelian, Penjualan, Point) -->
    <div v-if="isDetailModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
      <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl max-w-3xl w-full p-6 shadow-2xl space-y-5">
        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-4">
          <div>
            <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
              <FileText class="w-5 h-5 text-amber-500" />
              Detail Transaksi Pelanggan
            </h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-mono">
              {{ selectedCustomer?.code }} - {{ selectedCustomer?.name }} ({{ selectedCustomer?.city }})
            </p>
          </div>
          <button @click="isDetailModalOpen = false" class="p-1.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-lg">
            <X class="w-5 h-5" />
          </button>
        </div>

        <!-- Navigation Tabs (Persis Sitokmas) -->
        <div class="flex items-center gap-2 border-b border-slate-200 dark:border-slate-800 text-xs font-semibold">
          <button 
            @click="activeTab = 'penjualan'"
            :class="[activeTab === 'penjualan' ? 'border-amber-500 text-amber-600 dark:text-amber-400 border-b-2' : 'text-slate-500 hover:text-slate-900', 'px-4 py-2.5 transition-colors']"
          >
            Penjualan (Kasir)
          </button>
          <button 
            @click="activeTab = 'point'"
            :class="[activeTab === 'point' ? 'border-amber-500 text-amber-600 dark:text-amber-400 border-b-2' : 'text-slate-500 hover:text-slate-900', 'px-4 py-2.5 transition-colors']"
          >
            Mutasi Poin
          </button>
        </div>

        <!-- Tab Content -->
        <div class="max-h-80 overflow-y-auto">
          <!-- Penjualan List -->
          <table v-if="activeTab === 'penjualan'" class="w-full text-left text-xs">
            <thead class="bg-slate-50 dark:bg-slate-950 font-mono text-[10px] text-slate-500 uppercase">
              <tr>
                <th class="p-2.5">No Faktur</th>
                <th class="p-2.5">Tanggal</th>
                <th class="p-2.5">Total Berat</th>
                <th class="p-2.5 text-right">Grand Total</th>
                <th class="p-2.5 text-center">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
              <tr v-for="sale in customerSales" :key="sale.id">
                <td class="p-2.5 font-mono font-bold text-amber-600 dark:text-amber-400">{{ sale.invoice_number }}</td>
                <td class="p-2.5 text-slate-500">{{ new Date(sale.created_at).toLocaleDateString('id-ID') }}</td>
                <td class="p-2.5 font-mono">{{ parseFloat(sale.total_weight).toFixed(3) }} gr</td>
                <td class="p-2.5 text-right font-mono font-bold">{{ formatRupiah(sale.grand_total) }}</td>
                <td class="p-2.5 text-center">
                  <span class="px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-600 text-[10px] font-bold">LUNAS</span>
                </td>
              </tr>
              <tr v-if="customerSales.length === 0">
                <td colspan="5" class="p-6 text-center text-slate-400">Belum ada riwayat transaksi penjualan.</td>
              </tr>
            </tbody>
          </table>

          <!-- Point History -->
          <table v-if="activeTab === 'point'" class="w-full text-left text-xs">
            <thead class="bg-slate-50 dark:bg-slate-950 font-mono text-[10px] text-slate-500 uppercase">
              <tr>
                <th class="p-2.5">Tanggal</th>
                <th class="p-2.5">Jenis</th>
                <th class="p-2.5">Poin</th>
                <th class="p-2.5">Keterangan / Hadiah</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
              <tr v-for="p in customerPoints" :key="p.id">
                <td class="p-2.5 text-slate-500 font-mono">{{ new Date(p.created_at).toLocaleDateString('id-ID') }}</td>
                <td class="p-2.5">
                  <span :class="[p.type === 'earn' ? 'text-emerald-500' : 'text-rose-500', 'font-bold uppercase text-[10px]']">
                    {{ p.type === 'earn' ? 'Dapat Poin (+)' : 'Tukar Hadiah (-)' }}
                  </span>
                </td>
                <td class="p-2.5 font-mono font-bold">{{ p.points > 0 ? '+' : '' }}{{ p.points }}</td>
                <td class="p-2.5 text-slate-600 dark:text-slate-300">{{ p.notes || p.reward_name }}</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="flex justify-end pt-3 border-t border-slate-200 dark:border-slate-800">
          <button @click="isDetailModalOpen = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-xl">
            Tutup
          </button>
        </div>
      </div>
    </div>

    <!-- Modal 3: Ambil Hadiah / Tukar Poin Manual (Persis Screenshot Sitokmas) -->
    <div v-if="isRewardModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
      <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-5">
        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
          <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
            <Gift class="w-5 h-5 text-emerald-500" />
            Ambil Hadiah (Beri Manual)
          </h3>
          <button @click="isRewardModalOpen = false" class="p-1.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-lg">
            <X class="w-5 h-5" />
          </button>
        </div>

        <!-- Customer Summary Header Box -->
        <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 space-y-2 text-xs">
          <div class="flex justify-between">
            <span class="text-slate-400">Nama Pelanggan:</span>
            <span class="font-bold text-slate-900 dark:text-white">{{ rewardTargetCustomer?.name }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-slate-400">Kode Member:</span>
            <span class="font-mono text-amber-600 dark:text-amber-400 font-bold">{{ rewardTargetCustomer?.code }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-slate-400">Saldo Poin Saat Ini:</span>
            <span class="font-mono font-bold text-emerald-600 dark:text-emerald-400 text-sm">{{ rewardTargetCustomer?.points }} Pts</span>
          </div>
        </div>

        <form @submit.prevent="submitReward" class="space-y-4">
          <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase mb-1">
              Jumlah Poin yang Dikurangi *
            </label>
            <input 
              v-model="rewardForm.points"
              type="number" 
              min="1"
              :max="rewardTargetCustomer?.points || 1"
              required
              class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm font-mono font-bold text-rose-500"
            />
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase mb-1">
              Nama Hadiah / Souvenir *
            </label>
            <input 
              v-model="rewardForm.reward_name"
              type="text" 
              required
              placeholder="Contoh: Mug Emas / Payung / Logam Mulia 0.1gr"
              class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm"
            />
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase mb-1">
              Keterangan Penukaran
            </label>
            <textarea 
              v-model="rewardForm.notes"
              rows="2"
              placeholder="Catatan penyerahan hadiah fisik..."
              class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm"
            ></textarea>
          </div>

          <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200 dark:border-slate-800">
            <button type="button" @click="isRewardModalOpen = false" class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-400">
              Batal
            </button>
            <button 
              type="submit" 
              :disabled="rewardForm.processing || (rewardTargetCustomer?.points || 0) < 1"
              class="px-5 py-2 bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-bold text-xs rounded-xl shadow-sm transition-all"
            >
              Ambil Hadiah
            </button>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>
