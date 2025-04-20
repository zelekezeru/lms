<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { useForm } from "@inertiajs/vue3";
import { ref } from "vue";
import Form from "./Form.vue";

defineProps({
    roles: { type: Array, required: true },
});

// Initialize the form data
const form = useForm({
    name: "",
    role_name: "",
    contact_phone: "",
    job_position: "",
    employment_type: "",
    office_hours: "",
    profile_img: null,
});

// Submit the form
const submit = () => {
    form.post(route("employees.store"));
};
</script>

<template>
    <AppLayout>
        <div class="max-w-4xl mx-auto p-6">
            <div class="mb-6 text-center">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    Create Employee
                </h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                    Fill in the details to add a new employee.
                </p>
            </div>

            <div class="dark:bg-gray-900 bg-white-100 shadow-lg rounded-lg p-6">
                <Form :form="form" :departments="departments" :roles="roles" @submit="submit"/>
            </div>
        </div>
    </AppLayout>
</template>
