<script setup>
import { ref, computed } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
  Building2, Plus, Search, Edit2, Trash2, Phone, Mail, 
  MapPin, CreditCard, CheckCircle, XCircle, X, AlertCircle
} from 'lucide-vue-next';

const props = defineProps({
  suppliers: Object,
  filters: Object,
});

const search = ref(props.filters.search || '');
const isModalOpen = ref(false);
const isEditing = ref(false);
const editingId = ref(null);

const form = useForm({
  code: '',
  name: '',
  company_name: '',
  phone: '',
  telephone: '',
  email: '',
  city: '',
  address: '',
  bank_name: '',
  bank_account_number: '',
  bank_account_holder: '',
  notes: '',
  is_active: true,
});

const handleSearch = () => {
  router.get(route('admin.suppliers.index'), { search: search.value }, { preserveState: true, replace: true });
};

const openCreateModal = () => {
  isEditing.value = false;
  editingId.value = null;
  form.reset();
  form.code = 'SPL-' + Math.floor(1000 + Math.random() * 9000);
  form.is_active = true;
  isModalOpen.value = true;
};

const openEditModal = (supplier) => {
  isEditing.value = true;
  editingId.value = supplier.id;
  form.code = supplier.code;
  form.name = supplier.name;
  form.company_name = supplier.company_name || '';
  form.phone = supplier.phone || '';
  form.telephone = supplier.telephone || '';
  form.email = supplier.email || '';
  form.city = supplier.city || '';
  form.address = supplier.address || '';
  form.bank_name = supplier.bank_name || '';
  form.bank_account_number = supplier.bank_account_number || '';
  form.bank_account_holder = supplier.bank_account_holder || '';
  form.notes = supplier.notes || '';
  form.is_active = Boolean(supplier.is_active);
  isModalOpen.value = true;
};

const submitForm = () => {
  if (isEditing.value) {
    form.put(route('admin.suppliers.update', editingId.value), {
      onSuccess: () => {
        isModalOpen.value = false;
        form.reset();
      },
    });
  } else {
    form.post(route('admin.suppliers.store'), {
      onSuccess: () => {
        isModalOpen.value = false;
        form.reset();
      },
    });
  }
};

const deleteSupplier = (supplier) => {
  if (confirm(`Apakah Anda yakin ingin menghapus data supplier "${supplier.name}"? Data akan diarsipkan secara aman (Soft Delete).`)) {
    router.delete(route('admin.suppliers.destroy', supplier.id));
  }
};
</script>

<template>
  <AdminLayout title="Supplier & Pabrik Emas">
    <Head title="Supplier & Pabrik Emas - POS Emas" />

    <div class="space-y-6">
      <!-- Header -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <h1 class="text-2xl font-bold text-white tracking-wide flex items-center gap-2">
            <Building2 class="w-7 h-7 text-amber-400" />
            Supplier & Pabrik Emas
          </h1>
          <p class="text-slate-400 text-sm mt-1">
            Kelola data mitra pengrajin, distributor, dan pabrik perhiasan emas
          </p>
        </div>
        <button
          @click="openCreateModal"
          class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 font-semibold rounded-xl shadow-lg shadow-amber-500/20 transition-all duration-200"
        >
          <Plus class="w-5 h-5" />
          <span>Tambah Supplier Baru</span>
        </button>
      </div>

      <!-- Filter Bar -->
      <div class="bg-[#181834] border border-[#25254B] rounded-2xl p-4 flex flex-col md:flex-row gap-4 items-center justify-between shadow-xl">
        <div class="relative w-full md:w-96">
          <Search class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
          <input
            v-model="search"
            @keyup.enter="handleSearch"
            type="text"
            placeholder="Cari nama, PT/CV, kode, kota, telp..."
            class="w-full pl-10 pr-4 py-2 bg-[#0F0F23] border border-[#25254B] rounded-xl text-white text-sm focus:outline-none focus:border-amber-400/50 transition-colors"
          />
        </div>
        <div class="text-xs text-slate-400">
          Total: <span class="text-amber-400 font-semibold">{{ suppliers.total }} Supplier</span> terdaftar
        </div>
      </div>

      <!-- Data Table -->
      <div class="bg-[#181834] border border-[#25254B] rounded-2xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse text-sm">
            <thead>
              <tr class="border-b border-[#25254B] bg-[#1F1F41]/60 text-slate-300 font-medium">
                <th class="py-3.5 px-4">Kode & Nama Supplier</th>
                <th class="py-3.5 px-4">Kontak & Kota</th>
                <th class="py-3.5 px-4">Rekening Bank</th>
                <th class="py-3.5 px-4 text-center">Faktur</th>
                <th class="py-3.5 px-4 text-center">Status</th>
                <th class="py-3.5 px-4 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-[#25254B]">
              <tr 
                v-for="item in suppliers.data" 
                :key="item.id"
                class="hover:bg-[#1F1F41]/30 transition-colors text-slate-300"
              >
                <td class="py-3.5 px-4">
                  <div class="font-semibold text-white">{{ item.name }}</div>
                  <div class="text-xs text-amber-400/80 font-mono">{{ item.code }}</div>
                  <div v-if="item.company_name" class="text-xs text-slate-400 mt-0.5">{{ item.company_name }}</div>
                </td>
                <td class="py-3.5 px-4">
                  <div class="flex items-center gap-1.5 text-xs text-slate-300">
                    <Phone class="w-3.5 h-3.5 text-slate-400" />
                    <span>{{ item.phone || item.telephone || '-' }}</span>
                  </div>
                  <div v-if="item.city" class="flex items-center gap-1.5 text-xs text-slate-400 mt-1">
                    <MapPin class="w-3.5 h-3.5 text-slate-400" />
                    <span>{{ item.city }}</span>
                  </div>
                </td>
                <td class="py-3.5 px-4">
                  <div v-if="item.bank_name" class="text-xs">
                    <span class="font-semibold text-white">{{ item.bank_name }}:</span> 
                    <span class="text-slate-300 font-mono ml-1">{{ item.bank_account_number }}</span>
                    <div class="text-slate-400 text-[11px]">{{ item.bank_account_holder }}</div>
                  </div>
                  <span v-else class="text-xs text-slate-500 italic">Belum ada rekening</span>
                </td>
                <td class="py-3.5 px-4 text-center font-semibold text-slate-200">
                  <span class="px-2.5 py-1 rounded-full bg-slate-800 border border-slate-700 text-xs">
                    {{ item.purchases_count || 0 }} PB
                  </span>
                </td>
                <td class="py-3.5 px-4 text-center">
                  <span 
                    :class="item.is_active ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' : 'bg-red-500/10 text-red-400 border-red-500/20'"
                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs border font-medium"
                  >
                    <component :is="item.is_active ? CheckCircle : XCircle" class="w-3 h-3" />
                    {{ item.is_active ? 'Aktif' : 'Nonaktif' }}
                  </span>
                </td>
                <td class="py-3.5 px-4 text-right">
                  <div class="inline-flex items-center gap-2">
                    <button
                      @click="openEditModal(item)"
                      class="p-1.5 text-slate-400 hover:text-amber-400 hover:bg-[#25254B] rounded-lg transition-colors"
                      title="Edit Supplier"
                    >
                      <Edit2 class="w-4 h-4" />
                    </button>
                    <button
                      @click="deleteSupplier(item)"
                      class="p-1.5 text-slate-400 hover:text-red-400 hover:bg-[#25254B] rounded-lg transition-colors"
                      title="Hapus Supplier (Soft Delete)"
                    >
                      <Trash2 class="w-4 h-4" />
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="suppliers.data.length === 0">
                <td colspan="6" class="py-12 text-center text-slate-500">
                  Belum ada data supplier emas. Klik tombol "Tambah Supplier Baru" di atas.
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="suppliers.links && suppliers.links.length > 3" class="p-4 border-t border-[#25254B] flex items-center justify-between text-xs text-slate-400">
          <div>Menampilkan {{ suppliers.from }} - {{ suppliers.to }} dari {{ suppliers.total }} data</div>
          <div class="flex gap-1">
            <template v-for="(link, i) in suppliers.links" :key="i">
              <button
                v-if="link.url"
                @click="router.get(link.url)"
                v-html="link.label"
                :class="link.active ? 'bg-amber-500 text-slate-950 font-bold' : 'bg-[#1F1F41] text-slate-300 hover:bg-[#25254B]'"
                class="px-3 py-1.5 rounded-lg border border-[#25254B]"
              />
              <span v-else v-html="link.label" class="px-3 py-1.5 rounded-lg text-slate-600 cursor-not-allowed border border-[#25254B]/40" />
            </template>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Form Tambah / Edit -->
    <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/75 backdrop-blur-sm">
      <div class="bg-[#181834] border border-[#25254B] rounded-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto shadow-2xl p-6">
        <div class="flex items-center justify-between pb-4 border-b border-[#25254B]">
          <h3 class="text-lg font-bold text-white flex items-center gap-2">
            <Building2 class="w-5 h-5 text-amber-400" />
            {{ isEditing ? 'Edit Data Supplier' : 'Tambah Supplier Baru' }}
          </h3>
          <button @click="isModalOpen = false" class="text-slate-400 hover:text-white p-1 rounded-lg">
            <X class="w-5 h-5" />
          </button>
        </div>

        <form @submit.prevent="submitForm" class="mt-4 space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-semibold text-slate-300 mb-1">Kode Supplier *</label>
              <input
                v-model="form.code"
                type="text"
                required
                class="w-full px-3 py-2 bg-[#0F0F23] border border-[#25254B] rounded-xl text-white text-sm focus:border-amber-400"
              />
              <span v-if="form.errors.code" class="text-xs text-red-400 mt-1 block">{{ form.errors.code }}</span>
            </div>
            <div>
              <label class="block text-xs font-semibold text-slate-300 mb-1">Nama Supplier / Pengrajin *</label>
              <input
                v-model="form.name"
                type="text"
                required
                placeholder="Contoh: PT UBS Gold, CV Mulia Abadi"
                class="w-full px-3 py-2 bg-[#0F0F23] border border-[#25254B] rounded-xl text-white text-sm focus:border-amber-400"
              />
              <span v-if="form.errors.name" class="text-xs text-red-400 mt-1 block">{{ form.errors.name }}</span>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-semibold text-slate-300 mb-1">Nama PT / Badan Usaha</label>
              <input
                v-model="form.company_name"
                type="text"
                placeholder="Opsional"
                class="w-full px-3 py-2 bg-[#0F0F23] border border-[#25254B] rounded-xl text-white text-sm focus:border-amber-400"
              />
            </div>
            <div>
              <label class="block text-xs font-semibold text-slate-300 mb-1">Kota / Domisili</label>
              <input
                v-model="form.city"
                type="text"
                placeholder="Contoh: Surabaya, Jakarta"
                class="w-full px-3 py-2 bg-[#0F0F23] border border-[#25254B] rounded-xl text-white text-sm focus:border-amber-400"
              />
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-semibold text-slate-300 mb-1">No. HP / WhatsApp</label>
              <input
                v-model="form.phone"
                type="text"
                placeholder="0812xxxxxxxx"
                class="w-full px-3 py-2 bg-[#0F0F23] border border-[#25254B] rounded-xl text-white text-sm focus:border-amber-400"
              />
            </div>
            <div>
              <label class="block text-xs font-semibold text-slate-300 mb-1">Email</label>
              <input
                v-model="form.email"
                type="email"
                placeholder="supplier@email.com"
                class="w-full px-3 py-2 bg-[#0F0F23] border border-[#25254B] rounded-xl text-white text-sm focus:border-amber-400"
              />
            </div>
          </div>

          <!-- Informasi Perbankan -->
          <div class="p-3.5 bg-[#0F0F23] border border-[#25254B] rounded-xl space-y-3">
            <div class="text-xs font-bold text-amber-400 uppercase tracking-wider flex items-center gap-1.5">
              <CreditCard class="w-3.5 h-3.5" />
              Rekening Pembayaran / Transfer
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
              <div>
                <label class="block text-[11px] text-slate-400 mb-1">Nama Bank</label>
                <input
                  v-model="form.bank_name"
                  type="text"
                  placeholder="BCA / Mandiri / BRI"
                  class="w-full px-3 py-1.5 bg-[#181834] border border-[#25254B] rounded-lg text-white text-xs focus:border-amber-400"
                />
              </div>
              <div>
                <label class="block text-[11px] text-slate-400 mb-1">No. Rekening</label>
                <input
                  v-model="form.bank_account_number"
                  type="text"
                  placeholder="1234567890"
                  class="w-full px-3 py-1.5 bg-[#181834] border border-[#25254B] rounded-lg text-white text-xs focus:border-amber-400 font-mono"
                />
              </div>
              <div>
                <label class="block text-[11px] text-slate-400 mb-1">Atas Nama (A/N)</label>
                <input
                  v-model="form.bank_account_holder"
                  type="text"
                  placeholder="Nama pemilik rekening"
                  class="w-full px-3 py-1.5 bg-[#181834] border border-[#25254B] rounded-lg text-white text-xs focus:border-amber-400"
                />
              </div>
            </div>
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-300 mb-1">Alamat Lengkap</label>
            <textarea
              v-model="form.address"
              rows="2"
              class="w-full px-3 py-2 bg-[#0F0F23] border border-[#25254B] rounded-xl text-white text-sm focus:border-amber-400"
              placeholder="Alamat kantor / gudang supplier..."
            ></textarea>
          </div>

          <div class="flex items-center gap-2">
            <input
              v-model="form.is_active"
              type="checkbox"
              id="is_active"
              class="w-4 h-4 rounded bg-[#0F0F23] border-[#25254B] text-amber-500 focus:ring-amber-400"
            />
            <label for="is_active" class="text-xs text-slate-300 font-medium">Status Aktif (dapat dipilih di faktur pembelian)</label>
          </div>

          <div class="flex items-center justify-end gap-3 pt-4 border-t border-[#25254B]">
            <button
              type="button"
              @click="isModalOpen = false"
              class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-sm"
            >
              Batal
            </button>
            <button
              type="submit"
              :disabled="form.processing"
              class="px-5 py-2 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 font-semibold rounded-xl text-sm shadow-lg shadow-amber-500/20 disabled:opacity-50"
            >
              {{ form.processing ? 'Menyimpan...' : (isEditing ? 'Perbarui Supplier' : 'Simpan Supplier') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>
