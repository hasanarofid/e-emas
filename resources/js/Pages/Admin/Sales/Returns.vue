<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { 
  Undo2, 
  Search, 
  Plus, 
  Coins, 
  Clock, 
  CheckCircle2, 
  AlertCircle, 
  X, 
  Check, 
  FileText,
  Sparkles,
  ShoppingBag
} from 'lucide-vue-next';

const props = defineProps({
  returns: {
    type: Array,
    required: true,
  },
  stats: {
    type: Object,
    required: true,
  },
});

const search = ref('');
const isModalOpen = ref(false);

const form = useForm({
  invoice_number: '',
  customer_name: '',
  item_name: '',
  weight: '',
  original_price: '',
  deduction_amount: '',
  refund_amount: '',
  reason: '',
});

const openModal = () => {
  form.reset();
  isModalOpen.value = true;
};

const closeModal = () => {
  isModalOpen.value = false;
  form.reset();
};

const submitReturn = () => {
  alert('Permohonan retur penjualan berhasil dicatat dalam antrean supervisor.');
  closeModal();
};

const formatRupiah = (number) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0,
  }).format(number || 0);
};
</script>

<template>
  <AdminLayout>
    <Head title="Data Retur Penjualan Emas - POS Emas" />

    <div class="space-y-6">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2 text-xs font-mono text-amber-500 uppercase tracking-widest">
            <Undo2 class="w-4 h-4" />
            <span>Garansi & Pengembalian Produk</span>
          </div>
          <h2 class="text-2xl font-bold tracking-tight text-white mt-1">
            Data Retur Penjualan Perhiasan
          </h2>
          <p class="text-sm text-slate-400">
            Pencatatan pengembalian atau penukaran perhiasan emas (tukar ukuran jari, cacat pabrik, pembatalan nota).
          </p>
        </div>

        <button
          @click="openModal"
          class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 font-bold text-sm shadow-lg shadow-amber-500/20 transition-all cursor-pointer"
        >
          <Plus class="w-4 h-4" />
          <span>Buat Retur Penjualan Baru</span>
        </button>
      </div>

      <!-- Quick Summary Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-4 rounded-2xl bg-[#1E1E38] border border-slate-800 flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400 shrink-0">
            <Undo2 class="w-6 h-6" />
          </div>
          <div>
            <p class="text-xs font-medium text-slate-400">Total Transaksi Retur</p>
            <p class="text-xl font-bold text-white mt-0.5">{{ stats.total_returns || 0 }} Klaim</p>
          </div>
        </div>

        <div class="p-4 rounded-2xl bg-[#1E1E38] border border-slate-800 flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-rose-500/10 border border-rose-500/20 flex items-center justify-center text-rose-400 shrink-0">
            <Coins class="w-6 h-6" />
          </div>
          <div>
            <p class="text-xs font-medium text-slate-400">Total Dana Pengembalian</p>
            <p class="text-lg font-bold text-rose-400 mt-0.5">{{ formatRupiah(stats.total_refunded) }}</p>
          </div>
        </div>

        <div class="p-4 rounded-2xl bg-[#1E1E38] border border-slate-800 flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 shrink-0">
            <CheckCircle2 class="w-6 h-6" />
          </div>
          <div>
            <p class="text-xs font-medium text-slate-400">Potongan Ongkos Bikin</p>
            <p class="text-lg font-bold text-emerald-400 mt-0.5">{{ formatRupiah(stats.total_deductions) }}</p>
          </div>
        </div>

        <div class="p-4 rounded-2xl bg-[#1E1E38] border border-slate-800 flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400 shrink-0">
            <Clock class="w-6 h-6" />
          </div>
          <div>
            <p class="text-xs font-medium text-slate-400">Menunggu Verifikasi</p>
            <p class="text-xl font-bold text-white mt-0.5">{{ stats.pending_count || 0 }} Berkas</p>
          </div>
        </div>
      </div>

      <!-- Table Section -->
      <div class="rounded-2xl bg-[#1E1E38] border border-slate-800 overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-sm text-slate-300">
            <thead class="bg-[#151528] text-xs uppercase text-slate-400 font-semibold border-b border-slate-800">
              <tr>
                <th class="px-6 py-4">Nomor Retur / Waktu</th>
                <th class="px-6 py-4">Faktur Asal</th>
                <th class="px-6 py-4">Pelanggan</th>
                <th class="px-6 py-4">Barang & Karat</th>
                <th class="px-6 py-4">Alasan Retur</th>
                <th class="px-6 py-4 text-right">Potongan Bikin</th>
                <th class="px-6 py-4 text-right">Nilai Refund</th>
                <th class="px-6 py-4 text-center">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/80">
              <tr
                v-for="ret in returns"
                :key="ret.id"
                class="hover:bg-[#25254B]/40 transition-colors"
              >
                <td class="px-6 py-4">
                  <div class="font-mono font-bold text-amber-400 text-xs">{{ ret.return_number }}</div>
                  <div class="text-[11px] text-slate-400 font-mono">{{ ret.return_date }}</div>
                </td>
                <td class="px-6 py-4 font-mono text-xs text-white">
                  {{ ret.invoice_number }}
                </td>
                <td class="px-6 py-4">
                  <div class="font-semibold text-white text-xs">{{ ret.customer_name }}</div>
                  <div class="text-[11px] text-slate-400 font-mono">{{ ret.customer_phone }}</div>
                </td>
                <td class="px-6 py-4">
                  <div class="text-xs font-medium text-slate-200">{{ ret.item_name }}</div>
                  <div class="text-[11px] text-amber-400 font-mono">{{ ret.carat }} • {{ ret.weight }} Gr</div>
                </td>
                <td class="px-6 py-4 text-xs text-slate-300 max-w-xs">
                  {{ ret.reason }}
                </td>
                <td class="px-6 py-4 text-right font-mono text-xs text-slate-300">
                  {{ formatRupiah(ret.deduction_amount) }}
                </td>
                <td class="px-6 py-4 text-right font-mono font-bold text-rose-400 text-xs">
                  {{ formatRupiah(ret.refund_amount) }}
                </td>
                <td class="px-6 py-4 text-center">
                  <span
                    class="px-2.5 py-1 rounded-full text-[11px] font-semibold border"
                    :class="[
                      ret.status === 'selesai' ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' :
                      'bg-amber-500/10 text-amber-400 border-amber-500/20'
                    ]"
                  >
                    {{ ret.status === 'selesai' ? 'Disetujui / Selesai' : 'Dalam Proses' }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Modal Form Retur -->
    <div 
      v-if="isModalOpen" 
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm"
    >
      <div class="w-full max-w-lg rounded-2xl bg-[#1E1E38] border border-slate-700 shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
        <div class="p-5 border-b border-slate-800 flex items-center justify-between">
          <div class="flex items-center gap-2.5">
            <div class="w-9 h-9 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400">
              <Undo2 class="w-5 h-5" />
            </div>
            <div>
              <h3 class="text-base font-bold text-white">Buat Form Retur Penjualan</h3>
              <p class="text-xs text-slate-400">Input nota perhiasan yang akan ditukar atau dibatalkan.</p>
            </div>
          </div>
          <button @click="closeModal" class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-[#25254B]">
            <X class="w-5 h-5" />
          </button>
        </div>

        <form @submit.prevent="submitReturn" class="p-5 space-y-4">
          <div>
            <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Nomor Nota / Faktur Asal</label>
            <input 
              v-model="form.invoice_number"
              type="text" 
              placeholder="Contoh: INV-20261017-0001" 
              class="w-full px-3.5 py-2.5 rounded-xl bg-[#151528] border border-slate-700 text-white text-sm focus:outline-none focus:border-amber-500 font-mono"
              required 
            />
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Nama Pelanggan</label>
            <input 
              v-model="form.customer_name"
              type="text" 
              placeholder="Contoh: Ibu Rina Permatasari" 
              class="w-full px-3.5 py-2.5 rounded-xl bg-[#151528] border border-slate-700 text-white text-sm focus:outline-none focus:border-amber-500"
              required 
            />
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Nama Barang & Alasan Retur</label>
            <textarea 
              v-model="form.reason"
              rows="3" 
              placeholder="Jelaskan alasan retur (salah ukuran, tukar model, cacat pengait)..." 
              class="w-full px-3.5 py-2.5 rounded-xl bg-[#151528] border border-slate-700 text-white text-sm focus:outline-none focus:border-amber-500"
              required
            ></textarea>
          </div>

          <div class="pt-4 border-t border-slate-800 flex items-center justify-end gap-3">
            <button type="button" @click="closeModal" class="px-4 py-2.5 rounded-xl bg-[#25254B] text-slate-300 text-sm font-medium">
              Batal
            </button>
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-sm">
              Simpan Retur
            </button>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>
