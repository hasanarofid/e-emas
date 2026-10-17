<script setup>
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
  Percent, Plus, Save, Trash2, Check, AlertCircle, 
  HelpCircle, ArrowLeft, RefreshCw
} from 'lucide-vue-next';

const props = defineProps({
  deductions: Array,
  categories: Array,
  goldRates: Array,
});

// Duplikasi data deductions ke state reaktif lokal agar bisa diedit langsung per baris
const rows = ref(props.deductions.map(item => ({
  id: item.id,
  condition_status: item.condition_status,
  category_id: item.category_id || '',
  gold_rate_id: item.gold_rate_id || '',
  deduction_amount: Number(item.deduction_amount) || 0,
  deduction_type: item.deduction_type || 'percentage',
  deduct_by_weight: Boolean(item.deduct_by_weight),
  isNew: false,
  saving: false,
})));

// Tambah baris baru di awal tabel
const addNewRow = () => {
  rows.value.unshift({
    id: null,
    condition_status: '',
    category_id: '',
    gold_rate_id: '',
    deduction_amount: 0,
    deduction_type: 'percentage',
    deduct_by_weight: false,
    isNew: true,
    saving: false,
  });
};

// Simpan per baris
const saveRow = (row) => {
  if (!row.condition_status.trim()) {
    alert('Status Barang wajib diisi!');
    return;
  }

  row.saving = true;

  const payload = {
    condition_status: row.condition_status,
    category_id: row.category_id ? row.category_id : null,
    gold_rate_id: row.gold_rate_id ? row.gold_rate_id : null,
    deduction_amount: parseFloat(row.deduction_amount) || 0,
    deduction_type: row.deduction_type,
    deduct_by_weight: Boolean(row.deduct_by_weight),
  };

  if (row.id) {
    // Update existing
    router.put(route('admin.buyback-deductions.update', row.id), payload, {
      preserveScroll: true,
      onFinish: () => {
        row.saving = false;
      },
    });
  } else {
    // Store new
    router.post(route('admin.buyback-deductions.store'), payload, {
      preserveScroll: true,
      onSuccess: () => {
        row.isNew = false;
      },
      onFinish: () => {
        row.saving = false;
      },
    });
  }
};

// Hapus baris
const deleteRow = (row, index) => {
  if (row.isNew) {
    rows.value.splice(index, 1);
    return;
  }

  if (confirm(`Apakah Anda yakin ingin menghapus aturan potongan "${row.condition_status}"? Data akan diarsipkan aman (Soft Delete).`)) {
    router.delete(route('admin.buyback-deductions.destroy', row.id), {
      preserveScroll: true,
      onSuccess: () => {
        rows.value.splice(index, 1);
      },
    });
  }
};
</script>

<template>
  <AdminLayout title="Potongan Master Buyback">
    <Head title="Potongan Master Buyback - POS Emas" />

    <div class="space-y-6 max-w-7xl mx-auto">
      <!-- Breadcrumb & Judul Halaman -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2 text-xs text-amber-400 font-semibold mb-1">
            <span>Dashboard</span>
            <span>&gt;</span>
            <span class="text-slate-300">Potongan Master Buyback</span>
          </div>
          <h1 class="text-2xl font-bold text-white tracking-wide flex items-center gap-2.5">
            <span class="p-2 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400">
              <Percent class="w-6 h-6" />
            </span>
            POTONGAN MASTER BUYBACK
          </h1>
          <p class="text-slate-400 text-xs mt-1">
            Pengaturan parameter susut dan potongan harga beli kembali emas pelanggan mengacu standar Sitokmas
          </p>
        </div>
      </div>

      <!-- Main Box Container -->
      <div class="bg-[#181834] border border-[#25254B] rounded-2xl shadow-2xl overflow-hidden">
        <!-- Box Header -->
        <div class="px-6 py-4 bg-[#1F1F41]/60 border-b border-[#25254B] flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
          <div>
            <h2 class="text-sm font-bold text-white tracking-wide">
              Data Master Potongan Harga Beli (Buy Back)
            </h2>
            <p class="text-[11px] text-slate-400 mt-0.5">
              Aturan ini otomatis diterapkan saat kasir memproses transaksi Beli Kembali (Buyback) & Tukar Tambah
            </p>
          </div>
          <button
            @click="addNewRow"
            class="inline-flex items-center gap-1.5 px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-500 hover:to-blue-600 text-white font-semibold rounded-xl text-xs shadow-lg shadow-blue-500/20 transition-all duration-200"
          >
            <Plus class="w-4 h-4" />
            <span>Tambah Data</span>
          </button>
        </div>

        <!-- Table Form Multi-Row Sitokmas Edition -->
        <div class="overflow-x-auto p-4">
          <table class="w-full text-left border-collapse text-xs">
            <thead>
              <tr class="border-b border-[#25254B] bg-[#14142B] text-slate-300 font-semibold">
                <th class="py-3 px-3 min-w-[180px]">Status Barang</th>
                <th class="py-3 px-3 min-w-[160px]">Kategori Barang</th>
                <th class="py-3 px-3 min-w-[150px]">Kadar Barang</th>
                <th class="py-3 px-3 min-w-[210px]">Potongan Harga</th>
                <th class="py-3 px-3 min-w-[140px] text-center">Potong Sesuai Berat</th>
                <th class="py-3 px-3 min-w-[140px] text-center">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-[#25254B]/60">
              <tr 
                v-for="(row, idx) in rows" 
                :key="row.id || ('new-' + idx)"
                class="hover:bg-[#1F1F41]/25 transition-colors"
              >
                <!-- 1. Status Barang -->
                <td class="py-2.5 px-3">
                  <input
                    v-model="row.condition_status"
                    type="text"
                    required
                    placeholder="Contoh: Siap Jual, Rusak Total"
                    class="w-full px-3 py-1.5 bg-[#0F0F23] border border-[#2E2E56] rounded-lg text-white text-xs focus:border-amber-400 transition-colors"
                  />
                </td>

                <!-- 2. Kategori Barang -->
                <td class="py-2.5 px-3">
                  <select
                    v-model="row.category_id"
                    class="w-full px-2.5 py-1.5 bg-[#0F0F23] border border-[#2E2E56] rounded-lg text-white text-xs focus:border-amber-400"
                  >
                    <option value="">Semua Kategori</option>
                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                      {{ cat.name }}
                    </option>
                  </select>
                </td>

                <!-- 3. Kadar Barang -->
                <td class="py-2.5 px-3">
                  <select
                    v-model="row.gold_rate_id"
                    class="w-full px-2.5 py-1.5 bg-[#0F0F23] border border-[#2E2E56] rounded-lg text-white text-xs focus:border-amber-400"
                  >
                    <option value="">Semua Kadar</option>
                    <option v-for="rate in goldRates" :key="rate.id" :value="rate.id">
                      {{ rate.name }} ({{ rate.percentage }}%)
                    </option>
                  </select>
                </td>

                <!-- 4. Potongan Harga (Nominal / Persen) -->
                <td class="py-2.5 px-3">
                  <div class="flex items-center gap-1.5">
                    <input
                      v-model.number="row.deduction_amount"
                      type="number"
                      step="any"
                      min="0"
                      class="w-full px-2.5 py-1.5 bg-[#0F0F23] border border-[#2E2E56] rounded-lg text-amber-400 font-mono font-bold text-right text-xs focus:border-amber-400"
                    />
                    <select
                      v-model="row.deduction_type"
                      class="px-2 py-1.5 bg-[#1F1F41] border border-[#2E2E56] rounded-lg text-slate-200 text-xs focus:border-amber-400"
                    >
                      <option value="nominal">nominal</option>
                      <option value="percentage">%</option>
                    </select>
                  </div>
                </td>

                <!-- 5. Potong Sesuai Berat (Toggle) -->
                <td class="py-2.5 px-3 text-center">
                  <label class="inline-flex items-center cursor-pointer">
                    <input
                      v-model="row.deduct_by_weight"
                      type="checkbox"
                      class="sr-only peer"
                    />
                    <div class="relative w-11 h-6 bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                  </label>
                </td>

                <!-- 6. Aksi (Simpan & Hapus) -->
                <td class="py-2.5 px-3 text-center">
                  <div class="inline-flex items-center gap-1.5">
                    <button
                      type="button"
                      @click="saveRow(row)"
                      :disabled="row.saving"
                      class="px-3 py-1 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-lg text-xs shadow-sm transition-colors disabled:opacity-50"
                    >
                      {{ row.saving ? '...' : 'Simpan' }}
                    </button>
                    <button
                      type="button"
                      @click="deleteRow(row, idx)"
                      class="px-3 py-1 bg-red-600/90 hover:bg-red-500 text-white font-semibold rounded-lg text-xs shadow-sm transition-colors"
                    >
                      Hapus
                    </button>
                  </div>
                </td>
              </tr>

              <tr v-if="rows.length === 0">
                <td colspan="6" class="py-12 text-center text-slate-500">
                  Belum ada data master potongan harga beli. Klik tombol "+ Tambah Data" di atas untuk menambah baris aturan baru.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
