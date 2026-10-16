<script setup>
import { ref } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { 
  LayoutDashboard, 
  Menu, 
  X, 
  LogOut, 
  Gem,
  Store,
} from 'lucide-vue-next';
import ThemeToggle from '@/Components/ThemeToggle.vue';

const page = usePage();
const user = page.props.auth?.user || {};

const navigation = [
  { name: 'Dashboard', href: route('admin.dashboard'), icon: LayoutDashboard, current: route().current('admin.dashboard') },
  { name: 'Kurs Harga Emas', href: route('admin.gold-rates.index'), icon: TrendingUp, current: route().current('admin.gold-rates.*') },
  { name: 'Baki & Etalase', href: route('admin.trays.index'), icon: Layers, current: route().current('admin.trays.*') },
  { name: 'Inventaris Perhiasan', href: route('admin.items.index'), icon: Gem, current: route().current('admin.items.*') },
  { name: 'Pengaturan Toko', href: route('admin.settings.index'), icon: SettingsIcon, current: route().current('admin.settings.*') },
];

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

    <!-- Sidebar -->
    <aside 
      :class="[
        isSidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
        isSidebarCollapsed ? 'lg:w-20' : 'lg:w-72',
        'fixed top-0 bottom-0 left-0 z-50 w-72 bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 transition-all duration-300 ease-in-out flex flex-col justify-between shadow-sm'
      ]"
    >
      <div>
        <!-- Sidebar Header / Logo -->
        <div :class="[isSidebarCollapsed ? 'lg:px-0 lg:justify-center' : 'px-6 justify-between', 'flex items-center h-20 border-b border-slate-200 dark:border-slate-800']">
          <Link :href="route('admin.dashboard')" class="flex items-center gap-3 group">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-600 via-amber-400 to-yellow-200 p-0.5 shadow-md shadow-amber-500/20 shrink-0">
              <div class="w-full h-full bg-slate-950 rounded-[10px] flex items-center justify-center">
                <Gem class="w-5 h-5 text-amber-400" />
              </div>
            </div>
            <div :class="[isSidebarCollapsed ? 'lg:hidden' : 'block', 'transition-opacity duration-300']">
              <h1 class="text-sm font-bold tracking-tight text-slate-900 dark:text-white flex items-center gap-1.5">
                e-Emas POS
                <span class="text-[9px] bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-500/20 px-1.5 py-0.2 rounded font-mono">v1.0</span>
              </h1>
              <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate">Permata Abadi Gold</p>
            </div>
          </Link>
          <button 
            @click="isSidebarOpen = false" 
            class="p-2 text-slate-500 dark:text-slate-400 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 lg:hidden"
          >
            <X class="w-5 h-5" />
          </button>
        </div>

        <!-- Main Navigation Links -->
        <nav class="p-4 space-y-1.5">
          <div :class="[isSidebarCollapsed ? 'lg:hidden' : 'block', 'px-3 pt-2 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 font-mono']">
            Menu Operasional
          </div>

          <Link 
            v-for="item in navigation" 
            :key="item.name" 
            :href="item.href"
            :class="[
              item.current 
                ? 'bg-amber-500/10 text-amber-800 dark:text-amber-300 border-amber-500/30' 
                : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white border-transparent',
              'flex items-center gap-3 px-3.5 py-2.5 rounded-xl border text-sm font-medium transition-all group'
            ]"
          >
            <component 
              :is="item.icon" 
              :class="[
                item.current ? 'text-amber-600 dark:text-amber-400' : 'text-slate-400 dark:text-slate-500 group-hover:text-slate-700 dark:group-hover:text-slate-200', 
                'w-5 h-5 shrink-0 transition-colors'
              ]" 
            />
            <span :class="[isSidebarCollapsed ? 'lg:hidden' : 'block', 'truncate']">{{ item.name }}</span>
          </Link>

          <!-- Quick Action to Storefront -->
          <Link 
            href="/" 
            target="_blank"
            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-amber-600 dark:hover:text-amber-300 border border-transparent text-sm font-medium transition-all group"
          >
            <Store class="w-5 h-5 text-slate-400 group-hover:text-amber-500 shrink-0" />
            <span :class="[isSidebarCollapsed ? 'lg:hidden' : 'block', 'truncate']">Lihat Toko Depan</span>
          </Link>
        </nav>
      </div>

      <!-- User Profile & Logout Section in Sidebar -->
      <div class="p-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/60">
        <div class="flex items-center gap-3 p-2 rounded-xl bg-white dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800 shadow-sm">
          <div class="w-9 h-9 rounded-lg bg-amber-500/10 text-amber-700 dark:text-amber-400 flex items-center justify-center font-bold text-sm shrink-0">
            {{ user.name ? user.name.charAt(0).toUpperCase() : 'U' }}
          </div>
          <div :class="[isSidebarCollapsed ? 'lg:hidden' : 'block', 'overflow-hidden flex-1']">
            <p class="text-xs font-semibold text-slate-900 dark:text-white truncate">{{ user.name }}</p>
            <span class="inline-block px-1.5 py-0.2 text-[10px] rounded bg-amber-500/10 text-amber-700 dark:text-amber-300 font-mono uppercase">
              {{ user.role || 'kasir' }}
            </span>
          </div>
          <button 
            @click="logout"
            title="Keluar / Logout"
            class="p-1.5 text-slate-400 hover:text-rose-500 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors cursor-pointer"
          >
            <LogOut class="w-4 h-4" />
          </button>
        </div>
      </div>
    </aside>

    <!-- Main Content Area -->
    <div :class="[isSidebarCollapsed ? 'lg:ml-20' : 'lg:ml-72', 'flex-1 flex flex-col min-h-screen transition-all duration-300']">
      <!-- Top Navbar -->
      <header class="h-20 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 px-6 flex items-center justify-between sticky top-0 z-30 transition-colors duration-300">
        <div class="flex items-center gap-4">
          <button 
            @click="isSidebarOpen = true" 
            class="p-2 text-slate-600 dark:text-slate-400 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 lg:hidden"
          >
            <Menu class="w-6 h-6" />
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

        <!-- Right Quick Actions: ThemeToggle, Storefront, Logout -->
        <div class="flex items-center gap-3">
          <ThemeToggle />

          <Link 
            href="/" 
            target="_blank"
            class="hidden md:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-medium transition-colors"
          >
            <Store class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400" />
            <span>Katalog Depan</span>
          </Link>

          <button 
            @click="logout"
            class="flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 border border-rose-500/20 text-xs font-semibold transition-colors cursor-pointer"
          >
            <LogOut class="w-3.5 h-3.5" />
            <span>Keluar</span>
          </button>
        </div>
      </header>

      <!-- Main Body Content -->
      <main class="flex-1 p-6 md:p-8 max-w-7xl w-full mx-auto space-y-8">
        <slot />
      </main>

      <!-- Footer -->
      <footer class="py-4 px-8 border-t border-slate-200 dark:border-slate-800 text-xs text-slate-500 text-center transition-colors duration-300">
        e-Emas POS &copy; 2026 Toko Mas Permata Abadi. Presisi Gramasi & Transparansi Kadar Emas.
      </footer>
    </div>
  </div>
</template>
