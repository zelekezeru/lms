<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import Form from './Form.vue';

const props = defineProps({ student: Object });
const form = ref({ ...props.student });
const submit = () => {
    form.put(route('students.update', props.student.id), form.value);
};
</script>

<template>
    <Head title="Edit Student" />
    <AppLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-800">Edit Student</h2>
        </template>
        <Form :form="form" @submit="submit" />
    </AppLayout>
</template>
