<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { 
  ClipboardCheck, 
  Plus, 
  Layers, 
  User, 
  Calendar, 
  CheckCircle2, 
  AlertTriangle, 
  Clock,
  ArrowRight,
  X,
  Printer
} from 'lucide-vue-next';

const props = defineProps({
  audits: {
    type: Object,
    required: true
  },
  trays: {
    type: Array,
    default: () => []
  }
});

const isModalOpen = ref(false);
const form = ref({
  tray_id: props.trays[0]?.id || '',
  notes: ''
});
const isSubmitting = ref(false);

const startAudit = () => {
  if (!form.value.tray_id) return;
  isSubmitting.value = true;
  router.post(route('admin.stock-audits.store'), form.value, {
    onFinish: () => {
      isSubmitting.value = false;
      isModalOpen.value = false;
    }
  });
};
</script>

<template>
  <Head title="Stock Opname Baki Etalase - POS Emas" />

  <AdminLayout>
    <div class="space-y-6">
      
      <!-- Top Title Bar -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200 dark:border-slate-800">
        <div>
          <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-700 dark:text-amber-300 text-xs font-semibold mb-1">
            <ClipboardCheck class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400" />
            <span>Audit Fisik & Rekonsiliasi Nampan</span>
          </div>
          <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">
            Stock Opname Baki (Etalase)
          </h1>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
            Audit fisik harian perhiasan per baki menggunakan barcode scanner untuk mendeteksi kehilangan atau barang salah tempat.
          </p>
        </div>

        <button 
          @click="isModalOpen = true"
          class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-bold text-xs uppercase tracking-wider flex items-center justify-center gap-2 shadow-lg shadow-amber-500/20 transition-all cursor-pointer"
        >
          <Plus class="w-4 h-4" /> Mulai Audit Baki Baru
        </button>
      </div>

      <!-- Audit History Table -->
      <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs border-collapse">
            <thead>
              <tr class="bg-slate-50 dark:bg-slate-950/60 border-b border-slate-200 dark:border-slate-800 text-slate-500 font-semibold uppercase text-[10px] tracking-wider">
                <th class="p-4">Kode Sesi Audit</th>
                <th class="p-4">Baki Etalase</th>
                <th class="p-4">Auditor / Kasir</th>
                <th class="p-4 text-center">Status</th>
                <th class="p-4 text-center">Cocok</th>
                <th class="p-4 text-center">Kurang / Hilang</th>
                <th class="p-4 text-center">Salah Baki</th>
                <th class="p-4 text-center">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
              <tr 
                v-for="a in audits.data" 
                :key="a.id"
                class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition-colors"
              >
                <!-- Kode Audit & Tanggal -->
                <td class="p-4">
                  <span class="font-bold text-slate-900 dark:text-white font-mono block">{{ a.audit_code }}</span>
                  <span class="text-[10px] text-slate-400 font-mono">{{ new Date(a.created_at).toLocaleDateString('id-ID') }}</span>
                </td>

                <!-- Baki Etalase -->
                <td class="p-4">
                  <span class="font-bold text-amber-600 dark:text-amber-400 font-mono block">{{ a.tray?.code }}</span>
                  <span class="text-xs text-slate-600 dark:text-slate-400">{{ a.tray?.name }}</span>
                </td>

                <!-- Auditor -->
                <td class="p-4">
                  <span class="font-semibold text-slate-800 dark:text-slate-200">{{ a.auditor?.name || 'Kasir' }}</span>
                </td>

                <!-- Status -->
                <td class="p-4 text-center">
                  <span 
                    v-if="a.status === 'completed'"
                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-500/20 text-[10px] font-bold"
                  >
                    <CheckCircle2 class="w-3 h-3" /> Selesai
                  </span>
                  <span 
                    v-else
                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-500/20 text-[10px] font-bold"
                  >
                    <Clock class="w-3 h-3" /> Berlangsung
                  </span>
                </td>

                <!-- Cocok -->
                <td class="p-4 text-center font-mono font-bold text-emerald-600 dark:text-emerald-400">
                  {{ a.total_scanned_items - a.total_mismatched_items }} / {{ a.total_expected_items }}
                </td>

                <!-- Kurang / Hilang -->
                <td class="p-4 text-center font-mono font-bold" :class="a.total_missing_items > 0 ? 'text-rose-500' : 'text-slate-400'">
                  {{ a.total_missing_items }}
                </td>

                <!-- Salah Baki (Nyasar) -->
                <td class="p-4 text-center font-mono font-bold" :class="a.total_mismatched_items > 0 ? 'text-amber-500' : 'text-slate-400'">
                  {{ a.total_mismatched_items }}
                </td>

                <!-- Aksi -->
                <td class="p-4 text-center">
                  <Link 
                    :href="route('admin.stock-audits.show', a.id)"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold transition-colors"
                  >
                    <span>{{ a.status === 'completed' ? 'Lihat Laporan' : 'Lanjutkan Scan' }}</span>
                    <ArrowRight class="w-3 h-3" />
                  </Link>
                </td>
              </tr>

              <tr v-if="audits.data.length === 0">
                <td colspan="8" class="p-12 text-center text-slate-400">
                  <ClipboardCheck class="w-10 h-10 mx-auto text-slate-300 dark:text-slate-700 mb-2 stroke-[1.5]" />
                  <p class="text-xs font-medium">Belum ada sesi stock opname yang tercatat.</p>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Modal Start New Audit -->
      <div 
        v-if="isModalOpen" 
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm"
      >
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-2xl max-w-md w-full overflow-hidden p-6 space-y-4">
          <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
              <ClipboardCheck class="w-4 h-4 text-amber-500" />
              Mulai Sesi Stock Opname
            </h3>
            <button @click="isModalOpen = false" class="text-slate-400 hover:text-slate-600">
              <X class="w-4 h-4" />
            </button>
          </div>

          <div class="space-y-3">
            <div>
              <label class="text-xs font-bold text-slate-700 dark:text-slate-300 mb-1 block">Pilih Baki yang Akan Diaudit *</label>
              <select 
                v-model="form.tray_id"
                class="w-full px-3 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-xs text-slate-900 dark:text-white font-medium"
              >
                <option v-for="t in trays" :key="t.id" :value="t.id">
                  {{ t.code }} - {{ t.name }} ({{ t.total_items }} perhiasan)
                </option>
              </select>
            </div>

            <div>
              <label class="text-xs font-bold text-slate-700 dark:text-slate-300 mb-1 block">Catatan Sesi (Opsional)</label>
              <input 
                v-model="form.notes"
                type="text" 
                placeholder="Contoh: Audit rutin sebelum tutup toko sore"
                class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-xs text-slate-900 dark:text-white"
              />
            </div>
          </div>

          <div class="pt-3 border-t border-slate-200 dark:border-slate-800 flex items-center gap-2">
            <button 
              type="button"
              @click="isModalOpen = false"
              class="flex-1 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold text-xs cursor-pointer"
            >
              Batal
            </button>
            <button 
              type="button"
              @click="startAudit"
              :disabled="isSubmitting"
              class="flex-1 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs uppercase tracking-wider disabled:opacity-50 cursor-pointer transition-all"
            >
              {{ isSubmitting ? 'Membuka...' : 'Mulai Audit' }}
            </button>
          </div>
        </div>
      </div>

    </div>
  </AdminLayout>
</template>
