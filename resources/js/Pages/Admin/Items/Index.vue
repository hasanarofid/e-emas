<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import { 
  Gem, 
  Plus, 
  Search, 
  Printer, 
  Edit3, 
  Trash2, 
  Scale, 
  QrCode, 
  Sparkles, 
  X, 
  Filter,
  Layers,
  Coins
} from 'lucide-vue-next';

const props = defineProps({
  items: {
    type: Object,
    required: true
  },
  categories: {
    type: Array,
    default: () => []
  },
  goldRates: {
    type: Array,
    default: () => []
  },
  trays: {
    type: Array,
    default: () => []
  },
  filters: {
    type: Object,
    default: () => ({})
  },
  stats: {
    type: Object,
    required: true
  }
});

// Search & Filter State
const search = ref(props.filters.search || '');
const categoryId = ref(props.filters.category_id || '');
const goldRateId = ref(props.filters.gold_rate_id || '');
const trayId = ref(props.filters.tray_id || '');
const status = ref(props.filters.status || 'available');

const applyFilter = () => {
  router.get(route('admin.items.index'), {
    search: search.value,
    category_id: categoryId.value,
    gold_rate_id: goldRateId.value,
    tray_id: trayId.value,
    status: status.value,
  }, {
    preserveState: true,
    replace: true,
  });
};

// Modal Tambah / Edit Perhiasan
const isModalOpen = ref(false);
const isEditing = ref(false);
const editingItemId = ref(null);

const itemForm = useForm({
  sku: '',
  name: '',
  category_id: '',
  gold_rate_id: '',
  tray_id: '',
  gross_weight: '',
  stone_weight: '0',
  crafting_fee: '0',
  status: 'available',
  notes: '',
});

// Perhitungan Otomatis Berat Bersih & Estimasi Harga Jual di Form
const calculatedNetWeight = computed(() => {
  const gross = parseFloat(itemForm.gross_weight) || 0;
  const stone = parseFloat(itemForm.stone_weight) || 0;
  return Math.max(0, (gross - stone)).toFixed(3);
});

const calculatedEstimatedPrice = computed(() => {
  const selectedRate = props.goldRates.find(r => r.id === parseInt(itemForm.gold_rate_id));
  const rateSellPrice = selectedRate ? parseFloat(selectedRate.sell_price_per_gram) : 0;
  const net = parseFloat(calculatedNetWeight.value) || 0;
  const fee = parseFloat(itemForm.crafting_fee) || 0;
  return Math.round((rateSellPrice * net) + fee);
});

const openCreateModal = () => {
  isEditing.value = false;
  editingItemId.value = null;
  itemForm.reset();
  itemForm.stone_weight = '0';
  itemForm.crafting_fee = '0';
  itemForm.status = 'available';
  if (props.categories.length) itemForm.category_id = props.categories[0].id;
  if (props.goldRates.length) itemForm.gold_rate_id = props.goldRates[0].id;
  isModalOpen.value = true;
};

const openEditModal = (item) => {
  isEditing.value = true;
  editingItemId.value = item.id;
  itemForm.sku = item.sku;
  itemForm.name = item.name;
  itemForm.category_id = item.category_id;
  itemForm.gold_rate_id = item.gold_rate_id;
  itemForm.tray_id = item.tray_id || '';
  itemForm.gross_weight = item.gross_weight;
  itemForm.stone_weight = item.stone_weight;
  itemForm.crafting_fee = item.crafting_fee;
  itemForm.status = item.status;
  itemForm.notes = item.notes || '';
  isModalOpen.value = true;
};

const submitItem = () => {
  if (isEditing.value) {
    itemForm.put(route('admin.items.update', editingItemId.value), {
      onSuccess: () => { isModalOpen.value = false; },
    });
  } else {
    itemForm.post(route('admin.items.store'), {
      onSuccess: () => { isModalOpen.value = false; },
    });
  }
};

const deleteItem = (item) => {
  if (confirm(`Apakah Anda yakin ingin menghapus perhiasan ${item.sku} - ${item.name}?`)) {
    router.delete(route('admin.items.destroy', item.id));
  }
};

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
  <Head title="Inventaris Perhiasan - POS Emas" />

  <AdminLayout>
    <div class="space-y-8">
      <!-- Title & Actions Bar -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200 dark:border-slate-800">
        <div>
          <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-700 dark:text-amber-300 text-xs font-semibold mb-1">
            <Sparkles class="w-3.5 h-3.5 text-amber-500 dark:text-amber-400" />
            <span>Katalog Master Inventaris Emas</span>
          </div>
          <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">Inventaris Perhiasan & Barcode</h2>
          <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
            Data gramasi kotor, berat batu, berat bersih emas presisi 3 desimal, posisi baki, serta label cetak tag perhiasan.
          </p>
        </div>

        <button 
          @click="openCreateModal"
          class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 text-xs font-bold shadow-md shadow-amber-500/20 transition-all flex items-center gap-2 cursor-pointer self-start sm:self-auto"
        >
          <Plus class="w-4 h-4" />
          <span>Tambah Perhiasan Baru</span>
        </button>
      </div>

      <!-- Stats Summary Strip -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-sm">
          <span class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider font-semibold">Stok Perhiasan Aktif</span>
          <h3 class="text-2xl font-black text-slate-900 dark:text-white mt-1">{{ stats.total_available }} Item</h3>
        </div>
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-sm">
          <span class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider font-semibold">Total Berat Bersih</span>
          <h3 class="text-2xl font-black text-amber-700 dark:text-amber-300 font-mono mt-1">{{ formatWeight(stats.total_weight) }}</h3>
        </div>
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-sm">
          <span class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider font-semibold">Estimasi Valuasi Stok</span>
          <h3 class="text-2xl font-black text-slate-900 dark:text-white mt-1">{{ formatRupiah(stats.total_valuation) }}</h3>
        </div>
      </div>

      <!-- Filter Toolbar -->
      <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 shadow-sm">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
          <!-- Search SKU/Name -->
          <div class="relative">
            <input 
              type="text" 
              v-model="search"
              @keyup.enter="applyFilter"
              placeholder="Cari Barcode / SKU / Nama..."
              class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-950 border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white pl-8 pr-3 py-2 focus:border-amber-500 focus:ring-amber-500"
            />
            <Search class="w-4 h-4 text-slate-400 absolute left-2.5 top-2.5" />
          </div>

          <!-- Filter Kategori -->
          <select 
            v-model="categoryId"
            @change="applyFilter"
            class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-950 border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white py-2 px-3 focus:border-amber-500 focus:ring-amber-500"
          >
            <option value="">Semua Kategori</option>
            <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
          </select>

          <!-- Filter Kadar -->
          <select 
            v-model="goldRateId"
            @change="applyFilter"
            class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-950 border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white py-2 px-3 focus:border-amber-500 focus:ring-amber-500"
          >
            <option value="">Semua Kadar</option>
            <option v-for="r in goldRates" :key="r.id" :value="r.id">{{ r.name }}</option>
          </select>

          <!-- Filter Baki -->
          <select 
            v-model="trayId"
            @change="applyFilter"
            class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-950 border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white py-2 px-3 focus:border-amber-500 focus:ring-amber-500"
          >
            <option value="">Semua Baki</option>
            <option v-for="t in trays" :key="t.id" :value="t.id">{{ t.code }} - {{ t.name }}</option>
          </select>

          <!-- Filter Status -->
          <select 
            v-model="status"
            @change="applyFilter"
            class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-950 border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white py-2 px-3 focus:border-amber-500 focus:ring-amber-500"
          >
            <option value="available">Tersedia (Ready)</option>
            <option value="sold">Terjual</option>
            <option value="buyback">Buyback (Bekas)</option>
            <option value="repair">Dalam Servis</option>
            <option value="melted">Dilebur</option>
          </select>
        </div>
      </div>

      <!-- Tabel Inventaris Perhiasan -->
      <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs">
            <thead class="text-slate-500 dark:text-slate-400 border-b border-slate-100 dark:border-slate-800 uppercase tracking-wider text-[10px] bg-slate-50 dark:bg-slate-950/60">
              <tr>
                <th class="py-3 px-4">Barcode / SKU</th>
                <th class="py-3 px-4">Nama Perhiasan</th>
                <th class="py-3 px-4">Kadar</th>
                <th class="py-3 px-4">Baki Etalase</th>
                <th class="py-3 px-4">Berat Kotor</th>
                <th class="py-3 px-4">Berat Batu</th>
                <th class="py-3 px-4">Berat Bersih</th>
                <th class="py-3 px-4">Ongkos Bikin</th>
                <th class="py-3 px-4 text-right">Estimasi Jual</th>
                <th class="py-3 px-4 text-center">Status</th>
                <th class="py-3 px-4 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 text-slate-700 dark:text-slate-300">
              <tr v-for="item in items.data" :key="item.id" class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                <td class="py-3.5 px-4 font-mono font-bold text-amber-700 dark:text-amber-400">
                  <div class="flex items-center gap-1.5">
                    <QrCode class="w-3.5 h-3.5 text-slate-400" />
                    <span>{{ item.sku }}</span>
                  </div>
                </td>
                <td class="py-3.5 px-4 font-semibold text-slate-900 dark:text-white max-w-[200px] truncate">
                  {{ item.name }}
                </td>
                <td class="py-3.5 px-4 font-bold text-slate-800 dark:text-slate-200">
                  <span class="px-2 py-0.5 rounded bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-500/20 font-mono text-[10px]">
                    {{ item.gold_rate ? item.gold_rate.code : '-' }}
                  </span>
                </td>
                <td class="py-3.5 px-4 font-mono text-slate-700 dark:text-slate-300">
                  {{ item.tray ? item.tray.code : '-' }}
                </td>
                <td class="py-3.5 px-4 font-mono">{{ formatWeight(item.gross_weight) }}</td>
                <td class="py-3.5 px-4 font-mono text-slate-400">{{ formatWeight(item.stone_weight) }}</td>
                <td class="py-3.5 px-4 font-mono font-bold text-amber-700 dark:text-amber-300">{{ formatWeight(item.net_weight) }}</td>
                <td class="py-3.5 px-4 font-mono text-slate-500 dark:text-slate-400">{{ formatRupiah(item.crafting_fee) }}</td>
                <td class="py-3.5 px-4 text-right font-mono font-bold text-slate-900 dark:text-white">{{ formatRupiah(item.total_price) }}</td>
                <td class="py-3.5 px-4 text-center">
                  <span 
                    class="px-2 py-0.5 rounded-full text-[10px] font-semibold"
                    :class="[
                      item.status === 'available' ? 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-500/20' : '',
                      item.status === 'sold' ? 'bg-slate-200 dark:bg-slate-800 text-slate-500' : '',
                      item.status === 'buyback' ? 'bg-amber-500/10 text-amber-700 dark:text-amber-300' : ''
                    ]"
                  >
                    {{ item.status === 'available' ? 'Tersedia' : item.status }}
                  </span>
                </td>
                <td class="py-3.5 px-4 text-right">
                  <div class="flex items-center justify-end gap-1.5">
                    <Link 
                      :href="route('admin.items.print-tag', item.id)"
                      target="_blank"
                      class="p-1.5 text-slate-400 hover:text-amber-600 dark:hover:text-amber-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors"
                      title="Cetak Label Barcode Tag"
                    >
                      <Printer class="w-4 h-4" />
                    </Link>
                    <button 
                      @click="openEditModal(item)"
                      class="p-1.5 text-slate-400 hover:text-slate-700 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors cursor-pointer"
                      title="Edit Item"
                    >
                      <Edit3 class="w-4 h-4" />
                    </button>
                    <button 
                      v-if="item.status !== 'sold'"
                      @click="deleteItem(item)"
                      class="p-1.5 text-slate-400 hover:text-rose-500 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors cursor-pointer"
                      title="Hapus Item"
                    >
                      <Trash2 class="w-4 h-4" />
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="items.data.length === 0">
                <td colspan="11" class="py-8 text-center text-slate-400">
                  Tidak ada perhiasan yang sesuai dengan filter pencarian.
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="items.links && items.links.length > 3" class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
          <span class="text-slate-400">Menampilkan {{ items.from }} - {{ items.to }} dari {{ items.total }} barang</span>
          <div class="flex gap-1">
            <Link 
              v-for="(link, i) in items.links" 
              :key="i"
              :href="link.url || '#'"
              v-html="link.label"
              class="px-2.5 py-1 rounded-lg border text-xs"
              :class="[
                link.active 
                  ? 'bg-amber-500 text-slate-950 font-bold border-amber-500' 
                  : 'text-slate-600 dark:text-slate-400 border-slate-200 dark:border-slate-800 hover:bg-slate-100 dark:hover:bg-slate-800',
                !link.url ? 'opacity-30 pointer-events-none' : ''
              ]"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Form Tambah / Edit Perhiasan -->
    <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm overflow-y-auto">
      <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-8 max-w-2xl w-full shadow-2xl relative my-8">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
          <div class="flex items-center gap-2">
            <Gem class="w-5 h-5 text-amber-500" />
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">
              {{ isEditing ? 'Edit Data Perhiasan' : 'Input Perhiasan Masuk Baru' }}
            </h3>
          </div>
          <button @click="isModalOpen = false" class="p-1.5 text-slate-400 hover:text-slate-700 dark:hover:text-white rounded-lg">
            <X class="w-5 h-5" />
          </button>
        </div>

        <form @submit.prevent="submitItem" class="space-y-4 mt-4 text-xs">
          <!-- Row 1: SKU & Nama Barang -->
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">
                Barcode / SKU <span class="text-slate-400 font-normal">(Auto jika kosong)</span>
              </label>
              <input 
                type="text" 
                v-model="itemForm.sku"
                placeholder="cth: EMAS-17K-001"
                class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-950 border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white py-2 px-3 focus:border-amber-500 focus:ring-amber-500 font-mono"
              />
            </div>
            <div class="sm:col-span-2">
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Perhiasan</label>
              <input 
                type="text" 
                v-model="itemForm.name"
                placeholder="cth: Cincin Solitaire Zircon Putih Rose Gold"
                required
                class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-950 border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white py-2 px-3 focus:border-amber-500 focus:ring-amber-500"
              />
            </div>
          </div>

          <!-- Row 2: Kategori, Kadar, Baki -->
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kategori Perhiasan</label>
              <select 
                v-model="itemForm.category_id"
                required
                class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-950 border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white py-2 px-3 focus:border-amber-500 focus:ring-amber-500"
              >
                <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
              </select>
            </div>

            <div>
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kadar / Karat</label>
              <select 
                v-model="itemForm.gold_rate_id"
                required
                class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-950 border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white py-2 px-3 focus:border-amber-500 focus:ring-amber-500 font-bold"
              >
                <option v-for="r in goldRates" :key="r.id" :value="r.id">{{ r.name }}</option>
              </select>
            </div>

            <div>
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Baki Etalase</label>
              <select 
                v-model="itemForm.tray_id"
                class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-950 border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white py-2 px-3 focus:border-amber-500 focus:ring-amber-500"
              >
                <option value="">-- Tanpa Baki / Brankas --</option>
                <option v-for="t in trays" :key="t.id" :value="t.id">{{ t.code }} - {{ t.name }}</option>
              </select>
            </div>
          </div>

          <!-- Row 3: Presisi Timbangan Gramasi (Gross, Stone, Net Otomatis) -->
          <div class="bg-amber-500/5 dark:bg-slate-950/70 border border-amber-500/20 rounded-2xl p-4 space-y-3">
            <span class="font-bold text-amber-700 dark:text-amber-400 block text-[11px] uppercase tracking-wider flex items-center gap-1.5">
              <Scale class="w-3.5 h-3.5" /> Kalkulator Timbangan Emas Presisi (0.001 gr)
            </span>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
              <div>
                <label class="block text-slate-700 dark:text-slate-300 mb-1">1. Berat Kotor Timbangan (gr)</label>
                <input 
                  type="number" 
                  step="0.001"
                  v-model="itemForm.gross_weight"
                  placeholder="0.000"
                  required
                  class="w-full text-xs rounded-xl bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white font-mono font-bold py-2 px-3 focus:border-amber-500 focus:ring-amber-500"
                />
              </div>

              <div>
                <label class="block text-slate-700 dark:text-slate-300 mb-1">2. Berat Batu / Permata (gr)</label>
                <input 
                  type="number" 
                  step="0.001"
                  v-model="itemForm.stone_weight"
                  placeholder="0.000"
                  class="w-full text-xs rounded-xl bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white font-mono py-2 px-3 focus:border-amber-500 focus:ring-amber-500"
                />
              </div>

              <div class="bg-white dark:bg-slate-900 border border-amber-500/30 rounded-xl p-2.5 flex flex-col justify-center">
                <span class="text-[10px] text-slate-500 dark:text-slate-400 block">3. Berat Bersih Emas (Gross - Stone)</span>
                <span class="font-mono text-base font-extrabold text-amber-700 dark:text-amber-300">
                  {{ calculatedNetWeight }} gram
                </span>
              </div>
            </div>
          </div>

          <!-- Row 4: Ongkos Bikin & Estimasi Harga Jual Otomatis -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Ongkos Pembuatan / Bikin (Rp)</label>
              <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 font-mono">Rp</span>
                <input 
                  type="number" 
                  v-model="itemForm.crafting_fee"
                  placeholder="0"
                  class="w-full pl-9 pr-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-950 border-slate-300 dark:border-slate-800 font-mono text-slate-900 dark:text-white focus:border-amber-500 focus:ring-amber-500"
                />
              </div>
            </div>

            <div class="bg-slate-100 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl p-3 flex items-center justify-between">
              <div>
                <span class="text-[10px] text-slate-500 dark:text-slate-400 block">Estimasi Harga Jual Sistem:</span>
                <span class="text-[10px] text-slate-400">(Net Weight × Kurs Jual Hari Ini) + Ongkos</span>
              </div>
              <span class="text-base font-black text-slate-900 dark:text-white font-mono">
                {{ formatRupiah(calculatedEstimatedPrice) }}
              </span>
            </div>
          </div>

          <!-- Row 5: Status & Notes -->
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Status Perhiasan</label>
              <select 
                v-model="itemForm.status"
                class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-950 border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white py-2 px-3 focus:border-amber-500 focus:ring-amber-500"
              >
                <option value="available">Tersedia di Etalase</option>
                <option value="sold">Terjual</option>
                <option value="buyback">Buyback (Stok Bekas)</option>
                <option value="repair">Dalam Servis</option>
                <option value="melted">Dilebur</option>
              </select>
            </div>

            <div class="sm:col-span-2">
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Keterangan / Detail Ukuran (Ring Size, Panjang)</label>
              <input 
                type="text" 
                v-model="itemForm.notes"
                placeholder="cth: Size 14, model solitaire bunga"
                class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-950 border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white py-2 px-3 focus:border-amber-500 focus:ring-amber-500"
              />
            </div>
          </div>

          <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
            <button 
              type="button" 
              @click="isModalOpen = false"
              class="px-4 py-2 rounded-xl text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 cursor-pointer"
            >
              Batal
            </button>
            <button 
              type="submit"
              :disabled="itemForm.processing"
              class="px-5 py-2 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 text-slate-950 font-bold shadow-md shadow-amber-500/20 active:scale-95 transition-all cursor-pointer"
            >
              Simpan Perhiasan
            </button>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>
