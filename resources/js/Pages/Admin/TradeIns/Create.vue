<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
  Repeat, ArrowLeft, Save, Scale, Coins, 
  User, CheckCircle2, AlertCircle, ShoppingBag, ArrowRightLeft
} from 'lucide-vue-next';

const props = defineProps({
  customers: Array,
  goldRates: Array,
  items: Array,
  bankAccounts: Array,
  suggestedNumber: String,
});

const form = useForm({
  trade_in_number: props.suggestedNumber || '',
  customer_name: '',
  customer_phone: '',
  customer_id: null,
  
  // Barang Lama (Masuk ke Toko)
  old_item_name: '',
  old_gold_rate_id: '',
  old_weight: '',
  old_estimated_price: 0,
  
  // Barang Baru (Keluar dari Toko)
  new_item_id: null,
  new_item_name: '',
  new_weight: '',
  new_price: 0,
  
  // Pembayaran
  difference_amount: 0,
  payment_method: 'cash',
  bank_account_id: null,
  amount_paid: 0,
  change_due: 0,
  notes: '',
});

// Pilih Pelanggan Terdaftar
const onCustomerSelect = (e) => {
  const custId = e.target.value;
  if (!custId) return;
  const cust = props.customers.find(c => c.id == custId);
  if (cust) {
    form.customer_name = cust.name;
    form.customer_phone = cust.phone || '';
    form.customer_id = cust.id;
  }
};

// Pilih Barang Baru dari Stok Etalase
const onNewItemSelect = (e) => {
  const itemId = e.target.value;
  if (!itemId) return;
  const it = props.items.find(i => i.id == itemId);
  if (it) {
    form.new_item_name = it.name + (it.code ? ` (${it.code})` : '');
    form.new_weight = parseFloat(it.weight) || 0;
    form.new_price = parseFloat(it.price) || 0;
    recalculate();
  }
};

// Hitung Selisih
const recalculate = () => {
  const oldVal = parseFloat(form.old_estimated_price) || 0;
  const newVal = parseFloat(form.new_price) || 0;
  const diff = newVal - oldVal;
  
  form.difference_amount = diff;
  if (form.amount_paid < diff) {
    form.amount_paid = diff > 0 ? diff : 0;
  }
  form.change_due = Math.max(0, (parseFloat(form.amount_paid) || 0) - (diff > 0 ? diff : 0));
};

const onPaidChange = () => {
  const diff = parseFloat(form.difference_amount) || 0;
  const paid = parseFloat(form.amount_paid) || 0;
  form.change_due = Math.max(0, paid - (diff > 0 ? diff : 0));
};

const formatRupiah = (val) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val || 0);
};

const submit = () => {
  form.post(route('admin.trade-ins.store'));
};
</script>

<template>
  <AdminLayout>
    <Head title="Tukar Tambah Emas Baru" />

    <div class="space-y-6 max-w-6xl mx-auto">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <Link
            :href="route('admin.trade-ins.index')"
            class="p-2 bg-[#1C1C3E] border border-[#2D2D54] rounded-xl text-slate-400 hover:text-white hover:bg-[#25254B] transition-colors"
          >
            <ArrowLeft class="w-5 h-5" />
          </Link>
          <div>
            <h2 class="text-2xl font-bold tracking-tight text-white">
              Form Transaksi Tukar Tambah Baru
            </h2>
            <p class="text-sm text-slate-400">
              Input penimbangan emas lama pelanggan dan perhiasan baru dari etalase.
            </p>
          </div>
        </div>
      </div>

      <form @submit.prevent="submit" class="space-y-6">
        <!-- Data Pelanggan & Nomor Nota -->
        <div class="bg-[#1C1C3E] border border-[#2D2D54] rounded-2xl p-6">
          <h3 class="text-base font-semibold text-white flex items-center gap-2 mb-4">
            <User class="w-4 h-4 text-amber-400" />
            <span>Identitas Nota & Pelanggan</span>
          </h3>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-xs font-medium text-slate-300 mb-1">Nomor Nota TT</label>
              <input
                v-model="form.trade_in_number"
                type="text"
                required
                class="w-full bg-[#15152D] border border-[#2D2D54] rounded-xl px-4 py-2.5 text-sm text-white font-mono focus:border-amber-400 focus:outline-none"
              />
              <span v-if="form.errors.trade_in_number" class="text-xs text-rose-400 mt-1 block">{{ form.errors.trade_in_number }}</span>
            </div>

            <div>
              <label class="block text-xs font-medium text-slate-300 mb-1">Cari Pelanggan Member (Opsional)</label>
              <select
                @change="onCustomerSelect"
                class="w-full bg-[#15152D] border border-[#2D2D54] rounded-xl px-4 py-2.5 text-sm text-slate-300 focus:border-amber-400 focus:outline-none"
              >
                <option value="">-- Pilih dari Daftar Pelanggan --</option>
                <option v-for="c in customers" :key="c.id" :value="c.id">{{ c.name }} ({{ c.phone || 'No HP -' }})</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-medium text-slate-300 mb-1">Nama Pelanggan <span class="text-rose-400">*</span></label>
              <input
                v-model="form.customer_name"
                type="text"
                required
                placeholder="Nama lengkap pembeli"
                class="w-full bg-[#15152D] border border-[#2D2D54] rounded-xl px-4 py-2.5 text-sm text-white focus:border-amber-400 focus:outline-none"
              />
              <span v-if="form.errors.customer_name" class="text-xs text-rose-400 mt-1 block">{{ form.errors.customer_name }}</span>
            </div>
          </div>
        </div>

        <!-- 2 Kolom: Emas Lama vs Emas Baru -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <!-- Sisi Kiri: Emas Lama (Diterima Toko) -->
          <div class="bg-[#1C1C3E] border border-emerald-500/30 rounded-2xl p-6 relative">
            <div class="absolute -top-3 right-6 bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 text-xs px-3 py-0.5 rounded-full font-semibold uppercase">
              Emas Lama Masuk
            </div>

            <h3 class="text-base font-semibold text-emerald-400 flex items-center gap-2 mb-4">
              <Scale class="w-4 h-4" />
              <span>1. Perhiasan Emas Lama (Pelanggan)</span>
            </h3>

            <div class="space-y-4">
              <div>
                <label class="block text-xs font-medium text-slate-300 mb-1">Nama / Jenis Perhiasan Lama <span class="text-rose-400">*</span></label>
                <input
                  v-model="form.old_item_name"
                  type="text"
                  required
                  placeholder="Contoh: Kalung Rantai Itali Lama 375"
                  class="w-full bg-[#15152D] border border-[#2D2D54] rounded-xl px-4 py-2.5 text-sm text-white focus:border-emerald-400 focus:outline-none"
                />
              </div>

              <div class="grid grid-cols-2 gap-3">
                <div>
                  <label class="block text-xs font-medium text-slate-300 mb-1">Kadar / Karat Emas</label>
                  <select
                    v-model="form.old_gold_rate_id"
                    class="w-full bg-[#15152D] border border-[#2D2D54] rounded-xl px-3 py-2.5 text-sm text-slate-300 focus:border-emerald-400 focus:outline-none"
                  >
                    <option value="">-- Pilih Kadar --</option>
                    <option v-for="g in goldRates" :key="g.id" :value="g.id">{{ g.name }} ({{ g.percentage }}%)</option>
                  </select>
                </div>
                <div>
                  <label class="block text-xs font-medium text-slate-300 mb-1">Berat Timbang (Gram) <span class="text-rose-400">*</span></label>
                  <input
                    v-model="form.old_weight"
                    type="number"
                    step="0.001"
                    required
                    placeholder="0.000"
                    class="w-full bg-[#15152D] border border-[#2D2D54] rounded-xl px-4 py-2.5 text-sm text-white font-mono focus:border-emerald-400 focus:outline-none"
                  />
                </div>
              </div>

              <div>
                <label class="block text-xs font-medium text-slate-300 mb-1">Nilai Taksir Pembelian Toko (Rp) <span class="text-rose-400">*</span></label>
                <input
                  v-model="form.old_estimated_price"
                  @input="recalculate"
                  type="number"
                  min="0"
                  required
                  class="w-full bg-[#15152D] border border-emerald-500/50 rounded-xl px-4 py-2.5 text-lg font-bold text-emerald-400 font-mono focus:border-emerald-400 focus:outline-none"
                />
                <span class="text-xs text-slate-400 mt-1 block">Harga total taksiran yang dipotongkan ke pembelian baru</span>
              </div>
            </div>
          </div>

          <!-- Sisi Kanan: Emas Baru (Keluar Toko) -->
          <div class="bg-[#1C1C3E] border border-amber-500/30 rounded-2xl p-6 relative">
            <div class="absolute -top-3 right-6 bg-amber-500/20 border border-amber-500/40 text-amber-300 text-xs px-3 py-0.5 rounded-full font-semibold uppercase">
              Emas Baru Toko
            </div>

            <h3 class="text-base font-semibold text-amber-400 flex items-center gap-2 mb-4">
              <ShoppingBag class="w-4 h-4" />
              <span>2. Perhiasan Baru (Pilihan Pelanggan)</span>
            </h3>

            <div class="space-y-4">
              <div>
                <label class="block text-xs font-medium text-slate-300 mb-1">Pilih dari Stok Etalase Toko</label>
                <select
                  v-model="form.new_item_id"
                  @change="onNewItemSelect"
                  class="w-full bg-[#15152D] border border-[#2D2D54] rounded-xl px-3 py-2.5 text-sm text-slate-300 focus:border-amber-400 focus:outline-none"
                >
                  <option :value="null">-- Input Manual atau Pilih Etalase --</option>
                  <option v-for="it in items" :key="it.id" :value="it.id">
                    {{ it.code }} - {{ it.name }} ({{ Number(it.weight).toFixed(2) }} gr) - {{ formatRupiah(it.price) }}
                  </option>
                </select>
              </div>

              <div>
                <label class="block text-xs font-medium text-slate-300 mb-1">Nama Perhiasan Baru <span class="text-rose-400">*</span></label>
                <input
                  v-model="form.new_item_name"
                  type="text"
                  required
                  placeholder="Contoh: Gelang Sisik Naga Kadar 750"
                  class="w-full bg-[#15152D] border border-[#2D2D54] rounded-xl px-4 py-2.5 text-sm text-white focus:border-amber-400 focus:outline-none"
                />
              </div>

              <div>
                <label class="block text-xs font-medium text-slate-300 mb-1">Berat Perhiasan Baru (Gram) <span class="text-rose-400">*</span></label>
                <input
                  v-model="form.new_weight"
                  type="number"
                  step="0.001"
                  required
                  placeholder="0.000"
                  class="w-full bg-[#15152D] border border-[#2D2D54] rounded-xl px-4 py-2.5 text-sm text-white font-mono focus:border-amber-400 focus:outline-none"
                />
              </div>

              <div>
                <label class="block text-xs font-medium text-slate-300 mb-1">Harga Jual Perhiasan Baru (Rp) <span class="text-rose-400">*</span></label>
                <input
                  v-model="form.new_price"
                  @input="recalculate"
                  type="number"
                  min="0"
                  required
                  class="w-full bg-[#15152D] border border-amber-500/50 rounded-xl px-4 py-2.5 text-lg font-bold text-amber-400 font-mono focus:border-amber-400 focus:outline-none"
                />
                <span class="text-xs text-slate-400 mt-1 block">Harga banderol perhiasan baru di toko</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Ringkasan Kalkulasi & Pembayaran -->
        <div class="bg-[#1C1C3E] border border-[#2D2D54] rounded-2xl p-6">
          <h3 class="text-base font-semibold text-white flex items-center gap-2 mb-4">
            <Coins class="w-4 h-4 text-cyan-400" />
            <span>3. Perhitungan Selisih & Pembayaran Kasir</span>
          </h3>

          <div class="grid grid-cols-1 md:grid-cols-4 gap-4 bg-[#15152D] p-5 rounded-xl border border-[#2D2D54] mb-6">
            <div>
              <p class="text-xs text-slate-400">Harga Emas Baru:</p>
              <p class="text-lg font-bold text-amber-400 font-mono">{{ formatRupiah(form.new_price) }}</p>
            </div>
            <div>
              <p class="text-xs text-slate-400">Taksir Emas Lama (-):</p>
              <p class="text-lg font-bold text-emerald-400 font-mono">{{ formatRupiah(form.old_estimated_price) }}</p>
            </div>
            <div class="md:col-span-2 bg-[#1E1E3F] p-3 rounded-xl border border-[#2D2D54]">
              <p class="text-xs uppercase font-mono text-cyan-400 font-semibold">Selisih Yang Wajib Dibayar Pelanggan:</p>
              <p class="text-2xl font-black text-white font-mono mt-0.5">{{ formatRupiah(form.difference_amount) }}</p>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-xs font-medium text-slate-300 mb-1">Metode Pembayaran</label>
              <select
                v-model="form.payment_method"
                class="w-full bg-[#15152D] border border-[#2D2D54] rounded-xl px-4 py-2.5 text-sm text-slate-300 focus:border-amber-400 focus:outline-none"
              >
                <option value="cash">Tunai (Cash Kasir)</option>
                <option value="transfer">Transfer Rekening Bank</option>
                <option value="qris">QRIS Toko Emas</option>
              </select>
            </div>

            <div v-if="form.payment_method === 'transfer'">
              <label class="block text-xs font-medium text-slate-300 mb-1">Rekening Bank Tujuan</label>
              <select
                v-model="form.bank_account_id"
                class="w-full bg-[#15152D] border border-[#2D2D54] rounded-xl px-4 py-2.5 text-sm text-slate-300 focus:border-amber-400 focus:outline-none"
              >
                <option :value="null">-- Pilih Rekening Toko --</option>
                <option v-for="b in bankAccounts" :key="b.id" :value="b.id">{{ b.bank_name }} - {{ b.account_number }} (a.n {{ b.account_holder }})</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-medium text-slate-300 mb-1">Nominal Diterima Kasir (Rp)</label>
              <input
                v-model="form.amount_paid"
                @input="onPaidChange"
                type="number"
                min="0"
                class="w-full bg-[#15152D] border border-[#2D2D54] rounded-xl px-4 py-2.5 text-sm font-bold text-white font-mono focus:border-amber-400 focus:outline-none"
              />
            </div>

            <div>
              <label class="block text-xs font-medium text-slate-300 mb-1">Kembalian</label>
              <input
                :value="formatRupiah(form.change_due)"
                disabled
                class="w-full bg-[#15152D]/50 border border-[#2D2D54] rounded-xl px-4 py-2.5 text-sm font-bold text-emerald-400 font-mono"
              />
            </div>
          </div>

          <div class="mt-4">
            <label class="block text-xs font-medium text-slate-300 mb-1">Catatan Tambahan (Opsional)</label>
            <textarea
              v-model="form.notes"
              rows="2"
              placeholder="Catatan kondisi emas lama, cap nota toko lama, atau rincian lainnya..."
              class="w-full bg-[#15152D] border border-[#2D2D54] rounded-xl p-3 text-sm text-white placeholder-slate-500 focus:border-amber-400 focus:outline-none"
            ></textarea>
          </div>
        </div>

        <!-- Tombol Aksi -->
        <div class="flex items-center justify-end gap-3 pt-4">
          <Link
            :href="route('admin.trade-ins.index')"
            class="px-5 py-2.5 rounded-xl border border-[#2D2D54] text-slate-300 hover:bg-[#25254B] hover:text-white transition-all text-sm font-medium"
          >
            Batal
          </Link>
          <button
            type="submit"
            :disabled="form.processing"
            class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-bold text-sm shadow-lg shadow-amber-500/20 flex items-center gap-2 cursor-pointer transition-all"
          >
            <Save class="w-4 h-4" />
            <span>{{ form.processing ? 'Memproses...' : 'Simpan Transaksi Tukar Tambah' }}</span>
          </button>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>
