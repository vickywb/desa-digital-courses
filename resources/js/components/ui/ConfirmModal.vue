<script setup>
defineProps({
    show: Boolean,
    title: String,
    message: String,
    confirmText: { type: String, default: 'Ya' },
    cancelText: { type: String, default: 'Batal' },
    variant: { type: String, default: 'primary' },
    loading: Boolean,
});

const emit = defineEmits(['close', 'confirm']);
</script>

<template>
    <div v-if="show" class="fixed inset-0 z-50 flex bg-black/50" @click.self="emit('close')">
        <div class="flex flex-col w-[335px] shrink-0 rounded-2xl overflow-hidden bg-white m-auto">
            <div class="flex items-center justify-between p-4 gap-3 bg-desa-black">
                <p class="font-medium leading-5 text-white">{{ title }}</p>
                <button class="shrink-0" @click="emit('close')">
                    <svg class="size-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="flex flex-col p-4 gap-3">
                <p class="font-medium text-sm leading-[22.5px] text-desa-secondary">{{ message }}</p>
                <hr class="border-desa-background">
                <div class="flex items-center gap-3">
                    <button
                        class="flex-1 flex items-center justify-center h-14 rounded-2xl py-3 px-8 gap-[10px] border border-desa-background font-semibold text-sm hover:bg-desa-black hover:text-white transition-setup"
                        @click="emit('close')">
                        {{ cancelText }}
                    </button>
                    <button
                        class="flex-1 flex items-center justify-center h-14 rounded-2xl py-3 px-8 gap-[10px] font-semibold text-sm text-white transition-setup"
                        :class="variant === 'danger' ? 'bg-desa-red hover:bg-desa-black' : 'bg-desa-dark-green hover:bg-desa-black'"
                        :disabled="loading"
                        @click="emit('confirm')">
                        {{ loading ? 'Loading...' : confirmText }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
