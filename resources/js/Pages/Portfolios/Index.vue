<script setup>
import { Head, Link } from '@inertiajs/vue3';
import HeaderNavbar from '@/Components/Frontend/HeaderNavbar.vue';
import FooterSection from '@/Components/Frontend/FooterSection.vue';
import FloatingWhatsapp from '@/Components/Frontend/FloatingWhatsapp.vue';
import { MapPin, Calendar, Ruler, ArrowUpRight } from 'lucide-vue-next';

defineProps({
    portfolios: {
        type: Object,
        required: true
    },
    currentType: {
        type: String,
        default: null
    }
});
</script>

<template>
    <Head title="Portofolio Proyek Ornamen Arsitektur - Cahaya Ornamen" />

    <div class="min-h-screen bg-brand-cream text-brand-dark font-sans antialiased">
        <HeaderNavbar />

        <main class="pt-32 pb-24">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <!-- Page Title -->
                <div class="max-w-3xl mb-14 space-y-3">
                    <span class="text-xs font-bold uppercase tracking-widest text-brand-primary">
                        Galeri Lapangan
                    </span>
                    <h1 class="font-serif text-4xl sm:text-5xl font-bold text-brand-dark leading-tight">
                        Dokumentasi Hasil Pemasangan Ornamen di Seluruh Indonesia.
                    </h1>
                    <p class="text-base text-brand-dark/80">
                        Setiap proyek adalah bukti komitmen kami terhadap presisi cetakan, kekuatan struktur bentang, dan estetika arsitektur berkelas.
                    </p>
                </div>

                <!-- Portfolios Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div 
                        v-for="item in (portfolios.data || portfolios)" 
                        :key="item.id"
                        class="group rounded-2xl overflow-hidden bg-brand-cream border border-brand-dark/15 shadow-sm hover:shadow-2xl transition-all duration-300 flex flex-col justify-between"
                    >
                        <div class="relative h-64 overflow-hidden bg-brand-sand">
                            <img 
                                :src="item.thumbnail" 
                                :alt="item.title"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out" 
                            />
                            <div class="absolute top-3 left-3">
                                <span class="px-2.5 py-1 rounded text-[11px] font-bold uppercase tracking-wider bg-brand-dark/80 text-brand-cta backdrop-blur-sm">
                                    {{ item.project_type || 'Proyek' }}
                                </span>
                            </div>
                        </div>

                        <div class="p-6 space-y-3 flex-1 flex flex-col justify-between">
                            <div class="space-y-2">
                                <div class="flex items-center gap-2 text-xs text-brand-dark/60 font-medium">
                                    <MapPin class="w-3.5 h-3.5 text-brand-primary" />
                                    <span>{{ item.location }}</span>
                                    <span>•</span>
                                    <span>{{ item.completion_year || '2024' }}</span>
                                </div>
                                <h3 class="font-serif text-xl font-bold text-brand-dark group-hover:text-brand-primary transition-colors leading-snug">
                                    {{ item.title }}
                                </h3>
                                <p class="text-xs text-brand-dark/75">
                                    {{ item.ornament_scope }}
                                </p>
                            </div>

                            <div class="pt-4 border-t border-brand-dark/10">
                                <a 
                                    :href="`https://wa.me/6281234567890?text=Halo%20Cahaya%20Ornamen,%20saya%20tertarik%20dengan%20proyek:%20${encodeURIComponent(item.title)}`"
                                    target="_blank"
                                    class="inline-flex items-center gap-1.5 text-xs font-bold text-brand-primary hover:text-brand-dark"
                                >
                                    <span>Tanya Spek Proyek via WA</span>
                                    <ArrowUpRight class="w-3.5 h-3.5" />
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </main>

        <FooterSection />
        <FloatingWhatsapp />
    </div>
</template>
