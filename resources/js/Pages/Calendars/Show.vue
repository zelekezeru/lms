<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";

const props = defineProps({
    semester: Object,
});
</script>

<template>
    <AppLayout>
        <h2 class="text-2xl font-semibold mb-4 dark:text-white">
            {{ $t('semester.details_title1') }}
        </h2>
        <div class="bg-white dark:bg-gray-800 p-6 rounded shadow">
            <p><strong>{{ $t('semester.name') }}:</strong> {{ semester.name }}</p>
            <p><strong>{{ $t('semester.start_date') }}:</strong> {{ semester.start_date }}</p>
            <p><strong>{{ $t('semester.end_date') }}:</strong> {{ semester.end_date }}</p>
            <p><strong>{{ $t('semester.status') }}:</strong> {{ $t('status.' + semester.status.toLowerCase(), semester.status) }}</p>
            <p><strong>{{ $t('semester.created_at') }}:</strong> {{ semester.created_at_formatted }}</p>
            <p><strong>{{ $t('semester.updated_at') }}:</strong> {{ semester.updated_at_formatted }}</p>
        </div>
    </AppLayout>
</template>
