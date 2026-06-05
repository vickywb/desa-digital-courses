<script setup>
import { ref, onMounted } from 'vue';
import client from '../../api/client';

const items = ref([]);

onMounted(async () => {
    try {
        const res = await client.get('/village-staff/head-families');
        items.value = res.data.data ?? [];
    } catch {
        items.value = [];
    }
});
</script>

<template>
    <div class="flex flex-col gap-[14px]">
        <h1 class="font-semibold text-2xl">Kepala Rumah</h1>

        <div class="rounded-3xl bg-white p-6">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-desa-background">
                            <th class="px-4 py-4 font-medium text-desa-secondary text-sm">Nama</th>
                            <th class="px-4 py-4 font-medium text-desa-secondary text-sm">NIK</th>
                            <th class="px-4 py-4 font-medium text-desa-secondary text-sm">Gender</th>
                            <th class="px-4 py-4 font-medium text-desa-secondary text-sm">Pekerjaan</th>
                            <th class="px-4 py-4 font-medium text-desa-secondary text-sm">Status</th>
                            <th class="px-4 py-4 font-medium text-desa-secondary text-sm">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in items" :key="item.id" class="border-b border-desa-foreshadow last:border-0">
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex size-10 rounded-full overflow-hidden bg-desa-foreshadow shrink-0">
                                        <img :src="item.profile_picture" class="w-full h-full object-cover" alt="photo">
                                    </div>
                                    <span class="font-semibold leading-5">{{ item.full_name }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-desa-secondary">{{ item.identity_number }}</td>
                            <td class="px-4 py-4">{{ item.gender }}</td>
                            <td class="px-4 py-4 text-desa-secondary">{{ item.occupation }}</td>
                            <td class="px-4 py-4">{{ item.marital_status }}</td>
                            <td class="px-4 py-4">
                                <router-link :to="`/head-families/${item.id}/members`"
                                    class="rounded-2xl px-6 py-[14px] bg-desa-black font-medium leading-5 text-white text-sm inline-block">
                                    Manage
                                </router-link>
                            </td>
                        </tr>
                        <tr v-if="!items.length">
                            <td colspan="6" class="px-4 py-12 text-center text-desa-secondary font-medium">Belum ada data kepala rumah</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
