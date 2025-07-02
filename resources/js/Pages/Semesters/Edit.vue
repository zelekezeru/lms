<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { useForm } from "@inertiajs/vue3";
import Form from "./Form.vue";

const props = defineProps({
    semester: Object,
    years: Array,
});

const form = useForm({
    name: props.semester.name,
    year_id: props.semester.year.id,
    level: props.semester.level,
    start_date: props.semester.start_date,
    end_date: props.semester.end_date,
    _method: "PATCH",
});

const submit = () => {
    form.post(route("semesters.update", { semester: props.semester.id }), {
        onSuccess: () => {
            Swal.fire("Success!", "The semester has been updated.", "success");
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
                    Ecit {{ props.semester.name }}
                </h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                    Create a new semester by feeling out this forms.
                </p>
            </div>

            <!-- Form Card -->
            <div
                class="bg-white dark:bg-gray-900 shadow-lg rounded-lg p-6 transition"
            >
                <Form :form="form" @submit="submit" :years="years" />
            </div>
        </div>
    </AppLayout>
</template>
