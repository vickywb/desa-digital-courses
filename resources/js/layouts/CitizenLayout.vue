<script setup>
import { ref, provide } from 'vue';
import CitizenSidebar from '@/components/sidebar/CitizenSidebar.vue';
import Topbar from '@/components/Topbar.vue';

const sidebarOpen = ref(false);
provide('sidebarOpen', sidebarOpen);

function toggleSidebar() {
    sidebarOpen.value = !sidebarOpen.value;
}

function closeSidebar() {
    sidebarOpen.value = false;
}
</script>

<template>
    <div class="flex flex-1 min-h-screen">
        <CitizenSidebar :open="sidebarOpen" @close="closeSidebar" />

        <div id="Main-Container" class="flex flex-col flex-1 min-w-0">
            <Topbar @toggle-sidebar="toggleSidebar" />

            <div id="Content" class="relative flex flex-col flex-1 gap-[14px] p-4 md:p-6 pb-[30px] w-full shrink-0">
                <router-view />
            </div>
        </div>
    </div>
</template>
