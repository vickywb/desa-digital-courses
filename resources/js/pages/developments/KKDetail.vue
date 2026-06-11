<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import client from '../../api/client';
import { formatRupiah, formatToClientTimezone } from '../../helpers/format';

const route = useRoute();
const router = useRouter();
const item = ref(null);
const loading = ref(true);
const saving = ref(false);
const hasApplied = ref(false);
const applicantId = ref(null);

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

onMounted(async () => {
    try {
        const res = await client.get(`/village-resident/developments/${route.params.id}`);
        item.value = res.data.data ?? null;
    } catch {
        item.value = null;
    } finally {
        loading.value = false;
    }

    try {
        const myRes = await client.get('/village-resident/my-development-applicants');
        const myApps = myRes.data.data ?? [];
        const found = myApps.find(a => a.development?.id === route.params.id);
        if (found) {
            hasApplied.value = true;
            applicantId.value = found.id;
        }
    } catch {
        // silent
    }
});

async function applyNow() {
    saving.value = true;
    try {
        await client.post(`/village-resident/developments/${route.params.id}/applicants`);
        hasApplied.value = true;
    } catch (err) {
        const msg = err.response?.data?.message ?? 'Gagal mendaftar';
        alert(msg);
    } finally {
        saving.value = false;
    }
}

async function cancelApplication() {
    if (!confirm('Yakin ingin membatalkan lamaran?')) return;
    saving.value = true;
    try {
        await client.delete(`/village-resident/developments/${route.params.id}/applicants/${applicantId.value}`);
        hasApplied.value = false;
        applicantId.value = null;
    } catch (err) {
        const msg = err.response?.data?.message ?? 'Gagal membatalkan';
        alert(msg);
    } finally {
        saving.value = false;
    }
}
</script>

<template>
    <div class="flex flex-col gap-[14px]">
        <div class="flex items-center gap-2">
            <router-link to="/developments" class="flex items-center gap-1 font-medium text-desa-dark-green hover:underline">
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
            <router-link to="/developments" class="text-desa-dark-green hover:underline font-medium">Kembali</router-link>
        </div>

        <div v-else class="flex gap-[14px] flex-col lg:flex-row">
            <div class="w-full lg:w-[545px] shrink-0 rounded-2xl bg-white p-6 flex flex-col gap-6 h-fit">
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
                <div class="rounded-2xl bg-white p-6 flex flex-col gap-6">
                    <h2 class="font-medium text-sm leading-[17.5px] text-desa-secondary" v-if="hasApplied">Status Lamaran</h2>
                    <h2 class="font-medium text-sm leading-[17.5px] text-desa-secondary" v-else>Daftar Pembangunan</h2>

                    <div v-if="hasApplied" class="flex flex-col items-center gap-4 py-6">
                        <img src="@/assets/images/icons/tick-square-dark-green.svg" class="size-12" alt="icon" />
                        <p class="font-semibold text-lg text-desa-dark-green">Kamu Sudah Melamar</p>
                        <p class="font-medium text-sm text-desa-secondary text-center">Lamaran kamu sedang diproses oleh perangkat desa</p>
                        <button @click="cancelApplication" :disabled="saving"
                            class="rounded-2xl border border-desa-red py-[18px] px-6 font-medium leading-5 text-desa-red hover:bg-desa-red hover:text-white transition-setup w-full">
                            {{ saving ? 'Memproses...' : 'Batalkan Lamaran' }}
                        </button>
                    </div>

                    <div v-else class="flex flex-col gap-4">
                        <div class="flex flex-col gap-2">
                            <p class="font-medium text-sm text-desa-secondary">Tertarik untuk bergabung dalam pembangunan ini?</p>
                        </div>
                        <button @click="applyNow" :disabled="saving"
                            class="bg-desa-black rounded-2xl w-full py-[18px] flex justify-center items-center font-medium leading-5 text-white text-center hover:bg-desa-dark-green transition-setup">
                            {{ saving ? 'Memproses...' : 'Daftar & Ikut Sekarang' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
