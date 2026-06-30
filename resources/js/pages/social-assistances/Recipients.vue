<script setup>
import { ref, onMounted } from 'vue';
import client from '../../api/client';
import { formatRupiah, formatToClientTimezone } from '../../helpers/format';
import ConfirmModal from '@/components/ui/ConfirmModal.vue';

const items = ref([]);
const meta = ref(null);
const currentPage = ref(1);
const saving = ref(false);
const loading = ref(false);

const confirmState = ref({ show: false, title: '', message: '', variant: 'primary', onConfirm: null });

function openConfirm(title, message, variant, onConfirm) {
    confirmState.value = { show: true, title, message, variant, onConfirm };
}

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

function updateStatus(item, status) {
    const label = status === 'approved' ? 'Terima' : 'Tolak';
    const variant = status === 'approved' ? 'primary' : 'danger';
    openConfirm(
        `${label} Pengajuan`,
        `${label} pengajuan dari ${item.head_of_family?.full_name ?? 'warga'}?`,
        variant,
        async () => {
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
    );
}

onMounted(loadRecipients);
</script>

<template>
    <div class="flex flex-col gap-[14px]">
        <h1 class="font-semibold text-2xl">Pengajuan Bantuan Sosial</h1>

        <div v-if="!items.length" class="flex flex-col items-center justify-center py-20 gap-6">
            <img src="/assets/images/icons/bag-cross-secondary.svg" class="size-[52px]" alt="icon">
            <p class="font-medium text-desa-secondary">Belum ada pengajuan bansos</p>
        </div>

        <div v-if="loading" class="flex items-center justify-center py-20">
            <p class="font-medium text-desa-secondary">Memuat...</p>
        </div>

        <template v-else>
        <div v-for="item in items" :key="item.id" class="card flex flex-col gap-3 sm:gap-4 rounded-3xl p-4 sm:p-6 bg-white">
            <div class="flex items-start sm:items-center justify-between gap-2">
                <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-3 min-w-0 flex-1">
                    <p class="font-semibold text-sm sm:text-lg leading-5 sm:leading-[22.5px] line-clamp-1">{{ item.social_assistance?.title }}</p>
                    <p class="hidden sm:flex items-center gap-1 shrink-0">
                        <img src="/assets/images/icons/calendar-2-secondary-green.svg" class="flex size-[18px] shrink-0" alt="icon">
                        <span class="font-medium text-xs sm:text-sm text-desa-secondary whitespace-nowrap">{{ formatToClientTimezone(item.created_at) }}</span>
                    </p>
                </div>
                <span class="badge rounded-full px-3 sm:px-4 py-1.5 sm:py-2 text-[10px] sm:text-xs font-semibold text-white uppercase shrink-0" :class="statusClass(item.status)">
                    {{ statusLabel(item.status) }}
                </span>
            </div>

            <div class="flex sm:hidden items-center justify-between gap-2">
                <p class="font-medium text-xs text-desa-secondary truncate">{{ item.head_of_family?.full_name }}</p>
                <p class="font-semibold text-xs text-desa-dark-green shrink-0">Rp {{ formatRupiah(item.amount) }}</p>
            </div>

            <div class="hidden sm:flex flex-col gap-0">
                <hr class="border-desa-background">
                <div class="flex items-center w-full gap-3 py-3">
                    <div class="flex w-[100px] h-20 shrink-0 rounded-2xl overflow-hidden bg-desa-foreshadow">
                        <img :src="item.social_assistance?.social_assistance_file?.url" class="w-full h-full object-cover" alt="photo">
                    </div>
                    <div class="flex flex-col gap-[6px] min-w-0 flex-1">
                        <p class="font-semibold text-lg leading-[22.5px] line-clamp-1">{{ item.social_assistance?.title }}</p>
                        <p class="flex items-center gap-1">
                            <img src="/assets/images/icons/profile-secondary-green.svg" class="flex size-[18px] shrink-0" alt="icon">
                            <span class="font-medium text-sm text-desa-secondary truncate">{{ item.social_assistance?.provider }}</span>
                        </p>
                    </div>
                    <div class="flex items-center gap-3 shrink-0">
                        <div class="flex flex-col gap-1 text-right">
                            <p class="font-semibold text-lg leading-5 text-desa-dark-green">Rp {{ formatRupiah(item.social_assistance?.amount) }}</p>
                            <p class="font-medium text-sm text-desa-secondary">Uang Tunai</p>
                        </div>
                        <div class="flex size-[52px] rounded-2xl items-center justify-center bg-desa-foreshadow shrink-0">
                            <img src="/assets/images/icons/money-dark-green.svg" class="flex size-6 shrink-0" alt="icon">
                        </div>
                    </div>
                </div>
                <hr class="border-desa-background">
                <div class="flex items-center gap-6 justify-between py-3">
                    <div class="flex items-center gap-3 shrink-0">
                        <div class="flex size-[54px] rounded-full bg-desa-foreshadow overflow-hidden shrink-0">
                            <img :src="item.head_of_family?.profile_picture" class="w-full h-full object-cover" alt="icon">
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="font-semibold text-lg leading-5 text-desa-black truncate">{{ item.head_of_family?.full_name }}</p>
                            <p class="flex items-center gap-1">
                                <img src="/assets/images/icons/briefcase-secondary-green.svg" class="flex size-[18px] shrink-0" alt="icon">
                                <span class="font-medium text-sm text-desa-secondary truncate">{{ item.head_of_family?.occupation }}</span>
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 shrink-0">
                        <div class="flex size-[52px] rounded-2xl items-center justify-center bg-desa-foreshadow shrink-0">
                            <img src="/assets/images/icons/receive-square-2-dark-green.svg" class="flex size-6 shrink-0" alt="icon">
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="font-semibold text-lg leading-5 text-desa-dark-green truncate">Rp {{ formatRupiah(item.amount) }}</p>
                            <p class="font-medium text-sm text-desa-secondary">Nominal Pengajuan</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 justify-end shrink-0">
                        <template v-if="item.status === 'pending'">
                            <button @click="updateStatus(item, 'approved')" :disabled="saving"
                                class="rounded-lg px-3 py-1.5 bg-desa-dark-green text-white text-xs font-semibold hover:bg-desa-black transition-setup shrink-0">
                                Terima
                            </button>
                            <button @click="updateStatus(item, 'rejected')" :disabled="saving"
                                class="rounded-lg px-3 py-1.5 bg-desa-red text-white text-xs font-semibold hover:bg-desa-black transition-setup shrink-0">
                                Tolak
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            <div v-if="item.status === 'pending'" class="flex sm:hidden items-center gap-2 pt-1">
                <button @click="updateStatus(item, 'approved')" :disabled="saving"
                    class="flex-1 rounded-lg py-2 bg-desa-dark-green text-white text-xs font-semibold hover:bg-desa-black transition-setup">
                    Terima
                </button>
                <button @click="updateStatus(item, 'rejected')" :disabled="saving"
                    class="flex-1 rounded-lg py-2 bg-desa-red text-white text-xs font-semibold hover:bg-desa-black transition-setup">
                    Tolak
                </button>
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

    <ConfirmModal
        :show="confirmState.show"
        :title="confirmState.title"
        :message="confirmState.message"
        :variant="confirmState.variant"
        :loading="saving"
        @close="confirmState.show = false"
        @confirm="confirmState.onConfirm?.()"
    />
</template>
