<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { 
  ArrowDownLeft, 
  ArrowUpRight, 
  Search, 
  Plus, 
  Building2, 
  Calendar, 
  UserCheck, 
  X, 
  Filter,
  CheckCircle2
} from 'lucide-vue-next';

const props = defineProps({
  movements: {
    type: Object,
    required: true,
  },
  warehouses: {
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
const typeFilter = ref(props.filters.type || '');
const isModalOpen = ref(false);

const form = useForm({
  item_id: '',
  type: 'out',
  reason: 'melted',
  warehouse_id: '',
  notes: '',
});

const handleFilter = () => {
  router.get(route('admin.inventory.movements'), { 
    search: search.value, 
    type: typeFilter.value 
  }, { preserveState: true, replace: true });
};

const openModal = () => {
  form.reset();
  form.type = 'out';
  form.reason = 'melted';
  isModalOpen.value = true;
};

const closeModal = () => {
  isModalOpen.value = false;
  form.reset();
};

const submitForm = () => {
  form.post(route('admin.inventory.movements.store'), {
    onSuccess: () => closeModal(),
  });
};

const formatReason = (reason) => {
  const map = {
    'purchase_supplier': 'Pembelian Supplier',
    'manual_in': 'Input Stok Awal',
    'buyback_in': 'Masuk dari Buyback',
    'melted': 'Dilebur Jadi Emas Murni',
    'repair_out': 'Kirim Servis Tukang',
    'repair_in': 'Kembali dari Tukang Servis',
    'damaged_loss': 'Susut / Rusak Fisik',
    'adjustment_out': 'Penyesuaian Opname',
    'other': 'Lain-lain',
  };
  return map[reason] || reason;
};
</script>

<template>
  <AdminLayout>
    <Head title="Keluar Masuk Barang - POS Emas" />

    <div class="space-y-6">
      <!-- Header Section -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2 text-xs font-mono text-amber-600 dark:text-amber-400 uppercase tracking-widest">
            <ArrowDownLeft class="w-4 h-4" />
            <span>Persediaan Barang</span>
          </div>
          <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white mt-1">
            Keluar Masuk Barang (Non-Penjualan)
          </h2>
          <p class="text-sm text-slate-500 dark:text-slate-400">
            Pencatatan mutasi fisik perhiasan: peleburan ke batangan, kirim reparasi ke tukang, retur, atau penyesuaian khazanah.
          </p>
        </div>

        <button 
          @click="openModal"
          class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-semibold text-sm shadow-sm shadow-amber-500/20 transition-all cursor-pointer"
        >
          <Plus class="w-4 h-4" />
          <span>Catat Mutasi Masuk/Keluar</span>
        </button>
      </div>

      <!-- Search & Filter Bar -->
      <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="relative flex-1 w-full max-w-md">
          <Search class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
          <input 
            v-model="search"
            @keyup.enter="handleFilter"
            type="text" 
            placeholder="Cari No. Referensi atau SKU barang..."
            class="w-full pl-10 pr-4 py-2 bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-colors"
          />
        </div>

        <div class="flex items-center gap-3 w-full sm:w-auto">
          <select 
            v-model="typeFilter"
            @change="handleFilter"
            class="px-3.5 py-2 bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500"
          >
            <option value="">Semua Arah Gerak</option>
            <option value="in">Barang Masuk (IN)</option>
            <option value="out">Barang Keluar (OUT)</option>
          </select>
        </div>
      </div>

      <!-- Table Section -->
      <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 dark:bg-slate-950/50 text-slate-500 dark:text-slate-400 text-xs uppercase font-mono tracking-wider border-b border-slate-200 dark:border-slate-800">
              <tr>
                <th class="px-6 py-4">No. Referensi</th>
                <th class="px-6 py-4">Arah Gerak</th>
                <th class="px-6 py-4">Perhiasan</th>
                <th class="px-6 py-4">Alasan Pergerakan</th>
                <th class="px-6 py-4 text-right">Berat (Gram)</th>
                <th class="px-6 py-4">Gudang / Brankas</th>
                <th class="px-6 py-4 text-right">Petugas</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
              <tr v-for="mov in movements.data" :key="mov.id" class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition-colors">
                <td class="px-6 py-4 font-mono text-xs font-semibold text-slate-900 dark:text-white">
                  {{ mov.reference_number }}
                  <div class="text-[11px] text-slate-400 font-normal">
                    {{ new Date(mov.created_at).toLocaleString('id-ID') }}
                  </div>
                </td>
                <td class="px-6 py-4">
                  <span 
                    :class="[
                      mov.type === 'in' 
                        ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20' 
                        : 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-500/20',
                      'inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-mono font-bold border'
                    ]"
                  >
                    <component :is="mov.type === 'in' ? ArrowDownLeft : ArrowUpRight" class="w-3.5 h-3.5" />
                    {{ mov.type === 'in' ? 'MASUK' : 'KELUAR' }}
                  </span>
                </td>
                <td class="px-6 py-4">
                  <div class="font-medium text-slate-900 dark:text-white">
                    {{ mov.item?.name }}
                  </div>
                  <div class="font-mono text-xs text-amber-600 dark:text-amber-400">
                    {{ mov.item?.sku }} ({{ mov.item?.gold_rate?.code }})
                  </div>
                </td>
                <td class="px-6 py-4 text-xs font-medium text-slate-700 dark:text-slate-300">
                  {{ formatReason(mov.reason) }}
                  <div v-if="mov.notes" class="text-slate-400 font-normal truncate max-w-xs mt-0.5">
                    {{ mov.notes }}
                  </div>
                </td>
                <td class="px-6 py-4 text-right font-mono font-bold text-slate-900 dark:text-white">
                  {{ parseFloat(mov.weight).toFixed(3) }} gr
                </td>
                <td class="px-6 py-4 text-xs">
                  <span v-if="mov.warehouse" class="inline-flex items-center gap-1 text-slate-600 dark:text-slate-300">
                    <Building2 class="w-3.5 h-3.5 text-slate-400" />
                    {{ mov.warehouse.name }}
                  </span>
                  <span v-else class="text-slate-400">-</span>
                </td>
                <td class="px-6 py-4 text-right text-xs text-slate-600 dark:text-slate-300">
                  <span class="inline-flex items-center gap-1">
                    <UserCheck class="w-3.5 h-3.5 text-slate-400" />
                    {{ mov.user?.name || 'Sistem' }}
                  </span>
                </td>
              </tr>
              <tr v-if="movements.data.length === 0">
                <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                  Belum ada log pergerakan keluar/masuk barang non-penjualan.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Modal Catat Pergerakan -->
    <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
      <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl max-w-lg w-full p-6 shadow-xl space-y-5">
        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-4">
          <h3 class="text-lg font-bold text-slate-900 dark:text-white">
            Catat Pergerakan Barang Fisik
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
              required
              class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500"
            >
              <option value="" disabled>-- Pilih perhiasan --</option>
              <option v-for="item in items" :key="item.id" :value="item.id">
                [{{ item.sku }}] {{ item.name }} ({{ item.gross_weight }} gr) - Status: {{ item.status }}
              </option>
            </select>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase mb-1">
                Arah Pergerakan *
              </label>
              <select 
                v-model="form.type"
                required
                class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 font-semibold"
              >
                <option value="out">Barang Keluar (OUT)</option>
                <option value="in">Barang Masuk (IN)</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase mb-1">
                Alasan / Keperluan *
              </label>
              <select 
                v-model="form.reason"
                required
                class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500"
              >
                <option value="melted">Dilebur Jadi Emas Batangan</option>
                <option value="repair_out">Kirim Servis ke Tukang Emas</option>
                <option value="repair_in">Masuk Selesai Servis Tukang</option>
                <option value="damaged_loss">Susut / Kerusakan Fisik</option>
                <option value="manual_in">Penyesuaian Masuk Khazanah</option>
                <option value="other">Keperluan Lainnya</option>
              </select>
            </div>
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase mb-1">
              Gudang / Lokasi Brankas
            </label>
            <select 
              v-model="form.warehouse_id"
              class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500"
            >
              <option value="">-- Pilih Gudang (Opsional) --</option>
              <option v-for="wh in warehouses" :key="wh.id" :value="wh.id">
                {{ wh.code }} - {{ wh.name }}
              </option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase mb-1">
              Catatan Detail
            </label>
            <textarea 
              v-model="form.notes"
              rows="2"
              placeholder="Nomor SPK tukang atau keterangan tambahan..."
              class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500"
            ></textarea>
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
              Simpan Pergerakan
            </button>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>
