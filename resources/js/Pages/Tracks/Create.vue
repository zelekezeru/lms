<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import Form from "@/Pages/Tracks/Form.vue";
import { useForm } from "@inertiajs/vue3";

defineProps({ users: Array, programs: Array });

const form = useForm({
    name: "",
    description: "",
    user_id: "",
    program_id: "",
    duration: "",
});

const submit = () => form.post(route("tracks.store"));
</script>

<template>
    <AppLayout>
        <div class="max-w-5xl mx-auto p-6">
            <!-- Centered and Enhanced Title -->
            <div class="mb-6 text-center">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    {{ $t('programs.tracks.create_title') }}
                </h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                    {{ $t('programs.tracks.create_description') }}
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
                    :programs="programs"
                />
            </div>
        </div>
    </AppLayout>
</template>
