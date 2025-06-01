<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import Form from "./Form.vue";
import { useForm } from "@inertiajs/vue3";

const form = useForm({
    name: '',
    code: '',
    address: '',
    coordinator_id: null,
    status: 'Inactive',
});

const submit = () => {
    form.post(route("centers.store"));
};
</script>

<template>
    <AppLayout :title="$t('centers.create_title')">
        <div class="max-w-4xl mx-auto p-6">
            <div class="mb-6 text-center">
                <h2 class="text-2xl font-bold">{{ $t('centers.add') }}</h2>
                <p class="mt-2 text-gray-600">
                    {{ $t('centers.create_description') }}
                </p>
            </div>
            <Form :form="form" @submit="submit" />
        </div>
    </AppLayout>
</template>

