<script setup>
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Printer, ArrowLeft, QrCode, Sparkles } from 'lucide-vue-next';

const props = defineProps({
  item: {
    type: Object,
    required: true
  },
  storeSettings: {
    type: Object,
    default: () => ({})
  }
});

const tagFormat = ref('dumbbell'); // 'dumbbell' (2.5" x 1.25" Tail Tag) | 'fold80' (Standar Lipat)

const formatRupiah = (val) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0
  }).format(val || 0);
};

const formatWeight = (val) => {
  return parseFloat(val || 0).toFixed(3) + 'g';
};

const triggerPrint = () => {
  window.print();
};
</script>

<template>
  <Head :title="`Cetak Stiker Label Emas - ${item.sku}`" />

  <div class="min-h-screen bg-slate-100 dark:bg-slate-950 p-6 flex flex-col items-center justify-start font-sans">
    <!-- Non-Print Toolbar -->
    <div class="print:hidden mb-8 flex flex-col items-center gap-4 w-full max-w-xl">
      <div class="flex items-center gap-3">
        <button 
          @click="tagFormat = 'dumbbell'" 
          :class="[
            tagFormat === 'dumbbell' 
              ? 'bg-amber-500 text-slate-950 font-bold' 
              : 'bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 font-medium border border-slate-200 dark:border-slate-800',
            'px-4 py-2 rounded-xl text-xs transition-all cursor-pointer'
          ]"
        >
          Format Stiker Buntut / Dumbbell (2.5" × 1.25")
        </button>
        <button 
          @click="tagFormat = 'fold80'" 
          :class="[
            tagFormat === 'fold80' 
              ? 'bg-amber-500 text-slate-950 font-bold' 
              : 'bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 font-medium border border-slate-200 dark:border-slate-800',
            'px-4 py-2 rounded-xl text-xs transition-all cursor-pointer'
          ]"
        >
          Format Tag Lipat Halter 80mm
        </button>
      </div>

      <div class="flex items-center gap-3">
        <button 
          @click="triggerPrint"
          class="px-6 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs uppercase tracking-wider flex items-center gap-2 shadow-lg shadow-amber-500/20 cursor-pointer"
        >
          <Printer class="w-4 h-4" /> Cetak Label Stiker Sekarang
        </button>
        <button 
          onclick="window.close()" 
          class="px-4 py-2.5 rounded-xl bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold text-xs cursor-pointer"
        >
          Tutup
        </button>
      </div>
      <p class="text-[11px] text-slate-500 text-center">
        💡 <b>Tips Cetak:</b> Atur Margin ke "None" pada dialog print browser untuk hasil presisi di printer thermal (TSC, Zebra, Xprinter).
      </p>
    </div>

    <!-- FORMAT 1: JEWELRY DUMBBELL / BUTTERFLY TAIL TAG (Request Klien Gambar 1) -->
    <!-- Ukuran Real Stiker: 64mm x 32mm (2.5" x 1.25") -->
    <div 
      v-if="tagFormat === 'dumbbell'"
      class="bg-white text-black p-4 rounded-xl border border-slate-300 shadow-xl w-[70mm] h-[30mm] print:w-[70mm] print:h-[30mm] print:border-none print:shadow-none print:p-0 flex items-center justify-between font-mono relative overflow-hidden"
    >
      <!-- Flap 1 (Sayap Kiri: QR Code + Karat + Berat Bersih) -->
      <div class="w-[28mm] h-[28mm] border border-dashed border-gray-400 print:border-none p-1 flex flex-col justify-between items-center text-center">
        <!-- QR Code SVG Representation -->
        <div class="w-12 h-12 bg-black p-0.5 rounded flex items-center justify-center">
          <div class="w-full h-full bg-white flex flex-col items-center justify-center">
            <span class="text-[7px] font-black tracking-tighter">QR-EMAS</span>
            <div class="w-8 h-8 bg-[repeating-conic-gradient(#000_0_25%,#fff_0_50%)] bg-[length:4px_4px]"></div>
          </div>
        </div>
        <div class="leading-tight mt-0.5">
          <span class="text-[9px] font-black">{{ item.gold_rate?.code }}</span>
          <span class="block text-[8px] font-extrabold text-black">Net: {{ formatWeight(item.net_weight) }}</span>
        </div>
      </div>

      <!-- Center Thin Dumbbell Tail (Tangkai Buntut Bening untuk Lingkar Cincin/Kalung) -->
      <div class="w-[12mm] h-full flex flex-col items-center justify-center">
        <div class="w-full border-t-2 border-dotted border-gray-400 print:border-gray-200"></div>
        <span class="text-[6px] text-gray-400 uppercase tracking-tighter print:hidden font-sans">Buntut</span>
      </div>

      <!-- Flap 2 (Sayap Kanan: Barcode 1D + SKU + Toko + Harga) -->
      <div class="w-[28mm] h-[28mm] border border-dashed border-gray-400 print:border-none p-1 flex flex-col justify-between text-left">
        <div>
          <p class="text-[7px] font-bold uppercase truncate max-w-[26mm]">{{ storeSettings.store_name }}</p>
          <p class="text-[6px] text-gray-700 truncate max-w-[26mm]">{{ item.name }}</p>
        </div>

        <!-- Barcode 1D Lines -->
        <div class="my-0.5">
          <div class="h-5 w-full bg-[repeating-linear-gradient(90deg,#000,#000_1px,#fff_1px,#fff_2.5px,#000_2.5px,#000_3.5px,#fff_3.5px,#fff_5px)]"></div>
          <span class="text-[7px] font-black tracking-tight block text-center mt-0.5">{{ item.sku }}</span>
        </div>

        <div class="flex items-center justify-between text-[7px] font-bold border-t border-black pt-0.5">
          <span>Ktr: {{ formatWeight(item.gross_weight) }}</span>
          <span>{{ formatRupiah(item.total_price) }}</span>
        </div>
      </div>
    </div>

    <!-- FORMAT 2: STANDAR HALTER LIPAT 80mm -->
    <div 
      v-else
      class="bg-white text-black p-4 rounded-xl border border-slate-300 shadow-md w-[80mm] print:w-[80mm] print:border-none print:shadow-none print:p-0"
    >
      <div class="border border-black p-3 rounded-lg text-center font-mono space-y-1 text-xs">
        <p class="font-bold text-[11px] uppercase tracking-wider">{{ storeSettings.store_name }}</p>
        
        <div class="my-2 flex flex-col items-center">
          <div class="h-8 w-44 bg-[repeating-linear-gradient(90deg,#000,#000_2px,#fff_2px,#fff_4px,#000_4px,#000_5px,#fff_5px,#fff_7px)] mx-auto"></div>
          <span class="text-[10px] font-bold tracking-widest mt-0.5">{{ item.sku }}</span>
        </div>

        <p class="text-[10px] font-bold truncate max-w-[70mm] mx-auto">{{ item.name }}</p>

        <div class="grid grid-cols-2 gap-1 text-[9px] pt-1 border-t border-dashed border-gray-400">
          <div class="text-left">
            <span>Kdr: <b>{{ item.gold_rate?.code }}</b></span><br />
            <span>Ktr: <b>{{ formatWeight(item.gross_weight) }}</b></span>
          </div>
          <div class="text-right">
            <span>Baki: <b>{{ item.tray?.code || '-' }}</b></span><br />
            <span>Brs: <b>{{ formatWeight(item.net_weight) }}</b></span>
          </div>
        </div>

        <div class="pt-1 border-t border-black text-center font-bold text-[11px]">
          {{ formatRupiah(item.total_price) }}
        </div>
      </div>
    </div>

  </div>
</template>

<style>
@media print {
  @page {
    margin: 0;
    size: auto;
  }
  body {
    background: white !important;
    color: black !important;
    margin: 0;
    padding: 0;
  }
}
</style>
