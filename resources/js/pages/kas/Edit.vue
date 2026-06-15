<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import client from '../../api/client';

const router = useRouter();
const loading = ref(true);
const saving = ref(false);
const isEdit = ref(false);

const form = ref({
    total_balance: '',
    monthly_expense: '',
    iuran_kebersihan: '',
    iuran_keamanan: '',
    iuran_makam: '',
    expense_keamanan: '',
    expense_kebersihan: '',
});

onMounted(async () => {
    try {
        const res = await client.get('/village-staff/kas');
        const data = res.data.data;

        if (data) {
            isEdit.value = true;
            form.value = {
                total_balance: data.total_balance ?? '',
                monthly_expense: data.monthly_expense ?? '',
                iuran_kebersihan: data.iuran_kebersihan ?? '',
                iuran_keamanan: data.iuran_keamanan ?? '',
                iuran_makam: data.iuran_makam ?? '',
                expense_keamanan: data.expense_keamanan ?? '',
                expense_kebersihan: data.expense_kebersihan ?? '',
            };
        }
    } catch {
        // No existing data, create mode
    } finally {
        loading.value = false;
    }
});

async function save() {
    saving.value = true;
    try {
        await client.put('/village-staff/kas', {
            total_balance: form.value.total_balance || 0,
            monthly_expense: form.value.monthly_expense || 0,
            iuran_kebersihan: form.value.iuran_kebersihan || 0,
            iuran_keamanan: form.value.iuran_keamanan || 0,
            iuran_makam: form.value.iuran_makam || 0,
            expense_keamanan: form.value.expense_keamanan || 0,
            expense_kebersihan: form.value.expense_kebersihan || 0,
        });

        router.push('/staff/kas');
    } catch (err) {
        const msg = err.response?.data?.message ?? 'Gagal menyimpan data';
        alert(msg);
    } finally {
        saving.value = false;
    }
}

function fieldClass() {
    return 'w-full h-[40px] rounded-2xl px-3 border border-desa-background text-sm font-medium leading-5 focus:ring-[1.5px] focus:ring-desa-dark-green focus:outline-none';
}
</script>

<template>
    <div class="flex flex-col gap-[14px]">
        <div class="flex items-center gap-2">
            <router-link to="/staff/kas" class="flex items-center gap-1 font-medium text-desa-dark-green hover:underline">
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali
            </router-link>
            <span class="text-desa-secondary">/</span>
            <span class="font-medium text-desa-secondary">{{ isEdit ? 'Edit Kas Desa' : 'Buat Kas Desa' }}</span>
        </div>

        <div v-if="loading" class="flex items-center justify-center py-20">
            <p class="font-medium text-desa-secondary">Memuat...</p>
        </div>

        <form v-else @submit.prevent="save" class="flex flex-col gap-3 rounded-3xl bg-white p-3 sm:p-5 max-w-xl">
            <h1 class="font-semibold text-xl sm:text-2xl">{{ isEdit ? 'Edit Kas Desa' : 'Buat Kas Desa' }}</h1>

            <div class="grid grid-cols-2 gap-3">
                <div class="flex flex-col gap-1.5">
                    <label class="font-medium text-xs sm:text-sm text-desa-secondary">Total Kas</label>
                    <input v-model="form.total_balance" type="number" min="0" :class="fieldClass()" placeholder="Total kas desa">
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="font-medium text-xs sm:text-sm text-desa-secondary">Pengeluaran Bulan Ini</label>
                    <input v-model="form.monthly_expense" type="number" min="0" :class="fieldClass()" placeholder="Pengeluaran bulan ini">
                </div>
            </div>

            <hr class="border-desa-background">

            <p class="font-medium text-xs sm:text-sm text-desa-secondary">Iuran Per Bulan</p>
            <div class="grid grid-cols-3 gap-2">
                <div class="flex flex-col gap-1.5">
                    <label class="font-medium text-xs text-desa-secondary">Kebersihan</label>
                    <input v-model="form.iuran_kebersihan" type="number" min="0" :class="fieldClass()" placeholder="Iuran">
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="font-medium text-xs text-desa-secondary">Keamanan</label>
                    <input v-model="form.iuran_keamanan" type="number" min="0" :class="fieldClass()" placeholder="Iuran">
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="font-medium text-xs text-desa-secondary">Makam</label>
                    <input v-model="form.iuran_makam" type="number" min="0" :class="fieldClass()" placeholder="Iuran">
                </div>
            </div>

            <hr class="border-desa-background">

            <p class="font-medium text-xs sm:text-sm text-desa-secondary">Pengeluaran</p>
            <div class="grid grid-cols-2 gap-3">
                <div class="flex flex-col gap-1.5">
                    <label class="font-medium text-xs sm:text-sm text-desa-secondary">Bayar Keamanan</label>
                    <input v-model="form.expense_keamanan" type="number" min="0" :class="fieldClass()" placeholder="Biaya keamanan">
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="font-medium text-xs sm:text-sm text-desa-secondary">Vendor Kebersihan</label>
                    <input v-model="form.expense_kebersihan" type="number" min="0" :class="fieldClass()" placeholder="Biaya kebersihan">
                </div>
            </div>

            <div class="flex items-center gap-3 pt-1">
                <button type="button" @click="router.push('/staff/kas')"
                    class="flex items-center justify-center h-11 rounded-2xl px-6 border border-desa-background font-semibold text-sm hover:bg-desa-black hover:text-white transition-setup flex-1">
                    Batal
                </button>
                <button type="submit"
                    class="flex items-center justify-center h-11 rounded-2xl px-6 bg-desa-dark-green text-white font-semibold text-sm hover:bg-desa-black transition-setup flex-1"
                    :disabled="saving">
                    {{ saving ? 'Menyimpan...' : 'Simpan' }}
                </button>
            </div>
        </form>
    </div>
</template>
