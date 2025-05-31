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
    <Head :title="$t('role.edit_title')" />
    <AppLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-800">{{ $t('role.edit_title') }}</h2>
        </template>
        <Form :form="form" @submit="submit" />
    </AppLayout>
</template>
