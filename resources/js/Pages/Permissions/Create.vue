<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import Form from './Form.vue';

const form = ref({ id: '', name: '', guard_name: 'web' });
const submit = () => {
    router.post(route('permissions.store'), form.value);
};
</script>

<template>
    <Head title="Register Permission" />
    <AppLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-800">Register Permission</h2>
        </template>
        <Form :form="form" @submit="submit" />
    </AppLayout>
</template>
