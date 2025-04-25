<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { defineProps, ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import Form from "./Form.vue";

// Define props to accept the employee data
const props = defineProps({
    employee: { type: Object, required: true },
    roles: { type: Array, required: true },
});

// Initialize the form with the employee data or empty values
const form = useForm({
    id: props.employee.id || "",
    name: props.employee.name || "",
    email: props.employee.email || "",
    password: props.employee.password || "employees@default", // Set default if no password provided
    password_confirmation:
        props.employee.password_confirmation || "employees@default",
    role_name: props.employee.userRole || "",
    job_position: props.employee.jobPosition || "",
    employment_type: props.employee.employmentType || "",
    office_hours: props.employee.officeHours || "",
    profile_img: props.employee.profile_img || "",
    _method: "PUT", // Indicates PUT method for the update
});

// Submit the form, passing the employee ID to the route
const submit = (id) => {
    form.post(route("employees.update", { employee: id }));
};
</script>

<template>
    <AppLayout>
        <div class="max-w-4xl mx-auto p-6">
            <div class="mb-6 text-center">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    Edit {{ props.employee.name }}
                </h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                    Modify the employee details below.
                </p>
            </div>
            <!-- Employee Image -->
            <div class="flex justify-center mb-8">
                <div
                    v-if="!imageLoaded"
                    class="rounded-full w-44 h-44 bg-gray-300 dark:bg-gray-700 animate-pulse"
                ></div>
                <img
                    v-show="imageLoaded"
                    class="rounded-full w-44 h-44 object-contain bg-gray-400"
                    :src="employee.profileImg"
                    :alt="`profile image of ` + employee.name"
                    @load="imageLoaded = true"
                />
            </div>

            <div class="bg-white-100 dark:bg-gray-900 shadow-lg rounded-lg p-6">
                <!-- Pass the form to the Form component, and handle submit with employee.id -->
                <Form
                    :form="form"
                    :tracks="tracks"
                    :roles="roles"
                    @submit="submit(props.employee.id)"
                />
            </div>
        </div>
    </AppLayout>
</template>
