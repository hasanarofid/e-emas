<script setup>
import { Link } from '@inertiajs/vue3';
import { Clock, ArrowRight, BookOpen } from 'lucide-vue-next';

defineProps({
    articles: {
        type: Array,
        default: () => []
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
                        Edukasi & Wawasan Teknis
                    </div>
                    <h2 class="font-serif text-3xl sm:text-4xl md:text-5xl font-bold text-brand-dark tracking-tight leading-tight">
                        Wawasan Seputar Ornamen, GRC, & Arsitektur Tropis.
                    </h2>
                    <p class="text-base text-brand-dark/75">
                        Panduan praktis bagi pemilik rumah, arsitek, dan panitia pembangunan dalam menentukan jenis ornamen yang tepat dan efisien.
                    </p>
                </div>

                <Link 
                    :href="route('articles.index')"
                    class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-transparent hover:bg-brand-sand text-brand-dark font-bold text-sm border-2 border-brand-primary/40 hover:border-brand-primary transition-all group shrink-0"
                >
                    <span>Baca Semua Artikel</span>
                    <ArrowRight class="w-4 h-4 transition-transform group-hover:translate-x-1" />
                </Link>
            </div>

            <!-- Articles 3 Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <article 
                    v-for="post in articles" 
                    :key="post.id"
                    class="group rounded-2xl bg-brand-cream border border-brand-dark/15 overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between"
                >
                    <!-- Post Thumbnail -->
                    <div class="relative h-56 overflow-hidden bg-brand-sand">
                        <img 
                            :src="post.image_url" 
                            :alt="post.title"
                            class="w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-105"
                            loading="lazy" 
                        />
                        <div class="absolute top-3 left-3">
                            <span class="px-2.5 py-1 rounded text-[11px] font-bold uppercase tracking-wider bg-brand-dark/85 text-brand-cta backdrop-blur-sm">
                                {{ post.category?.name || 'Arsitektur' }}
                            </span>
                        </div>
                    </div>

                    <!-- Post Content -->
                    <div class="p-6 flex-1 flex flex-col justify-between space-y-4">
                        <div class="space-y-2.5">
                            <!-- Meta: Reading Time -->
                            <div class="flex items-center gap-2 text-xs text-brand-dark/60">
                                <Clock class="w-3.5 h-3.5" />
                                <span>{{ post.reading_time || 4 }} Menit Baca</span>
                                <span>•</span>
                                <span>{{ post.published_at || 'Terbaru' }}</span>
                            </div>

                            <!-- Title -->
                            <h3 class="font-serif text-xl font-bold text-brand-dark group-hover:text-brand-primary transition-colors leading-snug line-clamp-2">
                                <Link :href="route('articles.show', post.slug)">
                                    {{ post.title }}
                                </Link>
                            </h3>

                            <!-- Excerpt -->
                            <p class="text-xs sm:text-sm text-brand-dark/75 line-clamp-3 leading-relaxed">
                                {{ post.excerpt }}
                            </p>
                        </div>

                        <!-- Read More Link -->
                        <div class="pt-4 border-t border-brand-dark/10">
                            <Link 
                                :href="route('articles.show', post.slug)"
                                class="inline-flex items-center gap-1.5 text-xs font-bold text-brand-primary hover:text-brand-dark transition-colors group-hover:translate-x-1 duration-200"
                            >
                                <span>Baca Selengkapnya</span>
                                <ArrowRight class="w-3.5 h-3.5" />
                            </Link>
                        </div>
                    </div>
                </article>
            </div>

        </div>
    </section>
</template>
