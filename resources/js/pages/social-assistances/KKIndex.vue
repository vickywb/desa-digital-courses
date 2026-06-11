<script setup>
import { ref, onMounted } from 'vue';
import client from '../../api/client';
import { formatRupiah } from '../../helpers/format';

const items = ref([]);

onMounted(async () => {
    try {
        const res = await client.get('/village-resident/social-assistances');
        items.value = res.data.data ?? [];
    } catch {
        items.value = [];
    }
});
</script>

<template>
    <div class="flex flex-col gap-[14px]">
        <header>
            <h1 class="font-semibold text-2xl leading-[30px]">Bantuan Sosial</h1>
        </header>

        <section id="List-Bansos" class="flex flex-col gap-4">
            <div v-if="!items.length" class="flex flex-col items-center justify-center py-20 gap-6">
                <p class="font-medium text-desa-secondary">Belum ada bantuan sosial tersedia</p>
            </div>

            <div v-for="item in items" :key="item.id"
                class="bansos flex flex-col gap-6 p-6 bg-white rounded-3xl">
                <header class="flex items-center justify-between gap-[36px]">
                    <div class="flex items-center gap-4 min-w-0">
                        <div class="flex justify-center items-center w-[100px] h-[80px] shrink-0 rounded-2xl overflow-hidden bg-desa-foreshadow">
                            <img :src="item.social_assistance_file?.url" alt="image" class="size-full object-cover" />
                        </div>
                        <div class="title flex flex-col gap-[6px] min-w-0">
                            <h2 class="font-semibold text-lg leading-[22.5px] line-clamp-1">{{ item.title }}</h2>
                            <div class="flex items-center gap-1">
                                <img src="@/assets/images/icons/profile-secondary-green-bold.svg" class="size-4 shrink-0" alt="icon" />
                                <p class="font-medium text-sm leading-[17.5px] text-desa-secondary truncate">{{ item.provider }}</p>
                            </div>
                        </div>
                    </div>
                    <router-link :to="`/social-assistances/detail/${item.id}`" class="shrink-0">
                        <div class="rounded-2xl bg-desa-black py-[18px] px-6 font-medium leading-5 text-white">Detail</div>
                    </router-link>
                </header>
                <hr class="border-desa-background" />
                <section class="points grid grid-cols-3 gap-4">
                    <div class="point flex items-center gap-3">
                        <div class="p-[14px] shrink-0 bg-desa-foreshadow rounded-2xl">
                            <img src="@/assets/images/icons/money-dark-green.svg" alt="icon" class="size-6 shrink-0" />
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="font-semibold text-lg leading-[22.5px] text-desa-dark-green">Rp {{ formatRupiah(item.amount) }}</p>
                            <h3 class="font-medium text-sm leading-[17.5px] text-desa-secondary">Nominal</h3>
                        </div>
                    </div>
                    <div class="point flex items-center gap-3">
                        <div class="p-[14px] shrink-0 bg-desa-foreshadow rounded-2xl">
                            <img src="@/assets/images/icons/bag-2-dark-green.svg" alt="icon" class="size-6 shrink-0" />
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="font-semibold text-lg leading-[22.5px] text-desa-dark-green">{{ item.category }}</p>
                            <h3 class="font-medium text-sm leading-[17.5px] text-desa-secondary">Kategori</h3>
                        </div>
                    </div>
                    <div class="point flex items-center gap-3" v-if="item.is_available">
                        <div class="p-[14px] shrink-0 bg-desa-foreshadow rounded-2xl">
                            <img src="@/assets/images/icons/tick-square-dark-green.svg" alt="icon" class="size-6 shrink-0" />
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="font-semibold text-lg leading-[22.5px] text-desa-dark-green">Tersedia</p>
                            <h3 class="font-medium text-sm leading-[17.5px] text-desa-secondary">Status</h3>
                        </div>
                    </div>
                    <div class="point flex items-center gap-3" v-else>
                        <div class="p-[14px] shrink-0 bg-[#FF507017] rounded-2xl">
                            <img src="@/assets/images/icons/minus-square-red.svg" alt="icon" class="size-6 shrink-0" />
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="font-semibold text-lg leading-[22.5px] text-desa-red">Habis</p>
                            <h3 class="font-medium text-sm leading-[17.5px] text-desa-secondary">Status</h3>
                        </div>
                    </div>
                </section>
            </div>
        </section>
    </div>
</template>
