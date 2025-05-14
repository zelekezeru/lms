<script setup>
import { defineProps } from "vue";

const props = defineProps({
    instructors: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <div>
        <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-gray-100">
            Instructors
        </h2>
        <div v-if="instructors.length" class="space-y-4">
            <div
                v-for="instructor in instructors"
                :key="instructor.id"
                class="p-4 bg-white dark:bg-gray-800 shadow rounded-lg border dark:border-gray-700"
            >
                <p class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ instructor.name }}
                </p>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ instructor.email }}
                </p>
            </div>
        </div>
        <p v-else class="text-gray-500 dark:text-gray-400">
            No instructors assigned to this course.
        </p>
    </div>
</template>
