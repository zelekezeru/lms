<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { useForm } from "@inertiajs/vue3";
import Form from "./Form.vue";

defineProps({
    users: Object,
    years: Object,
    semesters: Object,
    courses: Object,
});

const form = useForm({
    name: "",
    grade_point: "",
    description: "",
    user_id: "",
    year_id: "",
    semester_id: "",
    course_id: "",
});

const submit = () => {
    form.post(route("grades.store"));
};
</script>

<template>
    <AppLayout>
        <div class="max-w-5xl mx-auto p-6">
            <div class="mb-6 text-center">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    Grade Creation Form
                </h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                    Please fill out the form below to create a new grade.
                </p>
            </div>

            <div class="bg-white dark:bg-gray-900 shadow-lg rounded-lg p-6 transition">
                <Form
                    :form="form"
                    @submit="submit"
                    :users="users"
                    :years="years"
                    :semesters="semesters"
                    :courses="courses"
                />
            </div>
        </div>
    </AppLayout>
</template>
