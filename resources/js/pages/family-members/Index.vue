<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import client from '../../api/client';

const route = useRoute();
const items = ref([]);
const headOfFamily = ref(null);
const showModal = ref(false);
const editMode = ref(''); // 'head_of_family' | 'family_member'
const editItem = ref(null);
const saving = ref(false);
const form = ref({
    full_name: '',
    identity_number: '',
    phone_number: '',
    occupation: '',
    date_of_birth: '',
    gender: '',
    marital_status: '',
    email: '',
    relation: '',
});

const genderOptions = [
    { value: 'male', label: 'Male' },
    { value: 'female', label: 'Female' },
];

const maritalOptions = [
    { value: 'single', label: 'Single' },
    { value: 'married', label: 'Married' },
    { value: 'widow', label: 'Widow' },
    { value: 'widower', label: 'Widower' },
];

const relationOptions = [
    { value: 'child', label: 'Child' },
    { value: 'wife', label: 'Wife' },
    { value: 'head', label: 'Head of Family' },
];

const relationLabel = (v) => ({ child: 'Child', wife: 'Wife', head: 'Head of Family' })[v] ?? v;
const maritalLabel = (v) => ({ single: 'Single', married: 'Married', widow: 'Widow', widower: 'Widower' })[v] ?? v;

onMounted(async () => {
    try {
        const [res, hfRes] = await Promise.all([
            client.get(`/village-staff/head-families/${route.params.id}/members`),
            client.get(`/village-staff/head-families/${route.params.id}`),
        ]);
        items.value = res.data.data ?? [];
        headOfFamily.value = hfRes.data.data ?? null;
    } catch {
        items.value = [];
        headOfFamily.value = null;
    }
});

function openEditMember(item) {
    editMode.value = 'family_member';
    editItem.value = item;
    form.value = {
        full_name: item.full_name ?? '',
        identity_number: item.identity_number ?? '',
        phone_number: item.phone_number ?? '',
        occupation: item.occupation ?? '',
        date_of_birth: item.date_of_birth ? item.date_of_birth.slice(0, 10) : '',
        gender: item.gender ?? '',
        marital_status: item.marital_status ?? '',
        email: item.email ?? '',
        relation: item.relation ?? '',
    };
    showModal.value = true;
}

function openEditHeadOfFamily() {
    editMode.value = 'head_of_family';
    editItem.value = headOfFamily.value;
    form.value = {
        full_name: headOfFamily.value.full_name ?? '',
        identity_number: headOfFamily.value.identity_number ?? '',
        phone_number: headOfFamily.value.phone_number ?? '',
        occupation: headOfFamily.value.occupation ?? '',
        date_of_birth: headOfFamily.value.date_of_birth ? headOfFamily.value.date_of_birth.slice(0, 10) : '',
        gender: headOfFamily.value.gender ?? '',
        marital_status: headOfFamily.value.marital_status ?? '',
        email: '',
        relation: '',
    };
    showModal.value = true;
}

function openCreate() {
    editMode.value = 'create';
    editItem.value = null;
    form.value = {
        full_name: '',
        identity_number: '',
        phone_number: '',
        occupation: '',
        date_of_birth: '',
        gender: '',
        marital_status: '',
        email: '',
        relation: '',
    };
    showModal.value = true;
}

function closeModal() {
    showModal.value = false;
    editMode.value = '';
    editItem.value = null;
}

async function deleteMember(item) {
    if (!confirm('Delete this family member?')) return;

    try {
        await client.delete(`/village-staff/head-families/${route.params.id}/members/${item.id}`);
        items.value = items.value.filter((m) => m.id !== item.id);
    } catch (err) {
        const msg = err.response?.data?.message ?? 'Failed to delete';
        alert(msg);
    }
}

async function saveEdit() {
    saving.value = true;
    try {
        const payload = {
            full_name: form.value.full_name,
            identity_number: form.value.identity_number,
            phone_number: form.value.phone_number,
            occupation: form.value.occupation,
            date_of_birth: form.value.date_of_birth,
            gender: form.value.gender,
            marital_status: form.value.marital_status,
        };

        if (editMode.value === 'create') {
            payload.email = form.value.email;
            payload.relation = form.value.relation;

            await client.post(
                `/village-staff/head-families/${route.params.id}/members`,
                payload
            );

            const [res] = await Promise.all([
                client.get(`/village-staff/head-families/${route.params.id}/members`),
            ]);
            items.value = res.data.data ?? [];
        } else if (editMode.value === 'family_member') {
            payload.email = form.value.email;
            payload.relation = form.value.relation;

            const res = await client.put(
                `/village-staff/head-families/${route.params.id}/members/${editItem.value.id}`,
                payload
            );

            const updated = res.data.data;
            const idx = items.value.findIndex((m) => m.id === updated.id);
            if (idx !== -1) items.value[idx] = updated;
        } else {
            const res = await client.put(
                `/village-staff/head-families/${route.params.id}`,
                payload
            );

            headOfFamily.value = res.data.data;
        }

        closeModal();
    } catch (err) {
        const msg = err.response?.data?.message ?? 'Failed to save data';
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
            <router-link to="/head-families" class="flex items-center gap-1 font-medium text-desa-dark-green hover:underline">
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Back
            </router-link>
            <span class="text-desa-secondary">/</span>
            <span class="font-medium text-desa-secondary">Family Members</span>
        </div>

        <div v-if="headOfFamily" class="rounded-3xl bg-white p-6">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="flex size-16 rounded-full overflow-hidden bg-desa-foreshadow shrink-0">
                        <img :src="headOfFamily.profile_picture?.url ?? '/desa-digital/src/assets/images/icons/crown-foreshadow-background.svg'"
                            class="w-full h-full object-cover" alt="photo">
                    </div>
                    <div class="flex flex-col gap-1">
                        <h2 class="font-semibold text-xl">{{ headOfFamily.full_name }}</h2>
                        <div class="flex flex-wrap gap-x-4 gap-y-1 text-sm text-desa-secondary">
                            <span v-if="headOfFamily.identity_number">ID: {{ headOfFamily.identity_number }}</span>
                            <span v-if="headOfFamily.gender">{{ headOfFamily.gender === 'male' ? 'Male' : 'Female' }}</span>
                            <span v-if="headOfFamily.occupation">{{ headOfFamily.occupation }}</span>
                            <span v-if="headOfFamily.marital_status">{{ maritalLabel(headOfFamily.marital_status) }}</span>
                        </div>
                    </div>
                </div>
                <button @click="openEditHeadOfFamily"
                    class="flex items-center gap-2 rounded-2xl px-6 py-3 bg-desa-black text-white font-medium hover:bg-desa-dark-green transition-setup shrink-0">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit Head of Family
                </button>
            </div>
        </div>

        <div class="rounded-3xl bg-white p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-lg">Family Members</h3>
                <button @click="openCreate"
                    class="flex items-center gap-2 rounded-2xl px-4 py-[10px] bg-desa-dark-green text-white font-semibold text-sm hover:bg-desa-black transition-setup">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Member
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-desa-background">
                            <th class="px-4 py-4 font-medium text-desa-secondary text-sm">Name</th>
                            <th class="px-4 py-4 font-medium text-desa-secondary text-sm">ID Number</th>
                            <th class="px-4 py-4 font-medium text-desa-secondary text-sm">Relation</th>
                            <th class="px-4 py-4 font-medium text-desa-secondary text-sm">Gender</th>
                            <th class="px-4 py-4 font-medium text-desa-secondary text-sm">Occupation</th>
                            <th class="px-4 py-4 font-medium text-desa-secondary text-sm">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in items" :key="item.id" class="border-b border-desa-foreshadow last:border-0">
                            <td class="px-4 py-4 font-semibold leading-5">{{ item.full_name }}</td>
                            <td class="px-4 py-4 text-desa-secondary">{{ item.identity_number }}</td>
                            <td class="px-4 py-4">{{ relationLabel(item.relation) }}</td>
                            <td class="px-4 py-4">{{ item.gender === 'male' ? 'Male' : 'Female' }}</td>
                            <td class="px-4 py-4 text-desa-secondary">{{ item.occupation }}</td>
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-2">
                                    <button @click="openEditMember(item)"
                                        class="rounded-2xl px-4 py-[10px] bg-desa-black font-medium leading-5 text-white text-sm hover:bg-desa-dark-green transition-setup">
                                        Edit
                                    </button>
                                    <button @click="deleteMember(item)"
                                        class="rounded-2xl px-4 py-[10px] border border-red-500 text-red-500 font-medium leading-5 text-sm hover:bg-red-500 hover:text-white transition-setup">
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!items.length">
                            <td colspan="6" class="px-4 py-12 text-center text-desa-secondary font-medium">No family members yet</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div v-if="showModal" class="fixed inset-0 z-50 flex bg-black/50" @click.self="closeModal">
        <div class="flex flex-col w-full max-w-lg mx-4 rounded-3xl bg-white max-h-[90vh] overflow-y-auto my-auto">
            <div class="flex items-center justify-between p-6 pb-0">
                <h3 class="font-semibold text-lg">
                    {{ editMode === 'create' ? 'Add Family Member' : editMode === 'head_of_family' ? 'Edit Head of Family' : 'Edit Family Member' }}
                </h3>
                <button @click="closeModal" class="flex size-8 items-center justify-center rounded-full hover:bg-desa-background transition-setup">
                    <svg class="size-5 text-desa-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form @submit.prevent="saveEdit" class="flex flex-col gap-4 p-6">
                <div class="flex flex-col gap-2">
                    <label class="font-medium text-sm text-desa-secondary">Full Name</label>
                    <input v-model="form.full_name" :class="fieldClass()" placeholder="Full name" required>
                </div>

                <div class="flex flex-col gap-2">
                    <label class="font-medium text-sm text-desa-secondary">ID Number</label>
                    <input v-model="form.identity_number" :class="fieldClass()" placeholder="National ID number">
                </div>

                <div v-if="editMode === 'family_member'" class="flex flex-col gap-2">
                    <label class="font-medium text-sm text-desa-secondary">Email</label>
                    <input v-model="form.email" type="email" :class="fieldClass()" placeholder="Email">
                </div>

                <div class="flex flex-col gap-2">
                    <label class="font-medium text-sm text-desa-secondary">Phone Number</label>
                    <input v-model="form.phone_number" :class="fieldClass()" placeholder="Phone number">
                </div>

                <div class="flex flex-col gap-2">
                    <label class="font-medium text-sm text-desa-secondary">Occupation</label>
                    <input v-model="form.occupation" :class="fieldClass()" placeholder="Occupation">
                </div>

                <div class="flex flex-col gap-2">
                    <label class="font-medium text-sm text-desa-secondary">Date of Birth</label>
                    <input v-model="form.date_of_birth" type="date" :class="fieldClass()">
                </div>

                <div class="flex flex-col gap-2">
                    <label class="font-medium text-sm text-desa-secondary">Gender</label>
                    <div class="relative">
                        <select v-model="form.gender" :class="selectClass()">
                            <option value="" disabled>Select gender</option>
                            <option v-for="opt in genderOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                        </select>
                        <svg class="absolute right-4 top-1/2 -translate-y-1/2 size-5 text-desa-secondary pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>

                <div class="flex flex-col gap-2">
                    <label class="font-medium text-sm text-desa-secondary">Marital Status</label>
                    <div class="relative">
                        <select v-model="form.marital_status" :class="selectClass()">
                            <option value="" disabled>Select status</option>
                            <option v-for="opt in maritalOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                        </select>
                        <svg class="absolute right-4 top-1/2 -translate-y-1/2 size-5 text-desa-secondary pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>

                <div v-if="editMode === 'family_member'" class="flex flex-col gap-2">
                    <label class="font-medium text-sm text-desa-secondary">Relation</label>
                    <div class="relative">
                        <select v-model="form.relation" :class="selectClass()">
                            <option value="" disabled>Select relation</option>
                            <option v-for="opt in relationOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                        </select>
                        <svg class="absolute right-4 top-1/2 -translate-y-1/2 size-5 text-desa-secondary pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="button" @click="closeModal"
                        class="flex items-center justify-center h-14 rounded-2xl px-8 border border-desa-background font-semibold text-sm hover:bg-desa-black hover:text-white transition-setup flex-1">
                        Cancel
                    </button>
                    <button type="submit"
                        class="flex items-center justify-center h-14 rounded-2xl px-8 bg-desa-dark-green text-white font-semibold text-sm hover:bg-desa-black transition-setup flex-1"
                        :disabled="saving">
                        {{ saving ? 'Saving...' : 'Save' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
