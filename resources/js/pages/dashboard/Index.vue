<script setup>
import { ref, onMounted } from 'vue';
import client from '../../api/client';

const stats = ref({
    totalPopulation: 0,
    totalDevelopments: 0,
    totalHeadFamilies: 0,
    totalEvents: 0,
});

onMounted(async () => {
    try {
        const prefix = '/village-staff';
        const [headF, dev, evt] = await Promise.all([
            client.get(`${prefix}/head-families`),
            client.get(`${prefix}/developments`),
            client.get(`${prefix}/events`),
        ]);

        const families = headF.data.data ?? [];
        const totalMembers = families.reduce((sum, f) => sum + (f.family_members_count ?? 0), 0);

        stats.value = {
            totalPopulation: families.length + totalMembers,
            totalHeadFamilies: families.length,
            totalDevelopments: dev.data.data?.length ?? 0,
            totalEvents: evt.data.data?.length ?? 0,
        };
    } catch {
        // Default values
    }
});
</script>

<template>
    <div class="flex flex-col gap-[14px]">
        <h1 class="font-semibold text-2xl">Desa Statistics</h1>

        <div id="Row-1" class="flex flex-col lg:flex-row gap-[14px]">
            <div class="flex flex-col w-full lg:w-[389px] h-auto lg:h-[358px] rounded-2xl p-6 gap-6 gradient-vertical">
                <img src="/desa-digital/src/assets/images/icons/gift-orange-background.svg" class="flex size-[86px] shrink-0" alt="icon">
                <div class="flex flex-col gap-3">
                    <p class="font-medium text-sm text-desa-lime">— Bantuan Sosial</p>
                    <p class="font-semibold text-2xl text-white text-nowrap">Dari Desa untuk Warga ❤️</p>
                    <p class="text-white">Wujudkan kesejahteraan desa dengan bantuan sosial yang tepat sasaran.</p>
                </div>
                <router-link to="/social-assistances" class="flex items-center justify-between rounded-2xl p-4 gap-[10px] bg-white">
                    <span class="font-medium text-desa-dark-green leading-5">Bikin Bantuan Sosial</span>
                    <img src="/desa-digital/src/assets/images/icons/add-square-dark-green.svg" class="flex size-6 shrink-0" alt="icon">
                </router-link>
            </div>

            <section id="Statistics" class="grid grid-cols-1 sm:grid-cols-2 flex-1 gap-[14px]">
                <div class="card flex flex-col w-full rounded-2xl p-6 gap-3 bg-white">
                    <div class="flex items-center justify-between">
                        <p class="font-medium text-desa-secondary">Jumlah Penduduk</p>
                        <img src="/desa-digital/src/assets/images/icons/profil-2user-foreshadow-background.svg" class="flex size-12 shrink-0" alt="icon">
                    </div>
                    <div class="flex flex-col gap-[6px]">
                        <p class="font-semibold text-[32px] leading-10">{{ stats.totalPopulation.toLocaleString() }}</p>
                    </div>
                </div>
                <div class="card flex flex-col w-full rounded-2xl p-6 gap-3 bg-white">
                    <div class="flex items-center justify-between">
                        <p class="font-medium text-desa-secondary">Pembangunan</p>
                        <img src="/desa-digital/src/assets/images/icons/buildings-foreshadow-background.svg" class="flex size-12 shrink-0" alt="icon">
                    </div>
                    <div class="flex flex-col gap-[6px]">
                        <p class="font-semibold text-[32px] leading-10">{{ stats.totalDevelopments.toLocaleString() }}</p>
                    </div>
                </div>
                <div class="card flex flex-col w-full rounded-2xl p-6 gap-3 bg-white">
                    <div class="flex items-center justify-between">
                        <p class="font-medium text-desa-secondary">Kepala Rumah</p>
                        <img src="/desa-digital/src/assets/images/icons/crown-foreshadow-background.svg" class="flex size-12 shrink-0" alt="icon">
                    </div>
                    <div class="flex flex-col gap-[6px]">
                        <p class="font-semibold text-[32px] leading-10">{{ stats.totalHeadFamilies.toLocaleString() }}</p>
                    </div>
                </div>
                <div class="card flex flex-col w-full rounded-2xl p-6 gap-3 bg-white">
                    <div class="flex items-center justify-between">
                        <p class="font-medium text-desa-secondary">Total Events</p>
                        <img src="/desa-digital/src/assets/images/icons/calendar-2-foreshadow-background.svg" class="flex size-12 shrink-0" alt="icon">
                    </div>
                    <div class="flex flex-col gap-[6px]">
                        <p class="font-semibold text-[32px] leading-10">{{ stats.totalEvents.toLocaleString() }}</p>
                    </div>
                </div>
            </section>
        </div>

        <div id="Row-2" class="flex flex-col sm:flex-row gap-[14px]">
            <router-link to="/social-assistances" class="flex flex-col w-full sm:w-[497px] shrink-0 rounded-2xl bg-white p-6 gap-3 hover:shadow-md transition-setup">
                <div class="flex items-center justify-between">
                    <p class="font-medium text-desa-secondary">Bantuan Sosial</p>
                    <img src="/desa-digital/src/assets/images/icons/bag-2-foreshadow-background.svg" class="flex size-12 shrink-0" alt="icon">
                </div>
                <p class="font-semibold text-[32px] leading-10">{{ stats.totalHeadFamilies }}</p>
                <p class="font-medium text-sm text-desa-secondary">Total kepala keluarga</p>
            </router-link>

            <router-link to="/events" class="flex flex-col flex-1 rounded-2xl bg-white p-6 gap-3 hover:shadow-md transition-setup">
                <div class="flex items-center justify-between">
                    <p class="font-medium text-desa-secondary">Events Mendatang</p>
                    <img src="/desa-digital/src/assets/images/icons/calendar-2-foreshadow-background.svg" class="flex size-12 shrink-0" alt="icon">
                </div>
                <p class="font-semibold text-[32px] leading-10">{{ stats.totalEvents }}</p>
                <p class="font-medium text-sm text-desa-secondary">Jadwal desa terbaru</p>
            </router-link>
        </div>

        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between h-auto sm:h-[125px] rounded-2xl p-6 sm:p-8 gap-4 gradient-horizontal">
            <div class="flex flex-col gap-[6px]">
                <p class="font-medium text-sm text-desa-lime">— Unduh Data Desa</p>
                <p class="font-semibold text-2xl text-white text-nowrap">Data Desa Terkini</p>
                <p class="text-sm text-desa-lime">Dapatkan laporan lengkap data desa dalam format PDF.</p>
            </div>
            <button class="flex items-center gap-[10px] rounded-2xl py-4 px-6 bg-white text-desa-dark-green font-medium hover:bg-desa-soft-green hover:text-white transition-setup shrink-0">
                <img src="/desa-digital/src/assets/images/icons/document-download-dark-green.svg" class="flex size-6 shrink-0" alt="icon">
                Download Laporan
            </button>
        </div>
    </div>
</template>
