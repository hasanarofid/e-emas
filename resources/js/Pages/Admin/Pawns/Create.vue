<script setup>
import { ref, computed, watch } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
  ArrowLeft, CreditCard, Scale, Coins, Calendar, 
  User, Save, HelpCircle, AlertCircle
} from 'lucide-vue-next';

const props = defineProps({
  customers: Array,
  goldRates: Array,
  suggestedNumber: String,
});

const today = new Date().toISOString().split('T')[0];
const nextMonth = new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];

const form = useForm({
  pawn_number: props.suggestedNumber || '',
  customer_id: '',
  customer_name: '',
  customer_phone: '',
  item_name: '',
  gold_rate_id: props.goldRates.length > 0 ? props.goldRates[0].id : '',
  gross_weight: 1.000,
  estimated_value: 0,
  loan_amount: 0,
  monthly_interest_rate: 2.00, // 2% per bulan default
  monthly_fee: 0,
  start_date: today,
  due_date: nextMonth,
  notes: '',
});

// Auto-fill nama dan telp saat memilih pelanggan
const onCustomerSelect = () => {
  const c = props.customers.find(item => item.id === form.customer_id);
  if (c) {
    form.customer_name = c.name;
    form.customer_phone = c.phone || '';
  }
};

// Hitung taksiran dan jasa titip otomatis
const selectedGoldRate = computed(() => {
  return props.goldRates.find(g => g.id === form.gold_rate_id);
});

watch([() => form.gross_weight, () => form.gold_rate_id], () => {
  if (selectedGoldRate.value) {
    const buybackPrice = parseFloat(selectedGoldRate.value.buyback_price) || 0;
    const weight = parseFloat(form.gross_weight) || 0;
    const est = Math.round(weight * buybackPrice);
    form.estimated_value = est;
    // Pinjaman default 80% dari taksiran pasar
    form.loan_amount = Math.round(est * 0.8);
  }
}, { immediate: true });

watch([() => form.loan_amount, () => form.monthly_interest_rate], () => {
  const loan = parseFloat(form.loan_amount) || 0;
  const rate = parseFloat(form.monthly_interest_rate) || 0;
  form.monthly_fee = Math.round(loan * (rate / 100));
}, { immediate: true });

const formatRupiah = (val) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val || 0);
};

const submit = () => {
  form.post(route('admin.pawns.store'));
};
</script>

<template>
  <AdminLayout title="Terbitkan Gadai Emas Baru">
    <Head title="Gadai Emas Baru - POS Emas" />

    <div class="space-y-6 max-w-4xl mx-auto">
      <!-- Header -->
      <div class="flex items-center gap-3">
        <Link
          :href="route('admin.pawns.index')"
          class="p-2 bg-[#181834] hover:bg-[#25254B] text-slate-300 rounded-xl border border-[#25254B] transition-colors"
        >
          <ArrowLeft class="w-5 h-5" />
        </Link>
        <div>
          <h1 class="text-2xl font-bold text-white tracking-wide flex items-center gap-2">
            <CreditCard class="w-7 h-7 text-amber-400" />
            Terbitkan Surat Gadai Emas Baru
          </h1>
          <p class="text-slate-400 text-xs mt-0.5">
            Penerimaan agunan perhiasan dan pencairan dana tunai kepada pelanggan
          </p>
        </div>
      </div>

      <form @submit.prevent="submit" class="space-y-6">
        <!-- Data Surat & Pelanggan -->
        <div class="bg-[#181834] border border-[#25254B] rounded-2xl p-5 shadow-xl space-y-4">
          <h2 class="text-xs font-bold text-amber-400 uppercase tracking-wider flex items-center gap-2">
            <User class="w-4 h-4" />
            Identitas Nasabah / Pelanggan
          </h2>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-xs font-semibold text-slate-300 mb-1">No. Surat Bukti Gadai *</label>
              <input
                v-model="form.pawn_number"
                type="text"
                required
                class="w-full px-3 py-2 bg-[#0F0F23] border border-[#25254B] rounded-xl text-white text-sm focus:border-amber-400 font-mono font-bold"
              />
              <span v-if="form.errors.pawn_number" class="text-xs text-red-400 mt-1 block">{{ form.errors.pawn_number }}</span>
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
              <label class="block text-xs font-semibold text-slate-300 mb-1">Nama Lengkap Nasabah *</label>
              <input
                v-model="form.customer_name"
                type="text"
                required
                placeholder="Contoh: Ibu Siti Rahma"
                class="w-full px-3 py-2 bg-[#0F0F23] border border-[#25254B] rounded-xl text-white text-sm focus:border-amber-400 font-semibold"
              />
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-xs font-semibold text-slate-300 mb-1">No. WhatsApp / HP</label>
              <input
                v-model="form.customer_phone"
                type="text"
                placeholder="0812xxxxxxxx"
                class="w-full px-3 py-2 bg-[#0F0F23] border border-[#25254B] rounded-xl text-white text-sm focus:border-amber-400"
              />
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-300 mb-1">Tanggal Mulai Pinjaman *</label>
              <input
                v-model="form.start_date"
                type="date"
                required
                class="w-full px-3 py-2 bg-[#0F0F23] border border-[#25254B] rounded-xl text-white text-sm focus:border-amber-400"
              />
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-300 mb-1">Tanggal Jatuh Tempo *</label>
              <input
                v-model="form.due_date"
                type="date"
                required
                class="w-full px-3 py-2 bg-[#0F0F23] border border-[#25254B] rounded-xl text-white text-sm focus:border-amber-400 font-semibold"
              />
            </div>
          </div>
        </div>

        <!-- Detail Barang Jaminan Agunan Emas -->
        <div class="bg-[#181834] border border-[#25254B] rounded-2xl p-5 shadow-xl space-y-4">
          <h2 class="text-xs font-bold text-amber-400 uppercase tracking-wider flex items-center gap-2">
            <Scale class="w-4 h-4" />
            Spesifikasi Barang Agunan Emas
          </h2>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-1">
              <label class="block text-xs font-semibold text-slate-300 mb-1">Nama & Bentuk Perhiasan *</label>
              <input
                v-model="form.item_name"
                type="text"
                required
                placeholder="Contoh: Kalung Rantai Italy"
                class="w-full px-3 py-2 bg-[#0F0F23] border border-[#25254B] rounded-xl text-white text-sm focus:border-amber-400"
              />
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-300 mb-1">Kadar Kemurnian Emas *</label>
              <select
                v-model="form.gold_rate_id"
                required
                class="w-full px-3 py-2 bg-[#0F0F23] border border-[#25254B] rounded-xl text-white text-sm focus:border-amber-400"
              >
                <option v-for="g in goldRates" :key="g.id" :value="g.id">
                  {{ g.name }} ({{ g.percentage }}%)
                </option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-300 mb-1">Berat Timbangan Kotor (gr) *</label>
              <input
                v-model.number="form.gross_weight"
                type="number"
                step="0.001"
                min="0.001"
                required
                class="w-full px-3 py-2 bg-[#0F0F23] border border-[#25254B] rounded-xl text-white text-sm focus:border-amber-400 font-mono font-bold text-center"
              />
            </div>
          </div>

          <!-- Taksiran & Plafon Pinjaman -->
          <div class="p-4 bg-[#0F0F23] border border-[#25254B] rounded-xl grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-xs text-slate-400 mb-1">Estimasi Taksiran Pasar (Rp)</label>
              <input
                v-model.number="form.estimated_value"
                type="number"
                min="0"
                class="w-full px-3 py-2 bg-[#181834] border border-[#25254B] rounded-lg text-amber-400 text-sm font-mono font-bold"
              />
              <span class="text-[10px] text-slate-500 mt-0.5 block">Sesuai kurs buyback toko</span>
            </div>

            <div>
              <label class="block text-xs text-slate-300 font-semibold mb-1">Plafon Pinjaman Dicairkan (Rp) *</label>
              <input
                v-model.number="form.loan_amount"
                type="number"
                min="0"
                required
                class="w-full px-3 py-2 bg-[#181834] border border-emerald-500/40 rounded-lg text-emerald-400 text-base font-mono font-bold"
              />
              <span class="text-[10px] text-slate-400 mt-0.5 block">Rekomendasi max 80% taksiran</span>
            </div>

            <div>
              <div class="flex items-center justify-between mb-1">
                <label class="text-xs text-slate-400">Jasa Titip / Bunga</label>
                <span class="text-[11px] text-amber-400 font-mono">{{ form.monthly_interest_rate }}% / bln</span>
              </div>
              <input
                v-model.number="form.monthly_fee"
                type="number"
                min="0"
                class="w-full px-3 py-2 bg-[#181834] border border-[#25254B] rounded-lg text-white text-sm font-mono font-semibold"
              />
              <span class="text-[10px] text-slate-500 mt-0.5 block">Biaya sewa brankas & asuransi per bulan</span>
            </div>
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-300 mb-1">Keterangan Kondisi Barang</label>
            <textarea
              v-model="form.notes"
              rows="2"
              placeholder="Catatan kondisi fisik emas, kelengkapan surat asli, baret/patah..."
              class="w-full px-3 py-2 bg-[#0F0F23] border border-[#25254B] rounded-xl text-white text-xs focus:border-amber-400"
            ></textarea>
          </div>
        </div>

        <!-- Tombol Aksi -->
        <div class="flex items-center justify-end gap-3">
          <Link
            :href="route('admin.pawns.index')"
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
            <span>{{ form.processing ? 'Menerbitkan...' : 'Terbitkan Surat Gadai Emas' }}</span>
          </button>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>
