<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import client from '../../api/client';

const route = useRoute();
const router = useRouter();
const item = ref(null);
const participants = ref([]);
const loading = ref(true);
const showModal = ref(false);
const saving = ref(false);

const form = ref({
    title: '',
    description: '',
    price: '',
    start_date: '',
    is_active: true,
    image: null,
});

async function loadParticipants() {
    try {
        const res = await client.get(`/village-staff/events/${route.params.id}/participants`);
        participants.value = res.data.data ?? [];
    } catch {
        participants.value = [];
    }
}

async function approveParticipant(participant) {
    if (!confirm(`Terima peserta ${participant.head_of_family?.full_name ?? 'ini'}?`)) return;
    saving.value = true;
    try {
        const res = await client.put(
            `/village-staff/events/${route.params.id}/participants/${participant.id}`,
            { payment_status: 'paid' }
        );
        const updated = res.data.data;
        const idx = participants.value.findIndex(p => p.id === updated.id);
        if (idx >= 0) participants.value[idx] = updated;
    } catch (err) {
        alert(err.response?.data?.message ?? 'Gagal menyetujui');
    } finally {
        saving.value = false;
    }
}

onMounted(async () => {
    try {
        const res = await client.get(`/village-staff/events/${route.params.id}`);
        item.value = res.data.data ?? null;
    } catch {
        item.value = null;
    } finally {
        loading.value = false;
    }

    loadParticipants();
});

function openEdit() {
    form.value = {
        title: item.value.title ?? '',
        description: item.value.description ?? '',
        price: item.value.price ?? '',
        start_date: item.value.start_date ?? '',
        is_active: item.value.is_active ?? true,
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
        payload.append('price', form.value.price);
        payload.append('start_date', form.value.start_date);
        payload.append('is_active', form.value.is_active ? '1' : '0');
        payload.append('_method', 'PUT');

        if (form.value.image) {
            payload.append('image', form.value.image);
        }

        const res = await client.post(
            `/village-staff/events/${route.params.id}`,
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
            <router-link to="/staff/events" class="flex items-center gap-1 font-medium text-desa-dark-green hover:underline">
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali
            </router-link>
            <span class="text-desa-secondary">/</span>
            <span class="font-medium text-desa-secondary">Detail Event</span>
        </div>

        <div v-if="loading" class="flex items-center justify-center py-20">
            <p class="font-medium text-desa-secondary">Memuat...</p>
        </div>

        <div v-else-if="!item" class="flex flex-col items-center justify-center py-20 gap-4">
            <p class="font-semibold text-lg text-desa-secondary">Data tidak ditemukan</p>
            <router-link to="/staff/events" class="text-desa-dark-green hover:underline font-medium">Kembali ke daftar</router-link>
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
                        <p class="font-medium leading-5 text-desa-secondary">Pendaftaran</p>
                        <span class="rounded-full px-4 py-2 text-xs font-semibold text-white"
                            :class="item.is_active ? 'bg-desa-soft-green' : 'bg-desa-red'">
                            {{ item.is_active ? 'Open' : 'Closed' }}
                        </span>
                    </div>
                    <hr class="border-desa-background">
                    <div class="flex items-center gap-3">
                        <div class="flex size-[52px] rounded-2xl items-center justify-center bg-desa-foreshadow overflow-hidden shrink-0">
                            <svg class="size-6 text-desa-dark-green" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="flex flex-col gap-1 min-w-0">
                            <p class="font-semibold text-lg leading-5">
                                {{ item.price && Number(item.price) > 0 ? 'Rp ' + Number(item.price).toLocaleString('id-ID') : 'Gratis' }}
                            </p>
                            <p class="font-medium text-sm text-desa-secondary">Harga</p>
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
                </section>

                <section class="flex flex-col flex-1 rounded-3xl p-6 gap-6 bg-white">
                    <div v-if="item.event_file" class="rounded-2xl overflow-hidden bg-desa-foreshadow">
                        <img :src="item.event_file.url" class="w-full h-48 md:h-64 object-cover" alt="image">
                    </div>

                    <div class="flex flex-col gap-3">
                        <p class="font-medium text-sm text-desa-secondary">Deskripsi</p>
                        <p class="font-medium leading-8 whitespace-pre-wrap">{{ item.description }}</p>
                    </div>

                    <hr class="border-desa-background">

                    <div class="grid grid-cols-2 gap-4">
                        <div class="flex flex-col gap-1">
                            <p class="font-medium text-sm text-desa-secondary">Harga</p>
                            <p class="font-semibold">{{ item.price && Number(item.price) > 0 ? 'Rp ' + Number(item.price).toLocaleString('id-ID') : 'Gratis' }}</p>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="font-medium text-sm text-desa-secondary">Pendaftaran</p>
                            <span class="rounded-full px-3 py-1 text-xs font-semibold text-white w-fit"
                                :class="item.is_active ? 'bg-desa-soft-green' : 'bg-desa-red'">
                                {{ item.is_active ? 'Open' : 'Closed' }}
                            </span>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="font-medium text-sm text-desa-secondary">Tanggal Mulai</p>
                            <p class="font-semibold">{{ item.start_date ? new Date(item.start_date).toLocaleDateString('id-ID') : '-' }}</p>
                        </div>
                    </div>
                </section>
            </div>
        </div>
            <section v-if="item" class="flex flex-col rounded-3xl p-6 gap-4 bg-white">
                <div class="flex items-center justify-between">
                    <h2 class="font-semibold text-lg">Peserta ({{ participants.length }})</h2>
                </div>

                <div v-if="participants.length === 0" class="flex flex-col items-center py-8 gap-2">
                    <p class="font-medium text-desa-secondary">Belum ada peserta</p>
                </div>

                <div v-else class="flex flex-col gap-3">
                    <div v-for="p in participants" :key="p.id"
                        class="flex items-center justify-between gap-4 rounded-2xl border border-desa-background p-4">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="flex size-10 rounded-full items-center justify-center bg-desa-foreshadow shrink-0 overflow-hidden">
                                <svg class="size-5 text-desa-dark-green" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <div class="flex flex-col min-w-0">
                                <p class="font-semibold text-sm leading-5 truncate">{{ p.head_of_family?.full_name ?? '-' }}</p>
                                <p class="text-xs text-desa-secondary">{{ p.family_member?.full_name ?? 'Kepala Keluarga' }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            <span class="rounded-full px-3 py-1 text-xs font-semibold text-white"
                                :class="p.payment_status === 'paid' ? 'bg-desa-dark-green' : 'bg-desa-yellow'">
                                {{ p.payment_status === 'paid' ? 'Dibayar' : 'Pending' }}
                            </span>

                            <template v-if="p.payment_status === 'pending'">
                                <button @click="approveParticipant(p)" :disabled="saving"
                                    class="rounded-lg px-3 py-1.5 bg-desa-dark-green text-white text-xs font-semibold hover:bg-desa-black transition-setup">
                                    Terima
                                </button>
                            </template>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <div v-if="showModal" class="fixed inset-0 z-50 flex bg-black/50" @click.self="closeModal">
        <div class="flex flex-col w-full max-w-lg mx-4 rounded-3xl bg-white max-h-[90vh] overflow-y-auto my-auto">
            <div class="flex items-center justify-between p-6 pb-0">
                <h3 class="font-semibold text-lg">Edit Event</h3>
                <button @click="closeModal" class="flex size-8 items-center justify-center rounded-full hover:bg-desa-background transition-setup">
                    <svg class="size-5 text-desa-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form @submit.prevent="saveEdit" class="flex flex-col gap-4 p-6">
                <div class="flex flex-col gap-2">
                    <label class="font-medium text-sm text-desa-secondary">Judul</label>
                    <input v-model="form.title" :class="fieldClass()" placeholder="Judul event" required>
                </div>

                <div class="flex flex-col gap-2">
                    <label class="font-medium text-sm text-desa-secondary">Deskripsi</label>
                    <textarea v-model="form.description" :class="fieldClass() + ' h-[100px] resize-none pt-3'" placeholder="Deskripsi event" required></textarea>
                </div>

                <div class="flex flex-col gap-2">
                    <label class="font-medium text-sm text-desa-secondary">Harga (Rp)</label>
                    <input v-model="form.price" type="number" :class="fieldClass()" placeholder="0 = Gratis" required>
                </div>

                <div class="flex flex-col gap-2">
                    <label class="font-medium text-sm text-desa-secondary">Tanggal Mulai</label>
                    <input v-model="form.start_date" type="datetime-local" :class="fieldClass()" required>
                </div>

                <div class="flex flex-col gap-2">
                    <label class="font-medium text-sm text-desa-secondary">Pendaftaran</label>
                    <div class="flex items-center gap-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" v-model="form.is_active" :value="true" class="accent-desa-dark-green size-4">
                            <span class="font-medium text-sm">Open</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" v-model="form.is_active" :value="false" class="accent-desa-red size-4">
                            <span class="font-medium text-sm">Closed</span>
                        </label>
                    </div>
                </div>

                <div class="flex flex-col gap-2">
                    <label class="font-medium text-sm text-desa-secondary">Gambar</label>
                    <div v-if="item?.event_file && !form.image" class="mb-2">
                        <img :src="item.event_file.url" class="h-24 rounded-2xl object-cover">
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
