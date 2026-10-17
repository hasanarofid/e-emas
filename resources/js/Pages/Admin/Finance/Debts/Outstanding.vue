<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
  Coins, AlertCircle, Clock, Calendar, 
  Building2, ArrowRight, Printer, AlertTriangle
} from 'lucide-vue-next';

const props = defineProps({
  debts: Object,
  suppliers: Array,
  filters: Object,
  stats: Object,
});

const supplierId = ref(props.filters.supplier_id || '');

const handleFilter = () => {
  router.get(route('admin.debts.outstanding'), {
    supplier_id: supplierId.value,
  }, { preserveState: true });
};

const formatRupiah = (val) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val || 0);
};

const formatDate = (dateString) => {
  if (!dateString) return '-';
  const d = new Date(dateString);
  return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
};

const isOverdue = (dueDate) => {
  if (!dueDate) return false;
  return new Date(dueDate) < new Date();
};
</script>

<template>
  <AdminLayout>
    <Head title="Outstanding Kredit & Tagihan Supplier" />

    <div class="space-y-6">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2 text-xs font-mono text-amber-500 uppercase tracking-widest">
            <Coins class="w-4 h-4" />
            <span>Manajemen Kewajiban Toko</span>
          </div>
          <h2 class="text-2xl font-bold tracking-tight text-white mt-1">
            Outstanding Kredit & Utang Berjalan
          </h2>
          <p class="text-sm text-slate-400">
            Daftar seluruh kewajiban utang pembelian emas ke supplier/pabrik yang belum lunas beserta tanggal jatuh tempo.
          </p>
        </div>

        <div class="flex items-center gap-3">
          <Link
            :href="route('admin.debts.index')"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#1C1C3E] border border-[#2D2D54] text-slate-300 hover:text-white hover:bg-[#25254B] transition-colors text-sm font-semibold"
          >
            <span>Buku Pembayaran Utang</span>
            <ArrowRight class="w-4 h-4" />
          </Link>
        </div>
      </div>

      <!-- KPI Summary -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-[#1C1C3E] border border-[#2D2D54] rounded-2xl p-5 flex items-center justify-between">
          <div>
            <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Total Tagihan Belum Lunas</p>
            <h3 class="text-2xl font-bold text-amber-400 mt-1">{{ formatRupiah(stats.total_outstanding) }}</h3>
            <span class="text-xs text-slate-400 mt-1">Sisa pokok utang aktif</span>
          </div>
          <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center">
            <Coins class="w-6 h-6" />
          </div>
        </div>

        <div class="bg-[#1C1C3E] border border-[#2D2D54] rounded-2xl p-5 flex items-center justify-between">
          <div>
            <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Tagihan Jatuh Tempo (Overdue)</p>
            <h3 class="text-2xl font-bold text-rose-400 mt-1">{{ stats.overdue_count }} Faktur</h3>
            <span class="text-xs text-slate-400 mt-1">Melewati batas tanggal jatuh tempo</span>
          </div>
          <div class="w-12 h-12 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 flex items-center justify-center">
            <AlertTriangle class="w-6 h-6" />
          </div>
        </div>

        <div class="bg-[#1C1C3E] border border-[#2D2D54] rounded-2xl p-5 flex items-center justify-between">
          <div>
            <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Nominal Jatuh Tempo</p>
            <h3 class="text-2xl font-bold text-rose-400 mt-1">{{ formatRupiah(stats.overdue_amount) }}</h3>
            <span class="text-xs text-slate-400 mt-1">Segera bayarkan ke supplier</span>
          </div>
          <div class="w-12 h-12 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 flex items-center justify-center">
            <Clock class="w-6 h-6" />
          </div>
        </div>
      </div>

      <!-- Filter -->
      <div class="bg-[#1C1C3E] border border-[#2D2D54] rounded-2xl p-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <span class="text-xs text-slate-400">Filter Supplier:</span>
          <select
            v-model="supplierId"
            @change="handleFilter"
            class="bg-[#15152D] border border-[#2D2D54] rounded-xl px-3 py-1.5 text-sm text-slate-300 focus:outline-none focus:border-amber-400"
          >
            <option value="">Semua Supplier Emas</option>
            <option v-for="s in suppliers" :key="s.id" :value="s.id">{{ s.name }}</option>
          </select>
        </div>
      </div>

      <!-- Table Outstanding -->
      <div class="bg-[#1C1C3E] border border-[#2D2D54] rounded-2xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-sm text-slate-300">
            <thead class="bg-[#15152D] text-xs uppercase font-mono tracking-wider text-slate-400 border-b border-[#2D2D54]">
              <tr>
                <th class="px-5 py-3.5">Kode Utang & Faktur</th>
                <th class="px-5 py-3.5">Supplier Pabrik</th>
                <th class="px-5 py-3.5">Jatuh Tempo</th>
                <th class="px-5 py-3.5 text-right">Total Utang</th>
                <th class="px-5 py-3.5 text-right">Sudah Dibayar</th>
                <th class="px-5 py-3.5 text-right">Sisa Outstanding</th>
                <th class="px-5 py-3.5 text-center">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-[#2D2D54]/50">
              <tr v-for="d in debts.data" :key="d.id" class="hover:bg-[#24244D]/50 transition-colors">
                <td class="px-5 py-4">
                  <div class="font-bold text-white font-mono">{{ d.payable_code }}</div>
                  <div class="text-xs text-slate-400">{{ d.purchase?.invoice_number || '-' }}</div>
                </td>
                <td class="px-5 py-4">
                  <div class="font-medium text-slate-200">{{ d.supplier?.name }}</div>
                  <div class="text-xs text-slate-400">{{ d.supplier?.phone || '-' }}</div>
                </td>
                <td class="px-5 py-4">
                  <div :class="[isOverdue(d.due_date) ? 'text-rose-400 font-bold' : 'text-slate-300', 'font-mono text-xs']">
                    {{ formatDate(d.due_date) }}
                  </div>
                  <div v-if="isOverdue(d.due_date)" class="text-[10px] text-rose-400 font-medium uppercase tracking-wider">
                    Lewat Jatuh Tempo
                  </div>
                </td>
                <td class="px-5 py-4 text-right font-mono text-slate-300">
                  {{ formatRupiah(d.total_amount) }}
                </td>
                <td class="px-5 py-4 text-right font-mono text-emerald-400">
                  {{ formatRupiah(d.paid_amount) }}
                </td>
                <td class="px-5 py-4 text-right font-mono font-bold text-amber-400">
                  {{ formatRupiah(d.remaining_amount) }}
                </td>
                <td class="px-5 py-4 text-center">
                  <span 
                    :class="[
                      d.status === 'unpaid' ? 'bg-rose-500/10 text-rose-400 border-rose-500/20' : 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                      'px-2.5 py-1 rounded-full text-xs font-semibold uppercase border'
                    ]"
                  >
                    {{ d.status === 'unpaid' ? 'Belum Dibayar' : 'Dicicil Sebagian' }}
                  </span>
                </td>
              </tr>
              <tr v-if="!debts.data || debts.data.length === 0">
                <td colspan="7" class="px-5 py-10 text-center text-slate-400">
                  Semua utang kredit supplier telah lunas atau belum ada tagihan.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
