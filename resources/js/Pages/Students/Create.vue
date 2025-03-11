<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import Form from './Form.vue';

const form = ref({ student_id: '', student_name: '', program: '', year_of_study: '' });
const submit = () => {
    router.post(route('students.store'), form.value);
};
</script>

<template>
    <Head title="Register Student" />
    <AppLayout>
        <template #header>
            
            <h2 class="text-xl font-semibold mb-4 dark:text-gray-200">Register Student</h2>

        </template>
        <Form :form="form" @submit="submit" />
    </AppLayout>
</template>
