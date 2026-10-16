<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { KeyRound, Mail, ShieldAlert, Sparkles, UserCheck } from 'lucide-vue-next';

defineProps({
  canResetPassword: {
    type: Boolean,
  },
  status: {
    type: String,
  },
});

const form = useForm({
  email: '',
  password: '',
  remember: false,
});

const fillCredential = (email, password) => {
  form.email = email;
  form.password = password;
};

const submit = () => {
  form.post(route('login'), {
    onFinish: () => form.reset('password'),
  });
};
</script>

<template>
  <GuestLayout>
    <Head title="Masuk Sistem Kasir - e-Emas" />

    <div v-if="status" class="mb-4 text-xs font-medium text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 p-3 rounded-xl">
      {{ status }}
    </div>

    <!-- Quick Demo Autofill Badges -->
    <div class="bg-slate-50 dark:bg-slate-950/70 border border-slate-200 dark:border-slate-800 p-3.5 rounded-2xl space-y-2 text-xs transition-colors duration-300">
      <div class="flex items-center justify-between text-slate-600 dark:text-slate-400">
        <span class="font-semibold text-[11px] text-amber-700 dark:text-amber-400 uppercase tracking-wider flex items-center gap-1.5">
          <Sparkles class="w-3 h-3 text-amber-500 dark:text-amber-400" />
          Pilih Akun Demo:
        </span>
      </div>
      <div class="grid grid-cols-2 gap-2 pt-1">
        <button
          type="button"
          @click="fillCredential('admin@emas.com', 'password123')"
          class="flex flex-col text-left p-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700/80 hover:border-amber-500 dark:hover:border-amber-500/50 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all group shadow-sm"
        >
          <span class="font-bold text-slate-800 dark:text-white group-hover:text-amber-600 dark:group-hover:text-amber-400 flex items-center gap-1">
            <UserCheck class="w-3 h-3 text-amber-500 dark:text-amber-400" /> Owner / Admin
          </span>
          <span class="text-[10px] text-slate-500 dark:text-slate-400 truncate">admin@emas.com</span>
        </button>

        <button
          type="button"
          @click="fillCredential('kasir@emas.com', 'password123')"
          class="flex flex-col text-left p-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700/80 hover:border-amber-500 dark:hover:border-amber-500/50 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all group shadow-sm"
        >
          <span class="font-bold text-slate-800 dark:text-white group-hover:text-amber-600 dark:group-hover:text-amber-400 flex items-center gap-1">
            <KeyRound class="w-3 h-3 text-amber-500 dark:text-amber-400" /> Kasir Toko
          </span>
          <span class="text-[10px] text-slate-500 dark:text-slate-400 truncate">kasir@emas.com</span>
        </button>
      </div>
    </div>

    <form @submit.prevent="submit" class="space-y-4 pt-2">
      <div>
        <InputLabel for="email" value="Alamat Email Kasir / Admin" class="text-slate-700 dark:text-slate-300 text-xs font-semibold" />

        <div class="relative mt-1">
          <TextInput
            id="email"
            type="email"
            class="block w-full bg-slate-50 dark:bg-slate-950 border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:border-amber-500 focus:ring-amber-500 rounded-xl text-sm py-2.5 px-3 transition-colors duration-300"
            v-model="form.email"
            placeholder="admin@emas.com"
            required
            autofocus
            autocomplete="username"
          />
        </div>

        <InputError class="mt-1.5 text-xs text-rose-500 dark:text-rose-400" :message="form.errors.email" />
      </div>

      <div>
        <InputLabel for="password" value="Kata Sandi (Password)" class="text-slate-700 dark:text-slate-300 text-xs font-semibold" />

        <div class="relative mt-1">
          <TextInput
            id="password"
            type="password"
            class="block w-full bg-slate-50 dark:bg-slate-950 border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:border-amber-500 focus:ring-amber-500 rounded-xl text-sm py-2.5 px-3 transition-colors duration-300"
            v-model="form.password"
            placeholder="••••••••"
            required
            autocomplete="current-password"
          />
        </div>

        <InputError class="mt-1.5 text-xs text-rose-500 dark:text-rose-400" :message="form.errors.password" />
      </div>

      <div class="flex items-center justify-between text-xs pt-1">
        <label class="flex items-center gap-2 cursor-pointer">
          <Checkbox name="remember" v-model:checked="form.remember" class="rounded bg-white dark:bg-slate-950 border-slate-300 dark:border-slate-700 text-amber-500 focus:ring-amber-500" />
          <span class="text-slate-600 dark:text-slate-400 font-medium select-none">Ingat saya</span>
        </label>

        <Link
          v-if="canResetPassword"
          :href="route('password.request')"
          class="text-xs font-semibold text-slate-500 dark:text-slate-400 hover:text-amber-600 dark:hover:text-amber-400 transition-colors"
        >
          Lupa kata sandi?
        </Link>
      </div>

      <div class="pt-2">
        <button
          type="submit"
          :disabled="form.processing"
          class="w-full py-3 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-bold text-sm shadow-lg shadow-amber-500/20 active:scale-[0.99] transition-all disabled:opacity-50 cursor-pointer"
        >
          <span v-if="form.processing">Memverifikasi Akun...</span>
          <span v-else>Masuk ke Sistem Kasir</span>
        </button>
      </div>
    </form>
  </GuestLayout>
</template>
