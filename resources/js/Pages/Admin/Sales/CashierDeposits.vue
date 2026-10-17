<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { 
  Receipt, 
  Coins, 
  CheckCircle2, 
  Clock, 
  Plus, 
  X, 
  Check, 
  UserCheck, 
  Building2,
  DollarSign
} from 'lucide-vue-next';

const props = defineProps({
  deposits: {
    type: Array,
    required: true,
  },
  stats: {
    type: Object,
    required: true,
  },
});

const isModalOpen = ref(false);

const form = useForm({
  shift_name: 'Shift 1 (Pagi - Sore)',
  initial_cash: 2000000,
  cash_sales: 15000000,
  actual_cash: 17000000,
  notes: '',
});

const openModal = () => {
  form.reset();
  isModalOpen.value = true;
};

const closeModal = () => {
  isModalOpen.value = false;
  form.reset();
};

const submitDeposit = () => {
  alert('Berita acara tutup shift dan setoran kasir berhasil dicatat!');
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
    <Head title="Setoran Kasir & Tutup Shift - POS Emas" />

    <div class="space-y-6">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2 text-xs font-mono text-amber-500 uppercase tracking-widest">
            <Receipt class="w-4 h-4" />
            <span>Rekonsiliasi Kas Laci Kasir</span>
          </div>
          <h2 class="text-2xl font-bold tracking-tight text-white mt-1">
            Setoran Kasir & Tutup Shift POS
          </h2>
          <p class="text-sm text-slate-400">
            Rekonsiliasi uang fisik di laci kasir vs penjualan tunai sistem, serah terima shift kasir, dan setoran ke brankas toko.
          </p>
        </div>

        <button
          @click="openModal"
          class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 font-bold text-sm shadow-lg shadow-amber-500/20 transition-all cursor-pointer"
        >
          <Plus class="w-4 h-4" />
          <span>Form Tutup Shift & Setor</span>
        </button>
      </div>

      <!-- Quick Summary Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-4 rounded-2xl bg-[#1E1E38] border border-slate-800 flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 shrink-0">
            <Coins class="w-6 h-6" />
          </div>
          <div>
            <p class="text-xs font-medium text-slate-400">Setoran Fisik Hari Ini</p>
            <p class="text-lg font-bold text-emerald-400 mt-0.5">{{ formatRupiah(stats.total_deposited_today) }}</p>
          </div>
        </div>

        <div class="p-4 rounded-2xl bg-[#1E1E38] border border-slate-800 flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400 shrink-0">
            <Building2 class="w-6 h-6" />
          </div>
          <div>
            <p class="text-xs font-medium text-slate-400">Akumulasi Masuk Brankas</p>
            <p class="text-lg font-bold text-white mt-0.5">{{ formatRupiah(stats.total_deposited_month) }}</p>
          </div>
        </div>

        <div class="p-4 rounded-2xl bg-[#1E1E38] border border-slate-800 flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400 shrink-0">
            <CheckCircle2 class="w-6 h-6" />
          </div>
          <div>
            <p class="text-xs font-medium text-slate-400">Selisih Kas Fisik</p>
            <p class="text-xl font-bold text-emerald-400 mt-0.5">Rp 0 (Cocok)</p>
          </div>
        </div>

        <div class="p-4 rounded-2xl bg-[#1E1E38] border border-slate-800 flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-purple-500/10 border border-purple-500/20 flex items-center justify-center text-purple-400 shrink-0">
            <UserCheck class="w-6 h-6" />
          </div>
          <div>
            <p class="text-xs font-medium text-slate-400">Status Verifikasi</p>
            <p class="text-base font-bold text-white mt-0.5">100% Selesai</p>
          </div>
        </div>
      </div>

      <!-- Table Section -->
      <div class="rounded-2xl bg-[#1E1E38] border border-slate-800 overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-sm text-slate-300">
            <thead class="bg-[#151528] text-xs uppercase text-slate-400 font-semibold border-b border-slate-800">
              <tr>
                <th class="px-6 py-4">Kode Setoran / Tanggal</th>
                <th class="px-6 py-4">Shift & Kasir</th>
                <th class="px-6 py-4 text-right">Modal Awal Laci</th>
                <th class="px-6 py-4 text-right">Penjualan Tunai</th>
                <th class="px-6 py-4 text-right">Fisik Disetor</th>
                <th class="px-6 py-4 text-center">Selisih Kas</th>
                <th class="px-6 py-4 text-center">Verifikasi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/80">
              <tr
                v-for="dep in deposits"
                :key="dep.id"
                class="hover:bg-[#25254B]/40 transition-colors"
              >
                <td class="px-6 py-4">
                  <div class="font-mono font-bold text-amber-400 text-xs">{{ dep.deposit_code }}</div>
                  <div class="text-[11px] text-slate-400 font-mono">{{ dep.date }}</div>
                </td>
                <td class="px-6 py-4">
                  <div class="font-semibold text-white text-xs">{{ dep.cashier_name }}</div>
                  <div class="text-[11px] text-amber-300 font-medium">{{ dep.shift_name }}</div>
                </td>
                <td class="px-6 py-4 text-right font-mono text-xs text-slate-400">
                  {{ formatRupiah(dep.initial_cash) }}
                </td>
                <td class="px-6 py-4 text-right font-mono text-xs text-slate-300 font-medium">
                  {{ formatRupiah(dep.cash_sales) }}
                </td>
                <td class="px-6 py-4 text-right font-mono font-bold text-emerald-400 text-xs">
                  {{ formatRupiah(dep.actual_cash) }}
                </td>
                <td class="px-6 py-4 text-center">
                  <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 font-mono">
                    Rp 0 (Pas)
                  </span>
                </td>
                <td class="px-6 py-4 text-center">
                  <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-blue-500/10 text-blue-400 border border-blue-500/20">
                    <Check class="w-3.5 h-3.5" />
                    {{ dep.verified_by }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Modal Form Tutup Shift -->
    <div 
      v-if="isModalOpen" 
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm"
    >
      <div class="w-full max-w-lg rounded-2xl bg-[#1E1E38] border border-slate-700 shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
        <div class="p-5 border-b border-slate-800 flex items-center justify-between">
          <div class="flex items-center gap-2.5">
            <div class="w-9 h-9 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400">
              <Receipt class="w-5 h-5" />
            </div>
            <div>
              <h3 class="text-base font-bold text-white">Form Tutup Shift & Setor Kasir</h3>
              <p class="text-xs text-slate-400">Input hitungan uang fisik di laci kasir.</p>
            </div>
          </div>
          <button @click="closeModal" class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-[#25254B]">
            <X class="w-5 h-5" />
          </button>
        </div>

        <form @submit.prevent="submitDeposit" class="p-5 space-y-4">
          <div>
            <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Pilihan Shift</label>
            <select v-model="form.shift_name" class="w-full px-3.5 py-2.5 rounded-xl bg-[#151528] border border-slate-700 text-white text-sm">
              <option value="Shift 1 (Pagi - Sore)">Shift 1 (Pagi - Sore)</option>
              <option value="Shift 2 (Sore - Malam)">Shift 2 (Sore - Malam)</option>
              <option value="Full Day">Full Day</option>
            </select>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Modal Awal Laci</label>
              <input v-model.number="form.initial_cash" type="number" class="w-full px-3.5 py-2 rounded-xl bg-[#151528] border border-slate-700 text-white text-sm font-mono" />
            </div>
            <div>
              <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Uang Fisik Dihitung</label>
              <input v-model.number="form.actual_cash" type="number" class="w-full px-3.5 py-2 rounded-xl bg-[#151528] border border-slate-700 text-white text-sm font-mono text-emerald-400 font-bold" />
            </div>
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Catatan Tambahan</label>
            <textarea v-model="form.notes" rows="2" placeholder="Catatan pecahan uang atau serah terima shift..." class="w-full px-3.5 py-2.5 rounded-xl bg-[#151528] border border-slate-700 text-white text-sm"></textarea>
          </div>

          <div class="pt-4 border-t border-slate-800 flex items-center justify-end gap-3">
            <button type="button" @click="closeModal" class="px-4 py-2.5 rounded-xl bg-[#25254B] text-slate-300 text-sm font-medium">Batal</button>
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-sm">Simpan Setoran</button>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>
