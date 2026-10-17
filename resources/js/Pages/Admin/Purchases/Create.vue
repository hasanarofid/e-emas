<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
  ArrowLeft, Plus, Trash2, Calculator, Building2, 
  FileSpreadsheet, Save, Calendar, Coins, Scale
} from 'lucide-vue-next';

const props = defineProps({
  suppliers: Array,
  warehouses: Array,
  goldRates: Array,
  categories: Array,
  suggestedInvoice: String,
});

const form = useForm({
  invoice_number: props.suggestedInvoice || '',
  supplier_invoice_number: '',
  supplier_id: props.suppliers.length > 0 ? props.suppliers[0].id : '',
  invoice_date: new Date().toISOString().split('T')[0],
  due_date: '',
  warehouse_id: props.warehouses.length > 0 ? props.warehouses[0].id : '',
  payment_method: 'cash',
  payment_status: 'paid',
  amount_paid: 0,
  discount: 0,
  tax: 0,
  notes: '',
  items: [
    {
      item_name: '',
      gold_rate_id: props.goldRates.length > 0 ? props.goldRates[0].id : '',
      category_id: props.categories.length > 0 ? props.categories[0].id : '',
      weight: 1.000,
      price_per_gram: 0,
      crafting_fee: 0,
    }
  ]
});

const addItemRow = () => {
  form.items.push({
    item_name: '',
    gold_rate_id: props.goldRates.length > 0 ? props.goldRates[0].id : '',
    category_id: props.categories.length > 0 ? props.categories[0].id : '',
    weight: 1.000,
    price_per_gram: 0,
    crafting_fee: 0,
  });
};

const removeItemRow = (index) => {
  if (form.items.length > 1) {
    form.items.splice(index, 1);
  }
};

const getItemSubtotal = (it) => {
  const w = parseFloat(it.weight) || 0;
  const p = parseFloat(it.price_per_gram) || 0;
  const f = parseFloat(it.crafting_fee) || 0;
  return (w * p) + f;
};

const totalWeight = computed(() => {
  return form.items.reduce((sum, it) => sum + (parseFloat(it.weight) || 0), 0);
});

const subtotal = computed(() => {
  return form.items.reduce((sum, it) => sum + getItemSubtotal(it), 0);
});

const grandTotal = computed(() => {
  const disc = parseFloat(form.discount) || 0;
  const tx = parseFloat(form.tax) || 0;
  return Math.max(0, subtotal.value - disc + tx);
});

const remainingBalance = computed(() => {
  const paid = parseFloat(form.amount_paid) || 0;
  return Math.max(0, grandTotal.value - paid);
});

// Auto isi amount_paid jika payment_status = 'paid'
const handleStatusChange = () => {
  if (form.payment_status === 'paid') {
    form.amount_paid = grandTotal.value;
  } else if (form.payment_status === 'unpaid') {
    form.amount_paid = 0;
  }
};

const formatRupiah = (val) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val || 0);
};

const submit = () => {
  form.post(route('admin.purchases.store'));
};
</script>

<template>
  <AdminLayout title="Input Faktur Pembelian Supplier">
    <Head title="Input Faktur Pembelian - POS Emas" />

    <div class="space-y-6 max-w-6xl mx-auto">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <Link
            :href="route('admin.purchases.index')"
            class="p-2 bg-[#181834] hover:bg-[#25254B] text-slate-300 rounded-xl border border-[#25254B] transition-colors"
          >
            <ArrowLeft class="w-5 h-5" />
          </Link>
          <div>
            <h1 class="text-2xl font-bold text-white tracking-wide flex items-center gap-2">
              <FileSpreadsheet class="w-7 h-7 text-amber-400" />
              Faktur Pembelian Emas Baru
            </h1>
            <p class="text-slate-400 text-xs mt-0.5">
              Input faktur pengadaan perhiasan baru dari pabrik / supplier emas
            </p>
          </div>
        </div>
      </div>

      <form @submit.prevent="submit" class="space-y-6">
        <!-- Bagian Informasi Header Faktur -->
        <div class="bg-[#181834] border border-[#25254B] rounded-2xl p-5 shadow-xl space-y-4">
          <h2 class="text-sm font-bold text-amber-400 uppercase tracking-wider flex items-center gap-2">
            <Building2 class="w-4 h-4" />
            Informasi Faktur & Supplier
          </h2>

          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
              <label class="block text-xs font-semibold text-slate-300 mb-1">No. Faktur Internal *</label>
              <input
                v-model="form.invoice_number"
                type="text"
                required
                class="w-full px-3 py-2 bg-[#0F0F23] border border-[#25254B] rounded-xl text-white text-sm focus:border-amber-400 font-mono font-semibold"
              />
              <span v-if="form.errors.invoice_number" class="text-xs text-red-400 mt-1 block">{{ form.errors.invoice_number }}</span>
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-300 mb-1">No. Surat Jalan / Faktur Pabrik</label>
              <input
                v-model="form.supplier_invoice_number"
                type="text"
                placeholder="Contoh: INV-UBS-2026/09"
                class="w-full px-3 py-2 bg-[#0F0F23] border border-[#25254B] rounded-xl text-white text-sm focus:border-amber-400 font-mono"
              />
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-300 mb-1">Mitra Supplier *</label>
              <select
                v-model="form.supplier_id"
                required
                class="w-full px-3 py-2 bg-[#0F0F23] border border-[#25254B] rounded-xl text-white text-sm focus:border-amber-400"
              >
                <option v-for="s in suppliers" :key="s.id" :value="s.id">
                  {{ s.name }} ({{ s.city || 'Pabrik' }})
                </option>
              </select>
              <span v-if="form.errors.supplier_id" class="text-xs text-red-400 mt-1 block">{{ form.errors.supplier_id }}</span>
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-300 mb-1">Gudang / Lokasi Masuk</label>
              <select
                v-model="form.warehouse_id"
                class="w-full px-3 py-2 bg-[#0F0F23] border border-[#25254B] rounded-xl text-white text-sm focus:border-amber-400"
              >
                <option value="">Pilih Gudang Penyimpanan</option>
                <option v-for="w in warehouses" :key="w.id" :value="w.id">
                  {{ w.name }} ({{ w.code }})
                </option>
              </select>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
              <label class="block text-xs font-semibold text-slate-300 mb-1">Tanggal Faktur *</label>
              <input
                v-model="form.invoice_date"
                type="date"
                required
                class="w-full px-3 py-2 bg-[#0F0F23] border border-[#25254B] rounded-xl text-white text-sm focus:border-amber-400"
              />
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-300 mb-1">Jatuh Tempo (Tempo)</label>
              <input
                v-model="form.due_date"
                type="date"
                class="w-full px-3 py-2 bg-[#0F0F23] border border-[#25254B] rounded-xl text-white text-sm focus:border-amber-400"
              />
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-300 mb-1">Metode Pembayaran *</label>
              <select
                v-model="form.payment_method"
                class="w-full px-3 py-2 bg-[#0F0F23] border border-[#25254B] rounded-xl text-white text-sm focus:border-amber-400"
              >
                <option value="cash">Tunai (Cash)</option>
                <option value="transfer">Transfer Bank</option>
                <option value="tempo">Tempo / Hutang Dagang</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-300 mb-1">Status Pelunasan *</label>
              <select
                v-model="form.payment_status"
                @change="handleStatusChange"
                class="w-full px-3 py-2 bg-[#0F0F23] border border-[#25254B] rounded-xl text-white text-sm focus:border-amber-400 font-semibold"
              >
                <option value="paid">Lunas (Paid)</option>
                <option value="partial">Sebagian (Partial)</option>
                <option value="unpaid">Belum Lunas / Tempo (Unpaid)</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Tabel Baris Item Perhiasan -->
        <div class="bg-[#181834] border border-[#25254B] rounded-2xl p-5 shadow-xl space-y-4">
          <div class="flex items-center justify-between">
            <h2 class="text-sm font-bold text-amber-400 uppercase tracking-wider flex items-center gap-2">
              <Scale class="w-4 h-4" />
              Rincian Item Perhiasan Masuk ({{ form.items.length }} baris)
            </h2>
            <button
              type="button"
              @click="addItemRow"
              class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#25254B] hover:bg-[#313163] text-amber-300 rounded-xl text-xs font-semibold transition-colors border border-amber-400/20"
            >
              <Plus class="w-4 h-4" />
              <span>Tambah Baris Item</span>
            </button>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
              <thead>
                <tr class="bg-[#1F1F41] text-slate-300 border-b border-[#25254B]">
                  <th class="py-2.5 px-3 min-w-[200px]">Nama Perhiasan *</th>
                  <th class="py-2.5 px-3 min-w-[130px]">Kadar Emas *</th>
                  <th class="py-2.5 px-3 min-w-[130px]">Kategori</th>
                  <th class="py-2.5 px-3 min-w-[100px] text-center">Berat (gr) *</th>
                  <th class="py-2.5 px-3 min-w-[130px] text-right">Harga / gr (Rp) *</th>
                  <th class="py-2.5 px-3 min-w-[120px] text-right">Ongkos Bikin</th>
                  <th class="py-2.5 px-3 min-w-[130px] text-right">Total Sub</th>
                  <th class="py-2.5 px-3 text-center">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-[#25254B]">
                <tr v-for="(it, idx) in form.items" :key="idx" class="hover:bg-[#1F1F41]/30">
                  <td class="py-2 px-3">
                    <input
                      v-model="it.item_name"
                      type="text"
                      required
                      placeholder="Cincin Solitaire 750"
                      class="w-full px-2.5 py-1.5 bg-[#0F0F23] border border-[#25254B] rounded-lg text-white text-xs focus:border-amber-400"
                    />
                  </td>
                  <td class="py-2 px-3">
                    <select
                      v-model="it.gold_rate_id"
                      required
                      class="w-full px-2 py-1.5 bg-[#0F0F23] border border-[#25254B] rounded-lg text-white text-xs focus:border-amber-400"
                    >
                      <option v-for="g in goldRates" :key="g.id" :value="g.id">
                        {{ g.name }} ({{ g.percentage }}%)
                      </option>
                    </select>
                  </td>
                  <td class="py-2 px-3">
                    <select
                      v-model="it.category_id"
                      required
                      class="w-full px-2 py-1.5 bg-[#0F0F23] border border-[#25254B] rounded-lg text-white text-xs focus:border-amber-400"
                    >
                      <option v-for="c in categories" :key="c.id" :value="c.id">
                        {{ c.name }}
                      </option>
                    </select>
                  </td>
                  <td class="py-2 px-3">
                    <input
                      v-model.number="it.weight"
                      type="number"
                      step="0.001"
                      min="0.001"
                      required
                      class="w-full px-2 py-1.5 bg-[#0F0F23] border border-[#25254B] rounded-lg text-white text-xs text-center focus:border-amber-400 font-mono font-semibold"
                    />
                  </td>
                  <td class="py-2 px-3">
                    <input
                      v-model.number="it.price_per_gram"
                      type="number"
                      step="1000"
                      min="0"
                      required
                      class="w-full px-2 py-1.5 bg-[#0F0F23] border border-[#25254B] rounded-lg text-white text-xs text-right focus:border-amber-400 font-mono"
                    />
                  </td>
                  <td class="py-2 px-3">
                    <input
                      v-model.number="it.crafting_fee"
                      type="number"
                      step="1000"
                      min="0"
                      class="w-full px-2 py-1.5 bg-[#0F0F23] border border-[#25254B] rounded-lg text-white text-xs text-right focus:border-amber-400 font-mono"
                    />
                  </td>
                  <td class="py-2 px-3 text-right font-mono font-bold text-amber-400">
                    {{ formatRupiah(getItemSubtotal(it)) }}
                  </td>
                  <td class="py-2 px-3 text-center">
                    <button
                      type="button"
                      @click="removeItemRow(idx)"
                      :disabled="form.items.length === 1"
                      class="p-1 text-slate-400 hover:text-red-400 disabled:opacity-20 rounded"
                    >
                      <Trash2 class="w-4 h-4" />
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Rangkuman Pembayaran & Kalkulasi -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="bg-[#181834] border border-[#25254B] rounded-2xl p-5 shadow-xl space-y-3">
            <h3 class="text-xs font-bold text-slate-300 uppercase tracking-wider">Catatan Tambahan</h3>
            <textarea
              v-model="form.notes"
              rows="4"
              placeholder="Tambahkan catatan khusus pengiriman, nomor resi, atau kesepakatan tempo..."
              class="w-full px-3 py-2 bg-[#0F0F23] border border-[#25254B] rounded-xl text-white text-xs focus:border-amber-400"
            ></textarea>
          </div>

          <div class="bg-[#181834] border border-[#25254B] rounded-2xl p-5 shadow-xl space-y-3 text-xs">
            <div class="flex justify-between text-slate-300">
              <span>Total Berat Emas:</span>
              <span class="font-bold text-white font-mono">{{ totalWeight.toFixed(3) }} gram</span>
            </div>
            <div class="flex justify-between text-slate-300">
              <span>Subtotal Perhiasan:</span>
              <span class="font-semibold text-white">{{ formatRupiah(subtotal) }}</span>
            </div>
            <div class="flex items-center justify-between gap-4">
              <span class="text-slate-300">Diskon Supplier (Rp):</span>
              <input
                v-model.number="form.discount"
                type="number"
                min="0"
                class="w-44 px-2 py-1 bg-[#0F0F23] border border-[#25254B] rounded-lg text-emerald-400 text-right font-mono text-xs focus:border-amber-400"
              />
            </div>
            <div class="flex items-center justify-between gap-4">
              <span class="text-slate-300">PPN / Pajak (Rp):</span>
              <input
                v-model.number="form.tax"
                type="number"
                min="0"
                class="w-44 px-2 py-1 bg-[#0F0F23] border border-[#25254B] rounded-lg text-slate-200 text-right font-mono text-xs focus:border-amber-400"
              />
            </div>
            <div class="flex justify-between text-sm font-bold text-white pt-2 border-t border-[#25254B]">
              <span>Grand Total:</span>
              <span class="text-amber-400 font-mono text-base">{{ formatRupiah(grandTotal) }}</span>
            </div>
            <div class="flex items-center justify-between gap-4 pt-1">
              <span class="text-slate-300 font-semibold">Jumlah Terbayar (Rp):</span>
              <input
                v-model.number="form.amount_paid"
                type="number"
                min="0"
                class="w-44 px-2 py-1 bg-[#0F0F23] border border-emerald-500/30 rounded-lg text-emerald-400 text-right font-mono text-xs font-bold focus:border-amber-400"
              />
            </div>
            <div v-if="remainingBalance > 0" class="flex justify-between text-red-400 font-semibold pt-1">
              <span>Sisa Hutang (Tempo):</span>
              <span class="font-mono">{{ formatRupiah(remainingBalance) }}</span>
            </div>
          </div>
        </div>

        <!-- Tombol Simpan -->
        <div class="flex items-center justify-end gap-3">
          <Link
            :href="route('admin.purchases.index')"
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
            <span>{{ form.processing ? 'Menyimpan Faktur...' : 'Simpan Faktur Pembelian' }}</span>
          </button>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>
