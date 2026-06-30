<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import client from '../../api/client';
import { formatRupiah, formatToClientTimezone } from '../../helpers/format';
import ConfirmModal from '@/components/ui/ConfirmModal.vue';

const route = useRoute();
const router = useRouter();
const item = ref(null);
const loading = ref(true);
const saving = ref(false);
const confirmState = ref({ show: false, title: '', message: '', variant: 'danger', onConfirm: null });

function openConfirm(title, message, variant, onConfirm) {
    confirmState.value = { show: true, title, message, variant, onConfirm };
}

const hasJoined = ref(false);
const myParticipation = ref(null);

const familyMembers = ref([]);
const selectedMemberIds = ref([]);
const proofFile = ref(null);

const quantity = computed(() => selectedMemberIds.value.length);

onMounted(async () => {
    try {
        const [eventRes, myRes, familyRes] = await Promise.all([
            client.get(`/village-resident/events/${route.params.id}`),
            client.get('/village-resident/my-event-participants'),
            client.get('/village-resident/family-members'),
        ]);
        item.value = eventRes.data.data ?? null;
        familyMembers.value = familyRes.data.data ?? [];

        const myData = myRes.data.data ?? [];
        const found = myData.find(p => p.event?.id === route.params.id);
        if (found) {
            hasJoined.value = true;
            myParticipation.value = found;
        }
    } catch {
        item.value = null;
    } finally {
        loading.value = false;
    }
});

function toggleMember(id) {
    const idx = selectedMemberIds.value.indexOf(id);
    if (idx >= 0) {
        selectedMemberIds.value.splice(idx, 1);
    } else {
        selectedMemberIds.value.push(id);
    }
}

function onProofChange(e) {
    proofFile.value = e.target.files[0] ?? null;
}

async function purchase() {
    saving.value = true;
    try {
        const payload = new FormData();
        payload.append('member_ids', JSON.stringify(selectedMemberIds.value));
        payload.append('quantity', String(quantity.value));
        payload.append('payment_status', Number(item.value.price) > 0 ? 'pending' : 'free');
        if (proofFile.value) {
            payload.append('proof', proofFile.value);
        }

        const res = await client.post(`/village-resident/events/${route.params.id}/participants`, payload);

        hasJoined.value = true;
        myParticipation.value = res.data.data ?? null;
        selectedMemberIds.value = [];
        proofFile.value = null;
    } catch (err) {
        const data = err.response?.data;
        const msg = data?.message ?? 'Gagal mendaftar';
        const details = data?.errors ? Object.values(data.errors).flat().join('\n') : '';
        alert(msg + (details ? '\n\n' + details : ''));
    } finally {
        saving.value = false;
    }
}

function cancel() {
    openConfirm(
        'Batalkan Partisipasi',
        'Yakin ingin membatalkan partisipasi?',
        'danger',
        async () => {
            saving.value = true;
            try {
                await client.delete(`/village-resident/events/${route.params.id}/participants/${myParticipation.value.id}`);
                hasJoined.value = false;
                myParticipation.value = null;
            } catch (err) {
                const msg = err.response?.data?.message ?? 'Gagal membatalkan';
                alert(msg);
            } finally {
                saving.value = false;
            }
        }
    );
}

function getMemberName(member) {
    if (member.member_type === 'head_of_family') return 'Kepala Keluarga';
    return member.family_member?.full_name ?? '-';
}
</script>

<template>
    <div class="flex flex-col gap-[14px]">
        <div class="flex items-center gap-2">
            <router-link to="/warga/events" class="flex items-center gap-1 font-medium text-desa-dark-green hover:underline">
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
            <router-link to="/warga/events" class="text-desa-dark-green hover:underline font-medium">Kembali</router-link>
        </div>

        <div v-else class="flex gap-[14px] flex-col lg:flex-row">
            <div class="w-full lg:w-[545px] shrink-0 rounded-2xl bg-white p-4 sm:p-6 flex flex-col gap-6 h-fit">
                <h2 class="font-medium text-sm leading-[17.5px] text-desa-secondary">Informasi Event</h2>

                <section class="flex items-center justify-between">
                    <div class="flex justify-center items-center w-[120px] h-[100px] shrink-0 rounded-2xl overflow-hidden bg-desa-foreshadow">
                        <img :src="item.event_file?.url" alt="image" class="size-full object-cover" />
                    </div>
                    <span class="rounded-full py-[12px] w-[111px] flex justify-center text-white font-semibold text-xs leading-[15px] shrink-0"
                        :class="item.is_active ? 'bg-desa-soft-green' : 'bg-desa-red'">
                        {{ item.is_active ? 'DIBUKA' : 'DITUTUP' }}
                    </span>
                </section>

                <section class="flex flex-col gap-[6px]">
                    <h3 class="font-semibold text-lg leading-[22.5px]">{{ item.title }}</h3>
                    <div class="flex items-center gap-1">
                        <p class="font-medium text-sm leading-[17.5px] text-desa-secondary">
                            {{ item.is_active ? 'Pendaftaran Dibuka' : 'Pendaftaran Ditutup' }}
                        </p>
                    </div>
                </section>

                <hr class="border-desa-background" />

                <section class="flex flex-col gap-6">
                    <div class="point flex items-center gap-3">
                        <div class="p-[14px] shrink-0 bg-desa-foreshadow rounded-2xl">
                            <img src="@/assets/images/icons/money-dark-green.svg" alt="icon" class="size-6 shrink-0" />
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="font-semibold text-lg leading-[22.5px] text-desa-dark-green">
                                {{ item.price && Number(item.price) > 0 ? 'Rp ' + formatRupiah(item.price) : 'Gratis' }}
                            </p>
                            <h3 class="font-medium text-sm leading-[17.5px] text-desa-secondary">Harga per orang</h3>
                        </div>
                    </div>
                    <hr class="border-desa-background" />
                    <div class="point flex items-center gap-3">
                        <div class="p-[14px] shrink-0 bg-desa-foreshadow rounded-2xl">
                            <img src="@/assets/images/icons/calendar-2-dark-green.svg" alt="icon" class="size-6 shrink-0" />
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="font-semibold text-lg leading-[22.5px]">{{ formatToClientTimezone(item.start_date) }}</p>
                            <h3 class="font-medium text-sm leading-[17.5px] text-desa-secondary">Tanggal</h3>
                        </div>
                    </div>
                </section>

                <hr class="border-desa-background" />

                <section class="flex flex-col gap-3">
                    <h2 class="font-medium text-sm leading-[17.5px] text-desa-secondary">Tentang Event</h2>
                    <p class="font-medium leading-8">{{ item.description }}</p>
                </section>
            </div>

            <div class="flex-1 flex flex-col gap-[14px]">
                <div class="rounded-2xl bg-white p-4 sm:p-6 flex flex-col gap-6">
                    <h2 class="font-semibold leading-5">Detail Pembelian</h2>

                    <div v-if="hasJoined" class="flex flex-col items-center gap-4 py-6">
                        <img src="@/assets/images/icons/tick-square-dark-green.svg" class="size-12" alt="icon" />
                        <p class="font-semibold text-lg text-desa-dark-green">Kamu Sudah Bergabung</p>
                        <div class="w-full flex flex-col gap-3">
                            <div class="flex justify-between">
                                <span class="font-medium text-sm text-desa-secondary">Peserta</span>
                                <span class="font-semibold text-right text-sm">
                                    <div v-for="m in (myParticipation?.members ?? [])" :key="m.id">
                                        {{ getMemberName(m) }}
                                    </div>
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-sm text-desa-secondary">Jumlah Tiket</span>
                                <span class="font-semibold">{{ myParticipation?.quantity }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-sm text-desa-secondary">Total</span>
                                <span class="font-semibold text-desa-dark-green">Rp {{ formatRupiah(myParticipation?.total_price) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-sm text-desa-secondary">Status</span>
                                <span class="rounded-full px-3 py-1 text-xs font-semibold text-white"
                                    :class="myParticipation?.payment_status === 'paid' ? 'bg-desa-soft-green' : 'bg-desa-yellow'">
                                    {{ myParticipation?.payment_status === 'paid' ? 'Lunas' : 'Pending' }}
                                </span>
                            </div>
                            <div v-if="myParticipation?.proof" class="flex flex-col gap-2">
                                <span class="font-medium text-sm text-desa-secondary">Bukti Bayar</span>
                                <a :href="myParticipation.proof.url" target="_blank" class="text-desa-dark-green underline text-sm">
                                    Lihat Bukti
                                </a>
                            </div>
                        </div>
                        <button @click="cancel" :disabled="saving"
                            class="rounded-2xl border border-desa-red py-[18px] px-6 font-medium leading-5 text-desa-red hover:bg-desa-red hover:text-white transition-setup w-full mt-2">
                            {{ saving ? 'Memproses...' : 'Batalkan Partisipasi' }}
                        </button>
                    </div>

                    <div v-else>
                        <div class="flex flex-col gap-4">
                            <div class="flex justify-between items-center">
                                <span class="font-medium text-sm text-desa-secondary">Harga per Orang</span>
                                <span class="font-semibold">
                                    {{ item.price && Number(item.price) > 0 ? 'Rp ' + formatRupiah(item.price) : 'Gratis' }}
                                </span>
                            </div>

                            <div class="flex flex-col gap-2">
                                <span class="font-medium text-sm text-desa-secondary">Pilih Peserta</span>
                                <div v-if="familyMembers.length" class="flex flex-col gap-2 max-h-48 overflow-y-auto">
                                    <label class="flex items-center gap-3 p-3 rounded-2xl border border-desa-background cursor-pointer hover:bg-desa-foreshadow transition-setup"
                                        :class="{ 'border-desa-dark-green bg-desa-soft-green/10': selectedMemberIds.includes('head') }">
                                        <input type="checkbox" :checked="selectedMemberIds.includes('head')"
                                            @change="toggleMember('head')" class="accent-desa-dark-green size-4 shrink-0">
                                        <span class="font-medium text-sm">Kepala Keluarga</span>
                                    </label>
                                    <label v-for="fm in familyMembers" :key="fm.id"
                                        class="flex items-center gap-3 p-3 rounded-2xl border border-desa-background cursor-pointer hover:bg-desa-foreshadow transition-setup"
                                        :class="{ 'border-desa-dark-green bg-desa-soft-green/10': selectedMemberIds.includes(fm.id) }">
                                        <input type="checkbox" :checked="selectedMemberIds.includes(fm.id)"
                                            @change="toggleMember(fm.id)" class="accent-desa-dark-green size-4 shrink-0">
                                        <span class="font-medium text-sm">{{ fm.full_name }}</span>
                                    </label>
                                </div>
                                <p v-else class="text-sm text-desa-secondary">Tidak ada anggota keluarga</p>
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="font-medium text-sm text-desa-secondary">Jumlah Peserta</span>
                                <span class="font-semibold">{{ quantity || 0 }} orang</span>
                            </div>

                            <hr class="border-desa-background" />

                            <div class="flex justify-between items-center">
                                <span class="font-semibold">Total</span>
                                <span class="font-semibold text-desa-dark-green text-lg">
                                    {{ item.price && Number(item.price) > 0 ? 'Rp ' + formatRupiah(Number(item.price) * quantity) : 'Gratis' }}
                                </span>
                            </div>

                            <div v-if="Number(item.price) > 0" class="flex flex-col gap-2">
                                <span class="font-medium text-sm text-desa-secondary">Upload Bukti Bayar</span>
                                <p class="text-xs text-desa-secondary">Scan QRIS atau transfer ke rekening desa, lalu upload bukti pembayaran</p>
                                <input type="file" accept="image/*" @change="onProofChange"
                                    class="w-full text-sm text-desa-secondary file:mr-4 file:py-2 file:px-4 file:rounded-2xl file:border-0 file:bg-desa-foreshadow file:font-medium file:text-desa-dark-green hover:file:bg-desa-soft-green transition-setup">
                            </div>

                            <button @click="purchase" :disabled="saving || !item.is_active || !quantity"
                                class="bg-desa-black rounded-2xl w-full py-[18px] flex justify-center items-center font-medium leading-5 text-white text-center hover:bg-desa-dark-green transition-setup mt-2 disabled:opacity-50">
                                {{ saving ? 'Memproses...' : (Number(item.price) > 0 ? 'Daftar & Upload Bukti' : 'Daftar Gratis') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <ConfirmModal
        :show="confirmState.show"
        :title="confirmState.title"
        :message="confirmState.message"
        :variant="confirmState.variant"
        :loading="saving"
        @close="confirmState.show = false"
        @confirm="confirmState.onConfirm?.()"
    />
</template>
