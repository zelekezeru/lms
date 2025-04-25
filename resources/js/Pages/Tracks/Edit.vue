<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import Form from "@/Pages/Tracks/Form.vue";
import { useForm, usePage } from "@inertiajs/vue3";
import Swal from "sweetalert2";

defineProps({ track: Object, users: Array, programs: Array });

const form = useForm({
    name: usePage().props.track.name,
    user_id: usePage().props.track.user_id,
    description: usePage().props.track.description,
    program_id: usePage().props.track.program_id,
});

const submit = (id) => {
    form.patch(route("tracks.update", { track: id }), {
        onSuccess: () =>
            Swal.fire(
                "Updated",
                "The track has been updated successfully",
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
                    Edit "{{ track.name }}"
                </h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                    Please fill out the form below to Update the track.
                </p>
            </div>

            <!-- Form Card -->
            <div
                class="bg-white dark:bg-gray-900 shadow-lg rounded-lg p-6 transition"
            >
                <Form
                    :form="form"
                    @submit="submit(track.id)"
                    :users="users"
                    :programs="programs"
                />
            </div>
        </div>
    </AppLayout>
</template>
