<script setup>
import { Link } from '@inertiajs/vue3';
import { MapPin, Phone, Mail, Clock, ShieldCheck, Heart } from 'lucide-vue-next';

defineProps({
    contactInfo: {
        type: Object,
        default: () => ({
            brand_name: 'Cahaya Ornamen',
            phone: '+62 812-3456-7890',
            whatsapp_number: '6281234567890',
            email: 'halo@cahayaornamen.com',
            workshop_address: 'Jl. Raya Industri Arsitektur No. 88, Kawasan Workshop GRC, Jawa Timur, Indonesia',
            working_hours: 'Senin - Sabtu: 08.00 - 17.00 WIB'
        })
    }
});
</script>

<template>
    <footer class="bg-brand-dark text-brand-cream border-t border-brand-primary/20 pt-16 pb-8 relative overflow-hidden">
        
        <!-- Subtle Accent Top Border Line -->
        <div class="h-1.5 w-full bg-gradient-to-r from-brand-primary via-brand-cta to-brand-primary absolute top-0 left-0"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-10 pb-12 border-b border-brand-cream/10">
                
                <!-- Col 1: Identity & About (2 cols on large screen) -->
                <div class="lg:col-span-2 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-brand-deep flex items-center justify-center text-brand-cta border border-brand-cta/20">
                            <!-- Geometric GRC Motif Icon -->
                            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M12 2L2 12l10 10 10-10L12 2z" />
                                <path d="M12 6L6 12l6 6 6-6-6-6z" />
                                <circle cx="12" cy="12" r="1.5" fill="currentColor" />
                            </svg>
                        </div>
                        <span class="font-serif text-2xl font-bold tracking-tight text-brand-cream">
                            CAHAYA <span class="text-brand-cta font-normal italic">ORNAMEN</span>
                        </span>
                    </div>

                    <p class="text-xs sm:text-sm text-brand-cream/75 leading-relaxed max-w-sm">
                        Produsen dan kontraktor ornamen arsitektur terkemuka spesialis GRC Krawangan, pilar klasik Eropa, kubah masjid, dan relief fasad dengan presisi cetakan tinggi.
                    </p>

                    <div class="pt-2 flex items-center gap-3 text-xs text-brand-cream/80">
                        <div class="flex items-center gap-1.5 px-3 py-1 rounded bg-brand-deep border border-brand-primary/30">
                            <ShieldCheck class="w-3.5 h-3.5 text-brand-cta" />
                            <span>Kualitas Standard SNI & AR-Glass</span>
                        </div>
                    </div>
                </div>

                <!-- Col 2: Kategori Ornamen Populer -->
                <div class="space-y-3">
                    <h4 class="font-serif text-base font-bold text-brand-cta uppercase tracking-wider">
                        Kategori Ornamen
                    </h4>
                    <ul class="space-y-2 text-xs sm:text-sm text-brand-cream/80">
                        <li>
                            <Link :href="route('products.index', { kategori: 'grc-krawangan' })" class="hover:text-brand-cta transition-colors">
                                GRC Krawangan Fasad
                            </Link>
                        </li>
                        <li>
                            <Link :href="route('products.index', { kategori: 'kubah-masjid-grc' })" class="hover:text-brand-cta transition-colors">
                                Kubah Masjid GRC
                            </Link>
                        </li>
                        <li>
                            <Link :href="route('products.index', { kategori: 'pilar-kolom-klasik' })" class="hover:text-brand-cta transition-colors">
                                Pilar Korintus & Doria
                            </Link>
                        </li>
                        <li>
                            <Link :href="route('products.index', { kategori: 'lis-profil-cornice' })" class="hover:text-brand-cta transition-colors">
                                Lis Profil & Cornice
                            </Link>
                        </li>
                        <li>
                            <Link :href="route('products.index', { kategori: 'ornamen-masif-relief' })" class="hover:text-brand-cta transition-colors">
                                Relief Kaligrafi & Mural 3D
                            </Link>
                        </li>
                    </ul>
                </div>

                <!-- Col 3: Navigasi Cepat -->
                <div class="space-y-3">
                    <h4 class="font-serif text-base font-bold text-brand-cta uppercase tracking-wider">
                        Perusahaan
                    </h4>
                    <ul class="space-y-2 text-xs sm:text-sm text-brand-cream/80">
                        <li>
                            <a href="#about" class="hover:text-brand-cta transition-colors">
                                Tentang Cahaya Ornamen
                            </a>
                        </li>
                        <li>
                            <a href="#services" class="hover:text-brand-cta transition-colors">
                                Layanan & Desain Kustom
                            </a>
                        </li>
                        <li>
                            <Link :href="route('portfolios.index')" class="hover:text-brand-cta transition-colors">
                                Galeri Proyek Lapangan
                            </Link>
                        </li>
                        <li>
                            <Link :href="route('articles.index')" class="hover:text-brand-cta transition-colors">
                                Tips & Artikel Arsitektur
                            </Link>
                        </li>
                        <li>
                            <Link :href="route('contact.index')" class="hover:text-brand-cta transition-colors">
                                Kontak & Alamat Workshop
                            </Link>
                        </li>
                    </ul>
                </div>

                <!-- Col 4: Informasi Workshop & Hotline -->
                <div class="space-y-3">
                    <h4 class="font-serif text-base font-bold text-brand-cta uppercase tracking-wider">
                        Workshop & Kontak
                    </h4>
                    <ul class="space-y-2.5 text-xs sm:text-sm text-brand-cream/80">
                        <li class="flex items-start gap-2">
                            <MapPin class="w-4 h-4 text-brand-cta shrink-0 mt-0.5" />
                            <span>{{ contactInfo.workshop_address }}</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <Phone class="w-4 h-4 text-brand-cta shrink-0" />
                            <span>{{ contactInfo.phone }}</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <Mail class="w-4 h-4 text-brand-cta shrink-0" />
                            <span>{{ contactInfo.email }}</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <Clock class="w-4 h-4 text-brand-cta shrink-0" />
                            <span>{{ contactInfo.working_hours }}</span>
                        </li>
                    </ul>
                </div>

            </div>

            <!-- Bottom Copyright & Credit -->
            <div class="pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-brand-cream/60">
                <div>
                    &copy; {{ new Date().getFullYear() }} Cahaya Ornamen (cahayaornamen.com). Hak Cipta Dilindungi Undang-Undang.
                </div>
                <div class="flex items-center gap-4">
                    <span>Spesialis Ornamen Arsitektur & GRC Presisi</span>
                </div>
            </div>

        </div>
    </footer>
</template>
