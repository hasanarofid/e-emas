<script setup>
import { Check, Hammer, Award, Compass, Sparkles } from 'lucide-vue-next';

defineProps({
    whatsappNumber: {
        type: String,
        default: '6281234567890'
    }
});
</script>

<template>
    <section id="about" class="py-24 bg-brand-sand/50 relative overflow-hidden border-y border-brand-dark/10">
        
        <!-- Subtle Arabesque Watermark on Right - Straight & Ultra Light -->
        <div class="absolute -right-20 sm:-right-28 top-1/2 -translate-y-1/2 w-[340px] sm:w-[420px] pointer-events-none select-none opacity-[0.05] sm:opacity-[0.07] mix-blend-multiply z-0">
            <img 
                src="/images/ornaments/mandala-transparent.png" 
                alt="Ornamen Arabesque Background" 
                class="w-full h-auto object-contain transform scale-x-[-1]"
                loading="lazy"
            />
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-center">
                
                <!-- Left: Overlapping Photo Composition (5 cols) -->
                <div class="lg:col-span-5 relative order-2 lg:order-1">
                    
                    <!-- Decorative Backdrop Box -->
                    <div class="w-full h-full absolute -top-4 -left-4 bg-brand-primary/10 rounded-2xl -z-10 border border-brand-primary/20"></div>

                    <!-- Main Image: Workshop Craftsmanship -->
                    <div class="rounded-2xl overflow-hidden shadow-xl border border-brand-dark/15">
                        <img 
                            src="https://images.unsplash.com/photo-1581092160607-ee22621dd758?auto=format&fit=crop&w=800&q=80" 
                            alt="Proses Pembuatan Cetakan GRC Cahaya Ornamen" 
                            class="w-full h-[380px] sm:h-[440px] object-cover hover:scale-105 transition-transform duration-700 ease-out"
                        />
                    </div>

                    <!-- Overlapping Secondary Detail Image -->
                    <div class="absolute -bottom-8 -right-4 sm:-right-8 w-48 sm:w-56 rounded-xl overflow-hidden shadow-2xl border-2 border-brand-cream hidden sm:block">
                        <img 
                            src="https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=400&q=80" 
                            alt="Detail Tekstur Relief Ornamen Semen GRC" 
                            class="w-full h-36 object-cover"
                        />
                        <div class="p-2.5 bg-brand-dark text-white text-[11px] font-medium text-center">
                            Detail Relief Finis Halus
                        </div>
                    </div>

                    <!-- Badge Highlight -->
                    <div class="absolute top-6 left-6 bg-brand-dark/90 text-brand-cta text-xs font-bold px-3 py-1.5 rounded-md backdrop-blur-sm border border-brand-cta/20 flex items-center gap-1.5">
                        <Sparkles class="w-3.5 h-3.5" />
                        <span>Pabrikasi Mandiri</span>
                    </div>

                </div>

                <!-- Right: Editorial Narrative Content (7 cols) -->
                <div class="lg:col-span-7 space-y-6 order-1 lg:order-2">
                    
                    <div class="space-y-2">
                        <span class="text-xs sm:text-sm font-bold tracking-widest text-brand-primary uppercase">
                            Tentang Cahaya Ornamen
                        </span>
                        <h2 class="font-serif text-3xl sm:text-4xl md:text-5xl font-bold text-brand-dark leading-tight">
                            Menyatukan Seni Arsitektur Klasik dengan Ketangguhan Teknologi Modern.
                        </h2>
                    </div>

                    <p class="text-base sm:text-lg text-brand-dark/85 leading-relaxed font-normal">
                        Didirikan dengan dedikasi pada keindahan ornamen arsitektur nusantara dan klasik dunia, 
                        <strong>Cahaya Ornamen</strong> telah melayani ratusan proyek masjid, gedung instansi, dan residensial mewah di berbagai wilayah Indonesia.
                    </p>

                    <p class="text-sm sm:text-base text-brand-dark/80 leading-relaxed">
                        Kami tidak sekadar mencetak ornamen, melainkan melakukan kalkulasi beban struktur, pemilihan formula bahan baku tahan alkali (*Alkali Resistant Glassfibre*), serta penyediaan rangka baja galvanis yang kokoh untuk memastikan setiap panel GRC terpasang dengan presisi dan awet puluhan tahun.
                    </p>

                    <!-- 4 Pillars of Excellence Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4">
                        <div class="p-4 rounded-xl bg-brand-cream border border-brand-dark/10 shadow-sm flex items-start gap-3.5">
                            <div class="p-2 rounded-lg bg-brand-dark text-brand-cta shrink-0">
                                <Award class="w-5 h-5" />
                            </div>
                            <div>
                                <h4 class="font-bold text-sm text-brand-dark">Kualitas Teruji</h4>
                                <p class="text-xs text-brand-dark/70 mt-0.5">Semen mutu tinggi & serat fiber impor tahan alkali.</p>
                            </div>
                        </div>

                        <div class="p-4 rounded-xl bg-brand-cream border border-brand-dark/10 shadow-sm flex items-start gap-3.5">
                            <div class="p-2 rounded-lg bg-brand-dark text-brand-cta shrink-0">
                                <Compass class="w-5 h-5" />
                            </div>
                            <div>
                                <h4 class="font-bold text-sm text-brand-dark">Custom Design 3D</h4>
                                <p class="text-xs text-brand-dark/70 mt-0.5">Motif apapun dapat dibuat sesuai gambar arsitek.</p>
                            </div>
                        </div>

                        <div class="p-4 rounded-xl bg-brand-cream border border-brand-dark/10 shadow-sm flex items-start gap-3.5">
                            <div class="p-2 rounded-lg bg-brand-dark text-brand-cta shrink-0">
                                <Hammer class="w-5 h-5" />
                            </div>
                            <div>
                                <h4 class="font-bold text-sm text-brand-dark">Bobot 80% Lebih Ringan</h4>
                                <p class="text-xs text-brand-dark/70 mt-0.5">Mengurangi beban struktur gedung & hemat fondasi.</p>
                            </div>
                        </div>

                        <div class="p-4 rounded-xl bg-brand-cream border border-brand-dark/10 shadow-sm flex items-start gap-3.5">
                            <div class="p-2 rounded-lg bg-brand-dark text-brand-cta shrink-0">
                                <Check class="w-5 h-5" />
                            </div>
                            <div>
                                <h4 class="font-bold text-sm text-brand-dark">Garansi Hasil Pasang</h4>
                                <p class="text-xs text-brand-dark/70 mt-0.5">Didukung teknisi berpengalaman sertifikasi K3.</p>
                            </div>
                        </div>
                    </div>

                    <!-- CTA Mini Action -->
                    <div class="pt-2">
                        <a 
                            :href="`https://wa.me/${whatsappNumber}?text=Halo%20Cahaya%20Ornamen,%20saya%20ingin%20tahu%20lebih%20banyak%20mengenai%20spesifikasi%20material%20GRC`"
                            target="_blank"
                            class="inline-flex items-center gap-2 text-sm font-bold text-brand-primary hover:text-brand-dark underline underline-offset-4 transition-colors"
                        >
                            <span>Pelajari Spesifikasi Material & Kunjungi Workshop Kami</span>
                            <span>&rarr;</span>
                        </a>
                    </div>

                </div>

            </div>

        </div>
    </section>
</template>
