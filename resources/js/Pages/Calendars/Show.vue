<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";

const props = defineProps({
    semester: Object,
});
</script>

<template>
    <AppLayout>
        <h2 class="text-2xl font-semibold mb-4 dark:text-white">
            Semester Details
        </h2>
        <div class="bg-white dark:bg-gray-800 p-6 rounded shadow">
            <p><strong>Name:</strong> {{ semester.name }}</p>
            <p><strong>Start Date:</strong> {{ semester.start_date }}</p>
            <p><strong>End Date:</strong> {{ semester.end_date }}</p>
            <p><strong>Status:</strong> {{ semester.status }}</p>
            <p><strong>Created At:</strong> {{ semester.created_at_formatted }}</p>
            <p><strong>Updated At:</strong> {{ semester.updated_at_formatted }}</p>
        </div>
    </AppLayout>
</template>
