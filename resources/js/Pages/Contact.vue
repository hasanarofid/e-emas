<script setup>
import { Head } from '@inertiajs/vue3';
import HeaderNavbar from '@/Components/Frontend/HeaderNavbar.vue';
import ConsultationCtaSection from '@/Components/Frontend/ConsultationCtaSection.vue';
import FooterSection from '@/Components/Frontend/FooterSection.vue';
import FloatingWhatsapp from '@/Components/Frontend/FloatingWhatsapp.vue';
import { MapPin, Phone, Mail, Clock } from 'lucide-vue-next';

defineProps({
    contactInfo: {
        type: Object,
        required: true
    }
});
</script>

<template>
    <Head title="Kontak & Konsultasi Ornamen - Cahaya Ornamen" />

    <div class="min-h-screen bg-brand-cream text-brand-dark font-sans antialiased">
        <HeaderNavbar :whatsapp-number="contactInfo.whatsapp_number" :brand-name="contactInfo.brand_name" />

        <main class="pt-32 pb-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-16">
                <div class="max-w-3xl space-y-3">
                    <span class="text-xs font-bold uppercase tracking-widest text-brand-primary">
                        Hubungi Kami
                    </span>
                    <h1 class="font-serif text-4xl sm:text-5xl font-bold text-brand-dark leading-tight">
                        Konsultasi Desain & Kunjungan Workshop.
                    </h1>
                    <p class="text-base text-brand-dark/80">
                        Kami siap mendengarkan rencana proyek Anda dan memberikan solusi ornamen arsitektur terbaik dengan perhitungan struktur yang aman.
                    </p>
                </div>

                <!-- Info Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-12">
                    <div class="p-6 rounded-2xl bg-brand-sand border border-brand-dark/15 space-y-2">
                        <MapPin class="w-6 h-6 text-brand-primary" />
                        <h3 class="font-serif text-lg font-bold text-brand-dark">Alamat Workshop</h3>
                        <p class="text-xs text-brand-dark/75">{{ contactInfo.workshop_address }}</p>
                    </div>

                    <div class="p-6 rounded-2xl bg-brand-sand border border-brand-dark/15 space-y-2">
                        <Phone class="w-6 h-6 text-brand-primary" />
                        <h3 class="font-serif text-lg font-bold text-brand-dark">Hotline & WhatsApp</h3>
                        <p class="text-xs text-brand-dark/75">{{ contactInfo.phone }}</p>
                    </div>

                    <div class="p-6 rounded-2xl bg-brand-sand border border-brand-dark/15 space-y-2">
                        <Clock class="w-6 h-6 text-brand-primary" />
                        <h3 class="font-serif text-lg font-bold text-brand-dark">Jam Operasional</h3>
                        <p class="text-xs text-brand-dark/75">{{ contactInfo.working_hours }}</p>
                    </div>
                </div>
            </div>

            <!-- Form & Direct WA -->
            <ConsultationCtaSection :whatsapp-number="contactInfo.whatsapp_number" />
        </main>

        <FooterSection :contact-info="contactInfo" />
        <FloatingWhatsapp :whatsapp-number="contactInfo.whatsapp_number" />
    </div>
</template>
