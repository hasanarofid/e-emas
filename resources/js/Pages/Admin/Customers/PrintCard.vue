<script setup>
import { Head } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import { Gem, Printer, ArrowLeft } from 'lucide-vue-next';

const props = defineProps({
  customer: {
    type: Object,
    required: true,
  },
  store: {
    type: Object,
    required: true,
  },
});

onMounted(() => {
  // Otomatis buka dialog print browser saat halaman dimuat
  setTimeout(() => {
    window.print();
  }, 500);
});

// Generator Barcode Pattern SVG Sederhana (Clean & Universal)
const generateBarcodeBars = (text) => {
  const bars = [];
  let x = 10;
  for (let i = 0; i < text.length; i++) {
    const charCode = text.charCodeAt(i);
    // Kombinasi tebal/tipis baris berdasarkan charCode
    const pattern = [(charCode % 3) + 1, ((charCode >> 1) % 2) + 1, ((charCode >> 2) % 3) + 1, 2];
    pattern.forEach((width, index) => {
      if (index % 2 === 0) {
        bars.push({ x, width: width * 1.5 });
      }
      x += width * 2;
    });
  }
  return { bars, totalWidth: x + 10 };
};

const barcodeData = generateBarcodeBars(props.customer.code);
</script>

<template>
  <div class="min-h-screen bg-slate-100 p-8 flex flex-col items-center justify-center font-sans antialiased print:bg-white print:p-0">
    <Head :title="`Kartu Member - ${customer.name} (${customer.code})`" />

    <!-- Print Control Bar (Tersembunyi saat dicetak) -->
    <div class="mb-6 flex items-center gap-4 print:hidden">
      <button 
        onclick="window.history.back()" 
        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-slate-300 text-slate-700 text-sm font-semibold hover:bg-slate-50 transition-colors shadow-sm"
      >
        <ArrowLeft class="w-4 h-4" />
        <span>Kembali</span>
      </button>

      <button 
        onclick="window.print()" 
        class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 text-sm font-bold shadow-md transition-all cursor-pointer"
      >
        <Printer class="w-4 h-4" />
        <span>Cetak Kartu Sekarang</span>
      </button>
    </div>

    <!-- Member Card Box (Ukuran Standar ID Card / Sitokmas Print Spec: 85.6mm x 53.98mm) -->
    <div class="w-[360px] h-[225px] bg-white border border-slate-300 rounded-2xl p-5 shadow-lg relative flex flex-col justify-between overflow-hidden print:shadow-none print:border print:border-black print:m-0 print:rounded-lg">
      
      <!-- Card Header -->
      <div class="flex items-center justify-between border-b border-slate-200 pb-3">
        <div class="flex items-center gap-2.5">
          <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-amber-600 via-amber-400 to-yellow-200 flex items-center justify-center text-slate-950 font-bold p-0.5 shadow-sm">
            <div class="w-full h-full bg-white rounded-full flex items-center justify-center">
              <Gem class="w-4 h-4 text-amber-500" />
            </div>
          </div>
          <div>
            <h4 class="text-xs font-black uppercase tracking-wider text-slate-900 leading-tight">
              MEMBER CARD
            </h4>
            <p class="text-[10px] font-bold text-amber-600 tracking-wide">
              {{ store.name }}
            </p>
          </div>
        </div>

        <span class="text-[9px] font-mono px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 font-bold uppercase">
          {{ customer.level }}
        </span>
      </div>

      <!-- Card Body / Member Information -->
      <div class="my-auto space-y-1 text-xs text-slate-800">
        <div class="flex">
          <span class="w-24 text-[11px] text-slate-500">Nama</span>
          <span class="text-[11px] font-bold text-slate-900">: {{ customer.name }}</span>
        </div>
        <div class="flex">
          <span class="w-24 text-[11px] text-slate-500">Kode Pelanggan</span>
          <span class="text-[11px] font-mono font-bold text-amber-700">: {{ customer.code }}</span>
        </div>
        <div class="flex">
          <span class="w-24 text-[11px] text-slate-500">No. Telp / WA</span>
          <span class="text-[11px] font-mono">: {{ customer.phone || '-' }}</span>
        </div>
        <div class="flex">
          <span class="w-24 text-[11px] text-slate-500">Alamat</span>
          <span class="text-[11px] truncate max-w-[200px]">: {{ customer.address }}</span>
        </div>
      </div>

      <!-- Card Footer / Barcode -->
      <div class="border-t border-slate-100 pt-2 flex flex-col items-center justify-center">
        <!-- Barcode SVG Render -->
        <svg :width="barcodeData.totalWidth" height="34" class="max-w-[240px]">
          <rect 
            v-for="(bar, idx) in barcodeData.bars" 
            :key="idx" 
            :x="bar.x" 
            y="0" 
            :width="bar.width" 
            height="34" 
            fill="#0F172A" 
          />
        </svg>
        <span class="font-mono text-[9px] font-bold tracking-widest text-slate-700 mt-0.5">
          *{{ customer.code }}*
        </span>
      </div>

    </div>

    <!-- Cetak Note -->
    <p class="text-xs text-slate-400 mt-4 print:hidden">
      Ukuran kartu optimal dicetak pada kertas kartu ID atau A4 potong standar.
    </p>
  </div>
</template>

<style scoped>
@media print {
  @page {
    size: auto;
    margin: 15mm;
  }
  body {
    background: transparent !important;
  }
}
</style>
