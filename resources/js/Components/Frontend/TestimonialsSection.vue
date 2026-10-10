<script setup>
import { Star, Quote } from 'lucide-vue-next';

defineProps({
    testimonials: {
        type: Array,
        default: () => []
    }
});
</script>

<template>
    <section class="py-24 bg-brand-sand/60 border-y border-brand-dark/10 relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Section Header -->
            <div class="text-center max-w-3xl mx-auto mb-16 space-y-3">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-brand-sand text-brand-dark text-xs font-bold uppercase tracking-widest border border-brand-primary/20">
                    Social Proof & Kepercayaan
                </div>
                <h2 class="font-serif text-3xl sm:text-4xl md:text-5xl font-bold text-brand-dark tracking-tight">
                    Dipercaya oleh Arsitek Terkemuka, Kontraktor, & DKM Masjid.
                </h2>
                <p class="text-base text-brand-dark/75">
                    Kepuasan klien kami dibangun atas dasar ketepatan jadwal fabrikasi, kerapian detail cetakan, dan komitmen keselamatan pemasangan.
                </p>
            </div>

            <!-- Testimonial Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div 
                    v-for="(testi, idx) in testimonials" 
                    :key="idx"
                    class="p-8 rounded-2xl bg-brand-cream border border-brand-dark/15 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between space-y-6 relative group"
                >
                    <!-- Top Quote Icon & 5 Stars -->
                    <div class="flex items-center justify-between">
                        <div class="flex gap-1 text-brand-dark">
                            <Star v-for="s in 5" :key="s" class="w-4 h-4 fill-brand-cta text-brand-dark" />
                        </div>
                        <Quote class="w-8 h-8 text-brand-primary/30 group-hover:text-brand-primary/60 transition-colors" />
                    </div>

                    <!-- Quote Text -->
                    <p class="font-serif text-base sm:text-lg text-brand-dark/90 italic leading-relaxed">
                        "{{ testi.quote }}"
                    </p>

                    <!-- Author Details -->
                    <div class="pt-4 border-t border-brand-dark/10 flex items-center gap-3.5">
                        <img 
                            :src="testi.avatar" 
                            :alt="testi.author" 
                            class="w-12 h-12 rounded-full object-cover border-2 border-brand-primary/30"
                        />
                        <div>
                            <h4 class="font-bold text-sm text-brand-dark">
                                {{ testi.author }}
                            </h4>
                            <p class="text-xs text-brand-dark/70">
                                {{ testi.role }}
                            </p>
                            <span class="inline-block mt-0.5 text-[10px] font-semibold text-brand-primary bg-brand-sand px-1.5 py-0.5 rounded">
                                {{ testi.project }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Client Logos Strip (Architectural & Contractor Partners) -->
            <div class="mt-16 pt-10 border-t border-brand-dark/10">
                <div class="text-center text-xs font-bold uppercase tracking-widest text-brand-dark/60 mb-6">
                    Mitra Pembangunan & Asosiasi
                </div>
                <div class="flex flex-wrap items-center justify-center gap-8 sm:gap-16 opacity-75 grayscale hover:grayscale-0 transition-all duration-300">
                    <span class="font-serif text-lg sm:text-xl font-bold tracking-wider text-brand-dark">
                        IKATAN ARSITEK INDONESIA
                    </span>
                    <span class="font-serif text-lg sm:text-xl font-bold tracking-wider text-brand-dark">
                        GAPENSI
                    </span>
                    <span class="font-serif text-lg sm:text-xl font-bold tracking-wider text-brand-dark">
                        DEWAN MASJID INDONESIA
                    </span>
                    <span class="font-serif text-lg sm:text-xl font-bold tracking-wider text-brand-dark">
                        LPJK NASIONAL
                    </span>
                </div>
            </div>

        </div>
    </section>
</template>
