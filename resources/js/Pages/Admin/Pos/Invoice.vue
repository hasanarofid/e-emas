<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { Printer, ArrowLeft, Send, FileText, CheckCircle2 } from 'lucide-vue-next';

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

// WhatsApp Direct Share
const sendWhatsApp = () => {
  if (!props.sale.customer_phone) {
    alert('Nomor telepon pelanggan belum diisi pada invoice ini.');
    return;
  }

  let phone = props.sale.customer_phone.replace(/[^0-9]/g, '');
  if (phone.startsWith('0')) {
    phone = '62' + phone.substring(1);
  }

  const storeName = props.storeSettings.store_name || 'Toko Mas Permata Abadi';
  let text = `*NOTA RESMI PEMBELIAN EMAS*\n`;
  text += `*${storeName}*\n`;
  text += `No. Nota: ${props.sale.invoice_number}\n`;
  text += `Tanggal: ${new Date(props.sale.created_at).toLocaleDateString('id-ID')}\n`;
  text += `Kepada Yth: ${props.sale.customer_name}\n`;
  text += `--------------------------------\n`;

  props.sale.items.forEach((it, idx) => {
    text += `${idx + 1}. ${it.item_name}\n`;
    text += `   Kadar: ${it.gold_rate_name} | Berat: ${parseFloat(it.net_weight).toFixed(3)} gr\n`;
    text += `   Total: ${formatRupiah(it.subtotal)}\n`;
  });

  text += `--------------------------------\n`;
  text += `*Total Gramasi:* ${parseFloat(props.sale.total_weight).toFixed(3)} gr\n`;
  text += `*Grand Total:* ${formatRupiah(props.sale.grand_total)}\n`;
  text += `*Pembayaran:* ${props.sale.payment_method.toUpperCase()}\n\n`;
  text += `Terima kasih atas kunjungan Anda. Simpan bukti ini untuk kemudahan buyback/tukar tambah.`;

  window.open(`https://wa.me/${phone}?text=${encodeURIComponent(text)}`, '_blank');
};
</script>

<template>
  <Head :title="`Struk Kasir Thermal - ${sale.invoice_number}`" />

  <div class="min-h-screen bg-slate-100 dark:bg-slate-950 p-4 sm:p-6 flex flex-col items-center justify-start font-sans">
    <!-- Non-Print Toolbar -->
    <div class="print:hidden mb-6 flex flex-wrap items-center justify-center gap-3 w-full max-w-xl">
      <Link 
        :href="route('admin.pos.index')"
        class="px-4 py-2.5 rounded-xl bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold text-xs flex items-center gap-2 hover:bg-slate-300 dark:hover:bg-slate-700 transition-colors cursor-pointer"
      >
        <ArrowLeft class="w-4 h-4" /> Kasir POS
      </Link>

      <button 
        @click="triggerPrint"
        class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs uppercase tracking-wider flex items-center gap-2 shadow-lg shadow-amber-500/20 cursor-pointer"
      >
        <Printer class="w-4 h-4" /> Cetak Struk Thermal
      </button>

      <Link 
        :href="route('admin.pos.certificate', sale.id)"
        class="px-4 py-2.5 rounded-xl bg-amber-500/10 hover:bg-amber-500/20 text-amber-700 dark:text-amber-400 border border-amber-500/20 font-bold text-xs flex items-center gap-2 transition-colors cursor-pointer"
      >
        <FileText class="w-4 h-4" /> Cetak Surat Emas (A5)
      </Link>

      <button 
        v-if="sale.customer_phone"
        @click="sendWhatsApp"
        class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs flex items-center gap-2 shadow-md cursor-pointer"
      >
        <Send class="w-4 h-4" /> Kirim WhatsApp
      </button>
    </div>

    <!-- Thermal Receipt Body (80mm width standard) -->
    <div class="bg-white text-black p-6 rounded-2xl border border-slate-300 shadow-xl w-full max-w-[80mm] print:w-full print:max-w-none print:border-none print:shadow-none print:p-0 font-mono text-xs">
      
      <!-- Store Header -->
      <div class="text-center pb-3 border-b border-dashed border-black">
        <h2 class="font-extrabold text-sm uppercase tracking-wider">{{ storeSettings.store_name }}</h2>
        <p class="text-[10px] text-gray-700 mt-0.5">{{ storeSettings.store_address }}</p>
        <p class="text-[10px] text-gray-700">Telp: {{ storeSettings.store_phone }}</p>
        <p class="text-[9px] text-gray-600 font-bold mt-0.5">{{ storeSettings.store_permit_number }}</p>
      </div>

      <!-- Transaction Info -->
      <div class="py-2.5 border-b border-dashed border-black space-y-1 text-[11px]">
        <div class="flex justify-between">
          <span>No. Nota:</span>
          <span class="font-bold">{{ sale.invoice_number }}</span>
        </div>
        <div class="flex justify-between">
          <span>Tanggal:</span>
          <span>{{ new Date(sale.created_at).toLocaleDateString('id-ID') }} {{ new Date(sale.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) }}</span>
        </div>
        <div class="flex justify-between">
          <span>Kasir:</span>
          <span>{{ sale.cashier?.name || 'Kasir' }}</span>
        </div>
        <div class="flex justify-between">
          <span>Pelanggan:</span>
          <span class="font-bold truncate max-w-[140px]">{{ sale.customer_name }}</span>
        </div>
        <div v-if="sale.customer_phone" class="flex justify-between">
          <span>No. HP:</span>
          <span>{{ sale.customer_phone }}</span>
        </div>
      </div>

      <!-- Item List -->
      <div class="py-3 border-b border-dashed border-black space-y-2">
        <div v-for="item in sale.items" :key="item.id" class="space-y-0.5">
          <p class="font-bold text-[11px]">{{ item.item_name }}</p>
          <div class="flex justify-between text-[10px] text-gray-700">
            <span>{{ item.sku }} • {{ item.gold_rate_name }}</span>
            <span>{{ formatWeight(item.net_weight) }}</span>
          </div>
          <div class="flex justify-between text-[10px] text-gray-700">
            <span>@{{ formatRupiah(item.gold_price_per_gram) }} + Ongkos {{ formatRupiah(item.crafting_fee) }}</span>
            <span class="font-bold text-black">{{ formatRupiah(item.subtotal) }}</span>
          </div>
        </div>
      </div>

      <!-- Calculations -->
      <div class="py-2.5 border-b border-dashed border-black space-y-1 text-[11px]">
        <div class="flex justify-between">
          <span>Total Berat:</span>
          <span class="font-bold">{{ formatWeight(sale.total_weight) }}</span>
        </div>
        <div class="flex justify-between">
          <span>Subtotal:</span>
          <span>{{ formatRupiah(sale.subtotal) }}</span>
        </div>
        <div v-if="sale.discount > 0" class="flex justify-between text-gray-700">
          <span>Diskon:</span>
          <span>-{{ formatRupiah(sale.discount) }}</span>
        </div>
        <div class="flex justify-between font-extrabold text-xs pt-1 border-t border-black">
          <span>GRAND TOTAL:</span>
          <span>{{ formatRupiah(sale.grand_total) }}</span>
        </div>
      </div>

      <!-- Payment Summary -->
      <div class="py-2.5 border-b border-dashed border-black space-y-1 text-[11px]">
        <div class="flex justify-between">
          <span>Metode:</span>
          <span class="uppercase font-bold">{{ sale.payment_method }}</span>
        </div>
        <div v-if="sale.bank_account" class="flex justify-between text-[10px]">
          <span>Rek Tujuan:</span>
          <span>{{ sale.bank_account.bank_name }} - {{ sale.bank_account.account_number }}</span>
        </div>
        <div class="flex justify-between">
          <span>Bayar:</span>
          <span>{{ formatRupiah(sale.amount_paid) }}</span>
        </div>
        <div class="flex justify-between">
          <span>Kembalian:</span>
          <span class="font-bold">{{ formatRupiah(sale.change_due) }}</span>
        </div>
      </div>

      <!-- Barcode / QR Section for Quick Scan Buyback -->
      <div class="pt-3 pb-2 text-center flex flex-col items-center">
        <!-- CSS-generated Barcode 1D line visual -->
        <div class="h-8 w-44 bg-[repeating-linear-gradient(90deg,#000,#000_2px,#fff_2px,#fff_4px,#000_4px,#000_5px,#fff_5px,#fff_7px)] mx-auto"></div>
        <p class="text-[9px] font-bold tracking-widest mt-1">{{ sale.invoice_number }}</p>
      </div>

      <!-- Policy & Footer -->
      <div class="text-center text-[9px] text-gray-700 space-y-1 pt-1">
        <p class="font-bold">SYARAT PENJUALAN KEMBALI (BUYBACK):</p>
        <p class="leading-tight">Barang dapat dijual kembali dengan membawa nota resmi ini. Potongan mengikuti kurs pasar hari berjalan.</p>
        <p class="pt-2 font-bold">{{ storeSettings.receipt_footer }}</p>
      </div>

    </div>
  </div>
</template>

<style>
@media print {
  @page {
    margin: 0;
    size: 80mm auto;
  }
  body {
    background: white !important;
    color: black !important;
    margin: 0;
    padding: 0;
  }
}
</style>
