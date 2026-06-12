<script setup>
import { ref, onMounted } from 'vue';
import client from '../../api/client';
import { formatRupiah, formatToClientTimezone } from '../../helpers/format';

const items = ref([]);
const saving = ref(false);

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

const recipientStatusClass = (status) => {
    const map = {
        pending: 'bg-desa-yellow',
        approved: 'bg-desa-soft-green',
        rejected: 'bg-desa-red',
    };
    return map[status] ?? 'bg-desa-grey';
};

async function loadRecipients() {
    const res = await client.get('/village-staff/social-assistances').catch(() => ({ data: { data: [] } }));
    const assistances = res.data.data ?? [];

    const allRecipients = [];
    for (const sa of assistances) {
        const recRes = await client.get(`/village-staff/social-assistances/${sa.id}/recipients`)
            .catch(() => ({ data: { data: [] } }));
        const recipients = recRes.data.data ?? [];
        allRecipients.push(...recipients.map((r) => ({
            ...r,
            social_assistance: sa,
            _assistance_id: sa.id,
        })));
    }
    items.value = allRecipients;
}

async function updateStatus(item, status) {
    const label = status === 'approved' ? 'Terima' : 'Tolak';
    if (!confirm(`${label} pengajuan dari ${item.head_of_family?.full_name ?? 'warga'}?`)) return;
    saving.value = true;
    try {
        const res = await client.put(
            `/village-staff/social-assistances/${item._assistance_id}/recipients/${item.id}`,
            { status }
        );
        const idx = items.value.findIndex(i => i.id === item.id);
        if (idx >= 0) {
            items.value[idx] = { ...res.data.data, social_assistance: items.value[idx].social_assistance, _assistance_id: item._assistance_id };
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
                    <span class="badge rounded-full px-4 py-2 text-xs font-semibold text-white uppercase" :class="recipientStatusClass(item.status)">
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
    </div>
</template>
