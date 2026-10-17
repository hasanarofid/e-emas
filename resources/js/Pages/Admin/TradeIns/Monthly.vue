<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
  CalendarDays, Repeat, Coins, Scale, Printer,
  ArrowDownRight, ArrowUpRight, TrendingUp
} from 'lucide-vue-next';

const props = defineProps({
  year: Number,
  month: Number,
  calendarDays: Array,
  stats: Object,
});

const selectedMonth = ref(props.month);
const selectedYear = ref(props.year);

const months = [
  { value: 1, label: 'Januari' },
  { value: 2, label: 'Februari' },
  { value: 3, label: 'Maret' },
  { value: 4, label: 'April' },
  { value: 5, label: 'Mei' },
  { value: 6, label: 'Juni' },
  { value: 7, label: 'Juli' },
  { value: 8, label: 'Agustus' },
  { value: 9, label: 'September' },
  { value: 10, label: 'Oktober' },
  { value: 11, label: 'November' },
  { value: 12, label: 'Desember' },
];

const years = [2024, 2025, 2026, 2027];

const handleFilter = () => {
  router.get(route('admin.trade-ins.monthly'), {
    year: selectedYear.value,
    month: selectedMonth.value,
  }, { preserveState: true });
};

const formatRupiah = (val) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val || 0);
};

const formatWeight = (val) => {
  return Number(val || 0).toFixed(3) + ' gr';
};
</script>

<template>
  <AdminLayout>
    <Head title="Tukar Tambah Bulanan - POS Emas" />

    <div class="space-y-6">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2 text-xs font-mono text-amber-500 uppercase tracking-widest">
            <CalendarDays class="w-4 h-4" />
            <span>Matriks Kalender Bulanan</span>
          </div>
          <h2 class="text-2xl font-bold tracking-tight text-white mt-1">
            Tukar Tambah Bulanan
          </h2>
          <p class="text-sm text-slate-400">
            Rekapitulasi omset selisih bayar dan perputaran gramasi emas tukar tambah per periode bulan.
          </p>
        </div>

        <div class="flex items-center gap-3">
          <div class="flex items-center gap-2 bg-[#1C1C3E] border border-[#2D2D54] rounded-xl px-3 py-1.5">
            <select
              v-model="selectedMonth"
              @change="handleFilter"
              class="bg-transparent text-sm text-white focus:outline-none cursor-pointer"
            >
              <option v-for="m in months" :key="m.value" :value="m.value" class="bg-[#1C1C3E] text-white">{{ m.label }}</option>
            </select>
            <select
              v-model="selectedYear"
              @change="handleFilter"
              class="bg-transparent text-sm text-white focus:outline-none cursor-pointer"
            >
              <option v-for="y in years" :key="y" :value="y" class="bg-[#1C1C3E] text-white">{{ y }}</option>
            </select>
          </div>
          <button 
            @click="window.print()"
            class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-[#1C1C3E] border border-[#2D2D54] text-slate-300 hover:text-white hover:bg-[#25254B] transition-colors text-sm cursor-pointer"
          >
            <Printer class="w-4 h-4" />
            <span>Cetak Bulanan</span>
          </button>
        </div>
      </div>

      <!-- KPI Stats -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-[#1C1C3E] border border-[#2D2D54] rounded-2xl p-4">
          <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Total Transaksi</p>
          <h3 class="text-2xl font-bold text-white mt-1">{{ stats.total_count }}</h3>
          <span class="text-xs text-slate-400 mt-1">Bulan {{ months.find(m => m.value == month)?.label }}</span>
        </div>

        <div class="bg-[#1C1C3E] border border-[#2D2D54] rounded-2xl p-4">
          <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Emas Lama Masuk</p>
          <h3 class="text-2xl font-bold text-emerald-400 mt-1">{{ formatWeight(stats.total_old_weight) }}</h3>
          <span class="text-xs text-slate-400 mt-1">Gramasi lebur/scrap</span>
        </div>

        <div class="bg-[#1C1C3E] border border-[#2D2D54] rounded-2xl p-4">
          <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Emas Baru Keluar</p>
          <h3 class="text-2xl font-bold text-amber-400 mt-1">{{ formatWeight(stats.total_new_weight) }}</h3>
          <span class="text-xs text-slate-400 mt-1">Gramasi terjual</span>
        </div>

        <div class="bg-[#1C1C3E] border border-[#2D2D54] rounded-2xl p-4">
          <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Arus Bersih Gram</p>
          <h3 :class="[stats.gold_surplus_deficit >= 0 ? 'text-amber-300' : 'text-emerald-300', 'text-2xl font-bold mt-1']">
            {{ formatWeight(stats.gold_surplus_deficit) }}
          </h3>
          <span class="text-xs text-slate-400 mt-1">Net outflow emas</span>
        </div>

        <div class="bg-[#1C1C3E] border border-[#2D2D54] rounded-2xl p-4">
          <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Total Kas Masuk</p>
          <h3 class="text-xl font-bold text-cyan-400 mt-1">{{ formatRupiah(stats.total_difference) }}</h3>
          <span class="text-xs text-slate-400 mt-1">Pendapatan selisih</span>
        </div>
      </div>

      <!-- Tabel Hari per Hari dalam Bulan -->
      <div class="bg-[#1C1C3E] border border-[#2D2D54] rounded-2xl overflow-hidden shadow-xl">
        <div class="p-4 border-b border-[#2D2D54] bg-[#15152D] flex items-center justify-between">
          <h3 class="font-bold text-white text-sm flex items-center gap-2">
            <CalendarDays class="w-4 h-4 text-amber-400" />
            <span>Rincian Harian Bulan {{ months.find(m => m.value == month)?.label }} {{ year }}</span>
          </h3>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left text-sm text-slate-300">
            <thead class="bg-[#15152D]/50 text-xs uppercase font-mono tracking-wider text-slate-400 border-b border-[#2D2D54]">
              <tr>
                <th class="px-5 py-3.5">Tanggal</th>
                <th class="px-5 py-3.5 text-center">Jumlah Transaksi</th>
                <th class="px-5 py-3.5 text-right">Emas Masuk (gr)</th>
                <th class="px-5 py-3.5 text-right">Emas Keluar (gr)</th>
                <th class="px-5 py-3.5 text-right">Kas Selisih (Rp)</th>
                <th class="px-5 py-3.5 text-center">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-[#2D2D54]/50">
              <tr 
                v-for="d in calendarDays" 
                :key="d.date" 
                :class="[d.count > 0 ? 'bg-[#24244D]/30 hover:bg-[#24244D]/70 font-medium' : 'text-slate-500 hover:bg-[#1E1E3F]', 'transition-colors']"
              >
                <td class="px-5 py-3.5 font-mono">
                  <span :class="d.count > 0 ? 'text-amber-400 font-bold' : 'text-slate-400'">{{ d.date }}</span>
                  <span class="text-xs text-slate-500 ml-2">(Hari ke-{{ d.day }})</span>
                </td>
                <td class="px-5 py-3.5 text-center">
                  <span v-if="d.count > 0" class="px-2 py-0.5 rounded-full bg-indigo-500/20 text-indigo-300 font-bold text-xs">
                    {{ d.count }} TT
                  </span>
                  <span v-else>-</span>
                </td>
                <td class="px-5 py-3.5 text-right font-mono" :class="d.old_weight > 0 ? 'text-emerald-400 font-semibold' : ''">
                  {{ d.old_weight > 0 ? formatWeight(d.old_weight) : '-' }}
                </td>
                <td class="px-5 py-3.5 text-right font-mono" :class="d.new_weight > 0 ? 'text-amber-400 font-semibold' : ''">
                  {{ d.new_weight > 0 ? formatWeight(d.new_weight) : '-' }}
                </td>
                <td class="px-5 py-3.5 text-right font-mono" :class="d.difference > 0 ? 'text-cyan-400 font-bold' : ''">
                  {{ d.difference > 0 ? formatRupiah(d.difference) : '-' }}
                </td>
                <td class="px-5 py-3.5 text-center">
                  <Link
                    v-if="d.count > 0"
                    :href="route('admin.trade-ins.daily', { date: d.date })"
                    class="text-xs text-amber-400 hover:text-amber-300 underline font-medium"
                  >
                    Buka Harian
                  </Link>
                  <span v-else class="text-xs text-slate-600">-</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
