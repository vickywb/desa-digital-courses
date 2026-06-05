<script setup>
import { ref, onMounted } from 'vue';
import { useAuthStore } from '../../stores/auth';
import client from '../../api/client';

const authStore = useAuthStore();
const headOfFamily = ref(null);
const members = ref([]);

onMounted(async () => {
    try {
        const me = await client.get('/auth/me');
        const user = me.data.data.user;
        const hf = user.head_of_family;

        if (hf) {
            headOfFamily.value = hf;

            const res = await client.get(`/village-staff/head-families/${hf.id}/members`);
            members.value = res.data.data ?? [];
        }
    } catch {
        headOfFamily.value = null;
    }
});

function groupedByRelation() {
    const groups = {};
    for (const m of members.value) {
        const rel = m.relation || 'Lainnya';
        if (!groups[rel]) groups[rel] = [];
        groups[rel].push(m);
    }
    return groups;
}

const relations = ['suami', 'istri', 'anak', 'orang_tua', 'mertua', 'lainnya'];
const relationLabel = (rel) => {
    const map = {
        suami: 'Suami', istri: 'Istri', anak: 'Anak',
        orang_tua: 'Orang Tua', mertua: 'Mertua', lainnya: 'Lainnya',
    };
    return map[rel] ?? rel;
};
</script>

<template>
    <div class="flex flex-col gap-6 pb-[30px]">
        <header class="flex items-center justify-between">
            <h1 class="font-semibold text-2xl">Anggota Keluarga</h1>
        </header>

        <section v-if="headOfFamily" class="flex flex-col gap-6 p-6 bg-white rounded-3xl">
            <h2 class="font-medium leading-5 text-desa-secondary">Kepala Keluarga (1)</h2>
            <div class="data rounded-2xl border border-desa-background p-6 flex justify-between items-center">
                <div class="name flex items-center gap-3 min-w-[240px]">
                    <div class="flex size-[64px] shrink-0 rounded-full overflow-hidden bg-desa-foreshadow">
                        <img :src="headOfFamily.profile_picture" class="w-full h-full object-cover" alt="photo">
                    </div>
                    <div class="flex flex-col gap-[6px]">
                        <h3 class="font-semibold text-lg leading-[22.5px]">{{ headOfFamily.full_name }}</h3>
                        <p class="flex items-center gap-1">
                            <img src="/desa-digital/src/assets/images/icons/briefcase-secondary-green.svg" alt="icon" class="size-[18px] shrink-0">
                            <span class="font-medium leading-5 text-desa-secondary">{{ headOfFamily.occupation }}</span>
                        </p>
                    </div>
                </div>
                <div class="nik flex flex-col gap-[6px] min-w-[155px]">
                    <div class="flex items-center gap-1">
                        <img src="/desa-digital/src/assets/images/icons/keyboard-secondary-green.svg" alt="icon" class="size-[18px] shrink-0">
                        <span class="font-medium text-sm text-desa-secondary">NIK</span>
                    </div>
                    <p class="font-semibold leading-5">{{ headOfFamily.identity_number }}</p>
                </div>
                <div class="umur flex flex-col gap-[6px] min-w-[92px]">
                    <div class="flex items-center gap-1">
                        <img src="/desa-digital/src/assets/images/icons/timer-secondary-green.svg" alt="icon" class="size-[18px] shrink-0">
                        <span class="font-medium text-sm text-desa-secondary">Usia</span>
                    </div>
                    <p class="font-semibold leading-5">{{ headOfFamily.date_of_birth ? new Date().getFullYear() - new Date(headOfFamily.date_of_birth).getFullYear() : '-' }} Tahun</p>
                </div>
            </div>
        </section>

        <template v-for="rel in relations" :key="rel">
            <section v-if="groupedByRelation()[rel]?.length" class="flex flex-col gap-6 p-6 bg-white rounded-3xl">
                <h2 class="font-medium leading-5 text-desa-secondary">{{ relationLabel(rel) }} ({{ groupedByRelation()[rel].length }})</h2>
                <div v-for="m in groupedByRelation()[rel]" :key="m.id"
                class="data rounded-2xl border border-desa-background p-6 flex items-center justify-between">
                <div class="name flex items-center gap-3 min-w-[240px]">
                    <div class="flex size-[64px] shrink-0 rounded-full overflow-hidden bg-desa-foreshadow">
                        <img :src="m.profile_picture" class="w-full h-full object-cover" alt="photo">
                    </div>
                    <div class="flex flex-col gap-[6px]">
                        <h3 class="font-semibold text-lg leading-[22.5px]">{{ m.full_name }}</h3>
                        <p class="flex items-center gap-1">
                            <img src="/desa-digital/src/assets/images/icons/briefcase-secondary-green.svg" alt="icon" class="size-[18px] shrink-0">
                            <span class="font-medium leading-5 text-desa-secondary">{{ m.occupation }}</span>
                        </p>
                    </div>
                </div>
                <div class="nik flex flex-col gap-[6px] min-w-[155px]">
                    <div class="flex items-center gap-1">
                        <img src="/desa-digital/src/assets/images/icons/keyboard-secondary-green.svg" alt="icon" class="size-[18px] shrink-0">
                        <span class="font-medium text-sm text-desa-secondary">NIK</span>
                    </div>
                    <p class="font-semibold leading-5">{{ m.identity_number }}</p>
                </div>
                <div class="umur flex flex-col gap-[6px] min-w-[92px]">
                    <div class="flex items-center gap-1">
                        <img src="/desa-digital/src/assets/images/icons/timer-secondary-green.svg" alt="icon" class="size-[18px] shrink-0">
                        <span class="font-medium text-sm text-desa-secondary">Usia</span>
                    </div>
                    <p class="font-semibold leading-5">{{ m.date_of_birth ? new Date().getFullYear() - new Date(m.date_of_birth).getFullYear() : '-' }} Tahun</p>
                </div>
            </div>
        </section>
    </template>
</div>
</template>
