<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import Form from "./Form.vue";
import { usePage } from "@inertiajs/vue3";
import { ref } from "vue";

const { departments, roles } = usePage().props;
</script>

<template>
    <AppLayout>
        <div class="max-w-4xl mx-auto p-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                Create Employee
            </h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Fill in the details to add a new employee.
            </p>
            <Form :departments="departments" :roles="roles" />
        </div>
    </AppLayout>
</template>
