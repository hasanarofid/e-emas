<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { 
  Landmark, 
  Plus, 
  Search, 
  Edit3, 
  Trash2, 
  X, 
  Check, 
  Sparkles,
  CreditCard,
  QrCode,
  Copy,
  CheckCircle2,
  Building2
} from 'lucide-vue-next';

const props = defineProps({
  bankAccounts: {
    type: Object,
    required: true,
  },
  filters: {
    type: Object,
    default: () => ({}),
  },
});

const search = ref(props.filters.search || '');
const isModalOpen = ref(false);
const isEditing = ref(false);
const editingAccountId = ref(null);
const copiedId = ref(null);

const form = useForm({
  bank_name: '',
  account_number: '',
  account_holder: '',
  notes: '',
  is_active: true,
});

const popularBanks = ['BCA', 'Bank Mandiri', 'BRI', 'BNI', 'BSI (Bank Syariah)', 'QRIS Merchant', 'CIMB Niaga'];

const handleSearch = () => {
  router.get(route('admin.bank-accounts.index'), { search: search.value }, { preserveState: true, replace: true });
};

const openCreateModal = () => {
  isEditing.value = false;
  editingAccountId.value = null;
  form.reset();
  form.is_active = true;
  isModalOpen.value = true;
};

const openEditModal = (account) => {
  isEditing.value = true;
  editingAccountId.value = account.id;
  form.bank_name = account.bank_name;
  form.account_number = account.account_number;
  form.account_holder = account.account_holder;
  form.notes = account.notes || '';
  form.is_active = Boolean(account.is_active);
  isModalOpen.value = true;
};

const closeModal = () => {
  isModalOpen.value = false;
  form.reset();
};

const submitForm = () => {
  if (isEditing.value) {
    form.put(route('admin.bank-accounts.update', editingAccountId.value), {
      onSuccess: () => closeModal(),
    });
  } else {
    form.post(route('admin.bank-accounts.store'), {
      onSuccess: () => closeModal(),
    });
  }
};

const deleteAccount = (account) => {
  if (confirm(`Hapus rekening "${account.bank_name} - ${account.account_number}"?`)) {
    router.delete(route('admin.bank-accounts.destroy', account.id));
  }
};

const copyAccountNumber = (account) => {
  navigator.clipboard.writeText(account.account_number);
  copiedId.value = account.id;
  setTimeout(() => {
    copiedId.value = null;
  }, 2000);
};

const getBankBadgeColor = (name) => {
  const upper = (name || '').toUpperCase();
  if (upper.includes('BCA')) return 'bg-blue-500/20 text-blue-400 border-blue-500/30';
  if (upper.includes('MANDIRI')) return 'bg-amber-500/20 text-amber-400 border-amber-500/30';
  if (upper.includes('BRI')) return 'bg-sky-500/20 text-sky-400 border-sky-500/30';
  if (upper.includes('BNI')) return 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30';
  if (upper.includes('QRIS')) return 'bg-rose-500/20 text-rose-400 border-rose-500/30';
  if (upper.includes('BSI')) return 'bg-teal-500/20 text-teal-400 border-teal-500/30';
  return 'bg-purple-500/20 text-purple-400 border-purple-500/30';
};
</script>

<template>
  <AdminLayout>
    <Head title="Rekening Bank & Transfer - POS Emas" />

    <div class="space-y-6">
      <!-- Header Section -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2 text-xs font-mono text-amber-500 uppercase tracking-widest">
            <Landmark class="w-4 h-4" />
            <span>Utilitas & Pengaturan</span>
          </div>
          <h2 class="text-2xl font-bold tracking-tight text-white mt-1">
            Bank Transfer & Pembayaran Non-Tunai
          </h2>
          <p class="text-sm text-slate-400">
            Kelola daftar rekening bank operasional toko dan QRIS untuk transaksi pembayaran di kasir.
          </p>
        </div>

        <button
          @click="openCreateModal"
          class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 font-semibold text-sm shadow-lg shadow-amber-500/20 transition-all cursor-pointer"
        >
          <Plus class="w-4 h-4" />
          <span>Tambah Rekening Baru</span>
        </button>
      </div>

      <!-- Quick Summary Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-4 rounded-2xl bg-[#1E1E38] border border-slate-800 flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400">
            <Landmark class="w-6 h-6" />
          </div>
          <div>
            <p class="text-xs font-medium text-slate-400">Total Rekening Toko</p>
            <p class="text-xl font-bold text-white mt-0.5">{{ bankAccounts.total || bankAccounts.data.length }} Rekening</p>
          </div>
        </div>

        <div class="p-4 rounded-2xl bg-[#1E1E38] border border-slate-800 flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400">
            <CheckCircle2 class="w-6 h-6" />
          </div>
          <div>
            <p class="text-xs font-medium text-slate-400">Rekening Aktif</p>
            <p class="text-xl font-bold text-emerald-400 mt-0.5">
              {{ bankAccounts.data.filter(a => a.is_active).length }} Siap Digunakan
            </p>
          </div>
        </div>

        <div class="p-4 rounded-2xl bg-[#1E1E38] border border-slate-800 flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400">
            <QrCode class="w-6 h-6" />
          </div>
          <div>
            <p class="text-xs font-medium text-slate-400">Metode Non-Tunai</p>
            <p class="text-xl font-bold text-white mt-0.5">Transfer & QRIS</p>
          </div>
        </div>
      </div>

      <!-- Table Section -->
      <div class="rounded-2xl bg-[#1E1E38] border border-slate-800 overflow-hidden shadow-xl">
        <!-- Search Bar -->
        <div class="p-4 border-b border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
          <div class="relative flex-1 max-w-md">
            <Search class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" />
            <input
              v-model="search"
              @keyup.enter="handleSearch"
              type="text"
              placeholder="Cari nama bank, nomor rekening, atau atas nama..."
              class="w-full pl-10 pr-4 py-2 rounded-xl bg-[#151528] border border-slate-700/80 text-white placeholder-slate-400 text-sm focus:outline-none focus:border-amber-500"
            />
          </div>
          <button
            @click="handleSearch"
            class="px-4 py-2 rounded-xl bg-[#25254B] hover:bg-[#323265] text-slate-200 text-sm font-medium transition-colors cursor-pointer"
          >
            Filter
          </button>
        </div>

        <!-- Accounts Table -->
        <div class="overflow-x-auto">
          <table class="w-full text-left text-sm text-slate-300">
            <thead class="bg-[#151528] text-xs uppercase text-slate-400 font-semibold border-b border-slate-800">
              <tr>
                <th class="px-6 py-4">Nama Bank / Merchant</th>
                <th class="px-6 py-4">Nomor Rekening</th>
                <th class="px-6 py-4">Atas Nama (Pemilik)</th>
                <th class="px-6 py-4">Catatan</th>
                <th class="px-6 py-4 text-center">Status</th>
                <th class="px-6 py-4 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/80">
              <tr v-if="bankAccounts.data.length === 0">
                <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                  <div class="flex flex-col items-center justify-center gap-2">
                    <Building2 class="w-10 h-10 text-slate-500" />
                    <p class="font-medium text-slate-300">Belum ada data rekening bank</p>
                    <p class="text-xs text-slate-500">Klik "Tambah Rekening Baru" untuk menambahkan rekening toko.</p>
                  </div>
                </td>
              </tr>
              <tr 
                v-for="account in bankAccounts.data" 
                :key="account.id"
                class="hover:bg-[#25254B]/40 transition-colors"
              >
                <td class="px-6 py-4">
                  <div class="flex items-center gap-3">
                    <span 
                      class="px-2.5 py-1 rounded-lg text-xs font-bold border"
                      :class="getBankBadgeColor(account.bank_name)"
                    >
                      {{ account.bank_name }}
                    </span>
                  </div>
                </td>
                <td class="px-6 py-4 font-mono font-medium text-white">
                  <div class="flex items-center gap-2">
                    <span>{{ account.account_number }}</span>
                    <button
                      @click="copyAccountNumber(account)"
                      title="Salin nomor rekening"
                      class="p-1 rounded hover:bg-slate-700/50 text-slate-400 hover:text-amber-400 transition-colors"
                    >
                      <Check v-if="copiedId === account.id" class="w-3.5 h-3.5 text-emerald-400" />
                      <Copy v-else class="w-3.5 h-3.5" />
                    </button>
                  </div>
                </td>
                <td class="px-6 py-4 font-medium text-slate-200">
                  {{ account.account_holder }}
                </td>
                <td class="px-6 py-4 text-xs text-slate-400">
                  {{ account.notes || '-' }}
                </td>
                <td class="px-6 py-4 text-center">
                  <span 
                    v-if="account.is_active" 
                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20"
                  >
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    Aktif
                  </span>
                  <span 
                    v-else 
                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-500/10 text-rose-400 border border-rose-500/20"
                  >
                    Non-Aktif
                  </span>
                </td>
                <td class="px-6 py-4 text-right">
                  <div class="flex items-center justify-end gap-2">
                    <button
                      @click="openEditModal(account)"
                      class="p-2 rounded-lg bg-[#25254B] hover:bg-amber-500/20 hover:text-amber-400 text-slate-300 transition-colors"
                      title="Edit Rekening"
                    >
                      <Edit3 class="w-4 h-4" />
                    </button>
                    <button
                      @click="deleteAccount(account)"
                      class="p-2 rounded-lg bg-[#25254B] hover:bg-rose-500/20 hover:text-rose-400 text-slate-300 transition-colors"
                      title="Hapus Rekening"
                    >
                      <Trash2 class="w-4 h-4" />
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="bankAccounts.links && bankAccounts.links.length > 3" class="p-4 border-t border-slate-800 flex items-center justify-between">
          <p class="text-xs text-slate-400">
            Menampilkan {{ bankAccounts.from || 0 }} - {{ bankAccounts.to || 0 }} dari {{ bankAccounts.total || 0 }} data
          </p>
          <div class="flex items-center gap-1">
            <button
              v-for="(link, i) in bankAccounts.links"
              :key="i"
              :disabled="!link.url || link.active"
              @click="router.get(link.url, {}, { preserveState: true })"
              v-html="link.label"
              class="px-3 py-1.5 rounded-lg text-xs font-medium transition-colors"
              :class="[
                link.active 
                  ? 'bg-amber-500 text-slate-950 font-bold' 
                  : link.url 
                    ? 'bg-[#25254B] text-slate-300 hover:bg-[#323265] hover:text-white' 
                    : 'opacity-40 bg-[#151528] text-slate-500 cursor-not-allowed'
              ]"
            ></button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Form Tambah / Edit -->
    <div 
      v-if="isModalOpen" 
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm"
    >
      <div class="w-full max-w-lg rounded-2xl bg-[#1E1E38] border border-slate-700 shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
        <div class="p-5 border-b border-slate-800 flex items-center justify-between">
          <div class="flex items-center gap-2.5">
            <div class="w-9 h-9 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400">
              <Landmark class="w-5 h-5" />
            </div>
            <div>
              <h3 class="text-base font-bold text-white">
                {{ isEditing ? 'Edit Rekening Bank' : 'Tambah Rekening Toko Baru' }}
              </h3>
              <p class="text-xs text-slate-400">
                Masukkan detail rekening bank resmi toko emas.
              </p>
            </div>
          </div>
          <button 
            @click="closeModal" 
            class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-[#25254B] transition-colors"
          >
            <X class="w-5 h-5" />
          </button>
        </div>

        <form @submit.prevent="submitForm" class="p-5 space-y-4">
          <!-- Nama Bank & Rekomendasi -->
          <div>
            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
              Nama Bank / Saluran Pembayaran <span class="text-rose-500">*</span>
            </label>
            <input 
              v-model="form.bank_name" 
              type="text" 
              placeholder="Contoh: BCA / Bank Mandiri / QRIS" 
              class="w-full px-3.5 py-2.5 rounded-xl bg-[#151528] border border-slate-700 text-white placeholder-slate-500 text-sm focus:outline-none focus:border-amber-500"
              required
            />
            <div class="flex flex-wrap gap-1.5 mt-2">
              <button
                v-for="b in popularBanks"
                :key="b"
                type="button"
                @click="form.bank_name = b"
                class="px-2 py-0.5 rounded text-[11px] font-medium bg-[#25254B] hover:bg-amber-500/20 hover:text-amber-300 text-slate-300 border border-slate-700 transition-colors"
              >
                {{ b }}
              </button>
            </div>
            <p v-if="form.errors.bank_name" class="text-xs text-rose-400 mt-1">{{ form.errors.bank_name }}</p>
          </div>

          <!-- Nomor Rekening -->
          <div>
            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
              Nomor Rekening / ID Merchant <span class="text-rose-500">*</span>
            </label>
            <input 
              v-model="form.account_number" 
              type="text" 
              placeholder="Contoh: 1234567890" 
              class="w-full px-3.5 py-2.5 rounded-xl bg-[#151528] border border-slate-700 text-white placeholder-slate-500 text-sm font-mono focus:outline-none focus:border-amber-500"
              required
            />
            <p v-if="form.errors.account_number" class="text-xs text-rose-400 mt-1">{{ form.errors.account_number }}</p>
          </div>

          <!-- Atas Nama -->
          <div>
            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
              Atas Nama Pemilik Rekening <span class="text-rose-500">*</span>
            </label>
            <input 
              v-model="form.account_holder" 
              type="text" 
              placeholder="Contoh: Toko Mas Permata Abadi / John Doe" 
              class="w-full px-3.5 py-2.5 rounded-xl bg-[#151528] border border-slate-700 text-white placeholder-slate-500 text-sm focus:outline-none focus:border-amber-500"
              required
            />
            <p v-if="form.errors.account_holder" class="text-xs text-rose-400 mt-1">{{ form.errors.account_holder }}</p>
          </div>

          <!-- Catatan / Keterangan -->
          <div>
            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
              Catatan / Info Tambahan (Opsional)
            </label>
            <input 
              v-model="form.notes" 
              type="text" 
              placeholder="Contoh: Khusus transfer cabang Malioboro" 
              class="w-full px-3.5 py-2.5 rounded-xl bg-[#151528] border border-slate-700 text-white placeholder-slate-500 text-sm focus:outline-none focus:border-amber-500"
            />
            <p v-if="form.errors.notes" class="text-xs text-rose-400 mt-1">{{ form.errors.notes }}</p>
          </div>

          <!-- Switch Status Aktif -->
          <div class="pt-2 flex items-center justify-between">
            <div>
              <p class="text-sm font-semibold text-white">Status Rekening Aktif</p>
              <p class="text-xs text-slate-400">Jika aktif, rekening ini akan tampil sebagai opsi di Kasir POS.</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
              <input 
                type="checkbox" 
                v-model="form.is_active" 
                class="sr-only peer"
              />
              <div class="w-11 h-6 bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-500"></div>
            </label>
          </div>

          <!-- Modal Actions -->
          <div class="pt-4 border-t border-slate-800 flex items-center justify-end gap-3">
            <button 
              type="button" 
              @click="closeModal" 
              class="px-4 py-2.5 rounded-xl bg-[#25254B] hover:bg-[#323265] text-slate-300 text-sm font-medium transition-colors"
            >
              Batal
            </button>
            <button 
              type="submit" 
              :disabled="form.processing"
              class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 font-semibold text-sm shadow-lg shadow-amber-500/20 transition-all disabled:opacity-50 cursor-pointer"
            >
              <Check class="w-4 h-4" />
              <span>{{ isEditing ? 'Simpan Perubahan' : 'Tambahkan Rekening' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>
