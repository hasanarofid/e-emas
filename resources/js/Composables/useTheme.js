import { ref, onMounted } from 'vue';

const isDark = ref(true);

export function useTheme() {
  const applyTheme = (dark) => {
    isDark.value = dark;
    if (typeof document !== 'undefined') {
      if (dark) {
        document.documentElement.classList.add('dark');
        localStorage.setItem('theme', 'dark');
      } else {
        document.documentElement.classList.remove('dark');
        localStorage.setItem('theme', 'light');
      }
    }
  };

  const toggleTheme = () => {
    applyTheme(!isDark.value);
  };

  const initTheme = () => {
    if (typeof window !== 'undefined') {
      const savedTheme = localStorage.getItem('theme');
      if (savedTheme) {
        applyTheme(savedTheme === 'dark');
      } else {
        // Default ke dark mode untuk tema eksklusif jewelry atelier
        applyTheme(true);
      }
    }
  };

  return {
    isDark,
    toggleTheme,
    initTheme,
  };
}
