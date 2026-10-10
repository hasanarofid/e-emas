<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import { MessageSquare, Menu, X, ArrowUpRight, Phone } from 'lucide-vue-next';

defineProps({
    whatsappNumber: {
        type: String,
        default: '6281234567890'
    },
    brandName: {
        type: String,
        default: 'Cahaya Ornamen'
    }
});

const isScrolled = ref(false);
const mobileMenuOpen = ref(false);

const checkScroll = () => {
    isScrolled.value = window.scrollY > 30;
};

onMounted(() => {
    window.addEventListener('scroll', checkScroll, { passive: true });
});

onUnmounted(() => {
    window.removeEventListener('scroll', checkScroll);
});
</script>

<template>
    <header 
        class="fixed top-0 left-0 right-0 z-50 transition-all duration-300"
        :class="[
            isScrolled 
                ? 'bg-brand-cream/95 backdrop-blur-md shadow-md border-b border-brand-dark/10 py-3' 
                : 'bg-brand-cream/80 backdrop-blur-sm py-5'
        ]"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                
                <!-- Brand Logo & Identity -->
                <Link :href="route('home')" class="group flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-brand-dark flex items-center justify-center text-brand-cta shadow-inner transition-transform duration-300 group-hover:scale-105">
                        <!-- Geometric GRC Motif Icon -->
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M12 2L2 12l10 10 10-10L12 2z" />
                            <path d="M12 6L6 12l6 6 6-6-6-6z" />
                            <circle cx="12" cy="12" r="1.5" fill="currentColor" />
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-serif text-xl sm:text-2xl font-bold tracking-tight text-brand-dark leading-tight group-hover:text-brand-primary transition-colors">
                            CAHAYA <span class="font-normal italic text-brand-primary">ORNAMEN</span>
                        </span>
                        <span class="text-[10px] tracking-widest uppercase font-semibold text-brand-dark/70">
                            Architectural GRC & Relief
                        </span>
                    </div>
                </Link>

                <!-- Desktop Navigation Menu -->
                <nav class="hidden md:flex items-center gap-8">
                    <Link 
                        :href="route('home')" 
                        class="text-sm font-medium text-brand-dark hover:text-brand-primary transition-colors relative py-1 after:absolute after:bottom-0 after:left-0 after:w-0 hover:after:w-full after:h-0.5 after:bg-brand-primary after:transition-all"
                    >
                        Beranda
                    </Link>
                    <a 
                        href="#about" 
                        class="text-sm font-medium text-brand-dark hover:text-brand-primary transition-colors py-1"
                    >
                        Tentang Kami
                    </a>
                    <a 
                        href="#services" 
                        class="text-sm font-medium text-brand-dark hover:text-brand-primary transition-colors py-1"
                    >
                        Layanan
                    </a>
                    <Link 
                        :href="route('products.index')" 
                        class="text-sm font-medium text-brand-dark hover:text-brand-primary transition-colors py-1 flex items-center gap-1"
                    >
                        Katalog Produk
                    </Link>
                    <Link 
                        :href="route('portfolios.index')" 
                        class="text-sm font-medium text-brand-dark hover:text-brand-primary transition-colors py-1"
                    >
                        Portofolio
                    </Link>
                    <Link 
                        :href="route('articles.index')" 
                        class="text-sm font-medium text-brand-dark hover:text-brand-primary transition-colors py-1"
                    >
                        Tips & Artikel
                    </Link>
                    <Link 
                        :href="route('contact.index')" 
                        class="text-sm font-medium text-brand-dark hover:text-brand-primary transition-colors py-1"
                    >
                        Kontak
                    </Link>
                </nav>

                <!-- Action Button (Prominent CTA WhatsApp) -->
                <div class="hidden sm:flex items-center gap-4">
                    <a 
                        :href="`https://wa.me/${whatsappNumber}?text=Halo%20Cahaya%20Ornamen,%20saya%20ingin%20konsultasi%20kebutuhan%20ornamen%20arsitektur`"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center gap-2.5 px-5 py-2.5 rounded-lg bg-brand-cta text-brand-dark font-bold text-sm tracking-wide shadow-cta-glow hover:bg-yellow-300 transition-all duration-200 transform hover:-translate-y-0.5 active:translate-y-0 border border-brand-dark/20"
                    >
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-brand-dark opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-brand-dark"></span>
                        </span>
                        <MessageSquare class="w-4 h-4 text-brand-dark stroke-[2.2]" />
                        <span>Konsultasi WA</span>
                        <ArrowUpRight class="w-3.5 h-3.5 opacity-80" />
                    </a>
                </div>

                <!-- Mobile Menu Button -->
                <div class="flex md:hidden items-center gap-2">
                    <a 
                        :href="`https://wa.me/${whatsappNumber}`"
                        target="_blank"
                        class="p-2 rounded-lg bg-brand-cta text-brand-dark font-bold text-xs"
                    >
                        <Phone class="w-4 h-4" />
                    </a>
                    <button 
                        @click="mobileMenuOpen = !mobileMenuOpen"
                        type="button" 
                        class="p-2 rounded-lg text-brand-dark hover:bg-brand-sand transition-colors"
                        aria-label="Toggle Navigation"
                    >
                        <Menu v-if="!mobileMenuOpen" class="w-6 h-6" />
                        <X v-else class="w-6 h-6" />
                    </button>
                </div>
            </div>

            <!-- Mobile Drawer -->
            <transition
                enter-active-class="transition duration-200 ease-out"
                enter-from-class="opacity-0 -translate-y-2"
                enter-to-class="opacity-100 translate-y-0"
                leave-active-class="transition duration-150 ease-in"
                leave-from-class="opacity-100 translate-y-0"
                leave-to-class="opacity-0 -translate-y-2"
            >
                <div 
                    v-if="mobileMenuOpen" 
                    class="md:hidden mt-4 pt-4 pb-6 border-t border-brand-dark/10 bg-brand-sand/90 backdrop-blur-md rounded-2xl p-5 shadow-xl space-y-4"
                >
                    <nav class="flex flex-col space-y-3">
                        <Link 
                            :href="route('home')" 
                            @click="mobileMenuOpen = false"
                            class="text-base font-medium text-brand-dark hover:text-brand-primary px-2 py-1.5 rounded-md"
                        >
                            Beranda
                        </Link>
                        <a 
                            href="#about" 
                            @click="mobileMenuOpen = false"
                            class="text-base font-medium text-brand-dark hover:text-brand-primary px-2 py-1.5 rounded-md"
                        >
                            Tentang Kami
                        </a>
                        <a 
                            href="#services" 
                            @click="mobileMenuOpen = false"
                            class="text-base font-medium text-brand-dark hover:text-brand-primary px-2 py-1.5 rounded-md"
                        >
                            Layanan
                        </a>
                        <Link 
                            :href="route('products.index')" 
                            @click="mobileMenuOpen = false"
                            class="text-base font-medium text-brand-dark hover:text-brand-primary px-2 py-1.5 rounded-md"
                        >
                            Katalog Produk
                        </Link>
                        <Link 
                            :href="route('portfolios.index')" 
                            @click="mobileMenuOpen = false"
                            class="text-base font-medium text-brand-dark hover:text-brand-primary px-2 py-1.5 rounded-md"
                        >
                            Portofolio Proyek
                        </Link>
                        <Link 
                            :href="route('articles.index')" 
                            @click="mobileMenuOpen = false"
                            class="text-base font-medium text-brand-dark hover:text-brand-primary px-2 py-1.5 rounded-md"
                        >
                            Tips & Artikel
                        </Link>
                        <Link 
                            :href="route('contact.index')" 
                            @click="mobileMenuOpen = false"
                            class="text-base font-medium text-brand-dark hover:text-brand-primary px-2 py-1.5 rounded-md"
                        >
                            Kontak & Lokasi Workshop
                        </Link>
                    </nav>

                    <div class="pt-2 border-t border-brand-dark/10">
                        <a 
                            :href="`https://wa.me/${whatsappNumber}?text=Halo%20Cahaya%20Ornamen,%20saya%20ingin%20konsultasi%20kebutuhan%20ornamen%20arsitektur`"
                            target="_blank"
                            class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-xl bg-brand-cta text-brand-dark font-bold text-sm shadow-md"
                        >
                            <MessageSquare class="w-4 h-4" />
                            <span>Konsultasi WA Sekarang</span>
                        </a>
                    </div>
                </div>
            </transition>
        </div>
    </header>
</template>
