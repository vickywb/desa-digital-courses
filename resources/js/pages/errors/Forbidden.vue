<script setup>
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const router = useRouter();
const auth = useAuthStore();

function goHome() {
    const target = auth.userRole === 'head_of_family' ? '/warga/dashboard' : '/staff/dashboard';
    router.push(target);
}
</script>

<template>
    <div class="flex min-h-screen items-center justify-center bg-desa-background">
        <div class="flex flex-col items-center gap-6 p-8 rounded-3xl bg-white max-w-md text-center">
            <div class="flex size-20 items-center justify-center rounded-full bg-desa-red/10">
                <svg class="size-10 text-desa-red" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <h1 class="font-semibold text-2xl">Akses Ditolak</h1>
            <p class="font-medium text-desa-secondary">Anda tidak memiliki hak akses ke halaman ini.</p>
            <button @click="goHome"
                class="rounded-2xl px-8 py-4 bg-desa-black text-white font-medium hover:bg-desa-dark-green transition-setup">
                Kembali ke Dashboard
            </button>
        </div>
    </div>
</template>
