<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
  CalendarDays, 
  Coins, 
  CheckCircle2, 
  AlertTriangle, 
  ArrowUpRight, 
  ArrowDownLeft, 
  ShieldCheck,
  Phone,
  Clock
} from 'lucide-vue-next';

const props = defineProps({
  year: Number,
  month: Number,
  calendarDays: Array,
  dueThisMonth: Array,
  stats: Object,
});

const selectedYear = ref(props.year);
const selectedMonth = ref(props.month);

const months = [
  { val: 1, name: 'Januari' },
  { val: 2, name: 'Februari' },
  { val: 3, name: 'Maret' },
  { val: 4, name: 'April' },
  { val: 5, name: 'Mei' },
  { val: 6, name: 'Juni' },
  { val: 7, name: 'Juli' },
  { val: 8, name: 'Agustus' },
  { val: 9, name: 'September' },
  { val: 10, name: 'Oktober' },
  { val: 11, name: 'November' },
  { val: 12, name: 'Desember' },
];

const handleMonthChange = () => {
  router.get(route('admin.pawns.monthly'), {
    year: selectedYear.value,
    month: selectedMonth.value,
  }, { preserveState: true });
};

const formatRupiah = (val) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val || 0);
};

const formatDate = (dateStr) => {
  if (!dateStr) return '-';
  const d = new Date(dateStr);
  return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
};
</script>

<template>
  <AdminLayout>
    <Head title="Laporan Gadai Bulanan - POS Emas" />

    <div class="space-y-6">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2 text-xs font-mono text-amber-500 uppercase tracking-widest">
            <CalendarDays class="w-4 h-4" />
            <span>Rekapitulasi Bulanan Gadai</span>
          </div>
          <h2 class="text-2xl font-bold tracking-tight text-white mt-1">
            Laporan Gadai Emas Bulanan
          </h2>
          <p class="text-sm text-slate-400">
            Performa pencairan modal gadai, pelunasan pokok, dan peringatan nasabah jatuh tempo.
          </p>
        </div>

        <div class="flex items-center gap-2.5">
          <select
            v-model="selectedMonth"
            @change="handleMonthChange"
            class="px-3.5 py-2 rounded-xl bg-[#1E1E38] border border-slate-700 text-white text-sm focus:outline-none focus:border-amber-500"
          >
            <option v-for="m in months" :key="m.val" :value="m.val">{{ m.name }}</option>
          </select>
          <select
            v-model="selectedYear"
            @change="handleMonthChange"
            class="px-3.5 py-2 rounded-xl bg-[#1E1E38] border border-slate-700 text-white text-sm focus:outline-none focus:border-amber-500 font-mono"
          >
            <option :value="2025">2025</option>
            <option :value="2026">2026</option>
          </select>
        </div>
      </div>

      <!-- Quick Summary Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-4 rounded-2xl bg-[#1E1E38] border border-slate-800 flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400 shrink-0">
            <Coins class="w-6 h-6" />
          </div>
          <div>
            <p class="text-xs font-medium text-slate-400">Portofolio Pinjaman Aktif</p>
            <p class="text-lg font-bold text-amber-400 mt-0.5">{{ formatRupiah(stats.active_portfolio) }}</p>
          </div>
        </div>

        <div class="p-4 rounded-2xl bg-[#1E1E38] border border-slate-800 flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-rose-500/10 border border-rose-500/20 flex items-center justify-center text-rose-400 shrink-0">
            <ArrowUpRight class="w-6 h-6" />
          </div>
          <div>
            <p class="text-xs font-medium text-slate-400">Pencairan Baru Bulan Ini</p>
            <p class="text-lg font-bold text-rose-400 mt-0.5">{{ formatRupiah(stats.total_new_loans) }}</p>
          </div>
        </div>

        <div class="p-4 rounded-2xl bg-[#1E1E38] border border-slate-800 flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 shrink-0">
            <ArrowDownLeft class="w-6 h-6" />
          </div>
          <div>
            <p class="text-xs font-medium text-slate-400">Total Pelunasan Tebus</p>
            <p class="text-lg font-bold text-emerald-400 mt-0.5">{{ formatRupiah(stats.total_redeemed) }}</p>
          </div>
        </div>

        <div class="p-4 rounded-2xl bg-[#1E1E38] border border-slate-800 flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-purple-500/10 border border-purple-500/20 flex items-center justify-center text-purple-400 shrink-0">
            <AlertTriangle class="w-6 h-6" />
          </div>
          <div>
            <p class="text-xs font-medium text-slate-400">Jatuh Tempo Bulan Ini</p>
            <p class="text-xl font-bold text-purple-400 mt-0.5">{{ stats.due_warning_count }} Berkas</p>
          </div>
        </div>
      </div>

      <!-- Peringatan Jatuh Tempo -->
      <div v-if="dueThisMonth && dueThisMonth.length > 0" class="p-5 rounded-2xl bg-[#1E1E38] border border-amber-500/30">
        <div class="flex items-center justify-between mb-3">
          <h3 class="text-base font-bold text-amber-300 flex items-center gap-2">
            <AlertTriangle class="w-5 h-5 text-amber-400" />
            <span>Peringatan Jatuh Tempo Gadai Bulan Ini</span>
          </h3>
          <span class="text-xs text-slate-400">Perlu konfirmasi perpanjangan atau pelunasan nasabah</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
          <div 
            v-for="p in dueThisMonth" 
            :key="p.id"
            class="p-3.5 rounded-xl bg-[#151528] border border-slate-800 flex flex-col justify-between"
          >
            <div>
              <div class="flex items-center justify-between">
                <span class="font-mono text-xs font-bold text-amber-400">{{ p.pawn_number }}</span>
                <span class="text-[11px] text-rose-400 font-semibold font-mono">Tempo: {{ formatDate(p.due_date) }}</span>
              </div>
              <p class="text-xs font-bold text-white mt-1.5">{{ p.customer_name }}</p>
              <p class="text-[11px] text-slate-400 font-mono">{{ p.customer_phone || '-' }}</p>
              <p class="text-[11px] text-slate-300 mt-2">{{ p.item_name }} ({{ p.gross_weight }} Gr)</p>
            </div>
            <div class="mt-3 pt-2 border-t border-slate-800 flex items-center justify-between">
              <span class="text-xs font-bold font-mono text-white">{{ formatRupiah(p.loan_amount) }}</span>
              <a 
                v-if="p.customer_phone"
                :href="`https://wa.me/${p.customer_phone.replace(/[^0-9]/g, '')}?text=Halo%20Bpk/Ibu%20${encodeURIComponent(p.customer_name)},%20kami%20dari%20Toko%20Mas%20mengingatkan%20perihal%20gadai%20${p.pawn_number}%20akan%20jatuh%20tempo%20pada%20${p.due_date}.`"
                target="_blank"
                class="px-2.5 py-1 rounded-lg bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-300 text-xs font-semibold flex items-center gap-1 transition-colors"
              >
                <Phone class="w-3 h-3" />
                <span>WA Nasabah</span>
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Tabel Kalender Harian -->
      <div class="rounded-2xl bg-[#1E1E38] border border-slate-800 overflow-hidden shadow-xl">
        <div class="p-4 border-b border-slate-800 flex items-center justify-between">
          <h3 class="text-base font-bold text-white">Rincian Arus Kas Gadai Per Hari</h3>
          <span class="text-xs text-slate-400 font-mono">{{ calendarDays.length }} Hari</span>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left text-sm text-slate-300">
            <thead class="bg-[#151528] text-xs uppercase text-slate-400 font-semibold border-b border-slate-800">
              <tr>
                <th class="px-6 py-3.5">Tanggal</th>
                <th class="px-6 py-3.5 text-right">Pencairan Baru (Keluar)</th>
                <th class="px-6 py-3.5 text-right">Pelunasan Tebus (Masuk)</th>
                <th class="px-6 py-3.5 text-right">Jasa Titip Diterima</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/80">
              <tr
                v-for="d in calendarDays"
                :key="d.date"
                class="hover:bg-[#25254B]/40 transition-colors"
              >
                <td class="px-6 py-3 font-mono font-bold text-white text-xs">
                  {{ d.date }}
                </td>
                <td class="px-6 py-3 text-right font-mono text-xs" :class="d.new_loans > 0 ? 'text-rose-400 font-bold' : 'text-slate-500'">
                  {{ d.new_loans > 0 ? formatRupiah(d.new_loans) : '-' }}
                </td>
                <td class="px-6 py-3 text-right font-mono text-xs" :class="d.redeemed > 0 ? 'text-emerald-400 font-bold' : 'text-slate-500'">
                  {{ d.redeemed > 0 ? formatRupiah(d.redeemed) : '-' }}
                </td>
                <td class="px-6 py-3 text-right font-mono text-xs" :class="d.interest > 0 ? 'text-purple-300 font-bold' : 'text-slate-500'">
                  {{ d.interest > 0 ? formatRupiah(d.interest) : '-' }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
