<script setup>
import { MessageSquare } from 'lucide-vue-next';

defineProps({
    whatsappNumber: {
        type: String,
        default: '6281234567890'
    },
    message: {
        type: String,
        default: 'Halo Cahaya Ornamen, saya ingin berkonsultasi mengenai kebutuhan ornamen arsitektur (GRC/Krawangan/Pilar/Kubah).'
    }
});
</script>

<template>
    <div class="fixed bottom-6 right-6 z-50 flex items-center gap-3 group">
        
        <!-- Hover Badge / Micro-copy -->
        <div class="hidden sm:flex items-center px-3.5 py-2 rounded-xl bg-brand-dark/95 backdrop-blur-md text-brand-cta text-xs font-bold shadow-2xl border border-brand-cta/30 translate-x-2 opacity-0 group-hover:opacity-100 group-hover:translate-x-0 transition-all duration-300 pointer-events-none">
            <span>Konsultasi Cepat WA</span>
        </div>

        <!-- Floating Icon Button -->
        <a 
            :href="`https://wa.me/${whatsappNumber}?text=${encodeURIComponent(message)}`"
            target="_blank"
            rel="noopener noreferrer"
            class="relative flex items-center justify-center w-14 h-14 rounded-full bg-brand-cta text-brand-dark shadow-2xl hover:scale-110 active:scale-95 transition-all duration-300 border-2 border-brand-dark/30 shadow-cta-glow"
            aria-label="Konsultasi WhatsApp Cahaya Ornamen"
        >
            <!-- Glowing Ping Indicator -->
            <span class="absolute -top-1 -right-1 flex h-4 w-4">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-500 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-4 w-4 bg-green-600 border-2 border-white"></span>
            </span>

            <MessageSquare class="w-7 h-7 text-brand-dark stroke-[2.3]" />
        </a>

    </div>
</template>
