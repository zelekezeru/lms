<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { useForm } from "@inertiajs/vue3";
import Form from "./Form.vue";

defineProps({
    roles: { type: Array, required: true },
});

// Initialize the form data
const form = useForm({
    name: "",
    email: "",
    password: "users@default", // Default password
    password_confirmation: "users@default", // Default confirmation
    role_name: "",
    profile_img: null,
});

// Submit the form
const submit = () => {
    form.post(route("users.store"));
};
</script>

<template>
    <AppLayout>
        <div class="max-w-4xl mx-auto p-6">
            <div class="mb-6 text-center">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    Create User
                </h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                    Fill in the details to add a new user.
                </p>
            </div>

            <div class="dark:bg-gray-900 bg-white shadow-lg rounded-lg p-6">
                <Form :form="form" :roles="roles" @submit="submit" />
            </div>
        </div>
    </AppLayout>
</template>
