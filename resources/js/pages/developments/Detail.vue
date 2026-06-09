<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import client from '../../api/client';

const route = useRoute();
const router = useRouter();
const item = ref(null);
const loading = ref(true);
const showModal = ref(false);
const saving = ref(false);

const form = ref({
    title: '',
    description: '',
    person_in_charge: '',
    amount: '',
    start_date: '',
    end_date: '',
    status: '',
    image: null,
});

const statusOptions = [
    { value: 'planned', label: 'Direncanakan', class: 'bg-desa-yellow' },
    { value: 'ongoing', label: 'Berlangsung', class: 'bg-desa-soft-green' },
    { value: 'completed', label: 'Selesai', class: 'bg-desa-dark-green' },
];

const statusLabel = computed(() => {
    const opt = statusOptions.find((o) => o.value === item.value?.status);
    return opt?.label ?? item.value?.status;
});

onMounted(async () => {
    try {
        const res = await client.get(`/village-staff/developments/${route.params.id}`);
        item.value = res.data.data ?? null;
    } catch {
        item.value = null;
    } finally {
        loading.value = false;
    }
});

function openEdit() {
    form.value = {
        title: item.value.title ?? '',
        description: item.value.description ?? '',
        person_in_charge: item.value.person_in_charge ?? '',
        amount: item.value.amount ?? '',
        start_date: item.value.start_date ? item.value.start_date.slice(0, 10) : '',
        end_date: item.value.end_date ? item.value.end_date.slice(0, 10) : '',
        status: item.value.status ?? '',
        image: null,
    };
    showModal.value = true;
}

function closeModal() {
    showModal.value = false;
    form.value.image = null;
}

function onImageChange(e) {
    form.value.image = e.target.files[0] ?? null;
}

async function saveEdit() {
    saving.value = true;
    try {
        const payload = new FormData();
        payload.append('title', form.value.title);
        payload.append('description', form.value.description);
        payload.append('person_in_charge', form.value.person_in_charge);
        payload.append('amount', form.value.amount);
        payload.append('start_date', form.value.start_date + ' 00:00:00');
        payload.append('end_date', form.value.end_date + ' 23:59:59');
        payload.append('status', form.value.status);
        payload.append('_method', 'PUT');

        if (form.value.image) {
            payload.append('image', form.value.image);
        }

        const res = await client.post(
            `/village-staff/developments/${route.params.id}`,
            payload,
            { headers: { 'Content-Type': 'multipart/form-data' } }
        );

        item.value = res.data.data ?? null;
        closeModal();
    } catch (err) {
        const msg = err.response?.data?.message ?? 'Gagal menyimpan data';
        alert(msg);
    } finally {
        saving.value = false;
    }
}

function statusBadgeClass(status) {
    const map = {
        planned: 'bg-desa-yellow',
        ongoing: 'bg-desa-soft-green',
        completed: 'bg-desa-dark-green',
    };
    return map[status] ?? 'bg-desa-grey';
}

function statusText(status) {
    const map = {
        planned: 'Direncanakan',
        ongoing: 'Berlangsung',
        completed: 'Selesai',
    };
    return map[status] ?? status;
}

function fieldClass() {
    return 'w-full h-[48px] rounded-2xl px-4 border border-desa-background font-medium leading-5 focus:ring-[1.5px] focus:ring-desa-dark-green focus:outline-none';
}

function selectClass() {
    return 'w-full h-[48px] rounded-2xl px-4 border border-desa-background font-medium leading-5 focus:ring-[1.5px] focus:ring-desa-dark-green focus:outline-none bg-white appearance-none';
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
            <router-link to="/developments" class="text-desa-dark-green hover:underline font-medium">Kembali ke daftar</router-link>
        </div>

        <div v-else class="flex flex-col gap-[14px]">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <h1 class="font-semibold text-2xl">{{ item.title }}</h1>
                <div class="flex gap-3 w-full sm:w-auto">
                    <button @click="openEdit"
                        class="flex items-center gap-2 rounded-2xl px-6 py-3 bg-desa-black text-white font-medium hover:bg-desa-dark-green transition-setup flex-1 sm:flex-initial justify-center">
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit
                    </button>
                </div>
            </div>

            <div class="flex flex-col lg:flex-row gap-[14px]">
                <section class="flex flex-col w-full lg:w-[400px] shrink-0 rounded-3xl p-6 gap-6 bg-white">
                    <div class="flex items-center justify-between">
                        <p class="font-medium leading-5 text-desa-secondary">Status</p>
                        <span class="rounded-full px-4 py-2 text-xs font-semibold text-white" :class="statusBadgeClass(item.status)">
                            {{ statusText(item.status) }}
                        </span>
                    </div>
                    <hr class="border-desa-background">
                    <div class="flex items-center gap-3">
                        <div class="flex size-[52px] rounded-2xl items-center justify-center bg-desa-foreshadow overflow-hidden shrink-0">
                            <svg class="size-6 text-desa-dark-green" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div class="flex flex-col gap-1 min-w-0">
                            <p class="font-semibold text-lg leading-5 truncate">{{ item.person_in_charge }}</p>
                            <p class="font-medium text-sm text-desa-secondary">Penanggung Jawab</p>
                        </div>
                    </div>
                    <hr class="border-desa-background">
                    <div class="flex items-center gap-3">
                        <div class="flex size-[52px] rounded-2xl items-center justify-center bg-desa-foreshadow overflow-hidden shrink-0">
                            <svg class="size-6 text-desa-dark-green" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="flex flex-col gap-1 min-w-0">
                            <p class="font-semibold text-lg leading-5">Rp {{ Number(item.amount ?? 0).toLocaleString('id-ID') }}</p>
                            <p class="font-medium text-sm text-desa-secondary">Anggaran</p>
                        </div>
                    </div>
                    <hr class="border-desa-background">
                    <div class="flex items-center gap-3">
                        <div class="flex size-[52px] rounded-2xl items-center justify-center bg-desa-foreshadow overflow-hidden shrink-0">
                            <svg class="size-6 text-desa-dark-green" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="flex flex-col gap-1 min-w-0">
                            <p class="font-semibold text-lg leading-5">{{ item.start_date ? new Date(item.start_date).toLocaleDateString('id-ID') : '-' }}</p>
                            <p class="font-medium text-sm text-desa-secondary">Tanggal Mulai</p>
                        </div>
                    </div>
                    <hr class="border-desa-background">
                    <div class="flex items-center gap-3">
                        <div class="flex size-[52px] rounded-2xl items-center justify-center bg-desa-foreshadow overflow-hidden shrink-0">
                            <svg class="size-6 text-desa-dark-green" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="flex flex-col gap-1 min-w-0">
                            <p class="font-semibold text-lg leading-5">{{ item.end_date ? new Date(item.end_date).toLocaleDateString('id-ID') : '-' }}</p>
                            <p class="font-medium text-sm text-desa-secondary">Tanggal Selesai</p>
                        </div>
                    </div>
                </section>

                <section class="flex flex-col flex-1 rounded-3xl p-6 gap-6 bg-white">
                    <div v-if="item.development_file" class="rounded-2xl overflow-hidden bg-desa-foreshadow">
                        <img :src="item.development_file.url" class="w-full h-48 md:h-64 object-cover" alt="image">
                    </div>

                    <div class="flex flex-col gap-3">
                        <p class="font-medium text-sm text-desa-secondary">Deskripsi</p>
                        <p class="font-medium leading-8 whitespace-pre-wrap">{{ item.description }}</p>
                    </div>

                    <hr class="border-desa-background">

                    <div class="grid grid-cols-2 gap-4">
                        <div class="flex flex-col gap-1">
                            <p class="font-medium text-sm text-desa-secondary">Penanggung Jawab</p>
                            <p class="font-semibold">{{ item.person_in_charge }}</p>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="font-medium text-sm text-desa-secondary">Anggaran</p>
                            <p class="font-semibold">Rp {{ Number(item.amount ?? 0).toLocaleString('id-ID') }}</p>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="font-medium text-sm text-desa-secondary">Tanggal Mulai</p>
                            <p class="font-semibold">{{ item.start_date ? new Date(item.start_date).toLocaleDateString('id-ID') : '-' }}</p>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="font-medium text-sm text-desa-secondary">Tanggal Selesai</p>
                            <p class="font-semibold">{{ item.end_date ? new Date(item.end_date).toLocaleDateString('id-ID') : '-' }}</p>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <div v-if="showModal" class="fixed inset-0 z-50 flex bg-black/50" @click.self="closeModal">
        <div class="flex flex-col w-full max-w-lg mx-4 rounded-3xl bg-white max-h-[90vh] overflow-y-auto my-auto">
            <div class="flex items-center justify-between p-6 pb-0">
                <h3 class="font-semibold text-lg">Edit Pembangunan</h3>
                <button @click="closeModal" class="flex size-8 items-center justify-center rounded-full hover:bg-desa-background transition-setup">
                    <svg class="size-5 text-desa-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form @submit.prevent="saveEdit" class="flex flex-col gap-4 p-6">
                <div class="flex flex-col gap-2">
                    <label class="font-medium text-sm text-desa-secondary">Judul</label>
                    <input v-model="form.title" :class="fieldClass()" placeholder="Judul pembangunan" required>
                </div>

                <div class="flex flex-col gap-2">
                    <label class="font-medium text-sm text-desa-secondary">Deskripsi</label>
                    <textarea v-model="form.description" :class="fieldClass() + ' h-[100px] resize-none pt-3'" placeholder="Deskripsi pembangunan" required></textarea>
                </div>

                <div class="flex flex-col gap-2">
                    <label class="font-medium text-sm text-desa-secondary">Penanggung Jawab</label>
                    <input v-model="form.person_in_charge" :class="fieldClass()" placeholder="Nama PIC" required>
                </div>

                <div class="flex flex-col gap-2">
                    <label class="font-medium text-sm text-desa-secondary">Anggaran (Rp)</label>
                    <input v-model="form.amount" type="number" :class="fieldClass()" placeholder="Jumlah anggaran" required>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="flex flex-col gap-2">
                        <label class="font-medium text-sm text-desa-secondary">Tanggal Mulai</label>
                        <input v-model="form.start_date" type="date" :class="fieldClass()" required>
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="font-medium text-sm text-desa-secondary">Tanggal Selesai</label>
                        <input v-model="form.end_date" type="date" :class="fieldClass()" required>
                    </div>
                </div>

                <div class="flex flex-col gap-2">
                    <label class="font-medium text-sm text-desa-secondary">Status</label>
                    <div class="relative">
                        <select v-model="form.status" :class="selectClass()" required>
                            <option value="" disabled>Pilih status</option>
                            <option v-for="opt in statusOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                        </select>
                        <svg class="absolute right-4 top-1/2 -translate-y-1/2 size-5 text-desa-secondary pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>

                <div class="flex flex-col gap-2">
                    <label class="font-medium text-sm text-desa-secondary">Gambar</label>
                    <div v-if="item?.development_file && !form.image" class="mb-2">
                        <img :src="item.development_file.url" class="h-24 rounded-2xl object-cover">
                    </div>
                    <input type="file" accept="image/*" @change="onImageChange"
                        class="w-full text-sm text-desa-secondary file:mr-4 file:py-2 file:px-4 file:rounded-2xl file:border-0 file:bg-desa-foreshadow file:font-medium file:text-desa-dark-green hover:file:bg-desa-soft-green transition-setup">
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="button" @click="closeModal"
                        class="flex items-center justify-center h-14 rounded-2xl px-8 border border-desa-background font-semibold text-sm hover:bg-desa-black hover:text-white transition-setup flex-1">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex items-center justify-center h-14 rounded-2xl px-8 bg-desa-dark-green text-white font-semibold text-sm hover:bg-desa-black transition-setup flex-1"
                        :disabled="saving">
                        {{ saving ? 'Menyimpan...' : 'Simpan' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
