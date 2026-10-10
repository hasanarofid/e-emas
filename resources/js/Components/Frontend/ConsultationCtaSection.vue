<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { 
    MessageSquare, 
    Send, 
    CheckCircle2, 
    FileText, 
    ShieldAlert, 
    Sparkles, 
    PhoneCall 
} from 'lucide-vue-next';

const props = defineProps({
    whatsappNumber: {
        type: String,
        default: '6281234567890'
    }
});

const isSubmitted = ref(false);

const form = useForm({
    name: '',
    phone: '',
    email: '',
    project_type: 'Masjid',
    location: '',
    estimated_volume: '',
    message: '',
});

const submitInquiry = () => {
    form.post(route('inquiries.store'), {
        preserveScroll: true,
        onSuccess: () => {
            isSubmitted.value = true;
            form.reset();
            setTimeout(() => {
                isSubmitted.value = false;
            }, 6000);
        }
    });
};
</script>

<template>
    <section id="konsultasi" class="py-24 bg-brand-deep text-brand-cream relative overflow-hidden">
        
        <!-- Subtle Pattern Backdrop -->
        <div class="absolute inset-0 opacity-10 pointer-events-none">
            <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="cta-dots" width="30" height="30" patternUnits="userSpaceOnUse">
                        <circle cx="2" cy="2" r="1.5" fill="#e1e100" />
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#cta-dots)" />
            </svg>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-center">
                
                <!-- Left Column: Value Prop & Direct WhatsApp (7 cols) -->
                <div class="lg:col-span-7 space-y-8">
                    
                    <div class="space-y-3">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-brand-dark text-brand-cta text-xs font-bold uppercase tracking-widest border border-brand-cta/20">
                            Konsultasi Bebas Biaya
                        </div>
                        <h2 class="font-serif text-3xl sm:text-4xl md:text-5xl font-bold tracking-tight text-white leading-tight">
                            Mulai Wujudkan Ornamen Arsitektur Impian Anda Bersama Kami.
                        </h2>
                        <p class="text-base sm:text-lg text-brand-cream/80 leading-relaxed font-normal">
                            Konsultasikan kebutuhan desain fasad, perhitungan volume GRC, struktur rangka pendukung, hingga estimasi anggaran biaya (RAB) tanpa dipungut biaya apapun.
                        </p>
                    </div>

                    <!-- Direct Fast Connect to WhatsApp -->
                    <div class="p-6 rounded-2xl bg-brand-dark/90 border border-brand-cta/30 backdrop-blur-md shadow-2xl flex flex-col sm:flex-row items-center justify-between gap-6">
                        <div class="space-y-1 text-center sm:text-left">
                            <div class="text-xs font-bold text-brand-light uppercase tracking-wider">
                                Respon Cepat (Hotline WhatsApp)
                            </div>
                            <div class="font-serif text-xl sm:text-2xl font-bold text-brand-cta">
                                +{{ whatsappNumber }}
                            </div>
                            <div class="text-xs text-brand-cream/70">
                                Aktif setiap hari kerja (08.00 - 17.00 WIB)
                            </div>
                        </div>

                        <a 
                            :href="`https://wa.me/${whatsappNumber}?text=Halo%20Cahaya%20Ornamen,%20saya%20ingin%20konsultasi%20langsung%20via%20WhatsApp`"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="px-6 py-3.5 rounded-xl bg-brand-cta text-brand-dark font-extrabold text-sm tracking-wide shadow-cta-glow hover:bg-yellow-300 transition-all flex items-center gap-2 shrink-0 border border-brand-dark/20"
                        >
                            <MessageSquare class="w-4 h-4 text-brand-dark stroke-[2.4]" />
                            <span>Chat WA Sekarang</span>
                        </a>
                    </div>

                    <!-- 3 Benefit Bullet Points -->
                    <div class="space-y-3 pt-2">
                        <div class="flex items-center gap-3 text-sm text-brand-cream/90">
                            <CheckCircle2 class="w-5 h-5 text-brand-cta shrink-0" />
                            <span>Gratis konsultasi teknis & rekomendasi ketebalan modul GRC.</span>
                        </div>
                        <div class="flex items-center gap-3 text-sm text-brand-cream/90">
                            <CheckCircle2 class="w-5 h-5 text-brand-cta shrink-0" />
                            <span>Transparansi formula bahan dan pengujian beban sebelum produksi massal.</span>
                        </div>
                        <div class="flex items-center gap-3 text-sm text-brand-cream/90">
                            <CheckCircle2 class="w-5 h-5 text-brand-cta shrink-0" />
                            <span>Dukungan pengiriman aman dan teknisi instalasi di seluruh Nusantara.</span>
                        </div>
                    </div>

                </div>

                <!-- Right Column: Consultation Form (5 cols) -->
                <div class="lg:col-span-5">
                    <div class="p-8 sm:p-10 rounded-3xl bg-brand-cream text-brand-dark border-2 border-brand-cta/40 shadow-2xl space-y-6">
                        
                        <div class="space-y-1">
                            <h3 class="font-serif text-2xl font-bold text-brand-dark">
                                Formulir Estimasi Proyek
                            </h3>
                            <p class="text-xs text-brand-dark/70">
                                Isi informasi di bawah, tim kami akan mengirimkan estimasi RAB dalam 1x24 jam.
                            </p>
                        </div>

                        <!-- Success Alert -->
                        <div v-if="isSubmitted" class="p-4 rounded-xl bg-green-100 border border-green-300 text-green-900 text-xs font-semibold flex items-center gap-2">
                            <CheckCircle2 class="w-4 h-4 text-green-700 shrink-0" />
                            <span>Terima kasih! Pesan Anda telah diterima dan akan segera dihubungi.</span>
                        </div>

                        <form @submit.prevent="submitInquiry" class="space-y-4">
                            
                            <!-- Nama Lengkap -->
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-brand-dark mb-1">
                                    Nama Lengkap *
                                </label>
                                <input 
                                    v-model="form.name"
                                    type="text" 
                                    required
                                    placeholder="Contoh: Ir. Hendra Saputra"
                                    class="w-full px-4 py-2.5 rounded-xl border border-brand-dark/20 bg-white text-sm text-brand-dark focus:ring-2 focus:ring-brand-primary focus:border-brand-primary outline-none transition-all"
                                />
                            </div>

                            <!-- No. WhatsApp / Telepon -->
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-brand-dark mb-1">
                                    No. WhatsApp / Telepon *
                                </label>
                                <input 
                                    v-model="form.phone"
                                    type="tel" 
                                    required
                                    placeholder="081234567890"
                                    class="w-full px-4 py-2.5 rounded-xl border border-brand-dark/20 bg-white text-sm text-brand-dark focus:ring-2 focus:ring-brand-primary focus:border-brand-primary outline-none transition-all"
                                />
                            </div>

                            <!-- Tipe Proyek & Lokasi -->
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-brand-dark mb-1">
                                        Tipe Bangunan
                                    </label>
                                    <select 
                                        v-model="form.project_type"
                                        class="w-full px-3 py-2.5 rounded-xl border border-brand-dark/20 bg-white text-sm text-brand-dark focus:ring-2 focus:ring-brand-primary outline-none"
                                    >
                                        <option value="Masjid">Masjid / Musholla</option>
                                        <option value="Hunian Klasik">Hunian Klasik</option>
                                        <option value="Gedung Komersial">Gedung / Kantor</option>
                                        <option value="Villa / Resort">Villa / Resort</option>
                                        <option value="Lainnya">Lainnya</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-brand-dark mb-1">
                                        Kota / Lokasi
                                    </label>
                                    <input 
                                        v-model="form.location"
                                        type="text" 
                                        placeholder="Surabaya / Jakarta"
                                        class="w-full px-3 py-2.5 rounded-xl border border-brand-dark/20 bg-white text-sm text-brand-dark focus:ring-2 focus:ring-brand-primary outline-none"
                                    />
                                </div>
                            </div>

                            <!-- Pesan / Rincian Ornamen -->
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-brand-dark mb-1">
                                    Rincian Kebutuhan Ornamen *
                                </label>
                                <textarea 
                                    v-model="form.message"
                                    rows="3" 
                                    required
                                    placeholder="Contoh: Butuh GRC Krawangan fasad luas ~80m² dan 4 buah pilar korintus tinggi 4 meter..."
                                    class="w-full px-4 py-2.5 rounded-xl border border-brand-dark/20 bg-white text-sm text-brand-dark focus:ring-2 focus:ring-brand-primary outline-none transition-all resize-none"
                                ></textarea>
                            </div>

                            <!-- Submit Button (#e1e100 Highlight CTA) -->
                            <button 
                                type="submit" 
                                :disabled="form.processing"
                                class="w-full py-4 rounded-xl bg-brand-cta text-brand-dark font-extrabold text-sm tracking-wide shadow-cta-glow hover:bg-yellow-300 active:translate-y-0.5 transition-all flex items-center justify-center gap-2 border border-brand-dark/25"
                            >
                                <Send class="w-4 h-4 text-brand-dark" />
                                <span>{{ form.processing ? 'Mengirim Data...' : 'Kirim Permintaan Konsultasi' }}</span>
                            </button>

                        </form>

                    </div>
                </div>

            </div>
        </div>
    </section>
</template>
