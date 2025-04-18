<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { useForm } from "@inertiajs/vue3";
import Form from "./Form.vue";

defineProps({
    weights: Object,
    students: Object,
    years: Object,
    semesters: Object,
    sections: Object,
    courses: Object,
    users: Object,
    grades: Object,
});

const form = useForm({
    point: "",
    description: "",
    changed_point: "",
    weight_id: "",
    student_id: "",
    year_id: "",
    semester_id: "",
    section_id: "",
    course_id: "",
    user_id: "",
    grade_id: "",
    changed_by: "",
    changed_at: "",
});

const submit = () => {
    form.post(route("results.store"));
};
</script>

<template>
    <AppLayout>
        <div class="max-w-5xl mx-auto p-6">
            <div class="mb-6 text-center">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    Result Creation Form
                </h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                    Please fill out the form below to create a new result.
                </p>
            </div>

            <div class="bg-white dark:bg-gray-900 shadow-lg rounded-lg p-6 transition">
                <Form
                    :form="form"
                    @submit="submit"
                    :weights="weights"
                    :students="students"
                    :years="years"
                    :semesters="semesters"
                    :sections="sections"
                    :courses="courses"
                    :users="users"
                    :grades="grades"
                />
            </div>
        </div>
    </AppLayout>
</template>
