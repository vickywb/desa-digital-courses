<script setup>
import { ref, onMounted } from 'vue';
import { useAuthStore } from '../../stores/auth';
import client from '../../api/client';

const authStore = useAuthStore();
const stats = ref({
    anggota: 0,
    events: 0,
    bansos: 0,
    pembangunan: 0,
});
const userName = ref('');

onMounted(async () => {
    try {
        const me = await client.get('/auth/me');
        const user = me.data.data.user;
        userName.value = user.head_of_family?.full_name ?? user.username;

        const [events, bansos, developments] = await Promise.all([
            client.get('/village-resident/events'),
            client.get('/village-resident/social-assistances'),
            client.get('/village-resident/developments'),
        ]);

        stats.value = {
            anggota: user.head_of_family?.family_members?.length ?? 0,
            events: events.data.data?.length ?? 0,
            bansos: bansos.data.data?.length ?? 0,
            pembangunan: developments.data.data?.length ?? 0,
        };
    } catch {
        //
    }
});
</script>

<template>
    <div class="flex flex-col gap-[14px]">
        <div class="flex flex-col gap-6 p-6 md:p-8 bg-white rounded-3xl gradient-vertical">
            <div class="flex items-center gap-4">
                <div class="flex size-16 md:size-20 rounded-full overflow-hidden bg-white/20 shrink-0">
                    <img src="/desa-digital/src/assets/images/photos/kk-photo-1.png" class="w-full h-full object-cover" alt="photo">
                </div>
                <div class="flex flex-col gap-1 text-white">
                    <p class="font-medium text-sm text-desa-lime">Selamat Datang</p>
                    <h1 class="font-semibold text-xl md:text-2xl truncate">{{ userName }}</h1>
                    <p class="font-medium text-sm opacity-80">Kepala Keluarga</p>
                </div>
            </div>
        </div>

        <section class="grid grid-cols-1 sm:grid-cols-2 gap-[14px]">
            <router-link to="/family-members" class="card flex flex-col rounded-2xl p-6 gap-3 bg-white hover:shadow-md transition-setup">
                <div class="flex items-center justify-between">
                    <p class="font-medium text-desa-secondary">Anggota Keluarga</p>
                    <img src="/desa-digital/src/assets/images/icons/profil-2user-foreshadow-background.svg" class="flex size-12 shrink-0" alt="icon">
                </div>
                <p class="font-semibold text-[32px] leading-10">{{ stats.anggota }}</p>
                <p class="font-medium text-sm text-desa-secondary">Total anggota keluarga</p>
            </router-link>

            <router-link to="/events" class="card flex flex-col rounded-2xl p-6 gap-3 bg-white hover:shadow-md transition-setup">
                <div class="flex items-center justify-between">
                    <p class="font-medium text-desa-secondary">Event Desa</p>
                    <img src="/desa-digital/src/assets/images/icons/calendar-2-foreshadow-background.svg" class="flex size-12 shrink-0" alt="icon">
                </div>
                <p class="font-semibold text-[32px] leading-10">{{ stats.events }}</p>
                <p class="font-medium text-sm text-desa-secondary">Event tersedia</p>
            </router-link>

            <router-link to="/social-assistances" class="card flex flex-col rounded-2xl p-6 gap-3 bg-white hover:shadow-md transition-setup">
                <div class="flex items-center justify-between">
                    <p class="font-medium text-desa-secondary">Bantuan Sosial</p>
                    <img src="/desa-digital/src/assets/images/icons/bag-2-foreshadow-background.svg" class="flex size-12 shrink-0" alt="icon">
                </div>
                <p class="font-semibold text-[32px] leading-10">{{ stats.bansos }}</p>
                <p class="font-medium text-sm text-desa-secondary">Bansos tersedia</p>
            </router-link>

            <router-link to="/developments" class="card flex flex-col rounded-2xl p-6 gap-3 bg-white hover:shadow-md transition-setup">
                <div class="flex items-center justify-between">
                    <p class="font-medium text-desa-secondary">Pembangunan</p>
                    <img src="/desa-digital/src/assets/images/icons/buildings-foreshadow-background.svg" class="flex size-12 shrink-0" alt="icon">
                </div>
                <p class="font-semibold text-[32px] leading-10">{{ stats.pembangunan }}</p>
                <p class="font-medium text-sm text-desa-secondary">Pembangunan berjalan</p>
            </router-link>
        </section>
    </div>
</template>
