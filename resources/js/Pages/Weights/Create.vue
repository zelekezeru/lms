<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { useForm } from "@inertiajs/vue3";
import Form from "./Form.vue";

defineProps({
    semesters: { type: Array, required: true },
    years: { type: Array, required: true },
    courses: { type: Array, required: true },
    sections: { type: Array, required: true },
});

const form = useForm({
    name: "",
    weight_point: "",
    weight_description: "",
    user_id: "",
    year_id: "",
    semester_id: "",
    course_id: "",
});

const submit = () => {
    form.post(route("weights.store"));
};
</script>

<template>
    <AppLayout>
        <div class="max-w-5xl mx-auto p-6">
            <div class="mb-6 text-center">
                <h2 class="text-2xl font-bold">Create Weight</h2>
                <p class="text-gray-600">Fill out the form to create a new weight.</p>
            </div>
            <div class="bg-white shadow rounded p-6">
                <Form
                    :form="form"
                    @submit="submit"
                    :years="years"
                    :semesters="semesters"
                    :courses="courses"
                    :sections="sections"
                />
            </div>
        </div>
    </AppLayout>
</template>
