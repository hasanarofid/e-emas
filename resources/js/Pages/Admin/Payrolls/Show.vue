<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { Printer, ArrowLeft, Banknote, Building2, CheckCircle2 } from 'lucide-vue-next';

const props = defineProps({
  payroll: Object,
});

const months = [
  'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
  'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];

const formatRupiah = (val) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val || 0);
};

const formatDate = (dateString) => {
  if (!dateString) return '-';
  const d = new Date(dateString);
  return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
};

const triggerPrint = () => {
  window.print();
};
</script>

<template>
  <div>
    <Head :title="`Slip Gaji - ${payroll.employee?.name || payroll.payroll_code}`" />

    <!-- Action Toolbar (hidden on print) -->
    <div class="print:hidden bg-slate-900 border-b border-slate-800 p-4 sticky top-0 z-50">
      <div class="max-w-3xl mx-auto flex items-center justify-between">
        <Link
          :href="route('admin.payrolls.index')"
          class="inline-flex items-center gap-2 text-sm text-slate-300 hover:text-white transition-colors"
        >
          <ArrowLeft class="w-4 h-4" />
          <span>Kembali ke Riwayat Slip</span>
        </Link>

        <button
          @click="triggerPrint"
          class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-sm shadow-md transition-all cursor-pointer"
        >
          <Printer class="w-4 h-4" />
          <span>Cetak Slip Gaji (Print / PDF)</span>
        </button>
      </div>
    </div>

    <!-- Printable Slip Document -->
    <div class="min-h-screen bg-slate-100 dark:bg-slate-950 py-8 px-4 print:p-0 print:bg-white">
      <div class="max-w-2xl mx-auto bg-white text-slate-900 rounded-2xl shadow-xl border border-slate-200 print:border-none print:shadow-none p-8 font-sans">
        
        <!-- Header -->
        <div class="border-b-2 border-slate-900 pb-5 mb-6 flex items-start justify-between">
          <div>
            <div class="flex items-center gap-2">
              <span class="w-3 h-3 rounded-full bg-amber-500 inline-block"></span>
              <h1 class="text-xl font-black uppercase tracking-wider text-slate-900">TOKO EMAS PERMATA ABADI</h1>
            </div>
            <p class="text-xs text-slate-600 mt-1">Pusat Perhiasan Emas & Logam Mulia Berkualitas</p>
            <p class="text-xs text-slate-500">Jl. Pasar Senen No. 88, Telp: (021) 555-8989 / 0812-3456-7890</p>
          </div>
          <div class="text-right">
            <span class="inline-block px-3 py-1 rounded bg-slate-900 text-amber-400 font-bold text-xs uppercase tracking-wider font-mono">
              SLIP GAJI KARYAWAN
            </span>
            <p class="font-mono text-xs font-bold mt-2 text-slate-800">{{ payroll.payroll_code }}</p>
            <p class="text-xs text-slate-500">{{ formatDate(payroll.payment_date) }}</p>
          </div>
        </div>

        <!-- Employee Info Grid -->
        <div class="grid grid-cols-2 gap-4 p-4 rounded-xl bg-slate-50 border border-slate-200 mb-6 text-xs">
          <div>
            <p class="text-slate-500">Nama Karyawan:</p>
            <p class="font-bold text-sm text-slate-900 mt-0.5">{{ payroll.employee?.name }}</p>
            <p class="text-slate-600 font-mono mt-0.5">NIK: {{ payroll.employee?.employee_code }}</p>
          </div>
          <div>
            <p class="text-slate-500">Jabatan & Periode:</p>
            <p class="font-bold text-sm text-slate-900 mt-0.5">{{ payroll.employee?.position }}</p>
            <p class="text-amber-700 font-semibold mt-0.5">
              Periode: {{ months[payroll.period_month - 1] }} {{ payroll.period_year }}
            </p>
          </div>
        </div>

        <!-- Breakdown Table -->
        <div class="space-y-4 mb-6">
          <table class="w-full text-xs">
            <thead>
              <tr class="border-b border-slate-300 text-slate-600 font-bold uppercase">
                <th class="py-2 text-left">Komponen Penghasilan</th>
                <th class="py-2 text-right">Jumlah (IDR)</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr>
                <td class="py-2.5 font-medium text-slate-800">Gaji Pokok</td>
                <td class="py-2.5 text-right font-mono font-semibold">{{ formatRupiah(payroll.base_salary) }}</td>
              </tr>
              <tr v-if="payroll.allowance > 0">
                <td class="py-2.5 font-medium text-slate-800">Tunjangan Tetap / Uang Makan</td>
                <td class="py-2.5 text-right font-mono font-semibold">{{ formatRupiah(payroll.allowance) }}</td>
              </tr>
              <tr v-if="payroll.commission_amount > 0">
                <td class="py-2.5 font-medium text-slate-800">
                  Komisi Omset Penjualan Emas
                  <span v-if="payroll.employee?.commission_rate" class="text-slate-500">({{ payroll.employee.commission_rate }}%)</span>
                </td>
                <td class="py-2.5 text-right font-mono font-semibold text-emerald-700">+{{ formatRupiah(payroll.commission_amount) }}</td>
              </tr>
              <tr v-if="payroll.bonus > 0">
                <td class="py-2.5 font-medium text-slate-800">Bonus Prestasi / Lembur</td>
                <td class="py-2.5 text-right font-mono font-semibold text-emerald-700">+{{ formatRupiah(payroll.bonus) }}</td>
              </tr>
              <tr v-if="payroll.deductions > 0">
                <td class="py-2.5 font-medium text-slate-800">Potongan Kasbon / Absensi</td>
                <td class="py-2.5 text-right font-mono font-semibold text-rose-700">-{{ formatRupiah(payroll.deductions) }}</td>
              </tr>
            </tbody>
            <tfoot>
              <tr class="border-t-2 border-slate-900 bg-amber-500/10">
                <td class="py-3 px-2 font-bold text-slate-900 text-sm uppercase">Total Gaji Diterima (THP)</td>
                <td class="py-3 px-2 text-right font-bold text-base font-mono text-slate-950">
                  {{ formatRupiah(payroll.net_salary) }}
                </td>
              </tr>
            </tfoot>
          </table>
        </div>

        <!-- Payment Info -->
        <div class="p-3 rounded-lg border border-slate-200 text-xs flex items-center justify-between mb-8 bg-slate-50">
          <div>
            <span class="text-slate-500">Metode Penyaluran:</span>
            <span class="font-bold ml-1 uppercase text-slate-800">{{ payroll.payment_method }}</span>
            <span v-if="payroll.employee?.bank_name" class="ml-2 text-slate-600">
              ({{ payroll.employee.bank_name }} - {{ payroll.employee.bank_account_number }} a.n {{ payroll.employee.bank_account_holder }})
            </span>
          </div>
          <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 font-bold uppercase text-[10px]">
            Lunas Ditransfer
          </span>
        </div>

        <!-- Signature Section -->
        <div class="grid grid-cols-2 gap-8 text-center text-xs pt-4 border-t border-slate-200">
          <div>
            <p class="text-slate-500">Penerima (Karyawan),</p>
            <div class="h-16"></div>
            <p class="font-bold text-slate-900 border-t border-slate-300 pt-1 inline-block min-w-44">
              {{ payroll.employee?.name }}
            </p>
          </div>
          <div>
            <p class="text-slate-500">Pihak Toko (Finance/Owner),</p>
            <div class="h-16"></div>
            <p class="font-bold text-slate-900 border-t border-slate-300 pt-1 inline-block min-w-44">
              {{ payroll.creator?.name || 'Owner Toko Mas' }}
            </p>
          </div>
        </div>

        <!-- Note footer -->
        <p class="text-[10px] text-center text-slate-400 mt-6 print:mt-10">
          Slip gaji ini diterbitkan resmi secara elektronik oleh POS Emas Atelier. Harap disimpan dengan baik sebagai arsip pribadi.
        </p>

      </div>
    </div>
  </div>
</template>
