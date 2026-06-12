<script setup>
import { ref, onMounted } from 'vue';
import client from '../../api/client';

const items = ref([]);
const showModal = ref(false);
const saving = ref(false);

const form = ref({
    title: '',
    category: 'staple',
    amount: '',
    provider: '',
    description: '',
    is_available: true,
    image: null,
});

onMounted(async () => {
    try {
        const res = await client.get('/village-staff/social-assistances');
        items.value = res.data.data ?? [];
    } catch {
        items.value = [];
    }
});

const categoryOptions = [
    { value: 'staple', label: 'Sembako' },
    { value: 'cash', label: 'Tunai' },
    { value: 'subsidized fuel', label: 'BBM Bersubsidi' },
    { value: 'healthcare', label: 'Kesehatan' },
];

function openModal() {
    form.value = {
        title: '', category: 'staple', amount: '',
        provider: '', description: '', is_available: true, image: null,
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
        payload.append('category', form.value.category);
        payload.append('amount', form.value.amount);
        payload.append('provider', form.value.provider);
        payload.append('description', form.value.description);
        payload.append('is_available', form.value.is_available ? '1' : '0');
        if (form.value.image) payload.append('image', form.value.image);
        const res = await client.post('/village-staff/social-assistances', payload, {
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

function selectClass() {
    return 'w-full h-[48px] rounded-2xl px-4 border border-desa-background font-medium leading-5 focus:ring-[1.5px] focus:ring-desa-dark-green focus:outline-none bg-white appearance-none';
}
</script>

<template>
    <div class="flex flex-col gap-[14px]">
        <div class="flex items-center justify-between">
            <h1 class="font-semibold text-2xl">Bantuan Sosial</h1>
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
                            <th class="px-4 py-4 font-medium text-desa-secondary text-sm">Kategori</th>
                            <th class="px-4 py-4 font-medium text-desa-secondary text-sm">Jumlah</th>
                            <th class="px-4 py-4 font-medium text-desa-secondary text-sm">Penyedia</th>
                            <th class="px-4 py-4 font-medium text-desa-secondary text-sm">Status</th>
                            <th class="px-4 py-4 font-medium text-desa-secondary text-sm">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in items" :key="item.id" class="border-b border-desa-foreshadow last:border-0">
                            <td class="px-4 py-4 font-semibold leading-5">{{ item.title }}</td>
                            <td class="px-4 py-4 text-desa-secondary">{{ item.category }}</td>
                            <td class="px-4 py-4 text-desa-secondary">Rp {{ Number(item.amount ?? 0).toLocaleString('id-ID') }}</td>
                            <td class="px-4 py-4 text-desa-secondary">{{ item.provider }}</td>
                            <td class="px-4 py-4">
                                <span class="rounded-full px-3 py-1 text-xs font-semibold text-white"
                                :class="item.is_available ? 'bg-desa-soft-green' : 'bg-desa-red'">
                                {{ item.is_available ? 'Tersedia' : 'Habis' }}
                            </span>
                        </td>
                        <td class="px-4 py-4">
                            <router-link :to="`/staff/social-assistances/${item.id}`"
                                class="rounded-2xl px-6 py-[14px] bg-desa-black font-medium leading-5 text-white text-sm inline-block">
                                Detail
                            </router-link>
                        </td>
                    </tr>
                    <tr v-if="!items.length">
                        <td colspan="6" class="px-4 py-12 text-center text-desa-secondary font-medium">Belum ada bantuan sosial</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div v-if="showModal" class="fixed inset-0 z-50 flex bg-black/50" @click.self="closeModal">
        <div class="flex flex-col w-full max-w-lg mx-4 rounded-3xl bg-white max-h-[90vh] overflow-y-auto my-auto">
            <div class="flex items-center justify-between p-6 pb-0">
                <h3 class="font-semibold text-lg">Tambah Bantuan Sosial</h3>
                <button @click="closeModal" class="flex size-8 items-center justify-center rounded-full hover:bg-desa-background transition-setup">
                    <svg class="size-5 text-desa-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form @submit.prevent="save" class="flex flex-col gap-4 p-6">
                <div class="flex flex-col gap-2">
                    <label class="font-medium text-sm text-desa-secondary">Judul</label>
                    <input v-model="form.title" :class="fieldClass()" placeholder="Judul bantuan sosial" required>
                </div>
                <div class="flex flex-col gap-2">
                    <label class="font-medium text-sm text-desa-secondary">Kategori</label>
                    <div class="relative">
                        <select v-model="form.category" :class="selectClass()" required>
                            <option value="" disabled>Pilih kategori</option>
                            <option v-for="opt in categoryOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                        </select>
                        <svg class="absolute right-4 top-1/2 -translate-y-1/2 size-5 text-desa-secondary pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
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
                    <textarea v-model="form.description" :class="fieldClass() + ' h-[100px] resize-none pt-3'" placeholder="Deskripsi" required></textarea>
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
