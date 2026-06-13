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

            <router-link v-for="item in items" :key="item.id" :to="`/warga/bansos/detail/${item.id}`"
                class="bansos flex flex-col gap-3 sm:gap-4 p-4 sm:p-6 bg-white rounded-3xl hover:shadow-md transition-setup">
                <div class="flex items-center justify-between gap-2">
                    <div class="min-w-0 flex-1">
                        <h2 class="font-semibold text-sm sm:text-lg leading-5 sm:leading-[22.5px] line-clamp-1">{{ item.title }}</h2>
                        <p class="hidden sm:flex items-center gap-1 mt-1">
                            <img src="@/assets/images/icons/profile-secondary-green-bold.svg" class="size-4 shrink-0" alt="icon" />
                            <span class="font-medium text-xs sm:text-sm text-desa-secondary truncate">{{ item.provider }}</span>
                        </p>
                    </div>
                    <span class="rounded-full px-3 py-1.5 text-[10px] sm:text-xs font-semibold shrink-0"
                        :class="item.is_available ? 'bg-desa-soft-green text-white' : 'bg-desa-red text-white'">
                        {{ item.is_available ? 'Tersedia' : 'Habis' }}
                    </span>
                </div>
                <div class="flex sm:hidden items-center gap-2">
                    <p class="font-semibold text-xs text-desa-dark-green">Rp {{ formatRupiah(item.amount) }}</p>
                    <span class="text-[10px] text-desa-secondary">•</span>
                    <p class="text-xs text-desa-secondary truncate">{{ item.category }}</p>
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
                        <div class="point flex items-center gap-3">
                            <div class="p-[14px] shrink-0 bg-desa-foreshadow rounded-2xl">
                                <img :src="item.is_available ? '/desa-digital/src/assets/images/icons/tick-square-dark-green.svg' : '/desa-digital/src/assets/images/icons/minus-square-red.svg'" alt="icon" class="size-6 shrink-0" />
                            </div>
                            <div class="flex flex-col gap-1">
                                <p class="font-semibold text-lg leading-[22.5px]" :class="item.is_available ? 'text-desa-dark-green' : 'text-desa-red'">
                                    {{ item.is_available ? 'Tersedia' : 'Habis' }}
                                </p>
                                <h3 class="font-medium text-sm leading-[17.5px] text-desa-secondary">Status</h3>
                            </div>
                        </div>
                    </section>
                </div>
            </router-link>
        </section>
    </div>
</template>
