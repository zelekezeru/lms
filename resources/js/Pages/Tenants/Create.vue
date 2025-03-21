<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import Form from "./Form.vue";
import { useForm, usePage } from "@inertiajs/vue3";
import { ref } from "vue";


const form = useForm({
  name: '',
  email: '',
  code:  '',
  phone: '',
  address: '',
  contact_person:  '',
  contact_phone:  '',
  contact_email:  '',
  logo: '',
  status: '',
  paid: '',
});


const submit = (id) => {
    form.post(route('tenants.store'));
};

</script>

<template>
    <AppLayout>
        <div class="max-w-4xl mx-auto p-6">
          <div class="mb-6 text-center">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Register Tenant</h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
              Fill in the details of a new tenant.
            </p>
          </div>
          <Form :form="form" @submit="submit" />
        </div>
    </AppLayout>
</template>
