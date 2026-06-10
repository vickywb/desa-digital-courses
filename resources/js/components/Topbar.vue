<script setup>
import { useAuthStore } from '@/stores/auth';
import { storeToRefs } from 'pinia';

const emit = defineEmits(['toggle-sidebar']);

const authStore = useAuthStore();
const { user } = storeToRefs(authStore);
const { logout } = authStore
import { computed } from 'vue';
const roleLabel = computed(() => {
    const role = user.value?.role;
    if (role === 'admin') return 'Administrator';
    if (role === 'head_village') return 'Kepala Desa';
    if (role === 'staff') return 'Staff Desa';
    if (role === 'head_of_family') return 'Kepala Keluarga';
    return '';
});
</script>

<template>
    <div id="Top-Bar" class="relative flex h-[72px] md:h-[116px] shrink-0">
        <div
            class="fixed top-0 left-0 lg:left-[280px] flex items-center w-full lg:w-[calc(100%-280px)] h-[72px] md:h-[116px] py-4 md:py-[30px] px-4 md:px-6 gap-2 md:gap-4 bg-white z-30 border-l border-desa-background">
            <button
                class="flex size-10 shrink-0 items-center justify-center rounded-2xl border border-desa-background hover:border-desa-secondary transition-setup lg:hidden"
                @click="emit('toggle-sidebar')"
            >
                <svg class="size-5 text-desa-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <form action="#" class="flex w-full max-md:hidden">
                <label
                    class="group flex w-full items-center rounded-full border border-desa-background py-4 px-6 gap-2 bg-white hover:border-desa-dark-green focus-within:border-desa-dark-green transition-setup">
                    <button type="submit" class="relative flex size-6 shrink-0">
                        <img src="@/assets/images/icons/search-normal-dark-green.svg"
                            class="absolute flex size-6 shrink-0 opacity-0 group-focus-within:opacity-100 transition-setup"
                            alt="icon">
                        <img src="@/assets/images/icons/search-normal-secondary-green.svg"
                            class="absolute flex size-6 shrink-0 opacity-100 group-focus-within:opacity-0 transition-setup"
                            alt="icon">
                    </button>
                    <input type="text"
                        class="w-full appearance-none outline-none font-medium leading-5 text-desa-black placeholder:placeholder-shown:text-desa-secondary"
                        placeholder="Cari nama penduduk, pengajuan, events, dll">
                </label>
            </form>
            <a href="#"
                class="flex size-10 md:size-14 shrink-0 items-center justify-center rounded-2xl border border-desa-background hover:border-desa-secondary transition-setup">
                <img src="@/assets/images/icons/notification-secondary-green.svg" class="size-5 md:size-6" alt="icon">
            </a>
            <a href="#"
                class="flex size-10 md:size-14 shrink-0 items-center justify-center rounded-2xl border border-desa-background hover:border-desa-secondary transition-setup max-md:hidden">
                <img src="@/assets/images/icons/setting-2-secondary-green.svg" class="size-6" alt="icon">
            </a>
            <div class="flex items-center gap-2 md:gap-4">
                <div class="flex size-10 md:size-14 shrink-0 rounded-full overflow-hidden bg-desa-foreshadow">
                    <img src="@/assets/images/photos/photo-1.png" class="w-full h-full object-cover" alt="photo"
                        v-if="user?.role === 'admin' || user?.role === 'head_village' || user?.role === 'staff'">
                    <img :src="user?.head_of_family?.profile_picture?.url" class="w-full h-full object-cover" alt="photo"
                        v-else>
                </div>
                <div class="flex flex-col gap-[6px] max-md:hidden">
                    <p class="font-semibold leading-5 w-[120px] truncate">{{ user?.username }}</p>
                    <p class="font-medium text-sm text-desa-secondary">{{ roleLabel }}</p>
                </div>
                <button @click="logout" class="flex size-5 md:size-6 shrink-0 cursor-pointer">
                    <img src="@/assets/images/icons/logout-red.svg" class="size-5 md:size-6" alt="logout">
                </button>
            </div>
        </div>
    </div>
</template>
