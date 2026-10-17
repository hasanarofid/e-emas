<script setup>
import { ref, computed, watch } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
  ArrowLeft, Scale, Layers, Calendar, Clock, 
  CheckCircle2, AlertCircle, Save
} from 'lucide-vue-next';

const props = defineProps({
  trays: Array,
  today: String,
});

const form = useForm({
  tray_id: props.trays.length > 0 ? props.trays[0].id : '',
  scale_date: props.today || new Date().toISOString().split('T')[0],
  session: 'morning',
  item_count_real: 0,
  weight_real: 0,
  notes: '',
});

const selectedTray = computed(() => {
  return props.trays.find(t => t.id === form.tray_id);
});

// Auto-populate target sistem saat baki dipilih
watch(selectedTray, (tray) => {
  if (tray) {
    form.item_count_real = tray.items_count || 0;
    form.weight_real = parseFloat(tray.items_sum_gross_weight) || 0;
  }
}, { immediate: true });

const weightSystem = computed(() => {
  return parseFloat(selectedTray.value?.items_sum_gross_weight) || 0;
});

const itemCountSystem = computed(() => {
  return parseInt(selectedTray.value?.items_count) || 0;
});

const diffWeight = computed(() => {
  const real = parseFloat(form.weight_real) || 0;
  return Number((real - weightSystem.value).toFixed(3));
});

const diffPieces = computed(() => {
  const real = parseInt(form.item_count_real) || 0;
  return real - itemCountSystem.value;
});

const formatWeight = (val) => {
  return Number(val || 0).toFixed(3) + ' gr';
};

const submit = () => {
  form.post(route('admin.tray-scales.store'));
};
</script>

<template>
  <AdminLayout title="Input Timbang Baki Kasir">
    <Head title="Input Timbang Baki - POS Emas" />

    <div class="space-y-6 max-w-3xl mx-auto">
      <!-- Header -->
      <div class="flex items-center gap-3">
        <Link
          :href="route('admin.tray-scales.index')"
          class="p-2 bg-[#181834] hover:bg-[#25254B] text-slate-300 rounded-xl border border-[#25254B] transition-colors"
        >
          <ArrowLeft class="w-5 h-5" />
        </Link>
        <div>
          <h1 class="text-2xl font-bold text-white tracking-wide flex items-center gap-2">
            <Scale class="w-7 h-7 text-amber-400" />
            Timbang Baki & Etalase Kasir
          </h1>
          <p class="text-slate-400 text-xs mt-0.5">
            Pencatatan gramasi fisik timbangan digital per baki emas untuk mencegah penyusutan
          </p>
        </div>
      </div>

      <form @submit.prevent="submit" class="space-y-6">
        <div class="bg-[#181834] border border-[#25254B] rounded-2xl p-5 shadow-xl space-y-4">
          <h2 class="text-xs font-bold text-amber-400 uppercase tracking-wider flex items-center gap-2">
            <Layers class="w-4 h-4" />
            Pilih Baki Etalase & Waktu Penimbangan
          </h2>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-xs font-semibold text-slate-300 mb-1">Baki Etalase *</label>
              <select
                v-model="form.tray_id"
                required
                class="w-full px-3 py-2 bg-[#0F0F23] border border-[#25254B] rounded-xl text-white text-sm focus:border-amber-400 font-semibold"
              >
                <option v-for="t in trays" :key="t.id" :value="t.id">
                  {{ t.code }} - {{ t.name }}
                </option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-300 mb-1">Tanggal Timbang *</label>
              <input
                v-model="form.scale_date"
                type="date"
                required
                class="w-full px-3 py-2 bg-[#0F0F23] border border-[#25254B] rounded-xl text-white text-sm focus:border-amber-400"
              />
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-300 mb-1">Sesi Penimbangan *</label>
              <select
                v-model="form.session"
                class="w-full px-3 py-2 bg-[#0F0F23] border border-[#25254B] rounded-xl text-white text-sm focus:border-amber-400 font-semibold"
              >
                <option value="morning">☀️ Buka Toko (Pagi)</option>
                <option value="evening">🌙 Tutup Kasir (Sore)</option>
              </select>
            </div>
          </div>

          <!-- Perbandingan Sistem vs Riil Timbangan Digital -->
          <div class="p-4 bg-[#0F0F23] border border-[#25254B] rounded-xl space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pb-3 border-b border-[#25254B]">
              <div class="p-3 bg-[#181834] rounded-xl border border-[#25254B]">
                <div class="text-[11px] text-slate-400 uppercase tracking-wider font-semibold">Tercatat di Sistem (Database)</div>
                <div class="mt-2 flex items-baseline justify-between">
                  <span class="text-xs text-slate-300">Total Berat:</span>
                  <span class="text-base font-bold text-white font-mono">{{ formatWeight(weightSystem) }}</span>
                </div>
                <div class="mt-1 flex items-baseline justify-between text-xs text-slate-400">
                  <span>Jumlah Perhiasan:</span>
                  <span class="font-bold text-white">{{ itemCountSystem }} pcs</span>
                </div>
              </div>

              <div class="p-3 bg-[#181834] rounded-xl border border-amber-500/30">
                <div class="text-[11px] text-amber-400 uppercase tracking-wider font-semibold">Timbangan Digital Riil Kasir</div>
                <div class="mt-2 space-y-2">
                  <div class="flex items-center justify-between gap-3">
                    <label class="text-xs text-slate-300 font-semibold">Berat Fisik Riil (gr) *</label>
                    <input
                      v-model.number="form.weight_real"
                      type="number"
                      step="0.001"
                      min="0"
                      required
                      class="w-36 px-2.5 py-1 bg-[#0F0F23] border border-amber-500/50 rounded-lg text-amber-400 text-sm font-mono font-bold text-right focus:border-amber-400"
                    />
                  </div>
                  <div class="flex items-center justify-between gap-3">
                    <label class="text-xs text-slate-300">Jumlah Fisik Riil (pcs) *</label>
                    <input
                      v-model.number="form.item_count_real"
                      type="number"
                      min="0"
                      required
                      class="w-36 px-2.5 py-1 bg-[#0F0F23] border border-slate-700 rounded-lg text-white text-xs font-mono font-semibold text-right focus:border-amber-400"
                    />
                  </div>
                </div>
              </div>
            </div>

            <!-- Hasil Rekonsiliasi -->
            <div class="flex flex-col md:flex-row items-center justify-between gap-3 pt-1">
              <div class="flex items-center gap-2">
                <component 
                  :is="(diffWeight == 0 && diffPieces == 0) ? CheckCircle2 : AlertCircle" 
                  :class="(diffWeight == 0 && diffPieces == 0) ? 'text-emerald-400' : 'text-red-400'"
                  class="w-5 h-5 shrink-0"
                />
                <div class="text-xs">
                  <div class="font-bold text-white">
                    {{ (diffWeight == 0 && diffPieces == 0) ? 'Fisik Sesuai 100% dengan Sistem' : 'Terdapat Selisih Fisik!' }}
                  </div>
                  <div class="text-slate-400 text-[11px]">
                    Selisih Berat: <span :class="diffWeight == 0 ? 'text-emerald-400' : 'text-red-400'" class="font-mono font-bold">{{ diffWeight > 0 ? '+' : '' }}{{ formatWeight(diffWeight) }}</span> | 
                    Selisih Pcs: <span :class="diffPieces == 0 ? 'text-emerald-400' : 'text-red-400'" class="font-mono font-bold">{{ diffPieces > 0 ? '+' : '' }}{{ diffPieces }} pcs</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-300 mb-1">Catatan Tambahan Kasir</label>
            <textarea
              v-model="form.notes"
              rows="2"
              placeholder="Keterangan kondisi etalase atau alasan jika terdapat selisih timbangan..."
              class="w-full px-3 py-2 bg-[#0F0F23] border border-[#25254B] rounded-xl text-white text-xs focus:border-amber-400"
            ></textarea>
          </div>
        </div>

        <!-- Tombol Aksi -->
        <div class="flex items-center justify-end gap-3">
          <Link
            :href="route('admin.tray-scales.index')"
            class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-sm font-semibold"
          >
            Batal
          </Link>
          <button
            type="submit"
            :disabled="form.processing"
            class="inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 font-bold rounded-xl shadow-lg shadow-amber-500/20 text-sm disabled:opacity-50 transition-all"
          >
            <Save class="w-4 h-4" />
            <span>{{ form.processing ? 'Menyimpan...' : 'Simpan Catatan Timbangan' }}</span>
          </button>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>
