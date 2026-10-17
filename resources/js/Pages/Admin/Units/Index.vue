<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { 
  Scale, 
  Plus, 
  Search, 
  Edit3, 
  Trash2, 
  X, 
  Package
} from 'lucide-vue-next';

const props = defineProps({
  units: {
    type: Object,
    required: true,
  },
  filters: {
    type: Object,
    default: () => ({}),
  },
});

const search = ref(props.filters.search || '');
const isModalOpen = ref(false);
const isEditing = ref(false);
const editingUnitId = ref(null);

const form = useForm({
  name: '',
  symbol: '',
  description: '',
  is_active: true,
});

const handleSearch = () => {
  router.get(route('admin.units.index'), { search: search.value }, { preserveState: true, replace: true });
};

const openCreateModal = () => {
  isEditing.value = false;
  editingUnitId.value = null;
  form.reset();
  form.is_active = true;
  isModalOpen.value = true;
};

const openEditModal = (unit) => {
  isEditing.value = true;
  editingUnitId.value = unit.id;
  form.name = unit.name;
  form.symbol = unit.symbol;
  form.description = unit.description || '';
  form.is_active = Boolean(unit.is_active);
  isModalOpen.value = true;
};

const closeModal = () => {
  isModalOpen.value = false;
  form.reset();
};

const submitForm = () => {
  if (isEditing.value) {
    form.put(route('admin.units.update', editingUnitId.value), {
      onSuccess: () => closeModal(),
    });
  } else {
    form.post(route('admin.units.store'), {
      onSuccess: () => closeModal(),
    });
  }
};

const deleteUnit = (unit) => {
  if (confirm(`Hapus satuan "${unit.name}"?`)) {
    router.delete(route('admin.units.destroy', unit.id));
  }
};
</script>

<template>
  <AdminLayout>
    <Head title="Master Satuan Barang - POS Emas" />

    <div class="space-y-6">
      <!-- Header Section -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2 text-xs font-mono text-amber-600 dark:text-amber-400 uppercase tracking-widest">
            <Scale class="w-4 h-4" />
            <span>Master Persediaan</span>
          </div>
          <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white mt-1">
            Satuan Barang (UoM)
          </h2>
          <p class="text-sm text-slate-500 dark:text-slate-400">
            Kelola satuan ukuran inventaris (Gram, Pieces, Butir, Set).
          </p>
        </div>

        <button 
          @click="openCreateModal"
          class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-semibold text-sm shadow-sm shadow-amber-500/20 transition-all cursor-pointer"
        >
          <Plus class="w-4 h-4" />
          <span>Tambah Satuan Baru</span>
        </button>
      </div>

      <!-- Search & Filter Bar -->
      <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between gap-4">
        <div class="relative flex-1 max-w-md">
          <Search class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
          <input 
            v-model="search"
            @keyup.enter="handleSearch"
            type="text" 
            placeholder="Cari nama atau simbol satuan..."
            class="w-full pl-10 pr-4 py-2 bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-colors"
          />
        </div>
      </div>

      <!-- Table Section -->
      <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 dark:bg-slate-950/50 text-slate-500 dark:text-slate-400 text-xs uppercase font-mono tracking-wider border-b border-slate-200 dark:border-slate-800">
              <tr>
                <th class="px-6 py-4">Simbol</th>
                <th class="px-6 py-4">Nama Satuan</th>
                <th class="px-6 py-4">Deskripsi</th>
                <th class="px-6 py-4 text-center">Jumlah Barang</th>
                <th class="px-6 py-4 text-center">Status</th>
                <th class="px-6 py-4 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
              <tr v-for="unit in units.data" :key="unit.id" class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition-colors">
                <td class="px-6 py-4 font-mono font-bold text-amber-600 dark:text-amber-400">
                  {{ unit.symbol }}
                </td>
                <td class="px-6 py-4 font-medium text-slate-900 dark:text-white">
                  {{ unit.name }}
                </td>
                <td class="px-6 py-4 text-slate-500 dark:text-slate-400 max-w-xs truncate">
                  {{ unit.description || '-' }}
                </td>
                <td class="px-6 py-4 text-center">
                  <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                    <Package class="w-3.5 h-3.5 text-amber-500" />
                    {{ unit.items_count }} item
                  </span>
                </td>
                <td class="px-6 py-4 text-center">
                  <span 
                    :class="[
                      unit.is_active ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20' : 'bg-slate-500/10 text-slate-500 border-slate-500/20',
                      'inline-block px-2.5 py-0.5 rounded-full text-xs font-medium border'
                    ]"
                  >
                    {{ unit.is_active ? 'Aktif' : 'Non-Aktif' }}
                  </span>
                </td>
                <td class="px-6 py-4 text-right space-x-2">
                  <button 
                    @click="openEditModal(unit)"
                    class="p-1.5 text-slate-500 hover:text-amber-600 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors cursor-pointer"
                    title="Edit Satuan"
                  >
                    <Edit3 class="w-4 h-4" />
                  </button>
                  <button 
                    @click="deleteUnit(unit)"
                    class="p-1.5 text-slate-500 hover:text-rose-600 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors cursor-pointer"
                    title="Hapus Satuan"
                  >
                    <Trash2 class="w-4 h-4" />
                  </button>
                </td>
              </tr>
              <tr v-if="units.data.length === 0">
                <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                  Belum ada satuan barang yang terdaftar.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Modal Form Unit -->
    <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
      <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl max-w-lg w-full p-6 shadow-xl space-y-5">
        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-4">
          <h3 class="text-lg font-bold text-slate-900 dark:text-white">
            {{ isEditing ? 'Edit Satuan Barang' : 'Tambah Satuan Baru' }}
          </h3>
          <button @click="closeModal" class="p-1.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-lg">
            <X class="w-5 h-5" />
          </button>
        </div>

        <form @submit.prevent="submitForm" class="space-y-4">
          <div>
            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase mb-1">
              Simbol Satuan (Singkatan) *
            </label>
            <input 
              v-model="form.symbol"
              type="text" 
              placeholder="Contoh: gr, pcs, btr"
              required
              class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm font-mono focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 lowercase"
            />
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase mb-1">
              Nama Satuan Lengkap *
            </label>
            <input 
              v-model="form.name"
              type="text" 
              placeholder="Contoh: Gram, Pieces, Butir"
              required
              class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500"
            />
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase mb-1">
              Deskripsi Penggunaan
            </label>
            <textarea 
              v-model="form.description"
              rows="3"
              placeholder="Keterangan peruntukan satuan..."
              class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500"
            ></textarea>
          </div>

          <div class="flex items-center gap-2">
            <input 
              v-model="form.is_active"
              id="unit_is_active"
              type="checkbox" 
              class="w-4 h-4 rounded text-amber-500 focus:ring-amber-500 border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950"
            />
            <label for="unit_is_active" class="text-sm font-medium text-slate-700 dark:text-slate-300">
              Satuan Aktif dalam Sistem
            </label>
          </div>

          <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200 dark:border-slate-800">
            <button 
              type="button" 
              @click="closeModal"
              class="px-4 py-2 text-sm text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl font-medium"
            >
              Batal
            </button>
            <button 
              type="submit"
              :disabled="form.processing"
              class="px-5 py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 font-semibold text-sm rounded-xl shadow-sm transition-all"
            >
              {{ isEditing ? 'Simpan Perubahan' : 'Tambah Satuan' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>
