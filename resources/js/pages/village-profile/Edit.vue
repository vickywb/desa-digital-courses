<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import client from '../../api/client';

const router = useRouter();
const profile = ref(null);
const loading = ref(true);
const saving = ref(false);

const form = ref({
    name: '',
    about: '',
    headman: '',
    people: '',
    agriculture_area: '',
    total_area: '',
    images: [],
});

onMounted(async () => {
    try {
        const res = await client.get('/village-staff/village-profiles');
        profile.value = res.data.data ?? null;

        if (profile.value) {
            form.value = {
                name: profile.value.name ?? '',
                about: profile.value.about ?? '',
                headman: profile.value.headman ?? '',
                people: profile.value.people ?? '',
                agriculture_area: profile.value.agriculture_area ?? '',
                total_area: profile.value.total_area ?? '',
                images: [],
            };
        }
    } catch {
        profile.value = null;
    } finally {
        loading.value = false;
    }
});

function onImagesChange(e) {
    form.value.images = Array.from(e.target.files ?? []);
}

async function save() {
    saving.value = true;
    try {
        const payload = new FormData();
        payload.append('name', form.value.name);
        payload.append('about', form.value.about);
        payload.append('headman', form.value.headman);
        payload.append('people', form.value.people);
        payload.append('agriculture_area', form.value.agriculture_area);
        payload.append('total_area', form.value.total_area);
        payload.append('_method', 'PUT');

        for (const img of form.value.images) {
            payload.append('images[]', img);
        }

        await client.post(
            `/village-staff/village-profiles/${profile.value.id}`,
            payload,
            { headers: { 'Content-Type': 'multipart/form-data' } }
        );

        router.push('/staff/village-profile');
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

function textareaClass() {
    return fieldClass() + ' h-[120px] resize-none pt-3';
}
</script>

<template>
    <div class="flex flex-col gap-[14px]">
        <div class="flex items-center gap-2">
            <router-link to="/staff/village-profile" class="flex items-center gap-1 font-medium text-desa-dark-green hover:underline">
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali
            </router-link>
            <span class="text-desa-secondary">/</span>
            <span class="font-medium text-desa-secondary">Edit Profile Desa</span>
        </div>

        <div v-if="loading" class="flex items-center justify-center py-20">
            <p class="font-medium text-desa-secondary">Memuat...</p>
        </div>

        <div v-else-if="!profile" class="flex flex-col items-center justify-center py-20 gap-4">
            <p class="font-semibold text-lg text-desa-secondary">Belum ada profile desa</p>
            <router-link to="/staff/village-profile/create" class="text-desa-dark-green hover:underline font-medium">Buat profile baru</router-link>
        </div>

        <form v-else @submit.prevent="save" class="flex flex-col gap-4 rounded-3xl bg-white p-6 max-w-2xl">
            <h1 class="font-semibold text-2xl">Edit Profile Desa</h1>

            <div class="flex flex-col gap-2">
                <label class="font-medium text-sm text-desa-secondary">Nama Desa</label>
                <input v-model="form.name" :class="fieldClass()" placeholder="Nama desa" required>
            </div>

            <div class="flex flex-col gap-2">
                <label class="font-medium text-sm text-desa-secondary">Tentang Desa</label>
                <textarea v-model="form.about" :class="textareaClass()" placeholder="Deskripsi tentang desa" required></textarea>
            </div>

            <div class="flex flex-col gap-2">
                <label class="font-medium text-sm text-desa-secondary">Kepala Desa</label>
                <input v-model="form.headman" :class="fieldClass()" placeholder="Nama kepala desa" required>
            </div>

            <div class="flex flex-col gap-2">
                <label class="font-medium text-sm text-desa-secondary">Jumlah Penduduk</label>
                <input v-model="form.people" type="number" min="0" :class="fieldClass()" placeholder="Jumlah penduduk" required>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="flex flex-col gap-2">
                    <label class="font-medium text-sm text-desa-secondary">Luas Pertanian</label>
                    <input v-model="form.agriculture_area" :class="fieldClass()" placeholder="Luas area pertanian" required>
                </div>
                <div class="flex flex-col gap-2">
                    <label class="font-medium text-sm text-desa-secondary">Luas Area</label>
                    <input v-model="form.total_area" :class="fieldClass()" placeholder="Luas total area" required>
                </div>
            </div>

            <div class="flex flex-col gap-2">
                <label class="font-medium text-sm text-desa-secondary">Foto Desa</label>
                <div v-if="profile.village_photo?.length" class="flex flex-wrap gap-2 mb-2">
                    <img v-for="photo in profile.village_photo" :key="photo.id" :src="photo.url" class="h-24 rounded-2xl object-cover">
                </div>
                <input type="file" accept="image/*" multiple @change="onImagesChange"
                    class="w-full text-sm text-desa-secondary file:mr-4 file:py-2 file:px-4 file:rounded-2xl file:border-0 file:bg-desa-foreshadow file:font-medium file:text-desa-dark-green hover:file:bg-desa-soft-green transition-setup">
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="button" @click="router.push('/staff/village-profile')"
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
</template>
