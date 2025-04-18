<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import Form from "./Form.vue";
import { useForm } from "@inertiajs/vue3";

// Props data coming from backend
const props = defineProps({
    users: { type: Array, required: true },
    programs: { type: Array, required: true },
    departments: { type: Array, required: true },
    years: { type: Array, required: true },
    semesters: { type: Array, required: true },
    courses: { type: Array, required: true },
    sections: { type: Array, required: true },
});

// Form initialization
const form = useForm({
    name: "",
    point: "",
    description: "",
    instructor_id: "",
    semester_id: "",
    course_id: "",
    section_id: "",
});

// Submit handler
const submit = () => {
    form.post(route("weights.store"), {
        preserveScroll: true,
    });
};
</script>

<template>
    <AppLayout>
        <div class="max-w-4xl mx-auto p-6">
            <div class="mb-6 text-center">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Create Weight</h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                    Fill in the details to create a new weight.
                </p>
            </div>
            <Form
                :form="form"
                :users="users"
                :programs="programs"
                :departments="departments"
                :years="years"
                :semesters="semesters"
                :courses="courses"
                :sections="sections"
                @submit="submit"
            />
        </div>
    </AppLayout>
</template>
