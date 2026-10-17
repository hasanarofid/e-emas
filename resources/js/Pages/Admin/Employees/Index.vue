<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
  Users, Plus, Search, UserCheck, Phone, Mail, 
  CreditCard, Banknote, Edit3, Trash2, ArrowUpRight,
  ShieldCheck, Award
} from 'lucide-vue-next';

const props = defineProps({
  employees: Object,
  filters: Object,
  stats: Object,
  positions: Array,
});

const search = ref(props.filters.search || '');
const positionFilter = ref(props.filters.position || '');
const statusFilter = ref(props.filters.status || '');

const handleFilter = () => {
  router.get(route('admin.employees.index'), {
    search: search.value,
    position: positionFilter.value,
    status: statusFilter.value,
  }, { preserveState: true, replace: true });
};

const deleteEmployee = (employee) => {
  if (confirm(`Apakah Anda yakin ingin menonaktifkan/menghapus pegawai "${employee.name}"? Data diarsipkan aman (Soft Delete).`)) {
    router.delete(route('admin.employees.destroy', employee.id));
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
  <AdminLayout title="Data Pegawai & Kasir">
    <Head title="Data Pegawai & Kasir - POS Emas" />

    <div class="space-y-6">
      <!-- Header -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <h1 class="text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-2.5">
            <Users class="w-6 h-6 text-amber-500" />
            <span>Manajemen Pegawai & Tim Kasir</span>
          </h1>
          <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
            Data karyawan toko emas, penugasan kasir, hak komisi omset penjualan, dan akun pembayaran.
          </p>
        </div>

        <div class="flex items-center gap-2.5">
          <Link
            :href="route('admin.payrolls.index')"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 text-sm font-semibold transition-all shadow-sm"
          >
            <Banknote class="w-4 h-4 text-emerald-500" />
            <span>Riwayat Penggajian</span>
          </Link>
          <Link
            :href="route('admin.employees.create')"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-sm shadow-md shadow-amber-500/20 transition-all cursor-pointer"
          >
            <Plus class="w-4 h-4" />
            <span>Tambah Pegawai</span>
          </Link>
        </div>
      </div>

      <!-- KPI Summary Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between">
          <div>
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Seluruh Tim</p>
            <p class="text-2xl font-bold text-slate-900 dark:text-white mt-1">{{ stats.total_employees }} Orang</p>
            <p class="text-xs text-slate-400 mt-0.5">Pegawai & staf terdaftar</p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-blue-500/10 text-blue-500 flex items-center justify-center">
            <Users class="w-6 h-6" />
          </div>
        </div>

        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between">
          <div>
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Pegawai Aktif Bekerja</p>
            <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">{{ stats.active_employees }} Orang</p>
            <p class="text-xs text-slate-400 mt-0.5">Staf bertugas aktif</p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center">
            <UserCheck class="w-6 h-6" />
          </div>
        </div>

        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between">
          <div>
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Beban Gaji Pokok</p>
            <p class="text-2xl font-bold text-amber-500 mt-1">{{ formatRupiah(stats.total_base_payroll) }}</p>
            <p class="text-xs text-slate-400 mt-0.5">Estimasi per bulan</p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-500 flex items-center justify-center">
            <Banknote class="w-6 h-6" />
          </div>
        </div>
      </div>

      <!-- Filters & Search -->
      <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col md:flex-row gap-3 items-center justify-between">
        <div class="relative w-full md:w-80">
          <Search class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" />
          <input
            v-model="search"
            @keyup.enter="handleFilter"
            type="text"
            placeholder="Cari nama, kode, no. HP, jabatan..."
            class="w-full pl-10 pr-4 py-2 text-sm rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500/30"
          />
        </div>

        <div class="flex items-center gap-3 w-full md:w-auto">
          <select
            v-model="positionFilter"
            @change="handleFilter"
            class="px-3 py-2 text-sm rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500/30"
          >
            <option value="">Semua Jabatan</option>
            <option v-for="pos in positions" :key="pos" :value="pos">{{ pos }}</option>
          </select>

          <select
            v-model="statusFilter"
            @change="handleFilter"
            class="px-3 py-2 text-sm rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500/30"
          >
            <option value="">Semua Status</option>
            <option value="tetap">Tetap</option>
            <option value="kontrak">Kontrak</option>
            <option value="magang">Magang</option>
          </select>
        </div>
      </div>

      <!-- Table Section -->
      <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse text-sm">
            <thead>
              <tr class="bg-slate-50/75 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800 text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                <th class="py-3.5 px-4 font-semibold">Pegawai & Kontak</th>
                <th class="py-3.5 px-4 font-semibold">Jabatan & Peran</th>
                <th class="py-3.5 px-4 font-semibold">Status / Tgl Masuk</th>
                <th class="py-3.5 px-4 font-semibold text-right">Gaji Pokok</th>
                <th class="py-3.5 px-4 font-semibold text-center">Komisi Penjualan</th>
                <th class="py-3.5 px-4 font-semibold">Rekening Bank</th>
                <th class="py-3.5 px-4 font-semibold text-center">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
              <tr 
                v-for="emp in employees.data" 
                :key="emp.id"
                class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors"
              >
                <!-- Pegawai & Kontak -->
                <td class="py-3.5 px-4">
                  <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-500 flex items-center justify-center font-bold text-sm shrink-0 border border-amber-500/20">
                      {{ emp.name.charAt(0).toUpperCase() }}
                    </div>
                    <div>
                      <div class="flex items-center gap-2">
                        <span class="font-bold text-slate-900 dark:text-white">{{ emp.name }}</span>
                        <span class="font-mono text-[10px] px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                          {{ emp.employee_code }}
                        </span>
                      </div>
                      <div class="flex items-center gap-3 text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        <span class="flex items-center gap-1"><Phone class="w-3 h-3 text-slate-400" />{{ emp.phone }}</span>
                        <span v-if="emp.email" class="flex items-center gap-1"><Mail class="w-3 h-3 text-slate-400" />{{ emp.email }}</span>
                      </div>
                    </div>
                  </div>
                </td>

                <!-- Jabatan & Peran -->
                <td class="py-3.5 px-4">
                  <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20">
                    <Award class="w-3.5 h-3.5" />
                    {{ emp.position }}
                  </span>
                  <div v-if="emp.user" class="text-[11px] text-slate-400 mt-1 flex items-center gap-1">
                    <ShieldCheck class="w-3 h-3 text-emerald-500" />
                    <span>Akun POS: {{ emp.user.name }}</span>
                  </div>
                </td>

                <!-- Status / Tgl Masuk -->
                <td class="py-3.5 px-4">
                  <span 
                    :class="[
                      emp.status === 'tetap' ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20' : 
                      emp.status === 'kontrak' ? 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20' : 
                      'bg-purple-500/10 text-purple-600 dark:text-purple-400 border-purple-500/20',
                      'px-2 py-0.5 rounded-full text-[11px] font-semibold border uppercase tracking-wider'
                    ]"
                  >
                    {{ emp.status }}
                  </span>
                  <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ formatDate(emp.join_date) }}</p>
                </td>

                <!-- Gaji Pokok -->
                <td class="py-3.5 px-4 text-right">
                  <span class="font-bold text-slate-900 dark:text-white font-mono">{{ formatRupiah(emp.base_salary) }}</span>
                  <p v-if="emp.allowance > 0" class="text-[11px] text-slate-400">+ Tun: {{ formatRupiah(emp.allowance) }}</p>
                </td>

                <!-- Komisi Penjualan -->
                <td class="py-3.5 px-4 text-center">
                  <span v-if="emp.commission_rate > 0" class="inline-block px-2 py-0.5 rounded bg-amber-500/15 text-amber-600 dark:text-amber-400 font-bold font-mono text-xs border border-amber-500/30">
                    {{ emp.commission_rate }}% Omset
                  </span>
                  <span v-else class="text-xs text-slate-400">-</span>
                </td>

                <!-- Rekening Bank -->
                <td class="py-3.5 px-4">
                  <div v-if="emp.bank_name" class="text-xs">
                    <span class="font-semibold text-slate-800 dark:text-slate-200">{{ emp.bank_name }}</span>
                    <p class="font-mono text-slate-500 dark:text-slate-400">{{ emp.bank_account_number }}</p>
                    <p class="text-[10px] text-slate-400">a.n {{ emp.bank_account_holder }}</p>
                  </div>
                  <span v-else class="text-xs text-slate-400">-</span>
                </td>

                <!-- Aksi -->
                <td class="py-3.5 px-4 text-center">
                  <div class="flex items-center justify-center gap-1.5">
                    <Link
                      :href="route('admin.payrolls.create', { employee_id: emp.id })"
                      title="Hitung Slip Gaji"
                      class="p-1.5 rounded-lg text-emerald-600 dark:text-emerald-400 hover:bg-emerald-500/10 transition-colors"
                    >
                      <Banknote class="w-4 h-4" />
                    </Link>
                    <Link
                      :href="route('admin.employees.edit', emp.id)"
                      title="Edit Data Pegawai"
                      class="p-1.5 rounded-lg text-amber-500 hover:bg-amber-500/10 transition-colors"
                    >
                      <Edit3 class="w-4 h-4" />
                    </Link>
                    <button
                      @click="deleteEmployee(emp)"
                      title="Hapus / Nonaktifkan (Soft Delete)"
                      class="p-1.5 rounded-lg text-rose-500 hover:bg-rose-500/10 transition-colors"
                    >
                      <Trash2 class="w-4 h-4" />
                    </button>
                  </div>
                </td>
              </tr>

              <tr v-if="employees.data.length === 0">
                <td colspan="7" class="py-12 text-center text-slate-400">
                  <Users class="w-12 h-12 mx-auto text-slate-300 dark:text-slate-700 mb-2" />
                  <p class="font-semibold">Belum ada data pegawai yang sesuai filter</p>
                  <p class="text-xs mt-1">Gunakan tombol "Tambah Pegawai" untuk mendaftarkan staf baru.</p>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="employees.links && employees.links.length > 3" class="p-4 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between">
          <p class="text-xs text-slate-500">
            Menampilkan {{ employees.from }} - {{ employees.to }} dari {{ employees.total }} data
          </p>
          <div class="flex gap-1">
            <template v-for="(link, i) in employees.links" :key="i">
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
