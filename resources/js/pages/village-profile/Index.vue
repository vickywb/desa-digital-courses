<script setup>
import { ref, onMounted } from 'vue';
import client from '../../api/client';
import { useAuthStore } from '../../stores/auth';

const authStore = useAuthStore();
const profile = ref(null);
const loading = ref(true);

const isStaff = authStore.userRole !== 'head_of_family';
const basePath = isStaff ? '/staff' : '/warga';

onMounted(async () => {
    try {
        const prefix = isStaff ? '/village-staff' : '/village-resident';
        const res = await client.get(`${prefix}/village-profiles`);
        profile.value = res.data.data ?? null;
    } catch {
        profile.value = null;
    } finally {
        loading.value = false;
    }
});

</script>

<template>
    <div class="flex flex-col gap-4 md:gap-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <h1 class="font-semibold text-2xl">Profile Desa</h1>
            <div v-if="isStaff" class="flex gap-3 w-full sm:w-auto">
                <button @click="$router.push('/staff/village-profile/edit')" class="flex items-center rounded-2xl py-4 px-6 gap-[10px] bg-desa-black flex-1 sm:flex-initial justify-center">
                    <p class="font-medium text-white">Ubah Data</p>
                    <img src="/assets/images/icons/edit-white.svg" class="flex size-6 shrink-0" alt="icon">
                </button>
                <button @click="$router.push('/staff/village-profile/create')" class="flex items-center rounded-2xl py-4 px-6 gap-[10px] bg-desa-dark-green flex-1 sm:flex-initial justify-center">
                    <p class="font-medium text-white">Create Profile</p>
                    <img src="/assets/images/icons/add-square-white.svg" class="flex size-6 shrink-0" alt="icon">
                </button>
            </div>
        </div>

        <div v-if="loading" class="flex items-center justify-center py-20">
            <p class="font-medium text-desa-secondary">Memuat...</p>
        </div>

        <div v-else-if="!profile" class="flex flex-col flex-1 items-center justify-center gap-6 py-20">
            <img src="/assets/images/icons/user-remove-secondary-green.svg" class="size-20 object-cover" alt="icon">
            <p class="font-semibold text-lg text-desa-secondary text-center px-4">Ops, Saat ini kamu belum membuat profile desa</p>
        </div>

        <div v-else class="flex flex-col gap-[14px]">
            <div class="flex flex-col lg:flex-row gap-[14px]">
                <section class="flex flex-col w-full lg:w-[565px] shrink-0 h-fit rounded-3xl p-4 sm:p-6 gap-4 sm:gap-6 bg-white">
                    <div class="flex items-center justify-between">
                        <p class="font-medium leading-5 text-desa-secondary">Nama Desa</p>
                        <img src="/assets/images/icons/building-foreshadow-background.svg" class="flex size-12 shrink-0" alt="icon">
                    </div>
                    <div class="flex flex-col gap-[6px]">
                        <h1 class="font-bold text-[32px] leading-10 break-words">{{ profile.name }}</h1>
                        <div class="flex items-center gap-0.5">
                            <img src="/assets/images/icons/location-secondary-green.svg" class="flex size-6 shrink-0" alt="icon">
                            <span class="font-medium text-sm text-desa-secondary">{{ profile.headman }}</span>
                        </div>
                    </div>
                    <div class="flex flex-col gap-3">
                        <p class="font-medium text-sm text-desa-secondary">Tentang Desa</p>
                        <p class="font-medium leading-8">{{ profile.about }}</p>
                    </div>
                </section>

                <section class="flex flex-col flex-1 h-fit shrink-0 rounded-3xl p-4 sm:p-6 gap-4 sm:gap-6 bg-white">
                    <p class="font-medium leading-5 text-desa-secondary">Detail Desa</p>
                    <div class="flex flex-col gap-[14px]">
                        <div class="flex items-center gap-3 w-full shrink-0">
                            <div class="flex size-[54px] rounded-full bg-desa-foreshadow overflow-hidden shrink-0">
                                <img src="/assets/images/photos/kk-photo-1.png" class="w-full h-full object-cover" alt="icon">
                            </div>
                            <div class="flex flex-col gap-1 min-w-0">
                                <p class="font-semibold text-lg leading-5 text-desa-black truncate">{{ profile.headman }}</p>
                                <p class="flex items-center gap-1">
                                    <span class="font-medium text-sm text-desa-secondary">Kepala Desa</span>
                                </p>
                            </div>
                        </div>
                        <hr class="border-desa-background">
                        <div class="flex items-center gap-3">
                            <div class="flex size-[52px] rounded-2xl items-center justify-center bg-desa-foreshadow overflow-hidden shrink-0">
                                <img src="/assets/images/icons/profile-2user-dark-green.svg" class="flex size-6 shrink-0" alt="icon">
                            </div>
                            <div class="flex flex-col gap-1 min-w-0">
                                <p class="font-semibold text-lg leading-5 truncate">{{ profile.people }}</p>
                                <p class="font-medium text-sm text-desa-secondary">Jumlah Penduduk</p>
                            </div>
                        </div>
                        <hr class="border-desa-background">
                        <div class="flex items-center gap-3">
                            <div class="flex size-[52px] rounded-2xl items-center justify-center bg-desa-foreshadow overflow-hidden shrink-0">
                                <img src="/assets/images/icons/tree-dark-green.svg" class="flex size-6 shrink-0" alt="icon">
                            </div>
                            <div class="flex flex-col gap-1 min-w-0">
                                <p class="font-semibold text-lg leading-5 truncate">{{ profile.agriculture_area }}</p>
                                <p class="font-medium text-sm text-desa-secondary">Luas Pertanian</p>
                            </div>
                        </div>
                        <hr class="border-desa-background">
                        <div class="flex items-center gap-3">
                            <div class="flex size-[52px] rounded-2xl items-center justify-center bg-desa-foreshadow overflow-hidden shrink-0">
                                <img src="/assets/images/icons/grid-5-dark-green.svg" class="flex size-6 shrink-0" alt="icon">
                            </div>
                            <div class="flex flex-col gap-1 min-w-0">
                                <p class="font-semibold text-lg leading-5 truncate">{{ profile.total_area }}</p>
                                <p class="font-medium text-sm text-desa-secondary">Luas Area</p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

        </div>
    </div>
</template>
