<script setup>
import { ref, computed, onMounted } from 'vue';
import client from '../../api/client';
import { formatRupiah, formatToClientTimezone } from '../../helpers/format';

const items = ref([]);
const myApplicantIds = ref([]);
const activeTab = ref('all');

const statusClass = (status) => {
    const map = {
        planned: 'bg-desa-yellow',
        ongoing: 'bg-desa-soft-green',
        completed: 'bg-desa-dark-green',
    };
    return map[status] ?? 'bg-desa-grey';
};

const statusLabel = (status) => {
    const map = {
        planned: 'Direncanakan',
        ongoing: 'Berlangsung',
        completed: 'Selesai',
    };
    return map[status] ?? status;
};

onMounted(async () => {
    try {
        const res = await client.get('/village-resident/developments');
        items.value = res.data.data ?? [];
    } catch {
        items.value = [];
    }

    try {
        const res = await client.get('/village-resident/my-development-applicants');
        myApplicantIds.value = (res.data.data ?? []).map(a => a.development?.id).filter(Boolean);
    } catch {
        myApplicantIds.value = [];
    }
});

const myItems = computed(() => items.value.filter(i => myApplicantIds.value.includes(i.id)));
</script>

<template>
    <div class="flex flex-col gap-[14px]">
        <header>
            <h1 class="font-semibold text-2xl leading-[30px]">Pembangunan Desa</h1>
        </header>

        <div class="flex gap-4 border-b border-desa-background">
            <button @click="activeTab = 'all'"
                class="pb-3 font-medium leading-5 transition-setup"
                :class="activeTab === 'all' ? 'text-desa-dark-green border-b-2 border-desa-dark-green' : 'text-desa-secondary'">
                Semua Pembangunan
            </button>
            <button @click="activeTab = 'mine'"
                class="pb-3 font-medium leading-5 transition-setup"
                :class="activeTab === 'mine' ? 'text-desa-dark-green border-b-2 border-desa-dark-green' : 'text-desa-secondary'">
                Lamaran Saya ({{ myItems.length }})
            </button>
        </div>

        <section class="flex flex-col gap-4">
            <div v-if="activeTab === 'all' && !items.length"
                class="flex flex-col items-center justify-center py-20 gap-6">
                <p class="font-medium text-desa-secondary">Belum ada pembangunan desa</p>
            </div>

            <div v-if="activeTab === 'mine' && !myItems.length"
                class="flex flex-col items-center justify-center py-20 gap-6">
                <p class="font-medium text-desa-secondary">Belum ada lamaran</p>
            </div>

            <router-link v-for="item in (activeTab === 'all' ? items : myItems)" :key="item.id" :to="`/warga/pembangunan/${item.id}`"
                class="flex flex-col gap-3 sm:gap-4 p-4 sm:p-6 bg-white rounded-3xl hover:shadow-md transition-setup">
                <div class="flex items-center justify-between gap-2">
                    <div class="min-w-0 flex-1">
                        <h2 class="font-semibold text-sm sm:text-lg leading-5 sm:leading-[22.5px] line-clamp-1">{{ item.title }}</h2>
                        <p class="hidden sm:flex items-center gap-1 mt-1 text-xs sm:text-sm text-desa-secondary truncate">{{ item.person_in_charge }}</p>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <span v-if="myApplicantIds.includes(item.id)"
                            class="rounded-full px-3 py-1.5 bg-desa-soft-green text-white font-semibold text-[10px] sm:text-xs">Ikut</span>
                        <span class="rounded-full px-3 py-1.5 text-[10px] sm:text-xs font-semibold text-white shrink-0"
                            :class="statusClass(item.status)">
                            {{ statusLabel(item.status) }}
                        </span>
                    </div>
                </div>

                <div class="flex sm:hidden items-center gap-2 text-xs text-desa-secondary">
                    <span>Rp {{ formatRupiah(item.amount) }}</span>
                    <span>•</span>
                    <span class="truncate">{{ formatToClientTimezone(item.start_date) }}</span>
                </div>

                <div class="hidden sm:block">
                    <hr class="border-desa-background" />
                    <section class="points grid grid-cols-3 gap-4 pt-3">
                        <div class="point flex items-center gap-3">
                            <div class="p-[14px] shrink-0 bg-desa-foreshadow rounded-2xl">
                                <img src="@/assets/images/icons/money-dark-green.svg" alt="icon" class="size-6 shrink-0" />
                            </div>
                            <div class="flex flex-col gap-1">
                                <p class="font-semibold text-lg leading-[22.5px] text-desa-dark-green">Rp {{ formatRupiah(item.amount) }}</p>
                                <h3 class="font-medium text-sm leading-[17.5px] text-desa-secondary">Anggaran</h3>
                            </div>
                        </div>
                        <div class="point flex items-center gap-3">
                            <div class="p-[14px] shrink-0 bg-desa-foreshadow rounded-2xl">
                                <img src="@/assets/images/icons/calendar-2-dark-green.svg" alt="icon" class="size-6 shrink-0" />
                            </div>
                            <div class="flex flex-col gap-1">
                                <p class="font-semibold text-lg leading-[22.5px] text-desa-dark-green">{{ formatToClientTimezone(item.start_date) }}</p>
                                <h3 class="font-medium text-sm leading-[17.5px] text-desa-secondary">Mulai</h3>
                            </div>
                        </div>
                        <div class="point flex items-center gap-3">
                            <div class="p-[14px] shrink-0 rounded-2xl bg-desa-foreshadow">
                                <span class="rounded-full px-3 py-1 text-xs font-semibold text-white"
                                    :class="statusClass(item.status)">
                                    {{ statusLabel(item.status) }}
                                </span>
                            </div>
                            <div class="flex flex-col gap-1">
                                <p class="font-semibold text-lg leading-[22.5px]">{{ statusLabel(item.status) }}</p>
                                <h3 class="font-medium text-sm leading-[17.5px] text-desa-secondary">Status</h3>
                            </div>
                        </div>
                    </section>
                </div>
            </router-link>
        </section>
    </div>
</template>
