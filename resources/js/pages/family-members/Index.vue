<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import client from '../../api/client';

const route = useRoute();
const items = ref([]);
const headFamilyName = ref('');

onMounted(async () => {
    try {
        const [res, hfRes] = await Promise.all([
            client.get(`/village-staff/head-families/${route.params.id}/members`),
            client.get(`/village-staff/head-families/${route.params.id}`),
        ]);
        items.value = res.data.data ?? [];
        headFamilyName.value = hfRes.data.data?.full_name ?? '';
    } catch {
        items.value = [];
    }
});
</script>

<template>
    <div class="flex flex-col gap-[14px]">
        <div class="flex items-center gap-2">
            <router-link to="/head-families" class="flex items-center gap-1 font-medium text-desa-dark-green hover:underline">
                <img src="/desa-digital/src/assets/images/icons/arrow-left-dark-green.svg" class="size-5" alt="back">
                Kembali
            </router-link>
            <span class="text-desa-secondary">/</span>
            <span class="font-medium text-desa-secondary">Anggota Keluarga {{ headFamilyName ? '- ' + headFamilyName : '' }}</span>
        </div>

        <div class="rounded-3xl bg-white p-6">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-desa-background">
                            <th class="px-4 py-4 font-medium text-desa-secondary text-sm">Nama</th>
                            <th class="px-4 py-4 font-medium text-desa-secondary text-sm">NIK</th>
                            <th class="px-4 py-4 font-medium text-desa-secondary text-sm">Hubungan</th>
                            <th class="px-4 py-4 font-medium text-desa-secondary text-sm">Gender</th>
                            <th class="px-4 py-4 font-medium text-desa-secondary text-sm">Pekerjaan</th>
                            <th class="px-4 py-4 font-medium text-desa-secondary text-sm">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in items" :key="item.id" class="border-b border-desa-foreshadow last:border-0">
                            <td class="px-4 py-4 font-semibold leading-5">{{ item.full_name }}</td>
                            <td class="px-4 py-4 text-desa-secondary">{{ item.identity_number }}</td>
                            <td class="px-4 py-4">{{ item.relation }}</td>
                            <td class="px-4 py-4">{{ item.gender }}</td>
                            <td class="px-4 py-4 text-desa-secondary">{{ item.occupation }}</td>
                            <td class="px-4 py-4">
                                <button class="rounded-2xl px-6 py-[14px] bg-desa-black font-medium leading-5 text-white text-sm">Manage</button>
                            </td>
                        </tr>
                        <tr v-if="!items.length">
                            <td colspan="6" class="px-4 py-12 text-center text-desa-secondary font-medium">Belum ada anggota keluarga</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
