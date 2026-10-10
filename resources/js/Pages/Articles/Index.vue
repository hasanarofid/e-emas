<script setup>
import { Head, Link } from '@inertiajs/vue3';
import HeaderNavbar from '@/Components/Frontend/HeaderNavbar.vue';
import FooterSection from '@/Components/Frontend/FooterSection.vue';
import FloatingWhatsapp from '@/Components/Frontend/FloatingWhatsapp.vue';
import { Clock, ArrowRight } from 'lucide-vue-next';

defineProps({
    articles: {
        type: Object,
        required: true
    },
    categories: {
        type: Array,
        default: () => []
    }
});
</script>

<template>
    <Head title="Tips & Artikel Arsitektur Ornamen - Cahaya Ornamen" />

    <div class="min-h-screen bg-brand-cream text-brand-dark font-sans antialiased">
        <HeaderNavbar />

        <main class="pt-32 pb-24">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <!-- Page Title -->
                <div class="max-w-3xl mb-14 space-y-3">
                    <span class="text-xs font-bold uppercase tracking-widest text-brand-primary">
                        Wawasan & Tips
                    </span>
                    <h1 class="font-serif text-4xl sm:text-5xl font-bold text-brand-dark leading-tight">
                        Artikel, Panduan Material GRC, & Inspirasi Desain Fasad.
                    </h1>
                    <p class="text-base text-brand-dark/80">
                        Artikel edukatif mengenai teknik pemasangan, kalkulasi ketebalan, dan estetika arsitektur klasik dunia.
                    </p>
                </div>

                <!-- Articles Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <article 
                        v-for="post in (articles.data || articles)" 
                        :key="post.id"
                        class="group rounded-2xl bg-brand-cream border border-brand-dark/15 overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between"
                    >
                        <div class="relative h-56 overflow-hidden bg-brand-sand">
                            <img 
                                :src="post.image_url" 
                                :alt="post.title"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out" 
                            />
                            <div class="absolute top-3 left-3">
                                <span class="px-2.5 py-1 rounded text-[11px] font-bold uppercase tracking-wider bg-brand-dark/80 text-brand-cta">
                                    {{ post.category?.name || 'Arsitektur' }}
                                </span>
                            </div>
                        </div>

                        <div class="p-6 space-y-3 flex-1 flex flex-col justify-between">
                            <div class="space-y-2">
                                <div class="flex items-center gap-2 text-xs text-brand-dark/60">
                                    <Clock class="w-3.5 h-3.5" />
                                    <span>{{ post.reading_time || 4 }} Menit Baca</span>
                                </div>
                                <h3 class="font-serif text-xl font-bold text-brand-dark group-hover:text-brand-primary transition-colors leading-snug line-clamp-2">
                                    <Link :href="route('articles.show', post.slug)">
                                        {{ post.title }}
                                    </Link>
                                </h3>
                                <p class="text-xs text-brand-dark/75 line-clamp-3">
                                    {{ post.excerpt }}
                                </p>
                            </div>

                            <div class="pt-4 border-t border-brand-dark/10">
                                <Link 
                                    :href="route('articles.show', post.slug)"
                                    class="inline-flex items-center gap-1.5 text-xs font-bold text-brand-primary hover:text-brand-dark"
                                >
                                    <span>Baca Artikel</span>
                                    <ArrowRight class="w-3.5 h-3.5" />
                                </Link>
                            </div>
                        </div>
                    </article>
                </div>

            </div>
        </main>

        <FooterSection />
        <FloatingWhatsapp />
    </div>
</template>
