<script setup>
import { ref, onMounted } from 'vue';
import client from '../../api/client';
import { formatRupiah, formatToClientTimezone } from '../../helpers/format';

const items = ref([]);

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

async function cancelRecipient(id) {
    if (!confirm('Yakin ingin membatalkan pengajuan ini?')) return;

    try {
        const item = items.value.find(i => i.id === id);
        if (!item) return;

        await client.delete(`/village-resident/social-assistances/${item.social_assistance_id}/recipients/${id}`);
        items.value = items.value.filter(i => i.id !== id);
    } catch {
        alert('Gagal membatalkan pengajuan');
    }
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
                <img src="/desa-digital/src/assets/images/icons/bag-cross-secondary.svg" class="size-[52px]" alt="icon">
                <p class="font-medium text-desa-secondary">Belum ada pengajuan bansos</p>
                <router-link to="/warga/bansos"
                    class="rounded-2xl bg-desa-black py-[18px] px-6 font-medium leading-5 text-white inline-block">
                    Ajukan Bansos
                </router-link>
            </div>

            <div v-for="item in items" :key="item.id"
                class="bansos flex flex-col gap-4 p-6 bg-white rounded-3xl">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-[2px]">
                        <img src="@/assets/images/icons/calendar-2-secondary-green.svg" class="flex size-[18px] shrink-0" alt="icon" />
                        <p class="font-medium text-sm leading-[17.5px] text-desa-secondary">{{ formatToClientTimezone(item.created_at) }}</p>
                    </div>
                    <span class="rounded-full py-[12px] w-[100px] flex justify-center text-white font-semibold text-xs leading-[15px] shrink-0"
                        :class="statusClass(item.status)">
                        {{ statusLabel(item.status) }}
                    </span>
                </div>
                <hr class="border-desa-background" />
                <header class="flex items-center gap-[48px] justify-between">
                    <div class="flex items-center gap-4 min-w-0">
                        <div class="flex justify-center items-center w-[100px] h-[80px] shrink-0 rounded-2xl overflow-hidden bg-desa-foreshadow">
                            <img :src="item.social_assistance?.social_assistance_file?.url" alt="image" class="size-full object-cover" />
                        </div>
                        <div class="title flex flex-col gap-[6px] min-w-0">
                            <h2 class="font-semibold text-lg leading-[22.5px] line-clamp-1">{{ item.social_assistance?.title }}</h2>
                            <div class="flex items-center gap-1">
                                <img src="@/assets/images/icons/profile-secondary-green-bold.svg" class="size-4 shrink-0" alt="icon" />
                                <p class="font-medium text-sm leading-[17.5px] text-desa-secondary truncate">{{ item.social_assistance?.provider }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 shrink-0">
                        <router-link :to="`/warga/bansos/pengajuan-saya/${item.id}`">
                            <div class="rounded-2xl bg-desa-black py-[18px] px-6 font-medium leading-5 text-white">Detail</div>
                        </router-link>
                        <button v-if="item.status === 'pending'" @click="cancelRecipient(item.id)"
                            class="rounded-2xl border border-desa-red py-[18px] px-6 font-medium leading-5 text-desa-red hover:bg-desa-red hover:text-white transition-setup">
                            Batalkan
                        </button>
                    </div>
                </header>
                <hr class="border-desa-background" />
                <section class="points grid grid-cols-3 gap-4">
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
                            <img src="@/assets/images/icons/bank-secondary-green.svg" alt="icon" class="size-6 shrink-0" />
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="font-semibold text-lg leading-[22.5px] text-desa-dark-green">{{ item.bank }}</p>
                            <h3 class="font-medium text-sm leading-[17.5px] text-desa-secondary">Bank</h3>
                        </div>
                    </div>
                </section>
            </div>
        </section>
    </div>
</template>
