<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue';
import { 
  ClipboardCheck, 
  Barcode, 
  CheckCircle2, 
  AlertTriangle, 
  AlertCircle, 
  ArrowLeft, 
  Printer, 
  Check, 
  Layers, 
  Scale, 
  Gem,
  Clock
} from 'lucide-vue-next';

const props = defineProps({
  audit: {
    type: Object,
    required: true
  },
  storeSettings: {
    type: Object,
    default: () => ({})
  }
});

const scanBarcode = ref('');
const barcodeInput = ref(null);
const activeTab = ref('all'); // 'all', 'matched', 'missing', 'unexpected'
const isCompleting = ref(false);

const handleScan = () => {
  if (!scanBarcode.value.trim() || props.audit.status === 'completed') return;

  router.post(route('admin.stock-audits.scan', props.audit.id), {
    barcode: scanBarcode.value.trim()
  }, {
    preserveScroll: true,
    onSuccess: () => {
      scanBarcode.value = '';
      focusScanner();
    }
  });
};

const completeAudit = () => {
  if (!confirm('Apakah Anda yakin ingin menyelesaikan dan mengunci sesi stock opname ini? Rekapitulasi selisih akan disimpan secara permanen.')) return;
  isCompleting.value = true;
  router.post(route('admin.stock-audits.complete', props.audit.id), {}, {
    onFinish: () => {
      isCompleting.value = false;
    }
  });
};

const focusScanner = () => {
  setTimeout(() => {
    barcodeInput.value?.focus();
  }, 50);
};

onMounted(() => {
  focusScanner();
});

const formatWeight = (val) => {
  return parseFloat(val || 0).toFixed(3) + ' gr';
};

const formatRupiah = (val) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0
  }).format(val || 0);
};
</script>

<template>
  <Head :title="`Audit ${audit.audit_code} - Baki ${audit.tray?.code}`" />

  <AdminLayout>
    <div class="space-y-6">
      
      <!-- Top Action Bar -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200 dark:border-slate-800">
        <div class="flex items-center gap-3">
          <Link 
            :href="route('admin.stock-audits.index')"
            class="p-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 transition-colors cursor-pointer"
          >
            <ArrowLeft class="w-4 h-4" />
          </Link>
          <div>
            <div class="flex items-center gap-2">
              <h1 class="text-xl font-extrabold text-slate-900 dark:text-white font-mono">
                {{ audit.audit_code }}
              </h1>
              <span 
                v-if="audit.status === 'completed'"
                class="px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-500/20 text-[10px] font-bold"
              >
                Terkunci / Selesai
              </span>
              <span 
                v-else
                class="px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-500/20 text-[10px] font-bold"
              >
                Sedang Berlangsung
              </span>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400">
              Baki: <b class="text-slate-800 dark:text-slate-200">{{ audit.tray?.code }} - {{ audit.tray?.name }}</b> • Auditor: {{ audit.auditor?.name }}
            </p>
          </div>
        </div>

        <div class="flex items-center gap-2">
          <button 
            onclick="window.print()"
            class="px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold flex items-center gap-1.5 transition-colors cursor-pointer"
          >
            <Printer class="w-4 h-4" /> Cetak Hasil Audit
          </button>

          <button 
            v-if="audit.status !== 'completed'"
            @click="completeAudit"
            :disabled="isCompleting"
            class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs uppercase tracking-wider flex items-center gap-2 shadow-lg shadow-emerald-600/20 transition-all cursor-pointer disabled:opacity-50"
          >
            <Check class="w-4 h-4" />
            <span>{{ isCompleting ? 'Menyimpan...' : 'Selesaikan & Kunci Audit' }}</span>
          </button>
        </div>
      </div>

      <!-- Realtime Metric Stats Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <!-- Card 1: Ekspektasi Sistem -->
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
          <span class="text-[11px] text-slate-400 block">Stok Terdaftar di Baki</span>
          <span class="text-2xl font-black text-slate-900 dark:text-white font-mono mt-1 block">
            {{ audit.total_expected_items }} <span class="text-xs font-normal text-slate-400">item</span>
          </span>
        </div>

        <!-- Card 2: Cocok (Matched) -->
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
          <span class="text-[11px] text-emerald-600 dark:text-emerald-400 font-bold block flex items-center gap-1">
            <CheckCircle2 class="w-3.5 h-3.5" /> Fisik Sesuai (Cocok)
          </span>
          <span class="text-2xl font-black text-emerald-600 dark:text-emerald-400 font-mono mt-1 block">
            {{ audit.total_scanned_items - audit.total_mismatched_items }} <span class="text-xs font-normal text-slate-400">item</span>
          </span>
        </div>

        <!-- Card 3: Kurang (Missing) -->
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
          <span class="text-[11px] text-rose-500 font-bold block flex items-center gap-1">
            <AlertCircle class="w-3.5 h-3.5" /> Belum Di-scan / Kurang
          </span>
          <span class="text-2xl font-black text-rose-500 font-mono mt-1 block">
            {{ audit.total_missing_items }} <span class="text-xs font-normal text-slate-400">item</span>
          </span>
        </div>

        <!-- Card 4: Nyasar (Unexpected) -->
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
          <span class="text-[11px] text-amber-500 font-bold block flex items-center gap-1">
            <AlertTriangle class="w-3.5 h-3.5" /> Salah Baki (Nyasar)
          </span>
          <span class="text-2xl font-black text-amber-500 font-mono mt-1 block">
            {{ audit.total_mismatched_items }} <span class="text-xs font-normal text-slate-400">item</span>
          </span>
        </div>
      </div>

      <!-- Live Barcode Scanning Terminal (If still active) -->
      <div 
        v-if="audit.status !== 'completed'"
        class="p-5 rounded-2xl bg-gradient-to-r from-amber-500/10 via-amber-500/5 to-transparent border border-amber-500/30 shadow-sm space-y-2"
      >
        <label class="text-xs font-bold text-amber-900 dark:text-amber-300 flex items-center gap-1.5 uppercase font-mono">
          <Barcode class="w-4 h-4 text-amber-500" />
          Scan Barcode Fisik Perhiasan di Baki Sekarang:
        </label>
        <div class="flex gap-2">
          <input 
            ref="barcodeInput"
            v-model="scanBarcode"
            @keydown.enter="handleScan"
            type="text" 
            placeholder="Scan barcode perhiasan berturut-turut..."
            class="w-full px-4 py-3 rounded-xl bg-white dark:bg-slate-950 border border-amber-500/40 text-sm font-mono font-bold text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500 shadow-inner"
          />
          <button 
            type="button"
            @click="handleScan"
            class="px-6 py-3 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs uppercase tracking-wider cursor-pointer shrink-0 transition-all"
          >
            Catat Scan
          </button>
        </div>
        <p class="text-[11px] text-slate-500">
          💡 Arahkan scanner ke label halter/stiker perhiasan satu per satu. Sistem langsung menandai status secara realtime.
        </p>
      </div>

      <!-- Reconciliation Item List -->
      <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden space-y-4 p-5">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-200 dark:border-slate-800 pb-3">
          <h3 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider font-mono">
            Daftar Perhiasan Hasil Rekonsiliasi ({{ audit.items.length }} Item)
          </h3>

          <div class="flex items-center gap-1.5 text-xs">
            <button 
              @click="activeTab = 'all'"
              :class="[activeTab === 'all' ? 'bg-slate-900 text-white dark:bg-white dark:text-slate-950 font-bold' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 font-medium', 'px-3 py-1 rounded-lg cursor-pointer']"
            >
              Semua
            </button>
            <button 
              @click="activeTab = 'matched'"
              :class="[activeTab === 'matched' ? 'bg-emerald-600 text-white font-bold' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 font-medium', 'px-3 py-1 rounded-lg cursor-pointer']"
            >
              Cocok
            </button>
            <button 
              @click="activeTab = 'missing'"
              :class="[activeTab === 'missing' ? 'bg-rose-600 text-white font-bold' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 font-medium', 'px-3 py-1 rounded-lg cursor-pointer']"
            >
              Belum Di-scan
            </button>
            <button 
              @click="activeTab = 'unexpected'"
              :class="[activeTab === 'unexpected' ? 'bg-amber-600 text-white font-bold' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 font-medium', 'px-3 py-1 rounded-lg cursor-pointer']"
            >
              Salah Baki
            </button>
          </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs border-collapse">
            <thead>
              <tr class="bg-slate-50 dark:bg-slate-950 border-b border-slate-200 dark:border-slate-800 text-slate-500 font-semibold uppercase text-[10px]">
                <th class="p-3">Barcode (SKU)</th>
                <th class="p-3">Nama Perhiasan</th>
                <th class="p-3 text-center">Kadar</th>
                <th class="p-3 text-right">Berat Bersih</th>
                <th class="p-3 text-center">Status Audit</th>
                <th class="p-3 text-right">Waktu Scan</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
              <tr 
                v-for="item in audit.items.filter(it => activeTab === 'all' || it.scan_status === activeTab)" 
                :key="item.id"
                class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition-colors"
              >
                <td class="p-3 font-mono font-bold text-slate-900 dark:text-white">
                  {{ item.sku }}
                </td>
                <td class="p-3 font-medium text-slate-800 dark:text-slate-200">
                  {{ item.item?.name || 'Perhiasan Tidak Dikenal' }}
                </td>
                <td class="p-3 text-center font-mono font-bold text-amber-700 dark:text-amber-400">
                  {{ item.item?.gold_rate?.code || '-' }}
                </td>
                <td class="p-3 text-right font-mono text-slate-700 dark:text-slate-300">
                  {{ formatWeight(item.item?.net_weight) }}
                </td>
                <td class="p-3 text-center">
                  <span 
                    v-if="item.scan_status === 'matched'"
                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-500/20 text-[10px] font-bold"
                  >
                    <CheckCircle2 class="w-3 h-3" /> Cocok
                  </span>
                  <span 
                    v-else-if="item.scan_status === 'missing'"
                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-rose-500/10 text-rose-700 dark:text-rose-400 border border-rose-500/20 text-[10px] font-bold"
                  >
                    <AlertCircle class="w-3 h-3" /> Belum Di-scan
                  </span>
                  <span 
                    v-else
                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-500/20 text-[10px] font-bold"
                  >
                    <AlertTriangle class="w-3 h-3" /> Nyasar (Salah Baki)
                  </span>
                </td>
                <td class="p-3 text-right font-mono text-[11px] text-slate-400">
                  {{ item.scanned_at ? new Date(item.scanned_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }) : '-' }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>

      </div>

    </div>
  </AdminLayout>
</template>
