<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { 
  Settings as SettingsIcon, 
  Store, 
  FileText, 
  Check, 
  Sparkles, 
  ShieldCheck, 
  Phone, 
  MapPin, 
  Receipt 
} from 'lucide-vue-next';

const props = defineProps({
  settings: {
    type: Object,
    required: true
  }
});

const form = useForm({
  store_name: props.settings.store_name || '',
  store_legal_name: props.settings.store_legal_name || '',
  store_phone: props.settings.store_phone || '',
  store_address: props.settings.store_address || '',
  store_permit_number: props.settings.store_permit_number || '',
  primary_color: props.settings.primary_color || '#D4AF37',
  gold_buyback_policy: props.settings.gold_buyback_policy || '',
  receipt_footer: props.settings.receipt_footer || '',
});

const submitSettings = () => {
  form.post(route('admin.settings.update'), {
    preserveScroll: true,
  });
};
</script>

<template>
  <Head title="Pengaturan Toko & Surat Emas - e-Emas" />

  <AdminLayout>
    <div class="space-y-8">
      <!-- Title Page -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200 dark:border-slate-800">
        <div>
          <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-700 dark:text-amber-300 text-xs font-semibold mb-1">
            <Sparkles class="w-3.5 h-3.5 text-amber-500 dark:text-amber-400" />
            <span>Identitas Bisnis & Format Surat Jaminan</span>
          </div>
          <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">Pengaturan Toko Emas</h2>
          <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
            Konfigurasi nama toko, legalitas SIUP, nomor kontak WhatsApp, klausul buyback emas, dan catatan nota resmi.
          </p>
        </div>
      </div>

      <form @submit.prevent="submitSettings" class="space-y-6">
        <!-- Section 1: Profil & Identitas Toko -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-4">
          <div class="pb-3 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
            <Store class="w-5 h-5 text-amber-500" />
            <h3 class="text-base font-bold text-slate-900 dark:text-white">Profil & Legalitas Toko</h3>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
            <div>
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Toko Emas (Branding)</label>
              <input 
                type="text" 
                v-model="form.store_name"
                required
                class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-950 border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white py-2.5 px-3 focus:border-amber-500 focus:ring-amber-500"
              />
            </div>

            <div>
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Badan Usaha / Perusahaan</label>
              <input 
                type="text" 
                v-model="form.store_legal_name"
                required
                class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-950 border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white py-2.5 px-3 focus:border-amber-500 focus:ring-amber-500"
              />
            </div>

            <div>
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nomor Izin Usaha / SIUP Emas</label>
              <input 
                type="text" 
                v-model="form.store_permit_number"
                placeholder="cth: SIUP: 510/088/EMAS/2026"
                class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-950 border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white py-2.5 px-3 focus:border-amber-500 focus:ring-amber-500 font-mono"
              />
            </div>

            <div>
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nomor Kontak WhatsApp Toko</label>
              <input 
                type="text" 
                v-model="form.store_phone"
                required
                class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-950 border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white py-2.5 px-3 focus:border-amber-500 focus:ring-amber-500 font-mono"
              />
            </div>

            <div class="sm:col-span-2">
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Alamat Lengkap Toko</label>
              <input 
                type="text" 
                v-model="form.store_address"
                required
                class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-950 border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white py-2.5 px-3 focus:border-amber-500 focus:ring-amber-500"
              />
            </div>
          </div>
        </div>

        <!-- Section 2: Kebijakan Surat Emas & Klausul Buyback -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-4">
          <div class="pb-3 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
            <ShieldCheck class="w-5 h-5 text-amber-500" />
            <h3 class="text-base font-bold text-slate-900 dark:text-white">Format Ketentuan Surat Emas & Buyback</h3>
          </div>

          <div class="space-y-4 text-xs">
            <div>
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">
                Klausul Ketentuan Pembelian Kembali (Buyback Policy)
              </label>
              <p class="text-[11px] text-slate-500 dark:text-slate-400 mb-1">Teks ini akan dicetak resmi pada bagian belakang/bawah Surat Jaminan Emas pelanggan.</p>
              <textarea 
                v-model="form.gold_buyback_policy"
                rows="4"
                class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-950 border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white p-3 focus:border-amber-500 focus:ring-amber-500 leading-relaxed"
              ></textarea>
            </div>

            <div>
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">
                Pesan Footer Struk Kasir / Surat Emas
              </label>
              <input 
                type="text" 
                v-model="form.receipt_footer"
                class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-950 border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white py-2.5 px-3 focus:border-amber-500 focus:ring-amber-500"
              />
            </div>
          </div>
        </div>

        <!-- Submit Button Toolbar -->
        <div class="flex items-center justify-end">
          <button 
            type="submit"
            :disabled="form.processing"
            class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-bold text-xs uppercase tracking-wider shadow-md shadow-amber-500/20 active:scale-95 transition-all flex items-center gap-2 cursor-pointer"
          >
            <Check class="w-4 h-4" />
            <span>Simpan Pengaturan Toko</span>
          </button>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>
