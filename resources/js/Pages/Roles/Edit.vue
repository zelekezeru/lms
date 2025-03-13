<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import Form from './Form.vue';

const props = defineProps({ role: Object });
const form = useForm({ ...props.role, _method: 'PUT' });
const submit = () => {
    form.post(route('roles.update', props.role.id));
};
</script>

<template>
    <Head title="Edit Role" />
    <AppLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-800">Edit Role</h2>
        </template>
        <Form :form="form" @submit="submit" />
    </AppLayout>
</template>
