<script setup>
import { computed } from 'vue';
import { useAuthStore } from '@/stores/auth';
import SidebarItem from './SidebarItem.vue';
import iconChartActive from '@/assets/images/icons/chart-square-dark-green.svg';
import iconChartInactive from '@/assets/images/icons/chart-square-secondary-green.svg';
import iconCrownActive from '@/assets/images/icons/crown-dark-green.svg';
import iconCrownInactive from '@/assets/images/icons/crown-secondary-green.svg';
import iconBagActive from '@/assets/images/icons/bag-2-dark-green.svg';
import iconBagInactive from '@/assets/images/icons/bag-2-secondary-green.svg';
import iconBuilding4Active from '@/assets/images/icons/building-4-dark-green.svg';
import iconBuilding4Inactive from '@/assets/images/icons/building-4-secondary-green.svg';
import iconCalendarActive from '@/assets/images/icons/calendar-2-dark-green.svg';
import iconCalendarInactive from '@/assets/images/icons/calendar-2-secondary-green.svg';

defineProps({
    open: Boolean,
});

const emit = defineEmits(['close']);

const authStore = useAuthStore();
const isKD = computed(() => authStore.userRole !== 'head_of_family');

const sidebarItems = computed(() => [
    {
        label: 'Dashboard',
        path: '/',
        iconActive: iconChartActive,
        iconInactive: iconChartInactive,
    },
    ...(isKD.value
        ? [{
            label: 'Kepala Rumah',
            path: '/head-families',
            iconActive: iconCrownActive,
            iconInactive: iconCrownInactive,
        }]
        : [{
            label: 'Anggota Keluarga',
            path: '/family-members',
            iconActive: iconCrownActive,
            iconInactive: iconCrownInactive,
        }]
    ),
    {
        label: 'Bantuan Sosial',
        path: '',
        iconActive: iconBagActive,
        iconInactive: iconBagInactive,
        children: [
            { label: 'List Bansos', path: '/social-assistances' },
            { label: 'Pengajuan Bansos', path: '/social-assistances/recipients' },
        ],
    },
    {
        label: 'Jadwal Desa',
        path: '',
        iconActive: iconCalendarActive,
        iconInactive: iconCalendarInactive,
        children: [
            { label: 'Pembangunan', path: '/developments' },
            { label: 'Event Desa', path: '/events' },
        ],
    },
    {
        label: 'Profile Desa',
        path: '/village-profile',
        iconActive: iconBuilding4Active,
        iconInactive: iconBuilding4Inactive,
    },
]);
</script>

<template>
    <aside
        id="Sidebar"
        class="relative flex w-[280px] shrink-0 min-h-screen bg-white border-r border-desa-foreshadow overflow-hidden max-lg:fixed max-lg:z-40 max-lg:transition-transform max-lg:duration-300 max-lg:ease-in-out"
        :class="open ? 'max-lg:translate-x-0' : 'max-lg:-translate-x-full'"
    >
        <div class="fixed top-0 h-full w-[280px] flex shrink-0 flex-1 z-20 bg-white">
            <div class="flex flex-col h-full w-full gap-6 pt-[30px] px-6">
                <div class="flex items-center justify-between">
                    <img src="@/assets/images/logos/logo-text.svg" class="flex h-[30px] shrink-0" alt="logo">
                    <button
                        class="flex size-11 items-center justify-center rounded-2xl border border-desa-background hover:border-desa-secondary transition-setup lg:hidden"
                        @click="emit('close')"
                    >
                        <svg class="size-6 text-desa-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="flex flex-col flex-1 gap-6 overflow-y-scroll hide-scrollbar">
                    <nav class="flex flex-col gap-2 pb-12">
                        <p class="font-medium text-sm text-desa-secondary">Main Menu</p>
                        <ul class="flex flex-col gap-2">
                            <SidebarItem v-for="item in sidebarItems" :key="item.path || item.label" :item="item" @close="emit('close')" />
                        </ul>
                    </nav>
                    <div class="flex items-center justify-between h-[84px] rounded-2xl p-5 mb-4 gap-3 bg-desa-black">
                        <div class="flex flex-col gap-1">
                            <p class="font-semibold leading-5 text-white">Beralih ke Pro</p>
                            <a href="#"
                                class="flex items-center font-medium text-sm hover:underline text-desa-soft-green">
                                Upgrade Plan
                                <img src="@/assets/images/icons/arrow-right-soft-green.png"
                                    class="flex size-3 shrink-0 ml-0.5" alt="icon">
                            </a>
                        </div>
                        <img src="@/assets/images/icons/crown-soft-green-background.svg" class="flex size-11 shrink-0"
                            alt="icon">
                    </div>
                </div>
            </div>
        </div>
    </aside>

    <div
        v-if="open"
        class="fixed inset-0 z-30 bg-black/50 lg:hidden"
        @click="emit('close')"
    />
</template>
