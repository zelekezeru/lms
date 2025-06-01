<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import Form from "./Form.vue";
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
  center: {
    type: Object,
    required: true,
  },
  coordinators: {
    type: Array,
    default: () => [],
  },
});

const form = useForm({
  name: props.center?.name || "",
  code: props.center?.code || "",
  address: props.center?.address || "",
  status: props.center?.status || "Inactive",
  coordinator_id: props.center?.coordinator_id || null,
  coordinators: props.coordinators, // Pass to Form.vue for select dropdown
  _method: "PATCH",
});

const submit = (id) => {
  form.post(route("centers.update", { center: id }));
};
</script>

<template>
  <AppLayout>
    <div class="max-w-4xl mx-auto p-6">
      <div class="mb-6 text-center">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
          {{ $t('centers.edit_title', 'Edit Center') }}
        </h2>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
          {{ $t('centers.edit_description', 'Modify center details below.') }}
        </p>
      </div>
      <Form :form="form" @submit="submit(center.id)" />
    </div>
  </AppLayout>
</template>
