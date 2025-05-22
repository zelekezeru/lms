<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import Form from "./Form.vue";
import { useForm } from "@inertiajs/vue3";

// Props data coming from backend
const props = defineProps({
    users: { type: Array, required: true },
    programs: { type: Array, required: true },
    years: { type: Array, required: true },
});

// Form initialization
const form = useForm({
    name: "",
    code: "",
    user_id: "",
    program_id: "",
    track_id: "",
    year_id: "",
    semester_id: "",
    study_mode_id: "",
});

// Submit handler
const submit = () => {
    form.post(route("sections.store"), {
        preserveScroll: true,
    });
};
</script>

<template>
    <AppLayout>
        <div class="max-w-4xl mx-auto p-6">
            <div class="mb-6 text-center">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    Create Section
                </h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                    Fill in the details to create a new section.
                </p>
            </div>
            <Form
                :form="form"
                :users="users"
                :programs="programs"
                :years="years"
                @submit="submit"
            />
        </div>
    </AppLayout>
</template>
