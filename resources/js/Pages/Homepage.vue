<script setup>
import { Head } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import gsap from 'gsap';

// Modular Frontend Components
import HeaderNavbar from '@/Components/Frontend/HeaderNavbar.vue';
import HeroSection from '@/Components/Frontend/HeroSection.vue';
import AboutSection from '@/Components/Frontend/AboutSection.vue';
import ServicesSection from '@/Components/Frontend/ServicesSection.vue';
import FeaturedCatalogSection from '@/Components/Frontend/FeaturedCatalogSection.vue';
import PortfolioBentoSection from '@/Components/Frontend/PortfolioBentoSection.vue';
import TestimonialsSection from '@/Components/Frontend/TestimonialsSection.vue';
import ArticlePreviewSection from '@/Components/Frontend/ArticlePreviewSection.vue';
import ConsultationCtaSection from '@/Components/Frontend/ConsultationCtaSection.vue';
import FooterSection from '@/Components/Frontend/FooterSection.vue';
import FloatingWhatsapp from '@/Components/Frontend/FloatingWhatsapp.vue';

const props = defineProps({
    categories: {
        type: Array,
        default: () => []
    },
    featuredProducts: {
        type: Array,
        default: () => []
    },
    portfolios: {
        type: Array,
        default: () => []
    },
    services: {
        type: Array,
        default: () => []
    },
    testimonials: {
        type: Array,
        default: () => []
    },
    articles: {
        type: Array,
        default: () => []
    },
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

onMounted(() => {
    // Subtle initial reveal animation using GSAP
    gsap.from('main > section', {
        opacity: 0,
        y: 20,
        duration: 0.8,
        stagger: 0.15,
        ease: 'power2.out'
    });
});
</script>

<template>
    <Head>
        <title>Cahaya Ornamen - Spesialis GRC Krawangan, Pilar Klasik, Kubah, & Ornamen Arsitektur</title>
        <meta name="description" content="Manufaktur dan kontraktor ornamen arsitektur terkemuka: GRC Krawangan fasad, kubah masjid GRC, pilar Eropa, dan lis profil dengan presisi cetakan tinggi." />
        <meta property="og:title" content="Cahaya Ornamen - Seni Ornamen Arsitektur & GRC Presisi" />
        <meta property="og:description" content="Penyedia ornamen arsitektur tahan cuaca dengan formula serat AR-Glass dan presisi cetak CNC untuk masjid, villa, dan gedung prestisius." />
    </Head>

    <div class="min-h-screen bg-brand-cream text-brand-dark font-sans selection:bg-brand-cta selection:text-brand-dark antialiased">
        
        <!-- Sticky Architectural Navbar -->
        <HeaderNavbar 
            :whatsapp-number="contactInfo.whatsapp_number" 
            :brand-name="contactInfo.brand_name" 
        />

        <main>
            <!-- 1. Asymmetrical Hero Section -->
            <HeroSection 
                :whatsapp-number="contactInfo.whatsapp_number" 
            />

            <!-- 2. About Section (Filosofi & Material GRC) -->
            <AboutSection 
                :whatsapp-number="contactInfo.whatsapp_number" 
            />

            <!-- 3. Services Section (Layanan Komprehensif) -->
            <ServicesSection 
                :services="services" 
                :whatsapp-number="contactInfo.whatsapp_number" 
            />

            <!-- 4. Featured Catalog Section (Preview Kategori & Produk Unggulan) -->
            <FeaturedCatalogSection 
                :categories="categories" 
                :featured-products="featuredProducts" 
                :whatsapp-number="contactInfo.whatsapp_number" 
            />

            <!-- 5. Portfolio Bento Section (Proyek Terpasang di Lapangan) -->
            <PortfolioBentoSection 
                :portfolios="portfolios" 
                :whatsapp-number="contactInfo.whatsapp_number" 
            />

            <!-- 6. Testimonials Section (Social Proof & Kepercayaan Klien) -->
            <TestimonialsSection 
                :testimonials="testimonials" 
            />

            <!-- 7. Article Preview Section (Tips & Edukasi SEO) -->
            <ArticlePreviewSection 
                :articles="articles" 
            />

            <!-- 8. Consultation CTA & Fast Quote Form -->
            <ConsultationCtaSection 
                :whatsapp-number="contactInfo.whatsapp_number" 
            />
        </main>

        <!-- Deep Green Architectural Footer -->
        <FooterSection 
            :contact-info="contactInfo" 
        />

        <!-- Floating WhatsApp Micro-Pill -->
        <FloatingWhatsapp 
            :whatsapp-number="contactInfo.whatsapp_number" 
        />

    </div>
</template>
