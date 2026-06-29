<script setup>
import { ref, onMounted } from "vue";
import client from "../../api/client";

const items = ref([]);

const genderLabel = (v) => ({ male: "Male", female: "Female" })[v] ?? v;
const maritalLabel = (v) =>
    ({
        single: "Single",
        married: "Married",
        widow: "Widow",
        widower: "Widower",
    })[v] ?? v;

onMounted(async () => {
    try {
        const res = await client.get("/village-staff/head-families");
        items.value = res.data.data ?? [];
    } catch {
        items.value = [];
    }
});
</script>

<template>
    <div class="flex flex-col gap-[14px]">
        <h1 class="font-semibold text-2xl">Head of Families</h1>

        <div class="rounded-3xl bg-white p-4 sm:p-6">
            <div class="hidden sm:block overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-desa-background">
                            <th
                                class="px-4 py-4 font-medium text-desa-secondary text-sm"
                            >
                                Name
                            </th>
                            <th
                                class="px-4 py-4 font-medium text-desa-secondary text-sm"
                            >
                                ID Number
                            </th>
                            <th
                                class="px-4 py-4 font-medium text-desa-secondary text-sm"
                            >
                                Gender
                            </th>
                            <th
                                class="px-4 py-4 font-medium text-desa-secondary text-sm"
                            >
                                Occupation
                            </th>
                            <th
                                class="px-4 py-4 font-medium text-desa-secondary text-sm"
                            >
                                Status
                            </th>
                            <th
                                class="px-4 py-4 font-medium text-desa-secondary text-sm"
                            >
                                Action
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="item in items"
                            :key="item.id"
                            class="border-b border-desa-foreshadow last:border-0"
                        >
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex size-10 rounded-full overflow-hidden bg-desa-foreshadow shrink-0"
                                    >
                                        <img
                                            :src="
                                                item.profile_picture?.url ??
                                                '/assets/images/no-image.png'
                                            "
                                            class="w-full h-full object-cover"
                                            alt="photo"
                                        />
                                    </div>
                                    <span class="font-semibold leading-5">{{
                                        item.full_name
                                    }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-desa-secondary">
                                {{ item.identity_number }}
                            </td>
                            <td class="px-4 py-4">
                                {{ genderLabel(item.gender) }}
                            </td>
                            <td class="px-4 py-4 text-desa-secondary">
                                {{ item.occupation }}
                            </td>
                            <td class="px-4 py-4">
                                {{ maritalLabel(item.marital_status) }}
                            </td>
                            <td class="px-4 py-4">
                                <router-link
                                    :to="`/staff/head-families/${item.id}/members`"
                                    class="rounded-2xl px-6 py-[14px] bg-desa-black font-medium leading-5 text-white text-sm inline-block"
                                >
                                    Manage
                                </router-link>
                            </td>
                        </tr>
                        <tr v-if="!items.length">
                            <td
                                colspan="6"
                                class="px-4 py-12 text-center text-desa-secondary font-medium"
                            >
                                No head of family data
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex sm:hidden flex-col gap-3">
                <router-link
                    v-for="item in items"
                    :key="item.id"
                    :to="`/staff/head-families/${item.id}/members`"
                    class="flex items-center justify-between p-4 rounded-2xl border border-desa-background hover:shadow-md transition-setup"
                >
                    <div class="flex items-center gap-3 min-w-0 flex-1">
                        <div
                            class="flex size-10 rounded-full overflow-hidden bg-desa-foreshadow shrink-0"
                        >
                            <img
                                :src="
                                    item.profile_picture?.url ??
                                    '/assets/images/no-image.png'
                                "
                                class="w-full h-full object-cover"
                                alt="photo"
                            />
                        </div>
                        <div class="min-w-0">
                            <p class="font-semibold text-sm leading-5 truncate">
                                {{ item.full_name }}
                            </p>
                            <p
                                class="text-xs text-desa-secondary mt-1 truncate"
                            >
                                {{ item.identity_number }}
                            </p>
                        </div>
                    </div>
                    <span
                        class="text-xs font-semibold text-desa-dark-green shrink-0 ml-2"
                        >Manage →</span
                    >
                </router-link>
                <p
                    v-if="!items.length"
                    class="text-center py-8 text-desa-secondary font-medium text-sm"
                >
                    No head of family data
                </p>
            </div>
        </div>
    </div>
</template>
