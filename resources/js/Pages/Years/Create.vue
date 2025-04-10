<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import Form from "./Form.vue";
import { useForm } from "@inertiajs/vue3";

const form = useForm({
    name: "",
    status: "inactive",
    is_approved: true,
    is_completed: false,
});
console.log(form.status)
const submit = () => {
    form.post(route("years.store"));
};
</script>

<template>
    <AppLayout title="Create Year">
        <div class="max-w-4xl mx-auto p-6">
            <div class="mb-6 text-center">
                <h2 class="text-2xl font-bold">Add Academic Year</h2>
                <p class="mt-2 text-gray-600">
                    Fill in the year details below.
                </p>
            </div>
            <Form :form="form" @submit="submit" />
        </div>
    </AppLayout>
</template>
