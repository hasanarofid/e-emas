<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { 
  Layers, 
  ArrowLeft, 
  Scale, 
  Sparkles, 
  Gem, 
  Printer, 
  ArrowRightLeft, 
  Store,
  CheckCircle,
  X
} from 'lucide-vue-next';

const props = defineProps({
  tray: {
    type: Object,
    required: true
  },
  items: {
    type: Array,
    default: () => []
  },
  availableTrays: {
    type: Array,
    default: () => []
  }
});

const isMoveModalOpen = ref(false);
const selectedItem = ref(null);

const moveForm = useForm({
  item_sku: '',
  to_tray_id: '',
  reason: 'Dipindahkan dari detail baki',
});

const openMoveModal = (item) => {
  selectedItem.value = item;
  moveForm.item_sku = item.sku;
  moveForm.to_tray_id = '';
  isMoveModalOpen.value = true;
};

const submitMove = () => {
  moveForm.post(route('admin.trays.move-item'), {
    onSuccess: () => {
      isMoveModalOpen.value = false;
    }
  });
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
  <Head :title="`Detail Baki ${tray.code} - POS Emas`" />

  <AdminLayout>
    <div class="space-y-8">
      <!-- Back & Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200 dark:border-slate-800">
        <div class="flex items-center gap-3">
          <Link 
            :href="route('admin.trays.index')"
            class="p-2.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors"
          >
            <ArrowLeft class="w-4 h-4" />
          </Link>
          <div>
            <div class="flex items-center gap-2">
              <span class="font-mono text-xs font-bold text-amber-700 dark:text-amber-400 bg-amber-500/10 px-2.5 py-0.5 rounded border border-amber-500/20">
                {{ tray.code }}
              </span>
              <h2 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white">{{ tray.name }}</h2>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 flex items-center gap-1">
              <Store class="w-3.5 h-3.5" />
              Lokasi: {{ tray.showcase_location || 'Etalase Utama' }}
            </p>
          </div>
        </div>

        <!-- Metrics Strip -->
        <div class="flex items-center gap-3 text-xs">
          <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 px-4 py-2 rounded-xl shadow-sm">
            <span class="text-slate-400 block text-[10px]">Isi Slot:</span>
            <span class="font-bold text-slate-900 dark:text-white">{{ tray.total_items }} / {{ tray.capacity }} Pcs</span>
          </div>
          <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 px-4 py-2 rounded-xl shadow-sm">
            <span class="text-slate-400 block text-[10px]">Total Berat:</span>
            <span class="font-mono font-bold text-amber-700 dark:text-amber-300">{{ formatWeight(tray.total_weight) }}</span>
          </div>
        </div>
      </div>

      <!-- Denah Visual Slot Baki (Jewelry Grid Layout) -->
      <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
          <div>
            <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
              <Layers class="w-4 h-4 text-amber-500" />
              Denah Fisik Slot Nampan ({{ tray.capacity }} Kotak)
            </h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Visual representasi fisik perhiasan yang diletakkan pada baki ini.</p>
          </div>
          <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
            <CheckCircle class="w-3.5 h-3.5" /> {{ tray.total_items }} Terisi
          </span>
        </div>

        <div class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 lg:grid-cols-12 gap-3 mt-6">
          <div 
            v-for="index in tray.capacity" 
            :key="index"
            class="aspect-square rounded-xl border flex flex-col items-center justify-center p-1 text-center transition-all relative group"
            :class="[
              items[index - 1] 
                ? 'bg-amber-500/10 border-amber-500/40 text-amber-700 dark:text-amber-300 shadow-sm' 
                : 'bg-slate-50 dark:bg-slate-950/60 border-dashed border-slate-200 dark:border-slate-800 text-slate-400'
            ]"
          >
            <template v-if="items[index - 1]">
              <Gem class="w-5 h-5 text-amber-500 mb-0.5" />
              <span class="text-[9px] font-mono font-bold truncate max-w-[50px] block leading-tight">{{ items[index - 1].gold_rate?.code }}</span>
              <span class="text-[8px] font-mono text-slate-500 dark:text-slate-400">{{ parseFloat(items[index - 1].net_weight).toFixed(2) }}g</span>

              <!-- Hover Tooltip -->
              <div class="absolute bottom-full mb-2 hidden group-hover:block z-30 bg-slate-950 text-white text-[10px] rounded-lg p-2 shadow-xl whitespace-nowrap border border-slate-800 pointer-events-none">
                <p class="font-bold">{{ items[index - 1].name }}</p>
                <p class="text-amber-400 font-mono">{{ items[index - 1].sku }} | {{ formatWeight(items[index - 1].net_weight) }}</p>
              </div>
            </template>
            <template v-else>
              <span class="text-[10px] font-mono text-slate-400 dark:text-slate-600">{{ index }}</span>
            </template>
          </div>
        </div>
      </div>

      <!-- Daftar List Perhiasan di Baki -->
      <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
        <h3 class="text-base font-bold text-slate-900 dark:text-white pb-4 border-b border-slate-100 dark:border-slate-800">
          Daftar Rincian Perhiasan di Baki Ini ({{ items.length }} Pcs)
        </h3>

        <div class="overflow-x-auto mt-4">
          <table class="w-full text-left text-xs">
            <thead class="text-slate-500 dark:text-slate-400 border-b border-slate-100 dark:border-slate-800 uppercase tracking-wider text-[10px]">
              <tr>
                <th class="py-3 px-3">Kode SKU / Barcode</th>
                <th class="py-3 px-3">Nama Perhiasan</th>
                <th class="py-3 px-3">Kadar / Karat</th>
                <th class="py-3 px-3">Berat Kotor</th>
                <th class="py-3 px-3">Berat Bersih</th>
                <th class="py-3 px-3 text-right">Estimasi Harga</th>
                <th class="py-3 px-3 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 text-slate-700 dark:text-slate-300">
              <tr v-for="item in items" :key="item.id" class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                <td class="py-3 px-3 font-mono font-bold text-amber-700 dark:text-amber-400">{{ item.sku }}</td>
                <td class="py-3 px-3 font-semibold text-slate-900 dark:text-white">{{ item.name }}</td>
                <td class="py-3 px-3">{{ item.gold_rate ? item.gold_rate.name : '-' }}</td>
                <td class="py-3 px-3 font-mono">{{ formatWeight(item.gross_weight) }}</td>
                <td class="py-3 px-3 font-mono font-bold text-amber-700 dark:text-amber-300">{{ formatWeight(item.net_weight) }}</td>
                <td class="py-3 px-3 text-right font-mono font-bold text-slate-900 dark:text-white">{{ formatRupiah(item.total_price) }}</td>
                <td class="py-3 px-3 text-right">
                  <div class="flex items-center justify-end gap-2">
                    <button 
                      @click="openMoveModal(item)"
                      class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-amber-500 hover:text-slate-950 text-slate-700 dark:text-slate-300 font-medium text-[11px] transition-colors flex items-center gap-1 cursor-pointer"
                      title="Pindahkan ke Baki Lain"
                    >
                      <ArrowRightLeft class="w-3 h-3" /> Pindah
                    </button>
                    <Link 
                      :href="route('admin.items.print-tag', item.id)"
                      target="_blank"
                      class="p-1 rounded-lg bg-slate-100 dark:bg-slate-800 hover:text-amber-600 dark:hover:text-amber-400 transition-colors"
                      title="Cetak Barcode Tag"
                    >
                      <Printer class="w-3.5 h-3.5" />
                    </Link>
                  </div>
                </td>
              </tr>
              <tr v-if="items.length === 0">
                <td colspan="7" class="py-8 text-center text-slate-400">
                  Belum ada perhiasan yang diletakkan pada baki ini.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Modal Pindah Baki Satuan -->
    <div v-if="isMoveModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
      <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl relative">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
          <div class="flex items-center gap-2">
            <ArrowRightLeft class="w-5 h-5 text-amber-500" />
            <h3 class="text-base font-bold text-slate-900 dark:text-white">Pindahkan Perhiasan</h3>
          </div>
          <button @click="isMoveModalOpen = false" class="p-1.5 text-slate-400 hover:text-slate-700 dark:hover:text-white rounded-lg">
            <X class="w-5 h-5" />
          </button>
        </div>

        <form @submit.prevent="submitMove" class="space-y-4 mt-4 text-xs">
          <div class="bg-slate-50 dark:bg-slate-950 p-3 rounded-xl border border-slate-200 dark:border-slate-800">
            <p class="font-bold text-slate-900 dark:text-white">{{ selectedItem?.name }}</p>
            <p class="text-amber-600 dark:text-amber-400 font-mono">{{ selectedItem?.sku }} | {{ formatWeight(selectedItem?.net_weight) }}</p>
          </div>

          <div>
            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Pilih Baki Tujuan</label>
            <select 
              v-model="moveForm.to_tray_id"
              required
              class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-950 border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white py-2 px-3 focus:border-amber-500 focus:ring-amber-500"
            >
              <option value="" disabled>-- Pilih Baki Tujuan --</option>
              <option v-for="t in availableTrays" :key="t.id" :value="t.id">
                {{ t.code }} - {{ t.name }} ({{ t.total_items }}/{{ t.capacity }} slot)
              </option>
            </select>
          </div>

          <div>
            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Alasan Pemindahan</label>
            <input 
              type="text" 
              v-model="moveForm.reason"
              placeholder="cth: Ganti baki etalase"
              class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-950 border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white py-2 px-3 focus:border-amber-500 focus:ring-amber-500"
            />
          </div>

          <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
            <button 
              type="button" 
              @click="isMoveModalOpen = false"
              class="px-4 py-2 rounded-xl text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 cursor-pointer"
            >
              Batal
            </button>
            <button 
              type="submit"
              :disabled="moveForm.processing"
              class="px-5 py-2 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 text-slate-950 font-bold shadow-md shadow-amber-500/20 active:scale-95 transition-all cursor-pointer"
            >
              Pindahkan
            </button>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>
