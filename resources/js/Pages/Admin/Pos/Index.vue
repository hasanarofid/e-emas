<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { 
  ShoppingBag, 
  Search, 
  Barcode, 
  Trash2, 
  CreditCard, 
  Banknote, 
  QrCode, 
  ArrowRight, 
  User, 
  Phone, 
  MapPin, 
  Gem, 
  Scale, 
  Sparkles, 
  Receipt, 
  FileText, 
  Check, 
  X,
  History,
  Building2,
  AlertCircle
} from 'lucide-vue-next';

const props = defineProps({
  items: {
    type: Array,
    default: () => []
  },
  goldRates: {
    type: Array,
    default: () => []
  },
  bankAccounts: {
    type: Array,
    default: () => []
  },
  trays: {
    type: Array,
    default: () => []
  },
  storeSettings: {
    type: Object,
    default: () => ({})
  },
  recentSales: {
    type: Array,
    default: () => []
  }
});

// Barcode & Search State
const searchQuery = ref('');
const selectedCategory = ref('all');
const selectedTray = ref('all');
const barcodeInputRef = ref(null);

// Cart State
const cart = ref([]);
const discount = ref(0);
const notes = ref('');

// Customer Info State (Required for Official Surat Emas)
const customer = ref({
  name: '',
  phone: '',
  address: ''
});

// Payment Modal State
const isPaymentModalOpen = ref(false);
const paymentMethod = ref('cash');
const selectedBankId = ref(props.bankAccounts[0]?.id || null);
const amountPaid = ref(0);
const isSubmitting = ref(false);
const isRecentDrawerOpen = ref(false);

// Formatters
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

// Filtered Items from Store Catalog
const filteredItems = computed(() => {
  return props.items.filter(item => {
    // Exclude items already in cart
    const inCart = cart.value.some(c => c.id === item.id);
    if (inCart) return false;

    // Search query matching SKU or Name
    const matchesSearch = !searchQuery.value || 
      item.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      item.sku.toLowerCase().includes(searchQuery.value.toLowerCase());

    // Tray filter
    const matchesTray = selectedTray.value === 'all' || item.tray_id === selectedTray.value;

    // Category filter
    const matchesCategory = selectedCategory.value === 'all' || item.category?.slug === selectedCategory.value;

    return matchesSearch && matchesTray && matchesCategory;
  });
});

// Categories derived from available items
const categories = computed(() => {
  const cats = new Map();
  props.items.forEach(it => {
    if (it.category) {
      cats.set(it.category.slug, it.category.name);
    }
  });
  return Array.from(cats.entries()).map(([slug, name]) => ({ slug, name }));
});

// Cart Calculations
const totalWeight = computed(() => {
  return cart.value.reduce((acc, it) => acc + parseFloat(it.net_weight || 0), 0);
});

const subtotal = computed(() => {
  return cart.value.reduce((acc, it) => {
    const goldPrice = parseFloat(it.gold_price_per_gram || it.gold_rate?.sell_price_per_gram || 0);
    const crafting = parseFloat(it.crafting_fee || 0);
    const itemTotal = (parseFloat(it.net_weight || 0) * goldPrice) + crafting;
    return acc + itemTotal;
  }, 0);
});

const grandTotal = computed(() => {
  return Math.max(0, subtotal.value - (parseFloat(discount.value) || 0));
});

const changeDue = computed(() => {
  const paid = parseFloat(amountPaid.value) || 0;
  return Math.max(0, paid - grandTotal.value);
});

// Cart Actions
const addToCart = (item) => {
  const existing = cart.value.find(c => c.id === item.id);
  if (!existing) {
    cart.value.push({
      ...item,
      gold_price_per_gram: item.gold_rate?.sell_price_per_gram || 0,
      crafting_fee: item.crafting_fee || 0
    });
  }
  searchQuery.value = '';
  focusBarcode();
};

const removeFromCart = (index) => {
  cart.value.splice(index, 1);
};

const handleBarcodeEnter = () => {
  if (!searchQuery.value.trim()) return;
  const match = props.items.find(it => 
    it.sku.toLowerCase() === searchQuery.value.trim().toLowerCase()
  );
  if (match) {
    addToCart(match);
  }
};

const focusBarcode = () => {
  setTimeout(() => {
    barcodeInputRef.value?.focus();
  }, 50);
};

// Keyboard Shortcuts (F2: Search Barcode, F8: Checkout)
const handleKeydown = (e) => {
  if (e.key === 'F2') {
    e.preventDefault();
    focusBarcode();
  } else if (e.key === 'F8') {
    e.preventDefault();
    if (cart.value.length > 0) {
      openPaymentModal();
    }
  } else if (e.key === 'Escape') {
    isPaymentModalOpen.value = false;
    isRecentDrawerOpen.value = false;
  }
};

onMounted(() => {
  window.addEventListener('keydown', handleKeydown);
  focusBarcode();
});

onUnmounted(() => {
  window.removeEventListener('keydown', handleKeydown);
});

// Payment Handlers
const openPaymentModal = () => {
  if (cart.value.length === 0) return;
  amountPaid.value = grandTotal.value; // Default to exact amount
  isPaymentModalOpen.value = true;
};

const setQuickAmount = (val) => {
  amountPaid.value = val;
};

const submitCheckout = () => {
  if (!customer.value.name.trim()) {
    alert('Nama pelanggan wajib diisi untuk penerbitan Surat Jaminan Emas resmi!');
    return;
  }

  if (amountPaid.value < grandTotal.value && paymentMethod.value !== 'split') {
    alert('Jumlah pembayaran kurang dari total tagihan!');
    return;
  }

  isSubmitting.value = true;

  router.post(route('admin.pos.store'), {
    customer_name: customer.value.name,
    customer_phone: customer.value.phone,
    customer_address: customer.value.address,
    items: cart.value.map(it => ({
      id: it.id,
      gold_price_per_gram: it.gold_price_per_gram,
      crafting_fee: it.crafting_fee
    })),
    discount: discount.value,
    payment_method: paymentMethod.value,
    bank_account_id: paymentMethod.value !== 'cash' ? selectedBankId.value : null,
    amount_paid: amountPaid.value,
    notes: notes.value
  }, {
    onFinish: () => {
      isSubmitting.value = false;
    }
  });
};
</script>

<template>
  <Head title="Kasir POS Penjualan Emas - e-Emas" />

  <AdminLayout>
    <div class="space-y-6">
      <!-- Top POS Status Bar -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center">
            <ShoppingBag class="w-5 h-5" />
          </div>
          <div>
            <h1 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
              Terminal Kasir POS
              <span class="text-[10px] bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 px-2 py-0.5 rounded-full font-mono uppercase font-bold">Live Ready</span>
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400">
              Shortcut: <kbd class="px-1.5 py-0.5 bg-slate-100 dark:bg-slate-800 rounded font-mono text-[10px]">F2</kbd> Scan Barcode | <kbd class="px-1.5 py-0.5 bg-slate-100 dark:bg-slate-800 rounded font-mono text-[10px]">F8</kbd> Bayar Sekarang
            </p>
          </div>
        </div>

        <div class="flex items-center gap-3">
          <button 
            @click="isRecentDrawerOpen = true"
            class="px-3.5 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold flex items-center gap-2 transition-colors cursor-pointer"
          >
            <History class="w-4 h-4 text-amber-500" />
            <span>Riwayat Transaksi ({{ recentSales.length }})</span>
          </button>
        </div>
      </div>

      <!-- Main POS Grid: 7 Cols Left (Catalog & Scanner), 5 Cols Right (Cart & Checkout) -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- LEFT COLUMN: Product Catalog & Barcode Scanner (7 Cols) -->
        <div class="lg:col-span-7 space-y-4">
          
          <!-- Quick Barcode Input & Filters -->
          <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-3">
            <div class="relative">
              <Barcode class="w-5 h-5 text-amber-500 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" />
              <input 
                ref="barcodeInputRef"
                v-model="searchQuery"
                @keydown.enter="handleBarcodeEnter"
                type="text" 
                placeholder="Scan Barcode (SKU) atau ketik nama perhiasan... (Tekan F2)"
                class="w-full pl-11 pr-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm font-medium transition-all"
              />
            </div>

            <!-- Filter Pills (Baki & Kategori) -->
            <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs">
              <button 
                @click="selectedCategory = 'all'"
                :class="[
                  selectedCategory === 'all' 
                    ? 'bg-amber-500 text-slate-950 font-bold' 
                    : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 font-medium',
                  'px-3 py-1.5 rounded-lg shrink-0 transition-colors cursor-pointer'
                ]"
              >
                Semua Kategori
              </button>
              <button 
                v-for="cat in categories" 
                :key="cat.slug"
                @click="selectedCategory = cat.slug"
                :class="[
                  selectedCategory === cat.slug 
                    ? 'bg-amber-500 text-slate-950 font-bold' 
                    : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 font-medium',
                  'px-3 py-1.5 rounded-lg shrink-0 transition-colors cursor-pointer'
                ]"
              >
                {{ cat.name }}
              </button>
            </div>
          </div>

          <!-- Product Cards Grid -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-[600px] overflow-y-auto pr-1">
            <div 
              v-for="item in filteredItems" 
              :key="item.id"
              @click="addToCart(item)"
              class="group p-3.5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:border-amber-500/50 hover:shadow-md transition-all cursor-pointer flex flex-col justify-between"
            >
              <div>
                <div class="flex items-start justify-between gap-2 mb-2">
                  <span class="px-2 py-0.5 rounded-md bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-500/20 text-[10px] font-bold font-mono">
                    {{ item.gold_rate?.code || 'Emas' }}
                  </span>
                  <span class="text-[10px] text-slate-400 font-mono">
                    {{ item.tray?.code || '-' }}
                  </span>
                </div>
                <h4 class="text-xs font-bold text-slate-900 dark:text-white line-clamp-2 group-hover:text-amber-500 transition-colors">
                  {{ item.name }}
                </h4>
                <p class="text-[10px] text-slate-400 font-mono mt-0.5">{{ item.sku }}</p>
              </div>

              <div class="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between">
                <div>
                  <span class="text-[10px] text-slate-400 block">Berat Bersih</span>
                  <span class="text-xs font-bold text-slate-800 dark:text-slate-200 font-mono">
                    {{ formatWeight(item.net_weight) }}
                  </span>
                </div>
                <div class="text-right">
                  <span class="text-[10px] text-slate-400 block">Taksiran Jual</span>
                  <span class="text-xs font-bold text-amber-600 dark:text-amber-400 font-mono">
                    {{ formatRupiah(item.total_price) }}
                  </span>
                </div>
              </div>
            </div>

            <div v-if="filteredItems.length === 0" class="col-span-full py-12 text-center text-slate-400">
              <Gem class="w-10 h-10 mx-auto text-slate-300 dark:text-slate-700 mb-2 stroke-[1.5]" />
              <p class="text-xs">Tidak ada perhiasan yang sesuai pencarian atau semua item sudah di keranjang.</p>
            </div>
          </div>
        </div>

        <!-- RIGHT COLUMN: Active Cart & Customer Data (5 Cols) -->
        <div class="lg:col-span-5 space-y-4">
          <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col justify-between min-h-[650px]">
            
            <div class="space-y-4">
              <!-- Cart Header -->
              <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
                <div class="flex items-center gap-2">
                  <h3 class="text-sm font-bold text-slate-900 dark:text-white">Keranjang Emas</h3>
                  <span class="px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-600 text-[10px] font-bold font-mono">
                    {{ cart.length }} Item
                  </span>
                </div>
                <button 
                  v-if="cart.length > 0"
                  @click="cart = []" 
                  class="text-xs text-rose-500 hover:underline cursor-pointer"
                >
                  Kosongkan
                </button>
              </div>

              <!-- Cart Items List -->
              <div class="space-y-2.5 max-h-[260px] overflow-y-auto pr-1">
                <div 
                  v-for="(item, idx) in cart" 
                  :key="item.id"
                  class="p-3 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 space-y-2"
                >
                  <div class="flex items-start justify-between gap-2">
                    <div class="overflow-hidden">
                      <p class="text-xs font-bold text-slate-900 dark:text-white truncate">{{ item.name }}</p>
                      <p class="text-[10px] text-slate-400 font-mono">{{ item.sku }} • {{ item.gold_rate?.code }}</p>
                    </div>
                    <button 
                      @click="removeFromCart(idx)" 
                      class="text-slate-400 hover:text-rose-500 transition-colors p-1"
                    >
                      <Trash2 class="w-3.5 h-3.5" />
                    </button>
                  </div>

                  <!-- Inline Item Pricing Configuration -->
                  <div class="grid grid-cols-3 gap-2 text-[10px] pt-1 border-t border-slate-200/60 dark:border-slate-800/60">
                    <div>
                      <span class="text-slate-400 block">Gramasi Bersih</span>
                      <span class="font-bold text-slate-800 dark:text-slate-200 font-mono">{{ formatWeight(item.net_weight) }}</span>
                    </div>
                    <div>
                      <span class="text-slate-400 block">Ongkos Bikin</span>
                      <input 
                        v-model.number="item.crafting_fee" 
                        type="number" 
                        class="w-full px-1.5 py-0.5 rounded bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white font-mono text-[10px]"
                      />
                    </div>
                    <div class="text-right">
                      <span class="text-slate-400 block">Subtotal</span>
                      <span class="font-bold text-amber-600 dark:text-amber-400 font-mono text-[11px]">
                        {{ formatRupiah((item.net_weight * (item.gold_price_per_gram || item.gold_rate?.sell_price_per_gram || 0)) + item.crafting_fee) }}
                      </span>
                    </div>
                  </div>
                </div>

                <div v-if="cart.length === 0" class="py-8 text-center text-slate-400">
                  <ShoppingBag class="w-8 h-8 mx-auto text-slate-300 dark:text-slate-700 mb-2 stroke-[1.5]" />
                  <p class="text-xs">Keranjang masih kosong.</p>
                  <p class="text-[10px] text-slate-500">Scan barcode atau pilih perhiasan di sebelah kiri.</p>
                </div>
              </div>

              <!-- Customer Info Section (Mandatory for Surat Jaminan Emas) -->
              <div class="pt-3 border-t border-slate-200 dark:border-slate-800 space-y-2.5">
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 flex items-center gap-1.5 font-mono">
                  <User class="w-3.5 h-3.5 text-amber-500" />
                  Identitas Pembeli (Surat Emas)
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                  <input 
                    v-model="customer.name"
                    type="text" 
                    placeholder="Nama Lengkap Pembeli *" 
                    required
                    class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-xs text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-1 focus:ring-amber-500"
                  />
                  <input 
                    v-model="customer.phone"
                    type="text" 
                    placeholder="No. WhatsApp / HP" 
                    class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-xs text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-1 focus:ring-amber-500"
                  />
                </div>
                <input 
                  v-model="customer.address"
                  type="text" 
                  placeholder="Alamat / Kota Domisili" 
                  class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-xs text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-1 focus:ring-amber-500"
                />
              </div>
            </div>

            <!-- Total Summary & Checkout Button -->
            <div class="pt-4 border-t border-slate-200 dark:border-slate-800 space-y-3 mt-4">
              <div class="space-y-1.5 text-xs">
                <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
                  <span>Total Gramasi Bersih</span>
                  <span class="font-bold text-slate-900 dark:text-white font-mono">{{ formatWeight(totalWeight) }}</span>
                </div>
                <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
                  <span>Subtotal Emas & Ongkos</span>
                  <span class="font-bold text-slate-900 dark:text-white font-mono">{{ formatRupiah(subtotal) }}</span>
                </div>
                <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
                  <span>Potongan / Diskon</span>
                  <input 
                    v-model.number="discount" 
                    type="number" 
                    class="w-28 px-2 py-0.5 rounded bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-right font-mono text-xs"
                  />
                </div>
              </div>

              <div class="p-3.5 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-between">
                <div>
                  <span class="text-[10px] uppercase tracking-wider text-amber-700 dark:text-amber-400 font-bold block">Grand Total</span>
                  <span class="text-xs text-slate-500">Harga Final Nota</span>
                </div>
                <span class="text-xl font-extrabold text-amber-600 dark:text-amber-400 font-mono tracking-tight">
                  {{ formatRupiah(grandTotal) }}
                </span>
              </div>

              <button 
                @click="openPaymentModal"
                :disabled="cart.length === 0"
                class="w-full py-3.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-bold text-sm tracking-wide shadow-lg shadow-amber-500/20 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 cursor-pointer transition-all"
              >
                <CreditCard class="w-4 h-4" />
                <span>Bayar Sekarang (F8)</span>
                <ArrowRight class="w-4 h-4" />
              </button>
            </div>

          </div>
        </div>
      </div>

      <!-- Payment & Multi-Method Modal -->
      <div 
        v-if="isPaymentModalOpen" 
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm"
      >
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-2xl max-w-lg w-full overflow-hidden animate-in fade-in zoom-in-95 duration-200">
          <!-- Modal Header -->
          <div class="p-6 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
            <div>
              <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <CreditCard class="w-5 h-5 text-amber-500" />
                Pembayaran & Cetak Surat Emas
              </h3>
              <p class="text-xs text-slate-500 dark:text-slate-400">Total Tagihan: <b class="text-amber-600 dark:text-amber-400 font-mono">{{ formatRupiah(grandTotal) }}</b></p>
            </div>
            <button 
              @click="isPaymentModalOpen = false" 
              class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-lg cursor-pointer"
            >
              <X class="w-5 h-5" />
            </button>
          </div>

          <!-- Modal Body -->
          <div class="p-6 space-y-5">
            <!-- Payment Method Selector -->
            <div>
              <label class="text-xs font-bold text-slate-700 dark:text-slate-300 mb-2 block">Metode Pembayaran</label>
              <div class="grid grid-cols-4 gap-2">
                <button 
                  type="button"
                  @click="paymentMethod = 'cash'"
                  :class="[
                    paymentMethod === 'cash' 
                      ? 'bg-amber-500/15 border-amber-500 text-amber-700 dark:text-amber-300 font-bold' 
                      : 'bg-slate-50 dark:bg-slate-950 border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400',
                    'p-2.5 rounded-xl border text-center text-xs flex flex-col items-center gap-1 transition-all cursor-pointer'
                  ]"
                >
                  <Banknote class="w-4 h-4" />
                  <span>Tunai</span>
                </button>

                <button 
                  type="button"
                  @click="paymentMethod = 'transfer'"
                  :class="[
                    paymentMethod === 'transfer' 
                      ? 'bg-amber-500/15 border-amber-500 text-amber-700 dark:text-amber-300 font-bold' 
                      : 'bg-slate-50 dark:bg-slate-950 border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400',
                    'p-2.5 rounded-xl border text-center text-xs flex flex-col items-center gap-1 transition-all cursor-pointer'
                  ]"
                >
                  <Building2 class="w-4 h-4" />
                  <span>Transfer</span>
                </button>

                <button 
                  type="button"
                  @click="paymentMethod = 'qris'"
                  :class="[
                    paymentMethod === 'qris' 
                      ? 'bg-amber-500/15 border-amber-500 text-amber-700 dark:text-amber-300 font-bold' 
                      : 'bg-slate-50 dark:bg-slate-950 border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400',
                    'p-2.5 rounded-xl border text-center text-xs flex flex-col items-center gap-1 transition-all cursor-pointer'
                  ]"
                >
                  <QrCode class="w-4 h-4" />
                  <span>QRIS</span>
                </button>

                <button 
                  type="button"
                  @click="paymentMethod = 'debit'"
                  :class="[
                    paymentMethod === 'debit' 
                      ? 'bg-amber-500/15 border-amber-500 text-amber-700 dark:text-amber-300 font-bold' 
                      : 'bg-slate-50 dark:bg-slate-950 border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400',
                    'p-2.5 rounded-xl border text-center text-xs flex flex-col items-center gap-1 transition-all cursor-pointer'
                  ]"
                >
                  <CreditCard class="w-4 h-4" />
                  <span>EDC Debit</span>
                </button>
              </div>
            </div>

            <!-- Destination Bank Account (if non-cash) -->
            <div v-if="paymentMethod !== 'cash'" class="space-y-1.5">
              <label class="text-xs font-bold text-slate-700 dark:text-slate-300 block">Pilih Rekening Tujuan Toko</label>
              <select 
                v-model="selectedBankId"
                class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-amber-500 font-medium"
              >
                <option v-for="b in bankAccounts" :key="b.id" :value="b.id">
                  {{ b.bank_name }} - {{ b.account_number }} (a.n {{ b.account_holder }})
                </option>
              </select>
            </div>

            <!-- Amount Paid Input & Quick Chips -->
            <div class="space-y-2">
              <label class="text-xs font-bold text-slate-700 dark:text-slate-300 block">Jumlah Uang Diterima (Rp)</label>
              <input 
                v-model.number="amountPaid"
                type="number" 
                class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white font-mono font-bold text-lg focus:outline-none focus:ring-2 focus:ring-amber-500"
              />

              <!-- Quick Cash Chips -->
              <div v-if="paymentMethod === 'cash'" class="flex items-center gap-1.5 flex-wrap pt-1">
                <button 
                  type="button"
                  @click="setQuickAmount(grandTotal)" 
                  class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 font-mono text-[10px] font-bold cursor-pointer"
                >
                  Uang Pas
                </button>
                <button 
                  v-for="amt in [1000000, 2000000, 5000000, 10000000]" 
                  :key="amt"
                  type="button"
                  @click="setQuickAmount(amt)" 
                  class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 font-mono text-[10px] cursor-pointer"
                >
                  {{ formatRupiah(amt) }}
                </button>
              </div>
            </div>

            <!-- Change Due Display -->
            <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 flex items-center justify-between text-xs">
              <span class="text-slate-500">Kembalian</span>
              <span class="text-base font-bold text-emerald-600 dark:text-emerald-400 font-mono">
                {{ formatRupiah(changeDue) }}
              </span>
            </div>
          </div>

          <!-- Modal Footer -->
          <div class="p-6 border-t border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/50 flex items-center gap-3">
            <button 
              type="button"
              @click="isPaymentModalOpen = false"
              class="flex-1 py-3 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold text-xs cursor-pointer"
            >
              Batal
            </button>
            <button 
              type="button"
              @click="submitCheckout"
              :disabled="isSubmitting"
              class="flex-1 py-3 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs uppercase tracking-wider flex items-center justify-center gap-2 shadow-lg shadow-amber-500/20 disabled:opacity-50 cursor-pointer transition-all"
            >
              <Check class="w-4 h-4" />
              <span>{{ isSubmitting ? 'Memproses...' : 'Selesaikan Transaksi' }}</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Recent Transactions Drawer -->
      <div 
        v-if="isRecentDrawerOpen" 
        class="fixed inset-0 z-50 flex justify-end bg-slate-950/70 backdrop-blur-sm"
      >
        <div class="w-full max-w-md bg-white dark:bg-slate-900 h-full p-6 shadow-2xl flex flex-col justify-between overflow-y-auto">
          <div class="space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
              <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <History class="w-4 h-4 text-amber-500" />
                Riwayat Transaksi Terakhir
              </h3>
              <button @click="isRecentDrawerOpen = false" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                <X class="w-5 h-5" />
              </button>
            </div>

            <div class="space-y-3">
              <div 
                v-for="sale in recentSales" 
                :key="sale.id"
                class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 space-y-2 text-xs"
              >
                <div class="flex items-center justify-between">
                  <span class="font-bold text-slate-900 dark:text-white font-mono">{{ sale.invoice_number }}</span>
                  <span class="text-[10px] text-slate-400 font-mono">{{ new Date(sale.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) }}</span>
                </div>
                <div class="flex items-center justify-between text-slate-500 text-[11px]">
                  <span>{{ sale.customer_name }}</span>
                  <span class="font-bold text-amber-600 dark:text-amber-400 font-mono">{{ formatRupiah(sale.grand_total) }}</span>
                </div>
                <div class="flex items-center gap-2 pt-2 border-t border-slate-200/60 dark:border-slate-800/60">
                  <a 
                    :href="route('admin.pos.invoice', sale.id)" 
                    target="_blank"
                    class="flex-1 py-1 rounded bg-slate-200 dark:bg-slate-800 hover:bg-slate-300 text-center text-[10px] font-semibold text-slate-700 dark:text-slate-300"
                  >
                    Struk Thermal
                  </a>
                  <a 
                    :href="route('admin.pos.certificate', sale.id)" 
                    target="_blank"
                    class="flex-1 py-1 rounded bg-amber-500/10 hover:bg-amber-500/20 text-center text-[10px] font-bold text-amber-700 dark:text-amber-400 border border-amber-500/20"
                  >
                    Surat Emas
                  </a>
                </div>
              </div>

              <div v-if="recentSales.length === 0" class="py-12 text-center text-slate-400 text-xs">
                Belum ada transaksi hari ini.
              </div>
            </div>
          </div>

          <button 
            @click="isRecentDrawerOpen = false" 
            class="w-full py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold text-xs mt-4 cursor-pointer"
          >
            Tutup
          </button>
        </div>
      </div>

    </div>
  </AdminLayout>
</template>
