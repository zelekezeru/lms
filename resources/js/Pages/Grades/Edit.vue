<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { useForm } from "@inertiajs/vue3";
import Form from "./Form.vue";

defineProps({
    grade: Object,
    users: Object,
    years: Object,
    semesters: Object,
    courses: Object,
});

const form = useForm({
    name: grade.name,
    grade_point: grade.grade_point,
    description: grade.description,
    user_id: grade.user_id,
    year_id: grade.year_id,
    semester_id: grade.semester_id,
    course_id: grade.course_id,
});

const submit = () => {
    form.put(route("grades.update", grade.id));
};
</script>

<template>
    <AppLayout>
        <div class="max-w-5xl mx-auto p-6">
            <!-- Page Title -->
            <div class="mb-6 text-center">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    Edit Grade
                </h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                    Update the details of the grade below.
                </p>
            </div>

            <!-- Form Card -->
            <div
                class="bg-white dark:bg-gray-900 shadow-lg rounded-lg p-6 transition"
            >
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
