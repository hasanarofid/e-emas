<script setup>
import { Link } from '@inertiajs/vue3';
import { ArrowUpRight, MessageSquare, Layers, Ruler } from 'lucide-vue-next';

const props = defineProps({
    product: {
        type: Object,
        required: true
    },
    whatsappNumber: {
        type: String,
        default: '6281316053870'
    }
});
</script>

<template>
    <div class="group relative rounded-2xl bg-brand-cream border border-brand-dark/15 overflow-hidden shadow-sm hover:shadow-card-hover transition-all duration-500 flex flex-col justify-between">
        
        <!-- Image Container with Architectural Frame -->
        <div class="relative w-full h-64 overflow-hidden bg-brand-sand">
            <img 
                :src="product.thumbnail || 'https://images.unsplash.com/photo-1541888946425-d0fbb186f5f8?auto=format&fit=crop&w=800&q=80'" 
                :alt="product.name" 
                class="w-full h-full object-cover object-center transition-transform duration-700 ease-out group-hover:scale-105"
                loading="lazy"
            />
            
            <!-- Category Tag Badge -->
            <div class="absolute top-3.5 left-3.5 z-10">
                <span class="inline-block px-3 py-1 rounded-md text-[11px] font-bold tracking-wider uppercase bg-brand-dark/90 text-brand-cta backdrop-blur-sm border border-brand-cta/20 shadow-sm">
                    {{ product.category?.name || 'Ornamen GRC' }}
                </span>
            </div>

            <!-- Material Spec Overlay Badge -->
            <div class="absolute bottom-3 left-3 right-3 z-10 flex items-center justify-between text-[11px] font-medium text-brand-cream">
                <span class="px-2.5 py-1 rounded-md bg-brand-dark/80 backdrop-blur-sm inline-flex items-center gap-1">
                    <Layers class="w-3 h-3 text-brand-light" />
                    <span class="truncate max-w-[160px]">{{ product.material }}</span>
                </span>
                
                <span v-if="product.dimensions" class="px-2.5 py-1 rounded-md bg-brand-dark/80 backdrop-blur-sm inline-flex items-center gap-1">
                    <Ruler class="w-3 h-3 text-brand-cta" />
                    <span>{{ product.dimensions }}</span>
                </span>
            </div>
        </div>

        <!-- Product Details Body -->
        <div class="p-6 flex-1 flex flex-col justify-between space-y-4">
            
            <div class="space-y-2">
                <h3 class="font-serif text-xl font-bold text-brand-dark group-hover:text-brand-primary transition-colors line-clamp-2 leading-snug">
                    {{ product.name }}
                </h3>
                
                <p class="text-xs sm:text-sm text-brand-dark/75 line-clamp-2 font-normal leading-relaxed">
                    {{ product.short_description }}
                </p>
            </div>

            <!-- Price Estimate & CTA Button -->
            <div class="pt-4 border-t border-brand-dark/10 flex items-center justify-between gap-2">
                <div>
                    <div class="text-[10px] text-brand-dark/60 font-semibold uppercase tracking-wider">Estimasi Harga</div>
                    <div class="font-serif text-sm sm:text-base font-bold text-brand-dark">
                        {{ product.price_range || 'Sesuai Ukuran' }}
                    </div>
                </div>

                <a 
                    :href="`https://wa.me/${whatsappNumber}?text=Halo%20Cahaya%20Ornamen,%20saya%20tertarik%20dengan%20produk:%20${encodeURIComponent(product.name)}`"
                    target="_blank"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-brand-cta text-brand-dark font-bold text-xs tracking-wide shadow-sm hover:bg-yellow-300 transition-colors border border-brand-dark/20 shrink-0"
                    title="Tanya ketersediaan dan custom ukuran via WhatsApp"
                >
                    <MessageSquare class="w-3.5 h-3.5 text-brand-dark" />
                    <span>Tanya WA</span>
                </a>
            </div>

        </div>

    </div>
</template>
