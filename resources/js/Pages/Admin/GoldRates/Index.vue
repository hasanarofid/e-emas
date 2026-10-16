<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { 
  TrendingUp, 
  Sparkles, 
  Check, 
  History, 
  Scale, 
  AlertCircle, 
  RefreshCw 
} from 'lucide-vue-next';

const props = defineProps({
  goldRates: {
    type: Array,
    required: true
  },
  recentHistories: {
    type: Array,
    default: () => []
  }
});

const form = useForm({
  rates: props.goldRates.map(r => ({
    id: r.id,
    code: r.code,
    name: r.name,
    percentage: r.percentage,
    buy_price_per_gram: r.buy_price_per_gram,
    sell_price_per_gram: r.sell_price_per_gram,
  })),
  notes: 'Update harga emas harian'
});

const formatRupiah = (val) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0
  }).format(val || 0);
};

const formatDate = (dateStr) => {
  return new Date(dateStr).toLocaleString('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

const submitPrices = () => {
  form.post(route('admin.gold-rates.update-prices'), {
    preserveScroll: true,
  });
};
</script>

<template>
  <Head title="Kurs Harga Emas Harian - POS Emas" />

  <AdminLayout>
    <div class="space-y-8">
      <!-- Title Page -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200 dark:border-slate-800">
        <div>
          <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-700 dark:text-amber-300 text-xs font-semibold mb-1">
            <Sparkles class="w-3.5 h-3.5 text-amber-500 dark:text-amber-400" />
            <span>Patokan Kurs Kasir Real-Time</span>
          </div>
          <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">Pembaruan Harga Emas Harian</h2>
          <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
            Ubah harga jual dan buyback (beli kembali) per gram. Nilai harga perhiasan di etalase akan otomatis ter-update.
          </p>
        </div>
      </div>

      <!-- Main Form: Update Kurs Massal -->
      <form @submit.prevent="submitPrices" class="space-y-6">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100 dark:border-slate-800">
            <div>
              <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <TrendingUp class="w-5 h-5 text-amber-500" />
                Daftar Kadar & Nilai Rupiah per Gram
              </h3>
              <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Input angka nominal tanpa titik atau koma.</p>
            </div>

            <!-- Notes Field -->
            <div class="w-full sm:w-72">
              <input 
                type="text" 
                v-model="form.notes"
                placeholder="Catatan update (cth: Tren pasar naik)"
                class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-950 border-slate-300 dark:border-slate-800 text-slate-800 dark:text-slate-200 focus:border-amber-500 focus:ring-amber-500 py-2 px-3"
              />
            </div>
          </div>

          <!-- Rates Grid Table -->
          <div class="overflow-x-auto mt-4">
            <table class="w-full text-left text-sm">
              <thead class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/60">
                <tr>
                  <th class="py-3 px-4">Kadar / Karat</th>
                  <th class="py-3 px-4">Kemurnian</th>
                  <th class="py-3 px-4">Harga Beli (Buyback) / gr</th>
                  <th class="py-3 px-4">Harga Jual Dasar / gr</th>
                  <th class="py-3 px-4 text-right">Margin / gr</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 text-slate-700 dark:text-slate-200">
                <tr v-for="(rate, idx) in form.rates" :key="rate.id" class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                  <td class="py-3.5 px-4 font-bold text-slate-900 dark:text-white">
                    <div class="flex items-center gap-2">
                      <span class="w-7 h-7 rounded-lg bg-amber-500/10 border border-amber-500/20 text-amber-700 dark:text-amber-400 flex items-center justify-center font-bold text-xs">
                        {{ rate.code }}
                      </span>
                      <span>{{ rate.name }}</span>
                    </div>
                  </td>
                  <td class="py-3.5 px-4 font-mono text-slate-600 dark:text-slate-300">
                    {{ rate.percentage }}%
                  </td>
                  <td class="py-3.5 px-4">
                    <div class="relative max-w-[200px]">
                      <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs text-slate-400 font-mono">Rp</span>
                      <input 
                        type="number" 
                        v-model="rate.buy_price_per_gram"
                        class="w-full pl-9 pr-3 py-1.5 text-xs rounded-xl bg-slate-50 dark:bg-slate-950 border-slate-300 dark:border-slate-800 font-mono font-bold text-amber-700 dark:text-amber-300 focus:border-amber-500 focus:ring-amber-500"
                        required
                        min="0"
                      />
                    </div>
                  </td>
                  <td class="py-3.5 px-4">
                    <div class="relative max-w-[200px]">
                      <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs text-slate-400 font-mono">Rp</span>
                      <input 
                        type="number" 
                        v-model="rate.sell_price_per_gram"
                        class="w-full pl-9 pr-3 py-1.5 text-xs rounded-xl bg-slate-50 dark:bg-slate-950 border-slate-300 dark:border-slate-800 font-mono font-bold text-slate-900 dark:text-white focus:border-amber-500 focus:ring-amber-500"
                        required
                        min="0"
                      />
                    </div>
                  </td>
                  <td class="py-3.5 px-4 text-right font-mono font-semibold text-emerald-600 dark:text-emerald-400 text-xs">
                    +{{ formatRupiah(rate.sell_price_per_gram - rate.buy_price_per_gram) }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Submit Button Toolbar -->
          <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <span class="text-xs text-slate-500 dark:text-slate-400">
              * Perubahan harga akan otomatis mengkalkulasi ulang estimasi harga perhiasan di etalase.
            </span>

            <button 
              type="submit"
              :disabled="form.processing"
              class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-bold text-xs uppercase tracking-wider shadow-md shadow-amber-500/20 active:scale-95 transition-all disabled:opacity-50 cursor-pointer flex items-center gap-2"
            >
              <RefreshCw v-if="form.processing" class="w-4 h-4 animate-spin" />
              <Check v-else class="w-4 h-4" />
              <span>Simpan & Sinkronkan Kurs</span>
            </button>
          </div>
        </div>
      </form>

      <!-- History Log Section -->
      <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
          <div>
            <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
              <History class="w-5 h-5 text-amber-500" />
              Riwayat Perubahan Kurs Terakhir
            </h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Audit log pergerakan harga emas untuk analisis keuntungan.</p>
          </div>
          <span class="text-xs text-slate-500 dark:text-slate-400 font-mono">{{ recentHistories.length }} Catatan</span>
        </div>

        <div class="overflow-x-auto mt-4">
          <table class="w-full text-left text-xs">
            <thead class="text-slate-500 dark:text-slate-400 border-b border-slate-100 dark:border-slate-800 uppercase tracking-wider text-[10px]">
              <tr>
                <th class="py-2.5 px-3">Waktu Perubahan</th>
                <th class="py-2.5 px-3">Kadar Emas</th>
                <th class="py-2.5 px-3">Harga Beli</th>
                <th class="py-2.5 px-3">Harga Jual</th>
                <th class="py-2.5 px-3">Diubah Oleh</th>
                <th class="py-2.5 px-3">Catatan</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 text-slate-700 dark:text-slate-300">
              <tr v-for="h in recentHistories" :key="h.id" class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                <td class="py-3 px-3 font-mono text-slate-500 dark:text-slate-400">{{ formatDate(h.created_at) }}</td>
                <td class="py-3 px-3 font-bold text-slate-900 dark:text-white">
                  {{ h.gold_rate ? h.gold_rate.name : '-' }}
                </td>
                <td class="py-3 px-3 font-mono text-amber-700 dark:text-amber-300">{{ formatRupiah(h.buy_price_per_gram) }}</td>
                <td class="py-3 px-3 font-mono font-semibold text-slate-900 dark:text-white">{{ formatRupiah(h.sell_price_per_gram) }}</td>
                <td class="py-3 px-3">{{ h.changed_by ? h.changed_by.name : 'Sistem' }}</td>
                <td class="py-3 px-3 text-slate-500 dark:text-slate-400 italic">{{ h.notes || '-' }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
