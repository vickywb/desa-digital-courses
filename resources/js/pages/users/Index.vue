<script setup>
import { ref, onMounted } from 'vue';
import client from '../../api/client';

const users = ref([]);
const showModal = ref(false);
const selectedUser = ref(null);
const selectedRole = ref('');
const loading = ref(false);
const errorMessage = ref('');

const roleOptions = [
    { value: 'admin', label: 'Admin' },
    { value: 'head_of_family', label: 'Head of Family' },
    { value: 'head_village', label: 'Head Village' },
    { value: 'staff', label: 'Staff' },
];

onMounted(async () => {
    await fetchUsers();
});

async function fetchUsers() {
    try {
        const response = await client.get('/admin/users');
        users.value = response.data.data ?? [];
    } catch {
        users.value = [];
    }
}

function openEditModal(user) {
    selectedUser.value = user;
    selectedRole.value = user.role;
    errorMessage.value = '';
    showModal.value = true;
}

function closeModal() {
    showModal.value = false;
    selectedUser.value = null;
    selectedRole.value = '';
    errorMessage.value = '';
}

async function saveRole() {
    if (!selectedUser.value || !selectedRole.value) return;

    loading.value = true;
    errorMessage.value = '';

    try {
        await client.put(`/admin/users/${selectedUser.value.id}`, {
            role: selectedRole.value,
        });
        closeModal();
        await fetchUsers();
    } catch (error) {
        errorMessage.value = error.response?.data?.message || 'Failed to update user role.';
    } finally {
        loading.value = false;
    }
}
</script>

<template>
    <div class="flex flex-col gap-[14px]">
        <h1 class="font-semibold text-2xl">Users</h1>

        <div class="rounded-3xl bg-white p-4 sm:p-6">
            <div class="hidden sm:block overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-desa-background">
                            <th class="px-4 py-4 font-medium text-desa-secondary text-sm">Username</th>
                            <th class="px-4 py-4 font-medium text-desa-secondary text-sm">Email</th>
                            <th class="px-4 py-4 font-medium text-desa-secondary text-sm">Role</th>
                            <th class="px-4 py-4 font-medium text-desa-secondary text-sm">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in users" :key="user.id"
                            class="border-b border-desa-foreshadow last:border-0">
                            <td class="px-4 py-4 font-semibold leading-5">{{ user.username }}</td>
                            <td class="px-4 py-4 text-desa-secondary">{{ user.email }}</td>
                            <td class="px-4 py-4">
                                <span class="font-medium">{{ user.role }}</span>
                            </td>
                            <td class="px-4 py-4">
                                <button
                                    class="rounded-2xl px-4 py-[10px] bg-desa-black font-medium leading-5 text-white text-sm hover:bg-desa-dark-green transition-setup"
                                    @click="openEditModal(user)">
                                    Edit
                                </button>
                            </td>
                        </tr>
                        <tr v-if="!users.length">
                            <td colspan="4" class="px-4 py-12 text-center text-desa-secondary font-medium">No users data
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex sm:hidden flex-col gap-3">
                <div v-for="user in users" :key="user.id"
                    class="flex items-center justify-between p-4 rounded-2xl border border-desa-background">
                    <div class="min-w-0 flex-1">
                        <p class="font-semibold text-sm leading-5 truncate">{{ user.username }}</p>
                        <p class="text-xs text-desa-secondary mt-1 truncate">{{ user.email }}</p>
                    </div>
                    <span class="rounded-full px-3 py-1 text-[10px] font-semibold shrink-0 ml-2"
                        :class="user.role === 'admin' ? 'bg-desa-dark-green text-white' : 'bg-desa-background text-desa-secondary'">
                        {{ user.role }}
                    </span>
                </div>
                <p v-if="!users.length" class="text-center py-8 text-desa-secondary font-medium text-sm">No users data</p>
            </div>
        </div>

        <!-- Edit Role Modal -->
        <div class="modal fixed inset-0 h-screen z-40 flex bg-[#080C1ACC]" v-if="showModal">
            <div class="flex flex-col w-[400px] shrink-0 rounded-2xl overflow-hidden bg-white m-auto">
                <div class="flex items-center justify-between p-4 gap-3 bg-desa-black">
                    <p class="font-medium leading-5 text-white">Edit User Role</p>
                    <button class="btn-close-modal" @click="closeModal">
                        <img src="@/assets/images/icons/close-circle-white.svg" class="flex size-6 shrink-0" alt="icon">
                    </button>
                </div>
                <div class="flex flex-col p-4 gap-4">
                    <div v-if="selectedUser" class="flex flex-col gap-1">
                        <p class="font-semibold text-sm">{{ selectedUser.username }}</p>
                        <p class="text-desa-secondary text-sm">{{ selectedUser.email }}</p>
                    </div>

                    <hr class="border-desa-background">

                    <div class="flex flex-col gap-2">
                        <label class="font-medium text-sm text-desa-secondary">Role</label>
                        <select v-model="selectedRole"
                            class="w-full h-[56px] rounded-2xl px-4 border-[1.5px] border-desa-background font-medium leading-5 focus:ring-[1.5px] focus:ring-desa-dark-green focus:outline-none transition-all duration-300 bg-white"
                            :class="{ 'border-red-500': errorMessage }">
                            <option value="" disabled>Select role</option>
                            <option v-for="option in roleOptions" :key="option.value" :value="option.value">
                                {{ option.label }}
                            </option>
                        </select>
                        <span class="text-left text-[12px] text-red-500" v-if="errorMessage">
                            {{ errorMessage }}
                        </span>
                    </div>

                    <hr class="border-desa-background">

                    <div class="flex items-center gap-3">
                        <button
                            class="flex items-center h-14 rounded-2xl py-3 px-8 gap-[10px] border border-desa-background w-full hover:bg-desa-black hover:text-white transition-setup"
                            @click="closeModal">
                            <span class="font-semibold text-sm">Cancel</span>
                        </button>
                        <button class="flex items-center h-14 rounded-2xl py-3 px-8 gap-[10px] bg-desa-dark-green w-full"
                            @click="saveRole" :disabled="loading">
                            <span class="flex items-center gap-[10px]" v-if="!loading">
                                <span class="font-semibold text-sm text-white">Save</span>
                            </span>
                            <span v-if="loading">
                                <span class="font-semibold text-sm text-white">Loading...</span>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
