<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import ProductCard from './ProductCard.vue';
import { ArrowRight, LayoutGrid, Check } from 'lucide-vue-next';

const props = defineProps({
    categories: {
        type: Array,
        default: () => []
    },
    featuredProducts: {
        type: Array,
        default: () => []
    },
    whatsappNumber: {
        type: String,
        default: '6281316053870'
    }
});

const activeCategory = ref('all');

const filteredProducts = computed(() => {
    if (activeCategory.value === 'all') {
        return props.featuredProducts;
    }
    return props.featuredProducts.filter(p => {
        const catName = p.category?.name?.toLowerCase() || '';
        return catName.includes(activeCategory.value.toLowerCase());
    });
});
</script>

<template>
    <section class="py-24 bg-brand-sand/40 border-y border-brand-dark/10 relative overflow-hidden">
        
        <!-- Subtle Arabesque Watermark on Left - Straight & Ultra Light -->
        <div class="absolute -left-20 sm:-left-28 top-1/2 -translate-y-1/2 w-[340px] sm:w-[420px] pointer-events-none select-none opacity-[0.05] sm:opacity-[0.07] mix-blend-multiply z-0">
            <img 
                src="/images/ornaments/mandala-transparent.png" 
                alt="Ornamen Arabesque Background" 
                class="w-full h-auto object-contain"
                loading="lazy"
            />
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            
            <!-- Section Header -->
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-14 gap-6">
                <div class="max-w-2xl space-y-3">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-brand-sand text-brand-dark text-xs font-bold uppercase tracking-widest border border-brand-primary/20">
                        Katalog Produk Terpilih
                    </div>
                    <h2 class="font-serif text-3xl sm:text-4xl md:text-5xl font-bold text-brand-dark tracking-tight leading-tight">
                        Mahakarya Ornamen GRC & Arsitektur Klasik.
                    </h2>
                    <p class="text-base text-brand-dark/80 font-normal">
                        Koleksi ornamen dengan ratusan varian motif siap cetak maupun kustom sesuai gambar 3D fasad bangunan Anda.
                    </p>
                </div>

                <!-- Link to Full Catalog -->
                <Link 
                    :href="route('products.index')"
                    class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-brand-cream hover:bg-brand-sand text-brand-dark font-bold text-sm border border-brand-dark/20 shadow-sm transition-all group shrink-0"
                >
                    <span>Lihat Semua Katalog</span>
                    <ArrowRight class="w-4 h-4 transition-transform group-hover:translate-x-1" />
                </Link>
            </div>

            <!-- Categories Showcase Grid (6 Core Categories) -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-16">
                <div 
                    v-for="(cat, idx) in categories" 
                    :key="idx"
                    @click="activeCategory = cat.name"
                    class="cursor-pointer group relative p-4 rounded-xl bg-brand-cream border border-brand-dark/15 hover:border-brand-primary transition-all duration-300 shadow-sm hover:shadow-md flex flex-col items-center text-center space-y-2.5"
                    :class="[activeCategory === cat.name ? 'ring-2 ring-brand-primary bg-brand-sand' : '']"
                >
                    <div class="w-14 h-14 rounded-lg overflow-hidden bg-brand-sand border border-brand-dark/10 group-hover:scale-105 transition-transform">
                        <img 
                            :src="cat.image || 'https://images.unsplash.com/photo-1541888946425-d0fbb186f5f8?auto=format&fit=crop&w=300&q=80'" 
                            :alt="cat.name"
                            class="w-full h-full object-cover" 
                        />
                    </div>
                    <div class="font-serif text-xs sm:text-sm font-bold text-brand-dark group-hover:text-brand-primary leading-tight">
                        {{ cat.name }}
                    </div>
                    <div class="text-[10px] text-brand-dark/60 font-medium">
                        {{ cat.count || 'Koleksi Lengkap' }}
                    </div>
                </div>
            </div>

            <!-- Filter Reset Button (if filtered) -->
            <div v-if="activeCategory !== 'all'" class="mb-6 flex items-center justify-between bg-brand-cream p-3 rounded-lg border border-brand-dark/10">
                <span class="text-xs font-semibold text-brand-dark">
                    Menampilkan kategori: <strong>{{ activeCategory }}</strong>
                </span>
                <button 
                    @click="activeCategory = 'all'" 
                    class="text-xs font-bold text-brand-primary hover:underline"
                >
                    Tampilkan Semua Produk
                </button>
            </div>

            <!-- Products Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                <ProductCard 
                    v-for="product in filteredProducts" 
                    :key="product.id"
                    :product="product"
                    :whatsapp-number="whatsappNumber"
                />
            </div>

            <!-- Bottom Custom Moulding Callout -->
            <div class="mt-16 text-center bg-brand-cream rounded-2xl p-8 border border-brand-dark/15 shadow-sm space-y-3">
                <h3 class="font-serif text-xl sm:text-2xl font-bold text-brand-dark">
                    Tidak Menemukan Motif Ornamen yang Anda Cari?
                </h3>
                <p class="text-sm text-brand-dark/75 max-w-2xl mx-auto">
                    Kami menerima pesanan kustom (custom molding) dari desain sket tangan, foto referensi, hingga file CAD 3D SketchUp/Revit tanpa batasan kerumitan motif.
                </p>
                <div class="pt-2">
                    <a 
                        :href="`https://wa.me/${whatsappNumber}?text=Halo%20Cahaya%20Ornamen,%20saya%20punya%20desain%20custom%20sendiri%20dan%20ingin%20dibuatkan%20cetakan%20GRC`"
                        target="_blank"
                        class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-brand-dark text-brand-cta font-bold text-sm hover:bg-black transition-colors"
                    >
                        <span>Konsultasi Cetakan Custom Gratis</span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </div>

        </div>
    </section>
</template>
