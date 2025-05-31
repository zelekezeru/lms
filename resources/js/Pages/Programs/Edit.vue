<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { defineProps } from "vue";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { router, useForm, usePage } from "@inertiajs/vue3";
import Form from "./Form.vue";

const props = defineProps({
    program: { type: Object, required: true },
    courses: { type: Object, required: true },
    users: { type: Object, required: true },
});

// Initialize form data
const form = useForm({
    name: props.program.name || "",
    courses: props.program.courses.map(course => course.id) || [],
    language: props.program.language || "",
    description: props.program.description || "",
    duration: props.program.duration || "",
    address: props.program.address || "",
    user_id: props.program.user_id || "",
    _method: "PATCH", 
});

// Submit form function
const submit = (id) => {
    form.post(route("programs.update", { program: id }), {
        onSuccess: () => {
            Swal.fire(
                "Updated",
                "The program has been updated successfully",
                "success"
            );
        },
    });
};
</script>

<template>
    <AppLayout>
        <div class="max-w-5xl mx-auto p-6">
            <!-- Centered and Enhanced Title -->
            <div class="mb-6 text-center">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    {{ $t('programs.edit_title', { name: program.name }) }}
                </h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                    {{ $t('programs.edit_description') }}
                </p>
            </div>

            <!-- Form Card -->
            <div
                class="bg-white dark:bg-gray-900 shadow-lg rounded-lg p-6 transition"
            >
                <Form :form="form" :courses="courses" @submit="submit(program.id)" :users="users" />
            </div>
        </div>
    </AppLayout>
</template>
