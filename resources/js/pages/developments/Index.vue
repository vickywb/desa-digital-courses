<script setup>
import { ref, onMounted } from 'vue';
import client from '../../api/client';

const items = ref([]);

const statusClass = (status) => {
    const map = {
        planned: 'bg-desa-yellow',
        ongoing: 'bg-desa-soft-green',
        completed: 'bg-desa-dark-green',
    };
    return map[status] ?? 'bg-desa-grey';
};

onMounted(async () => {
    try {
        const res = await client.get('/village-staff/developments');
        items.value = res.data.data ?? [];
    } catch {
        items.value = [];
    }
});
</script>

<template>
    <div class="flex flex-col gap-[14px]">
        <h1 class="font-semibold text-2xl">Pembangunan Desa</h1>

        <div class="rounded-3xl bg-white p-6">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-desa-background">
                            <th class="px-4 py-4 font-medium text-desa-secondary text-sm">Judul</th>
                            <th class="px-4 py-4 font-medium text-desa-secondary text-sm">PIC</th>
                            <th class="px-4 py-4 font-medium text-desa-secondary text-sm">Anggaran</th>
                            <th class="px-4 py-4 font-medium text-desa-secondary text-sm">Mulai</th>
                            <th class="px-4 py-4 font-medium text-desa-secondary text-sm">Status</th>
                            <th class="px-4 py-4 font-medium text-desa-secondary text-sm">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in items" :key="item.id" class="border-b border-desa-foreshadow last:border-0">
                            <td class="px-4 py-4 font-semibold leading-5">{{ item.title }}</td>
                            <td class="px-4 py-4 text-desa-secondary">{{ item.person_in_charge }}</td>
                            <td class="px-4 py-4 text-desa-secondary">Rp {{ Number(item.amount ?? 0).toLocaleString('id-ID') }}</td>
                            <td class="px-4 py-4 text-desa-secondary">{{ item.start_date ? new Date(item.start_date).toLocaleDateString('id-ID') : '-' }}</td>
                            <td class="px-4 py-4">
                                <span class="rounded-full px-3 py-1 text-xs font-semibold text-white" :class="statusClass(item.status)">
                                    {{ item.status }}
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                <router-link :to="`/developments/${item.id}`"
                                    class="rounded-2xl px-6 py-[14px] bg-desa-black font-medium leading-5 text-white text-sm inline-block">
                                    Detail
                                </router-link>
                            </td>
                        </tr>
                        <tr v-if="!items.length">
                            <td colspan="6" class="px-4 py-12 text-center text-desa-secondary font-medium">Belum ada pembangunan desa</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
