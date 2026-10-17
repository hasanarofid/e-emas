<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
  Scale, Plus, Calendar, CheckCircle2, AlertCircle, 
  Layers, User, Clock, ArrowRight
} from 'lucide-vue-next';

const props = defineProps({
  logs: Object,
  filters: Object,
  trays: Array,
});

const dateFilter = ref(props.filters.date || '');
const sessionFilter = ref(props.filters.session || '');

const handleFilter = () => {
  router.get(route('admin.tray-scales.index'), {
    date: dateFilter.value,
    session: sessionFilter.value,
  }, { preserveState: true, replace: true });
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
  <AdminLayout title="Timbang Baki & Etalase">
    <Head title="Timbang Baki Kasir - POS Emas" />

    <div class="space-y-6">
      <!-- Header -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <h1 class="text-2xl font-bold text-white tracking-wide flex items-center gap-2">
            <Scale class="w-7 h-7 text-amber-400" />
            Timbang Baki & Etalase Kasir
          </h1>
          <p class="text-slate-400 text-sm mt-1">
            Rekonsiliasi gramasi fisik emas di etalase baki setiap buka toko (pagi) dan tutup kasir (sore)
          </p>
        </div>
        <Link
          :href="route('admin.tray-scales.create')"
          class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 font-semibold rounded-xl shadow-lg shadow-amber-500/20 transition-all duration-200"
        >
          <Plus class="w-5 h-5" />
          <span>Input Timbang Baki Baru</span>
        </Link>
      </div>

      <!-- Filter Bar -->
      <div class="bg-[#181834] border border-[#25254B] rounded-2xl p-4 flex flex-col md:flex-row gap-4 items-center justify-between shadow-xl">
        <div class="flex flex-col md:flex-row gap-3 w-full md:w-auto flex-1">
          <input
            v-model="dateFilter"
            @change="handleFilter"
            type="date"
            class="px-3.5 py-2 bg-[#0F0F23] border border-[#25254B] rounded-xl text-white text-sm focus:outline-none focus:border-amber-400/50"
          />
          <select
            v-model="sessionFilter"
            @change="handleFilter"
            class="px-3.5 py-2 bg-[#0F0F23] border border-[#25254B] rounded-xl text-white text-sm focus:outline-none focus:border-amber-400/50"
          >
            <option value="">Semua Sesi Penimbangan</option>
            <option value="morning">Timbang Pagi (Buka Toko)</option>
            <option value="evening">Timbang Sore (Tutup Kasir)</option>
          </select>
        </div>
        <div class="text-xs text-slate-400">
          Total Log: <span class="text-amber-400 font-semibold">{{ logs.total }} Rekaman</span>
        </div>
      </div>

      <!-- Table -->
      <div class="bg-[#181834] border border-[#25254B] rounded-2xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse text-sm">
            <thead>
              <tr class="border-b border-[#25254B] bg-[#1F1F41]/60 text-slate-300 font-medium">
                <th class="py-3.5 px-4">Tanggal & Sesi</th>
                <th class="py-3.5 px-4">Baki Etalase</th>
                <th class="py-3.5 px-4 text-center">Jumlah Item (Sistem vs Riil)</th>
                <th class="py-3.5 px-4 text-right">Berat Sistem</th>
                <th class="py-3.5 px-4 text-right">Berat Timbangan Riil</th>
                <th class="py-3.5 px-4 text-center">Selisih Fisik</th>
                <th class="py-3.5 px-4 text-center">Status</th>
                <th class="py-3.5 px-4">Petugas</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-[#25254B]">
              <tr 
                v-for="item in logs.data" 
                :key="item.id"
                class="hover:bg-[#1F1F41]/30 transition-colors text-slate-300"
              >
                <td class="py-3.5 px-4">
                  <div class="font-semibold text-white">{{ formatDate(item.scale_date) }}</div>
                  <span 
                    :class="item.session === 'morning' ? 'bg-amber-500/10 text-amber-400 border-amber-500/20' : 'bg-purple-500/10 text-purple-400 border-purple-500/20'"
                    class="inline-block mt-0.5 px-2 py-0.5 rounded text-[11px] font-semibold border"
                  >
                    {{ item.session === 'morning' ? '☀️ Buka Pagi' : '🌙 Tutup Sore' }}
                  </span>
                </td>
                <td class="py-3.5 px-4">
                  <div class="font-bold text-amber-400 font-mono">{{ item.tray?.code || '-' }}</div>
                  <div class="text-xs text-slate-300">{{ item.tray?.name || '-' }}</div>
                </td>
                <td class="py-3.5 px-4 text-center font-mono">
                  <span class="text-slate-400">{{ item.item_count_system }} pcs</span>
                  <span class="text-slate-600 mx-1.5">vs</span>
                  <span class="font-bold text-white">{{ item.item_count_real }} pcs</span>
                </td>
                <td class="py-3.5 px-4 text-right font-mono text-slate-400">
                  {{ formatWeight(item.weight_system) }}
                </td>
                <td class="py-3.5 px-4 text-right font-mono font-bold text-white">
                  {{ formatWeight(item.weight_real) }}
                </td>
                <td class="py-3.5 px-4 text-center font-mono font-bold">
                  <span 
                    :class="item.difference_weight == 0 ? 'text-emerald-400' : 'text-red-400'"
                  >
                    {{ item.difference_weight > 0 ? '+' : '' }}{{ formatWeight(item.difference_weight) }}
                  </span>
                </td>
                <td class="py-3.5 px-4 text-center">
                  <span 
                    v-if="item.status === 'matched'"
                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20"
                  >
                    <CheckCircle2 class="w-3.5 h-3.5" />
                    Sesuai
                  </span>
                  <span 
                    v-else
                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-500/10 text-red-400 border border-red-500/20"
                  >
                    <AlertCircle class="w-3.5 h-3.5" />
                    Selisih
                  </span>
                </td>
                <td class="py-3.5 px-4 text-xs text-slate-400">
                  {{ item.user?.name || '-' }}
                  <div v-if="item.notes" class="text-[11px] text-slate-500 italic mt-0.5">
                    "{{ item.notes }}"
                  </div>
                </td>
              </tr>
              <tr v-if="logs.data.length === 0">
                <td colspan="8" class="py-12 text-center text-slate-500">
                  Belum ada catatan timbang baki. Klik "Input Timbang Baki Baru" untuk memulai rekonsiliasi.
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="logs.links && logs.links.length > 3" class="p-4 border-t border-[#25254B] flex items-center justify-between text-xs text-slate-400">
          <div>Menampilkan {{ logs.from }} - {{ logs.to }} dari {{ logs.total }} catatan</div>
          <div class="flex gap-1">
            <template v-for="(link, i) in logs.links" :key="i">
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
  </AdminLayout>
</template>
