<script setup>
import Form from "./Form.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import { usePage } from "@inertiajs/vue3";

const { departments } = usePage().props;
const { course } = usePage().props;
const { user } = usePage().props;

</script>

<template>
    <AppLayout>
        <Form :departments="departments" />
    </AppLayout>
</template>
