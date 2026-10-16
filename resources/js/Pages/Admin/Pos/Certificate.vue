<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { Printer, ArrowLeft, Receipt, ShieldCheck, Gem } from 'lucide-vue-next';

const props = defineProps({
  sale: {
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
  return parseFloat(val || 0).toFixed(3) + ' gr';
};

const triggerPrint = () => {
  window.print();
};
</script>

<template>
  <Head :title="`Surat Jaminan Emas - ${sale.invoice_number}`" />

  <div class="min-h-screen bg-slate-200 dark:bg-slate-950 p-4 sm:p-8 flex flex-col items-center justify-start font-serif">
    <!-- Non-Print Toolbar -->
    <div class="print:hidden mb-6 flex flex-wrap items-center justify-center gap-3 w-full max-w-4xl font-sans">
      <Link 
        :href="route('admin.pos.index')"
        class="px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold text-xs flex items-center gap-2 hover:bg-slate-300 transition-colors cursor-pointer"
      >
        <ArrowLeft class="w-4 h-4" /> Kasir POS
      </Link>

      <button 
        @click="triggerPrint"
        class="px-6 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-500 text-white font-bold text-xs uppercase tracking-wider flex items-center gap-2 shadow-lg shadow-amber-600/20 cursor-pointer"
      >
        <Printer class="w-4 h-4" /> Cetak Surat Emas (A5 / Kuarto)
      </button>

      <Link 
        :href="route('admin.pos.invoice', sale.id)"
        class="px-4 py-2.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-800 text-slate-700 dark:text-slate-300 font-semibold text-xs flex items-center gap-2 hover:bg-slate-100 transition-colors cursor-pointer"
      >
        <Receipt class="w-4 h-4" /> Lihat Struk Thermal
      </Link>
    </div>

    <!-- Official Certificate Container (A5 Landscape: 210mm x 148mm) -->
    <div class="bg-white text-slate-900 w-[210mm] min-h-[148mm] p-8 rounded-lg shadow-2xl border-4 border-double border-amber-700/60 print:w-full print:min-h-0 print:border-amber-700/80 print:shadow-none print:p-6 relative flex flex-col justify-between">
      
      <!-- Inner Thin Gold Border -->
      <div class="absolute inset-2 border border-amber-600/30 pointer-events-none"></div>

      <div>
        <!-- Certificate Header (KOP TOKO RESMI) -->
        <div class="flex items-center justify-between pb-3 border-b-2 border-amber-800/80 relative z-10">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-full border-2 border-amber-600 flex items-center justify-center bg-amber-50">
              <Gem class="w-6 h-6 text-amber-700" />
            </div>
            <div>
              <h1 class="text-lg font-black tracking-wider uppercase text-amber-950 font-serif">
                {{ storeSettings.store_legal_name || storeSettings.store_name }}
              </h1>
              <p class="text-[10px] text-slate-600 font-sans tracking-wide">
                {{ storeSettings.store_address }} • Telp: {{ storeSettings.store_phone }}
              </p>
              <p class="text-[9px] font-bold text-amber-800 font-sans tracking-widest mt-0.5">
                {{ storeSettings.store_permit_number }}
              </p>
            </div>
          </div>

          <div class="text-right">
            <div class="inline-block px-3 py-1 bg-amber-50 border border-amber-300 rounded text-center">
              <span class="block text-[8px] uppercase tracking-widest font-sans font-bold text-amber-800">SURAT JAMINAN EMAS</span>
              <span class="text-xs font-mono font-bold text-slate-900">{{ sale.invoice_number }}</span>
            </div>
            <p class="text-[9px] text-slate-500 font-sans mt-1">
              Tanggal: <b>{{ new Date(sale.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) }}</b>
            </p>
          </div>
        </div>

        <!-- Customer Identity Info Block -->
        <div class="grid grid-cols-2 gap-4 py-2.5 text-[11px] font-sans border-b border-slate-200">
          <div>
            <span class="text-slate-500 text-[10px] block">Diberikan Kepada (Pembeli):</span>
            <p class="font-bold text-slate-900 text-xs">{{ sale.customer_name }}</p>
            <p v-if="sale.customer_address" class="text-slate-600 text-[10px]">{{ sale.customer_address }}</p>
          </div>
          <div class="text-right">
            <span class="text-slate-500 text-[10px] block">Kontak / No. WhatsApp:</span>
            <p class="font-mono font-semibold text-slate-800 text-[11px]">{{ sale.customer_phone || '-' }}</p>
            <span class="text-[9px] text-emerald-700 font-semibold flex items-center justify-end gap-1">
              <ShieldCheck class="w-3 h-3 text-emerald-600" /> Jaminan Kadar Sah & Teruji
            </span>
          </div>
        </div>

        <!-- Items Table -->
        <div class="mt-3">
          <table class="w-full text-left font-sans text-[10px] border-collapse border border-slate-300">
            <thead>
              <tr class="bg-amber-100/60 text-slate-800 border-b border-slate-300 font-bold uppercase text-[9px] tracking-wider">
                <th class="p-1.5 border-r border-slate-300 text-center w-8">No</th>
                <th class="p-1.5 border-r border-slate-300">Nama Perhiasan & SKU</th>
                <th class="p-1.5 border-r border-slate-300 text-center">Kadar</th>
                <th class="p-1.5 border-r border-slate-300 text-right">Berat Kotor</th>
                <th class="p-1.5 border-r border-slate-300 text-right">Berat Batu</th>
                <th class="p-1.5 border-r border-slate-300 text-right font-bold text-slate-900">Berat Bersih</th>
                <th class="p-1.5 border-r border-slate-300 text-right">Ongkos Bikin</th>
                <th class="p-1.5 text-right font-bold text-slate-900">Total Harga</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(it, idx) in sale.items" :key="it.id" class="border-b border-slate-200">
                <td class="p-1.5 border-r border-slate-300 text-center font-mono">{{ idx + 1 }}</td>
                <td class="p-1.5 border-r border-slate-300 font-semibold">
                  {{ it.item_name }}
                  <span class="block text-[8px] text-slate-400 font-mono">{{ it.sku }}</span>
                </td>
                <td class="p-1.5 border-r border-slate-300 text-center font-bold text-amber-900">{{ it.gold_rate_name }}</td>
                <td class="p-1.5 border-r border-slate-300 text-right font-mono">{{ formatWeight(it.gross_weight) }}</td>
                <td class="p-1.5 border-r border-slate-300 text-right font-mono text-slate-500">{{ formatWeight(it.stone_weight) }}</td>
                <td class="p-1.5 border-r border-slate-300 text-right font-mono font-bold text-amber-950 bg-amber-50/40">
                  {{ formatWeight(it.net_weight) }}
                </td>
                <td class="p-1.5 border-r border-slate-300 text-right font-mono text-slate-600">{{ formatRupiah(it.crafting_fee) }}</td>
                <td class="p-1.5 text-right font-mono font-bold">{{ formatRupiah(it.subtotal) }}</td>
              </tr>
            </tbody>
            <tfoot>
              <tr class="bg-amber-50/50 font-bold border-t-2 border-slate-300 text-[10px]">
                <td colspan="5" class="p-1.5 text-right uppercase tracking-wider">Total Gramasi Bersih:</td>
                <td class="p-1.5 text-right font-mono text-amber-950 border-r border-slate-300 bg-amber-100/50">
                  {{ formatWeight(sale.total_weight) }}
                </td>
                <td class="p-1.5 text-right uppercase tracking-wider">Grand Total:</td>
                <td class="p-1.5 text-right font-mono text-xs text-amber-900">{{ formatRupiah(sale.grand_total) }}</td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>

      <!-- Bottom Terms & Signature Section -->
      <div class="mt-4 pt-3 border-t border-slate-300 font-sans text-[9px] relative z-10">
        <div class="grid grid-cols-12 gap-4">
          <!-- Buyback Policy -->
          <div class="col-span-8 space-y-1 text-slate-600 leading-tight">
            <p class="font-bold uppercase text-slate-800 text-[9px] tracking-wider">KETENTUAN & JAMINAN BELI KEMBALI (BUYBACK):</p>
            <p>{{ storeSettings.gold_buyback_policy }}</p>
            <p class="text-[8px] text-slate-400 italic mt-1">*Surat ini sah sebagai bukti kepemilikan dan jaminan keaslian emas dari {{ storeSettings.store_name }}.</p>
          </div>

          <!-- Signature Sign-off -->
          <div class="col-span-4 flex items-end justify-between text-center">
            <div class="w-24">
              <p class="text-[8px] text-slate-500 mb-8">Penerima / Pembeli,</p>
              <div class="border-b border-dotted border-slate-400"></div>
              <p class="font-bold text-[9px] mt-0.5 truncate">{{ sale.customer_name }}</p>
            </div>

            <div class="w-28">
              <p class="text-[8px] text-slate-500 mb-8">Petugas Kasir Toko,</p>
              <div class="border-b border-dotted border-slate-400"></div>
              <p class="font-bold text-[9px] mt-0.5">{{ sale.cashier?.name || 'Kasir Toko' }}</p>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<style>
@media print {
  @page {
    size: 210mm 148mm; /* A5 Landscape */
    margin: 0;
  }
  body {
    background: white !important;
    color: black !important;
    margin: 0;
    padding: 0;
  }
}
</style>
