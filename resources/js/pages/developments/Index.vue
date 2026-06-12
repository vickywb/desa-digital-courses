<script setup>
import { ref, onMounted } from 'vue';
import client from '../../api/client';

const items = ref([]);
const showModal = ref(false);
const saving = ref(false);

const form = ref({
    title: '',
    description: '',
    person_in_charge: '',
    amount: '',
    start_date: '',
    end_date: '',
    status: 'planned',
    image: null,
});

const statusClass = (status) => {
    const map = {
        planned: 'bg-desa-yellow',
        ongoing: 'bg-desa-soft-green',
        completed: 'bg-desa-dark-green',
    };
    return map[status] ?? 'bg-desa-grey';
};

onMounted(async () => {
    try {
        const res = await client.get('/village-staff/developments');
        items.value = res.data.data ?? [];
    } catch {
        items.value = [];
    }
});

function openModal() {
    form.value = {
        title: '', description: '', person_in_charge: '',
        amount: '', start_date: '', end_date: '',
        status: 'planned', image: null,
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
        payload.append('person_in_charge', form.value.person_in_charge);
        payload.append('amount', form.value.amount);
        payload.append('start_date', form.value.start_date + ' 00:00:00');
        payload.append('end_date', form.value.end_date + ' 23:59:59');
        payload.append('status', form.value.status);
        if (form.value.image) payload.append('image', form.value.image);
        const res = await client.post('/village-staff/developments', payload, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });
        items.value.unshift(res.data.data?.[0] ?? {});
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
            <h1 class="font-semibold text-2xl">Pembangunan Desa</h1>
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
                            <th class="px-4 py-4 font-medium text-desa-secondary text-sm">PIC</th>
                            <th class="px-4 py-4 font-medium text-desa-secondary text-sm">Anggaran</th>
                            <th class="px-4 py-4 font-medium text-desa-secondary text-sm">Mulai</th>
                            <th class="px-4 py-4 font-medium text-desa-secondary text-sm">Status</th>
                            <th class="px-4 py-4 font-medium text-desa-secondary text-sm">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in items" :key="item.id" class="border-b border-desa-foreshadow last:border-0">
                            <td class="px-4 py-4 font-semibold leading-5">{{ item.title }}</td>
                            <td class="px-4 py-4 text-desa-secondary">{{ item.person_in_charge }}</td>
                            <td class="px-4 py-4 text-desa-secondary">Rp {{ Number(item.amount ?? 0).toLocaleString('id-ID') }}</td>
                            <td class="px-4 py-4 text-desa-secondary">{{ item.start_date ? new Date(item.start_date).toLocaleDateString('id-ID') : '-' }}</td>
                            <td class="px-4 py-4">
                                <span class="rounded-full px-3 py-1 text-xs font-semibold text-white" :class="statusClass(item.status)">
                                    {{ item.status }}
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                <router-link :to="`/staff/developments/${item.id}`"
                                    class="rounded-2xl px-6 py-[14px] bg-desa-black font-medium leading-5 text-white text-sm inline-block">
                                    Detail
                                </router-link>
                            </td>
                        </tr>
                        <tr v-if="!items.length">
                            <td colspan="6" class="px-4 py-12 text-center text-desa-secondary font-medium">Belum ada pembangunan desa</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div v-if="showModal" class="fixed inset-0 z-50 flex bg-black/50" @click.self="closeModal">
        <div class="flex flex-col w-full max-w-lg mx-4 rounded-3xl bg-white max-h-[90vh] overflow-y-auto my-auto">
            <div class="flex items-center justify-between p-6 pb-0">
                <h3 class="font-semibold text-lg">Tambah Pembangunan</h3>
                <button @click="closeModal" class="flex size-8 items-center justify-center rounded-full hover:bg-desa-background transition-setup">
                    <svg class="size-5 text-desa-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form @submit.prevent="save" class="flex flex-col gap-4 p-6">
                <div class="flex flex-col gap-2">
                    <label class="font-medium text-sm text-desa-secondary">Judul</label>
                    <input v-model="form.title" :class="fieldClass()" placeholder="Judul pembangunan" required>
                </div>
                <div class="flex flex-col gap-2">
                    <label class="font-medium text-sm text-desa-secondary">Deskripsi</label>
                    <textarea v-model="form.description" :class="fieldClass() + ' h-[100px] resize-none pt-3'" placeholder="Deskripsi" required></textarea>
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
</template>
