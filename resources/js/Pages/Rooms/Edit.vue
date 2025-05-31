<script setup>
import { useForm } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import Form from "./Form.vue";

const props = defineProps({
    room: {
        type: Object,
        required: true,
    },

    programs: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    name: props.room.name,
    capacity: props.room.capacity,
    location: props.room.location,
});

const submit = (id) => {
    form.put(route("rooms.update", id));
};
</script>

<template>
    <AppLayout>
        <div class="max-w-5xl mx-auto p-6">
            <!-- Centered and Enhanced Title -->
            <div class="mb-6 text-center">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    Edit "{{ room.name }}"
                </h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                    Please fill out the form below to Update the room.
                </p>
            </div>
            <!-- Form Card -->
            <div
                class="bg-white dark:bg-gray-900 shadow-lg rounded-lg p-6 transition"
            >
                <Form
                    :form="form"
                    @submit="submit(room.id)"
                />
            </div>
        </div>
    </AppLayout>
</template>
