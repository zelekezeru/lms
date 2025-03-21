<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import Form from "@/Pages/Departments/Form.vue";
import { useForm, usePage } from "@inertiajs/vue3";
import Swal from "sweetalert2";

defineProps({ department: Object, users: Array, programs: Array });

const form = useForm({
  name: usePage().props.department.name,
  code: usePage().props.department.code,
  user_id: usePage().props.department.user_id,
  duration: usePage().props.department.duration,
  description: usePage().props.department.description,
  program_id: usePage().props.department.program_id,
});

const submit = (id) => {
  form.patch(route("departments.update", { department: id }), {
    onSuccess: () => Swal.fire("Updated", "The department has been updated successfully", "success"),
  });
};
</script>

<template>
  <AppLayout>
    <div class="flex justify-center">
      <Form :form="form" :submit="() => submit(department.id)" :users="users" :programs="programs" />
    </div>
  </AppLayout>
</template>
