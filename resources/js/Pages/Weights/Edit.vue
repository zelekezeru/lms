<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import Form from "./Form.vue";
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
    weight: Object,
    users: Array,
    programs: Array,
    departments: Array,
    years: Array,
    semesters: Array,
    courses: Array,
    sections: Array,
});

const form = useForm({
    name: props.weight?.name || "",
    point: props.weight?.point || "",
    description: props.weight?.description || "",
    instructor_id: props.weight?.instructor_id || "",
    semester_id: props.weight?.semester_id || "",
    course_id: props.weight?.course_id || "",
    section_id: props.weight?.section_id || "",
    _method: "PATCH",
});

const submit = () => {
    form.post(route("weights.update", { weight: props.weight.id }), {
        preserveScroll: true,
    });
};
</script>

<template>
    <AppLayout>
        <div class="max-w-4xl mx-auto p-6">
            <div class="mb-6 text-center">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Edit Weight</h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                    Modify the details of the weight.
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
