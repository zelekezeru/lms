<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import Form from './Form.vue';

const form = ref({ 
    student_id: '', 
    student_name: '', 
    program: '', 
    year_of_study: '' 
});

const submit = () => {
    router.post(route('students.store'), form.value);
};
</script>

<template>
    <Head title="Register Student" />
    <AppLayout>
        <div
                class="bg-white dark:bg-gray-900 shadow-lg rounded-lg p-6 transition"
            >
        <Form :form="form" @submit="submit" />
        </div>
    </AppLayout>
</template>
