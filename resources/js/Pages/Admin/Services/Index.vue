<script setup>
import { ref } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
  Wrench, Plus, Search, Calendar, Scale, 
  Coins, CheckCircle2, Clock, Sparkles, X, Trash2, 
  ChevronRight, ArrowRight
} from 'lucide-vue-next';

const props = defineProps({
  services: Object,
  filters: Object,
  stats: Object,
});

const search = ref(props.filters.search || '');
const statusFilter = ref(props.filters.status || '');
const isStatusModalOpen = ref(false);
const selectedService = ref(null);

const statusForm = useForm({
  status: 'ready',
  weight_after: '',
  notes: '',
});

const handleFilter = () => {
  router.get(route('admin.services.index'), {
    search: search.value,
    status: statusFilter.value,
  }, { preserveState: true, replace: true });
};

const openStatusModal = (service) => {
  selectedService.value = service;
  statusForm.status = service.status === 'received' ? 'in_progress' : (service.status === 'in_progress' ? 'ready' : 'picked_up');
  statusForm.weight_after = service.weight_after || service.weight_before;
  statusForm.notes = '';
  isStatusModalOpen.value = true;
};

const submitStatus = () => {
  if (!selectedService.value) return;
  statusForm.patch(route('admin.services.update-status', selectedService.value.id), {
    onSuccess: () => {
      isStatusModalOpen.value = false;
    },
  });
};

const deleteService = (service) => {
  if (confirm(`Apakah Anda yakin ingin menghapus data servis "${service.service_number}"? Data akan diarsipkan aman (Soft Delete).`)) {
    router.delete(route('admin.services.destroy', service.id));
  }
};

const formatRupiah = (val) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val || 0);
};

const formatWeight = (val) => {
  return Number(val || 0).toFixed(3) + ' gr';
};

const formatDate = (dateString) => {
  if (!dateString) return '-';
  const d = new Date(dateString);
  return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
};
</script>

<template>
  <AdminLayout title="Servis & Cuci Perhiasan">
    <Head title="Servis & Cuci Perhiasan - POS Emas" />

    <div class="space-y-6">
      <!-- Header -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <h1 class="text-2xl font-bold text-white tracking-wide flex items-center gap-2">
            <Wrench class="w-7 h-7 text-amber-400" />
            Servis & Cuci Perhiasan (Reparasi Emas)
          </h1>
          <p class="text-slate-400 text-sm mt-1">
            Pengelolaan reparasi patah, cuci sepuh, pasang permata zircon, dan resize ukuran perhiasan
          </p>
        </div>
        <Link
          :href="route('admin.services.create')"
          class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 font-semibold rounded-xl shadow-lg shadow-amber-500/20 transition-all duration-200"
        >
          <Plus class="w-5 h-5" />
          <span>Daftarkan Order Servis Baru</span>
        </Link>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-[#181834] border border-[#25254B] rounded-2xl p-4 flex items-center gap-4 shadow-xl">
          <div class="w-11 h-11 rounded-xl bg-purple-500/10 border border-purple-500/20 flex items-center justify-center text-purple-400">
            <Wrench class="w-5 h-5" />
          </div>
          <div>
            <div class="text-xs text-slate-400 font-medium">Total Order Servis</div>
            <div class="text-xl font-bold text-white mt-0.5">{{ stats.total_services }}</div>
          </div>
        </div>

        <div class="bg-[#181834] border border-[#25254B] rounded-2xl p-4 flex items-center gap-4 shadow-xl">
          <div class="w-11 h-11 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400">
            <Clock class="w-5 h-5" />
          </div>
          <div>
            <div class="text-xs text-slate-400 font-medium">Sedang Dikerjakan</div>
            <div class="text-xl font-bold text-white mt-0.5">{{ stats.in_progress_count }}</div>
          </div>
        </div>

        <div class="bg-[#181834] border border-[#25254B] rounded-2xl p-4 flex items-center gap-4 shadow-xl">
          <div class="w-11 h-11 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400">
            <Sparkles class="w-5 h-5" />
          </div>
          <div>
            <div class="text-xs text-slate-400 font-medium">Siap Diambil</div>
            <div class="text-xl font-bold text-white mt-0.5">{{ stats.ready_count }}</div>
          </div>
        </div>

        <div class="bg-[#181834] border border-[#25254B] rounded-2xl p-4 flex items-center gap-4 shadow-xl">
          <div class="w-11 h-11 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400">
            <CheckCircle2 class="w-5 h-5" />
          </div>
          <div>
            <div class="text-xs text-slate-400 font-medium">Selesai & Diambil</div>
            <div class="text-xl font-bold text-white mt-0.5">{{ stats.picked_up_count }}</div>
          </div>
        </div>
      </div>

      <!-- Filter Bar -->
      <div class="bg-[#181834] border border-[#25254B] rounded-2xl p-4 flex flex-col md:flex-row gap-4 items-center justify-between shadow-xl">
        <div class="flex flex-col md:flex-row gap-3 w-full md:w-auto flex-1">
          <div class="relative w-full md:w-80">
            <Search class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
            <input
              v-model="search"
              @keyup.enter="handleFilter"
              type="text"
              placeholder="Cari no servis, nama, jenis servis..."
              class="w-full pl-10 pr-4 py-2 bg-[#0F0F23] border border-[#25254B] rounded-xl text-white text-sm focus:outline-none focus:border-amber-400/50"
            />
          </div>
          <select
            v-model="statusFilter"
            @change="handleFilter"
            class="px-3.5 py-2 bg-[#0F0F23] border border-[#25254B] rounded-xl text-white text-sm focus:outline-none focus:border-amber-400/50"
          >
            <option value="">Semua Status Servis</option>
            <option value="received">Diterima Toko</option>
            <option value="in_progress">Sedang Dikerjakan Pengrajin</option>
            <option value="ready">Selesai (Siap Diambil)</option>
            <option value="picked_up">Sudah Diambil Nasabah</option>
          </select>
        </div>
        <div class="text-xs text-slate-400">
          Total: <span class="text-amber-400 font-semibold">{{ services.total }} Order</span>
        </div>
      </div>

      <!-- Table -->
      <div class="bg-[#181834] border border-[#25254B] rounded-2xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse text-sm">
            <thead>
              <tr class="border-b border-[#25254B] bg-[#1F1F41]/60 text-slate-300 font-medium">
                <th class="py-3.5 px-4">No. Order & Pelanggan</th>
                <th class="py-3.5 px-4">Barang & Jenis Servis</th>
                <th class="py-3.5 px-4 text-center">Gramasi Fisik</th>
                <th class="py-3.5 px-4 text-right">Biaya / DP / Sisa</th>
                <th class="py-3.5 px-4 text-center">Status</th>
                <th class="py-3.5 px-4 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-[#25254B]">
              <tr 
                v-for="item in services.data" 
                :key="item.id"
                class="hover:bg-[#1F1F41]/30 transition-colors text-slate-300"
              >
                <td class="py-3.5 px-4">
                  <div class="font-bold text-amber-400 font-mono">{{ item.service_number }}</div>
                  <div class="font-semibold text-white mt-0.5">{{ item.customer_name }}</div>
                  <div class="text-xs text-slate-400">{{ item.customer_phone || '-' }}</div>
                  <div class="text-[11px] text-slate-500 mt-0.5 flex items-center gap-1">
                    <Calendar class="w-3 h-3" />
                    <span>Terima: {{ formatDate(item.received_date) }}</span>
                  </div>
                </td>
                <td class="py-3.5 px-4">
                  <div class="font-semibold text-white">{{ item.item_name }}</div>
                  <span class="inline-block mt-1 px-2 py-0.5 rounded-md bg-[#25254B] text-amber-300 text-xs font-medium">
                    {{ item.service_type }}
                  </span>
                  <div v-if="item.damage_description" class="text-xs text-slate-400 mt-1 line-clamp-1">
                    {{ item.damage_description }}
                  </div>
                </td>
                <td class="py-3.5 px-4 text-center font-mono text-xs">
                  <div>Awal: <span class="font-semibold text-white">{{ formatWeight(item.weight_before) }}</span></div>
                  <div v-if="item.weight_after" class="text-emerald-400">
                    Selesai: {{ formatWeight(item.weight_after) }}
                  </div>
                </td>
                <td class="py-3.5 px-4 text-right">
                  <div class="font-bold text-white">{{ formatRupiah(item.estimated_cost) }}</div>
                  <div class="text-xs text-emerald-400">DP: {{ formatRupiah(item.down_payment) }}</div>
                  <div v-if="item.remaining_cost > 0" class="text-xs text-red-400 font-semibold">
                    Sisa: {{ formatRupiah(item.remaining_cost) }}
                  </div>
                </td>
                <td class="py-3.5 px-4 text-center">
                  <span 
                    v-if="item.status === 'received'"
                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-700 text-slate-300"
                  >
                    Diterima
                  </span>
                  <span 
                    v-else-if="item.status === 'in_progress'"
                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/20"
                  >
                    <Clock class="w-3 h-3" />
                    Dikerjakan
                  </span>
                  <span 
                    v-else-if="item.status === 'ready'"
                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-500/10 text-blue-400 border border-blue-500/20"
                  >
                    <Sparkles class="w-3 h-3" />
                    Siap Ambil
                  </span>
                  <span 
                    v-else-if="item.status === 'picked_up'"
                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20"
                  >
                    <CheckCircle2 class="w-3 h-3" />
                    Diambil
                  </span>
                </td>
                <td class="py-3.5 px-4 text-right">
                  <div class="inline-flex items-center gap-2">
                    <button
                      v-if="item.status !== 'picked_up'"
                      @click="openStatusModal(item)"
                      class="px-2.5 py-1 bg-[#25254B] hover:bg-amber-500 hover:text-slate-950 text-amber-300 font-semibold rounded-lg text-xs transition-colors"
                      title="Update Progres Servis"
                    >
                      Update
                    </button>
                    <button
                      @click="deleteService(item)"
                      class="p-1.5 text-slate-400 hover:text-red-400 hover:bg-[#25254B] rounded-lg transition-colors"
                      title="Hapus Order Servis (Soft Delete)"
                    >
                      <Trash2 class="w-4 h-4" />
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="services.data.length === 0">
                <td colspan="6" class="py-12 text-center text-slate-500">
                  Belum ada order servis perhiasan. Klik "Daftarkan Order Servis Baru" di atas.
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="services.links && services.links.length > 3" class="p-4 border-t border-[#25254B] flex items-center justify-between text-xs text-slate-400">
          <div>Menampilkan {{ services.from }} - {{ services.to }} dari {{ services.total }} order</div>
          <div class="flex gap-1">
            <template v-for="(link, i) in services.links" :key="i">
              <button
                v-if="link.url"
                @click="router.get(link.url)"
                v-html="link.label"
                :class="link.active ? 'bg-amber-500 text-slate-950 font-bold' : 'bg-[#1F1F41] text-slate-300 hover:bg-[#25254B]'"
                class="px-3 py-1.5 rounded-lg border border-[#25254B]"
              />
              <span v-else v-html="link.label" class="px-3 py-1.5 rounded-lg text-slate-600 cursor-not-allowed border border-[#25254B]/40" />
            </template>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Update Status Servis -->
    <div v-if="isStatusModalOpen && selectedService" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/75 backdrop-blur-sm">
      <div class="bg-[#181834] border border-[#25254B] rounded-2xl w-full max-w-md shadow-2xl p-6">
        <div class="flex items-center justify-between pb-4 border-b border-[#25254B]">
          <h3 class="text-lg font-bold text-white flex items-center gap-2">
            <Wrench class="w-5 h-5 text-amber-400" />
            Update Status Servis {{ selectedService.service_number }}
          </h3>
          <button @click="isStatusModalOpen = false" class="text-slate-400 hover:text-white p-1 rounded-lg">
            <X class="w-5 h-5" />
          </button>
        </div>

        <form @submit.prevent="submitStatus" class="mt-4 space-y-4">
          <div>
            <label class="block text-xs font-semibold text-slate-300 mb-1">Status Pengerjaan *</label>
            <select
              v-model="statusForm.status"
              class="w-full px-3 py-2 bg-[#0F0F23] border border-[#25254B] rounded-xl text-white text-sm focus:border-amber-400 font-semibold"
            >
              <option value="received">Diterima Toko</option>
              <option value="in_progress">Sedang Dikerjakan Pengrajin</option>
              <option value="ready">Selesai (Siap Diambil Nasabah)</option>
              <option value="picked_up">Sudah Diambil (Lunas)</option>
              <option value="cancelled">Dibatalkan</option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-300 mb-1">Berat Akhir Setelah Diservis (gr)</label>
            <input
              v-model.number="statusForm.weight_after"
              type="number"
              step="0.001"
              class="w-full px-3 py-2 bg-[#0F0F23] border border-[#25254B] rounded-xl text-white text-sm focus:border-amber-400 font-mono text-center"
            />
            <span class="text-[10px] text-slate-500 mt-1 block">Catat jika ada penambahan emas patri / permata baru</span>
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-300 mb-1">Catatan Pengerjaan</label>
            <textarea
              v-model="statusForm.notes"
              rows="2"
              placeholder="Catatan dari pengrajin atau bukti pelunasan sisa..."
              class="w-full px-3 py-2 bg-[#0F0F23] border border-[#25254B] rounded-xl text-white text-xs focus:border-amber-400"
            ></textarea>
          </div>

          <div class="flex items-center justify-end gap-3 pt-3 border-t border-[#25254B]">
            <button
              type="button"
              @click="isStatusModalOpen = false"
              class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs font-semibold"
            >
              Batal
            </button>
            <button
              type="submit"
              :disabled="statusForm.processing"
              class="px-5 py-2 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 font-bold rounded-xl text-xs shadow-lg shadow-amber-500/20 disabled:opacity-50"
            >
              {{ statusForm.processing ? 'Menyimpan...' : 'Simpan Status' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>
