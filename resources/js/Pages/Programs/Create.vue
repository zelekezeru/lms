<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { router, useForm } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import Form from "./Form.vue";

// Define the props for this component
defineProps({
    users: {
        type: Object,
    },
    courses: {
        type: Object,
    },
});

// Initialize the form with the program fields
const form = useForm({
    name: "",
    description: "",
    language: "",
    duration: "",
    courses: [],
    user_id: "", 
});

const submit = () => {
    form.post(route("programs.store")); 
};
</script>

<template>
    <AppLayout>
        <div class="max-w-5xl mx-auto p-6">
            <!-- Centered and Enhanced Title -->
            <div class="mb-6 text-center">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    Program Creation Form
                </h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                    Please fill out the form below to create a new program.
                </p>
            </div>

            <!-- Form Card -->
            <div
                class="bg-white dark:bg-gray-900 shadow-lg rounded-lg p-6 transition"
            >
                <Form :courses="courses" :form="form" @submit="submit" :users="users" />
            </div>
        </div>
    </AppLayout>
</template>