<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import Form from './Form.vue';

const form = useForm({ name: '', guard_name: 'web' });
const submit = () => {
    form.post(route('roles.store'));
};
</script>

<template>
    <Head title="Register Role" />
    <AppLayout>
        <div class="flex justify-center">
            <div
                class="bg-gray-200 dark:bg-gray-800 shadow-lg rounded-md p-6 max-w-lg w-full"
            >
                <h2 class="text-xl font-semibold mb-4 dark:text-gray-200">
                    Department Creation Form
                </h2>
                
                <Form :form="form" @submit="submit" />
            </div>
        </div>
    </AppLayout>
</template>
