<script setup>
import { ref, computed, onMounted } from 'vue';
import client from '../../api/client';
import { formatRupiah, formatToClientTimezone } from '../../helpers/format';

const items = ref([]);
const myEventIds = ref([]);
const activeTab = ref('all');

onMounted(async () => {
    try {
        const res = await client.get('/village-resident/events');
        items.value = res.data.data ?? [];
    } catch {
        items.value = [];
    }

    try {
        const res = await client.get('/village-resident/my-event-participants');
        myEventIds.value = (res.data.data ?? []).map(p => p.event?.id).filter(Boolean);
    } catch {
        myEventIds.value = [];
    }
});

const myItems = computed(() => items.value.filter(i => myEventIds.value.includes(i.id)));
</script>

<template>
    <div class="flex flex-col gap-[14px]">
        <header>
            <h1 class="font-semibold text-2xl leading-[30px]">Event Desa</h1>
        </header>

        <div class="flex gap-4 border-b border-desa-background">
            <button @click="activeTab = 'all'"
                class="pb-3 font-medium leading-5 transition-setup"
                :class="activeTab === 'all' ? 'text-desa-dark-green border-b-2 border-desa-dark-green' : 'text-desa-secondary'">
                Semua Event
            </button>
            <button @click="activeTab = 'mine'"
                class="pb-3 font-medium leading-5 transition-setup"
                :class="activeTab === 'mine' ? 'text-desa-dark-green border-b-2 border-desa-dark-green' : 'text-desa-secondary'">
                Saya Ikut ({{ myItems.length }})
            </button>
        </div>

        <section class="flex flex-col gap-4">
            <div v-if="activeTab === 'all' && !items.length"
                class="flex flex-col items-center justify-center py-20 gap-6">
                <p class="font-medium text-desa-secondary">Belum ada event desa</p>
            </div>

            <div v-if="activeTab === 'mine' && !myItems.length"
                class="flex flex-col items-center justify-center py-20 gap-6">
                <p class="font-medium text-desa-secondary">Belum ada event yang diikuti</p>
            </div>

            <div v-for="item in (activeTab === 'all' ? items : myItems)" :key="item.id"
                class="flex flex-col gap-6 p-6 bg-white rounded-3xl">
                <header class="flex items-center justify-between gap-[36px]">
                    <div class="flex items-center gap-4 min-w-0">
                        <div class="flex justify-center items-center w-[100px] h-[80px] shrink-0 rounded-2xl overflow-hidden bg-desa-foreshadow">
                            <img :src="item.event_file?.url" alt="image" class="size-full object-cover" />
                        </div>
                        <div class="title flex flex-col gap-[6px] min-w-0">
                            <h2 class="font-semibold text-lg leading-[22.5px] line-clamp-1">{{ item.title }}</h2>
                            <div class="flex items-center gap-1">
                                <img src="@/assets/images/icons/profile-secondary-green-bold.svg" class="size-4 shrink-0" alt="icon" />
                                <p class="font-medium text-sm leading-[17.5px] text-desa-secondary">
                                    {{ item.price && Number(item.price) > 0 ? 'Rp ' + formatRupiah(item.price) : 'Gratis' }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 shrink-0">
                        <span v-if="myEventIds.includes(item.id)"
                            class="rounded-full py-2 px-4 bg-desa-soft-green text-white font-semibold text-xs">Sudah Ikut</span>
                        <router-link :to="`/warga/events/${item.id}`">
                            <div class="rounded-2xl bg-desa-black py-[18px] px-6 font-medium leading-5 text-white">Detail</div>
                        </router-link>
                    </div>
                </header>
                <hr class="border-desa-background" />
                <section class="points grid grid-cols-3 gap-4">
                    <div class="point flex items-center gap-3">
                        <div class="p-[14px] shrink-0 bg-desa-foreshadow rounded-2xl">
                            <img src="@/assets/images/icons/money-dark-green.svg" alt="icon" class="size-6 shrink-0" />
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="font-semibold text-lg leading-[22.5px] text-desa-dark-green">
                                {{ item.price && Number(item.price) > 0 ? 'Rp ' + formatRupiah(item.price) : 'Gratis' }}
                            </p>
                            <h3 class="font-medium text-sm leading-[17.5px] text-desa-secondary">Harga</h3>
                        </div>
                    </div>
                    <div class="point flex items-center gap-3">
                        <div class="p-[14px] shrink-0 bg-desa-foreshadow rounded-2xl">
                            <img src="@/assets/images/icons/calendar-2-dark-green.svg" alt="icon" class="size-6 shrink-0" />
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="font-semibold text-lg leading-[22.5px] text-desa-dark-green">{{ formatToClientTimezone(item.start_date) }}</p>
                            <h3 class="font-medium text-sm leading-[17.5px] text-desa-secondary">Tanggal</h3>
                        </div>
                    </div>
                    <div class="point flex items-center gap-3">
                        <div class="p-[14px] shrink-0 bg-desa-foreshadow rounded-2xl">
                            <span class="rounded-full px-3 py-1 text-xs font-semibold text-white"
                                :class="item.is_active ? 'bg-desa-soft-green' : 'bg-desa-red'">
                                {{ item.is_active ? 'Dibuka' : 'Ditutup' }}
                            </span>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="font-semibold text-lg leading-[22.5px]">{{ item.is_active ? 'Dibuka' : 'Ditutup' }}</p>
                            <h3 class="font-medium text-sm leading-[17.5px] text-desa-secondary">Pendaftaran</h3>
                        </div>
                    </div>
                </section>
            </div>
        </section>
    </div>
</template>
