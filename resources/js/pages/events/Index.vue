<script setup>
import { ref, onMounted } from 'vue';
import client from '../../api/client';

const items = ref([]);

onMounted(async () => {
    try {
        const res = await client.get('/village-staff/events');
        items.value = res.data.data ?? [];
    } catch {
        items.value = [];
    }
});
</script>

<template>
    <div class="flex flex-col gap-[14px]">
        <h1 class="font-semibold text-2xl">Event Desa</h1>

        <div class="rounded-3xl bg-white p-6">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-desa-background">
                            <th class="px-4 py-4 font-medium text-desa-secondary text-sm">Judul</th>
                            <th class="px-4 py-4 font-medium text-desa-secondary text-sm">Harga</th>
                            <th class="px-4 py-4 font-medium text-desa-secondary text-sm">Mulai</th>
                            <th class="px-4 py-4 font-medium text-desa-secondary text-sm">Pendaftaran</th>
                            <th class="px-4 py-4 font-medium text-desa-secondary text-sm">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in items" :key="item.id" class="border-b border-desa-foreshadow last:border-0">
                            <td class="px-4 py-4 font-semibold leading-5">{{ item.title }}</td>
                            <td class="px-4 py-4 text-desa-secondary">{{ item.price ? `Rp ${Number(item.price).toLocaleString('id-ID')}` : 'Gratis' }}</td>
                            <td class="px-4 py-4 text-desa-secondary">{{ item.start_date ? new Date(item.start_date).toLocaleDateString('id-ID') : '-' }}</td>
                            <td class="px-4 py-4">
                                <span class="rounded-full px-3 py-1 text-xs font-semibold text-white"
                                :class="item.is_active ? 'bg-desa-soft-green' : 'bg-desa-red'">
                                {{ item.is_active ? 'Open' : 'Closed' }}
                            </span>
                        </td>
                        <td class="px-4 py-4">
                            <router-link :to="`/events/${item.id}`"
                                class="rounded-2xl px-6 py-[14px] bg-desa-black font-medium leading-5 text-white text-sm inline-block">
                                Detail
                            </router-link>
                        </td>
                    </tr>
                    <tr v-if="!items.length">
                        <td colspan="5" class="px-4 py-12 text-center text-desa-secondary font-medium">Belum ada event desa</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
</template>
