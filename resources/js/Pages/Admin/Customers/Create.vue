<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { 
  UserPlus, 
  ArrowLeft, 
  Check, 
  MapPin, 
  Phone, 
  Mail, 
  CreditCard, 
  Calendar 
} from 'lucide-vue-next';

const props = defineProps({
  suggestedCode: {
    type: String,
    required: true,
  },
});

const form = useForm({
  code: props.suggestedCode,
  name: '',
  birth_date: '',
  identity_number: '',
  phone: '',
  telephone: '',
  email: '',
  city: 'Yogyakarta',
  address: '',
  level: 'pelanggan',
  notes: '',
});

const cities = [
  'Yogyakarta',
  'Sleman',
  'Bantul',
  'Kulon Progo',
  'Gunungkidul',
  'Surakarta / Solo',
  'Semarang',
  'Magelang',
  'Jakarta',
  'Bandung',
  'Bogor',
  'Tangerang',
  'Majalengka',
  'Surabaya',
  'Denpasar',
];

const submitForm = () => {
  form.post(route('admin.customers.store'));
};
</script>

<template>
  <AdminLayout>
    <Head title="Tambah Pelanggan Baru - POS Emas" />

    <div class="max-w-4xl mx-auto space-y-6">
      <!-- Breadcrumb & Header -->
      <div class="flex items-center justify-between">
        <div>
          <div class="flex items-center gap-2 text-xs font-mono text-amber-600 dark:text-amber-400 uppercase tracking-widest">
            <UserPlus class="w-4 h-4" />
            <span>Master Pelanggan</span>
          </div>
          <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white mt-1">
            Tambah Pelanggan Baru
          </h2>
          <p class="text-sm text-slate-500 dark:text-slate-400">
            Daftarkan identitas pelanggan untuk transaksi kasir, cetak surat emas, dan akumulasi poin hadiah.
          </p>
        </div>

        <Link 
          :href="route('admin.customers.index')"
          class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold transition-colors"
        >
          <ArrowLeft class="w-4 h-4" />
          <span>Kembali ke Data Pelanggan</span>
        </Link>
      </div>

      <!-- Main Form Card -->
      <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">
        <form @submit.prevent="submitForm" class="space-y-6">
          
          <!-- Row 0: Kode Pelanggan Unik -->
          <div class="p-4 bg-amber-500/5 dark:bg-amber-500/10 border border-amber-500/20 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
              <span class="text-xs font-bold text-amber-700 dark:text-amber-400 uppercase font-mono tracking-wider">
                Kode Kartu Member / Barcode Pelanggan
              </span>
              <p class="text-xs text-slate-500 dark:text-slate-400">
                Kode unik dibuat otomatis oleh sistem untuk kartu barcode.
              </p>
            </div>
            <input 
              v-model="form.code"
              type="text" 
              required
              class="px-3.5 py-1.5 bg-white dark:bg-slate-950 border border-amber-500/30 rounded-xl text-sm font-mono font-bold text-amber-700 dark:text-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-500/30 w-full sm:w-48 text-center uppercase"
            />
          </div>

          <!-- Row 1: Nama Lengkap -->
          <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase mb-1">
              Nama Lengkap Pelanggan *
            </label>
            <input 
              v-model="form.name"
              type="text" 
              placeholder="Masukkan nama lengkap pelanggan sesuai KTP"
              required
              class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500"
            />
          </div>

          <!-- Row 2: Tanggal Lahir & No Identitas -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase mb-1">
                Tanggal Lahir (Opsional)
              </label>
              <input 
                v-model="form.birth_date"
                type="date" 
                class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 text-slate-700 dark:text-slate-300"
              />
            </div>
            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase mb-1">
                No. Identitas (KTP / SIM - Opsional)
              </label>
              <input 
                v-model="form.identity_number"
                type="text" 
                placeholder="16 digit NIK KTP / No. SIM"
                class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm font-mono focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500"
              />
            </div>
          </div>

          <!-- Row 3: Kontak (No HP / WhatsApp & No Telp Rumah) -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase mb-1">
                No. HP / WhatsApp
              </label>
              <input 
                v-model="form.phone"
                type="text" 
                placeholder="08xxxxxxxxxx"
                class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm font-mono focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500"
              />
            </div>
            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase mb-1">
                No. Telepon Rumah / Kantor
              </label>
              <input 
                v-model="form.telephone"
                type="text" 
                placeholder="0274-xxxxxx / Opsional"
                class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm font-mono focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500"
              />
            </div>
          </div>

          <!-- Row 4: Email & Kota -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase mb-1">
                Email
              </label>
              <input 
                v-model="form.email"
                type="email" 
                placeholder="pelanggan@email.com"
                class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500"
              />
            </div>
            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase mb-1">
                Kota Domisili *
              </label>
              <select 
                v-model="form.city"
                required
                class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500"
              >
                <option v-for="c in cities" :key="c" :value="c">{{ c }}</option>
              </select>
            </div>
          </div>

          <!-- Row 5: Alamat Lengkap -->
          <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase mb-1">
              Alamat Lengkap *
            </label>
            <textarea 
              v-model="form.address"
              rows="3"
              placeholder="Nama jalan, RT/RW, kelurahan, kecamatan..."
              required
              class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500"
            ></textarea>
          </div>

          <!-- Row 6: Level Pelanggan -->
          <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase mb-1">
              Level / Golongan Pelanggan *
            </label>
            <select 
              v-model="form.level"
              required
              class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500"
            >
              <option value="pelanggan">Pelanggan Reguler</option>
              <option value="agen">Agen Perhiasan</option>
              <option value="vip">Member Prioritas (VIP)</option>
              <option value="marketer">Marketer / Reseller Emas</option>
            </select>
          </div>

          <!-- Action Buttons -->
          <div class="flex items-center gap-3 pt-4 border-t border-slate-200 dark:border-slate-800">
            <button 
              type="submit"
              :disabled="form.processing"
              class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-sm shadow-sm shadow-amber-500/20 transition-all cursor-pointer"
            >
              <Check class="w-4 h-4" />
              <span>Simpan & Daftarkan Pelanggan</span>
            </button>
            <Link 
              :href="route('admin.customers.index')"
              class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 text-sm font-medium transition-colors"
            >
              Batal
            </Link>
          </div>

        </form>
      </div>
    </div>
  </AdminLayout>
</template>
