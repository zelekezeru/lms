<script setup>
import Form from "./Form.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import { usePage } from "@inertiajs/vue3";

const { departments } = usePage().props;
const { roles } = usePage().props;
</script>

<template>
    <AppLayout>
        <Form :departments="departments" :roles="roles"/>
    </AppLayout>
</template>
