<script setup>
import { ref, onMounted } from 'vue';
import client from '../../api/client';

const items = ref([]);
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

onMounted(async () => {
    try {
        const res = await client.get('/village-staff/events');
        items.value = res.data.data ?? [];
    } catch {
        items.value = [];
    }
});

function openModal() {
    form.value = {
        title: '', description: '', price: '',
        start_date: '', is_active: true, image: null,
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

async function save() {
    saving.value = true;
    try {
        const payload = new FormData();
        payload.append('title', form.value.title);
        payload.append('description', form.value.description);
        payload.append('price', form.value.price);
        payload.append('start_date', form.value.start_date);
        payload.append('is_active', form.value.is_active ? '1' : '0');
        if (form.value.image) payload.append('image', form.value.image);
        const res = await client.post('/village-staff/events', payload, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });
        items.value.unshift(res.data.data ?? {});
        closeModal();
    } catch (err) {
        alert(err.response?.data?.message ?? 'Gagal menyimpan');
    } finally {
        saving.value = false;
    }
}

function fieldClass() {
    return 'w-full h-[48px] rounded-2xl px-4 border border-desa-background font-medium leading-5 focus:ring-[1.5px] focus:ring-desa-dark-green focus:outline-none';
}
</script>

<template>
    <div class="flex flex-col gap-[14px]">
        <div class="flex items-center justify-between">
            <h1 class="font-semibold text-2xl">Event Desa</h1>
            <button @click="openModal"
                class="flex items-center gap-2 rounded-2xl px-6 py-3 bg-desa-dark-green text-white font-medium hover:bg-desa-black transition-setup">
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Tambah
            </button>
        </div>

        <div class="rounded-3xl bg-white p-6">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-desa-background">
                            <th class="px-4 py-4 font-medium text-desa-secondary text-sm">Judul</th>
                            <th class="px-4 py-4 font-medium text-desa-secondary text-sm">Harga</th>
                            <th class="px-4 py-4 font-medium text-desa-secondary text-sm">Mulai</th>
                            <th class="px-4 py-4 font-medium text-desa-secondary text-sm">Pendaftaran</th>
                            <th class="px-4 py-4 font-medium text-desa-secondary text-sm">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in items" :key="item.id" class="border-b border-desa-foreshadow last:border-0">
                            <td class="px-4 py-4 font-semibold leading-5">{{ item.title }}</td>
                            <td class="px-4 py-4 text-desa-secondary">{{ item.price ? `Rp ${Number(item.price).toLocaleString('id-ID')}` : 'Gratis' }}</td>
                            <td class="px-4 py-4 text-desa-secondary">{{ item.start_date ? new Date(item.start_date).toLocaleDateString('id-ID') : '-' }}</td>
                            <td class="px-4 py-4">
                                <span class="rounded-full px-3 py-1 text-xs font-semibold text-white"
                                :class="item.is_active ? 'bg-desa-soft-green' : 'bg-desa-red'">
                                {{ item.is_active ? 'Open' : 'Closed' }}
                            </span>
                        </td>
                        <td class="px-4 py-4">
                            <router-link :to="`/staff/events/${item.id}`"
                                class="rounded-2xl px-6 py-[14px] bg-desa-black font-medium leading-5 text-white text-sm inline-block">
                                Detail
                            </router-link>
                        </td>
                    </tr>
                    <tr v-if="!items.length">
                        <td colspan="5" class="px-4 py-12 text-center text-desa-secondary font-medium">Belum ada event desa</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div v-if="showModal" class="fixed inset-0 z-50 flex bg-black/50" @click.self="closeModal">
        <div class="flex flex-col w-full max-w-lg mx-4 rounded-3xl bg-white max-h-[90vh] overflow-y-auto my-auto">
            <div class="flex items-center justify-between p-6 pb-0">
                <h3 class="font-semibold text-lg">Tambah Event</h3>
                <button @click="closeModal" class="flex size-8 items-center justify-center rounded-full hover:bg-desa-background transition-setup">
                    <svg class="size-5 text-desa-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form @submit.prevent="save" class="flex flex-col gap-4 p-6">
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
</div>
</template>
