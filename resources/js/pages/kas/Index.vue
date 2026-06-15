<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';
import client from '../../api/client';

const router = useRouter();
const authStore = useAuthStore();
const kas = ref(null);
const loading = ref(true);

const isCitizen = computed(() => authStore.userRole === 'head_of_family');
const apiPrefix = computed(() => isCitizen.value ? '/village-resident' : '/village-staff');
const basePath = computed(() => isCitizen.value ? '/warga' : '/staff');

onMounted(async () => {
    try {
        const res = await client.get(`${apiPrefix.value}/kas`);
        kas.value = res.data.data ?? null;
    } catch {
        kas.value = null;
    } finally {
        loading.value = false;
    }
});

function formatRupiah(value) {
    if (value === null || value === undefined) return '0';
    return Number(value).toLocaleString('id-ID');
}
</script>

<template>
    <div class="flex flex-col gap-[14px]">
        <div class="flex items-center justify-between gap-3 flex-wrap">
            <h1 class="font-semibold text-2xl">Kas Desa</h1>
            <button v-if="!isCitizen" @click="router.push(`${basePath}/kas/edit`)"
                class="flex items-center rounded-2xl py-3 px-5 gap-2 bg-desa-dark-green hover:bg-desa-black transition-setup">
                <p class="font-medium text-white">{{ kas ? 'Edit Kas' : 'Buat Kas' }}</p>
                <img src="/desa-digital/src/assets/images/icons/add-square-white.svg" class="flex size-6 shrink-0" alt="icon">
            </button>
        </div>

        <div v-if="loading" class="flex items-center justify-center py-20">
            <p class="font-medium text-desa-secondary">Memuat...</p>
        </div>

        <div v-else-if="!kas" class="flex flex-col items-center justify-center gap-6 py-20">
            <img src="/desa-digital/src/assets/images/icons/wallet-remove-secondary-green.svg" class="size-20 object-cover" alt="icon">
            <p class="font-semibold text-lg text-desa-secondary text-center px-4">Belum ada data kas desa</p>
        </div>

        <template v-else>
            <div class="rounded-3xl p-6 sm:p-8 gradient-vertical flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 sm:gap-6">
                <div class="flex items-center gap-4 sm:gap-6">
                    <div class="flex size-[72px] sm:size-[86px] rounded-2xl items-center justify-center bg-white/20">
                        <img src="/desa-digital/src/assets/images/icons/wallet-check-dark-green.svg" class="flex size-10 sm:size-12 shrink-0" alt="icon">
                    </div>
                    <div class="flex flex-col gap-1 text-white">
                        <p class="font-medium text-sm text-desa-lime">— Total Kas Desa</p>
                        <p class="font-bold text-3xl sm:text-4xl">Rp {{ formatRupiah(kas.total_balance) }}</p>
                        <p class="font-medium text-sm opacity-80">Saldo kas desa terkini</p>
                    </div>
                </div>
                <div class="flex flex-col gap-1 text-right sm:text-left">
                    <p class="font-medium text-sm text-desa-lime">Pengeluaran Bulan Ini</p>
                    <p class="font-bold text-2xl sm:text-3xl text-white">Rp {{ formatRupiah(kas.monthly_expense) }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-[14px]">
                <div class="rounded-3xl bg-white p-4 sm:p-6 flex flex-col gap-4">
                    <div class="flex items-center gap-3">
                        <div class="flex size-[44px] rounded-2xl items-center justify-center bg-desa-foreshadow">
                            <img src="/desa-digital/src/assets/images/icons/money-dark-green.svg" class="flex size-6 shrink-0" alt="icon">
                        </div>
                        <p class="font-semibold text-lg">Iuran Per Bulan</p>
                    </div>
                    <div class="flex flex-col gap-[14px]">
                        <div class="flex items-center justify-between py-1">
                            <div class="flex items-center gap-2">
                                <span class="flex size-3 rounded-full bg-blue-500"></span>
                                <span class="font-medium text-sm text-desa-secondary">Kebersihan</span>
                            </div>
                            <span class="font-semibold">Rp {{ formatRupiah(kas.iuran_kebersihan) }}</span>
                        </div>
                        <div class="flex items-center justify-between py-1">
                            <div class="flex items-center gap-2">
                                <span class="flex size-3 rounded-full bg-orange-500"></span>
                                <span class="font-medium text-sm text-desa-secondary">Keamanan</span>
                            </div>
                            <span class="font-semibold">Rp {{ formatRupiah(kas.iuran_keamanan) }}</span>
                        </div>
                        <div class="flex items-center justify-between py-1">
                            <div class="flex items-center gap-2">
                                <span class="flex size-3 rounded-full bg-purple-500"></span>
                                <span class="font-medium text-sm text-desa-secondary">Makam</span>
                            </div>
                            <span class="font-semibold">Rp {{ formatRupiah(kas.iuran_makam) }}</span>
                        </div>
                        <hr class="border-desa-background">
                        <div class="flex items-center justify-between">
                            <span class="font-medium text-sm">Total Iuran</span>
                            <span class="font-semibold text-desa-dark-green text-lg">
                                Rp {{ formatRupiah(
                                    (kas.iuran_kebersihan || 0) +
                                    (kas.iuran_keamanan || 0) +
                                    (kas.iuran_makam || 0)
                                ) }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="rounded-3xl bg-white p-4 sm:p-6 flex flex-col gap-4">
                    <div class="flex items-center gap-3">
                        <div class="flex size-[44px] rounded-2xl items-center justify-center bg-desa-foreshadow">
                            <img src="/desa-digital/src/assets/images/icons/wallet-3-secondary-green.svg" class="flex size-6 shrink-0" alt="icon">
                        </div>
                        <p class="font-semibold text-lg">Pengeluaran</p>
                    </div>
                    <div class="flex flex-col gap-[14px]">
                        <div class="flex items-center justify-between py-1">
                            <div class="flex items-center gap-2">
                                <span class="flex size-3 rounded-full bg-red-500"></span>
                                <span class="font-medium text-sm text-desa-secondary">Bayar Keamanan</span>
                            </div>
                            <span class="font-semibold">Rp {{ formatRupiah(kas.expense_keamanan) }}</span>
                        </div>
                        <div class="flex items-center justify-between py-1">
                            <div class="flex items-center gap-2">
                                <span class="flex size-3 rounded-full bg-yellow-500"></span>
                                <span class="font-medium text-sm text-desa-secondary">Vendor Kebersihan</span>
                            </div>
                            <span class="font-semibold">Rp {{ formatRupiah(kas.expense_kebersihan) }}</span>
                        </div>
                        <hr class="border-desa-background">
                        <div class="flex items-center justify-between">
                            <span class="font-medium text-sm">Total Pengeluaran</span>
                            <span class="font-semibold text-desa-red text-lg">
                                Rp {{ formatRupiah(
                                    (kas.expense_keamanan || 0) +
                                    (kas.expense_kebersihan || 0)
                                ) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</template>
