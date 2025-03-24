<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import Form from "@/Pages/Departments/Form.vue";
import { useForm } from "@inertiajs/vue3";

defineProps({ users: Array, programs: Array });

const form = useForm({
    name: "",
    code: "",
    description: "",
    duration: "",
    user_id: "",
    program_id: "",
});

const submit = () => form.post(route("departments.store"));
</script>

<template>
    <AppLayout>
        <div class="max-w-5xl mx-auto p-6">
            <!-- Centered and Enhanced Title -->
            <div class="mb-6 text-center">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    Department Creation Form
                </h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                    Please fill out the form below to create a new department.
                </p>
            </div>

            <!-- Form Card -->
            <div
                class="bg-white dark:bg-gray-900 shadow-lg rounded-lg p-6 transition"
            >
                <Form
                    :form="form"
                    @submit="submit"
                    :users="users"
                    :programs="programs"
                />
            </div>
        </div>
    </AppLayout>
</template>
