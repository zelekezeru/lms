<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { useForm } from "@inertiajs/vue3";
import Form from "./Form.vue";

defineProps({
    result: Object,
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
    point: result.point,
    description: result.description,
    changed_point: result.changed_point,
    weight_id: result.weight_id,
    student_id: result.student_id,
    year_id: result.year_id,
    semester_id: result.semester_id,
    section_id: result.section_id,
    course_id: result.course_id,
    user_id: result.user_id,
    grade_id: result.grade_id,
    changed_by: result.changed_by,
    changed_at: result.changed_at,
});

const submit = () => {
    form.put(route("results.update", result.id));
};
</script>

<template>
    <AppLayout>
        <div class="max-w-5xl mx-auto p-6">
            <!-- Page Title -->
            <div class="mb-6 text-center">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    Edit Result
                </h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                    Update the details of the result below.
                </p>
            </div>

            <!-- Form Card -->
            <div
                class="bg-white dark:bg-gray-900 shadow-lg rounded-lg p-6 transition"
            >
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
