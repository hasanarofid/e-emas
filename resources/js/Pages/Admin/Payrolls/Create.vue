<script setup>
import { ref, computed, watch } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
  Banknote, ArrowLeft, Save, UserCheck, Calendar, 
  CreditCard, Calculator, FileText, CheckCircle2
} from 'lucide-vue-next';

const props = defineProps({
  employees: Array,
  bankAccounts: Array,
  payrollCode: String,
  selectedEmployee: Object,
  calculatedCommission: Number,
  currentMonth: Number,
  currentYear: Number,
});

const form = useForm({
  payroll_code: props.payrollCode || 'PAY-202610-0001',
  employee_id: props.selectedEmployee ? props.selectedEmployee.id : (props.employees[0]?.id || ''),
  period_month: props.currentMonth || new Date().getMonth() + 1,
  period_year: props.currentYear || new Date().getFullYear(),
  payment_date: new Date().toISOString().slice(0, 10),
  base_salary: props.selectedEmployee ? parseFloat(props.selectedEmployee.base_salary) : 0,
  allowance: props.selectedEmployee ? parseFloat(props.selectedEmployee.allowance) : 0,
  commission_amount: props.calculatedCommission || 0,
  bonus: 0,
  deductions: 0,
  net_salary: 0,
  payment_method: 'transfer',
  bank_account_id: props.bankAccounts[0]?.id || '',
  status: 'paid',
  notes: '',
});

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

// Re-fetch commissions & base salary when employee changes
const onEmployeeChange = () => {
  const emp = props.employees.find(e => e.id === Number(form.employee_id));
  if (emp) {
    form.base_salary = parseFloat(emp.base_salary) || 0;
    form.allowance = parseFloat(emp.allowance) || 0;
    router.get(route('admin.payrolls.create'), {
      employee_id: emp.id,
      month: form.period_month,
      year: form.period_year,
    }, { preserveState: true, replace: true });
  }
};

// Hitung Net Salary otomatis
const calculatedNet = computed(() => {
  const base = parseFloat(form.base_salary) || 0;
  const allow = parseFloat(form.allowance) || 0;
  const comm = parseFloat(form.commission_amount) || 0;
  const bon = parseFloat(form.bonus) || 0;
  const ded = parseFloat(form.deductions) || 0;
  return Math.max(0, base + allow + comm + bon - ded);
});

watch(calculatedNet, (newVal) => {
  form.net_salary = newVal;
}, { immediate: true });

const submit = () => {
  form.net_salary = calculatedNet.value;
  form.post(route('admin.payrolls.store'));
};

const formatRupiah = (val) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val || 0);
};
</script>

<template>
  <AdminLayout title="Proses Penggajian Pegawai">
    <Head title="Proses Penggajian - POS Emas" />

    <div class="max-w-4xl mx-auto space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <Link
            :href="route('admin.payrolls.index')"
            class="p-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors"
          >
            <ArrowLeft class="w-5 h-5" />
          </Link>
          <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-2.5">
              <Banknote class="w-6 h-6 text-emerald-500" />
              <span>Proses Hitung & Pembayaran Gaji</span>
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
              Hitung gaji pokok, tunjangan, komisi omset kasir, dan cetak slip gaji emas resmi.
            </p>
          </div>
        </div>
      </div>

      <form @submit.prevent="submit" class="space-y-6">
        <!-- 1. Pegawai & Periode -->
        <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
          <h2 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2 pb-3 border-b border-slate-100 dark:border-slate-800 uppercase tracking-wider">
            <UserCheck class="w-4 h-4 text-amber-500" />
            <span>1. Data Pegawai & Periode Gaji</span>
          </h2>

          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                No. Slip Gaji <span class="text-rose-500">*</span>
              </label>
              <input
                v-model="form.payroll_code"
                type="text"
                required
                class="w-full px-3.5 py-2 text-sm rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white font-mono focus:ring-2 focus:ring-amber-500/30"
              />
            </div>

            <div class="md:col-span-3">
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                Pilih Pegawai <span class="text-rose-500">*</span>
              </label>
              <select
                v-model="form.employee_id"
                @change="onEmployeeChange"
                required
                class="w-full px-3.5 py-2 text-sm rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white font-semibold focus:ring-2 focus:ring-amber-500/30"
              >
                <option v-for="emp in employees" :key="emp.id" :value="emp.id">
                  {{ emp.name }} - {{ emp.position }} (Gaji Pokok: {{ formatRupiah(emp.base_salary) }})
                </option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                Bulan Penggajian <span class="text-rose-500">*</span>
              </label>
              <select
                v-model="form.period_month"
                @change="onEmployeeChange"
                required
                class="w-full px-3.5 py-2 text-sm rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500/30"
              >
                <option v-for="m in months" :key="m.value" :value="m.value">{{ m.label }}</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                Tahun <span class="text-rose-500">*</span>
              </label>
              <input
                v-model="form.period_year"
                type="number"
                required
                class="w-full px-3.5 py-2 text-sm rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white font-mono focus:ring-2 focus:ring-amber-500/30"
              />
            </div>

            <div class="md:col-span-2">
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                Tanggal Pembayaran / Transfer <span class="text-rose-500">*</span>
              </label>
              <input
                v-model="form.payment_date"
                type="date"
                required
                class="w-full px-3.5 py-2 text-sm rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500/30"
              />
            </div>
          </div>
        </div>

        <!-- 2. Rincian Penghasilan & Potongan -->
        <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
          <h2 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2 pb-3 border-b border-slate-100 dark:border-slate-800 uppercase tracking-wider">
            <Calculator class="w-4 h-4 text-amber-500" />
            <span>2. Rincian Komponen Gaji & Insentif</span>
          </h2>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                Gaji Pokok (Rp) <span class="text-rose-500">*</span>
              </label>
              <input
                v-model="form.base_salary"
                type="number"
                step="1000"
                required
                class="w-full px-3.5 py-2 text-sm rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white font-mono focus:ring-2 focus:ring-amber-500/30"
              />
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                Tunjangan Tetap / Makan (Rp)
              </label>
              <input
                v-model="form.allowance"
                type="number"
                step="1000"
                class="w-full px-3.5 py-2 text-sm rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white font-mono focus:ring-2 focus:ring-amber-500/30"
              />
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                Komisi Omset Penjualan Emas (Rp)
              </label>
              <input
                v-model="form.commission_amount"
                type="number"
                step="1000"
                class="w-full px-3.5 py-2 text-sm rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white font-mono focus:ring-2 focus:ring-amber-500/30"
              />
              <span class="text-[11px] text-emerald-500 mt-1 block">Otomatis dihitung dari transaksi kasir</span>
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                Bonus Prestasi / Lembur (Rp)
              </label>
              <input
                v-model="form.bonus"
                type="number"
                step="1000"
                class="w-full px-3.5 py-2 text-sm rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white font-mono focus:ring-2 focus:ring-amber-500/30"
              />
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                Potongan / Kasbon (Rp)
              </label>
              <input
                v-model="form.deductions"
                type="number"
                step="1000"
                class="w-full px-3.5 py-2 text-sm rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-rose-300 dark:border-rose-800 text-rose-600 dark:text-rose-400 font-mono focus:ring-2 focus:ring-rose-500/30"
              />
            </div>

            <!-- Big Highlight Take Home Pay -->
            <div class="p-3 rounded-xl bg-amber-500/10 border border-amber-500/20 flex flex-col justify-center">
              <span class="text-[11px] font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider">
                Total Gaji Bersih (THP)
              </span>
              <span class="text-xl font-bold font-mono text-amber-500 mt-0.5">
                {{ formatRupiah(calculatedNet) }}
              </span>
            </div>
          </div>
        </div>

        <!-- 3. Metode Pembayaran & Kas Toko -->
        <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
          <h2 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2 pb-3 border-b border-slate-100 dark:border-slate-800 uppercase tracking-wider">
            <CreditCard class="w-4 h-4 text-amber-500" />
            <span>3. Cara Bayar & Rekening Sumber Dana</span>
          </h2>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                Metode Pembayaran <span class="text-rose-500">*</span>
              </label>
              <select
                v-model="form.payment_method"
                required
                class="w-full px-3.5 py-2 text-sm rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500/30"
              >
                <option value="transfer">Transfer Bank (Payroll)</option>
                <option value="tunai">Tunai dari Kasir Toko</option>
              </select>
            </div>

            <div v-if="form.payment_method === 'transfer'">
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                Rekening Sumber Toko
              </label>
              <select
                v-model="form.bank_account_id"
                class="w-full px-3.5 py-2 text-sm rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500/30"
              >
                <option v-for="b in bankAccounts" :key="b.id" :value="b.id">
                  {{ b.bank_name }} - {{ b.account_number }} (a.n {{ b.account_holder }})
                </option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                Status Slip <span class="text-rose-500">*</span>
              </label>
              <select
                v-model="form.status"
                required
                class="w-full px-3.5 py-2 text-sm rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500/30"
              >
                <option value="paid">Dibayar Lunas (Paid)</option>
                <option value="draft">Draft (Belum Ditransfer)</option>
              </select>
            </div>

            <div class="md:col-span-3">
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                Catatan / Keterangan Slip
              </label>
              <textarea
                v-model="form.notes"
                rows="2"
                placeholder="Catatan tambahan untuk slip gaji pegawai..."
                class="w-full px-3.5 py-2 text-sm rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500/30"
              ></textarea>
            </div>
          </div>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-end gap-3">
          <Link
            :href="route('admin.payrolls.index')"
            class="px-5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-sm font-semibold hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
          >
            Batal
          </Link>
          <button
            type="submit"
            :disabled="form.processing"
            class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 disabled:opacity-50 text-slate-950 font-bold text-sm shadow-md shadow-emerald-500/20 transition-all cursor-pointer"
          >
            <CheckCircle2 class="w-4 h-4" />
            <span>Simpan & Terbitkan Slip Gaji</span>
          </button>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>
