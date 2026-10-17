<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
  Edit3, ArrowLeft, Save, Building2, Phone, Mail, 
  CreditCard, Banknote, ShieldCheck, Award
} from 'lucide-vue-next';

const props = defineProps({
  employee: Object,
  users: Array,
  positions: Array,
});

const form = useForm({
  employee_code: props.employee.employee_code,
  name: props.employee.name,
  id_card_number: props.employee.id_card_number || '',
  phone: props.employee.phone,
  email: props.employee.email || '',
  address: props.employee.address || '',
  position: props.employee.position,
  status: props.employee.status,
  join_date: props.employee.join_date ? props.employee.join_date.slice(0, 10) : '',
  base_salary: parseFloat(props.employee.base_salary) || 0,
  allowance: parseFloat(props.employee.allowance) || 0,
  commission_rate: parseFloat(props.employee.commission_rate) || 0,
  user_id: props.employee.user_id || '',
  bank_name: props.employee.bank_name || '',
  bank_account_number: props.employee.bank_account_number || '',
  bank_account_holder: props.employee.bank_account_holder || '',
  is_active: Boolean(props.employee.is_active),
  notes: props.employee.notes || '',
});

const submit = () => {
  form.put(route('admin.employees.update', props.employee.id));
};
</script>

<template>
  <AdminLayout title="Edit Data Pegawai">
    <Head :title="`Edit Pegawai - ${employee.name}`" />

    <div class="max-w-4xl mx-auto space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <Link
            :href="route('admin.employees.index')"
            class="p-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors"
          >
            <ArrowLeft class="w-5 h-5" />
          </Link>
          <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-2.5">
              <Edit3 class="w-6 h-6 text-amber-500" />
              <span>Edit Data Pegawai: {{ employee.name }}</span>
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
              Perbarui rincian staf toko, status kontrak, dan kompensasi gaji.
            </p>
          </div>
        </div>
      </div>

      <!-- Form Box -->
      <form @submit.prevent="submit" class="space-y-6">
        <!-- 1. Identitas & Kontak -->
        <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
          <h2 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2 pb-3 border-b border-slate-100 dark:border-slate-800 uppercase tracking-wider">
            <Building2 class="w-4 h-4 text-amber-500" />
            <span>1. Informasi Pribadi & Kontak</span>
          </h2>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                Kode Pegawai <span class="text-rose-500">*</span>
              </label>
              <input
                v-model="form.employee_code"
                type="text"
                required
                class="w-full px-3.5 py-2 text-sm rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white font-mono focus:ring-2 focus:ring-amber-500/30"
              />
              <p v-if="form.errors.employee_code" class="text-xs text-rose-500 mt-1">{{ form.errors.employee_code }}</p>
            </div>

            <div class="md:col-span-2">
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                Nama Lengkap Pegawai <span class="text-rose-500">*</span>
              </label>
              <input
                v-model="form.name"
                type="text"
                required
                class="w-full px-3.5 py-2 text-sm rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500/30"
              />
              <p v-if="form.errors.name" class="text-xs text-rose-500 mt-1">{{ form.errors.name }}</p>
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                Nomor KTP / NIK
              </label>
              <input
                v-model="form.id_card_number"
                type="text"
                class="w-full px-3.5 py-2 text-sm rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white font-mono focus:ring-2 focus:ring-amber-500/30"
              />
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                No. HP / WhatsApp <span class="text-rose-500">*</span>
              </label>
              <input
                v-model="form.phone"
                type="text"
                required
                class="w-full px-3.5 py-2 text-sm rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500/30"
              />
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                Email
              </label>
              <input
                v-model="form.email"
                type="email"
                class="w-full px-3.5 py-2 text-sm rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500/30"
              />
            </div>

            <div class="md:col-span-3">
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                Alamat Domisili
              </label>
              <textarea
                v-model="form.address"
                rows="2"
                class="w-full px-3.5 py-2 text-sm rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500/30"
              ></textarea>
            </div>
          </div>
        </div>

        <!-- 2. Posisi, Status & Akun POS -->
        <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
          <h2 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2 pb-3 border-b border-slate-100 dark:border-slate-800 uppercase tracking-wider">
            <Award class="w-4 h-4 text-amber-500" />
            <span>2. Posisi & Status Pegawai</span>
          </h2>

          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                Jabatan / Posisi <span class="text-rose-500">*</span>
              </label>
              <select
                v-model="form.position"
                required
                class="w-full px-3.5 py-2 text-sm rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500/30"
              >
                <option v-for="pos in positions" :key="pos" :value="pos">{{ pos }}</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                Status Kerja <span class="text-rose-500">*</span>
              </label>
              <select
                v-model="form.status"
                required
                class="w-full px-3.5 py-2 text-sm rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500/30"
              >
                <option value="tetap">Karyawan Tetap</option>
                <option value="kontrak">Kontrak</option>
                <option value="magang">Magang / Trainee</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                Tanggal Masuk <span class="text-rose-500">*</span>
              </label>
              <input
                v-model="form.join_date"
                type="date"
                required
                class="w-full px-3.5 py-2 text-sm rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500/30"
              />
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                Tautkan Akun Kasir POS
              </label>
              <select
                v-model="form.user_id"
                class="w-full px-3.5 py-2 text-sm rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500/30"
              >
                <option value="">-- Tidak Ditautkan --</option>
                <option v-for="u in users" :key="u.id" :value="u.id">{{ u.name }} ({{ u.email }})</option>
              </select>
            </div>
          </div>
        </div>

        <!-- 3. Struktur Gaji & Komisi -->
        <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
          <h2 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2 pb-3 border-b border-slate-100 dark:border-slate-800 uppercase tracking-wider">
            <Banknote class="w-4 h-4 text-amber-500" />
            <span>3. Struktur Gaji & Komisi Omset Penjualan</span>
          </h2>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                Gaji Pokok Bulanan (Rp) <span class="text-rose-500">*</span>
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
                Tunjangan Tetap (Rp)
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
                Komisi Penjualan Emas (% Omset)
              </label>
              <input
                v-model="form.commission_rate"
                type="number"
                step="0.01"
                min="0"
                max="100"
                class="w-full px-3.5 py-2 text-sm rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white font-mono focus:ring-2 focus:ring-amber-500/30"
              />
            </div>
          </div>
        </div>

        <!-- 4. Rekening Bank Transfer -->
        <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
          <h2 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2 pb-3 border-b border-slate-100 dark:border-slate-800 uppercase tracking-wider">
            <CreditCard class="w-4 h-4 text-amber-500" />
            <span>4. Rekening Bank Payroll</span>
          </h2>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                Nama Bank
              </label>
              <input
                v-model="form.bank_name"
                type="text"
                class="w-full px-3.5 py-2 text-sm rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500/30"
              />
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                Nomor Rekening
              </label>
              <input
                v-model="form.bank_account_number"
                type="text"
                class="w-full px-3.5 py-2 text-sm rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white font-mono focus:ring-2 focus:ring-amber-500/30"
              />
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                Atas Nama Rekening
              </label>
              <input
                v-model="form.bank_account_holder"
                type="text"
                class="w-full px-3.5 py-2 text-sm rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500/30"
              />
            </div>
          </div>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-end gap-3">
          <Link
            :href="route('admin.employees.index')"
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
            <span>Perbarui Data Pegawai</span>
          </button>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>
