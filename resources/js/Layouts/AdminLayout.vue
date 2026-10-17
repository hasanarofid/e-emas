<script setup>
import { ref, computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { 
  LayoutDashboard, 
  Menu, 
  X, 
  LogOut, 
  Gem, 
  Store, 
  TrendingUp, 
  Layers, 
  Settings as SettingsIcon, 
  ShoppingBag, 
  ArrowRightLeft, 
  ClipboardCheck, 
  BarChart3,
  Sliders,
  Box,
  Users,
  FileText,
  Zap,
  CreditCard,
  Repeat,
  Wrench,
  Scale,
  UserCheck,
  Calculator,
  ChevronDown,
  ChevronRight,
  Search,
  Circle,
  Building2,
  Award,
  ShoppingCart
} from 'lucide-vue-next';
import ThemeToggle from '@/Components/ThemeToggle.vue';

const page = usePage();
const user = page.props.auth?.user || {};

const isSidebarOpen = ref(false);
const isSidebarCollapsed = ref(false);
const menuSearch = ref('');

const isRoute = (pattern) => {
  try {
    return route().current(pattern);
  } catch (e) {
    return false;
  }
};

// State accordion: hanya terbuka ke menu yang sedang aktif/tertuju. Saat di Dashboard, semua tertutup.
const openGroups = ref({
  utilitas: isRoute('admin.settings.*'),
  persediaan: isRoute('admin.brands.*') || isRoute('admin.units.*') || isRoute('admin.warehouses.*') || isRoute('admin.gold-rates.*') || isRoute('admin.items.*') || isRoute('admin.trays.*') || isRoute('admin.inventory.*') || isRoute('admin.stock-audits.*'),
  pelanggan: isRoute('admin.customers.*'),
  pembelian: isRoute('admin.purchases.*') || isRoute('admin.suppliers.*') || isRoute('admin.buyback-deductions.*'),
  pembelianCepat: isRoute('admin.buybacks.*'),
  penjualan: isRoute('admin.pos.*') || isRoute('admin.sales.*'),
  gadai: isRoute('admin.pawns.*'),
  tukarTambah: isRoute('admin.trade-ins.*'),
  servis: isRoute('admin.services.*'),
  timbangBaki: false,
  pegawai: isRoute('admin.employees.*'),
  keuangan: isRoute('admin.finance.*'),
});

const toggleGroup = (groupKey) => {
  openGroups.value[groupKey] = !openGroups.value[groupKey];
};

const logout = () => {
  router.post(route('logout'));
};
</script>

<template>
  <div class="min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 font-sans antialiased relative selection:bg-amber-500 selection:text-slate-950 flex transition-colors duration-300">
    <!-- Mobile Sidebar Backdrop -->
    <div 
      v-if="isSidebarOpen" 
      @click="isSidebarOpen = false" 
      class="fixed inset-0 z-40 bg-slate-950/80 backdrop-blur-sm lg:hidden transition-opacity"
    ></div>

    <!-- Sidebar Multi-Level Sitokmas Edition -->
    <aside 
      :class="[
        isSidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
        isSidebarCollapsed ? 'lg:w-20' : 'lg:w-72',
        'fixed top-0 bottom-0 left-0 z-50 w-72 bg-[#1B1B38] text-slate-200 border-r border-[#2A2A4E] transition-all duration-300 ease-in-out flex flex-col justify-between shadow-xl'
      ]"
    >
      <div class="flex flex-col h-full overflow-hidden">
        <!-- Sidebar Header / Logo -->
        <div class="flex items-center justify-between h-16 px-5 border-b border-[#2A2A4E] shrink-0 bg-[#15152D]">
          <Link :href="route('admin.dashboard')" class="flex items-center gap-3 group">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-amber-500 via-amber-300 to-yellow-100 p-0.5 shadow-md shadow-amber-500/20 shrink-0">
              <div class="w-full h-full bg-[#15152D] rounded-[10px] flex items-center justify-center">
                <Gem class="w-4 h-4 text-amber-400" />
              </div>
            </div>
            <div>
              <h1 class="text-sm font-bold tracking-tight text-white flex items-center gap-1.5">
                POS Emas
                <span class="text-[9px] bg-amber-500/20 text-amber-300 border border-amber-500/30 px-1.5 py-0.2 rounded font-mono">PRO</span>
              </h1>
              <p class="text-[10px] text-slate-400 truncate">Toko Mas Permata Abadi</p>
            </div>
          </Link>
          <button 
            @click="isSidebarOpen = false" 
            class="p-1.5 text-slate-400 hover:text-white rounded-lg hover:bg-[#232348] lg:hidden"
          >
            <X class="w-5 h-5" />
          </button>
        </div>

        <!-- Search Bar Menu -->
        <div class="px-4 py-3 border-b border-[#2A2A4E] bg-[#171732]">
          <div class="relative">
            <Search class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" />
            <input 
              v-model="menuSearch"
              type="text" 
              placeholder="Cari menu..."
              class="w-full pl-8 pr-3 py-1.5 bg-[#1F1F40] border border-[#2E2E56] rounded-lg text-xs text-white placeholder-slate-400 focus:outline-none focus:border-amber-400 transition-colors"
            />
          </div>
        </div>

        <!-- Navigation Scrollable Area -->
        <nav class="flex-1 overflow-y-auto px-3 py-3 space-y-1 text-xs select-none scrollbar-thin scrollbar-thumb-slate-700">
          
          <!-- 1. DASHBOARD -->
          <Link 
            :href="route('admin.dashboard')"
            :class="[
              isRoute('admin.dashboard') ? 'bg-amber-500 text-slate-950 font-bold shadow-md shadow-amber-500/20' : 'text-slate-300 hover:bg-[#25254B] hover:text-white',
              'flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all'
            ]"
          >
            <LayoutDashboard class="w-4 h-4 shrink-0" />
            <span class="truncate">Dashboard</span>
          </Link>

          <!-- 2. UTILITAS -->
          <div>
            <button 
              @click="toggleGroup('utilitas')" 
              type="button"
              class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-slate-300 hover:bg-[#25254B] hover:text-white transition-all cursor-pointer"
            >
              <div class="flex items-center gap-3">
                <Sliders class="w-4 h-4 text-slate-400 shrink-0" />
                <span>Utilitas</span>
              </div>
              <ChevronDown :class="[openGroups.utilitas ? 'rotate-180' : '', 'w-3.5 h-3.5 text-slate-400 transition-transform']" />
            </button>
            <div v-show="openGroups.utilitas" class="pl-8 pr-2 py-1 space-y-1 border-l border-[#2E2E56] ml-4 my-1">
              <Link :href="route('admin.settings.index')" class="flex items-center gap-2 py-1.5 px-2 rounded-lg text-slate-400 hover:text-white hover:bg-[#25254B]">
                <Circle class="w-1.5 h-1.5 fill-current" />
                <span>Profil Toko</span>
              </Link>
              <Link :href="route('admin.settings.index')" class="flex items-center gap-2 py-1.5 px-2 rounded-lg text-slate-400 hover:text-white hover:bg-[#25254B]">
                <Circle class="w-1.5 h-1.5 fill-current" />
                <span>Pengaturan Toko</span>
              </Link>
              <Link :href="route('admin.settings.index')" class="flex items-center gap-2 py-1.5 px-2 rounded-lg text-slate-400 hover:text-white hover:bg-[#25254B]">
                <Circle class="w-1.5 h-1.5 fill-current" />
                <span>Bank Transfer</span>
              </Link>
            </div>
          </div>

          <!-- 3. PERSEDIAAN (BADGE 18, 57) -->
          <div>
            <button 
              @click="toggleGroup('persediaan')" 
              type="button"
              class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-slate-300 hover:bg-[#25254B] hover:text-white transition-all cursor-pointer"
            >
              <div class="flex items-center gap-3">
                <Box class="w-4 h-4 text-slate-400 shrink-0" />
                <span>Persediaan</span>
                <div class="flex items-center gap-1">
                  <span class="px-1.5 py-0.2 rounded-full bg-rose-500/80 text-[10px] text-white font-mono font-bold">18</span>
                  <span class="px-1.5 py-0.2 rounded-full bg-amber-500/80 text-[10px] text-slate-950 font-mono font-bold">57</span>
                </div>
              </div>
              <ChevronDown :class="[openGroups.persediaan ? 'rotate-180' : '', 'w-3.5 h-3.5 text-slate-400 transition-transform']" />
            </button>
            <div v-show="openGroups.persediaan" class="pl-8 pr-2 py-1 space-y-1 border-l border-[#2E2E56] ml-4 my-1">
              <Link :href="route('admin.brands.index')" :class="[isRoute('admin.brands.*') ? 'text-amber-400 font-semibold bg-[#25254B]' : 'text-slate-400 hover:text-white hover:bg-[#25254B]', 'flex items-center gap-2 py-1.5 px-2 rounded-lg']">
                <Circle class="w-1.5 h-1.5 fill-current" />
                <span>Brand & Merk</span>
              </Link>
              <Link :href="route('admin.units.index')" :class="[isRoute('admin.units.*') ? 'text-amber-400 font-semibold bg-[#25254B]' : 'text-slate-400 hover:text-white hover:bg-[#25254B]', 'flex items-center gap-2 py-1.5 px-2 rounded-lg']">
                <Circle class="w-1.5 h-1.5 fill-current" />
                <span>Satuan Barang</span>
              </Link>
              <Link :href="route('admin.warehouses.index')" :class="[isRoute('admin.warehouses.*') ? 'text-amber-400 font-semibold bg-[#25254B]' : 'text-slate-400 hover:text-white hover:bg-[#25254B]', 'flex items-center gap-2 py-1.5 px-2 rounded-lg']">
                <Circle class="w-1.5 h-1.5 fill-current" />
                <span>Gudang & Brankas</span>
              </Link>
              <Link :href="route('admin.gold-rates.index')" :class="[isRoute('admin.gold-rates.*') ? 'text-amber-400 font-semibold bg-[#25254B]' : 'text-slate-400 hover:text-white hover:bg-[#25254B]', 'flex items-center gap-2 py-1.5 px-2 rounded-lg']">
                <Circle class="w-1.5 h-1.5 fill-current" />
                <span>Kadar & Kurs Emas</span>
              </Link>
              <Link :href="route('admin.items.index')" :class="[isRoute('admin.items.*') ? 'text-amber-400 font-semibold bg-[#25254B]' : 'text-slate-400 hover:text-white hover:bg-[#25254B]', 'flex items-center gap-2 py-1.5 px-2 rounded-lg']">
                <Circle class="w-1.5 h-1.5 fill-current" />
                <span>Data Barang & Barcode</span>
              </Link>
              <Link :href="route('admin.trays.index')" :class="[isRoute('admin.trays.*') ? 'text-amber-400 font-semibold bg-[#25254B]' : 'text-slate-400 hover:text-white hover:bg-[#25254B]', 'flex items-center gap-2 py-1.5 px-2 rounded-lg']">
                <Circle class="w-1.5 h-1.5 fill-current" />
                <span>Baki & Etalase</span>
              </Link>
              <Link :href="route('admin.inventory.mutations')" :class="[isRoute('admin.inventory.mutations') ? 'text-amber-400 font-semibold bg-[#25254B]' : 'text-slate-400 hover:text-white hover:bg-[#25254B]', 'flex items-center gap-2 py-1.5 px-2 rounded-lg']">
                <Circle class="w-1.5 h-1.5 fill-current" />
                <span>Mutasi Baki Barang</span>
              </Link>
              <Link :href="route('admin.stock-audits.index')" :class="[isRoute('admin.stock-audits.*') ? 'text-amber-400 font-semibold bg-[#25254B]' : 'text-slate-400 hover:text-white hover:bg-[#25254B]', 'flex items-center gap-2 py-1.5 px-2 rounded-lg']">
                <Circle class="w-1.5 h-1.5 fill-current" />
                <span>Stok Opname Baki</span>
              </Link>
              <Link :href="route('admin.inventory.movements')" :class="[isRoute('admin.inventory.movements') ? 'text-amber-400 font-semibold bg-[#25254B]' : 'text-slate-400 hover:text-white hover:bg-[#25254B]', 'flex items-center gap-2 py-1.5 px-2 rounded-lg']">
                <Circle class="w-1.5 h-1.5 fill-current" />
                <span>Keluar Masuk Barang</span>
              </Link>
              <Link :href="route('admin.reports.index')" :class="[isRoute('admin.reports.*') ? 'text-amber-400 font-semibold bg-[#25254B]' : 'text-slate-400 hover:text-white hover:bg-[#25254B]', 'flex items-center gap-2 py-1.5 px-2 rounded-lg']">
                <Circle class="w-1.5 h-1.5 fill-current" />
                <span>Laporan Persediaan</span>
              </Link>
            </div>
          </div>

          <!-- 4. PELANGGAN -->
          <div>
            <button 
              @click="toggleGroup('pelanggan')" 
              type="button"
              class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-slate-300 hover:bg-[#25254B] hover:text-white transition-all cursor-pointer"
            >
              <div class="flex items-center gap-3">
                <Users class="w-4 h-4 text-slate-400 shrink-0" />
                <span>Pelanggan</span>
              </div>
              <ChevronDown :class="[openGroups.pelanggan ? 'rotate-180' : '', 'w-3.5 h-3.5 text-slate-400 transition-transform']" />
            </button>
            <div v-show="openGroups.pelanggan" class="pl-8 pr-2 py-1 space-y-1 border-l border-[#2E2E56] ml-4 my-1">
              <Link :href="route('admin.customers.create')" :class="[isRoute('admin.customers.create') ? 'text-amber-400 font-semibold bg-[#25254B]' : 'text-slate-400 hover:text-white hover:bg-[#25254B]', 'flex items-center gap-2 py-1.5 px-2 rounded-lg']">
                <Circle class="w-1.5 h-1.5 fill-current" />
                <span>Tambah Pelanggan</span>
              </Link>
              <Link :href="route('admin.customers.index')" :class="[isRoute('admin.customers.index') ? 'text-amber-400 font-semibold bg-[#25254B]' : 'text-slate-400 hover:text-white hover:bg-[#25254B]', 'flex items-center gap-2 py-1.5 px-2 rounded-lg']">
                <Circle class="w-1.5 h-1.5 fill-current" />
                <span>Data Pelanggan</span>
              </Link>
              <Link :href="route('admin.customers.index')" class="flex items-center gap-2 py-1.5 px-2 rounded-lg text-slate-400 hover:text-white hover:bg-[#25254B]">
                <Circle class="w-1.5 h-1.5 fill-current" />
                <span>Point & Member</span>
              </Link>
            </div>
          </div>

          <!-- 5. PEMBELIAN -->
          <div>
            <button 
              @click="toggleGroup('pembelian')" 
              type="button"
              class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-slate-300 hover:bg-[#25254B] hover:text-white transition-all cursor-pointer"
            >
              <div class="flex items-center gap-3">
                <FileText class="w-4 h-4 text-slate-400 shrink-0" />
                <span>Pembelian</span>
              </div>
              <ChevronDown :class="[openGroups.pembelian ? 'rotate-180' : '', 'w-3.5 h-3.5 text-slate-400 transition-transform']" />
            </button>
            <div v-show="openGroups.pembelian" class="pl-8 pr-2 py-1 space-y-1 border-l border-[#2E2E56] ml-4 my-1">
              <Link :href="route('admin.suppliers.index')" :class="[isRoute('admin.suppliers.*') ? 'text-amber-400 font-semibold bg-[#25254B]' : 'text-slate-400 hover:text-white hover:bg-[#25254B]', 'flex items-center gap-2 py-1.5 px-2 rounded-lg']">
                <Circle class="w-1.5 h-1.5 fill-current" />
                <span>Supplier & Pabrik Emas</span>
              </Link>
              <Link :href="route('admin.purchases.index')" :class="[isRoute('admin.purchases.*') ? 'text-amber-400 font-semibold bg-[#25254B]' : 'text-slate-400 hover:text-white hover:bg-[#25254B]', 'flex items-center gap-2 py-1.5 px-2 rounded-lg']">
                <Circle class="w-1.5 h-1.5 fill-current" />
                <span>Faktur Pembelian</span>
              </Link>
              <Link :href="route('admin.buyback-deductions.index')" :class="[isRoute('admin.buyback-deductions.*') ? 'text-amber-400 font-semibold bg-[#25254B]' : 'text-slate-400 hover:text-white hover:bg-[#25254B]', 'flex items-center gap-2 py-1.5 px-2 rounded-lg']">
                <Circle class="w-1.5 h-1.5 fill-current" />
                <span>Potongan Master Buyback</span>
              </Link>
            </div>
          </div>

          <!-- 6. PEMBELIAN CEPAT (ACCORDION SITOKMAS EDITION) -->
          <div>
            <button 
              @click="toggleGroup('pembelianCepat')" 
              type="button"
              class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-slate-300 hover:bg-[#25254B] hover:text-white transition-all cursor-pointer"
            >
              <div class="flex items-center gap-3">
                <ShoppingCart class="w-4 h-4 text-slate-400 shrink-0" />
                <span>Pembelian Cepat</span>
              </div>
              <ChevronDown :class="[openGroups.pembelianCepat ? 'rotate-180' : '', 'w-3.5 h-3.5 text-slate-400 transition-transform']" />
            </button>
            <div v-show="openGroups.pembelianCepat" class="pl-8 pr-2 py-1 space-y-1 border-l border-[#2E2E56] ml-4 my-1">
              <Link 
                :href="route('admin.buybacks.index')" 
                :class="[isRoute('admin.buybacks.index') ? 'text-amber-400 font-semibold bg-[#25254B]' : 'text-slate-400 hover:text-white hover:bg-[#25254B]', 'flex items-center gap-2 py-1.5 px-2 rounded-lg']"
              >
                <Circle class="w-1.5 h-1.5 fill-current" />
                <span>Data Pembelian Cepat</span>
              </Link>
              <Link 
                :href="route('admin.buybacks.create')" 
                :class="[isRoute('admin.buybacks.create') ? 'text-amber-400 font-semibold bg-[#25254B]' : 'text-slate-400 hover:text-white hover:bg-[#25254B]', 'flex items-center gap-2 py-1.5 px-2 rounded-lg']"
              >
                <Circle class="w-1.5 h-1.5 fill-current" />
                <span>Pembelian Baru</span>
              </Link>
              <Link 
                :href="route('admin.buybacks.index')" 
                class="flex items-center gap-2 py-1.5 px-2 rounded-lg text-slate-400 hover:text-white hover:bg-[#25254B]"
              >
                <Circle class="w-1.5 h-1.5 fill-current" />
                <span>Laporan Pembelian Cepat</span>
              </Link>
              <Link 
                :href="route('admin.buybacks.index', { period: 'daily' })" 
                class="flex items-center gap-2 py-1.5 px-2 rounded-lg text-slate-400 hover:text-white hover:bg-[#25254B]"
              >
                <Circle class="w-1.5 h-1.5 fill-current" />
                <span>Pembelian Harian</span>
              </Link>
              <Link 
                :href="route('admin.buybacks.index', { period: 'monthly' })" 
                class="flex items-center gap-2 py-1.5 px-2 rounded-lg text-slate-400 hover:text-white hover:bg-[#25254B]"
              >
                <Circle class="w-1.5 h-1.5 fill-current" />
                <span>Pembelian Bulanan</span>
              </Link>
            </div>
          </div>

          <!-- 7. PENJUALAN (POS KASIR RESMI) -->
          <div>
            <button 
              @click="toggleGroup('penjualan')" 
              type="button"
              class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-slate-300 hover:bg-[#25254B] hover:text-white transition-all cursor-pointer"
            >
              <div class="flex items-center gap-3">
                <ShoppingBag class="w-4 h-4 text-amber-400 shrink-0" />
                <span class="font-semibold text-amber-300">Penjualan (POS)</span>
              </div>
              <ChevronDown :class="[openGroups.penjualan ? 'rotate-180' : '', 'w-3.5 h-3.5 text-slate-400 transition-transform']" />
            </button>
            <div v-show="openGroups.penjualan" class="pl-8 pr-2 py-1 space-y-1 border-l border-[#2E2E56] ml-4 my-1">
              <Link :href="route('admin.pos.index')" :class="[isRoute('admin.pos.*') ? 'text-amber-300 font-bold bg-amber-500/20 border border-amber-500/30' : 'text-slate-400 hover:text-white', 'flex items-center gap-2 py-1.5 px-2 rounded-lg']">
                <Circle class="w-1.5 h-1.5 fill-current text-amber-400" />
                <span>Kasir POS & Cetak Nota</span>
              </Link>
              <Link :href="route('admin.reports.index')" class="flex items-center gap-2 py-1.5 px-2 rounded-lg text-slate-400 hover:text-white hover:bg-[#25254B]">
                <Circle class="w-1.5 h-1.5 fill-current" />
                <span>Data & Laporan Penjualan</span>
              </Link>
            </div>
          </div>

          <!-- 8. GADAI PERHIASAN -->
          <div>
            <button 
              @click="toggleGroup('gadai')" 
              type="button"
              class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-slate-300 hover:bg-[#25254B] hover:text-white transition-all cursor-pointer"
            >
              <div class="flex items-center gap-3">
                <CreditCard class="w-4 h-4 text-slate-400 shrink-0" />
                <span>Gadai Perhiasan</span>
                <span class="px-1.5 py-0.2 rounded-full bg-slate-700 text-[10px] text-slate-300 font-mono">0</span>
              </div>
              <ChevronDown :class="[openGroups.gadai ? 'rotate-180' : '', 'w-3.5 h-3.5 text-slate-400 transition-transform']" />
            </button>
            <div v-show="openGroups.gadai" class="pl-8 pr-2 py-1 space-y-1 border-l border-[#2E2E56] ml-4 my-1">
              <Link :href="route('admin.pawns.create')" :class="[isRoute('admin.pawns.create') ? 'text-amber-400 font-semibold bg-[#25254B]' : 'text-slate-400 hover:text-white hover:bg-[#25254B]', 'flex items-center gap-2 py-1.5 px-2 rounded-lg']">
                <Circle class="w-1.5 h-1.5 fill-current" />
                <span>Gadai Baru</span>
              </Link>
              <Link :href="route('admin.pawns.index')" :class="[isRoute('admin.pawns.index') ? 'text-amber-400 font-semibold bg-[#25254B]' : 'text-slate-400 hover:text-white hover:bg-[#25254B]', 'flex items-center gap-2 py-1.5 px-2 rounded-lg']">
                <Circle class="w-1.5 h-1.5 fill-current" />
                <span>Data Gadai Perhiasan</span>
              </Link>
            </div>
          </div>

          <!-- 9. TUKAR TAMBAH -->
          <div>
            <button 
              @click="toggleGroup('tukarTambah')" 
              type="button"
              class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-slate-300 hover:bg-[#25254B] hover:text-white transition-all cursor-pointer"
            >
              <div class="flex items-center gap-3">
                <Repeat class="w-4 h-4 text-slate-400 shrink-0" />
                <span>Tukar Tambah</span>
              </div>
              <ChevronDown :class="[openGroups.tukarTambah ? 'rotate-180' : '', 'w-3.5 h-3.5 text-slate-400 transition-transform']" />
            </button>
            <div v-show="openGroups.tukarTambah" class="pl-8 pr-2 py-1 space-y-1 border-l border-[#2E2E56] ml-4 my-1">
              <Link :href="route('admin.pos.index')" class="flex items-center gap-2 py-1.5 px-2 rounded-lg text-slate-400 hover:text-white hover:bg-[#25254B]">
                <Circle class="w-1.5 h-1.5 fill-current" />
                <span>Tukar Tambah Kasir (POS)</span>
              </Link>
              <Link :href="route('admin.buybacks.index')" class="flex items-center gap-2 py-1.5 px-2 rounded-lg text-slate-400 hover:text-white hover:bg-[#25254B]">
                <Circle class="w-1.5 h-1.5 fill-current" />
                <span>Riwayat Emas Masuk</span>
              </Link>
            </div>
          </div>

          <!-- 10. SERVIS PERHIASAN -->
          <div>
            <button 
              @click="toggleGroup('servis')" 
              type="button"
              class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-slate-300 hover:bg-[#25254B] hover:text-white transition-all cursor-pointer"
            >
              <div class="flex items-center gap-3">
                <Wrench class="w-4 h-4 text-slate-400 shrink-0" />
                <span>Servis Perhiasan</span>
              </div>
              <ChevronDown :class="[openGroups.servis ? 'rotate-180' : '', 'w-3.5 h-3.5 text-slate-400 transition-transform']" />
            </button>
            <div v-show="openGroups.servis" class="pl-8 pr-2 py-1 space-y-1 border-l border-[#2E2E56] ml-4 my-1">
              <Link :href="route('admin.services.create')" :class="[isRoute('admin.services.create') ? 'text-amber-400 font-semibold bg-[#25254B]' : 'text-slate-400 hover:text-white hover:bg-[#25254B]', 'flex items-center gap-2 py-1.5 px-2 rounded-lg']">
                <Circle class="w-1.5 h-1.5 fill-current" />
                <span>Order Servis Baru</span>
              </Link>
              <Link :href="route('admin.services.index')" :class="[isRoute('admin.services.index') ? 'text-amber-400 font-semibold bg-[#25254B]' : 'text-slate-400 hover:text-white hover:bg-[#25254B]', 'flex items-center gap-2 py-1.5 px-2 rounded-lg']">
                <Circle class="w-1.5 h-1.5 fill-current" />
                <span>Data Servis Perhiasan</span>
              </Link>
            </div>
          </div>

          <!-- 11. TIMBANG BAKI -->
          <div>
            <button 
              @click="toggleGroup('timbangBaki')" 
              type="button"
              class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-slate-300 hover:bg-[#25254B] hover:text-white transition-all cursor-pointer"
            >
              <div class="flex items-center gap-3">
                <Scale class="w-4 h-4 text-slate-400 shrink-0" />
                <span>Timbang Baki</span>
              </div>
              <ChevronDown :class="[openGroups.timbangBaki ? 'rotate-180' : '', 'w-3.5 h-3.5 text-slate-400 transition-transform']" />
            </button>
            <div v-show="openGroups.timbangBaki" class="pl-8 pr-2 py-1 space-y-1 border-l border-[#2E2E56] ml-4 my-1">
              <Link :href="route('admin.tray-scales.create')" :class="[isRoute('admin.tray-scales.create') ? 'text-amber-400 font-semibold bg-[#25254B]' : 'text-slate-400 hover:text-white hover:bg-[#25254B]', 'flex items-center gap-2 py-1.5 px-2 rounded-lg']">
                <Circle class="w-1.5 h-1.5 fill-current" />
                <span>Timbang Baki Baru</span>
              </Link>
              <Link :href="route('admin.tray-scales.index')" :class="[isRoute('admin.tray-scales.index') ? 'text-amber-400 font-semibold bg-[#25254B]' : 'text-slate-400 hover:text-white hover:bg-[#25254B]', 'flex items-center gap-2 py-1.5 px-2 rounded-lg']">
                <Circle class="w-1.5 h-1.5 fill-current" />
                <span>Riwayat Timbang Harian</span>
              </Link>
              <Link :href="route('admin.trays.index')" class="flex items-center gap-2 py-1.5 px-2 rounded-lg text-slate-400 hover:text-white hover:bg-[#25254B]">
                <Circle class="w-1.5 h-1.5 fill-current" />
                <span>Katalog Baki & Etalase</span>
              </Link>
            </div>
          </div>

          <!-- 12. PEGAWAI -->
          <div>
            <button 
              @click="toggleGroup('pegawai')" 
              type="button"
              class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-slate-300 hover:bg-[#25254B] hover:text-white transition-all cursor-pointer"
            >
              <div class="flex items-center gap-3">
                <UserCheck class="w-4 h-4 text-slate-400 shrink-0" />
                <span>Pegawai</span>
              </div>
              <ChevronDown :class="[openGroups.pegawai ? 'rotate-180' : '', 'w-3.5 h-3.5 text-slate-400 transition-transform']" />
            </button>
            <div v-show="openGroups.pegawai" class="pl-8 pr-2 py-1 space-y-1 border-l border-[#2E2E56] ml-4 my-1">
              <span class="block py-1.5 px-2 rounded-lg text-slate-400 hover:text-white hover:bg-[#25254B] cursor-pointer">
                Tambah Pegawai
              </span>
              <span class="block py-1.5 px-2 rounded-lg text-slate-400 hover:text-white hover:bg-[#25254B] cursor-pointer">
                Data Pegawai & Kasir
              </span>
              <span class="block py-1.5 px-2 rounded-lg text-slate-400 hover:text-white hover:bg-[#25254B] cursor-pointer">
                Penggajian
              </span>
            </div>
          </div>

          <!-- 13. KEUANGAN (BADGE 46, 12) -->
          <div>
            <button 
              @click="toggleGroup('keuangan')" 
              type="button"
              class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-slate-300 hover:bg-[#25254B] hover:text-white transition-all cursor-pointer"
            >
              <div class="flex items-center gap-3">
                <Calculator class="w-4 h-4 text-slate-400 shrink-0" />
                <span>Keuangan</span>
                <div class="flex items-center gap-1">
                  <span class="px-1.5 py-0.2 rounded-full bg-rose-500/80 text-[10px] text-white font-mono font-bold">46</span>
                  <span class="px-1.5 py-0.2 rounded-full bg-amber-500/80 text-[10px] text-slate-950 font-mono font-bold">12</span>
                </div>
              </div>
              <ChevronDown :class="[openGroups.keuangan ? 'rotate-180' : '', 'w-3.5 h-3.5 text-slate-400 transition-transform']" />
            </button>
            <div v-show="openGroups.keuangan" class="pl-8 pr-2 py-1 space-y-1 border-l border-[#2E2E56] ml-4 my-1">
              <Link :href="route('admin.reports.index')" class="flex items-center gap-2 py-1.5 px-2 rounded-lg text-slate-400 hover:text-white hover:bg-[#25254B]">
                <Circle class="w-1.5 h-1.5 fill-current" />
                <span>Buku Kas Toko</span>
              </Link>
              <span class="block py-1.5 px-2 rounded-lg text-slate-400 hover:text-white hover:bg-[#25254B] cursor-pointer">
                Pengeluaran Harian & Bulanan
              </span>
              <span class="block py-1.5 px-2 rounded-lg text-slate-400 hover:text-white hover:bg-[#25254B] cursor-pointer">
                Rekapitulasi Laba Rugi
              </span>
            </div>
          </div>

        </nav>

        <!-- User Profile & Footer Sidebar -->
        <div class="p-3 border-t border-[#2A2A4E] bg-[#15152D] shrink-0">
          <div class="flex items-center gap-3 p-2 rounded-xl bg-[#1E1E3F] border border-[#2D2D54]">
            <div class="w-8 h-8 rounded-lg bg-amber-500/20 text-amber-300 flex items-center justify-center font-bold text-xs shrink-0">
              {{ user.name ? user.name.charAt(0).toUpperCase() : 'U' }}
            </div>
            <div class="overflow-hidden flex-1">
              <p class="text-xs font-semibold text-white truncate">{{ user.name }}</p>
              <span class="inline-block px-1.5 py-0.2 text-[9px] rounded bg-amber-500/20 text-amber-300 font-mono uppercase">
                {{ user.role || 'kasir' }}
              </span>
            </div>
            <button 
              @click="logout"
              title="Keluar / Logout"
              class="p-1.5 text-slate-400 hover:text-rose-400 hover:bg-[#2A2A56] rounded-lg transition-colors cursor-pointer"
            >
              <LogOut class="w-4 h-4" />
            </button>
          </div>
        </div>
      </div>
    </aside>

    <!-- Main Content Area -->
    <div :class="[isSidebarCollapsed ? 'lg:ml-20' : 'lg:ml-72', 'flex-1 flex flex-col min-h-screen transition-all duration-300']">
      <!-- Top Navbar -->
      <header class="h-16 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 px-6 flex items-center justify-between sticky top-0 z-30 transition-colors duration-300">
        <div class="flex items-center gap-4">
          <button 
            @click="isSidebarOpen = true" 
            class="p-2 text-slate-600 dark:text-slate-400 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 lg:hidden"
          >
            <Menu class="w-5 h-5" />
          </button>

          <!-- Store Identity Header -->
          <div class="hidden sm:flex items-center gap-2 text-xs">
            <span class="px-2.5 py-1 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-700 dark:text-amber-400 font-semibold flex items-center gap-1.5">
              <Store class="w-3.5 h-3.5" />
              Toko Mas Permata Abadi
            </span>
            <span class="text-slate-300 dark:text-slate-600">|</span>
            <span class="text-slate-500 dark:text-slate-400 font-mono">{{ new Date().toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }) }}</span>
          </div>
        </div>

        <!-- Right Quick Actions: ThemeToggle, Storefront, POS Kasir Shortcut -->
        <div class="flex items-center gap-3">
          <ThemeToggle />

          <Link 
            :href="route('admin.pos.index')"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-500 hover:bg-amber-600 text-slate-950 text-xs font-bold shadow-sm shadow-amber-500/20 transition-all"
          >
            <ShoppingBag class="w-3.5 h-3.5" />
            <span>Kasir POS</span>
          </Link>

          <Link 
            href="/" 
            target="_blank"
            class="hidden md:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-medium transition-colors"
          >
            <Store class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400" />
            <span>Katalog Depan</span>
          </Link>
        </div>
      </header>

      <!-- Main Body Content -->
      <main class="flex-1 p-6 md:p-8 max-w-7xl w-full mx-auto space-y-8">
        <slot />
      </main>

      <!-- Footer -->
      <footer class="py-4 px-8 border-t border-slate-200 dark:border-slate-800 text-xs text-slate-500 text-center transition-colors duration-300">
        POS Emas &copy; 2026 Toko Mas Permata Abadi. Presisi Gramasi & Transparansi Kadar Emas.
      </footer>
    </div>
  </div>
</template>
