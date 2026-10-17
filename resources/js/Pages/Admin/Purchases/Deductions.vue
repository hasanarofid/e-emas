<script setup>
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
  Percent, Coins, Edit2, CheckCircle2, ShieldCheck, 
  HelpCircle, Sparkles, X, Save
} from 'lucide-vue-next';

const props = defineProps({
  deductions: Array,
});

const isModalOpen = ref(false);
const editingDeduction = ref(null);

const form = useForm({
  deduction_percentage: 0,
  deduction_fee_per_gram: 0,
  crafting_fee_deducted: true,
  condition_notes: '',
});

const openEditModal = (deduction) => {
  editingDeduction.value = deduction;
  form.deduction_percentage = deduction.deduction_percentage;
  form.deduction_fee_per_gram = deduction.deduction_fee_per_gram;
  form.crafting_fee_deducted = Boolean(deduction.crafting_fee_deducted);
  form.condition_notes = deduction.condition_notes || '';
  isModalOpen.value = true;
};

const submitUpdate = () => {
  if (!editingDeduction.value) return;
  form.put(route('admin.buyback-deductions.update', editingDeduction.value.id), {
    onSuccess: () => {
      isModalOpen.value = false;
    },
  });
};

const formatRupiah = (val) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val || 0);
};
</script>

<template>
  <AdminLayout title="Potongan Master Buyback">
    <Head title="Potongan Master Buyback - POS Emas" />

    <div class="space-y-6">
      <!-- Header -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <h1 class="text-2xl font-bold text-white tracking-wide flex items-center gap-2">
            <Percent class="w-7 h-7 text-amber-400" />
            Potongan Master Buyback Per Kadar Emas
          </h1>
          <p class="text-slate-400 text-sm mt-1">
            Standar potongan susut gramasi, persentase nilai, dan ongkos pembuatan saat pelanggan menjual kembali perhiasan
          </p>
        </div>
      </div>

      <!-- Info Banner Alur Bisnis Emas -->
      <div class="bg-gradient-to-r from-amber-500/10 via-amber-600/5 to-transparent border border-amber-500/20 rounded-2xl p-4 flex items-start gap-3">
        <Sparkles class="w-5 h-5 text-amber-400 shrink-0 mt-0.5" />
        <div class="text-xs text-slate-300 leading-relaxed">
          <span class="font-semibold text-amber-400">Aturan Kalkulasi Kasir Beli Kembali (Buyback):</span>
          Setiap transaksi buyback di kasir akan secara otomatis mengacu pada tabel ini. Potongan persentase dan biaya susut per gram langsung dikurangi dari estimasi nilai buyback perhiasan pelanggan berdasarkan kadar emasnya.
        </div>
      </div>

      <!-- Deductions Cards / Table -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div 
          v-for="item in deductions" 
          :key="item.id"
          class="bg-[#181834] border border-[#25254B] rounded-2xl p-5 shadow-xl hover:border-amber-400/40 transition-all flex flex-col justify-between"
        >
          <div>
            <div class="flex items-center justify-between pb-3 border-b border-[#25254B]">
              <div>
                <h3 class="text-base font-bold text-white">{{ item.gold_rate?.name }}</h3>
                <span class="text-xs text-amber-400/80 font-mono font-semibold">{{ item.gold_rate?.percentage }}% Kemurnian</span>
              </div>
              <button
                @click="openEditModal(item)"
                class="p-2 bg-[#25254B] hover:bg-amber-500 hover:text-slate-950 text-slate-300 rounded-xl transition-colors"
                title="Edit Aturan Potongan"
              >
                <Edit2 class="w-4 h-4" />
              </button>
            </div>

            <div class="mt-4 space-y-3 text-xs">
              <div class="flex justify-between items-center py-1">
                <span class="text-slate-400">Potongan Nilai (%):</span>
                <span class="text-base font-bold text-amber-400 font-mono">{{ item.deduction_percentage }}%</span>
              </div>

              <div class="flex justify-between items-center py-1">
                <span class="text-slate-400">Biaya Susut / Gram:</span>
                <span class="text-sm font-semibold text-white font-mono">{{ formatRupiah(item.deduction_fee_per_gram) }}</span>
              </div>

              <div class="flex justify-between items-center py-1">
                <span class="text-slate-400">Ongkos Bikin Hangus:</span>
                <span 
                  :class="item.crafting_fee_deducted ? 'text-emerald-400' : 'text-slate-400'"
                  class="font-semibold inline-flex items-center gap-1"
                >
                  <CheckCircle2 v-if="item.crafting_fee_deducted" class="w-3.5 h-3.5" />
                  {{ item.crafting_fee_deducted ? 'Ya (Tidak Diganti)' : 'Tidak (Diganti)' }}
                </span>
              </div>

              <div v-if="item.condition_notes" class="pt-2 border-t border-[#25254B]/60 text-[11px] text-slate-400">
                <span class="text-slate-500 block">Syarat & Ketentuan:</span>
                {{ item.condition_notes }}
              </div>
            </div>
          </div>

          <div class="mt-4 pt-3 border-t border-[#25254B] flex items-center justify-between text-[11px] text-slate-500">
            <span>Sistem POS Emas</span>
            <span class="text-emerald-400/80 font-medium">Aktif Digunakan Kasir</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Edit Potongan -->
    <div v-if="isModalOpen && editingDeduction" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/75 backdrop-blur-sm">
      <div class="bg-[#181834] border border-[#25254B] rounded-2xl w-full max-w-lg shadow-2xl p-6">
        <div class="flex items-center justify-between pb-4 border-b border-[#25254B]">
          <h3 class="text-lg font-bold text-white flex items-center gap-2">
            <Percent class="w-5 h-5 text-amber-400" />
            Edit Potongan Buyback {{ editingDeduction.gold_rate?.name }}
          </h3>
          <button @click="isModalOpen = false" class="text-slate-400 hover:text-white p-1 rounded-lg">
            <X class="w-5 h-5" />
          </button>
        </div>

        <form @submit.prevent="submitUpdate" class="mt-4 space-y-4">
          <div>
            <label class="block text-xs font-semibold text-slate-300 mb-1">Potongan Nilai Buyback (%) *</label>
            <div class="relative">
              <input
                v-model.number="form.deduction_percentage"
                type="number"
                step="0.1"
                min="0"
                max="100"
                required
                class="w-full px-3 py-2 bg-[#0F0F23] border border-[#25254B] rounded-xl text-white text-sm focus:border-amber-400 font-mono font-bold"
              />
              <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 font-bold">%</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-1">Persentase yang dipotong dari total harga beli kembali standar.</p>
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-300 mb-1">Biaya Susut Gramasi / Gram (Rp) *</label>
            <input
              v-model.number="form.deduction_fee_per_gram"
              type="number"
              step="500"
              min="0"
              required
              class="w-full px-3 py-2 bg-[#0F0F23] border border-[#25254B] rounded-xl text-white text-sm focus:border-amber-400 font-mono font-semibold"
            />
            <p class="text-[11px] text-slate-400 mt-1">Biaya peleburan/susut per gram berat kotor perhiasan.</p>
          </div>

          <div class="flex items-center gap-2 pt-1">
            <input
              v-model="form.crafting_fee_deducted"
              type="checkbox"
              id="crafting_fee_deducted"
              class="w-4 h-4 rounded bg-[#0F0F23] border-[#25254B] text-amber-500 focus:ring-amber-400"
            />
            <label for="crafting_fee_deducted" class="text-xs text-slate-300 font-medium">
              Ongkos Bikin Hangus (Toko tidak mengembalikan ongkos pembuatan)
            </label>
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-300 mb-1">Keterangan / Kondisi Khusus</label>
            <textarea
              v-model="form.condition_notes"
              rows="3"
              placeholder="Contoh: Perhiasan patah dikenakan tambahan biaya susut Rp 15.000/gr..."
              class="w-full px-3 py-2 bg-[#0F0F23] border border-[#25254B] rounded-xl text-white text-xs focus:border-amber-400"
            ></textarea>
          </div>

          <div class="flex items-center justify-end gap-3 pt-4 border-t border-[#25254B]">
            <button
              type="button"
              @click="isModalOpen = false"
              class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs font-semibold"
            >
              Batal
            </button>
            <button
              type="submit"
              :disabled="form.processing"
              class="inline-flex items-center gap-1.5 px-5 py-2 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 font-bold rounded-xl text-xs shadow-lg shadow-amber-500/20 disabled:opacity-50"
            >
              <Save class="w-4 h-4" />
              <span>{{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>
