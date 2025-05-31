<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import Form from "./Form.vue";
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
    year: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    name: props.year?.name || "",
    status: props.year?.status,
    is_approved: props.year?.is_approved,
    is_completed: props.year?.is_completed,
    _method: "PATCH",
});

const submit = (id) => {
    form.post(route("years.update", { year: id }));
};
</script>

<template>
    <AppLayout>
        <div class="max-w-4xl mx-auto p-6">
            <div class="mb-6 text-center">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    {{ $t('year.edit_title') }}
                </h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                    {{ $t('year.edit_description') }}
                </p>
            </div>
            <Form :form="form" @submit="submit(year.id)" />
        </div>
    </AppLayout>
</template>
