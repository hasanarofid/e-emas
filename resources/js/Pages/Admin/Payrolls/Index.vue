<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
  Banknote, Plus, Search, Calendar, UserCheck, 
  CreditCard, FileText, Printer, Trash2, CheckCircle2,
  AlertCircle
} from 'lucide-vue-next';

const props = defineProps({
  payrolls: Object,
  employees: Array,
  filters: Object,
  stats: Object,
});

const monthFilter = ref(props.filters.month || '');
const yearFilter = ref(props.filters.year || new Date().getFullYear());
const employeeFilter = ref(props.filters.employee_id || '');
const statusFilter = ref(props.filters.status || '');

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

const handleFilter = () => {
  router.get(route('admin.payrolls.index'), {
    month: monthFilter.value,
    year: yearFilter.value,
    employee_id: employeeFilter.value,
    status: statusFilter.value,
  }, { preserveState: true, replace: true });
};

const deletePayroll = (payroll) => {
  if (confirm(`Apakah Anda yakin ingin membatalkan/menghapus slip gaji "${payroll.payroll_code}" untuk ${payroll.employee?.name}? Data akan diarsipkan (Soft Delete).`)) {
    router.delete(route('admin.payrolls.destroy', payroll.id));
  }
};

const formatRupiah = (val) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val || 0);
};

const formatDate = (dateString) => {
  if (!dateString) return '-';
  const d = new Date(dateString);
  return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
};
</script>

<template>
  <AdminLayout title="Data Penggajian Pegawai">
    <Head title="Data Penggajian - POS Emas" />

    <div class="space-y-6">
      <!-- Header -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <h1 class="text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-2.5">
            <Banknote class="w-6 h-6 text-emerald-500" />
            <span>Riwayat & Slip Penggajian Pegawai</span>
          </h1>
          <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
            Rekap slip gaji karyawan, komisi penjualan kasir toko emas, dan bukti bayar transfer bank.
          </p>
        </div>

        <div class="flex items-center gap-2.5">
          <Link
            :href="route('admin.employees.index')"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 text-sm font-semibold transition-all shadow-sm"
          >
            <UserCheck class="w-4 h-4 text-blue-500" />
            <span>Data Pegawai</span>
          </Link>
          <Link
            :href="route('admin.payrolls.create')"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-sm shadow-md shadow-amber-500/20 transition-all cursor-pointer"
          >
            <Plus class="w-4 h-4" />
            <span>Proses Penggajian Baru</span>
          </Link>
        </div>
      </div>

      <!-- KPI Summary Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between">
          <div>
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Gaji Dibayarkan</p>
            <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">{{ formatRupiah(stats.total_paid) }}</p>
            <p class="text-xs text-slate-400 mt-0.5">Kumulatif seluruh slip</p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center">
            <CheckCircle2 class="w-6 h-6" />
          </div>
        </div>

        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between">
          <div>
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Gaji Bulan Ini</p>
            <p class="text-2xl font-bold text-amber-500 mt-1">{{ formatRupiah(stats.this_month_total) }}</p>
            <p class="text-xs text-slate-400 mt-0.5">Pengeluaran periode aktif</p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-500 flex items-center justify-center">
            <Calendar class="w-6 h-6" />
          </div>
        </div>

        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between">
          <div>
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Lembar Slip Gaji</p>
            <p class="text-2xl font-bold text-slate-900 dark:text-white mt-1">{{ stats.total_slips }} Slip</p>
            <p class="text-xs text-slate-400 mt-0.5">Dokumen pembayaran</p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-blue-500/10 text-blue-500 flex items-center justify-center">
            <FileText class="w-6 h-6" />
          </div>
        </div>
      </div>

      <!-- Filters & Period Selectors -->
      <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col md:flex-row gap-3 items-center justify-between">
        <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
          <select
            v-model="employeeFilter"
            @change="handleFilter"
            class="px-3 py-2 text-sm rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500/30"
          >
            <option value="">Semua Pegawai</option>
            <option v-for="emp in employees" :key="emp.id" :value="emp.id">
              {{ emp.name }} ({{ emp.position }})
            </option>
          </select>

          <select
            v-model="monthFilter"
            @change="handleFilter"
            class="px-3 py-2 text-sm rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500/30"
          >
            <option value="">Semua Bulan</option>
            <option v-for="m in months" :key="m.value" :value="m.value">{{ m.label }}</option>
          </select>

          <select
            v-model="yearFilter"
            @change="handleFilter"
            class="px-3 py-2 text-sm rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500/30"
          >
            <option value="2025">2025</option>
            <option value="2026">2026</option>
            <option value="2027">2027</option>
          </select>

          <select
            v-model="statusFilter"
            @change="handleFilter"
            class="px-3 py-2 text-sm rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500/30"
          >
            <option value="">Semua Status</option>
            <option value="paid">Dibayar (Paid)</option>
            <option value="draft">Draft</option>
            <option value="cancelled">Dibatalkan</option>
          </select>
        </div>
      </div>

      <!-- Payroll Table -->
      <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse text-sm">
            <thead>
              <tr class="bg-slate-50/75 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800 text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                <th class="py-3.5 px-4 font-semibold">No. Slip / Tanggal</th>
                <th class="py-3.5 px-4 font-semibold">Pegawai & Jabatan</th>
                <th class="py-3.5 px-4 font-semibold">Periode Gaji</th>
                <th class="py-3.5 px-4 font-semibold text-right">Gaji Pokok & Tun.</th>
                <th class="py-3.5 px-4 font-semibold text-right">Komisi & Bonus</th>
                <th class="py-3.5 px-4 font-semibold text-right">Take Home Pay</th>
                <th class="py-3.5 px-4 font-semibold text-center">Status / Bayar</th>
                <th class="py-3.5 px-4 font-semibold text-center">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
              <tr 
                v-for="pay in payrolls.data" 
                :key="pay.id"
                class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors"
              >
                <!-- Slip & Tanggal -->
                <td class="py-3.5 px-4">
                  <span class="font-bold font-mono text-amber-500">{{ pay.payroll_code }}</span>
                  <p class="text-xs text-slate-400 mt-0.5">{{ formatDate(pay.payment_date) }}</p>
                </td>

                <!-- Pegawai -->
                <td class="py-3.5 px-4">
                  <div class="font-bold text-slate-900 dark:text-white">{{ pay.employee?.name || '-' }}</div>
                  <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    {{ pay.employee?.position || '-' }} ({{ pay.employee?.employee_code || '-' }})
                  </div>
                </td>

                <!-- Periode -->
                <td class="py-3.5 px-4">
                  <span class="font-semibold text-slate-800 dark:text-slate-200">
                    {{ months.find(m => m.value === pay.period_month)?.label || pay.period_month }} {{ pay.period_year }}
                  </span>
                  <p class="text-xs text-slate-400 mt-0.5 capitalize">{{ pay.payment_method }}</p>
                </td>

                <!-- Gaji Pokok & Tunjangan -->
                <td class="py-3.5 px-4 text-right">
                  <span class="font-mono text-slate-900 dark:text-white font-semibold">{{ formatRupiah(pay.base_salary) }}</span>
                  <p v-if="pay.allowance > 0" class="text-[11px] text-slate-400">+ Tun: {{ formatRupiah(pay.allowance) }}</p>
                </td>

                <!-- Komisi & Bonus -->
                <td class="py-3.5 px-4 text-right">
                  <span v-if="pay.commission_amount > 0 || pay.bonus > 0" class="font-mono text-emerald-600 dark:text-emerald-400 font-semibold">
                    +{{ formatRupiah(Number(pay.commission_amount) + Number(pay.bonus)) }}
                  </span>
                  <span v-else class="text-xs text-slate-400">-</span>
                  <p v-if="pay.deductions > 0" class="text-[11px] text-rose-500 font-mono">- Pot: {{ formatRupiah(pay.deductions) }}</p>
                </td>

                <!-- Net Take Home Pay -->
                <td class="py-3.5 px-4 text-right">
                  <span class="font-bold font-mono text-amber-500 text-base">
                    {{ formatRupiah(pay.net_salary) }}
                  </span>
                </td>

                <!-- Status & Metode -->
                <td class="py-3.5 px-4 text-center">
                  <span
                    :class="[
                      pay.status === 'paid' ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20' : 
                      pay.status === 'draft' ? 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20' : 
                      'bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-500/20',
                      'px-2.5 py-0.5 rounded-full text-xs font-semibold border uppercase tracking-wider'
                    ]"
                  >
                    {{ pay.status }}
                  </span>
                  <p v-if="pay.bank_account" class="text-[10px] text-slate-400 mt-1">via {{ pay.bank_account.bank_name }}</p>
                </td>

                <!-- Aksi -->
                <td class="py-3.5 px-4 text-center">
                  <div class="flex items-center justify-center gap-1.5">
                    <Link
                      :href="route('admin.payrolls.show', pay.id)"
                      title="Cetak Slip Gaji Toko Emas"
                      class="p-1.5 rounded-lg text-amber-500 hover:bg-amber-500/10 transition-colors"
                    >
                      <Printer class="w-4 h-4" />
                    </Link>
                    <button
                      @click="deletePayroll(pay)"
                      title="Batalkan / Hapus Slip Gaji (Soft Delete)"
                      class="p-1.5 rounded-lg text-rose-500 hover:bg-rose-500/10 transition-colors"
                    >
                      <Trash2 class="w-4 h-4" />
                    </button>
                  </div>
                </td>
              </tr>

              <tr v-if="payrolls.data.length === 0">
                <td colspan="8" class="py-12 text-center text-slate-400">
                  <Banknote class="w-12 h-12 mx-auto text-slate-300 dark:text-slate-700 mb-2" />
                  <p class="font-semibold">Belum ada slip gaji pada periode ini</p>
                  <p class="text-xs mt-1">Klik tombol "Proses Penggajian Baru" untuk membuat slip gaji pegawai.</p>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="payrolls.links && payrolls.links.length > 3" class="p-4 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between">
          <p class="text-xs text-slate-500">
            Menampilkan {{ payrolls.from }} - {{ payrolls.to }} dari {{ payrolls.total }} data
          </p>
          <div class="flex gap-1">
            <template v-for="(link, i) in payrolls.links" :key="i">
              <Link
                v-if="link.url"
                :href="link.url"
                v-html="link.label"
                :class="[
                  link.active ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800',
                  'px-3 py-1.5 rounded-lg text-xs font-medium transition-colors'
                ]"
              />
              <span
                v-else
                v-html="link.label"
                class="px-3 py-1.5 rounded-lg text-xs font-medium text-slate-400 dark:text-slate-600 opacity-60"
              />
            </template>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
