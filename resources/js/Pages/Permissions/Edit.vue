<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import Form from './Form.vue';

const props = defineProps({ permission: Object });
const form = ref({ ...props.permission });
const submit = () => {
    router.put(route('permissions.update', props.permission.id), form.value);
};
</script>

<template>
    <Head title="Edit Permission" />
    <AppLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-800">Edit Permission</h2>
        </template>
        <Form :form="form" @submit="submit" />
    </AppLayout>
</template>
