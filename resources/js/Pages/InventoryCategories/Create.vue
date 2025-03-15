<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { useForm } from "@inertiajs/vue3";
import { ref } from "vue";
import { PhotoIcon } from "@heroicons/vue/24/outline";
import Form from "./Form.vue";

defineProps({
    departments: { type: Object, required: true },
    roles: { type: Object, required: true },
});

// Initialize form data
const form = useForm({
    name: "",
});

// Submit the form
const submit = () => {
    form.post(route("inventoryCategories.store"));
};
</script>

<template>
    <AppLayout>
        <div class="max-w-4xl mx-auto p-6">
            <div class="mb-6 text-center">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    Create inventory Supplier
                </h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                    Fill in the details to add a new inventory Supplier.
                </p>
            </div>

            <div class="dark:bg-gray-900 bg-white-100 shadow-lg rounded-lg p-6">
                <Form :form="form" @submit="submit" />
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Smooth fade-in effect for image preview */
img {
    transition: opacity 0.3s ease-in-out;
}
</style>
