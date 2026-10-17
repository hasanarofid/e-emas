<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
  FileSpreadsheet, Plus, Search, Eye, Trash2, 
  Coins, Scale, CheckCircle2, AlertCircle, Clock, 
  Building2, Calendar, FileText, ChevronRight, X
} from 'lucide-vue-next';

const props = defineProps({
  purchases: Object,
  filters: Object,
  stats: Object,
});

const search = ref(props.filters.search || '');
const paymentStatus = ref(props.filters.payment_status || '');
const selectedPurchase = ref(null);
const isDetailModalOpen = ref(false);

const handleFilter = () => {
  router.get(route('admin.purchases.index'), {
    search: search.value,
    payment_status: paymentStatus.value,
  }, { preserveState: true, replace: true });
};

const formatRupiah = (val) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val || 0);
};

const formatWeight = (val) => {
  return Number(val || 0).toFixed(3) + ' gr';
};

const formatDate = (dateString) => {
  if (!dateString) return '-';
  const d = new Date(dateString);
  return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
};

const openDetail = (purchase) => {
  selectedPurchase.value = purchase;
  isDetailModalOpen.value = true;
};

const deletePurchase = (purchase) => {
  if (confirm(`Apakah Anda yakin ingin menghapus faktur pembelian "${purchase.invoice_number}"? Data akan diarsipkan dengan aman (Soft Delete) dan stok barang akan disesuaikan.`)) {
    router.delete(route('admin.purchases.destroy', purchase.id));
  }
};
</script>

<template>
  <AdminLayout title="Faktur Pembelian Emas">
    <Head title="Faktur Pembelian Emas - POS Emas" />

    <div class="space-y-6">
      <!-- Header -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <h1 class="text-2xl font-bold text-white tracking-wide flex items-center gap-2">
            <FileSpreadsheet class="w-7 h-7 text-amber-400" />
            Faktur Pembelian Supplier
          </h1>
          <p class="text-slate-400 text-sm mt-1">
            Riwayat penerimaan stok perhiasan emas dan faktur pengadaan dari mitra pabrik
          </p>
        </div>
        <Link
          :href="route('admin.purchases.create')"
          class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 font-semibold rounded-xl shadow-lg shadow-amber-500/20 transition-all duration-200"
        >
          <Plus class="w-5 h-5" />
          <span>Input Faktur Pembelian Baru</span>
        </Link>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-[#181834] border border-[#25254B] rounded-2xl p-4 flex items-center gap-4 shadow-xl">
          <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400">
            <FileText class="w-6 h-6" />
          </div>
          <div>
            <div class="text-xs text-slate-400 font-medium">Total Faktur Pembelian</div>
            <div class="text-2xl font-bold text-white mt-0.5">{{ stats.total_purchases }} <span class="text-xs text-slate-400 font-normal">transaksi</span></div>
          </div>
        </div>

        <div class="bg-[#181834] border border-[#25254B] rounded-2xl p-4 flex items-center gap-4 shadow-xl">
          <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400">
            <Coins class="w-6 h-6" />
          </div>
          <div>
            <div class="text-xs text-slate-400 font-medium">Total Nilai Pembelian</div>
            <div class="text-2xl font-bold text-white mt-0.5">{{ formatRupiah(stats.total_expenditure) }}</div>
          </div>
        </div>

        <div class="bg-[#181834] border border-[#25254B] rounded-2xl p-4 flex items-center gap-4 shadow-xl">
          <div class="w-12 h-12 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400">
            <Scale class="w-6 h-6" />
          </div>
          <div>
            <div class="text-xs text-slate-400 font-medium">Total Gramasi Emas Masuk</div>
            <div class="text-2xl font-bold text-white mt-0.5">{{ formatWeight(stats.total_weight_purchased) }}</div>
          </div>
        </div>
      </div>

      <!-- Filter Bar -->
      <div class="bg-[#181834] border border-[#25254B] rounded-2xl p-4 flex flex-col md:flex-row gap-4 items-center justify-between shadow-xl">
        <div class="flex flex-col md:flex-row gap-3 w-full md:w-auto flex-1">
          <div class="relative w-full md:w-80">
            <Search class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
            <input
              v-model="search"
              @keyup.enter="handleFilter"
              type="text"
              placeholder="Cari no faktur, supplier..."
              class="w-full pl-10 pr-4 py-2 bg-[#0F0F23] border border-[#25254B] rounded-xl text-white text-sm focus:outline-none focus:border-amber-400/50"
            />
          </div>
          <select
            v-model="paymentStatus"
            @change="handleFilter"
            class="px-3.5 py-2 bg-[#0F0F23] border border-[#25254B] rounded-xl text-white text-sm focus:outline-none focus:border-amber-400/50"
          >
            <option value="">Semua Status Pelunasan</option>
            <option value="paid">Lunas (Paid)</option>
            <option value="partial">Sebagian (Partial)</option>
            <option value="unpaid">Belum Dibayar / Tempo (Unpaid)</option>
          </select>
        </div>
        <div class="text-xs text-slate-400">
          Menampilkan <span class="text-amber-400 font-semibold">{{ purchases.total }} Faktur</span>
        </div>
      </div>

      <!-- Table Faktur -->
      <div class="bg-[#181834] border border-[#25254B] rounded-2xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse text-sm">
            <thead>
              <tr class="border-b border-[#25254B] bg-[#1F1F41]/60 text-slate-300 font-medium">
                <th class="py-3.5 px-4">No. Faktur & Tanggal</th>
                <th class="py-3.5 px-4">Supplier & Pabrik</th>
                <th class="py-3.5 px-4 text-center">Gramasi & Item</th>
                <th class="py-3.5 px-4 text-right">Grand Total</th>
                <th class="py-3.5 px-4 text-center">Pelunasan</th>
                <th class="py-3.5 px-4 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-[#25254B]">
              <tr 
                v-for="item in purchases.data" 
                :key="item.id"
                class="hover:bg-[#1F1F41]/30 transition-colors text-slate-300"
              >
                <td class="py-3.5 px-4">
                  <div class="font-bold text-amber-400 font-mono">{{ item.invoice_number }}</div>
                  <div class="text-xs text-slate-400 mt-0.5 flex items-center gap-1.5">
                    <Calendar class="w-3.5 h-3.5 text-slate-500" />
                    <span>{{ formatDate(item.invoice_date) }}</span>
                  </div>
                  <div v-if="item.supplier_invoice_number" class="text-[11px] text-slate-500 font-mono">
                    Ref: {{ item.supplier_invoice_number }}
                  </div>
                </td>
                <td class="py-3.5 px-4">
                  <div class="font-semibold text-white">{{ item.supplier?.name || '-' }}</div>
                  <div v-if="item.warehouse" class="text-xs text-slate-400 mt-0.5 flex items-center gap-1">
                    <Building2 class="w-3 h-3 text-slate-500" />
                    <span>Masuk ke: {{ item.warehouse.name }}</span>
                  </div>
                </td>
                <td class="py-3.5 px-4 text-center">
                  <div class="font-semibold text-white font-mono">{{ formatWeight(item.total_weight) }}</div>
                  <div class="text-xs text-slate-400">{{ item.total_items }} varian perhiasan</div>
                </td>
                <td class="py-3.5 px-4 text-right">
                  <div class="font-bold text-emerald-400">{{ formatRupiah(item.grand_total) }}</div>
                  <div v-if="item.remaining_balance > 0" class="text-xs text-red-400">
                    Sisa: {{ formatRupiah(item.remaining_balance) }}
                  </div>
                </td>
                <td class="py-3.5 px-4 text-center">
                  <span 
                    v-if="item.payment_status === 'paid'"
                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20"
                  >
                    <CheckCircle2 class="w-3.5 h-3.5" />
                    Lunas
                  </span>
                  <span 
                    v-else-if="item.payment_status === 'partial'"
                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/20"
                  >
                    <Clock class="w-3.5 h-3.5" />
                    Sebagian
                  </span>
                  <span 
                    v-else
                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-500/10 text-red-400 border border-red-500/20"
                  >
                    <AlertCircle class="w-3.5 h-3.5" />
                    Tempo
                  </span>
                </td>
                <td class="py-3.5 px-4 text-right">
                  <div class="inline-flex items-center gap-2">
                    <button
                      @click="openDetail(item)"
                      class="p-1.5 text-slate-400 hover:text-amber-400 hover:bg-[#25254B] rounded-lg transition-colors"
                      title="Lihat Detail Item"
                    >
                      <Eye class="w-4 h-4" />
                    </button>
                    <button
                      @click="deletePurchase(item)"
                      class="p-1.5 text-slate-400 hover:text-red-400 hover:bg-[#25254B] rounded-lg transition-colors"
                      title="Hapus Faktur (Soft Delete)"
                    >
                      <Trash2 class="w-4 h-4" />
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="purchases.data.length === 0">
                <td colspan="6" class="py-12 text-center text-slate-500">
                  Belum ada faktur pembelian supplier. Klik "Input Faktur Pembelian Baru" untuk memulai.
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="purchases.links && purchases.links.length > 3" class="p-4 border-t border-[#25254B] flex items-center justify-between text-xs text-slate-400">
          <div>Menampilkan {{ purchases.from }} - {{ purchases.to }} dari {{ purchases.total }} faktur</div>
          <div class="flex gap-1">
            <template v-for="(link, i) in purchases.links" :key="i">
              <button
                v-if="link.url"
                @click="router.get(link.url)"
                v-html="link.label"
                :class="link.active ? 'bg-amber-500 text-slate-950 font-bold' : 'bg-[#1F1F41] text-slate-300 hover:bg-[#25254B]'"
                class="px-3 py-1.5 rounded-lg border border-[#25254B]"
              />
              <span v-else v-html="link.label" class="px-3 py-1.5 rounded-lg text-slate-600 cursor-not-allowed border border-[#25254B]/40" />
            </template>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Detail Item Faktur -->
    <div v-if="isDetailModalOpen && selectedPurchase" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/75 backdrop-blur-sm">
      <div class="bg-[#181834] border border-[#25254B] rounded-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto shadow-2xl p-6">
        <div class="flex items-center justify-between pb-4 border-b border-[#25254B]">
          <div>
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
              <FileSpreadsheet class="w-5 h-5 text-amber-400" />
              Detail Faktur {{ selectedPurchase.invoice_number }}
            </h3>
            <p class="text-xs text-slate-400 mt-1">
              Supplier: <span class="text-amber-400 font-semibold">{{ selectedPurchase.supplier?.name }}</span> | 
              Tanggal: {{ formatDate(selectedPurchase.invoice_date) }}
            </p>
          </div>
          <button @click="isDetailModalOpen = false" class="text-slate-400 hover:text-white p-1 rounded-lg">
            <X class="w-5 h-5" />
          </button>
        </div>

        <div class="mt-4">
          <div class="overflow-x-auto rounded-xl border border-[#25254B]">
            <table class="w-full text-left text-xs border-collapse">
              <thead>
                <tr class="bg-[#1F1F41] text-slate-300 border-b border-[#25254B]">
                  <th class="py-2.5 px-3">Nama Perhiasan</th>
                  <th class="py-2.5 px-3 text-center">Berat (gr)</th>
                  <th class="py-2.5 px-3 text-right">Harga / gr</th>
                  <th class="py-2.5 px-3 text-right">Ongkos Bikin</th>
                  <th class="py-2.5 px-3 text-right">Total Sub</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-[#25254B] text-slate-300">
                <tr v-for="it in selectedPurchase.items" :key="it.id" class="hover:bg-[#1F1F41]/30">
                  <td class="py-2.5 px-3">
                    <div class="font-semibold text-white">{{ it.item_name }}</div>
                  </td>
                  <td class="py-2.5 px-3 text-center font-mono">{{ formatWeight(it.weight) }}</td>
                  <td class="py-2.5 px-3 text-right">{{ formatRupiah(it.price_per_gram) }}</td>
                  <td class="py-2.5 px-3 text-right">{{ formatRupiah(it.crafting_fee) }}</td>
                  <td class="py-2.5 px-3 text-right font-semibold text-amber-400">{{ formatRupiah(it.subtotal) }}</td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="mt-4 p-4 bg-[#0F0F23] border border-[#25254B] rounded-xl space-y-2 text-xs">
            <div class="flex justify-between text-slate-300">
              <span>Subtotal Perhiasan:</span>
              <span class="font-semibold">{{ formatRupiah(selectedPurchase.subtotal) }}</span>
            </div>
            <div v-if="selectedPurchase.discount > 0" class="flex justify-between text-emerald-400">
              <span>Diskon Supplier:</span>
              <span>-{{ formatRupiah(selectedPurchase.discount) }}</span>
            </div>
            <div v-if="selectedPurchase.tax > 0" class="flex justify-between text-slate-400">
              <span>Pajak PPN:</span>
              <span>+{{ formatRupiah(selectedPurchase.tax) }}</span>
            </div>
            <div class="flex justify-between text-sm font-bold text-white pt-2 border-t border-[#25254B]">
              <span>Grand Total:</span>
              <span class="text-amber-400">{{ formatRupiah(selectedPurchase.grand_total) }}</span>
            </div>
            <div class="flex justify-between text-slate-300">
              <span>Jumlah Terbayar:</span>
              <span class="text-emerald-400 font-semibold">{{ formatRupiah(selectedPurchase.amount_paid) }}</span>
            </div>
            <div v-if="selectedPurchase.remaining_balance > 0" class="flex justify-between text-red-400 font-semibold">
              <span>Sisa Hutang Tempo:</span>
              <span>{{ formatRupiah(selectedPurchase.remaining_balance) }}</span>
            </div>
          </div>
        </div>

        <div class="flex justify-end mt-4 pt-3 border-t border-[#25254B]">
          <button
            @click="isDetailModalOpen = false"
            class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs font-semibold"
          >
            Tutup
          </button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
