<script setup>
import { 
    Compass, 
    Factory, 
    ShieldCheck, 
    Calculator, 
    ArrowUpRight,
    CheckCircle
} from 'lucide-vue-next';

defineProps({
    services: {
        type: Array,
        default: () => []
    },
    whatsappNumber: {
        type: String,
        default: '6281316053870'
    }
});

// Icon component lookup map
const iconMap = {
    Compass,
    Factory,
    ShieldCheck,
    Calculator
};
</script>

<template>
    <section id="services" class="py-24 bg-brand-cream relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Section Header -->
            <div class="max-w-3xl mb-16 space-y-3">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-brand-sand text-brand-dark text-xs font-bold uppercase tracking-widest border border-brand-primary/20">
                    Layanan Komprehensif
                </div>
                <h2 class="font-serif text-3xl sm:text-4xl md:text-5xl font-bold text-brand-dark tracking-tight leading-tight">
                    Dari Gambar Konsep Arsitek Hingga Instalasi Sempurna di Lapangan.
                </h2>
                <p class="text-base sm:text-lg text-brand-dark/75">
                    Kami mendampingi setiap tahapan proyek ornamen arsitektur dengan standar engineering tinggi untuk menjamin keamanan, keindahan, dan efisiensi biaya.
                </p>
            </div>

            <!-- Services Grid with Editorial Numbering -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div 
                    v-for="(service, idx) in services" 
                    :key="idx"
                    class="group relative p-8 sm:p-10 rounded-2xl bg-brand-sand/70 hover:bg-brand-sand transition-all duration-300 border border-brand-dark/15 hover:border-brand-primary/50 shadow-sm hover:shadow-xl flex flex-col justify-between"
                >
                    <!-- Top Bar: Big Editorial Number + Icon -->
                    <div class="flex items-center justify-between mb-6">
                        <span class="font-serif text-4xl sm:text-5xl font-extrabold text-brand-primary/25 group-hover:text-brand-primary/50 transition-colors">
                            {{ service.number }}
                        </span>
                        <div class="w-12 h-12 rounded-xl bg-brand-dark text-brand-cta flex items-center justify-center shadow-md group-hover:scale-110 transition-transform duration-300">
                            <component :is="iconMap[service.icon] || Compass" class="w-6 h-6 stroke-[1.8]" />
                        </div>
                    </div>

                    <!-- Middle Content -->
                    <div class="space-y-3 mb-6">
                        <h3 class="font-serif text-2xl font-bold text-brand-dark group-hover:text-brand-primary transition-colors">
                            {{ service.title }}
                        </h3>
                        <p class="text-sm sm:text-base text-brand-dark/80 leading-relaxed font-normal">
                            {{ service.description }}
                        </p>
                    </div>

                    <!-- Bottom Tags & Action Link -->
                    <div class="pt-6 border-t border-brand-dark/10 flex flex-wrap items-center justify-between gap-4">
                        <div class="flex flex-wrap gap-2">
                            <span 
                                v-for="(tag, tagIdx) in service.tags" 
                                :key="tagIdx"
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-brand-cream text-[11px] font-semibold text-brand-dark/85 border border-brand-dark/10"
                            >
                                <CheckCircle class="w-3 h-3 text-brand-primary" />
                                <span>{{ tag }}</span>
                            </span>
                        </div>

                        <a 
                            :href="`https://wa.me/${whatsappNumber}?text=Halo%20Cahaya%20Ornamen,%20saya%20ingin%20konsultasi%20mengenai%20layanan:%20${encodeURIComponent(service.title)}`"
                            target="_blank"
                            class="inline-flex items-center gap-1 text-xs font-bold text-brand-primary hover:text-brand-dark transition-colors group-hover:translate-x-1 duration-200"
                        >
                            <span>Konsultasikan</span>
                            <ArrowUpRight class="w-4 h-4" />
                        </a>
                    </div>
                </div>
            </div>

            <!-- Bottom Assurance Banner -->
            <div class="mt-12 p-6 rounded-2xl bg-brand-dark text-brand-cream border border-brand-cta/20 flex flex-col sm:flex-row items-center justify-between gap-6 shadow-2xl">
                <div class="space-y-1 text-center sm:text-left">
                    <div class="font-serif text-lg sm:text-xl font-bold text-brand-cta">
                        Punya Gambar Kerja (CAD/PDF) dari Arsitek Anda?
                    </div>
                    <div class="text-xs sm:text-sm text-brand-cream/80">
                        Kirimkan file denah atau gambar 3D Anda untuk perhitungan volume dan estimasi RAB gratis dalam 1x24 jam.
                    </div>
                </div>
                <a 
                    :href="`https://wa.me/${whatsappNumber}?text=Halo%20Cahaya%20Ornamen,%20saya%20ingin%20mengirimkan%20gambar%20kerja%20arsitek%20untuk%20dihitungkan%20RAB`"
                    target="_blank"
                    class="px-6 py-3 rounded-xl bg-brand-cta text-brand-dark font-extrabold text-sm tracking-wide shadow-cta-glow hover:bg-yellow-300 transition-all shrink-0"
                >
                    Kirim Gambar via WA
                </a>
            </div>

        </div>
    </section>
</template>
