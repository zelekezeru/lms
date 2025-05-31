<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import Form from "./Form.vue";
import { useForm, usePage } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps({
  tenant: {
    type: Object,
    required: true,
  }
});

const form = useForm({
  name: props.tenant?.name || '',
  email: props.tenant?.email || '',
  code: props.tenant?.code || '',
  phone: props.tenant?.phone || '',
  address: props.tenant?.address || '',
  contact_person: props.tenant?.contact_person || '',
  contact_phone: props.tenant?.contact_phone || '',
  contact_email: props.tenant?.contact_email || '',
  logo: '',
  status: props.tenant?.status,
  paid: props.tenant?.paid,
  _method: 'PATCH',
});


const submit = (id) => {
    form.post(route('tenants.update', { tenant: id }), {
        forceFormData: true,
    });
};


</script>

<template>
    <AppLayout>
        <div class="max-w-4xl mx-auto p-6">
          <div class="mb-6 text-center">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $t('tenant.edit_title') }}</h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
              {{ $t('tenant.edit_description') }}
            </p>
          </div>
          <Form :form="form" @submit="submit(tenant.id)"/>
        </div>
    </AppLayout>
</template>
