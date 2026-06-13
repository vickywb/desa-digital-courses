<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import client from '../../api/client';
import { formatRupiah, formatToClientTimezone } from '../../helpers/format';

const route = useRoute();
const router = useRouter();
const item = ref(null);
const loading = ref(true);

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

onMounted(async () => {
    try {
        const res = await client.get(`/village-resident/social-assistance-recipients/${route.params.id}`);
        item.value = res.data.data ?? null;
    } catch {
        item.value = null;
    } finally {
        loading.value = false;
    }
});
</script>

<template>
    <div class="flex flex-col gap-[14px]">
        <div class="flex items-center gap-2">
            <router-link to="/warga/bansos/pengajuan-saya" class="flex items-center gap-1 font-medium text-desa-dark-green hover:underline">
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali
            </router-link>
            <span class="text-desa-secondary">/</span>
            <span class="font-medium text-desa-secondary">Detail Pengajuan</span>
        </div>

        <div v-if="loading" class="flex items-center justify-center py-20">
            <p class="font-medium text-desa-secondary">Memuat...</p>
        </div>

        <div v-else-if="!item" class="flex flex-col items-center justify-center py-20 gap-4">
            <p class="font-semibold text-lg text-desa-secondary">Data tidak ditemukan</p>
            <router-link to="/warga/bansos/pengajuan-saya" class="text-desa-dark-green hover:underline font-medium">Kembali</router-link>
        </div>

        <div v-else class="flex gap-[14px] flex-col lg:flex-row">
            <div class="w-full lg:w-[545px] shrink-0 rounded-2xl bg-white p-4 sm:p-6 flex flex-col gap-4 sm:gap-6 h-fit">
                <h2 class="font-medium text-sm leading-[17.5px] text-desa-secondary">Informasi Bantuan Sosial</h2>

                <section class="flex items-center justify-between">
                    <div class="flex justify-center items-center w-[120px] h-[100px] shrink-0 rounded-2xl overflow-hidden bg-desa-foreshadow">
                        <img :src="item.social_assistance?.social_assistance_file?.url" alt="image" class="size-full object-cover" />
                    </div>
                    <span class="rounded-full py-[12px] w-[111px] flex justify-center text-white font-semibold text-xs leading-[15px] shrink-0"
                        :class="item.social_assistance?.is_available ? 'bg-desa-soft-green' : 'bg-desa-red'">
                        {{ item.social_assistance?.is_available ? 'TERSEDIA' : 'HABIS' }}
                    </span>
                </section>

                <section class="flex flex-col gap-[6px]">
                    <h3 class="font-semibold text-lg leading-[22.5px]">{{ item.social_assistance?.title }}</h3>
                    <div class="flex items-center gap-1">
                        <img src="@/assets/images/icons/profile-secondary-green-bold.svg" alt="icon" class="size-[18px] shrink-0" />
                        <p class="font-medium text-sm leading-[17.5px] text-desa-secondary">{{ item.social_assistance?.provider }}</p>
                    </div>
                </section>

                <hr class="border-desa-background" />

                <section class="flex flex-col gap-6">
                    <div class="point flex items-center gap-3">
                        <div class="p-[14px] shrink-0 bg-desa-foreshadow rounded-2xl">
                            <img src="@/assets/images/icons/money-dark-green.svg" alt="icon" class="size-6 shrink-0" />
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="font-semibold text-lg leading-[22.5px] text-desa-dark-green">Rp {{ formatRupiah(item.social_assistance?.amount) }}</p>
                            <h3 class="font-medium text-sm leading-[17.5px] text-desa-secondary">Nominal</h3>
                        </div>
                    </div>
                    <hr class="border-desa-background" />
                    <div class="point flex items-center gap-3">
                        <div class="p-[14px] shrink-0 bg-desa-foreshadow rounded-2xl">
                            <img src="@/assets/images/icons/bag-2-dark-green.svg" alt="icon" class="size-6 shrink-0" />
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="font-semibold text-lg leading-[22.5px] text-desa-dark-green">{{ item.social_assistance?.category }}</p>
                            <h3 class="font-medium text-sm leading-[17.5px] text-desa-secondary">Kategori</h3>
                        </div>
                    </div>
                </section>

                <hr class="border-desa-background" />

                <section class="flex flex-col gap-3">
                    <h2 class="font-medium text-sm leading-[17.5px] text-desa-secondary">Tentang Bantuan</h2>
                    <p class="font-medium leading-8">{{ item.social_assistance?.description }}</p>
                </section>
            </div>

            <div class="flex-1 flex flex-col gap-[14px]">
                <div class="rounded-2xl bg-white p-4 sm:p-6 flex flex-col gap-4 sm:gap-6">
                    <section class="flex items-center justify-between">
                        <h2 class="font-medium text-sm leading-[17.5px] text-desa-secondary">Status Pengajuan</h2>
                        <span class="rounded-full py-[12px] w-[100px] flex justify-center text-white font-semibold text-xs leading-[15px] shrink-0"
                            :class="statusClass(item.status)">
                            {{ statusLabel(item.status) }}
                        </span>
                    </section>
                    <hr class="border-desa-background" />
                    <section class="flex flex-col gap-4">
                        <h2 class="font-medium text-sm leading-[17.5px] text-desa-secondary">Bukti Menerima Bansos</h2>
                        <div v-if="item.proof_file?.url" class="relative flex justify-center items-center w-full h-[230px] overflow-hidden rounded-2xl bg-desa-foreshadow">
                            <img :src="item.proof_file.url" alt="proof" class="w-full h-full object-cover" />
                        </div>
                        <div v-else class="flex justify-center items-center w-full h-[230px] rounded-2xl border-2 border-dashed border-desa-background">
                            <p class="text-center w-[240px] font-medium text-xs leading-[19.2px] text-desa-secondary">
                                {{ item.status === 'approved' ? 'Belum ada bukti' : 'Bukti akan muncul jika pengajuan sudah disetujui' }}
                            </p>
                        </div>
                    </section>
                </div>

                <div class="flex flex-col gap-4 sm:gap-6 rounded-2xl bg-white p-4 sm:p-6">
                    <section class="flex flex-col gap-6">
                        <h2 class="font-medium text-sm leading-[17.5px] text-desa-secondary">Detail Pengajuan</h2>
                        <div class="point flex items-center gap-3">
                            <div class="p-[14px] shrink-0 bg-desa-foreshadow rounded-2xl">
                                <img src="@/assets/images/icons/receive-square-2-dark-green.svg" alt="icon" class="size-6 shrink-0" />
                            </div>
                            <div class="flex flex-col gap-1">
                                <p class="font-semibold text-lg leading-[22.5px] text-desa-dark-green">Rp {{ formatRupiah(item.amount) }}</p>
                                <h3 class="font-medium text-sm leading-[17.5px] text-desa-secondary">Nominal Pengajuan</h3>
                            </div>
                        </div>
                        <hr class="border-desa-background" />
                        <div class="point flex items-center gap-3">
                            <div class="p-[14px] shrink-0 bg-desa-foreshadow rounded-2xl">
                                <img src="@/assets/images/icons/calendar-2-dark-green.svg" alt="icon" class="size-6 shrink-0" />
                            </div>
                            <div class="flex flex-col gap-1">
                                <p class="font-semibold text-lg leading-[22.5px]">{{ formatToClientTimezone(item.created_at) }}</p>
                                <h3 class="font-medium text-sm leading-[17.5px] text-desa-secondary">Tanggal Pengajuan</h3>
                            </div>
                        </div>
                        <hr class="border-desa-background" />
                        <div class="point flex items-center gap-3">
                            <div class="p-[14px] shrink-0 bg-desa-foreshadow rounded-2xl">
                                <img :src="`/desa-digital/src/assets/images/logos/kk-${item.bank.toLowerCase()}.png`" alt="icon" class="size-6 shrink-0" />
                            </div>
                            <div class="flex flex-col gap-1">
                                <p class="font-semibold text-lg leading-[22.5px]">{{ item.bank }} - {{ item.account_number }}</p>
                                <h3 class="font-medium text-sm leading-[17.5px] text-desa-secondary">Rekening Bank</h3>
                            </div>
                        </div>
                    </section>
                    <hr class="border-desa-background" />
                    <section class="flex flex-col gap-1">
                        <h2 class="font-medium text-sm leading-[17.5px] text-desa-secondary">Alasan Pengajuan:</h2>
                        <p class="font-medium leading-8">"{{ item.reason }}"</p>
                    </section>
                </div>
            </div>
        </div>
    </div>
</template>
