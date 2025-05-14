<script setup>
import { defineProps } from "vue";

const props = defineProps({
    course: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <div>
        <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-gray-100">
            Course
        </h2>
        
        <p class="text-gray-500 dark:text-gray-400">
            No course assigned to this course.
        </p>
    </div>
</template>
