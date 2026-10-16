<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { 
  Layers, 
  Plus, 
  ArrowRightLeft, 
  Scale, 
  Sparkles, 
  Eye, 
  Edit3, 
  Trash2, 
  X, 
  Check, 
  QrCode,
  Store,
  Filter
} from 'lucide-vue-next';

const props = defineProps({
  trays: {
    type: Array,
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
  filters: {
    type: Object,
    default: () => ({})
  },
  stats: {
    type: Object,
    required: true
  }
});

// State Modal Tambah/Edit Baki
const isModalOpen = ref(false);
const isEditing = ref(false);
const editingTrayId = ref(null);

const trayForm = useForm({
  code: '',
  name: '',
  showcase_location: '',
  category_id: '',
  gold_rate_id: '',
  capacity: 24,
  status: 'active',
  notes: '',
});

// State Modal Pindah Baki Cepat
const isMoveModalOpen = ref(false);
const moveForm = useForm({
  item_sku: '',
  to_tray_id: '',
  reason: 'Pindah etalase kasir',
});

const openCreateModal = () => {
  isEditing.value = false;
  editingTrayId.value = null;
  trayForm.reset();
  trayForm.capacity = 24;
  trayForm.status = 'active';
  isModalOpen.value = true;
};

const openEditModal = (tray) => {
  isEditing.value = true;
  editingTrayId.value = tray.id;
  trayForm.code = tray.code;
  trayForm.name = tray.name;
  trayForm.showcase_location = tray.showcase_location || '';
  trayForm.category_id = tray.category_id || '';
  trayForm.gold_rate_id = tray.gold_rate_id || '';
  trayForm.capacity = tray.capacity;
  trayForm.status = tray.status;
  trayForm.notes = tray.notes || '';
  isModalOpen.value = true;
};

const submitTrayForm = () => {
  if (isEditing.value) {
    trayForm.put(route('admin.trays.update', editingTrayId.value), {
      onSuccess: () => { isModalOpen.value = false; },
    });
  } else {
    trayForm.post(route('admin.trays.store'), {
      onSuccess: () => { isModalOpen.value = false; },
    });
  }
};

const submitMoveForm = () => {
  moveForm.post(route('admin.trays.move-item'), {
    onSuccess: () => {
      isMoveModalOpen.value = false;
      moveForm.reset();
    },
  });
};

const deleteTray = (tray) => {
  if (confirm(`Apakah Anda yakin ingin menghapus baki ${tray.code}?`)) {
    router.delete(route('admin.trays.destroy', tray.id));
  }
};

const formatWeight = (val) => {
  return parseFloat(val || 0).toFixed(3) + ' gr';
};
</script>

<template>
  <Head title="Manajemen Baki Etalase - e-Emas" />

  <AdminLayout>
    <div class="space-y-8">
      <!-- Title & Actions Bar -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200 dark:border-slate-800">
        <div>
          <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-700 dark:text-amber-300 text-xs font-semibold mb-1">
            <Sparkles class="w-3.5 h-3.5 text-amber-500 dark:text-amber-400" />
            <span>Manajemen Display Fisik Etalase</span>
          </div>
          <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">Baki & Nampan Perhiasan</h2>
          <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
            Pantau kapasitas slot fisik baki, audit stok per nampan, dan pindahkan perhiasan antar etalase.
          </p>
        </div>

        <div class="flex items-center gap-3">
          <button 
            @click="isMoveModalOpen = true"
            class="px-4 py-2.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 hover:border-amber-500 text-slate-800 dark:text-slate-200 text-xs font-bold transition-all shadow-sm flex items-center gap-2 cursor-pointer"
          >
            <ArrowRightLeft class="w-4 h-4 text-amber-600 dark:text-amber-400" />
            <span>Pindah Baki Cepat</span>
          </button>

          <button 
            @click="openCreateModal"
            class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 text-xs font-bold shadow-md shadow-amber-500/20 transition-all flex items-center gap-2 cursor-pointer"
          >
            <Plus class="w-4 h-4" />
            <span>Tambah Baki Baru</span>
          </button>
        </div>
      </div>

      <!-- Stats Summary Strip -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-sm">
          <span class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider font-semibold">Total Baki Aktif</span>
          <h3 class="text-2xl font-black text-slate-900 dark:text-white mt-1">{{ stats.total_trays }} Baki</h3>
        </div>
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-sm">
          <span class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider font-semibold">Total Perhiasan di Baki</span>
          <h3 class="text-2xl font-black text-slate-900 dark:text-white mt-1">{{ stats.total_items_in_trays }} Pcs</h3>
        </div>
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-sm">
          <span class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider font-semibold">Total Gramasi Fisik</span>
          <h3 class="text-2xl font-black text-amber-700 dark:text-amber-300 font-mono mt-1">{{ formatWeight(stats.total_weight_in_trays) }}</h3>
        </div>
      </div>

      <!-- Grid Visual Baki Etalase -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div 
          v-for="tray in trays" 
          :key="tray.id"
          class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:border-amber-500/50 rounded-2xl p-5 shadow-sm transition-all group flex flex-col justify-between"
        >
          <div>
            <!-- Header Baki -->
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
              <span class="font-mono text-xs font-bold text-amber-700 dark:text-amber-400 bg-amber-500/10 px-2.5 py-1 rounded-md border border-amber-500/20">
                {{ tray.code }}
              </span>
              <span class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-1">
                <Store class="w-3.5 h-3.5" />
                {{ tray.showcase_location || 'Etalase' }}
              </span>
            </div>

            <!-- Nama & Kategori -->
            <h4 class="font-bold text-slate-900 dark:text-white text-base mt-3 group-hover:text-amber-600 dark:group-hover:text-amber-300 transition-colors">
              {{ tray.name }}
            </h4>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
              Kategori: <span class="font-medium text-slate-800 dark:text-slate-200">{{ tray.category ? tray.category.name : 'Semua Kategori' }}</span>
              <span v-if="tray.gold_rate" class="ml-2 font-mono text-amber-600 dark:text-amber-400 font-bold">({{ tray.gold_rate.code }})</span>
            </p>

            <!-- Metrics Bobot & Item -->
            <div class="grid grid-cols-2 gap-3 mt-4 pt-3 border-t border-slate-100 dark:border-slate-800/80 text-xs">
              <div class="bg-slate-50 dark:bg-slate-950/60 p-2.5 rounded-xl border border-slate-200 dark:border-slate-800">
                <span class="text-slate-400 text-[11px] block">Slot Terisi:</span>
                <span class="font-bold text-slate-900 dark:text-white text-sm">{{ tray.total_items }} / {{ tray.capacity }} slot</span>
              </div>
              <div class="bg-slate-50 dark:bg-slate-950/60 p-2.5 rounded-xl border border-slate-200 dark:border-slate-800">
                <span class="text-slate-400 text-[11px] block">Total Bobot:</span>
                <span class="font-bold text-amber-700 dark:text-amber-300 text-sm font-mono">{{ formatWeight(tray.total_weight) }}</span>
              </div>
            </div>

            <!-- Progress Slot Kapasitas -->
            <div class="mt-4">
              <div class="flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400 mb-1">
                <span>Kapasitas Baki Fisik</span>
                <span class="font-bold">{{ Math.round((tray.total_items / (tray.capacity || 1)) * 100) }}%</span>
              </div>
              <div class="w-full bg-slate-100 dark:bg-slate-950 rounded-full h-2 overflow-hidden">
                <div 
                  class="bg-gradient-to-r from-amber-500 to-yellow-400 h-2 rounded-full transition-all duration-500" 
                  :style="{ width: Math.min(100, Math.round((tray.total_items / (tray.capacity || 1)) * 100)) + '%' }"
                ></div>
              </div>
            </div>
          </div>

          <!-- Bottom Action Buttons -->
          <div class="mt-5 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
            <Link 
              :href="route('admin.trays.show', tray.id)"
              class="font-bold text-amber-700 dark:text-amber-400 hover:underline flex items-center gap-1"
            >
              <Eye class="w-3.5 h-3.5" /> Lihat Isi Baki
            </Link>

            <div class="flex items-center gap-2">
              <button 
                @click="openEditModal(tray)"
                class="p-1.5 text-slate-400 hover:text-slate-700 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors cursor-pointer"
                title="Edit Baki"
              >
                <Edit3 class="w-4 h-4" />
              </button>
              <button 
                v-if="tray.total_items === 0"
                @click="deleteTray(tray)"
                class="p-1.5 text-slate-400 hover:text-rose-500 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors cursor-pointer"
                title="Hapus Baki Kosong"
              >
                <Trash2 class="w-4 h-4" />
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Form Tambah/Edit Baki -->
    <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
      <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl relative">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
          <h3 class="text-lg font-bold text-slate-900 dark:text-white">
            {{ isEditing ? 'Edit Baki Etalase' : 'Tambah Baki Etalase Baru' }}
          </h3>
          <button @click="isModalOpen = false" class="p-1.5 text-slate-400 hover:text-slate-700 dark:hover:text-white rounded-lg">
            <X class="w-5 h-5" />
          </button>
        </div>

        <form @submit.prevent="submitTrayForm" class="space-y-4 mt-4">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Kode Baki (Unik)</label>
              <input 
                type="text" 
                v-model="trayForm.code"
                placeholder="cth: BK-CNC-03" 
                required
                class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-950 border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white py-2 px-3 focus:border-amber-500 focus:ring-amber-500"
              />
            </div>
            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Kapasitas Slot</label>
              <input 
                type="number" 
                v-model="trayForm.capacity"
                min="1" 
                max="200"
                required
                class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-950 border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white py-2 px-3 focus:border-amber-500 focus:ring-amber-500"
              />
            </div>
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Baki</label>
            <input 
              type="text" 
              v-model="trayForm.name"
              placeholder="cth: Baki Cincin Berlian Pria" 
              required
              class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-950 border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white py-2 px-3 focus:border-amber-500 focus:ring-amber-500"
            />
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Lokasi Etalase Toko</label>
            <input 
              type="text" 
              v-model="trayForm.showcase_location"
              placeholder="cth: Etalase Utama Depan Baris A" 
              class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-950 border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white py-2 px-3 focus:border-amber-500 focus:ring-amber-500"
            />
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Kategori Perhiasan</label>
              <select 
                v-model="trayForm.category_id"
                class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-950 border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white py-2 px-3 focus:border-amber-500 focus:ring-amber-500"
              >
                <option value="">Semua Kategori</option>
                <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Khusus Kadar (Opsional)</label>
              <select 
                v-model="trayForm.gold_rate_id"
                class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-950 border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white py-2 px-3 focus:border-amber-500 focus:ring-amber-500"
              >
                <option value="">Campuran</option>
                <option v-for="rate in goldRates" :key="rate.id" :value="rate.id">{{ rate.name }}</option>
              </select>
            </div>
          </div>

          <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
            <button 
              type="button" 
              @click="isModalOpen = false"
              class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 cursor-pointer"
            >
              Batal
            </button>
            <button 
              type="submit"
              :disabled="trayForm.processing"
              class="px-5 py-2 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 text-slate-950 font-bold text-xs shadow-md shadow-amber-500/20 active:scale-95 transition-all cursor-pointer"
            >
              Simpan Baki
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal Form Pindah Baki Cepat via Barcode SKU -->
    <div v-if="isMoveModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
      <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl relative">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
          <div class="flex items-center gap-2">
            <ArrowRightLeft class="w-5 h-5 text-amber-500" />
            <h3 class="text-base font-bold text-slate-900 dark:text-white">Pindah Perhiasan Antar Baki</h3>
          </div>
          <button @click="isMoveModalOpen = false" class="p-1.5 text-slate-400 hover:text-slate-700 dark:hover:text-white rounded-lg">
            <X class="w-5 h-5" />
          </button>
        </div>

        <form @submit.prevent="submitMoveForm" class="space-y-4 mt-4">
          <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Scan Barcode / Input SKU Barang</label>
            <div class="relative">
              <input 
                type="text" 
                v-model="moveForm.item_sku"
                placeholder="Scan barcode perhiasan (cth: EMAS-202610-001)"
                required
                autofocus
                class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-950 border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white py-2.5 px-3 focus:border-amber-500 focus:ring-amber-500 font-mono"
              />
              <QrCode class="w-4 h-4 text-amber-500 absolute right-3 top-3" />
            </div>
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Pilih Baki Tujuan</label>
            <select 
              v-model="moveForm.to_tray_id"
              required
              class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-950 border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white py-2 px-3 focus:border-amber-500 focus:ring-amber-500"
            >
              <option value="" disabled>-- Pilih Baki Tujuan --</option>
              <option v-for="t in trays" :key="t.id" :value="t.id">
                {{ t.code }} - {{ t.name }} ({{ t.total_items }}/{{ t.capacity }} slot)
              </option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Alasan Pemindahan</label>
            <input 
              type="text" 
              v-model="moveForm.reason"
              placeholder="cth: Penataan ulang etalase"
              class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-950 border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white py-2 px-3 focus:border-amber-500 focus:ring-amber-500"
            />
          </div>

          <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
            <button 
              type="button" 
              @click="isMoveModalOpen = false"
              class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 cursor-pointer"
            >
              Batal
            </button>
            <button 
              type="submit"
              :disabled="moveForm.processing"
              class="px-5 py-2 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 text-slate-950 font-bold text-xs shadow-md shadow-amber-500/20 active:scale-95 transition-all cursor-pointer"
            >
              Pindahkan Sekarang
            </button>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>
