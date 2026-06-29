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
const kas = ref(null);
const userName = ref('');

onMounted(async () => {
    const user = authStore.user;
    userName.value = user?.head_of_family?.full_name ?? user?.username ?? '';

    try {
        const [familyRes, events, bansos, developments, kasRes] = await Promise.all([
            client.get('/village-resident/family-members'),
            client.get('/village-resident/events'),
            client.get('/village-resident/social-assistances'),
            client.get('/village-resident/developments'),
            client.get('/village-resident/kas').catch(() => null),
        ]);

        stats.value = {
            anggota: familyRes.data.data?.length ?? 0,
            events: events.data.data?.length ?? 0,
            bansos: bansos.data.data?.length ?? 0,
            pembangunan: developments.data.data?.length ?? 0,
        };

        kas.value = kasRes?.data?.data ?? null;
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
                    <img src="/assets/images/photos/kk-photo-1.png" class="w-full h-full object-cover" alt="photo">
                </div>
                <div class="flex flex-col gap-1 text-white">
                    <p class="font-medium text-sm text-desa-lime">Selamat Datang</p>
                    <h1 class="font-semibold text-xl md:text-2xl truncate">{{ userName }}</h1>
                    <p class="font-medium text-sm opacity-80">Kepala Keluarga</p>
                </div>
            </div>
        </div>

        <section class="grid grid-cols-2 gap-[10px] sm:gap-[14px]">
            <router-link to="/warga/kas" class="card flex flex-col rounded-2xl p-3 sm:p-6 gap-2 sm:gap-3 bg-white hover:shadow-md transition-setup">
                <div class="flex items-center justify-between">
                    <p class="font-medium text-xs sm:text-sm text-desa-secondary">Total Kas</p>
                    <img src="/assets/images/icons/location-secondary-green.svg" class="flex size-8 sm:size-12 shrink-0" alt="icon">
                </div>
                <p class="font-semibold text-xl sm:text-[32px] leading-7 sm:leading-10 text-desa-dark-green">
                    {{ kas ? 'Rp ' + Number(kas.total_balance).toLocaleString('id-ID') : '-' }}
                </p>
                <p class="font-medium text-xs sm:text-sm text-desa-secondary">Kas desa terkini</p>
            </router-link>

            <router-link to="/warga/kas" class="card flex flex-col rounded-2xl p-3 sm:p-6 gap-2 sm:gap-3 bg-white hover:shadow-md transition-setup">
                <div class="flex items-center justify-between">
                    <p class="font-medium text-xs sm:text-sm text-desa-secondary">Pengeluaran Kas</p>
                    <img src="/assets/images/icons/grid-5-dark-green.svg" class="flex size-8 sm:size-12 shrink-0" alt="icon">
                </div>
                <p class="font-semibold text-xl sm:text-[32px] leading-7 sm:leading-10 text-desa-red">
                    {{ kas ? 'Rp ' + Number(kas.monthly_expense).toLocaleString('id-ID') : '-' }}
                </p>
                <p class="font-medium text-xs sm:text-sm text-desa-secondary">Pengeluaran bulan ini</p>
            </router-link>
        </section>

        <section class="grid grid-cols-2 gap-[10px] sm:gap-[14px]">
            <router-link to="/warga/family-members" class="card flex flex-col rounded-2xl p-3 sm:p-6 gap-2 sm:gap-3 bg-white hover:shadow-md transition-setup">
                <div class="flex items-center justify-between">
                    <p class="font-medium text-xs sm:text-sm text-desa-secondary">Anggota Keluarga</p>
                    <img src="/assets/images/icons/profil-2user-foreshadow-background.svg" class="flex size-8 sm:size-12 shrink-0" alt="icon">
                </div>
                <p class="font-semibold text-xl sm:text-[32px] leading-7 sm:leading-10">{{ stats.anggota }}</p>
                <p class="font-medium text-xs sm:text-sm text-desa-secondary">Total anggota</p>
            </router-link>

            <router-link to="/warga/events" class="card flex flex-col rounded-2xl p-3 sm:p-6 gap-2 sm:gap-3 bg-white hover:shadow-md transition-setup">
                <div class="flex items-center justify-between">
                    <p class="font-medium text-xs sm:text-sm text-desa-secondary">Event Desa</p>
                    <img src="/assets/images/icons/calendar-2-foreshadow-background.svg" class="flex size-8 sm:size-12 shrink-0" alt="icon">
                </div>
                <p class="font-semibold text-xl sm:text-[32px] leading-7 sm:leading-10">{{ stats.events }}</p>
                <p class="font-medium text-xs sm:text-sm text-desa-secondary">Event tersedia</p>
            </router-link>

            <router-link to="/warga/bansos" class="card flex flex-col rounded-2xl p-3 sm:p-6 gap-2 sm:gap-3 bg-white hover:shadow-md transition-setup">
                <div class="flex items-center justify-between">
                    <p class="font-medium text-xs sm:text-sm text-desa-secondary">Bantuan Sosial</p>
                    <img src="/assets/images/icons/bag-2-foreshadow-background.svg" class="flex size-8 sm:size-12 shrink-0" alt="icon">
                </div>
                <p class="font-semibold text-xl sm:text-[32px] leading-7 sm:leading-10">{{ stats.bansos }}</p>
                <p class="font-medium text-xs sm:text-sm text-desa-secondary">Bansos tersedia</p>
            </router-link>

            <router-link to="/warga/pembangunan" class="card flex flex-col rounded-2xl p-3 sm:p-6 gap-2 sm:gap-3 bg-white hover:shadow-md transition-setup">
                <div class="flex items-center justify-between">
                    <p class="font-medium text-xs sm:text-sm text-desa-secondary">Pembangunan</p>
                    <img src="/assets/images/icons/buildings-foreshadow-background.svg" class="flex size-8 sm:size-12 shrink-0" alt="icon">
                </div>
                <p class="font-semibold text-xl sm:text-[32px] leading-7 sm:leading-10">{{ stats.pembangunan }}</p>
                <p class="font-medium text-xs sm:text-sm text-desa-secondary">Pembangunan berjalan</p>
            </router-link>
        </section>
    </div>
</template>
