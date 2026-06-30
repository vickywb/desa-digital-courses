<script setup>
import { ref, onMounted } from 'vue';
import client from '../../api/client';
import { formatRupiah, formatToClientTimezone } from '../../helpers/format';
import ConfirmModal from '@/components/ui/ConfirmModal.vue';

const items = ref([]);
const confirmState = ref({ show: false, title: '', message: '', variant: 'danger', onConfirm: null });

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
        pending: 'MENUNGGU',
        approved: 'DITERIMA',
        rejected: 'DITOLAK',
    };
    return map[status] ?? status;
};

function cancelRecipient(id) {
    const item = items.value.find(i => i.id === id);
    if (!item) return;

    openConfirm(
        'Batalkan Pengajuan',
        'Yakin ingin membatalkan pengajuan ini?',
        'danger',
        async () => {
            try {
                await client.delete(`/village-resident/social-assistances/${item.social_assistance_id}/recipients/${id}`);
                items.value = items.value.filter(i => i.id !== id);
            } catch {
                alert('Gagal membatalkan pengajuan');
            }
        }
    );
}

onMounted(async () => {
    try {
        const res = await client.get('/village-resident/social-assistance-recipients');
        items.value = res.data.data ?? [];
    } catch {
        items.value = [];
    }
});
</script>

<template>
    <div class="flex flex-col gap-[14px]">
        <header>
            <h1 class="font-semibold text-2xl leading-[30px]">Pengajuan Bantuan Sosial</h1>
        </header>

        <section id="List-Pengajuan-Bansos" class="flex flex-col gap-[14px]">
            <div v-if="!items.length" class="flex flex-col items-center justify-center py-20 gap-6">
                <img src="/assets/images/icons/bag-cross-secondary.svg" class="size-[52px]" alt="icon">
                <p class="font-medium text-desa-secondary">Belum ada pengajuan bansos</p>
                <router-link to="/warga/bansos"
                    class="rounded-2xl bg-desa-black py-[18px] px-6 font-medium leading-5 text-white inline-block">
                    Ajukan Bansos
                </router-link>
            </div>

            <router-link v-for="item in items" :key="item.id" :to="`/warga/bansos/pengajuan-saya/${item.id}`"
                class="bansos flex flex-col gap-3 sm:gap-4 p-4 sm:p-6 bg-white rounded-3xl hover:shadow-md transition-setup">
                <div class="flex items-start sm:items-center justify-between gap-2">
                    <div class="min-w-0 flex-1">
                        <h2 class="font-semibold text-sm sm:text-lg leading-5 sm:leading-[22.5px] line-clamp-1">{{ item.social_assistance?.title }}</h2>
                        <p class="hidden sm:flex items-center gap-1 mt-1">
                            <img src="@/assets/images/icons/calendar-2-secondary-green.svg" class="flex size-[18px] shrink-0" alt="icon" />
                            <span class="font-medium text-xs sm:text-sm text-desa-secondary">{{ formatToClientTimezone(item.created_at) }}</span>
                        </p>
                    </div>
                    <span class="rounded-full px-3 sm:px-4 py-1.5 sm:py-[12px] flex justify-center text-white font-semibold text-[10px] sm:text-xs leading-[15px] shrink-0"
                        :class="statusClass(item.status)">
                        {{ statusLabel(item.status) }}
                    </span>
                </div>

                <div class="flex sm:hidden items-center justify-between gap-2">
                    <p class="font-semibold text-xs text-desa-dark-green">Rp {{ formatRupiah(item.amount) }}</p>
                    <button v-if="item.status === 'pending'" @click.stop="cancelRecipient(item.id)"
                        class="rounded-lg px-3 py-1.5 border border-desa-red text-desa-red text-xs font-semibold">
                        Batal
                    </button>
                </div>

                <div class="hidden sm:block">
                    <hr class="border-desa-background" />
                    <header class="flex items-center gap-[48px] justify-between py-3">
                        <div class="flex items-center gap-4 min-w-0">
                            <div class="flex justify-center items-center w-[100px] h-[80px] shrink-0 rounded-2xl overflow-hidden bg-desa-foreshadow">
                                <img :src="item.social_assistance?.social_assistance_file?.url" alt="image" class="size-full object-cover" />
                            </div>
                            <div class="flex flex-col gap-[6px] min-w-0">
                                <h2 class="font-semibold text-lg leading-[22.5px] line-clamp-1">{{ item.social_assistance?.title }}</h2>
                                <div class="flex items-center gap-1">
                                    <img src="@/assets/images/icons/profile-secondary-green-bold.svg" class="size-4 shrink-0" alt="icon" />
                                    <p class="font-medium text-sm leading-[17.5px] text-desa-secondary truncate">{{ item.social_assistance?.provider }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 shrink-0" @click.stop>
                            <button v-if="item.status === 'pending'" @click="cancelRecipient(item.id)"
                                class="rounded-2xl border border-desa-red py-[18px] px-6 font-medium leading-5 text-desa-red hover:bg-desa-red hover:text-white transition-setup">
                                Batalkan
                            </button>
                        </div>
                    </header>
                    <hr class="border-desa-background" />
                    <section class="points grid grid-cols-3 gap-4 pt-3">
                        <div class="point flex items-center gap-3">
                            <div class="p-[14px] shrink-0 bg-desa-foreshadow rounded-2xl">
                                <img src="@/assets/images/icons/money-dark-green.svg" alt="icon" class="size-6 shrink-0" />
                            </div>
                            <div class="flex flex-col gap-1">
                                <p class="font-semibold text-lg leading-[22.5px] text-desa-dark-green">Rp {{ formatRupiah(item.social_assistance?.amount) }}</p>
                                <h3 class="font-medium text-sm leading-[17.5px] text-desa-secondary">{{ item.social_assistance?.category }}</h3>
                            </div>
                        </div>
                        <div class="point flex items-center gap-3">
                            <div class="p-[14px] shrink-0 bg-desa-foreshadow rounded-2xl">
                                <img src="@/assets/images/icons/receive-square-2-dark-green.svg" alt="icon" class="size-6 shrink-0" />
                            </div>
                            <div class="flex flex-col gap-1">
                                <p class="font-semibold text-lg leading-[22.5px] text-desa-dark-green">Rp {{ formatRupiah(item.amount) }}</p>
                                <h3 class="font-medium text-sm leading-[17.5px] text-desa-secondary">Nominal Pengajuan</h3>
                            </div>
                        </div>
                        <div class="point flex items-center gap-3">
                            <div class="p-[14px] shrink-0 bg-desa-foreshadow rounded-2xl">
                                <img :src="`/assets/images/logos/kk-${item.bank.toLowerCase()}.png`" alt="icon" class="size-6 shrink-0" />
                            </div>
                            <div class="flex flex-col gap-1">
                                <p class="font-semibold text-lg leading-[22.5px] text-desa-dark-green">{{ item.bank }}</p>
                                <h3 class="font-medium text-sm leading-[17.5px] text-desa-secondary">Bank</h3>
                            </div>
                        </div>
                    </section>
                </div>
            </router-link>
        </section>
    </div>

    <ConfirmModal
        :show="confirmState.show"
        :title="confirmState.title"
        :message="confirmState.message"
        :variant="confirmState.variant"
        :loading="false"
        @close="confirmState.show = false"
        @confirm="confirmState.onConfirm?.()"
    />
</template>
