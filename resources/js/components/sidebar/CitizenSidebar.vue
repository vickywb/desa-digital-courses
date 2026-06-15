<script setup>
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
import iconMoneyActive from '@/assets/images/icons/wallet-check-dark-green.svg';
import iconMoneyInactive from '@/assets/images/icons/wallet-3-secondary-green.svg';

defineProps({
    open: Boolean,
});

const emit = defineEmits(['close']);

const sidebarItems = [
    {
        label: 'Dashboard',
        path: '/warga/dashboard',
        iconActive: iconChartActive,
        iconInactive: iconChartInactive,
    },
    {
        label: 'Anggota Keluarga',
        path: '/warga/family-members',
        iconActive: iconCrownActive,
        iconInactive: iconCrownInactive,
    },
    {
        label: 'Bantuan Sosial',
        path: '',
        iconActive: iconBagActive,
        iconInactive: iconBagInactive,
        children: [
            { label: 'List Bansos', path: '/warga/bansos' },
            { label: 'Pengajuan Saya', path: '/warga/bansos/pengajuan-saya' },
        ],
    },
    {
        label: 'Jadwal Desa',
        path: '',
        iconActive: iconCalendarActive,
        iconInactive: iconCalendarInactive,
        children: [
            { label: 'Pembangunan', path: '/warga/pembangunan' },
            { label: 'Event Desa', path: '/warga/events' },
        ],
    },
    {
        label: 'Profile Desa',
        path: '/warga/village-profile',
        iconActive: iconBuilding4Active,
        iconInactive: iconBuilding4Inactive,
    },
    {
        label: 'Kas Desa',
        path: '/warga/kas',
        iconActive: iconMoneyActive,
        iconInactive: iconMoneyInactive,
    },
];
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
