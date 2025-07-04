<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { useForm } from "@inertiajs/vue3";
import Form from "./Form.vue";

const props = defineProps({
    years: Array,
    studyModes: Array,
});

const form = useForm({
    name: "",
    year_id: "",
    level: "",
    start_date: "",
    end_date: "",
    study_modes: {},
});

const submit = () => {
    form.post(route("semesters.store"), {
        onSuccess: () => {
            Swal.fire("Success!", "The semester has been created.", "success");
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
                    Create Semester
                </h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                    Create a new semester by feeling out this forms.
                </p>
            </div>

            <!-- Form Card -->
            <div
                class="bg-white dark:bg-gray-900 shadow-lg rounded-lg p-6 transition"
            >
                <Form
                    :form="form"
                    @submit="submit"
                    :years="years"
                    :study-modes="studyModes"
                />
            </div>
        </div>
    </AppLayout>
</template>
