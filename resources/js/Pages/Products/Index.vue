<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import HeaderNavbar from '@/Components/Frontend/HeaderNavbar.vue';
import ProductCard from '@/Components/Frontend/ProductCard.vue';
import FooterSection from '@/Components/Frontend/FooterSection.vue';
import FloatingWhatsapp from '@/Components/Frontend/FloatingWhatsapp.vue';
import { Search, Filter, Layers } from 'lucide-vue-next';

const props = defineProps({
    categories: {
        type: Array,
        default: () => []
    },
    products: {
        type: Object,
        required: true
    },
    currentCategory: {
        type: String,
        default: null
    },
    searchQuery: {
        type: String,
        default: ''
    }
});

const search = ref(props.searchQuery || '');

const handleSearch = () => {
    router.get(route('products.index'), {
        kategori: props.currentCategory,
        q: search.value
    }, { preserveState: true });
};

const filterByCategory = (slug) => {
    router.get(route('products.index'), {
        kategori: slug,
        q: search.value || undefined
    }, { preserveState: true });
};
</script>

<template>
    <Head title="Katalog Produk Ornamen Arsitektur - Cahaya Ornamen" />

    <div class="min-h-screen bg-brand-cream text-brand-dark font-sans antialiased">
        <HeaderNavbar />

        <main class="pt-32 pb-24">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <!-- Page Title -->
                <div class="max-w-3xl mb-12 space-y-3">
                    <span class="text-xs font-bold uppercase tracking-widest text-brand-primary">
                        Katalog Produk Lengkap
                    </span>
                    <h1 class="font-serif text-4xl sm:text-5xl font-bold text-brand-dark leading-tight">
                        Pilihan Ornamen GRC, Krawangan, Pilar, & Kubah.
                    </h1>
                    <p class="text-base text-brand-dark/80">
                        Eksplorasi koleksi ornamen dengan spesifikasi teknis lengkap, formula semen tahan cuaca tropis, dan dimensi fleksibel.
                    </p>
                </div>

                <!-- Filter Bar & Search Input -->
                <div class="mb-10 p-4 rounded-2xl bg-brand-sand border border-brand-dark/15 flex flex-col md:flex-row items-center justify-between gap-4">
                    
                    <!-- Category Filter Buttons -->
                    <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
                        <button 
                            @click="filterByCategory(null)"
                            class="px-4 py-2 rounded-xl text-xs font-bold transition-all"
                            :class="[!currentCategory ? 'bg-brand-dark text-brand-cta shadow-sm' : 'bg-brand-cream text-brand-dark hover:bg-brand-sand']"
                        >
                            Semua Kategori
                        </button>
                        <button 
                            v-for="cat in categories" 
                            :key="cat.id"
                            @click="filterByCategory(cat.slug)"
                            class="px-4 py-2 rounded-xl text-xs font-bold transition-all"
                            :class="[currentCategory === cat.slug ? 'bg-brand-dark text-brand-cta shadow-sm' : 'bg-brand-cream text-brand-dark hover:bg-brand-sand']"
                        >
                            {{ cat.name }}
                        </button>
                    </div>

                    <!-- Search Input -->
                    <div class="relative w-full md:w-72">
                        <input 
                            v-model="search"
                            @keyup.enter="handleSearch"
                            type="text" 
                            placeholder="Cari ornamen..."
                            class="w-full pl-9 pr-4 py-2 rounded-xl bg-brand-cream border border-brand-dark/20 text-xs text-brand-dark focus:ring-2 focus:ring-brand-primary outline-none"
                        />
                        <Search class="w-4 h-4 text-brand-dark/50 absolute left-3 top-2.5" />
                    </div>

                </div>

                <!-- Products Grid -->
                <div v-if="products.data && products.data.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    <ProductCard 
                        v-for="product in products.data" 
                        :key="product.id"
                        :product="product"
                    />
                </div>

                <!-- Empty State -->
                <div v-else class="text-center py-20 bg-brand-sand rounded-3xl p-8 border border-brand-dark/15 space-y-3">
                    <Layers class="w-12 h-12 text-brand-primary mx-auto opacity-50" />
                    <h3 class="font-serif text-xl font-bold text-brand-dark">Belum ada produk yang cocok</h3>
                    <p class="text-sm text-brand-dark/70">Coba ubah kata kunci pencarian atau pilih kategori lain.</p>
                </div>

            </div>
        </main>

        <FooterSection />
        <FloatingWhatsapp />
    </div>
</template>
