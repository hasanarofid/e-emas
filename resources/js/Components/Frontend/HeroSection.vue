<script setup>
import { onMounted, ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import { MessageSquare, ArrowRight, ShieldCheck, Sparkles, Layers, CheckCircle2 } from 'lucide-vue-next';

defineProps({
    whatsappNumber: {
        type: String,
        default: '6281234567890'
    }
});

const heroRef = ref(null);
</script>

<template>
    <section ref="heroRef" class="relative pt-32 pb-20 md:pt-40 md:pb-28 overflow-hidden bg-brand-cream">
        
        <!-- Subtle Architectural Background Grid & Texture -->
        <div class="absolute inset-0 pointer-events-none opacity-20">
            <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="arch-grid" width="80" height="80" patternUnits="userSpaceOnUse">
                        <path d="M 80 0 L 0 0 0 80" fill="none" stroke="#063b00" stroke-width="0.8" stroke-dasharray="2,6" />
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#arch-grid)" />
            </svg>
        </div>

        <!-- Ambient Warm Glow -->
        <div class="absolute -top-24 right-10 w-96 h-96 bg-brand-sand rounded-full blur-3xl opacity-50 pointer-events-none"></div>
        <div class="absolute bottom-0 -left-20 w-80 h-80 bg-brand-light/10 rounded-full blur-2xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">
                
                <!-- Left Asymmetrical Content Column (7 cols) -->
                <div class="lg:col-span-7 space-y-6 sm:space-y-8">
                    
                    <!-- Top Eyebrow Badge -->
                    <div class="inline-flex items-center gap-2.5 px-4 py-1.5 rounded-full bg-brand-sand border border-brand-primary/20 shadow-sm">
                        <span class="w-2 h-2 rounded-full bg-brand-primary animate-pulse"></span>
                        <span class="text-xs sm:text-sm font-semibold tracking-wide text-brand-dark uppercase">
                            Spesialis Ornamen Arsitektur & GRC Presisi
                        </span>
                    </div>

                    <!-- Majestic Serif Heading -->
                    <h1 class="font-serif text-4xl sm:text-5xl md:text-6xl font-extrabold text-brand-dark leading-[1.12] tracking-tight">
                        Kemewahan Ornamen <br class="hidden sm:inline" />
                        <span class="italic font-normal text-brand-primary">Arsitektur</span> dengan Presisi Tanpa Kompromi.
                    </h1>

                    <!-- Editorial Subtitle -->
                    <p class="text-base sm:text-lg md:text-xl text-brand-dark/80 font-normal leading-relaxed max-w-2xl">
                        Mewujudkan karakter megah pada fasad masjid, hunian klasik, dan gedung prestisius melalui 
                        <strong class="font-semibold text-brand-dark"> GRC Krawangan</strong>, pilar klasik, lis profil, serta kubah arsitektur berbahan semen serat khusus tahan cuaca tropis ekstrem.
                    </p>

                    <!-- Key Selling Points Badges -->
                    <div class="flex flex-wrap items-center gap-y-2 gap-x-6 text-xs sm:text-sm text-brand-dark/90 font-medium pt-1">
                        <div class="flex items-center gap-2">
                            <CheckCircle2 class="w-4 h-4 text-brand-primary flex-shrink-0" />
                            <span>100% Anti-Retak & Anti-Lumut</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <CheckCircle2 class="w-4 h-4 text-brand-primary flex-shrink-0" />
                            <span>Custom Motif CAD/3D CNC</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <CheckCircle2 class="w-4 h-4 text-brand-primary flex-shrink-0" />
                            <span>Instalasi Seantero Indonesia</span>
                        </div>
                    </div>

                    <!-- Asymmetrical Action Buttons -->
                    <div class="pt-2 flex flex-col sm:flex-row items-stretch sm:items-center gap-4">
                        
                        <!-- Prominent CTA Highlight (Color: #e1e100) -->
                        <a 
                            :href="`https://wa.me/${whatsappNumber}?text=Halo%20Cahaya%20Ornamen,%20saya%20ingin%20konsultasi%20desain%20dan%20estimasi%20RAB%20ornamen`"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center justify-center gap-3 px-8 py-4 rounded-xl bg-brand-cta text-brand-dark font-extrabold text-base tracking-wide shadow-cta-glow hover:bg-yellow-300 transition-all duration-300 transform hover:-translate-y-1 active:translate-y-0 border border-brand-dark/25 group"
                        >
                            <MessageSquare class="w-5 h-5 text-brand-dark stroke-[2.4]" />
                            <span>Konsultasi & Estimasi RAB</span>
                            <ArrowRight class="w-4 h-4 transition-transform group-hover:translate-x-1" />
                        </a>

                        <!-- Secondary Elegant Link Button -->
                        <Link 
                            :href="route('products.index')"
                            class="inline-flex items-center justify-center gap-2.5 px-7 py-4 rounded-xl bg-transparent hover:bg-brand-sand text-brand-dark font-semibold text-base border-2 border-brand-primary/40 hover:border-brand-primary transition-all duration-200"
                        >
                            <span>Eksplorasi Katalog</span>
                            <Layers class="w-4 h-4 text-brand-primary" />
                        </Link>
                    </div>

                    <!-- Client Trust Counter Ribbon -->
                    <div class="pt-6 border-t border-brand-dark/10 grid grid-cols-3 gap-4 text-brand-dark">
                        <div>
                            <div class="font-serif text-2xl sm:text-3xl font-bold text-brand-dark">15+</div>
                            <div class="text-[11px] sm:text-xs text-brand-dark/70 uppercase tracking-wider font-medium">Tahun Pengalaman</div>
                        </div>
                        <div>
                            <div class="font-serif text-2xl sm:text-3xl font-bold text-brand-dark">350+</div>
                            <div class="text-[11px] sm:text-xs text-brand-dark/70 uppercase tracking-wider font-medium">Proyek Terpasang</div>
                        </div>
                        <div>
                            <div class="font-serif text-2xl sm:text-3xl font-bold text-brand-dark">2.500m²</div>
                            <div class="text-[11px] sm:text-xs text-brand-dark/70 uppercase tracking-wider font-medium">Luas Workshop Pabrik</div>
                        </div>
                    </div>

                </div>

                <!-- Right Overlapping Visual Composition (5 cols) -->
                <div class="lg:col-span-5 relative mt-6 lg:mt-0">
                    
                    <!-- Decorative Architectural Frame Backdrop -->
                    <div class="absolute -inset-4 sm:-inset-6 bg-brand-sand/80 rounded-3xl -rotate-1 border border-brand-primary/20 shadow-architectural pointer-events-none"></div>

                    <!-- Main Hero Visual Card -->
                    <div class="relative rounded-2xl overflow-hidden shadow-2xl border border-brand-dark/15 bg-brand-dark group">
                        <img 
                            src="https://images.unsplash.com/photo-1541888946425-d0fbb186f5f8?auto=format&fit=crop&w=1200&q=85" 
                            alt="Fasad Ornamen GRC Krawangan Modern Cahaya Ornamen" 
                            class="w-full h-[420px] sm:h-[500px] object-cover object-center transition-transform duration-700 ease-out group-hover:scale-105"
                            loading="eager"
                        />
                        <div class="absolute inset-0 bg-gradient-to-t from-brand-dark/80 via-transparent to-black/20 pointer-events-none"></div>
                        
                        <!-- Caption on Photo -->
                        <div class="absolute bottom-6 left-6 right-6 text-white space-y-1">
                            <span class="inline-block px-2.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-brand-primary text-white">
                                Masterpiece Project
                            </span>
                            <h3 class="font-serif text-lg sm:text-xl font-bold text-brand-cream">
                                Fasad GRC Krawangan Masjid Al-Munawwar
                            </h3>
                            <p class="text-xs text-brand-cream/80">
                                Malang • Luas 450 m² • Selesai 2024
                            </p>
                        </div>
                    </div>

                    <!-- Overlapping Floating Card 1: Top Right Material Spec -->
                    <div class="absolute -top-6 -right-3 sm:-right-8 bg-brand-cream/95 backdrop-blur-md rounded-xl p-4 border border-brand-dark/15 shadow-xl max-w-[210px] hidden sm:block animate-float">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-brand-dark text-brand-cta flex items-center justify-center">
                                <ShieldCheck class="w-5 h-5" />
                            </div>
                            <div>
                                <div class="text-xs font-bold text-brand-dark">Formula AR-Glass</div>
                                <div class="text-[10px] text-brand-dark/70">Anti Karat & Ringan</div>
                            </div>
                        </div>
                    </div>

                    <!-- Overlapping Floating Card 2: Bottom Left Quality Assurance -->
                    <div class="absolute -bottom-8 -left-3 sm:-left-8 bg-brand-sand/95 backdrop-blur-md rounded-xl p-4 border border-brand-dark/15 shadow-2xl max-w-[240px] transform hover:scale-105 transition-transform">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-brand-primary text-white flex items-center justify-center">
                                <Sparkles class="w-5 h-5" />
                            </div>
                            <div>
                                <div class="text-xs font-bold text-brand-dark">Presisi Milimeter</div>
                                <div class="text-[10px] text-brand-dark/80">Cetakan CNC Master Mold</div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </section>
</template>

<style scoped>
@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-8px); }
}
.animate-float {
    animation: float 5s ease-in-out infinite;
}
</style>
