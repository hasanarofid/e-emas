<script setup>
import { useTheme } from '@/Composables/useTheme';
import { Moon, Sun } from 'lucide-vue-next';

defineProps({
  showLabel: {
    type: Boolean,
    default: false
  }
});

const { isDark, toggleTheme } = useTheme();
</script>

<template>
  <button
    type="button"
    @click="toggleTheme"
    :title="isDark ? 'Ganti ke Mode Terang (Light)' : 'Ganti ke Mode Gelap (Dark)'"
    class="p-2 rounded-xl transition-all duration-200 border cursor-pointer flex items-center gap-2 select-none
      bg-slate-100 hover:bg-slate-200 text-slate-700 border-slate-300
      dark:bg-slate-900/80 dark:hover:bg-slate-800 dark:text-amber-400 dark:border-slate-800
      active:scale-95 shadow-sm"
  >
    <Sun v-if="isDark" class="w-4 h-4 text-amber-400 animate-spin-slow" />
    <Moon v-else class="w-4 h-4 text-slate-700" />
    <span v-if="showLabel" class="text-xs font-semibold">
      {{ isDark ? 'Mode Gelap' : 'Mode Terang' }}
    </span>
  </button>
</template>

<style scoped>
@keyframes spin-slow {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}
.animate-spin-slow {
  animation: spin-slow 20s linear infinite;
}
</style>
