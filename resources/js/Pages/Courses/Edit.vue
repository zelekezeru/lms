<script setup>
import { useForm } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import Form from "./Form.vue";

const props = defineProps({
    course: {
        type: Object,
        required: true,
    },

    programs: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    name: props.course.name,
    code: props.course.code,
    programs: props.course.programs.map((program) => program.id),
    code: props.course.code,
    credit_hours: props.course.credit_hours,
    duration: props.course.duration,
    description: props.course.description ?? "",
    is_training: Boolean(props.course.is_training),
    status: Boolean(props.course.status),
    is_published: Boolean(props.course.is_published),
    is_approved: Boolean(props.course.is_approved),
    is_completed: Boolean(props.course.is_completed),
});

const submit = (id) => {
    form.put(route("courses.update", id));
};
</script>

<template>
    <AppLayout>
        <div class="max-w-5xl mx-auto p-6">
            <!-- Centered and Enhanced Title -->
            <div class="mb-6 text-center">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    Edit "{{ course.name }}"
                </h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                    Please fill out the form below to Update the course.
                </p>
            </div>

            <!-- Form Card -->
            <div
                class="bg-white dark:bg-gray-900 shadow-lg rounded-lg p-6 transition"
            >
                <Form
                    :form="form"
                    :programs="programs"
                    @submit="submit(course.id)"
                />
            </div>
        </div>
    </AppLayout>
</template>
