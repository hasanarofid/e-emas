<script setup>
import { Head, Link } from '@inertiajs/vue3';
import HeaderNavbar from '@/Components/Frontend/HeaderNavbar.vue';
import FooterSection from '@/Components/Frontend/FooterSection.vue';
import FloatingWhatsapp from '@/Components/Frontend/FloatingWhatsapp.vue';
import { ArrowLeft, Clock, Calendar, MessageSquare, Share2 } from 'lucide-vue-next';

defineProps({
    article: {
        type: Object,
        required: true
    },
    relatedArticles: {
        type: Array,
        default: () => []
    }
});
</script>

<template>
    <Head :title="`${article.title} - Cahaya Ornamen`" />

    <div class="min-h-screen bg-brand-cream text-brand-dark font-sans antialiased">
        <HeaderNavbar />

        <main class="pt-32 pb-24">
            <article class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <!-- Back Link -->
                <div class="mb-8">
                    <Link :href="route('articles.index')" class="inline-flex items-center gap-2 text-xs font-bold text-brand-primary hover:text-brand-dark transition-colors">
                        <ArrowLeft class="w-4 h-4" />
                        <span>Kembali ke Semua Artikel</span>
                    </Link>
                </div>

                <!-- Article Header -->
                <header class="space-y-4 mb-8">
                    <span class="px-3 py-1 rounded text-xs font-bold uppercase tracking-wider bg-brand-sand text-brand-dark border border-brand-primary/20">
                        {{ article.category?.name || 'Tips Arsitektur' }}
                    </span>
                    <h1 class="font-serif text-3xl sm:text-4xl md:text-5xl font-bold text-brand-dark leading-tight">
                        {{ article.title }}
                    </h1>
                    <div class="flex items-center gap-4 text-xs text-brand-dark/70 font-medium">
                        <span class="flex items-center gap-1.5">
                            <Clock class="w-4 h-4 text-brand-primary" />
                            {{ article.reading_time || 4 }} Menit Baca
                        </span>
                    </div>
                </header>

                <!-- Featured Image -->
                <div class="rounded-3xl overflow-hidden shadow-xl mb-12 border border-brand-dark/15 max-h-[480px]">
                    <img 
                        :src="article.image_url" 
                        :alt="article.title"
                        class="w-full h-full object-cover" 
                    />
                </div>

                <!-- Article Body Content -->
                <div class="prose prose-lg max-w-none text-brand-dark/90 leading-relaxed font-normal space-y-6">
                    <p class="text-lg sm:text-xl font-serif italic text-brand-dark leading-relaxed">
                        {{ article.excerpt }}
                    </p>
                    <div v-html="article.content"></div>
                </div>

                <!-- Bottom Consultation Box in Article -->
                <div class="mt-16 p-8 rounded-2xl bg-brand-sand border border-brand-dark/15 flex flex-col sm:flex-row items-center justify-between gap-6">
                    <div class="space-y-1">
                        <h4 class="font-serif text-lg font-bold text-brand-dark">Punya Pertanyaan Teknis Seputar Ornamen?</h4>
                        <p class="text-xs text-brand-dark/75">Konsultasikan kebutuhan proyek Anda langsung bersama tim arsitek & fabrikator kami.</p>
                    </div>
                    <a 
                        href="https://wa.me/6281234567890?text=Halo%20Cahaya%20Ornamen,%20saya%20membaca%20artikel%20dan%20ingin%20konsultasi"
                        target="_blank"
                        class="px-5 py-2.5 rounded-xl bg-brand-cta text-brand-dark font-bold text-xs shadow-md hover:bg-yellow-300 transition-all shrink-0 flex items-center gap-2"
                    >
                        <MessageSquare class="w-4 h-4 text-brand-dark" />
                        <span>Tanya Arsitek via WA</span>
                    </a>
                </div>

            </article>
        </main>

        <FooterSection />
        <FloatingWhatsapp />
    </div>
</template>
