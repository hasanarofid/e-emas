<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { Printer, ArrowLeft, Plus, CheckCircle2, ShieldCheck, Gem } from 'lucide-vue-next';

const props = defineProps({
  buyback: {
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
  <Head :title="`Kuitansi Buyback - ${buyback.buyback_number}`" />

  <div class="min-h-screen bg-slate-200 dark:bg-slate-950 p-4 sm:p-8 flex flex-col items-center justify-start font-sans">
    
    <!-- Non-Print Toolbar -->
    <div class="print:hidden mb-6 flex flex-wrap items-center justify-center gap-3 w-full max-w-2xl">
      <Link 
        :href="route('admin.buybacks.index')"
        class="px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold text-xs flex items-center gap-2 hover:bg-slate-300 transition-colors cursor-pointer"
      >
        <ArrowLeft class="w-4 h-4" /> Daftar Buyback
      </Link>

      <button 
        @click="triggerPrint"
        class="px-6 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-500 text-white font-bold text-xs uppercase tracking-wider flex items-center gap-2 shadow-lg shadow-amber-600/20 cursor-pointer"
      >
        <Printer class="w-4 h-4" /> Cetak Kuitansi Buyback
      </button>

      <Link 
        :href="route('admin.buybacks.create')"
        class="px-4 py-2.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-800 text-slate-700 dark:text-slate-300 font-semibold text-xs flex items-center gap-2 hover:bg-slate-100 transition-colors cursor-pointer"
      >
        <Plus class="w-4 h-4" /> Transaksi Baru
      </Link>
    </div>

    <!-- Official Buyback Receipt Slip Container (A5 Landscape: 210mm x 148mm) -->
    <div class="bg-white text-slate-900 w-[210mm] min-h-[148mm] p-8 rounded-lg shadow-2xl border-2 border-slate-400 print:w-full print:min-h-0 print:border-none print:shadow-none print:p-6 flex flex-col justify-between">
      
      <div>
        <!-- Store Header (KOP TOKO) -->
        <div class="flex items-center justify-between pb-3 border-b-2 border-slate-800">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-slate-900 text-amber-400 flex items-center justify-center">
              <Gem class="w-5 h-5" />
            </div>
            <div>
              <h2 class="text-base font-extrabold tracking-wide uppercase text-slate-900">
                {{ storeSettings.store_legal_name || storeSettings.store_name }}
              </h2>
              <p class="text-[10px] text-slate-600">
                {{ storeSettings.store_address }} • Telp: {{ storeSettings.store_phone }}
              </p>
              <p class="text-[9px] font-bold text-slate-700 font-mono">
                {{ storeSettings.store_permit_number }}
              </p>
            </div>
          </div>

          <div class="text-right">
            <span class="inline-block px-2.5 py-0.5 bg-slate-100 border border-slate-300 rounded font-mono font-bold text-xs">
              {{ buyback.buyback_number }}
            </span>
            <p class="text-[9px] text-slate-500 mt-1 font-mono">
              {{ new Date(buyback.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) }} {{ new Date(buyback.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) }}
            </p>
          </div>
        </div>

        <!-- Document Title -->
        <div class="text-center py-2">
          <h1 class="text-xs font-black uppercase tracking-widest text-slate-900">
            KUITANSI PEMBELIAN KEMBALI EMAS (BUYBACK)
          </h1>
        </div>

        <!-- Seller & Transaction Info -->
        <div class="grid grid-cols-2 gap-4 py-2 border-y border-slate-200 text-[11px]">
          <div>
            <span class="text-slate-500 text-[10px] block">Penjual Emas / Pelanggan:</span>
            <p class="font-bold text-slate-900">{{ buyback.customer_name }}</p>
            <p v-if="buyback.customer_phone" class="text-slate-600 text-[10px] font-mono">{{ buyback.customer_phone }}</p>
            <p v-if="buyback.customer_nik" class="text-slate-500 text-[10px] font-mono">NIK: {{ buyback.customer_nik }}</p>
          </div>

          <div class="text-right">
            <span class="text-slate-500 text-[10px] block">Referensi Nota Asal:</span>
            <p class="font-bold font-mono text-slate-900">
              {{ buyback.original_invoice_number ? buyback.original_invoice_number : 'Emas Luar (Tanpa Surat Toko)' }}
            </p>
            <span class="text-[10px] text-slate-600 block mt-0.5">
              Metode Pembayaran: <b class="uppercase">{{ buyback.payment_method }}</b>
            </span>
          </div>
        </div>

        <!-- Items Table -->
        <div class="mt-3">
          <table class="w-full text-left text-[10px] border-collapse border border-slate-300">
            <thead>
              <tr class="bg-slate-100 text-slate-800 border-b border-slate-300 font-bold uppercase text-[9px]">
                <th class="p-1.5 border-r border-slate-300 text-center w-8">No</th>
                <th class="p-1.5 border-r border-slate-300">Nama Barang & Kondisi</th>
                <th class="p-1.5 border-r border-slate-300 text-center">Kadar</th>
                <th class="p-1.5 border-r border-slate-300 text-right">Berat Timbang</th>
                <th class="p-1.5 border-r border-slate-300 text-right">Kurs Beli/gr</th>
                <th class="p-1.5 border-r border-slate-300 text-right">Potongan</th>
                <th class="p-1.5 text-right font-bold">Total Diterima</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(it, idx) in buyback.items" :key="it.id" class="border-b border-slate-200">
                <td class="p-1.5 border-r border-slate-300 text-center font-mono">{{ idx + 1 }}</td>
                <td class="p-1.5 border-r border-slate-300">
                  <span class="font-bold">{{ it.item_name }}</span>
                  <span v-if="it.condition_notes" class="block text-[8px] text-slate-500 italic">({{ it.condition_notes }})</span>
                </td>
                <td class="p-1.5 border-r border-slate-300 text-center font-bold text-amber-900">{{ it.gold_rate?.code }}</td>
                <td class="p-1.5 border-r border-slate-300 text-right font-mono font-bold">{{ formatWeight(it.weight_actual) }}</td>
                <td class="p-1.5 border-r border-slate-300 text-right font-mono">{{ formatRupiah(it.buy_price_per_gram) }}</td>
                <td class="p-1.5 border-r border-slate-300 text-right font-mono text-rose-600 font-semibold">
                  {{ it.deduction_amount > 0 ? '-' + formatRupiah(it.deduction_amount) : '0' }}
                </td>
                <td class="p-1.5 text-right font-mono font-bold">{{ formatRupiah(it.total_amount) }}</td>
              </tr>
            </tbody>
            <tfoot>
              <tr class="bg-slate-50 font-bold border-t-2 border-slate-300 text-[10px]">
                <td colspan="3" class="p-1.5 text-right uppercase">Total Berat Emas:</td>
                <td class="p-1.5 text-right font-mono text-slate-900 border-r border-slate-300">{{ formatWeight(buyback.total_weight_actual) }}</td>
                <td colspan="2" class="p-1.5 text-right uppercase">Total Kas Dibayarkan:</td>
                <td class="p-1.5 text-right font-mono text-xs text-slate-900 bg-slate-100 font-black">{{ formatRupiah(buyback.grand_total) }}</td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>

      <!-- Bottom Signature Block -->
      <div class="mt-4 pt-3 border-t border-slate-300 text-[9px]">
        <div class="grid grid-cols-12 gap-4">
          <div class="col-span-8 text-slate-500 leading-tight">
            <p class="font-bold uppercase text-slate-700">Pernyataan Penjual:</p>
            <p>Emas yang dijual adalah milik sah pribadi dan tidak tersangkut tindak pidana apapun. Uang pembayaran telah diterima dengan benar dan pas sesuai rincian di atas.</p>
          </div>

          <div class="col-span-4 flex items-end justify-between text-center">
            <div class="w-24">
              <p class="text-[8px] text-slate-500 mb-8">Yang Menyerahkan,</p>
              <div class="border-b border-dotted border-slate-400"></div>
              <p class="font-bold text-[9px] mt-0.5 truncate">{{ buyback.customer_name }}</p>
            </div>

            <div class="w-28">
              <p class="text-[8px] text-slate-500 mb-8">Petugas Toko / Kasir,</p>
              <div class="border-b border-dotted border-slate-400"></div>
              <p class="font-bold text-[9px] mt-0.5">{{ buyback.cashier?.name || 'Kasir Toko' }}</p>
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
