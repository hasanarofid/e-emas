<script setup>
import { Head } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import { Printer, ArrowLeft } from 'lucide-vue-next';

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
  <Head :title="`Cetak Label Barcode - ${item.sku}`" />

  <div class="min-h-screen bg-slate-100 dark:bg-slate-950 p-6 flex flex-col items-center justify-center font-sans">
    <!-- Non-Print Toolbar -->
    <div class="print:hidden mb-6 flex items-center gap-4">
      <button 
        @click="triggerPrint"
        class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs uppercase tracking-wider flex items-center gap-2 shadow-lg shadow-amber-500/20 cursor-pointer"
      >
        <Printer class="w-4 h-4" /> Cetak Tag Stiker Sekarang
      </button>
      <button 
        onclick="window.close()" 
        class="px-4 py-2.5 rounded-xl bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold text-xs cursor-pointer"
      >
        Tutup
      </button>
    </div>

    <!-- Jewelry Tag Preview (Ukuran Standar Label Perhiasan Halter Lipat) -->
    <div class="bg-white text-black p-4 rounded-xl border border-slate-300 shadow-md w-[80mm] print:w-auto print:border-none print:shadow-none print:p-0">
      <div class="border border-black p-3 rounded-lg text-center font-mono space-y-1 text-xs">
        <!-- Store Name -->
        <p class="font-bold text-[11px] uppercase tracking-wider">{{ storeSettings.store_name }}</p>
        
        <!-- Barcode Representation -->
        <div class="my-2 flex flex-col items-center">
          <div class="h-8 w-44 bg-[repeating-linear-gradient(90deg,#000,#000_2px,#fff_2px,#fff_4px,#000_4px,#000_5px,#fff_5px,#fff_7px)] mx-auto"></div>
          <span class="text-[10px] font-bold tracking-widest mt-0.5">{{ item.sku }}</span>
        </div>

        <!-- Item Info -->
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
  body {
    background: white !important;
    color: black !important;
  }
}
</style>
