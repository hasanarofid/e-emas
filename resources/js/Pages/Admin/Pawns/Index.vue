<script setup>
import { ref } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
  CreditCard, Plus, Search, Calendar, Scale, 
  Coins, CheckCircle2, Clock, AlertCircle, X, Trash2,
  FileText, ShieldCheck
} from 'lucide-vue-next';

const props = defineProps({
  pawns: Object,
  filters: Object,
  stats: Object,
});

const search = ref(props.filters.search || '');
const statusFilter = ref(props.filters.status || '');
const isRedeemModalOpen = ref(false);
const selectedPawn = ref(null);

const redeemForm = useForm({
  settled_amount: 0,
  notes: '',
});

const handleFilter = () => {
  router.get(route('admin.pawns.index'), {
    search: search.value,
    status: statusFilter.value,
  }, { preserveState: true, replace: true });
};

const openRedeemModal = (pawn) => {
  selectedPawn.value = pawn;
  // Pokok pinjaman + jasa titip 1 bulan sebagai saran
  const loan = parseFloat(pawn.loan_amount) || 0;
  const fee = parseFloat(pawn.monthly_fee) || 0;
  redeemForm.settled_amount = loan + fee;
  redeemForm.notes = 'Pelunasan pokok pinjaman dan jasa titip';
  isRedeemModalOpen.value = true;
};

const submitRedeem = () => {
  if (!selectedPawn.value) return;
  redeemForm.post(route('admin.pawns.redeem', selectedPawn.value.id), {
    onSuccess: () => {
      isRedeemModalOpen.value = false;
    },
  });
};

const deletePawn = (pawn) => {
  if (confirm(`Apakah Anda yakin ingin menghapus arsip gadai "${pawn.pawn_number}"? Data akan diarsipkan aman (Soft Delete).`)) {
    router.delete(route('admin.pawns.destroy', pawn.id));
  }
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
</script>

<template>
  <AdminLayout title="Gadai Perhiasan Emas">
    <Head title="Gadai Perhiasan Emas - POS Emas" />

    <div class="space-y-6">
      <!-- Header -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <h1 class="text-2xl font-bold text-white tracking-wide flex items-center gap-2">
            <CreditCard class="w-7 h-7 text-amber-400" />
            Gadai Perhiasan Emas (Pawn System)
          </h1>
          <p class="text-slate-400 text-sm mt-1">
            Layanan pinjaman dana tunai dengan agunan / jaminan perhiasan emas pelanggan
          </p>
        </div>
        <Link
          :href="route('admin.pawns.create')"
          class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 font-semibold rounded-xl shadow-lg shadow-amber-500/20 transition-all duration-200"
        >
          <Plus class="w-5 h-5" />
          <span>Terbitkan Gadai Baru</span>
        </Link>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-[#181834] border border-[#25254B] rounded-2xl p-4 flex items-center gap-4 shadow-xl">
          <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400">
            <Clock class="w-6 h-6" />
          </div>
          <div>
            <div class="text-xs text-slate-400 font-medium">Gadai Aktif Berjalan</div>
            <div class="text-2xl font-bold text-white mt-0.5">{{ stats.active_count }} <span class="text-xs text-slate-400 font-normal">titipan</span></div>
          </div>
        </div>

        <div class="bg-[#181834] border border-[#25254B] rounded-2xl p-4 flex items-center gap-4 shadow-xl">
          <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400">
            <Coins class="w-6 h-6" />
          </div>
          <div>
            <div class="text-xs text-slate-400 font-medium">Total Dana Pinjaman Cair</div>
            <div class="text-2xl font-bold text-white mt-0.5">{{ formatRupiah(stats.active_loan) }}</div>
          </div>
        </div>

        <div class="bg-[#181834] border border-[#25254B] rounded-2xl p-4 flex items-center gap-4 shadow-xl">
          <div class="w-12 h-12 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400">
            <CheckCircle2 class="w-6 h-6" />
          </div>
          <div>
            <div class="text-xs text-slate-400 font-medium">Gadai Ditebus Lunas</div>
            <div class="text-2xl font-bold text-white mt-0.5">{{ stats.redeemed_count }} <span class="text-xs text-slate-400 font-normal">transaksi</span></div>
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
              placeholder="Cari no gadai, nama, perhiasan..."
              class="w-full pl-10 pr-4 py-2 bg-[#0F0F23] border border-[#25254B] rounded-xl text-white text-sm focus:outline-none focus:border-amber-400/50"
            />
          </div>
          <select
            v-model="statusFilter"
            @change="handleFilter"
            class="px-3.5 py-2 bg-[#0F0F23] border border-[#25254B] rounded-xl text-white text-sm focus:outline-none focus:border-amber-400/50"
          >
            <option value="">Semua Status Gadai</option>
            <option value="active">Aktif (Sedang Berjalan)</option>
            <option value="redeemed">Ditebus Lunas (Selesai)</option>
            <option value="extended">Diperpanjang</option>
            <option value="auctioned">Dilelang (Jatuh Tempo)</option>
          </select>
        </div>
        <div class="text-xs text-slate-400">
          Total: <span class="text-amber-400 font-semibold">{{ pawns.total }} Transaksi</span>
        </div>
      </div>

      <!-- Data Table -->
      <div class="bg-[#181834] border border-[#25254B] rounded-2xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse text-sm">
            <thead>
              <tr class="border-b border-[#25254B] bg-[#1F1F41]/60 text-slate-300 font-medium">
                <th class="py-3.5 px-4">No. Gadai & Pelanggan</th>
                <th class="py-3.5 px-4">Jaminan Perhiasan</th>
                <th class="py-3.5 px-4 text-right">Pinjaman & Jasa Titip</th>
                <th class="py-3.5 px-4 text-center">Jatuh Tempo</th>
                <th class="py-3.5 px-4 text-center">Status</th>
                <th class="py-3.5 px-4 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-[#25254B]">
              <tr 
                v-for="item in pawns.data" 
                :key="item.id"
                class="hover:bg-[#1F1F41]/30 transition-colors text-slate-300"
              >
                <td class="py-3.5 px-4">
                  <div class="font-bold text-amber-400 font-mono">{{ item.pawn_number }}</div>
                  <div class="font-semibold text-white mt-0.5">{{ item.customer_name }}</div>
                  <div class="text-xs text-slate-400">{{ item.customer_phone || '-' }}</div>
                </td>
                <td class="py-3.5 px-4">
                  <div class="font-semibold text-white">{{ item.item_name }}</div>
                  <div class="text-xs text-slate-400 flex items-center gap-2 mt-0.5">
                    <span class="text-amber-400/90 font-mono font-semibold">{{ formatWeight(item.gross_weight) }}</span>
                    <span>•</span>
                    <span>{{ item.gold_rate?.name || '-' }}</span>
                  </div>
                  <div class="text-[11px] text-slate-500">
                    Taksiran: {{ formatRupiah(item.estimated_value) }}
                  </div>
                </td>
                <td class="py-3.5 px-4 text-right">
                  <div class="font-bold text-emerald-400">{{ formatRupiah(item.loan_amount) }}</div>
                  <div class="text-xs text-slate-400 mt-0.5">
                    Bunga: {{ item.monthly_interest_rate }}% ({{ formatRupiah(item.monthly_fee) }}/bln)
                  </div>
                </td>
                <td class="py-3.5 px-4 text-center">
                  <div class="font-medium text-slate-200">{{ formatDate(item.due_date) }}</div>
                  <div class="text-xs text-slate-500">Mulai: {{ formatDate(item.start_date) }}</div>
                </td>
                <td class="py-3.5 px-4 text-center">
                  <span 
                    v-if="item.status === 'active'"
                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/20"
                  >
                    <Clock class="w-3.5 h-3.5" />
                    Aktif
                  </span>
                  <span 
                    v-else-if="item.status === 'redeemed'"
                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20"
                  >
                    <CheckCircle2 class="w-3.5 h-3.5" />
                    Ditebus
                  </span>
                  <span 
                    v-else
                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-500/10 text-red-400 border border-red-500/20"
                  >
                    <AlertCircle class="w-3.5 h-3.5" />
                    {{ item.status }}
                  </span>
                </td>
                <td class="py-3.5 px-4 text-right">
                  <div class="inline-flex items-center gap-2">
                    <button
                      v-if="item.status === 'active'"
                      @click="openRedeemModal(item)"
                      class="px-2.5 py-1 bg-emerald-500/20 hover:bg-emerald-500 text-emerald-300 hover:text-slate-950 font-semibold rounded-lg text-xs transition-colors"
                      title="Tebus Gadai"
                    >
                      Tebus
                    </button>
                    <button
                      @click="deletePawn(item)"
                      class="p-1.5 text-slate-400 hover:text-red-400 hover:bg-[#25254B] rounded-lg transition-colors"
                      title="Hapus Arsip (Soft Delete)"
                    >
                      <Trash2 class="w-4 h-4" />
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="pawns.data.length === 0">
                <td colspan="6" class="py-12 text-center text-slate-500">
                  Belum ada transaksi gadai perhiasan. Klik "Terbitkan Gadai Baru" di atas.
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="pawns.links && pawns.links.length > 3" class="p-4 border-t border-[#25254B] flex items-center justify-between text-xs text-slate-400">
          <div>Menampilkan {{ pawns.from }} - {{ pawns.to }} dari {{ pawns.total }} transaksi</div>
          <div class="flex gap-1">
            <template v-for="(link, i) in pawns.links" :key="i">
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

    <!-- Modal Pelunasan Tebus Gadai -->
    <div v-if="isRedeemModalOpen && selectedPawn" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/75 backdrop-blur-sm">
      <div class="bg-[#181834] border border-[#25254B] rounded-2xl w-full max-w-md shadow-2xl p-6">
        <div class="flex items-center justify-between pb-4 border-b border-[#25254B]">
          <h3 class="text-lg font-bold text-white flex items-center gap-2">
            <CheckCircle2 class="w-5 h-5 text-emerald-400" />
            Pelunasan / Tebus Gadai
          </h3>
          <button @click="isRedeemModalOpen = false" class="text-slate-400 hover:text-white p-1 rounded-lg">
            <X class="w-5 h-5" />
          </button>
        </div>

        <form @submit.prevent="submitRedeem" class="mt-4 space-y-4">
          <div class="p-3 bg-[#0F0F23] rounded-xl border border-[#25254B] text-xs space-y-1.5">
            <div class="flex justify-between text-slate-300">
              <span>No. Surat Gadai:</span>
              <span class="font-mono font-bold text-amber-400">{{ selectedPawn.pawn_number }}</span>
            </div>
            <div class="flex justify-between text-slate-300">
              <span>Nama Nasabah:</span>
              <span class="font-semibold text-white">{{ selectedPawn.customer_name }}</span>
            </div>
            <div class="flex justify-between text-slate-300">
              <span>Barang Agunan:</span>
              <span>{{ selectedPawn.item_name }} ({{ formatWeight(selectedPawn.gross_weight) }})</span>
            </div>
            <div class="flex justify-between text-slate-300 pt-1 border-t border-[#25254B]">
              <span>Pokok Pinjaman:</span>
              <span class="font-semibold text-emerald-400">{{ formatRupiah(selectedPawn.loan_amount) }}</span>
            </div>
            <div class="flex justify-between text-slate-300">
              <span>Jasa Titip / Bunga (1 Bln):</span>
              <span class="font-semibold text-amber-400">{{ formatRupiah(selectedPawn.monthly_fee) }}</span>
            </div>
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-300 mb-1">Total Biaya Penebusan Diterima (Rp) *</label>
            <input
              v-model.number="redeemForm.settled_amount"
              type="number"
              min="0"
              required
              class="w-full px-3 py-2 bg-[#0F0F23] border border-emerald-500/40 rounded-xl text-emerald-400 text-base font-mono font-bold focus:border-amber-400"
            />
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-300 mb-1">Catatan Penebusan</label>
            <textarea
              v-model="redeemForm.notes"
              rows="2"
              class="w-full px-3 py-2 bg-[#0F0F23] border border-[#25254B] rounded-xl text-white text-xs focus:border-amber-400"
            ></textarea>
          </div>

          <div class="flex items-center justify-end gap-3 pt-3 border-t border-[#25254B]">
            <button
              type="button"
              @click="isRedeemModalOpen = false"
              class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs font-semibold"
            >
              Batal
            </button>
            <button
              type="submit"
              :disabled="redeemForm.processing"
              class="px-5 py-2 bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-slate-950 font-bold rounded-xl text-xs shadow-lg shadow-emerald-500/20 disabled:opacity-50"
            >
              {{ redeemForm.processing ? 'Memproses...' : 'Konfirmasi Pelunasan & Serahkan Emas' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>
