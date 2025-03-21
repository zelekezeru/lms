<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import Form from "@/Pages/Departments/Form.vue";
import { useForm } from "@inertiajs/vue3";

defineProps({ users: Array, programs: Array });

const form = useForm({ name: "", code: "", description: "", duration: "", user_id: "", program_id: "" });
const submit = () => form.post(route("departments.store"));
</script>

<template>
  <AppLayout>
    <div class="flex justify-center">
      <Form :form="form" :submit="submit" :users="users" :programs="programs" />
    </div>
  </AppLayout>
</template>
