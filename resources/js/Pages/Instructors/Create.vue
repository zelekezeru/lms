<script setup>
import Form from "./Form.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import { useForm, usePage } from "@inertiajs/vue3";

defineProps({
    departments: { type: Object, required: true },
    roles: { type: Object, required: true },
});

const form = useForm({
    name: "",
    email: "",
    password: "instructors@default",
    password_confirmation: "instructors@default",
    role_name: "",
    department_id: "",
    job_position: "",
    hire_date: "",
    status: "",
    specialization: "",
    bio: "",
    employment_type: "",
    office_hours: "",
    profile_img: null,
});

// Submit the form
const submit = () => {
    form.post(route("instructors.store"));
};
 
</script>

<template>
    <AppLayout>
        <div class="mb-6 text-center">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                Add Instructor
            </h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                Fill in the details to register a new instructor.
            </p>
        </div>
        <Form :form="form" :departments="departments" :roles="roles" @submit="submit"/>
    </AppLayout>
</template>
