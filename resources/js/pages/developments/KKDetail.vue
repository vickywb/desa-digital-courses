<script setup>
import { ref, watch, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import client from '../../api/client';
import { formatRupiah, formatToClientTimezone } from '../../helpers/format';
import ConfirmModal from '@/components/ui/ConfirmModal.vue';

const route = useRoute();
const router = useRouter();
const item = ref(null);
const loading = ref(true);
const saving = ref(false);
const confirmState = ref({ show: false, title: '', message: '', variant: 'danger', onConfirm: null });

function openConfirm(title, message, variant, onConfirm) {
    confirmState.value = { show: true, title, message, variant, onConfirm };
}

const hasApplied = ref(false);
const applicantId = ref(null);
const applicantStatus = ref(null);

const statusClass = (status) => {
    const map = {
        planned: 'bg-desa-yellow',
        ongoing: 'bg-desa-soft-green',
        completed: 'bg-desa-dark-green',
    };
    return map[status] ?? 'bg-desa-grey';
};

const statusLabel = (status) => {
    const map = {
        planned: 'Direncanakan',
        ongoing: 'Berlangsung',
        completed: 'Selesai',
    };
    return map[status] ?? status;
};

async function fetchData(id) {
    loading.value = true;
    hasApplied.value = false;
    applicantId.value = null;
    applicantStatus.value = null;
    item.value = null;

    await Promise.all([
        client.get(`/village-resident/developments/${id}`).then(res => {
            item.value = res.data.data ?? null;
        }).catch(() => {
            item.value = null;
        }),
        client.get('/village-resident/my-development-applicants').then(res => {
            const myApps = res.data.data ?? [];
            const found = myApps.find(a => a.development?.id === id);
            if (found) {
                hasApplied.value = true;
                applicantId.value = found.id;
                applicantStatus.value = found.status;
            }
        }).catch(() => {
            // silent
        }),
    ]);

    loading.value = false;
}

onMounted(() => {
    fetchData(route.params.id);
});

watch(() => route.params.id, (newId) => {
    if (newId) fetchData(newId);
});

async function applyNow() {
    saving.value = true;
    try {
        const res = await client.post(`/village-resident/developments/${route.params.id}/applicants`);
        const data = res.data.data ?? {};
        hasApplied.value = true;
        applicantId.value = data.id;
        applicantStatus.value = data.status;
    } catch (err) {
        const msg = err.response?.data?.message ?? 'Gagal mendaftar';
        alert(msg);
    } finally {
        saving.value = false;
    }
}

function cancelApplication() {
    openConfirm(
        'Batalkan Lamaran',
        'Yakin ingin membatalkan lamaran?',
        'danger',
        async () => {
            saving.value = true;
            try {
                await client.delete(`/village-resident/developments/${route.params.id}/applicants/${applicantId.value}`);
                hasApplied.value = false;
                applicantId.value = null;
                applicantStatus.value = null;
            } catch (err) {
                const msg = err.response?.data?.message ?? 'Gagal membatalkan';
                alert(msg);
            } finally {
                saving.value = false;
            }
        }
    );
}
</script>

<template>
    <div class="flex flex-col gap-[14px]">
        <div class="flex items-center gap-2">
            <router-link to="/warga/pembangunan" class="flex items-center gap-1 font-medium text-desa-dark-green hover:underline">
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali
            </router-link>
            <span class="text-desa-secondary">/</span>
            <span class="font-medium text-desa-secondary">Detail Pembangunan</span>
        </div>

        <div v-if="loading" class="flex items-center justify-center py-20">
            <p class="font-medium text-desa-secondary">Memuat...</p>
        </div>

        <div v-else-if="!item" class="flex flex-col items-center justify-center py-20 gap-4">
            <p class="font-semibold text-lg text-desa-secondary">Data tidak ditemukan</p>
            <router-link to="/warga/pembangunan" class="text-desa-dark-green hover:underline font-medium">Kembali</router-link>
        </div>

        <div v-else class="flex gap-[14px] flex-col lg:flex-row">
            <div class="w-full lg:w-[545px] shrink-0 rounded-2xl bg-white p-4 sm:p-6 flex flex-col gap-6 h-fit">
                <h2 class="font-medium text-sm leading-[17.5px] text-desa-secondary">Informasi Pembangunan</h2>

                <section class="flex items-center justify-between">
                    <div class="flex justify-center items-center w-[120px] h-[100px] shrink-0 rounded-2xl overflow-hidden bg-desa-foreshadow">
                        <img :src="item.development_file?.url" alt="image" class="size-full object-cover" />
                    </div>
                    <span class="rounded-full py-[12px] w-[111px] flex justify-center text-white font-semibold text-xs leading-[15px] shrink-0"
                        :class="statusClass(item.status)">
                        {{ statusLabel(item.status) }}
                    </span>
                </section>

                <section class="flex flex-col gap-[6px]">
                    <h3 class="font-semibold text-lg leading-[22.5px]">{{ item.title }}</h3>
                    <div class="flex items-center gap-1">
                        <img src="@/assets/images/icons/profile-secondary-green-bold.svg" alt="icon" class="size-[18px] shrink-0" />
                        <p class="font-medium text-sm leading-[17.5px] text-desa-secondary">{{ item.person_in_charge }}</p>
                    </div>
                </section>

                <hr class="border-desa-background" />

                <section class="flex flex-col gap-6">
                    <div class="point flex items-center gap-3">
                        <div class="p-[14px] shrink-0 bg-desa-foreshadow rounded-2xl">
                            <img src="@/assets/images/icons/money-dark-green.svg" alt="icon" class="size-6 shrink-0" />
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="font-semibold text-lg leading-[22.5px] text-desa-dark-green">Rp {{ formatRupiah(item.amount) }}</p>
                            <h3 class="font-medium text-sm leading-[17.5px] text-desa-secondary">Anggaran</h3>
                        </div>
                    </div>
                    <hr class="border-desa-background" />
                    <div class="point flex items-center gap-3">
                        <div class="p-[14px] shrink-0 bg-desa-foreshadow rounded-2xl">
                            <img src="@/assets/images/icons/calendar-2-dark-green.svg" alt="icon" class="size-6 shrink-0" />
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="font-semibold text-lg leading-[22.5px]">{{ formatToClientTimezone(item.start_date) }}</p>
                            <h3 class="font-medium text-sm leading-[17.5px] text-desa-secondary">Mulai</h3>
                        </div>
                    </div>
                    <hr class="border-desa-background" />
                    <div class="point flex items-center gap-3">
                        <div class="p-[14px] shrink-0 bg-desa-foreshadow rounded-2xl">
                            <img src="@/assets/images/icons/calendar-2-dark-green.svg" alt="icon" class="size-6 shrink-0" />
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="font-semibold text-lg leading-[22.5px]">{{ formatToClientTimezone(item.end_date) }}</p>
                            <h3 class="font-medium text-sm leading-[17.5px] text-desa-secondary">Selesai</h3>
                        </div>
                    </div>
                </section>

                <hr class="border-desa-background" />

                <section class="flex flex-col gap-3">
                    <h2 class="font-medium text-sm leading-[17.5px] text-desa-secondary">Tentang Pembangunan</h2>
                    <p class="font-medium leading-8">{{ item.description }}</p>
                </section>
            </div>

            <div class="flex-1 flex flex-col gap-[14px]">
                <div class="rounded-2xl bg-white p-4 sm:p-6 flex flex-col gap-6">
                    <template v-if="hasApplied">
                        <h2 class="font-medium text-sm leading-[17.5px] text-desa-secondary">Status Lamaran</h2>

                        <div v-if="applicantStatus === 'pending'" class="flex flex-col items-center gap-4 py-6">
                            <img src="@/assets/images/icons/tick-square-dark-green.svg" class="size-12" alt="icon" />
                            <p class="font-semibold text-lg text-desa-dark-green">Menunggu Persetujuan</p>
                            <p class="font-medium text-sm text-desa-secondary text-center">Lamaran kamu sedang diproses oleh perangkat desa</p>
                            <button @click="cancelApplication" :disabled="saving"
                                class="rounded-2xl border border-desa-red py-[18px] px-6 font-medium leading-5 text-desa-red hover:bg-desa-red hover:text-white transition-setup w-full">
                                {{ saving ? 'Memproses...' : 'Batalkan Lamaran' }}
                            </button>
                        </div>

                        <div v-else-if="applicantStatus === 'approved'" class="flex flex-col items-center gap-4 py-6">
                            <svg class="size-12 text-desa-dark-green" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="font-semibold text-lg text-desa-dark-green">Lamaran Diterima</p>
                            <p class="font-medium text-sm text-desa-secondary text-center">Selamat, lamaran kamu telah diterima. Silakan hubungi perangkat desa untuk informasi lebih lanjut.</p>
                        </div>

                        <div v-else-if="applicantStatus === 'rejected'" class="flex flex-col items-center gap-4 py-6">
                            <svg class="size-12 text-desa-red" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="font-semibold text-lg text-desa-red">Lamaran Ditolak</p>
                            <p class="font-medium text-sm text-desa-secondary text-center">Maaf, lamaran kamu ditolak. Silakan coba pembangunan lainnya.</p>
                        </div>
                    </template>

                    <template v-else-if="item.status === 'planned'">
                        <h2 class="font-medium text-sm leading-[17.5px] text-desa-secondary">Daftar Pembangunan</h2>

                        <div class="flex flex-col gap-4">
                            <div class="flex flex-col gap-2">
                                <p class="font-medium text-sm text-desa-secondary">Tertarik untuk bergabung dalam pembangunan ini?</p>
                            </div>
                            <button @click="applyNow" :disabled="saving"
                                class="bg-desa-black rounded-2xl w-full py-[18px] flex justify-center items-center font-medium leading-5 text-white text-center hover:bg-desa-dark-green transition-setup">
                                {{ saving ? 'Memproses...' : 'Daftar & Ikut Sekarang' }}
                            </button>
                        </div>
                    </template>

                    <template v-else-if="item.status === 'ongoing'">
                        <h2 class="font-medium text-sm leading-[17.5px] text-desa-secondary">Pembangunan Sedang Berjalan</h2>

                        <div class="flex flex-col items-center gap-4 py-6">
                            <svg class="size-12 text-desa-soft-green" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            <p class="font-semibold text-lg text-desa-soft-green">Sedang Berlangsung</p>
                            <p class="font-medium text-sm text-desa-secondary text-center">Pembangunan ini sedang berjalan dan tidak menerima pendaftaran baru.</p>
                        </div>
                    </template>

                    <template v-else>
                        <h2 class="font-medium text-sm leading-[17.5px] text-desa-secondary">Pembangunan Selesai</h2>

                        <div class="flex flex-col items-center gap-4 py-6">
                            <svg class="size-12 text-desa-dark-green" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="font-semibold text-lg text-desa-dark-green">Pembangunan Selesai</p>
                            <p class="font-medium text-sm text-desa-secondary text-center">Pembangunan ini sudah selesai dilaksanakan.</p>
                        </div>
                    </template>
                </div>
            </div>
        </div>
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
