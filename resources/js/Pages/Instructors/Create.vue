<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { useForm } from "@inertiajs/vue3";
import { ref } from "vue";
import Form from "./Form.vue";

defineProps({
    roles: { type: Object, required: true },
});

const form = useForm({
    name: "",
    email: "",
    role_name: "",
    contact_phone: "",
    hire_date: "",
    status: "",
    specialization: "",
    bio: "",
    employment_type: "",
    profile_img: "",
    status: "Inactive",
});

// Submit the form
const submit = () => {
    console.log(form);
    
    form.post(route("instructors.store"));
};
 
</script>

<template>
    <AppLayout>
        <div class="max-w-4xl mx-auto p-6">
            <div class="mb-6 text-center">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    Create Instructor
                </h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                    Fill in the details to add a new Instructor.
                </p>
            </div>

            <div class="dark:bg-gray-900 bg-white-100 shadow-lg rounded-lg p-6">
                <Form :form="form" :departments="departments" :roles="roles" @submit="submit"/>
            </div>
        </div>
    </AppLayout>
</template>
