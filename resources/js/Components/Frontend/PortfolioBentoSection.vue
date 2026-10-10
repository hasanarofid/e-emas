<script setup>
import { Link } from '@inertiajs/vue3';
import { ArrowUpRight, MapPin, Calendar, Ruler, Building } from 'lucide-vue-next';

defineProps({
    portfolios: {
        type: Array,
        default: () => []
    },
    whatsappNumber: {
        type: String,
        default: '6281234567890'
    }
});
</script>

<template>
    <section class="py-24 bg-brand-cream relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Section Header -->
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-6">
                <div class="max-w-2xl space-y-3">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-brand-sand text-brand-dark text-xs font-bold uppercase tracking-widest border border-brand-primary/20">
                        Portofolio Lapangan
                    </div>
                    <h2 class="font-serif text-3xl sm:text-4xl md:text-5xl font-bold text-brand-dark tracking-tight leading-tight">
                        Bukti Nyata Presisi & Estetika di Berbagai Proyek Nusantara.
                    </h2>
                    <p class="text-base text-brand-dark/80 font-normal">
                        Dokumentasi instalasi fisik ornamen GRC, pilar, kubah, dan lis profil yang telah kami selesaikan dengan kepuasan klien 100%.
                    </p>
                </div>

                <Link 
                    :href="route('portfolios.index')"
                    class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-transparent hover:bg-brand-sand text-brand-dark font-bold text-sm border-2 border-brand-primary/40 hover:border-brand-primary transition-all group shrink-0"
                >
                    <span>Lihat Semua Proyek</span>
                    <ArrowUpRight class="w-4 h-4 transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5" />
                </Link>
            </div>

            <!-- Bento Grid Layout -->
            <div class="grid grid-cols-12 gap-6 sm:gap-8">
                
                <div 
                    v-for="(item, idx) in portfolios" 
                    :key="item.id || idx"
                    class="group relative rounded-2xl overflow-hidden shadow-md hover:shadow-2xl transition-all duration-500 border border-brand-dark/15 bg-brand-dark"
                    :class="[
                        idx === 0 ? 'col-span-12 lg:col-span-8 h-[400px] sm:h-[480px]' : 
                        idx === 1 ? 'col-span-12 sm:col-span-6 lg:col-span-4 h-[400px] sm:h-[480px]' : 
                        'col-span-12 sm:col-span-6 lg:col-span-4 h-[350px] sm:h-[400px]'
                    ]"
                >
                    <!-- Background Image -->
                    <img 
                        :src="item.thumbnail" 
                        :alt="item.title"
                        class="w-full h-full object-cover object-center transition-transform duration-700 ease-out group-hover:scale-105"
                        loading="lazy" 
                    />

                    <!-- Architectural Vignette Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-t from-brand-dark/95 via-brand-dark/40 to-transparent transition-opacity duration-300"></div>

                    <!-- Top Tags (Year & Project Type) -->
                    <div class="absolute top-4 left-4 right-4 flex items-center justify-between z-10">
                        <span class="px-3 py-1 rounded-md text-[11px] font-bold uppercase tracking-wider bg-brand-dark/80 text-brand-cta backdrop-blur-md border border-brand-cta/20">
                            {{ item.project_type || 'Proyek Arsitektur' }}
                        </span>

                        <span class="px-2.5 py-1 rounded-md text-[11px] font-semibold bg-brand-cream/90 text-brand-dark backdrop-blur-md flex items-center gap-1">
                            <Calendar class="w-3 h-3 text-brand-primary" />
                            <span>{{ item.completion_year || '2024' }}</span>
                        </span>
                    </div>

                    <!-- Bottom Details Content -->
                    <div class="absolute bottom-6 left-6 right-6 z-10 space-y-2">
                        
                        <!-- Location Info -->
                        <div class="flex items-center gap-1.5 text-xs text-brand-cream/80 font-medium">
                            <MapPin class="w-3.5 h-3.5 text-brand-light" />
                            <span>{{ item.location }}</span>
                        </div>

                        <!-- Project Title -->
                        <h3 class="font-serif text-xl sm:text-2xl font-bold text-brand-cream group-hover:text-brand-cta transition-colors leading-snug">
                            {{ item.title }}
                        </h3>

                        <!-- Ornament Scope -->
                        <div class="text-xs text-brand-sand/90 font-normal flex items-center gap-1.5 pt-1">
                            <Ruler class="w-3.5 h-3.5 text-brand-light shrink-0" />
                            <span class="line-clamp-1">{{ item.ornament_scope }}</span>
                        </div>

                        <!-- Action Link on Hover -->
                        <div class="pt-2 flex items-center gap-2">
                            <a 
                                :href="`https://wa.me/${whatsappNumber}?text=Halo%20Cahaya%20Ornamen,%20saya%20tertarik%20dengan%20referensi%20proyek:%20${encodeURIComponent(item.title)}`"
                                target="_blank"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-brand-cta text-brand-dark text-xs font-bold hover:bg-yellow-300 transition-colors shadow-sm"
                            >
                                <span>Tanya Proyek Ini via WA</span>
                                <ArrowUpRight class="w-3.5 h-3.5" />
                            </a>
                        </div>

                    </div>

                </div>

            </div>

        </div>
    </section>
</template>
