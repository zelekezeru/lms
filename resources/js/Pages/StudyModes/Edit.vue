<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import Form from "./Form.vue";
import { useForm, usePage } from "@inertiajs/vue3";
import Swal from "sweetalert2";


defineProps({
    studyMode: {
        type: Object,
        required: true,
    },
    programs: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    program_id: usePage().props.studyMode.program.id,
    mode: usePage().props.studyMode.mode,
    duration: usePage().props.studyMode.duration,
    fees: usePage().props.studyMode.fees,
});

const submit = (id) => {
    form.patch(route("studyModes.update", { studyMode: id }), {
        onSuccess: () =>
            Swal.fire(
                "Updated",
                "The studyMode has been updated successfully",
                "success"
            ),
    });
};
</script>

<template>
    <AppLayout>
        <div class="max-w-5xl mx-auto p-6">
            <!-- Centered and Enhanced Title -->
            <div class="mb-6 text-center">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    Edit "{{ studyMode.program.name }} ({{ studyMode.mode }})"
                </h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                    Please fill out the form below to Update the studyMode.
                </p>
            </div>

            <!-- Form Card -->
            <div
                class="bg-white dark:bg-gray-900 shadow-lg rounded-lg p-6 transition"
            >
                <Form
                    :form="form"
                    @submit="submit(studyMode.id)"
                    :users="users"
                    :programs="programs"
                />
            </div>
        </div>
    </AppLayout>
</template>
