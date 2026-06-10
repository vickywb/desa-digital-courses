<script setup>
import { ref, onMounted } from 'vue';
import { useAuthStore } from '../../stores/auth';
import client from '../../api/client';

const authStore = useAuthStore();
const headOfFamily = ref(null);
const members = ref([]);
const showModal = ref(false);
const editMode = ref('');
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

const relationLabels = {
    wife: 'Wife',
    child: 'Child',
};

async function fetchMembers() {
    try {
        const res = await client.get('/village-resident/family-members');
        members.value = res.data.data ?? [];
    } catch {
        members.value = [];
    }
}

onMounted(async () => {
    try {
        const me = await client.get('/auth/me');
        const user = me.data.data.user;
        const hf = user.head_of_family;

        if (hf) {
            headOfFamily.value = hf;
            await fetchMembers();
        }
    } catch {
        headOfFamily.value = null;
    }
});

function groupedByRelation() {
    const groups = {};
    for (const m of members.value) {
        const rel = m.relation || 'other';
        if (!groups[rel]) groups[rel] = [];
        groups[rel].push(m);
    }
    return groups;
}

const relations = ['wife', 'child'];

const relationLabel = (rel) => relationLabels[rel] ?? rel;

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

function openEdit(item) {
    editMode.value = 'edit';
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

function closeModal() {
    showModal.value = false;
    editMode.value = '';
    editItem.value = null;
}

async function saveMember() {
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

        if (editMode.value === 'head_of_family') {
            const res = await client.put('/auth/profile', payload);
            headOfFamily.value = res.data.data;
        } else if (editMode.value === 'edit') {
            payload.email = form.value.email;
            payload.relation = form.value.relation;
            await client.put(`/village-resident/family-members/${editItem.value.id}`, payload);
        } else {
            payload.email = form.value.email;
            payload.relation = form.value.relation;
            await client.post('/village-resident/family-members', payload);
        }

        closeModal();
        await fetchMembers();
    } catch (err) {
        const msg = err.response?.data?.message ?? 'Failed to save data';
        alert(msg);
    } finally {
        saving.value = false;
    }
}

async function deleteMember(item) {
    if (!confirm('Delete this family member?')) return;

    try {
        await client.delete(`/village-resident/family-members/${item.id}`);
        await fetchMembers();
    } catch (err) {
        const msg = err.response?.data?.message ?? 'Failed to delete';
        alert(msg);
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
    <div class="flex flex-col gap-6 pb-[30px]">
        <header class="flex items-center justify-between">
            <h1 class="font-semibold text-2xl">Family Members</h1>
            <button @click="openCreate"
                class="flex items-center gap-2 rounded-2xl px-6 py-3 bg-desa-dark-green text-white font-semibold text-sm hover:bg-desa-black transition-setup">
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Add Member
            </button>
        </header>

        <section v-if="headOfFamily" class="flex flex-col gap-6 p-6 bg-white rounded-3xl">
            <h2 class="font-medium leading-5 text-desa-secondary">Head of Family (1)</h2>
            <div class="data rounded-2xl border border-desa-background p-6 flex justify-between items-center">
                <div class="name flex items-center gap-3 min-w-[240px]">
                    <div class="flex size-[64px] shrink-0 rounded-full overflow-hidden bg-desa-foreshadow">
                        <img :src="headOfFamily.profile_picture?.url ?? '/desa-digital/src/assets/images/no-image.png'" class="w-full h-full object-cover" alt="photo">
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
                        <span class="font-medium text-sm text-desa-secondary">ID Number</span>
                    </div>
                    <p class="font-semibold leading-5">{{ headOfFamily.identity_number }}</p>
                </div>
                <div class="umur flex flex-col gap-[6px] min-w-[92px]">
                    <div class="flex items-center gap-1">
                        <img src="/desa-digital/src/assets/images/icons/timer-secondary-green.svg" alt="icon" class="size-[18px] shrink-0">
                        <span class="font-medium text-sm text-desa-secondary">Age</span>
                    </div>
                    <p class="font-semibold leading-5">{{ headOfFamily.date_of_birth ? new Date().getFullYear() - new Date(headOfFamily.date_of_birth).getFullYear() : '-' }} Years</p>
                </div>
                <div class="flex shrink-0">
                    <button @click="openEditHeadOfFamily"
                        class="rounded-2xl px-4 py-[10px] bg-desa-black font-medium leading-5 text-white text-sm hover:bg-desa-dark-green transition-setup">
                        Edit
                    </button>
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
                        <img :src="m.family_member_file?.url ?? '/desa-digital/src/assets/images/no-image.png'" class="w-full h-full object-cover" alt="photo">
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
                        <span class="font-medium text-sm text-desa-secondary">ID Number</span>
                    </div>
                    <p class="font-semibold leading-5">{{ m.identity_number }}</p>
                </div>
                <div class="umur flex flex-col gap-[6px] min-w-[92px]">
                    <div class="flex items-center gap-1">
                        <img src="/desa-digital/src/assets/images/icons/timer-secondary-green.svg" alt="icon" class="size-[18px] shrink-0">
                        <span class="font-medium text-sm text-desa-secondary">Age</span>
                    </div>
                    <p class="font-semibold leading-5">{{ m.date_of_birth ? new Date().getFullYear() - new Date(m.date_of_birth).getFullYear() : '-' }} Years</p>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <button @click="openEdit(m)"
                        class="rounded-2xl px-4 py-[10px] bg-desa-black font-medium leading-5 text-white text-sm hover:bg-desa-dark-green transition-setup">
                        Edit
                    </button>
                    <button @click="deleteMember(m)"
                        class="rounded-2xl px-4 py-[10px] border border-red-500 text-red-500 font-medium leading-5 text-sm hover:bg-red-500 hover:text-white transition-setup">
                        Delete
                    </button>
                </div>
            </div>
        </section>
    </template>

    <div v-if="!members.length && headOfFamily" class="text-center py-12 text-desa-secondary font-medium">
        No family members yet
    </div>
</div>

<div v-if="showModal" class="fixed inset-0 z-50 flex bg-black/50" @click.self="closeModal">
    <div class="flex flex-col w-full max-w-lg mx-4 rounded-3xl bg-white max-h-[90vh] overflow-y-auto my-auto">
        <div class="flex items-center justify-between p-6 pb-0">
            <h3 class="font-semibold text-lg">
                {{ editMode === 'head_of_family' ? 'Edit Head of Family' : editMode === 'edit' ? 'Edit Family Member' : 'Add Family Member' }}
            </h3>
            <button @click="closeModal" class="flex size-8 items-center justify-center rounded-full hover:bg-desa-background transition-setup">
                <svg class="size-5 text-desa-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form @submit.prevent="saveMember" class="flex flex-col gap-4 p-6">
            <div class="flex flex-col gap-2">
                <label class="font-medium text-sm text-desa-secondary">Full Name</label>
                <input v-model="form.full_name" :class="fieldClass()" placeholder="Full name" required>
            </div>

            <div class="flex flex-col gap-2">
                <label class="font-medium text-sm text-desa-secondary">ID Number</label>
                <input v-model="form.identity_number" :class="fieldClass()" placeholder="National ID number" required>
            </div>

            <div v-if="editMode !== 'head_of_family'" class="flex flex-col gap-2">
                <label class="font-medium text-sm text-desa-secondary">Email</label>
                <input v-model="form.email" type="email" :class="fieldClass()" placeholder="Email">
            </div>

            <div class="flex flex-col gap-2">
                <label class="font-medium text-sm text-desa-secondary">Phone Number</label>
                <input v-model="form.phone_number" :class="fieldClass()" placeholder="Phone number">
            </div>

            <div class="flex flex-col gap-2">
                <label class="font-medium text-sm text-desa-secondary">Occupation</label>
                <input v-model="form.occupation" :class="fieldClass()" placeholder="Occupation" required>
            </div>

            <div class="flex flex-col gap-2">
                <label class="font-medium text-sm text-desa-secondary">Date of Birth</label>
                <input v-model="form.date_of_birth" type="date" :class="fieldClass()" required>
            </div>

            <div class="flex flex-col gap-2">
                <label class="font-medium text-sm text-desa-secondary">Gender</label>
                <div class="relative">
                    <select v-model="form.gender" :class="selectClass()" required>
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
                    <select v-model="form.marital_status" :class="selectClass()" required>
                        <option value="" disabled>Select status</option>
                        <option v-for="opt in maritalOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                    </select>
                    <svg class="absolute right-4 top-1/2 -translate-y-1/2 size-5 text-desa-secondary pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </div>

            <div v-if="editMode !== 'head_of_family'" class="flex flex-col gap-2">
                <label class="font-medium text-sm text-desa-secondary">Relation</label>
                <div class="relative">
                    <select v-model="form.relation" :class="selectClass()" required>
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
