<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';
import { 
  ArrowRightLeft, 
  Search, 
  Scale, 
  Banknote, 
  User, 
  Phone, 
  FileText, 
  Flame, 
  Gem, 
  Trash2, 
  Plus, 
  Check, 
  AlertTriangle,
  ArrowLeft,
  Layers,
  Sparkles
} from 'lucide-vue-next';

const props = defineProps({
  goldRates: {
    type: Array,
    default: () => []
  },
  bankAccounts: {
    type: Array,
    default: () => []
  },
  storeSettings: {
    type: Object,
    default: () => ({})
  },
  selectedSale: {
    type: Object,
    default: null
  }
});

// Old Invoice Search State
const invoiceQuery = ref('');
const isSearchingInvoice = ref(false);
const searchResults = ref([]);
const isResultsOpen = ref(false);

// Form State
const form = ref({
  customer_name: '',
  customer_phone: '',
  customer_nik: '',
  original_sale_id: null,
  original_invoice_number: '',
  original_weight: 0,
  payment_method: 'cash',
  notes: '',
  items: [
    {
      item_name: '',
      gold_rate_id: props.goldRates[0]?.id || null,
      weight_actual: 0,
      buy_price_per_gram: props.goldRates[0]?.buy_price_per_gram || 0,
      deduction_amount: 0,
      condition_notes: '',
      destination: 'melt', // 'melt' | 'stock_secondary' | 'repair'
    }
  ]
});

const isSubmitting = ref(false);

const formatRupiah = (val) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0
  }).format(val || 0);
};

const formatWeight = (val) => {
  return parseFloat(val || 0).toFixed(3) + ' gr';
};

// Search Sales Function
const handleSearchInvoice = async () => {
  if (invoiceQuery.value.trim().length < 2) return;
  isSearchingInvoice.value = true;
  try {
    const res = await fetch(route('admin.buybacks.search-sale', { query: invoiceQuery.value.trim() }));
    const data = await res.json();
    searchResults.value = data;
    isResultsOpen.value = true;
  } catch (err) {
    console.error('Error searching sale:', err);
  } finally {
    isSearchingInvoice.value = false;
  }
};

// Select Sale from Search
const selectSale = (sale) => {
  form.value.customer_name = sale.customer_name;
  form.value.customer_phone = sale.customer_phone || '';
  form.value.original_sale_id = sale.id;
  form.value.original_invoice_number = sale.invoice_number;
  form.value.original_weight = parseFloat(sale.total_weight || 0);

  // Map sale items to buyback items
  if (sale.items && sale.items.length > 0) {
    form.value.items = sale.items.map(it => {
      // Find matching gold rate
      const matchedRate = props.goldRates.find(r => r.code === it.gold_rate_name) || props.goldRates[0];
      return {
        item_name: it.item_name,
        gold_rate_id: matchedRate ? matchedRate.id : props.goldRates[0]?.id,
        weight_actual: parseFloat(it.net_weight || 0),
        buy_price_per_gram: matchedRate ? parseFloat(matchedRate.buy_price_per_gram) : 0,
        deduction_amount: 0,
        condition_notes: 'Asal Nota Toko',
        destination: 'melt',
      };
    });
  }

  isResultsOpen.value = false;
  invoiceQuery.value = sale.invoice_number;
};

// Update buy price when gold rate is changed
const onGoldRateChange = (idx) => {
  const item = form.value.items[idx];
  const rate = props.goldRates.find(r => r.id === item.gold_rate_id);
  if (rate) {
    item.buy_price_per_gram = parseFloat(rate.buy_price_per_gram || 0);
  }
};

// Add / Remove item rows
const addItemRow = () => {
  form.value.items.push({
    item_name: '',
    gold_rate_id: props.goldRates[0]?.id || null,
    weight_actual: 0,
    buy_price_per_gram: props.goldRates[0]?.buy_price_per_gram || 0,
    deduction_amount: 0,
    condition_notes: '',
    destination: 'melt',
  });
};

const removeItemRow = (idx) => {
  if (form.value.items.length > 1) {
    form.value.items.splice(idx, 1);
  }
};

// Calculations
const totalActualWeight = computed(() => {
  return form.value.items.reduce((acc, it) => acc + (parseFloat(it.weight_actual) || 0), 0);
});

const weightDifference = computed(() => {
  if (!form.value.original_weight) return 0;
  return form.value.original_weight - totalActualWeight.value;
});

const totalDeduction = computed(() => {
  return form.value.items.reduce((acc, it) => acc + (parseFloat(it.deduction_amount) || 0), 0);
});

const grandTotalBuyback = computed(() => {
  return form.value.items.reduce((acc, it) => {
    const weight = parseFloat(it.weight_actual) || 0;
    const rate = parseFloat(it.buy_price_per_gram) || 0;
    const deduct = parseFloat(it.deduction_amount) || 0;
    const sub = Math.max(0, (weight * rate) - deduct);
    return acc + sub;
  }, 0);
});

// Submit
const submitBuyback = () => {
  if (!form.value.customer_name.trim()) {
    alert('Nama pelanggan penjual emas wajib diisi!');
    return;
  }

  isSubmitting.value = true;
  router.post(route('admin.buybacks.store'), form.value, {
    onFinish: () => {
      isSubmitting.value = false;
    }
  });
};

onMounted(() => {
  if (props.selectedSale) {
    selectSale(props.selectedSale);
  }
});
</script>

<template>
  <Head title="Catat Transaksi Beli Kembali Emas - POS Emas" />

  <AdminLayout>
    <div class="space-y-6 max-w-6xl mx-auto">
      
      <!-- Top Header & Back Link -->
      <div class="flex items-center justify-between pb-2 border-b border-slate-200 dark:border-slate-800">
        <div class="flex items-center gap-3">
          <Link 
            :href="route('admin.buybacks.index')"
            class="p-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 transition-colors cursor-pointer"
          >
            <ArrowLeft class="w-4 h-4" />
          </Link>
          <div>
            <h1 class="text-xl font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
              Pencatatan Beli Kembali (Buyback)
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400">
              Formulir transaksi pembelian kembali emas dari pelanggan toko atau emas luar.
            </p>
          </div>
        </div>

        <div class="text-right">
          <span class="text-xs text-slate-400 block font-sans">Total Nilai Kas Diberikan:</span>
          <span class="text-xl font-extrabold text-amber-600 dark:text-amber-400 font-mono">
            {{ formatRupiah(grandTotalBuyback) }}
          </span>
        </div>
      </div>

      <!-- Section 1: Pencarian Nota Penjualan Lama (Optional / Auto-fill) -->
      <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-3 relative">
        <div class="flex items-center justify-between">
          <h3 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2 font-mono">
            <Search class="w-4 h-4 text-amber-500" />
            1. Verifikasi Nota Penjualan Lama (Opsional)
          </h3>
          <span class="text-[10px] text-slate-400">Kosongkan jika emas dari toko luar / tanpa surat</span>
        </div>

        <div class="flex gap-2">
          <div class="relative flex-1">
            <input 
              v-model="invoiceQuery"
              @keydown.enter.prevent="handleSearchInvoice"
              type="text" 
              placeholder="Ketik No. Nota (contoh: INV-EMAS-...), Nama Pembeli, atau No. HP..."
              class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-xs text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500 font-mono"
            />
          </div>
          <button 
            type="button"
            @click="handleSearchInvoice"
            :disabled="isSearchingInvoice"
            class="px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold flex items-center gap-1.5 transition-colors cursor-pointer"
          >
            <Search class="w-3.5 h-3.5" />
            <span>{{ isSearchingInvoice ? 'Mencari...' : 'Cari Nota' }}</span>
          </button>
        </div>

        <!-- Search Results Dropdown -->
        <div 
          v-if="isResultsOpen && searchResults.length > 0"
          class="absolute left-5 right-5 top-full mt-1 z-30 p-2 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-2xl max-h-60 overflow-y-auto space-y-1 text-xs"
        >
          <div 
            v-for="s in searchResults" 
            :key="s.id"
            @click="selectSale(s)"
            class="p-3 rounded-xl hover:bg-amber-500/10 cursor-pointer flex items-center justify-between transition-colors border border-transparent hover:border-amber-500/30"
          >
            <div>
              <span class="font-bold font-mono text-slate-900 dark:text-white">{{ s.invoice_number }}</span>
              <p class="text-[11px] text-slate-500">{{ s.customer_name }} • {{ s.customer_phone || '-' }} • {{ new Date(s.created_at).toLocaleDateString('id-ID') }}</p>
            </div>
            <div class="text-right">
              <span class="font-bold text-amber-600 dark:text-amber-400 font-mono">{{ formatWeight(s.total_weight) }}</span>
              <p class="text-[10px] text-slate-400 font-mono">{{ formatRupiah(s.grand_total) }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Section 2: Data Pelanggan / Penjual Emas -->
      <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
        <h3 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2 font-mono">
          <User class="w-4 h-4 text-amber-500" />
          2. Identitas Pelanggan (Penjual Emas)
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div>
            <label class="text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1 block">Nama Pelanggan *</label>
            <input 
              v-model="form.customer_name"
              type="text" 
              required
              placeholder="Nama lengkap penjual emas" 
              class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-amber-500 font-medium"
            />
          </div>

          <div>
            <label class="text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1 block">No. WhatsApp / HP</label>
            <input 
              v-model="form.customer_phone"
              type="text" 
              placeholder="Contoh: 08123456789" 
              class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-amber-500 font-mono"
            />
          </div>

          <div>
            <label class="text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1 block">NIK / No. KTP</label>
            <input 
              v-model="form.customer_nik"
              type="text" 
              placeholder="16 digit NIK (Verifikasi Barang Berharga)" 
              class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-amber-500 font-mono"
            />
          </div>
        </div>
      </div>

      <!-- Section 3: Rincian Barang Buyback & Timbangan -->
      <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
        <div class="flex items-center justify-between">
          <h3 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2 font-mono">
            <Scale class="w-4 h-4 text-amber-500" />
            3. Rincian Barang & Hasil Timbang Aktual
          </h3>

          <button 
            type="button"
            @click="addItemRow"
            class="px-3 py-1.5 rounded-lg bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-500/20 text-xs font-bold flex items-center gap-1 hover:bg-amber-500/20 transition-colors cursor-pointer"
          >
            <Plus class="w-3.5 h-3.5" /> Tambah Barang
          </button>
        </div>

        <!-- Table of Items -->
        <div class="space-y-3">
          <div 
            v-for="(it, idx) in form.items" 
            :key="idx"
            class="p-4 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 space-y-3"
          >
            <div class="flex items-center justify-between">
              <span class="text-xs font-bold text-amber-600 dark:text-amber-400 font-mono">Barang #{{ idx + 1 }}</span>
              <button 
                v-if="form.items.length > 1"
                type="button" 
                @click="removeItemRow(idx)"
                class="text-slate-400 hover:text-rose-500 p-1 cursor-pointer"
              >
                <Trash2 class="w-4 h-4" />
              </button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
              <!-- Item Name -->
              <div class="sm:col-span-4">
                <label class="text-[10px] text-slate-400 block mb-1">Nama Perhiasan</label>
                <input 
                  v-model="it.item_name" 
                  type="text" 
                  placeholder="Contoh: Cincin Solitaire 17K"
                  required
                  class="w-full px-3 py-2 rounded-lg bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-xs text-slate-900 dark:text-white"
                />
              </div>

              <!-- Kadar Emas -->
              <div class="sm:col-span-2">
                <label class="text-[10px] text-slate-400 block mb-1">Kadar Emas</label>
                <select 
                  v-model="it.gold_rate_id"
                  @change="onGoldRateChange(idx)"
                  class="w-full px-2.5 py-2 rounded-lg bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-xs text-slate-900 dark:text-white font-medium"
                >
                  <option v-for="r in goldRates" :key="r.id" :value="r.id">
                    {{ r.code }} ({{ r.percentage }}%)
                  </option>
                </select>
              </div>

              <!-- Berat Timbang Aktual -->
              <div class="sm:col-span-2">
                <label class="text-[10px] text-slate-400 block mb-1">Berat Timbang (gr)</label>
                <input 
                  v-model.number="it.weight_actual" 
                  type="number" 
                  step="0.001"
                  required
                  placeholder="0.000"
                  class="w-full px-3 py-2 rounded-lg bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-xs text-slate-900 dark:text-white font-mono font-bold"
                />
              </div>

              <!-- Kurs Beli Hari Ini -->
              <div class="sm:col-span-2">
                <label class="text-[10px] text-slate-400 block mb-1">Kurs Beli /gr (Rp)</label>
                <input 
                  v-model.number="it.buy_price_per_gram" 
                  type="number" 
                  class="w-full px-3 py-2 rounded-lg bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-xs text-slate-900 dark:text-white font-mono"
                />
              </div>

              <!-- Potongan Susut / Kerusakan -->
              <div class="sm:col-span-2">
                <label class="text-[10px] text-slate-400 block mb-1">Potongan (Rp)</label>
                <input 
                  v-model.number="it.deduction_amount" 
                  type="number" 
                  placeholder="0"
                  class="w-full px-3 py-2 rounded-lg bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-xs text-rose-500 font-mono font-semibold"
                />
              </div>
            </div>

            <!-- Second Row: Kondisi & Alokasi Barang -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-slate-200/60 dark:border-slate-800/60 text-xs">
              <div>
                <label class="text-[10px] text-slate-400 block mb-1">Catatan Kondisi Fisik</label>
                <input 
                  v-model="it.condition_notes" 
                  type="text" 
                  placeholder="Contoh: Mulus, Patah pengait, Batu lepas"
                  class="w-full px-3 py-1.5 rounded-lg bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-xs text-slate-900 dark:text-white"
                />
              </div>

              <div>
                <label class="text-[10px] text-slate-400 block mb-1">Alokasi Tujuan Emas Bekas</label>
                <select 
                  v-model="it.destination"
                  class="w-full px-3 py-1.5 rounded-lg bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-xs text-slate-900 dark:text-white font-medium"
                >
                  <option value="melt">Peleburan / Rongsok Emas (Melt Scrap)</option>
                  <option value="stock_secondary">Etalase Emas Bekas (Resale Second-hand)</option>
                  <option value="repair">Masuk Servis / Reparasi Toko</option>
                </select>
              </div>
            </div>

            <!-- Subtotal Per Item -->
            <div class="flex justify-between items-center text-xs pt-1 border-t border-slate-200/60 dark:border-slate-800/60">
              <span class="text-slate-400 text-[11px]">Subtotal Beli Item Ini:</span>
              <span class="font-bold text-amber-600 dark:text-amber-400 font-mono text-sm">
                {{ formatRupiah(Math.max(0, (it.weight_actual * it.buy_price_per_gram) - (it.deduction_amount || 0))) }}
              </span>
            </div>
          </div>
        </div>

        <!-- Weight Comparison Alert (If linked to original sale) -->
        <div 
          v-if="form.original_weight > 0"
          class="p-3.5 rounded-xl bg-amber-500/10 border border-amber-500/20 text-xs flex items-center justify-between"
        >
          <div class="flex items-center gap-2">
            <Scale class="w-4 h-4 text-amber-600" />
            <span class="text-slate-700 dark:text-slate-300">
              Berat di Nota Asli: <b>{{ formatWeight(form.original_weight) }}</b> | Berat Timbang Ulang: <b>{{ formatWeight(totalActualWeight) }}</b>
            </span>
          </div>
          <span :class="[weightDifference > 0 ? 'text-amber-600 font-bold' : 'text-emerald-600 font-bold', 'font-mono']">
            Selisih Susut: {{ formatWeight(weightDifference) }}
          </span>
        </div>
      </div>

      <!-- Section 4: Pembayaran & Submit -->
      <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
        <h3 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2 font-mono">
          <Banknote class="w-4 h-4 text-amber-500" />
          4. Metode Pengeluaran Kas Toko
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1 block">Metode Pembayaran ke Pelanggan</label>
            <div class="grid grid-cols-2 gap-2">
              <button 
                type="button"
                @click="form.payment_method = 'cash'"
                :class="[
                  form.payment_method === 'cash' 
                    ? 'bg-amber-500/15 border-amber-500 text-amber-700 dark:text-amber-300 font-bold' 
                    : 'bg-slate-50 dark:bg-slate-950 border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400',
                  'p-3 rounded-xl border text-center text-xs flex items-center justify-center gap-2 transition-all cursor-pointer'
                ]"
              >
                <Banknote class="w-4 h-4" />
                <span>Kas Tunai</span>
              </button>

              <button 
                type="button"
                @click="form.payment_method = 'transfer'"
                :class="[
                  form.payment_method === 'transfer' 
                    ? 'bg-amber-500/15 border-amber-500 text-amber-700 dark:text-amber-300 font-bold' 
                    : 'bg-slate-50 dark:bg-slate-950 border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400',
                  'p-3 rounded-xl border text-center text-xs flex items-center justify-center gap-2 transition-all cursor-pointer'
                ]"
              >
                <ArrowRightLeft class="w-4 h-4" />
                <span>Transfer Bank</span>
              </button>
            </div>
          </div>

          <div>
            <label class="text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1 block">Catatan Transaksi</label>
            <input 
              v-model="form.notes"
              type="text" 
              placeholder="Catatan tambahan buyback..."
              class="w-full px-3 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-xs text-slate-900 dark:text-white"
            />
          </div>
        </div>

        <!-- Grand Total Bar & Submit Button -->
        <div class="pt-4 border-t border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4">
          <div>
            <span class="text-xs text-slate-500 block">Total Uang yang Diterima Pelanggan:</span>
            <span class="text-2xl font-black text-amber-600 dark:text-amber-400 font-mono">
              {{ formatRupiah(grandTotalBuyback) }}
            </span>
          </div>

          <button 
            type="button"
            @click="submitBuyback"
            :disabled="isSubmitting"
            class="w-full sm:w-auto px-8 py-3.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-bold text-xs uppercase tracking-wider flex items-center justify-center gap-2 shadow-lg shadow-amber-500/20 disabled:opacity-50 cursor-pointer transition-all"
          >
            <Check class="w-4 h-4" />
            <span>{{ isSubmitting ? 'Memproses...' : 'Selesaikan Transaksi & Cetak Kuitansi' }}</span>
          </button>
        </div>

      </div>

    </div>
  </AdminLayout>
</template>
