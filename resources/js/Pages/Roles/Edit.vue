<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import Form from './Form.vue';

const props = defineProps({ role: Object });
const form = ref({ ...props.role });
const submit = () => {
    router.put(route('roles.update', props.role.id), form.value);
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
