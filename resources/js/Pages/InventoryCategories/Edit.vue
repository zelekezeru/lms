<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { useForm } from "@inertiajs/vue3";
import Form from "./Form.vue";
const props = defineProps({
    inventoryCategory: { type: Object, required: true },
});

// Initialize form data
const form = useForm({
    name: props.inventoryCategory.name || "",
    _method: "PATCH",
});

// Submit the form
const submit = (id) => {
    form.post(route("inventoryCategories.update", { inventoryCategory: id }));
};
</script>

<template>
    <AppLayout>
        <div class="max-w-4xl mx-auto p-6">
            <div class="mb-6 text-center">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    Edit inventory Supplier
                </h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                    Fill in the details to edit inventory Supplier.
                </p>
            </div>

            <div class="bg-white-100 dark:bg-gray-900 shadow-lg rounded-lg p-6">
                <Form :form="form" @submit="submit(inventoryCategory.id)" />
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
