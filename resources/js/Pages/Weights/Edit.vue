<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { useForm } from "@inertiajs/vue3";
import Form from "./Form.vue";

defineProps({
    weight: Object,
    users: Object,
});

const form = useForm({
    name: weight.name,
    weight_point: weight.weight_point,
    weight_description: weight.weight_description,
    user_id: weight.user_id,
});

const submit = () => {
    form.put(route("weights.update", weight.id));
};
</script>

<template>
    <AppLayout>
        <div class="max-w-5xl mx-auto p-6">
            <!-- Page Title -->
            <div class="mb-6 text-center">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    Edit Weight
                </h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                    Update the details of the weight below.
                </p>
            </div>

            <!-- Form Card -->
            <div
                class="bg-white dark:bg-gray-900 shadow-lg rounded-lg p-6 transition"
            >
                <Form :form="form" @submit="submit" :users="users" />
            </div>
        </div>
    </AppLayout>
</template>
