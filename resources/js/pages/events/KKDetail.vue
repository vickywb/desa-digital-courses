<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import client from '../../api/client';
import { formatRupiah, formatToClientTimezone } from '../../helpers/format';

const route = useRoute();
const router = useRouter();
const item = ref(null);
const loading = ref(true);
const saving = ref(false);
const hasJoined = ref(false);
const myParticipation = ref(null);

const quantity = ref(1);
const ppnRate = 0.12;

const totalPrice = computed(() => {
    if (!item.value?.price || Number(item.value.price) <= 0) return 0;
    const base = Number(item.value.price) * quantity.value;
    const ppn = base * ppnRate;
    return base + ppn;
});

onMounted(async () => {
    try {
        const [eventRes, myRes] = await Promise.all([
            client.get(`/village-resident/events/${route.params.id}`),
            client.get('/village-resident/my-event-participants'),
        ]);
        item.value = eventRes.data.data ?? null;
        const myData = myRes.data.data ?? [];
        const found = myData.find(p => p.event?.id === route.params.id);
        if (found) {
            hasJoined.value = true;
            myParticipation.value = found;
            quantity.value = found.quantity;
        }
    } catch {
        item.value = null;
    } finally {
        loading.value = false;
    }
});

async function purchase() {
    saving.value = true;
    try {
        const payload = {
            quantity: quantity.value,
            total_price: String(totalPrice.value),
            payment_status: Number(item.value.price) > 0 ? 'pending' : 'free',
        };

        await client.post(`/village-resident/events/${route.params.id}/participants`, payload);
        hasJoined.value = true;
        quantity.value = 1;
    } catch (err) {
        const msg = err.response?.data?.message ?? 'Gagal membeli tiket';
        alert(msg);
    } finally {
        saving.value = false;
    }
}

async function cancel() {
    if (!confirm('Yakin ingin membatalkan partisipasi?')) return;
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
            <div class="w-full lg:w-[545px] shrink-0 rounded-2xl bg-white p-6 flex flex-col gap-6 h-fit">
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
                            <h3 class="font-medium text-sm leading-[17.5px] text-desa-secondary">Harga</h3>
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
                <div class="rounded-2xl bg-white p-6 flex flex-col gap-6">
                    <h2 class="font-semibold leading-5">Detail Pembelian</h2>

                    <div v-if="hasJoined" class="flex flex-col items-center gap-4 py-6">
                        <img src="@/assets/images/icons/tick-square-dark-green.svg" class="size-12" alt="icon" />
                        <p class="font-semibold text-lg text-desa-dark-green">Kamu Sudah Bergabung</p>
                        <div class="w-full flex flex-col gap-3">
                            <div class="flex justify-between">
                                <span class="font-medium text-sm text-desa-secondary">Jumlah Tiket</span>
                                <span class="font-semibold">{{ myParticipation?.quantity ?? quantity }}</span>
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
                        </div>
                        <button @click="cancel" :disabled="saving"
                            class="rounded-2xl border border-desa-red py-[18px] px-6 font-medium leading-5 text-desa-red hover:bg-desa-red hover:text-white transition-setup w-full mt-2">
                            {{ saving ? 'Memproses...' : 'Batalkan Partisipasi' }}
                        </button>
                    </div>

                    <div v-else>
                        <div class="flex flex-col gap-4">
                            <div class="flex justify-between items-center">
                                <span class="font-medium text-sm text-desa-secondary">Harga Tiket</span>
                                <span class="font-semibold">
                                    {{ item.price && Number(item.price) > 0 ? 'Rp ' + formatRupiah(item.price) : 'Gratis' }}
                                </span>
                            </div>

                            <div v-if="item.price && Number(item.price) > 0" class="flex justify-between items-center">
                                <span class="font-medium text-sm text-desa-secondary">Jumlah</span>
                                <div class="flex items-center gap-3">
                                    <button @click="quantity = Math.max(1, quantity - 1)" :disabled="quantity <= 1"
                                        class="flex size-8 items-center justify-center rounded-full border border-desa-background hover:bg-desa-foreshadow disabled:opacity-50">
                                        -
                                    </button>
                                    <span class="font-semibold w-8 text-center">{{ quantity }}</span>
                                    <button @click="quantity++"
                                        class="flex size-8 items-center justify-center rounded-full border border-desa-background hover:bg-desa-foreshadow">
                                        +
                                    </button>
                                </div>
                            </div>

                            <div v-if="item.price && Number(item.price) > 0" class="flex justify-between items-center">
                                <span class="font-medium text-sm text-desa-secondary">PPN 12%</span>
                                <span class="font-semibold">Rp {{ formatRupiah(Number(item.price) * quantity * ppnRate) }}</span>
                            </div>

                            <hr class="border-desa-background" />

                            <div class="flex justify-between items-center">
                                <span class="font-semibold">Total</span>
                                <span class="font-semibold text-desa-dark-green text-lg">
                                    {{ item.price && Number(item.price) > 0 ? 'Rp ' + formatRupiah(totalPrice) : 'Gratis' }}
                                </span>
                            </div>

                            <button @click="purchase" :disabled="saving || !item.is_active"
                                class="bg-desa-black rounded-2xl w-full py-[18px] flex justify-center items-center font-medium leading-5 text-white text-center hover:bg-desa-dark-green transition-setup mt-2 disabled:opacity-50">
                                {{ saving ? 'Memproses...' : (Number(item.price) > 0 ? 'Beli Tiket' : 'Daftar Gratis') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
