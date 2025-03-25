<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
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
    password_confirmation: props.employee.password_confirmation || "employees@default",
    role_name: props.employee.role_name || "",
    job_position: props.employee.job_position || "",
    employment_type: props.employee.employment_type || "",
    office_hours: props.employee.office_hours || "",
    _method: "PUT",  // Indicates PUT method for the update
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
                    Edit Employee
                </h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                    Modify the employee details below.
                </p>
            </div>

            <div class="bg-white-100 dark:bg-gray-900 shadow-lg rounded-lg p-6">
                <!-- Pass the form to the Form component, and handle submit with employee.id -->
                <Form 
                    :form="form" 
                    :departments="departments" 
                    :roles="roles" 
                    @submit="submit(props.employee.id)" 
                />
            </div>
        </div>
    </AppLayout>
</template>
