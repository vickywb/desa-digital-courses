<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import client from '../../api/client';

const route = useRoute();
const router = useRouter();
const item = ref(null);
const loading = ref(true);

const relationLabel = (rel) => {
    const map = { wife: 'Istri', child: 'Anak', head: 'Kepala Keluarga' };
    return map[rel] ?? rel;
};

onMounted(async () => {
    try {
        const res = await client.get(`/village-staff/head-families/${route.params.headId}/members/${route.params.memberId}`);
        item.value = res.data.data ?? null;
    } catch {
        item.value = null;
    } finally {
        loading.value = false;
    }
});

async function deleteMember() {
    if (!confirm(`Yakin ingin menghapus ${item.value.full_name}?`)) return;
    try {
        await client.delete(`/village-staff/head-families/${route.params.headId}/members/${route.params.memberId}`);
        router.push(`/staff/head-families/${route.params.headId}/members`);
    } catch {
        alert('Gagal menghapus anggota keluarga');
    }
}
</script>

<template>
    <div class="flex flex-col gap-[14px]">
        <div class="flex items-center gap-2">
            <router-link :to="`/staff/head-families/${route.params.headId}/members`"
                class="flex items-center gap-1 font-medium text-desa-dark-green hover:underline">
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali
            </router-link>
            <span class="text-desa-secondary">/</span>
            <span class="font-medium text-desa-secondary">Detail Anggota</span>
        </div>

        <div v-if="loading" class="flex items-center justify-center py-20">
            <p class="font-medium text-desa-secondary">Memuat...</p>
        </div>

        <div v-else-if="!item" class="flex flex-col items-center justify-center py-20 gap-4">
            <p class="font-semibold text-lg text-desa-secondary">Data tidak ditemukan</p>
            <router-link :to="`/staff/head-families/${route.params.headId}/members`"
                class="text-desa-dark-green hover:underline font-medium">Kembali</router-link>
        </div>

        <div v-else class="flex gap-[14px] flex-col lg:flex-row">
            <div class="w-full lg:w-[400px] shrink-0 rounded-2xl bg-white p-4 sm:p-6 flex flex-col gap-6 h-fit">
                <div class="flex flex-col items-center gap-4">
                    <div class="flex size-24 sm:size-28 rounded-full overflow-hidden bg-desa-foreshadow">
                        <img :src="item.family_member_file?.url ?? '/assets/images/no-image.png'"
                            class="w-full h-full object-cover" alt="photo" />
                    </div>
                    <div class="text-center">
                        <h2 class="font-semibold text-xl leading-6">{{ item.full_name }}</h2>
                        <p class="font-medium text-sm text-desa-secondary mt-1">{{ relationLabel(item.relation) }}</p>
                    </div>
                </div>

                <div class="flex gap-3">
                    <router-link :to="`/staff/head-families/${route.params.headId}/members`"
                        class="flex items-center justify-center h-12 rounded-2xl px-6 border border-desa-background font-semibold text-sm hover:bg-desa-black hover:text-white transition-setup flex-1">
                        Kembali
                    </router-link>
                    <button @click="deleteMember"
                        class="flex items-center justify-center h-12 rounded-2xl px-6 bg-desa-red text-white font-semibold text-sm hover:bg-desa-black transition-setup flex-1">
                        Hapus
                    </button>
                </div>
            </div>

            <div class="flex-1 flex flex-col gap-[14px]">
                <div class="rounded-2xl bg-white p-4 sm:p-6 flex flex-col gap-4 sm:gap-6">
                    <h2 class="font-semibold leading-5">Informasi Pribadi</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="flex flex-col gap-1">
                            <p class="font-medium text-xs text-desa-secondary">NIK</p>
                            <p class="font-semibold text-sm leading-5">{{ item.identity_number }}</p>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="font-medium text-xs text-desa-secondary">Jenis Kelamin</p>
                            <p class="font-semibold text-sm leading-5 capitalize">{{ item.gender ?? '-' }}</p>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="font-medium text-xs text-desa-secondary">Tanggal Lahir</p>
                            <p class="font-semibold text-sm leading-5">{{ item.date_of_birth ? new Date(item.date_of_birth).toLocaleDateString('id-ID') : '-' }}</p>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="font-medium text-xs text-desa-secondary">Usia</p>
                            <p class="font-semibold text-sm leading-5">{{ item.date_of_birth ? new Date().getFullYear() - new Date(item.date_of_birth).getFullYear() : '-' }} tahun</p>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="font-medium text-xs text-desa-secondary">Pekerjaan</p>
                            <p class="font-semibold text-sm leading-5">{{ item.occupation ?? '-' }}</p>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="font-medium text-xs text-desa-secondary">Status Pernikahan</p>
                            <p class="font-semibold text-sm leading-5 capitalize">{{ item.marital_status ?? '-' }}</p>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="font-medium text-xs text-desa-secondary">No. HP</p>
                            <p class="font-semibold text-sm leading-5">{{ item.phone_number ?? '-' }}</p>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="font-medium text-xs text-desa-secondary">Email</p>
                            <p class="font-semibold text-sm leading-5">{{ item.email ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
