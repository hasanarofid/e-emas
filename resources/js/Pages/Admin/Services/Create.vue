<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
  ArrowLeft, Wrench, User, Calendar, Scale, 
  Coins, Sparkles, Save
} from 'lucide-vue-next';

const props = defineProps({
  customers: Array,
  suggestedNumber: String,
});

const today = new Date().toISOString().split('T')[0];
const targetDay = new Date(Date.now() + 3 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];

const serviceOptions = [
  'Cuci & Poles Kilap Emas',
  'Patri & Sambung Patah',
  'Pasang Permata Zircon / Berlian',
  'Sepuh Emas Kuning / Putih (Chrome)',
  'Resize / Ubah Ukuran Cincin',
  'Custom Order & Kombinasi',
];

const form = useForm({
  service_number: props.suggestedNumber || '',
  customer_id: '',
  customer_name: '',
  customer_phone: '',
  item_name: '',
  weight_before: 1.000,
  service_type: 'Cuci & Poles Kilap Emas',
  damage_description: '',
  estimated_cost: 50000,
  down_payment: 0,
  received_date: today,
  completion_target_date: targetDay,
  notes: '',
});

const onCustomerSelect = () => {
  const c = props.customers.find(item => item.id === form.customer_id);
  if (c) {
    form.customer_name = c.name;
    form.customer_phone = c.phone || '';
  }
};

const remainingCost = computed(() => {
  const cost = parseFloat(form.estimated_cost) || 0;
  const dp = parseFloat(form.down_payment) || 0;
  return Math.max(0, cost - dp);
});

const formatRupiah = (val) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val || 0);
};

const submit = () => {
  form.post(route('admin.services.store'));
};
</script>

<template>
  <AdminLayout title="Order Servis Perhiasan Baru">
    <Head title="Order Servis Baru - POS Emas" />

    <div class="space-y-6 max-w-4xl mx-auto">
      <!-- Header -->
      <div class="flex items-center gap-3">
        <Link
          :href="route('admin.services.index')"
          class="p-2 bg-[#181834] hover:bg-[#25254B] text-slate-300 rounded-xl border border-[#25254B] transition-colors"
        >
          <ArrowLeft class="w-5 h-5" />
        </Link>
        <div>
          <h1 class="text-2xl font-bold text-white tracking-wide flex items-center gap-2">
            <Wrench class="w-7 h-7 text-amber-400" />
            Order Servis & Reparasi Emas Baru
          </h1>
          <p class="text-slate-400 text-xs mt-0.5">
            Penerimaan perhiasan pelanggan untuk cuci, sepuh, patri, atau resize
          </p>
        </div>
      </div>

      <form @submit.prevent="submit" class="space-y-6">
        <!-- Data Pelanggan -->
        <div class="bg-[#181834] border border-[#25254B] rounded-2xl p-5 shadow-xl space-y-4">
          <h2 class="text-xs font-bold text-amber-400 uppercase tracking-wider flex items-center gap-2">
            <User class="w-4 h-4" />
            Identitas Pelanggan
          </h2>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-xs font-semibold text-slate-300 mb-1">No. Tanda Terima Servis *</label>
              <input
                v-model="form.service_number"
                type="text"
                required
                class="w-full px-3 py-2 bg-[#0F0F23] border border-[#25254B] rounded-xl text-white text-sm focus:border-amber-400 font-mono font-bold"
              />
              <span v-if="form.errors.service_number" class="text-xs text-red-400 mt-1 block">{{ form.errors.service_number }}</span>
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-300 mb-1">Pilih Member (Opsional)</label>
              <select
                v-model="form.customer_id"
                @change="onCustomerSelect"
                class="w-full px-3 py-2 bg-[#0F0F23] border border-[#25254B] rounded-xl text-white text-sm focus:border-amber-400"
              >
                <option value="">Pelanggan Baru / Non-Member</option>
                <option v-for="c in customers" :key="c.id" :value="c.id">
                  {{ c.name }} ({{ c.phone || 'Tanpa HP' }})
                </option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-300 mb-1">Nama Pelanggan *</label>
              <input
                v-model="form.customer_name"
                type="text"
                required
                placeholder="Contoh: Ibu Henny"
                class="w-full px-3 py-2 bg-[#0F0F23] border border-[#25254B] rounded-xl text-white text-sm focus:border-amber-400 font-semibold"
              />
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-xs font-semibold text-slate-300 mb-1">No. HP / WhatsApp</label>
              <input
                v-model="form.customer_phone"
                type="text"
                placeholder="0812xxxxxxxx"
                class="w-full px-3 py-2 bg-[#0F0F23] border border-[#25254B] rounded-xl text-white text-sm focus:border-amber-400"
              />
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-300 mb-1">Tanggal Masuk Servis *</label>
              <input
                v-model="form.received_date"
                type="date"
                required
                class="w-full px-3 py-2 bg-[#0F0F23] border border-[#25254B] rounded-xl text-white text-sm focus:border-amber-400"
              />
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-300 mb-1">Estimasi Tanggal Selesai</label>
              <input
                v-model="form.completion_target_date"
                type="date"
                class="w-full px-3 py-2 bg-[#0F0F23] border border-[#25254B] rounded-xl text-white text-sm focus:border-amber-400 font-semibold"
              />
            </div>
          </div>
        </div>

        <!-- Spesifikasi Barang & Pengerjaan -->
        <div class="bg-[#181834] border border-[#25254B] rounded-2xl p-5 shadow-xl space-y-4">
          <h2 class="text-xs font-bold text-amber-400 uppercase tracking-wider flex items-center gap-2">
            <Sparkles class="w-4 h-4" />
            Rincian Barang & Jenis Reparasi
          </h2>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-xs font-semibold text-slate-300 mb-1">Nama / Jenis Perhiasan *</label>
              <input
                v-model="form.item_name"
                type="text"
                required
                placeholder="Contoh: Gelang Rantai Sisik Naga"
                class="w-full px-3 py-2 bg-[#0F0F23] border border-[#25254B] rounded-xl text-white text-sm focus:border-amber-400"
              />
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-300 mb-1">Berat Saat Diterima (gr) *</label>
              <input
                v-model.number="form.weight_before"
                type="number"
                step="0.001"
                min="0.001"
                required
                class="w-full px-3 py-2 bg-[#0F0F23] border border-[#25254B] rounded-xl text-white text-sm focus:border-amber-400 font-mono text-center font-bold"
              />
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-300 mb-1">Jenis Layanan Servis *</label>
              <select
                v-model="form.service_type"
                required
                class="w-full px-3 py-2 bg-[#0F0F23] border border-[#25254B] rounded-xl text-white text-sm focus:border-amber-400"
              >
                <option v-for="opt in serviceOptions" :key="opt" :value="opt">
                  {{ opt }}
                </option>
              </select>
            </div>
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-300 mb-1">Keterangan Kerusakan / Catatan Pelanggan</label>
            <textarea
              v-model="form.damage_description"
              rows="2"
              placeholder="Contoh: Sambungan kokot patah, perlu patri emas 750 dan dipoles ulang..."
              class="w-full px-3 py-2 bg-[#0F0F23] border border-[#25254B] rounded-xl text-white text-xs focus:border-amber-400"
            ></textarea>
          </div>

          <!-- Estimasi Biaya & DP -->
          <div class="p-4 bg-[#0F0F23] border border-[#25254B] rounded-xl grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-xs text-slate-400 mb-1">Estimasi Total Biaya Servis (Rp) *</label>
              <input
                v-model.number="form.estimated_cost"
                type="number"
                min="0"
                required
                class="w-full px-3 py-2 bg-[#181834] border border-[#25254B] rounded-lg text-white text-sm font-mono font-bold"
              />
            </div>

            <div>
              <label class="block text-xs text-slate-400 mb-1">Uang Muka / DP Dibayar (Rp)</label>
              <input
                v-model.number="form.down_payment"
                type="number"
                min="0"
                class="w-full px-3 py-2 bg-[#181834] border border-emerald-500/40 rounded-lg text-emerald-400 text-sm font-mono font-bold"
              />
            </div>

            <div>
              <label class="block text-xs text-slate-400 mb-1">Sisa Pembayaran Saat Pengambilan</label>
              <div class="px-3 py-2 bg-[#181834] border border-[#25254B] rounded-lg text-amber-400 text-sm font-mono font-bold">
                {{ formatRupiah(remainingCost) }}
              </div>
            </div>
          </div>
        </div>

        <!-- Tombol Aksi -->
        <div class="flex items-center justify-end gap-3">
          <Link
            :href="route('admin.services.index')"
            class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-sm font-semibold"
          >
            Batal
          </Link>
          <button
            type="submit"
            :disabled="form.processing"
            class="inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 font-bold rounded-xl shadow-lg shadow-amber-500/20 text-sm disabled:opacity-50 transition-all"
          >
            <Save class="w-4 h-4" />
            <span>{{ form.processing ? 'Menyimpan...' : 'Daftarkan Order Servis' }}</span>
          </button>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>
