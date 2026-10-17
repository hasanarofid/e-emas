<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { 
  ArrowRightLeft, 
  Search, 
  Plus, 
  Layers, 
  Gem, 
  Scale, 
  Calendar, 
  UserCheck, 
  X, 
  Check 
} from 'lucide-vue-next';

const props = defineProps({
  mutations: {
    type: Object,
    required: true,
  },
  trays: {
    type: Array,
    default: () => [],
  },
  items: {
    type: Array,
    default: () => [],
  },
  filters: {
    type: Object,
    default: () => ({}),
  },
});

const search = ref(props.filters.search || '');
const isModalOpen = ref(false);

const form = useForm({
  item_id: '',
  from_tray_id: '',
  to_tray_id: '',
  reason: 'Pemindahan display etalase',
});

const onItemSelect = () => {
  const selected = props.items.find(i => i.id === form.item_id);
  if (selected) {
    form.from_tray_id = selected.tray_id || '';
  }
};

const handleSearch = () => {
  router.get(route('admin.inventory.mutations'), { search: search.value }, { preserveState: true, replace: true });
};

const openModal = () => {
  form.reset();
  form.reason = 'Pemindahan display etalase';
  isModalOpen.value = true;
};

const closeModal = () => {
  isModalOpen.value = false;
  form.reset();
};

const submitForm = () => {
  form.post(route('admin.trays.move-item'), {
    onSuccess: () => closeModal(),
  });
};
</script>

<template>
  <AdminLayout>
    <Head title="Mutasi Barang Antar Baki - POS Emas" />

    <div class="space-y-6">
      <!-- Header Section -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2 text-xs font-mono text-amber-600 dark:text-amber-400 uppercase tracking-widest">
            <ArrowRightLeft class="w-4 h-4" />
            <span>Persediaan Barang</span>
          </div>
          <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white mt-1">
            Mutasi Barang Antar Baki
          </h2>
          <p class="text-sm text-slate-500 dark:text-slate-400">
            Riwayat log dan mutasi perpindahan perhiasan dari satu baki/etalase ke baki lainnya.
          </p>
        </div>

        <button 
          @click="openModal"
          class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-semibold text-sm shadow-sm shadow-amber-500/20 transition-all cursor-pointer"
        >
          <Plus class="w-4 h-4" />
          <span>Pindah Baki Perhiasan</span>
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
            placeholder="Cari SKU atau nama perhiasan..."
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
                <th class="px-6 py-4">Waktu Mutasi</th>
                <th class="px-6 py-4">Perhiasan (SKU & Nama)</th>
                <th class="px-6 py-4">Kadar / Kategori</th>
                <th class="px-6 py-4">Baki Asal</th>
                <th class="px-6 py-4">Baki Tujuan</th>
                <th class="px-6 py-4">Alasan</th>
                <th class="px-6 py-4 text-right">Petugas</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
              <tr v-for="mut in mutations.data" :key="mut.id" class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition-colors">
                <td class="px-6 py-4 font-mono text-xs text-slate-500">
                  <span class="flex items-center gap-1.5">
                    <Calendar class="w-3.5 h-3.5 text-slate-400" />
                    {{ new Date(mut.created_at).toLocaleString('id-ID') }}
                  </span>
                </td>
                <td class="px-6 py-4">
                  <div class="font-medium text-slate-900 dark:text-white">
                    {{ mut.item?.name }}
                  </div>
                  <div class="font-mono text-xs text-amber-600 dark:text-amber-400">
                    {{ mut.item?.sku }}
                  </div>
                </td>
                <td class="px-6 py-4 text-xs">
                  <span class="px-2 py-0.5 rounded bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-500/20 font-mono">
                    {{ mut.item?.gold_rate?.code }}
                  </span>
                  <span class="ml-1.5 text-slate-500">{{ mut.item?.category?.name }}</span>
                </td>
                <td class="px-6 py-4">
                  <span v-if="mut.from_tray" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-xs font-mono">
                    <Layers class="w-3 h-3 text-slate-400" />
                    {{ mut.from_tray.code }} ({{ mut.from_tray.name }})
                  </span>
                  <span v-else class="text-xs text-slate-400 italic">Pemasukan Awal</span>
                </td>
                <td class="px-6 py-4">
                  <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-500/20 text-xs font-mono font-medium">
                    <Layers class="w-3 h-3 text-amber-500" />
                    {{ mut.to_tray?.code }} ({{ mut.to_tray?.name }})
                  </span>
                </td>
                <td class="px-6 py-4 text-xs text-slate-500 dark:text-slate-400">
                  {{ mut.reason || '-' }}
                </td>
                <td class="px-6 py-4 text-right text-xs text-slate-600 dark:text-slate-300">
                  <span class="inline-flex items-center gap-1">
                    <UserCheck class="w-3.5 h-3.5 text-slate-400" />
                    {{ mut.user?.name || 'Sistem' }}
                  </span>
                </td>
              </tr>
              <tr v-if="mutations.data.length === 0">
                <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                  Belum ada log mutasi baki perhiasan.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Modal Pindah Baki -->
    <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
      <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl max-w-lg w-full p-6 shadow-xl space-y-5">
        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-4">
          <h3 class="text-lg font-bold text-slate-900 dark:text-white">
            Pindahkan Perhiasan ke Baki Baru
          </h3>
          <button @click="closeModal" class="p-1.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-lg">
            <X class="w-5 h-5" />
          </button>
        </div>

        <form @submit.prevent="submitForm" class="space-y-4">
          <div>
            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase mb-1">
              Pilih Perhiasan *
            </label>
            <select 
              v-model="form.item_id"
              @change="onItemSelect"
              required
              class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500"
            >
              <option value="" disabled>-- Pilih perhiasan yang akan dipindah --</option>
              <option v-for="item in items" :key="item.id" :value="item.id">
                [{{ item.sku }}] {{ item.name }} ({{ item.gross_weight }} gr)
              </option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase mb-1">
              Baki Tujuan Baru *
            </label>
            <select 
              v-model="form.to_tray_id"
              required
              class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500"
            >
              <option value="" disabled>-- Pilih Baki Tujuan --</option>
              <option v-for="tray in trays" :key="tray.id" :value="tray.id">
                {{ tray.code }} - {{ tray.name }} ({{ tray.total_items }}/{{ tray.capacity }})
              </option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase mb-1">
              Alasan Mutasi
            </label>
            <input 
              v-model="form.reason"
              type="text" 
              placeholder="Contoh: Rotasi display etalase depan"
              class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500"
            />
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
              Proses Mutasi
            </button>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>
