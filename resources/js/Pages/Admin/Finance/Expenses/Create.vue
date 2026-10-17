<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
  ArrowDownLeft, ArrowLeft, Save, Tag, Calendar, 
  CreditCard, User, FileText
} from 'lucide-vue-next';

const props = defineProps({
  defaultCode: String,
  bankAccounts: Array,
  categories: Array,
});

const form = useForm({
  expense_number: props.defaultCode || 'EXP-202610-0001',
  category: props.categories[0] || 'Operasional Toko',
  title: '',
  amount: '',
  expense_date: new Date().toISOString().slice(0, 10),
  payment_method: 'tunai',
  bank_account_id: '',
  recipient: '',
  notes: '',
});

const submit = () => {
  form.post(route('admin.expenses.store'));
};
</script>

<template>
  <AdminLayout title="Tambah Pengeluaran Operasional">
    <Head title="Tambah Pengeluaran - POS Emas" />

    <div class="max-w-3xl mx-auto space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <Link
            :href="route('admin.expenses.index')"
            class="p-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors"
          >
            <ArrowLeft class="w-5 h-5" />
          </Link>
          <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-2.5">
              <ArrowDownLeft class="w-6 h-6 text-rose-500" />
              <span>Catat Pengeluaran Operasional Toko</span>
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
              Pengeluaran akan langsung memotong saldo kas toko dan tercatat di Buku Kas.
            </p>
          </div>
        </div>
      </div>

      <!-- Form Box -->
      <form @submit.prevent="submit" class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
              No. Bukti Pengeluaran <span class="text-rose-500">*</span>
            </label>
            <input
              v-model="form.expense_number"
              type="text"
              required
              class="w-full px-3.5 py-2 text-sm rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white font-mono focus:ring-2 focus:ring-amber-500/30"
            />
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
              Tanggal Pengeluaran <span class="text-rose-500">*</span>
            </label>
            <input
              v-model="form.expense_date"
              type="date"
              required
              class="w-full px-3.5 py-2 text-sm rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500/30"
            />
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
              Kategori Pengeluaran <span class="text-rose-500">*</span>
            </label>
            <select
              v-model="form.category"
              required
              class="w-full px-3.5 py-2 text-sm rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500/30"
            >
              <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
              Penerima Dana (Nama / Lembaga)
            </label>
            <input
              v-model="form.recipient"
              type="text"
              placeholder="Contoh: PLN, Percetakan Kotak Emas, Staf Toko"
              class="w-full px-3.5 py-2 text-sm rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500/30"
            />
          </div>

          <div class="md:col-span-2">
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
              Keperluan / Uraian Pengeluaran <span class="text-rose-500">*</span>
            </label>
            <input
              v-model="form.title"
              type="text"
              required
              placeholder="Contoh: Pembelian 200 pcs box beludru merah kalung dan cincin emas"
              class="w-full px-3.5 py-2 text-sm rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500/30"
            />
          </div>

          <div class="md:col-span-2">
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
              Nominal Beban Biaya (IDR) <span class="text-rose-500">*</span>
            </label>
            <input
              v-model="form.amount"
              type="number"
              step="1000"
              required
              placeholder="Contoh: 750000"
              class="w-full px-3.5 py-2.5 text-base rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white font-mono font-bold focus:ring-2 focus:ring-amber-500/30"
            />
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
              Metode Pembayaran <span class="text-rose-500">*</span>
            </label>
            <select
              v-model="form.payment_method"
              required
              class="w-full px-3.5 py-2 text-sm rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500/30"
            >
              <option value="tunai">💵 Tunai (Kas Laci Kasir)</option>
              <option value="transfer">🏦 Transfer Rekening Bank</option>
            </select>
          </div>

          <div v-if="form.payment_method === 'transfer'">
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
              Pilih Rekening Bank Sumber
            </label>
            <select
              v-model="form.bank_account_id"
              class="w-full px-3.5 py-2 text-sm rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500/30"
            >
              <option value="">-- Pilih Rekening --</option>
              <option v-for="b in bankAccounts" :key="b.id" :value="b.id">
                {{ b.bank_name }} - {{ b.account_number }} (a.n {{ b.account_holder }})
              </option>
            </select>
          </div>

          <div class="md:col-span-2">
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
              Catatan / Keterangan
            </label>
            <textarea
              v-model="form.notes"
              rows="2"
              placeholder="Catatan tambahan (opsional)..."
              class="w-full px-3.5 py-2 text-sm rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500/30"
            ></textarea>
          </div>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
          <Link
            :href="route('admin.expenses.index')"
            class="px-5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-sm font-semibold hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
          >
            Batal
          </Link>
          <button
            type="submit"
            :disabled="form.processing"
            class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-rose-500 hover:bg-rose-600 disabled:opacity-50 text-white font-bold text-sm shadow-md shadow-rose-500/20 transition-all cursor-pointer"
          >
            <Save class="w-4 h-4" />
            <span>Simpan Pengeluaran</span>
          </button>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>
