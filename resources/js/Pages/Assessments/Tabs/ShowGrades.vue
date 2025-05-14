<script setup>
import { defineProps } from "vue";
import { Link } from "@inertiajs/vue3";
import "sweetalert2/dist/sweetalert2.min.css";
import { PencilIcon, TrashIcon } from "@heroicons/vue/24/solid";

const props = defineProps({
    results: {
        type: Object,
        required: true,
    },
    weights: {
        type: Object,
        required: false,
    },
});
</script>

<template>
        <!-- Grades Panel -->
    <div v-show="selectedTab === 'grades'" class="grid grid-cols-2 gap-2">
        <div class="flex flex-col">
            <span class="text-sm text-gray-500 dark:text-gray-400"
                >Grades</span
            >
            <span
                class="text-lg font-medium text-gray-900 dark:text-gray-100"
            >
                {{ section.grades }}
            </span>
        </div>

        <div class="flex flex-col">
            <span class="text-sm text-gray-500 dark:text-gray-400"
                >Actions</span
            >
            <Link
                :href="route('grades.create', { section: section.id })"
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
                <PlusCircleIcon class="w-5 h-5 mr-2" />
                Add Grade
            </Link>
        </div>
    </div>
</template>