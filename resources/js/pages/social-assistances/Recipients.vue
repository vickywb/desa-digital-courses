<script setup>
import { ref, onMounted } from 'vue';
import client from '../../api/client';
import { formatRupiah, formatToClientTimezone } from '../../helpers/format';

const items = ref([]);
const meta = ref(null);
const currentPage = ref(1);
const saving = ref(false);
const loading = ref(false);

const statusClass = (status) => {
    const map = {
        pending: 'bg-desa-yellow',
        approved: 'bg-desa-soft-green',
        rejected: 'bg-desa-red',
    };
    return map[status] ?? 'bg-desa-grey';
};

const statusLabel = (status) => {
    const map = {
        pending: 'Menunggu',
        approved: 'Disetujui',
        rejected: 'Ditolak',
    };
    return map[status] ?? status;
};

async function loadRecipients(page = 1) {
    loading.value = true;
    const res = await client.get(`/village-staff/social-assistances/recipients/all?page=${page}`)
        .catch(() => ({ data: { data: [], meta: null } }));
    items.value = res.data.data ?? [];
    meta.value = res.data.meta ?? null;
    currentPage.value = page;
    loading.value = false;
}

async function updateStatus(item, status) {
    const label = status === 'approved' ? 'Terima' : 'Tolak';
    if (!confirm(`${label} pengajuan dari ${item.head_of_family?.full_name ?? 'warga'}?`)) return;
    saving.value = true;
    try {
        const res = await client.put(
            `/village-staff/social-assistances/${item.social_assistance_id}/recipients/${item.id}`,
            { status }
        );
        const idx = items.value.findIndex(i => i.id === item.id);
        if (idx >= 0) {
            items.value[idx] = res.data.data;
        }
    } catch (err) {
        alert(err.response?.data?.message ?? 'Gagal memperbarui');
    } finally {
        saving.value = false;
    }
}

onMounted(loadRecipients);
</script>

<template>
    <div class="flex flex-col gap-[14px]">
        <h1 class="font-semibold text-2xl">Pengajuan Bantuan Sosial</h1>

        <div v-if="!items.length" class="flex flex-col items-center justify-center py-20 gap-6">
            <img src="/desa-digital/src/assets/images/icons/bag-cross-secondary.svg" class="size-[52px]" alt="icon">
            <p class="font-medium text-desa-secondary">Belum ada pengajuan bansos</p>
        </div>

        <div v-if="loading" class="flex items-center justify-center py-20">
            <p class="font-medium text-desa-secondary">Memuat...</p>
        </div>

        <template v-else>
        <div v-for="item in items" :key="item.id" class="card flex flex-col gap-4 rounded-3xl p-6 bg-white">
            <div class="flex items-center justify-between">
                <p class="flex items-center gap-1">
                    <img src="/desa-digital/src/assets/images/icons/calendar-2-secondary-green.svg" class="flex size-[18px] shrink-0" alt="icon">
                    <span class="font-medium text-sm text-desa-secondary">{{ formatToClientTimezone(item.created_at) }}</span>
                </p>
                <span class="badge rounded-full px-4 py-2 text-xs font-semibold text-white uppercase" :class="statusClass(item.status)">
                    {{ statusLabel(item.status) }}
                </span>
            </div>
            <hr class="border-desa-background">
            <div class="flex items-center w-full">
                <div class="flex w-[100px] h-20 shrink-0 rounded-2xl overflow-hidden bg-desa-foreshadow">
                    <img :src="item.social_assistance?.social_assistance_file?.url" class="w-full h-full object-cover" alt="photo">
                </div>
                <div class="flex flex-col gap-[6px] w-full ml-4 mr-9">
                    <p class="font-semibold text-lg leading-[22.5px] line-clamp-1">{{ item.social_assistance?.title }}</p>
                    <p class="flex items-center gap-1">
                        <img src="/desa-digital/src/assets/images/icons/profile-secondary-green.svg" class="flex size-[18px] shrink-0" alt="icon">
                        <span class="font-medium text-sm text-desa-secondary">{{ item.social_assistance?.provider }}</span>
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="flex flex-col gap-1 text-right">
                        <p class="font-semibold text-lg leading-5 text-desa-dark-green text-nowrap">Rp {{ formatRupiah(item.social_assistance?.amount) }}</p>
                        <p class="font-medium text-sm text-desa-secondary">Uang Tunai</p>
                    </div>
                    <div class="flex size-[52px] rounded-2xl items-center justify-center bg-desa-foreshadow">
                        <img src="/desa-digital/src/assets/images/icons/money-dark-green.svg" class="flex size-6 shrink-0" alt="icon">
                    </div>
                </div>
            </div>
            <hr class="border-desa-background">
            <div class="flex items-center gap-6 justify-between">
                <div class="flex items-center gap-3 w-[302px] shrink-0">
                    <div class="flex size-[54px] rounded-full bg-desa-foreshadow overflow-hidden">
                        <img :src="item.head_of_family?.profile_picture" class="w-full h-full object-cover" alt="icon">
                    </div>
                    <div class="flex flex-col gap-1">
                        <p class="font-semibold text-lg leading-5 text-desa-black">{{ item.head_of_family?.full_name }}</p>
                        <p class="flex items-center gap-1">
                            <img src="/desa-digital/src/assets/images/icons/briefcase-secondary-green.svg" class="flex size-[18px] shrink-0" alt="icon">
                            <span class="font-medium text-sm text-desa-secondary">{{ item.head_of_family?.occupation }}</span>
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-3 w-[302px] shrink-0">
                    <div class="flex size-[52px] rounded-2xl items-center justify-center bg-desa-foreshadow">
                        <img src="/desa-digital/src/assets/images/icons/receive-square-2-dark-green.svg" class="flex size-6 shrink-0" alt="icon">
                    </div>
                    <div class="flex flex-col gap-1">
                        <p class="font-semibold text-lg leading-5 text-desa-dark-green text-nowrap">Rp {{ formatRupiah(item.amount) }}</p>
                        <p class="font-medium text-sm text-desa-secondary">Nominal Pengajuan</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 justify-end shrink-0">
                    <span class="badge rounded-full px-4 py-2 text-xs font-semibold text-white uppercase" :class="statusClass(item.status)">
                        {{ statusLabel(item.status) }}
                    </span>

                    <template v-if="item.status === 'pending'">
                        <button @click="updateStatus(item, 'approved')" :disabled="saving"
                            class="rounded-lg px-3 py-1.5 bg-desa-dark-green text-white text-xs font-semibold hover:bg-desa-black transition-setup">
                            Terima
                        </button>
                        <button @click="updateStatus(item, 'rejected')" :disabled="saving"
                            class="rounded-lg px-3 py-1.5 bg-desa-red text-white text-xs font-semibold hover:bg-desa-black transition-setup">
                            Tolak
                        </button>
                    </template>
                </div>
            </div>
        </div>

        <div v-if="meta" class="flex items-center justify-center gap-4 mt-4">
            <button @click="loadRecipients(currentPage - 1)" :disabled="currentPage <= 1 || loading"
                class="rounded-2xl px-6 py-3 border border-desa-background font-medium text-sm hover:bg-desa-black hover:text-white transition-setup disabled:opacity-40 disabled:cursor-not-allowed">
                Sebelumnya
            </button>
            <span class="font-medium text-sm text-desa-secondary">
                Halaman {{ meta.current_page }} dari {{ meta.last_page }}
            </span>
            <button @click="loadRecipients(currentPage + 1)" :disabled="currentPage >= meta.last_page || loading"
                class="rounded-2xl px-6 py-3 border border-desa-background font-medium text-sm hover:bg-desa-black hover:text-white transition-setup disabled:opacity-40 disabled:cursor-not-allowed">
                Selanjutnya
            </button>
        </div>
        </template>
    </div>
</template>
