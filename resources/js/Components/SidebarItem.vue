<script setup>
import { computed } from "vue";
import { usePage, Link } from "@inertiajs/vue3";

const props = defineProps({
    href: { type: String, default: "#" },
    label: { type: String, required: true },
    icon: { type: Function, required: true },
    isCollapsed: { type: Boolean, default: false },
});

// highlight active link
const currentUrl = usePage().url;
const isActive = computed(() => currentUrl.startsWith(props.href));
</script>

<template>
    <Link
        :href="href"
        class="flex items-center p-2 rounded hover:bg-gray-300 dark:hover:bg-gray-800 border-b dark:border-gray-600"
        :class="{ 'bg-gray-200 dark:bg-gray-700': isActive }"
    >
        <component
            :is="icon"
            class="h-5 w-5 text-gray-600 dark:text-gray-400"
        />
        <span v-if="!isCollapsed" class="ml-3 text-gray-700 dark:text-gray-300">
            {{ label }}
        </span>
    </Link>
</template>
