<script setup>
import { ref } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
  CreditCard, Search, Calendar, Building2, Plus, 
  CheckCircle2, Clock, AlertCircle, X, Trash2, Banknote
} from 'lucide-vue-next';

const props = defineProps({
  debts: Object,
  suppliers: Array,
  bankAccounts: Array,
  filters: Object,
  stats: Object,
});

const supplierFilter = ref(props.filters.supplier_id || '');
const statusFilter = ref(props.filters.status || '');
const isPayModalOpen = ref(false);
const selectedDebt = ref(null);

const payForm = useForm({
  payment_amount: 0,
  payment_date: new Date().toISOString().slice(0, 10),
  payment_method: 'transfer',
  bank_account_id: props.bankAccounts[0]?.id || '',
  notes: '',
});

const handleFilter = () => {
  router.get(route('admin.debts.index'), {
    supplier_id: supplierFilter.value,
    status: statusFilter.value,
  }, { preserveState: true, replace: true });
};

const openPayModal = (debt) => {
  selectedDebt.value = debt;
  payForm.payment_amount = parseFloat(debt.remaining_amount) || 0;
  payForm.notes = `Pembayaran utang emas ${debt.payable_code} ke ${debt.supplier?.name}`;
  isPayModalOpen.value = true;
};

const submitPayment = () => {
  if (!selectedDebt.value) return;
  payForm.post(route('admin.debts.pay', selectedDebt.value.id), {
    onSuccess: () => {
      isPayModalOpen.value = false;
    },
  });
};

const deleteDebt = (debt) => {
  if (confirm(`Apakah Anda yakin ingin menghapus catatan utang "${debt.payable_code}"? Data akan diarsipkan (Soft Delete).`)) {
    router.delete(route('admin.debts.destroy', debt.id));
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
  <AdminLayout title="Pembayaran Utang Supplier Emas">
    <Head title="Utang Supplier & Outstanding Kredit - POS Emas" />

    <div class="space-y-6">
      <!-- Header -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <h1 class="text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-2.5">
            <CreditCard class="w-6 h-6 text-amber-500" />
            <span>Pembayaran Utang & Outstanding Kredit Supplier</span>
          </h1>
          <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
            Pantau jatuh tempo faktur konsinyasi emas, cicilan utang pabrik, dan riwayat pelunasan.
          </p>
        </div>

        <div class="flex items-center gap-2.5">
          <Link
            :href="route('admin.purchases.index')"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 text-sm font-semibold transition-all shadow-sm"
          >
            <Building2 class="w-4 h-4 text-amber-500" />
            <span>Faktur Pembelian Emas</span>
          </Link>
        </div>
      </div>

      <!-- KPI Summary Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between">
          <div>
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Outstanding Kredit</p>
            <p class="text-2xl font-bold font-mono text-rose-600 dark:text-rose-400 mt-1">{{ formatRupiah(stats.total_outstanding) }}</p>
            <p class="text-xs text-slate-400 mt-0.5">Sisa utang belum dibayar</p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-rose-500/10 text-rose-500 flex items-center justify-center">
            <Clock class="w-6 h-6" />
          </div>
        </div>

        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between">
          <div>
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Utang Terbayar</p>
            <p class="text-2xl font-bold font-mono text-emerald-600 dark:text-emerald-400 mt-1">{{ formatRupiah(stats.total_paid) }}</p>
            <p class="text-xs text-slate-400 mt-0.5">Pelunasan supplier</p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center">
            <CheckCircle2 class="w-6 h-6" />
          </div>
        </div>

        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between">
          <div>
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Faktur Belum Lunas</p>
            <p class="text-2xl font-bold font-mono text-amber-500 mt-1">{{ stats.unpaid_count }} Tagihan</p>
            <p class="text-xs text-slate-400 mt-0.5">Jatuh tempo berjalan</p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-500 flex items-center justify-center">
            <AlertCircle class="w-6 h-6" />
          </div>
        </div>
      </div>

      <!-- Filters -->
      <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col md:flex-row gap-3 items-center justify-between">
        <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
          <select
            v-model="supplierFilter"
            @change="handleFilter"
            class="px-3 py-1.5 text-xs rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500/30"
          >
            <option value="">Semua Pabrik & Supplier</option>
            <option v-for="sup in suppliers" :key="sup.id" :value="sup.id">{{ sup.name }}</option>
          </select>

          <select
            v-model="statusFilter"
            @change="handleFilter"
            class="px-3 py-1.5 text-xs rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500/30"
          >
            <option value="">Semua Status Utang</option>
            <option value="unpaid">Belum Dibayar (Unpaid)</option>
            <option value="partial">Sebagian (Partial)</option>
            <option value="paid">Lunas (Paid)</option>
          </select>
        </div>
      </div>

      <!-- Debts Table -->
      <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse text-sm">
            <thead>
              <tr class="bg-slate-50/75 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800 text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                <th class="py-3.5 px-4 font-semibold">No. Utang / Faktur</th>
                <th class="py-3.5 px-4 font-semibold">Pabrik & Supplier</th>
                <th class="py-3.5 px-4 font-semibold">Jatuh Tempo</th>
                <th class="py-3.5 px-4 font-semibold text-right">Total Utang</th>
                <th class="py-3.5 px-4 font-semibold text-right">Sudah Dibayar</th>
                <th class="py-3.5 px-4 font-semibold text-right">Sisa Utang</th>
                <th class="py-3.5 px-4 font-semibold text-center">Status</th>
                <th class="py-3.5 px-4 font-semibold text-center">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
              <tr 
                v-for="d in debts.data" 
                :key="d.id"
                class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors"
              >
                <!-- No Utang / Faktur -->
                <td class="py-3.5 px-4">
                  <span class="font-mono text-xs font-bold text-amber-500">{{ d.payable_code }}</span>
                  <p v-if="d.purchase" class="text-xs text-slate-400 mt-0.5">Faktur: {{ d.purchase.invoice_number }}</p>
                </td>

                <!-- Supplier -->
                <td class="py-3.5 px-4">
                  <div class="font-bold text-slate-900 dark:text-white">{{ d.supplier?.name || '-' }}</div>
                  <p class="text-xs text-slate-400 mt-0.5">{{ d.supplier?.phone || '-' }}</p>
                </td>

                <!-- Jatuh Tempo -->
                <td class="py-3.5 px-4 text-xs font-semibold text-rose-500">
                  {{ formatDate(d.due_date) }}
                </td>

                <!-- Total Utang -->
                <td class="py-3.5 px-4 text-right font-mono font-semibold text-slate-900 dark:text-white">
                  {{ formatRupiah(d.total_debt) }}
                </td>

                <!-- Sudah Dibayar -->
                <td class="py-3.5 px-4 text-right font-mono font-semibold text-emerald-600 dark:text-emerald-400">
                  {{ formatRupiah(d.paid_amount) }}
                </td>

                <!-- Sisa Utang -->
                <td class="py-3.5 px-4 text-right font-mono font-bold text-rose-600 dark:text-rose-400">
                  {{ formatRupiah(d.remaining_amount) }}
                </td>

                <!-- Status -->
                <td class="py-3.5 px-4 text-center">
                  <span 
                    :class="[
                      d.status === 'paid' ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20' : 
                      d.status === 'partial' ? 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20' : 
                      'bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-500/20',
                      'px-2.5 py-0.5 rounded-full text-xs font-semibold border uppercase tracking-wider'
                    ]"
                  >
                    {{ d.status }}
                  </span>
                </td>

                <!-- Aksi -->
                <td class="py-3.5 px-4 text-center">
                  <div class="flex items-center justify-center gap-1.5">
                    <button
                      v-if="d.status !== 'paid'"
                      @click="openPayModal(d)"
                      title="Bayar Utang Emas"
                      class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-bold text-xs shadow-sm transition-all cursor-pointer"
                    >
                      <Banknote class="w-3.5 h-3.5" />
                      <span>Bayar</span>
                    </button>
                    <button
                      @click="deleteDebt(d)"
                      title="Hapus / Arsipkan"
                      class="p-1.5 rounded-lg text-rose-500 hover:bg-rose-500/10 transition-colors"
                    >
                      <Trash2 class="w-4 h-4" />
                    </button>
                  </div>
                </td>
              </tr>

              <tr v-if="debts.data.length === 0">
                <td colspan="8" class="py-12 text-center text-slate-400">
                  <CreditCard class="w-12 h-12 mx-auto text-slate-300 dark:text-slate-700 mb-2" />
                  <p class="font-semibold">Belum ada data utang yang sesuai filter</p>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Modal Pembayaran Utang -->
    <div v-if="isPayModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/70 backdrop-blur-sm p-4">
      <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
          <div class="flex items-center gap-2 text-slate-900 dark:text-white font-bold text-base">
            <Banknote class="w-5 h-5 text-emerald-500" />
            <span>Bayar Utang Emas: {{ selectedDebt?.payable_code }}</span>
          </div>
          <button @click="isPayModalOpen = false" class="text-slate-400 hover:text-white">
            <X class="w-5 h-5" />
          </button>
        </div>

        <form @submit.prevent="submitPayment" class="space-y-4">
          <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800 text-xs space-y-1">
            <p class="text-slate-500">Supplier: <span class="font-bold text-slate-800 dark:text-slate-200">{{ selectedDebt?.supplier?.name }}</span></p>
            <p class="text-slate-500">Sisa Tagihan: <span class="font-bold text-rose-500 font-mono">{{ formatRupiah(selectedDebt?.remaining_amount) }}</span></p>
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
              Nominal Bayar (Rp) <span class="text-rose-500">*</span>
            </label>
            <input
              v-model="payForm.payment_amount"
              type="number"
              step="1000"
              required
              :max="selectedDebt?.remaining_amount"
              class="w-full px-3.5 py-2 text-base font-mono font-bold rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500/30"
            />
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                Tgl Pembayaran <span class="text-rose-500">*</span>
              </label>
              <input
                v-model="payForm.payment_date"
                type="date"
                required
                class="w-full px-3 py-1.5 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white"
              />
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                Metode Pembayaran <span class="text-rose-500">*</span>
              </label>
              <select
                v-model="payForm.payment_method"
                required
                class="w-full px-3 py-1.5 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white"
              >
                <option value="transfer">Transfer Rekening</option>
                <option value="tunai">Kasir Tunai</option>
              </select>
            </div>
          </div>

          <div v-if="payForm.payment_method === 'transfer'">
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
              Pilih Rekening Bank Sumber Toko
            </label>
            <select
              v-model="payForm.bank_account_id"
              class="w-full px-3 py-1.5 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white"
            >
              <option v-for="b in bankAccounts" :key="b.id" :value="b.id">
                {{ b.bank_name }} - {{ b.account_number }} (a.n {{ b.account_holder }})
              </option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
              Catatan Pembayaran
            </label>
            <input
              v-model="payForm.notes"
              type="text"
              class="w-full px-3 py-1.5 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white"
            />
          </div>

          <div class="flex items-center justify-end gap-3 pt-3">
            <button
              type="button"
              @click="isPayModalOpen = false"
              class="px-4 py-2 rounded-xl border border-slate-300 dark:border-slate-700 text-xs font-semibold text-slate-700 dark:text-slate-300"
            >
              Batal
            </button>
            <button
              type="submit"
              :disabled="payForm.processing"
              class="px-5 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-600 font-bold text-xs text-slate-950 shadow-md transition-all cursor-pointer"
            >
              Konfirmasi & Potong Kas
            </button>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>
