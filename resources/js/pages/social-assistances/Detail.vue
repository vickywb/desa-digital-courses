<script setup>
import { ref, onMounted } from 'vue';
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
    category: '',
    amount: '',
    provider: '',
    description: '',
    is_available: true,
    image: null,
});

onMounted(async () => {
    try {
        const res = await client.get(`/village-staff/social-assistances/${route.params.id}`);
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
        category: item.value.category ?? '',
        amount: item.value.amount ?? '',
        provider: item.value.provider ?? '',
        description: item.value.description ?? '',
        is_available: item.value.is_available ?? true,
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
        payload.append('category', form.value.category);
        payload.append('amount', form.value.amount);
        payload.append('provider', form.value.provider);
        payload.append('description', form.value.description);
        payload.append('is_available', form.value.is_available ? '1' : '0');
        payload.append('_method', 'PUT');

        if (form.value.image) {
            payload.append('image', form.value.image);
        }

        const res = await client.post(
            `/village-staff/social-assistances/${route.params.id}`,
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
            <router-link to="/staff/social-assistances" class="flex items-center gap-1 font-medium text-desa-dark-green hover:underline">
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali
            </router-link>
            <span class="text-desa-secondary">/</span>
            <span class="font-medium text-desa-secondary">Detail Bantuan Sosial</span>
        </div>

        <div v-if="loading" class="flex items-center justify-center py-20">
            <p class="font-medium text-desa-secondary">Memuat...</p>
        </div>

        <div v-else-if="!item" class="flex flex-col items-center justify-center py-20 gap-4">
            <p class="font-semibold text-lg text-desa-secondary">Data tidak ditemukan</p>
            <router-link to="/staff/social-assistances" class="text-desa-dark-green hover:underline font-medium">Kembali ke daftar</router-link>
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
                <section class="flex flex-col w-full lg:w-[400px] shrink-0 rounded-3xl p-4 sm:p-6 gap-4 sm:gap-6 bg-white">
                    <div class="flex items-center justify-between">
                        <p class="font-medium leading-5 text-desa-secondary">Status</p>
                        <span class="rounded-full px-4 py-2 text-xs font-semibold text-white"
                            :class="item.is_available ? 'bg-desa-soft-green' : 'bg-desa-red'">
                            {{ item.is_available ? 'Tersedia' : 'Habis' }}
                        </span>
                    </div>
                    <hr class="border-desa-background">
                    <div class="flex items-center gap-3">
                        <div class="flex size-[52px] rounded-2xl items-center justify-center bg-desa-foreshadow overflow-hidden shrink-0">
                            <svg class="size-6 text-desa-dark-green" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                        </div>
                        <div class="flex flex-col gap-1 min-w-0">
                            <p class="font-semibold text-lg leading-5 truncate">{{ item.category }}</p>
                            <p class="font-medium text-sm text-desa-secondary">Kategori</p>
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
                            <p class="font-medium text-sm text-desa-secondary">Jumlah</p>
                        </div>
                    </div>
                    <hr class="border-desa-background">
                    <div class="flex items-center gap-3">
                        <div class="flex size-[52px] rounded-2xl items-center justify-center bg-desa-foreshadow overflow-hidden shrink-0">
                            <svg class="size-6 text-desa-dark-green" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <div class="flex flex-col gap-1 min-w-0">
                            <p class="font-semibold text-lg leading-5 truncate">{{ item.provider }}</p>
                            <p class="font-medium text-sm text-desa-secondary">Penyedia</p>
                        </div>
                    </div>
                </section>

                <section class="flex flex-col flex-1 rounded-3xl p-4 sm:p-6 gap-4 sm:gap-6 bg-white">
                    <div v-if="item.social_assistance_file" class="rounded-2xl overflow-hidden bg-desa-foreshadow">
                        <img :src="item.social_assistance_file.url" class="w-full h-48 md:h-64 object-cover" alt="image">
                    </div>

                    <div class="flex flex-col gap-3">
                        <p class="font-medium text-sm text-desa-secondary">Deskripsi</p>
                        <p class="font-medium leading-8 whitespace-pre-wrap">{{ item.description }}</p>
                    </div>

                    <hr class="border-desa-background">

                    <div class="grid grid-cols-2 gap-4">
                        <div class="flex flex-col gap-1">
                            <p class="font-medium text-sm text-desa-secondary">Kategori</p>
                            <p class="font-semibold">{{ item.category }}</p>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="font-medium text-sm text-desa-secondary">Penyedia</p>
                            <p class="font-semibold">{{ item.provider }}</p>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="font-medium text-sm text-desa-secondary">Jumlah</p>
                            <p class="font-semibold">Rp {{ Number(item.amount ?? 0).toLocaleString('id-ID') }}</p>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="font-medium text-sm text-desa-secondary">Ketersediaan</p>
                            <span class="rounded-full px-3 py-1 text-xs font-semibold text-white w-fit"
                                :class="item.is_available ? 'bg-desa-soft-green' : 'bg-desa-red'">
                                {{ item.is_available ? 'Tersedia' : 'Habis' }}
                            </span>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <div v-if="showModal" class="fixed inset-0 z-50 flex bg-black/50" @click.self="closeModal">
        <div class="flex flex-col w-full max-w-lg mx-4 rounded-3xl bg-white max-h-[90vh] overflow-y-auto my-auto">
            <div class="flex items-center justify-between p-6 pb-0">
                <h3 class="font-semibold text-lg">Edit Bantuan Sosial</h3>
                <button @click="closeModal" class="flex size-8 items-center justify-center rounded-full hover:bg-desa-background transition-setup">
                    <svg class="size-5 text-desa-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form @submit.prevent="saveEdit" class="flex flex-col gap-4 p-6">
                <div class="flex flex-col gap-2">
                    <label class="font-medium text-sm text-desa-secondary">Judul</label>
                    <input v-model="form.title" :class="fieldClass()" placeholder="Judul bantuan sosial" required>
                </div>

                <div class="flex flex-col gap-2">
                    <label class="font-medium text-sm text-desa-secondary">Kategori</label>
                    <input v-model="form.category" :class="fieldClass()" placeholder="Kategori bansos" required>
                </div>

                <div class="flex flex-col gap-2">
                    <label class="font-medium text-sm text-desa-secondary">Penyedia</label>
                    <input v-model="form.provider" :class="fieldClass()" placeholder="Nama penyedia" required>
                </div>

                <div class="flex flex-col gap-2">
                    <label class="font-medium text-sm text-desa-secondary">Jumlah (Rp)</label>
                    <input v-model="form.amount" type="number" :class="fieldClass()" placeholder="Jumlah bansos" required>
                </div>

                <div class="flex flex-col gap-2">
                    <label class="font-medium text-sm text-desa-secondary">Deskripsi</label>
                    <textarea v-model="form.description" :class="fieldClass() + ' h-[100px] resize-none pt-3'" placeholder="Deskripsi bantuan sosial" required></textarea>
                </div>

                <div class="flex flex-col gap-2">
                    <label class="font-medium text-sm text-desa-secondary">Ketersediaan</label>
                    <div class="flex items-center gap-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" v-model="form.is_available" :value="true" class="accent-desa-dark-green size-4">
                            <span class="font-medium text-sm">Tersedia</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" v-model="form.is_available" :value="false" class="accent-desa-red size-4">
                            <span class="font-medium text-sm">Habis</span>
                        </label>
                    </div>
                </div>

                <div class="flex flex-col gap-2">
                    <label class="font-medium text-sm text-desa-secondary">Gambar</label>
                    <div v-if="item?.social_assistance_file && !form.image" class="mb-2">
                        <img :src="item.social_assistance_file.url" class="h-24 rounded-2xl object-cover">
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
