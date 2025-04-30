<script setup>
import { useForm } from "@inertiajs/vue3";
import Form from "./Form.vue";
import AppLayout from "@/Layouts/AppLayout.vue";

// Props from controller
defineProps({
    programs: {
        type: Object,
        required: true,
    },
});
const form = useForm({
    name: "",
    credit_hours: "",
    duration: "",
    description: "",
    programs: [],
    is_training: false,
    status: false,
    is_published: false,
    is_approved: false,
    is_completed: false,
});

const submit = () => {
    form.post(route("courses.store"));
};
</script>

<template>
    <AppLayout>
        <div class="max-w-5xl mx-auto p-6">
            <!-- Centered and Enhanced Title -->
            <div class="mb-6 text-center">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    Course Creation Form
                </h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                    Please fill out the form below to create a new course.
                </p>
            </div>

            <!-- Form Card -->
            <div
                class="bg-white dark:bg-gray-900 shadow-lg rounded-lg p-6 transition"
            >
                <Form
                    :form="form"
                    @submit="submit"
                    :programs="programs"
                    :submit="submit"
                />
            </div>
        </div>
    </AppLayout>
</template>
