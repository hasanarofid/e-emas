<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
  Calendar, Repeat, Coins, Scale, Printer,
  ArrowDownRight, ArrowUpRight, ArrowLeft
} from 'lucide-vue-next';

const props = defineProps({
  selectedDate: String,
  tradeIns: Array,
  stats: Object,
});

const date = ref(props.selectedDate);

const handleDateChange = () => {
  router.get(route('admin.trade-ins.daily'), { date: date.value }, { preserveState: true });
};

const formatRupiah = (val) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val || 0);
};

const formatWeight = (val) => {
  return Number(val || 0).toFixed(3) + ' gr';
};

const formatDate = (dateStr) => {
  if (!dateStr) return '-';
  const d = new Date(dateStr);
  return d.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
};
</script>

<template>
  <AdminLayout>
    <Head title="Tukar Tambah Harian - POS Emas" />

    <div class="space-y-6">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2 text-xs font-mono text-amber-500 uppercase tracking-widest">
            <Calendar class="w-4 h-4" />
            <span>Rekapitulasi Harian</span>
          </div>
          <h2 class="text-2xl font-bold tracking-tight text-white mt-1">
            Tukar Tambah Harian
          </h2>
          <p class="text-sm text-slate-400">
            Rekap transaksi tukar tambah perhiasan untuk tanggal: <span class="text-white font-medium">{{ formatDate(selectedDate) }}</span>.
          </p>
        </div>

        <div class="flex items-center gap-3">
          <div class="flex items-center gap-2 bg-[#1C1C3E] border border-[#2D2D54] rounded-xl px-3 py-1.5">
            <span class="text-xs text-slate-400">Pilih Tanggal:</span>
            <input 
              v-model="date"
              @change="handleDateChange"
              type="date"
              class="bg-transparent text-sm text-white font-mono focus:outline-none cursor-pointer"
            />
          </div>
          <button 
            @click="window.print()"
            class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-[#1C1C3E] border border-[#2D2D54] text-slate-300 hover:text-white hover:bg-[#25254B] transition-colors text-sm cursor-pointer"
          >
            <Printer class="w-4 h-4" />
            <span>Cetak Rekap</span>
          </button>
        </div>
      </div>

      <!-- KPI Stats -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-[#1C1C3E] border border-[#2D2D54] rounded-2xl p-4 flex items-center justify-between">
          <div>
            <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Transaksi Hari Ini</p>
            <h3 class="text-2xl font-bold text-white mt-1">{{ stats.count }}</h3>
            <span class="text-xs text-slate-400 mt-1">Nota selesai</span>
          </div>
          <div class="w-12 h-12 rounded-xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center">
            <Repeat class="w-6 h-6" />
          </div>
        </div>

        <div class="bg-[#1C1C3E] border border-[#2D2D54] rounded-2xl p-4 flex items-center justify-between">
          <div>
            <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Emas Lama Masuk</p>
            <h3 class="text-2xl font-bold text-emerald-400 mt-1">{{ formatWeight(stats.old_weight_in) }}</h3>
            <span class="text-xs text-slate-400 mt-1">Diterima timbangan toko</span>
          </div>
          <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center">
            <ArrowDownRight class="w-6 h-6" />
          </div>
        </div>

        <div class="bg-[#1C1C3E] border border-[#2D2D54] rounded-2xl p-4 flex items-center justify-between">
          <div>
            <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Emas Baru Keluar</p>
            <h3 class="text-2xl font-bold text-amber-400 mt-1">{{ formatWeight(stats.new_weight_out) }}</h3>
            <span class="text-xs text-slate-400 mt-1">Keluar dari etalase</span>
          </div>
          <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center">
            <ArrowUpRight class="w-6 h-6" />
          </div>
        </div>

        <div class="bg-[#1C1C3E] border border-[#2D2D54] rounded-2xl p-4 flex items-center justify-between">
          <div>
            <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Kas Masuk Selisih</p>
            <h3 class="text-xl font-bold text-cyan-400 mt-1">{{ formatRupiah(stats.total_difference_paid) }}</h3>
            <span class="text-xs text-slate-400 mt-1">Total selisih kasir</span>
          </div>
          <div class="w-12 h-12 rounded-xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 flex items-center justify-center">
            <Coins class="w-6 h-6" />
          </div>
        </div>
      </div>

      <!-- Tabel Transaksi Hari Ini -->
      <div class="bg-[#1C1C3E] border border-[#2D2D54] rounded-2xl overflow-hidden shadow-xl">
        <div class="p-4 border-b border-[#2D2D54] bg-[#15152D] flex items-center justify-between">
          <h3 class="font-bold text-white text-sm flex items-center gap-2">
            <Repeat class="w-4 h-4 text-amber-400" />
            <span>Rincian Nota Tukar Tambah Hari Ini</span>
          </h3>
          <span class="text-xs text-slate-400 font-mono">{{ tradeIns.length }} transaksi</span>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left text-sm text-slate-300">
            <thead class="bg-[#15152D]/50 text-xs uppercase font-mono tracking-wider text-slate-400 border-b border-[#2D2D54]">
              <tr>
                <th class="px-5 py-3.5">No Nota</th>
                <th class="px-5 py-3.5">Pelanggan</th>
                <th class="px-5 py-3.5">Emas Lama (Masuk)</th>
                <th class="px-5 py-3.5">Emas Baru (Keluar)</th>
                <th class="px-5 py-3.5 text-right">Selisih Bayar</th>
                <th class="px-5 py-3.5 text-center">Metode</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-[#2D2D54]/50">
              <tr v-for="t in tradeIns" :key="t.id" class="hover:bg-[#24244D]/50 transition-colors">
                <td class="px-5 py-4 font-bold text-white font-mono">{{ t.trade_in_number }}</td>
                <td class="px-5 py-4">
                  <div class="text-slate-200 font-medium">{{ t.customer_name }}</div>
                  <div class="text-xs text-slate-400">{{ t.customer_phone || '-' }}</div>
                </td>
                <td class="px-5 py-4">
                  <div class="text-slate-200">{{ t.old_item_name }}</div>
                  <div class="text-xs text-emerald-400 font-mono">
                    {{ formatWeight(t.old_weight) }} • Nilai Taksir: {{ formatRupiah(t.old_estimated_price) }}
                  </div>
                </td>
                <td class="px-5 py-4">
                  <div class="text-slate-200">{{ t.new_item_name }}</div>
                  <div class="text-xs text-amber-400 font-mono">
                    {{ formatWeight(t.new_weight) }} • Harga: {{ formatRupiah(t.new_price) }}
                  </div>
                </td>
                <td class="px-5 py-4 text-right font-bold text-white font-mono">
                  {{ formatRupiah(t.difference_amount) }}
                </td>
                <td class="px-5 py-4 text-center">
                  <span class="px-2.5 py-1 rounded-full text-xs font-semibold uppercase bg-[#25254B] text-slate-300 border border-[#2D2D54]">
                    {{ t.payment_method }}
                  </span>
                </td>
              </tr>
              <tr v-if="tradeIns.length === 0">
                <td colspan="6" class="px-5 py-10 text-center text-slate-400">
                  <Repeat class="w-10 h-10 mx-auto text-slate-600 mb-2" />
                  <p>Tidak ada transaksi tukar tambah pada tanggal {{ formatDate(selectedDate) }}.</p>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
