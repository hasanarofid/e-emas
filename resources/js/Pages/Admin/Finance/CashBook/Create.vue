<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
  Calculator, ArrowLeft, Save, ArrowDownLeft, ArrowUpRight, 
  Wallet, Building2, FileText
} from 'lucide-vue-next';

const props = defineProps({
  defaultCode: String,
  bankAccounts: Array,
});

const form = useForm({
  transaction_number: props.defaultCode || 'CSH-202610-0001',
  type: 'in',
  category: 'modal_awal',
  amount: '',
  transaction_date: new Date().toISOString().slice(0, 10),
  bank_account_id: '',
  description: '',
  notes: '',
});

const categories = {
  in: [
    { value: 'modal_awal', label: 'Modal Awal Kasir Pagi Hari' },
    { value: 'setoran_kasir', label: 'Setoran Uang Kas Masuk' },
    { value: 'tarik_tunai', label: 'Tarik Tunai dari Bank ke Toko' },
    { value: 'pendapatan_lain', label: 'Pendapatan Lain-lain' },
  ],
  out: [
    { value: 'setor_bank', label: 'Setor Kasir Tunai ke Rekening Bank' },
    { value: 'prive_owner', label: 'Pengambilan Prive Pemilik' },
    { value: 'transfer_antar_kas', label: 'Pindah Dana / Transfer Antar Kas' },
    { value: 'biaya_lain', label: 'Beban / Pengeluaran Lain' },
  ]
};

const submit = () => {
  form.post(route('admin.cash-book.store'));
};
</script>

<template>
  <AdminLayout title="Catat Arus Kas Toko">
    <Head title="Catat Arus Kas - POS Emas" />

    <div class="max-w-3xl mx-auto space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <Link
            :href="route('admin.cash-book.index')"
            class="p-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors"
          >
            <ArrowLeft class="w-5 h-5" />
          </Link>
          <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-2.5">
              <Calculator class="w-6 h-6 text-amber-500" />
              <span>Catat Arus Kas Masuk / Keluar</span>
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
              Tambah mutasi saldo fisik laci kasir tunai atau rekening bank toko emas.
            </p>
          </div>
        </div>
      </div>

      <!-- Form Box -->
      <form @submit.prevent="submit" class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-6">
        
        <!-- Pilihan Arah Kas -->
        <div>
          <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-2">
            Arah Arus Kas <span class="text-rose-500">*</span>
          </label>
          <div class="grid grid-cols-2 gap-4">
            <button
              type="button"
              @click="form.type = 'in'; form.category = 'modal_awal'"
              :class="[
                form.type === 'in' 
                  ? 'bg-emerald-500/15 border-emerald-500 text-emerald-600 dark:text-emerald-400 font-bold ring-2 ring-emerald-500/20' 
                  : 'bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-500',
                'p-4 rounded-xl border flex items-center justify-center gap-3 transition-all'
              ]"
            >
              <ArrowDownLeft class="w-5 h-5" />
              <span>KAS MASUK (INFLOW)</span>
            </button>

            <button
              type="button"
              @click="form.type = 'out'; form.category = 'setor_bank'"
              :class="[
                form.type === 'out' 
                  ? 'bg-rose-500/15 border-rose-500 text-rose-600 dark:text-rose-400 font-bold ring-2 ring-rose-500/20' 
                  : 'bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-500',
                'p-4 rounded-xl border flex items-center justify-center gap-3 transition-all'
              ]"
            >
              <ArrowUpRight class="w-5 h-5" />
              <span>KAS KELUAR (OUTFLOW)</span>
            </button>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
              No. Bukti Kas <span class="text-rose-500">*</span>
            </label>
            <input
              v-model="form.transaction_number"
              type="text"
              required
              class="w-full px-3.5 py-2 text-sm rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white font-mono focus:ring-2 focus:ring-amber-500/30"
            />
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
              Tanggal Transaksi <span class="text-rose-500">*</span>
            </label>
            <input
              v-model="form.transaction_date"
              type="date"
              required
              class="w-full px-3.5 py-2 text-sm rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500/30"
            />
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
              Kategori Transaksi <span class="text-rose-500">*</span>
            </label>
            <select
              v-model="form.category"
              required
              class="w-full px-3.5 py-2 text-sm rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500/30"
            >
              <option v-for="cat in categories[form.type]" :key="cat.value" :value="cat.value">
                {{ cat.label }}
              </option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
              Akun / Sumber Dana <span class="text-rose-500">*</span>
            </label>
            <select
              v-model="form.bank_account_id"
              class="w-full px-3.5 py-2 text-sm rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500/30"
            >
              <option value="">💵 Kasir Tunai Toko (Cash Drawer)</option>
              <option v-for="b in bankAccounts" :key="b.id" :value="b.id">
                🏦 {{ b.bank_name }} - {{ b.account_number }} (a.n {{ b.account_holder }})
              </option>
            </select>
          </div>

          <div class="md:col-span-2">
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
              Nominal Dana (IDR) <span class="text-rose-500">*</span>
            </label>
            <input
              v-model="form.amount"
              type="number"
              step="1000"
              required
              placeholder="Contoh: 1500000"
              class="w-full px-3.5 py-2.5 text-base rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white font-mono font-bold focus:ring-2 focus:ring-amber-500/30"
            />
          </div>

          <div class="md:col-span-2">
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
              Keterangan & Deskripsi Transaksi <span class="text-rose-500">*</span>
            </label>
            <input
              v-model="form.description"
              type="text"
              required
              placeholder="Contoh: Setoran uang tunai kasir POS 1 ke rekening BCA operasional toko"
              class="w-full px-3.5 py-2 text-sm rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500/30"
            />
          </div>

          <div class="md:col-span-2">
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
              Catatan Tambahan
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
            :href="route('admin.cash-book.index')"
            class="px-5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-sm font-semibold hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
          >
            Batal
          </Link>
          <button
            type="submit"
            :disabled="form.processing"
            class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 disabled:opacity-50 text-slate-950 font-bold text-sm shadow-md shadow-amber-500/20 transition-all cursor-pointer"
          >
            <Save class="w-4 h-4" />
            <span>Simpan Transaksi Kas</span>
          </button>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>
