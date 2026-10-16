<script setup>
import { Link } from '@inertiajs/vue3';
import { Gem, Sparkles } from 'lucide-vue-next';
import ThemeToggle from '@/Components/ThemeToggle.vue';
</script>

<template>
  <div class="min-h-screen bg-slate-100 dark:bg-slate-950 text-slate-800 dark:text-slate-100 font-sans antialiased relative overflow-hidden flex flex-col items-center justify-center p-6 selection:bg-amber-500 selection:text-slate-950 transition-colors duration-300">
    <!-- Ambient Gold Glow Blobs -->
    <div class="absolute top-[-20%] left-[-10%] w-[500px] h-[500px] rounded-full bg-amber-500/10 dark:bg-amber-500/15 blur-[120px] pointer-events-none"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[500px] h-[500px] rounded-full bg-amber-600/10 dark:bg-amber-600/15 blur-[120px] pointer-events-none"></div>

    <!-- Top Floating Theme Toggle -->
    <div class="absolute top-6 right-6 z-20">
      <ThemeToggle :show-label="true" />
    </div>

    <!-- Card Container -->
    <div class="w-full sm:max-w-md bg-white dark:bg-slate-900/90 border border-slate-200 dark:border-slate-800 backdrop-blur-xl shadow-xl dark:shadow-2xl dark:shadow-slate-950/60 rounded-3xl p-8 relative z-10 space-y-6 transition-colors duration-300">
      <!-- Branding e-Emas POS -->
      <div class="flex flex-col items-center gap-3">
        <Link href="/" class="group flex items-center justify-center">
          <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-amber-600 via-amber-400 to-yellow-200 p-0.5 shadow-lg shadow-amber-500/30 group-hover:scale-105 transition-all">
            <div class="w-full h-full bg-slate-950 rounded-[14px] flex items-center justify-center">
              <Gem class="w-7 h-7 text-amber-400" />
            </div>
          </div>
        </Link>
        <div class="text-center">
          <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-amber-500/10 border border-amber-500/30 text-amber-700 dark:text-amber-300 text-[11px] font-semibold mb-1">
            <Sparkles class="w-3 h-3 text-amber-500 dark:text-amber-400" />
            <span>Toko Mas Permata Abadi</span>
          </div>
          <h2 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">e-Emas POS</h2>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Sistem Kasir & Manajemen Toko Emas</p>
        </div>
      </div>

      <!-- Form Content Slot -->
      <div class="space-y-4">
        <slot />
      </div>
    </div>

    <!-- Back to store public landing -->
    <div class="mt-6 text-center text-xs text-slate-500 dark:text-slate-400 relative z-10">
      <Link href="/" class="hover:text-amber-600 dark:hover:text-amber-400 transition-colors">
        &larr; Kembali ke Halaman Utama Toko
      </Link>
    </div>
  </div>
</template>
