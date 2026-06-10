<script setup>
import { ref, onMounted } from 'vue';
import client from '../../api/client';

const users = ref([]);

onMounted(async () => {
    try {
        const response = await client.get('/admin/users');
        users.value = response.data.data ?? [];
    } catch {
        users.value = [];
    }
});
</script>

<template>
    <div class="flex flex-col gap-[14px]">
        <h1 class="font-semibold text-2xl">Users</h1>

        <div class="rounded-3xl bg-white p-6">
            <div class="overflow-x-auto">
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
                        <tr v-for="user in users" :key="user.id" class="border-b border-desa-foreshadow last:border-0">
                            <td class="px-4 py-4 font-semibold leading-5">{{ user.username }}</td>
                            <td class="px-4 py-4 text-desa-secondary">{{ user.email }}</td>
                            <td class="px-4 py-4">
                                <span class="font-medium">{{ user.role }}</span>
                            </td>
                            <td class="px-4 py-4">
                                <button
                                    class="rounded-2xl px-4 py-[10px] bg-desa-black font-medium leading-5 text-white text-sm hover:bg-desa-dark-green transition-setup">
                                    Edit
                                </button>
                            </td>
                        </tr>
                        <tr v-if="!users.length">
                            <td colspan="4" class="px-4 py-12 text-center text-desa-secondary font-medium">No users data</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
