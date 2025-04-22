<script setup>
import { defineProps } from "vue";
import { Link } from "@inertiajs/vue3";
import "sweetalert2/dist/sweetalert2.min.css";
import { CogIcon } from "@heroicons/vue/24/solid";

const props = defineProps({
    
});
</script>

<template>
    <div>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Academic Information
            </h2>

            <div class="flex justify-center mb-6"></div>
        </div>

        <div class="overflow-x-auto">
            <div
                class="mt-8 border-t border-b border-gray-300 dark:border-gray-600 pt-4 pb-4"
            >
                <!-- instructor details -->
                <div class="grid grid-cols-2 gap-4"></div>
            </div>
        </div>
    </div>
</template>
