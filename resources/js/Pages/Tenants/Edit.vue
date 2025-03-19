<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import Form from "./Form.vue";
import { usePage } from "@inertiajs/vue3";
import { ref } from "vue";

const { tenant } = usePage().props;
</script>

<template>
    <AppLayout>
        <div class="max-w-4xl mx-auto p-6">
          <div class="mb-6 text-center">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Edit Tenant</h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
              Modify the details of the tenant.
            </p>
          </div>
          <Form :isEdit="true" :tenant="tenant" />
        </div>
    </AppLayout>
</template>
