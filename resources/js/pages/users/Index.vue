<template>
    <div class="space-y-4">
        <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-900">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-3 font-medium text-gray-900 dark:text-white">Username</th>
                            <th class="px-4 py-3 font-medium text-gray-900 dark:text-white">Email</th>
                            <th class="px-4 py-3 font-medium text-gray-900 dark:text-white">Role</th>
                            <th class="px-4 py-3 font-medium text-gray-900 dark:text-white">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in users" :key="user.id" class="border-b border-gray-100 dark:border-gray-800">
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ user.username }}</td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ user.email }}</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                {{ user.role }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <button class="text-sm text-blue-600 hover:underline dark:text-blue-400">Edit</button>
                            </td>
                        </tr>
                        <tr v-if="!users.length">
                            <td colspan="4" class="px-4 py-8 text-center text-gray-500">Tidak ada data</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

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
