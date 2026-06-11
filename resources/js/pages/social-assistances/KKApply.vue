<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import client from '../../api/client';
import { formatRupiah } from '../../helpers/format';

const route = useRoute();
const router = useRouter();
const item = ref(null);
const loading = ref(true);
const saving = ref(false);
const error = ref('');

const form = ref({
    bank: '',
    account_number: '',
    amount: '',
    reason: '',
    proof: null,
});

onMounted(async () => {
    try {
        const res = await client.get(`/village-resident/social-assistances/${route.params.id}`);
        item.value = res.data.data ?? null;
    } catch {
        item.value = null;
    } finally {
        loading.value = false;
    }
});

function onProofChange(e) {
    form.value.proof = e.target.files[0] ?? null;
}

async function submitForm() {
    saving.value = true;
    error.value = '';

    try {
        const payload = new FormData();
        payload.append('bank', form.value.bank);
        payload.append('account_number', form.value.account_number);
        payload.append('amount', form.value.amount);
        payload.append('reason', form.value.reason);
        if (form.value.proof) {
            payload.append('proof', form.value.proof);
        }

        await client.post(
            `/village-resident/social-assistances/${route.params.id}/recipients`,
            payload,
            { headers: { 'Content-Type': 'multipart/form-data' } }
        );

        router.push('/social-assistances/my-recipients');
    } catch (err) {
        const data = err.response?.data;
        if (data?.errors) {
            const messages = Object.values(data.errors).flat();
            error.value = messages.join('\n');
        } else {
            error.value = data?.message ?? 'Gagal mengajukan bansos';
        }
    } finally {
        saving.value = false;
    }
}

function fieldClass() {
    return 'w-full h-[48px] rounded-2xl px-4 border border-desa-background font-medium leading-5 focus:ring-[1.5px] focus:ring-desa-dark-green focus:outline-none';
}

function textareaClass() {
    return 'w-full rounded-2xl p-4 border border-desa-background font-medium leading-5 focus:ring-[1.5px] focus:ring-desa-dark-green focus:outline-none resize-none';
}
</script>

<template>
    <div class="flex flex-col gap-[14px]">
        <div class="flex items-center gap-2">
            <router-link to="/social-assistances" class="flex items-center gap-1 font-medium text-desa-dark-green hover:underline">
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali
            </router-link>
            <span class="text-desa-secondary">/</span>
            <span class="font-medium text-desa-secondary">Ajukan Bantuan Sosial</span>
        </div>

        <div v-if="loading" class="flex items-center justify-center py-20">
            <p class="font-medium text-desa-secondary">Memuat...</p>
        </div>

        <div v-else-if="!item" class="flex flex-col items-center justify-center py-20 gap-4">
            <p class="font-semibold text-lg text-desa-secondary">Data tidak ditemukan</p>
            <router-link to="/social-assistances" class="text-desa-dark-green hover:underline font-medium">Kembali ke daftar</router-link>
        </div>

        <div v-else class="flex gap-[14px] flex-col lg:flex-row">
            <div class="w-full lg:w-[545px] shrink-0 rounded-2xl bg-white p-6 flex flex-col gap-6 h-fit">
                <h2 class="font-medium text-sm leading-[17.5px] text-desa-secondary">Informasi Bantuan Sosial</h2>

                <section class="flex items-center justify-between">
                    <div class="flex justify-center items-center w-[120px] h-[100px] shrink-0 rounded-2xl overflow-hidden bg-desa-foreshadow">
                        <img :src="item.social_assistance_file?.url" alt="image" class="size-full object-cover" />
                    </div>
                    <span class="rounded-full py-[12px] w-[111px] flex justify-center text-white font-semibold text-xs leading-[15px] shrink-0"
                        :class="item.is_available ? 'bg-desa-soft-green' : 'bg-desa-red'">
                        {{ item.is_available ? 'TERSEDIA' : 'HABIS' }}
                    </span>
                </section>

                <section class="flex flex-col gap-[6px]">
                    <h3 class="font-semibold text-lg leading-[22.5px]">{{ item.title }}</h3>
                    <div class="flex items-center gap-1">
                        <img src="@/assets/images/icons/profile-secondary-green-bold.svg" alt="icon" class="size-[18px] shrink-0" />
                        <p class="font-medium text-sm leading-[17.5px] text-desa-secondary">{{ item.provider }}</p>
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
                            <h3 class="font-medium text-sm leading-[17.5px] text-desa-secondary">Nominal</h3>
                        </div>
                    </div>
                    <hr class="border-desa-background" />
                    <div class="point flex items-center gap-3">
                        <div class="p-[14px] shrink-0 bg-desa-foreshadow rounded-2xl">
                            <img src="@/assets/images/icons/bag-2-dark-green.svg" alt="icon" class="size-6 shrink-0" />
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="font-semibold text-lg leading-[22.5px] text-desa-dark-green">{{ item.category }}</p>
                            <h3 class="font-medium text-sm leading-[17.5px] text-desa-secondary">Kategori</h3>
                        </div>
                    </div>
                </section>

                <hr class="border-desa-background" />

                <section class="flex flex-col gap-3">
                    <h2 class="font-medium text-sm leading-[17.5px] text-desa-secondary">Tentang Bantuan</h2>
                    <p class="font-medium leading-8">{{ item.description }}</p>
                </section>
            </div>

            <form @submit.prevent="submitForm" class="flex-1 flex flex-col gap-[14px]">
                <div v-if="error" class="rounded-2xl bg-desa-red/10 p-4">
                    <p class="font-medium text-sm text-desa-red whitespace-pre-line">{{ error }}</p>
                </div>

                <section class="rounded-2xl bg-white p-6 flex flex-col gap-6">
                    <h2 class="font-semibold leading-5">Bank Account Detail</h2>

                    <div class="flex flex-col gap-2">
                        <label class="font-medium text-sm text-desa-secondary">Pilih Bank</label>
                        <select v-model="form.bank" required :class="fieldClass() + ' bg-white appearance-none'">
                            <option value="" disabled>Pilih bank</option>
                            <option value="BCA">Bank Central Asia (BCA)</option>
                            <option value="Mandiri">Bank Mandiri</option>
                            <option value="BNI">Bank Negara Indonesia (BNI)</option>
                            <option value="BRI">Bank Rakyat Indonesia (BRI)</option>
                        </select>
                    </div>

                    <div class="flex flex-col gap-2">
                        <label class="font-medium text-sm text-desa-secondary">Nomor Rekening</label>
                        <input v-model="form.account_number" type="text" placeholder="Masukan Nomor Rekening" :class="fieldClass()" required>
                    </div>

                    <div class="flex flex-col gap-2">
                        <label class="font-medium text-sm text-desa-secondary">Nominal Pengajuan (Rp)</label>
                        <input v-model="form.amount" type="number" placeholder="Input amount" :class="fieldClass()" required>
                    </div>

                    <div class="flex flex-col gap-2">
                        <label class="font-medium text-sm text-desa-secondary">Alasan Pengajuan</label>
                        <textarea v-model="form.reason" placeholder="Jelaskan alasan pengajuan bantuan" :class="textareaClass() + ' h-[150px]'" required></textarea>
                    </div>

                    <div class="flex flex-col gap-2">
                        <label class="font-medium text-sm text-desa-secondary">Bukti Pendukung (optional)</label>
                        <input type="file" accept="image/*" @change="onProofChange"
                            class="w-full text-sm text-desa-secondary file:mr-4 file:py-2 file:px-4 file:rounded-2xl file:border-0 file:bg-desa-foreshadow file:font-medium file:text-desa-dark-green hover:file:bg-desa-soft-green transition-setup">
                    </div>
                </section>

                <div class="flex gap-3">
                    <router-link to="/social-assistances"
                        class="flex items-center justify-center h-14 rounded-2xl px-8 border border-desa-background font-semibold text-sm hover:bg-desa-black hover:text-white transition-setup flex-1">
                        Batal
                    </router-link>
                    <button type="submit"
                        class="flex items-center justify-center h-14 rounded-2xl px-8 bg-desa-black text-white font-semibold text-sm hover:bg-desa-dark-green transition-setup flex-1"
                        :disabled="saving || !item.is_available">
                        {{ saving ? 'Mengirim...' : 'Ajukan Bantuan' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
