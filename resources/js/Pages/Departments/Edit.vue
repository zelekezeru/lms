<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { defineProps } from "vue";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { router, useForm, usePage } from "@inertiajs/vue3";

// Define props for the department and units (though units may not be needed for departments)
defineProps({
  department: {
    type: Object,
    required: true,
  },
});

console.log(usePage().props.department.name);

const form = useForm({
  'name': usePage().props.department.name,
  'code': usePage().props.department.code,
  'description': usePage().props.department.description,
  'established_year': usePage().props.department.establishedYear,
  'contact_email': usePage().props.department.contactEmail,
  'phone': usePage().props.department.phone,
  'location': usePage().props.department.location,
});

// Submit form function
const submit = (id) => {
  form.patch(route('departments.update', { department: id }), {
    onSuccess: () => {
      Swal.fire("Updated", "The department has been updated successfully", "success");
    },
  });
};
</script>

<template>
  <AppLayout>
    <div class="flex justify-center">
      <div class="dark:bg-gray-800 shadow-lg rounded-md p-6 max-w-lg w-full">
        <h2 class="text-xl font-semibold mb-4 dark:text-gray-200">Edit "{{ department.name }}"</h2>
        <form @submit.prevent="submit(department.id)">
          <!-- Department Name -->
          <div class="mb-4">
            <InputLabel for="name" value="Name" class="block mb-1 dark:text-gray-200" />
            <TextInput
              id="name"
              type="text"
              v-model="form.name"
              required
              autofocus
              autocomplete="name"
              class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-200"
            />
            <InputError :message="form.errors.name" class="mt-2 text-sm text-red-600 dark:text-red-400" />
          </div>

          <!-- Department Description -->
          <div class="mb-4">
            <InputLabel for="description" value="Description" class="block mb-1 dark:text-gray-200" />
            <TextInput
              id="description"
              type="text"
              v-model="form.description"
              required
              autocomplete="description"
              class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-200"
            />
            <InputError :message="form.errors.description" class="mt-2 text-sm text-red-600 dark:text-red-400" />
          </div>

          <!-- Submit Button -->
          <PrimaryButton v-if="!form.processing" class="w-full dark:bg-gray-200 dark:text-gray-800">Submit</PrimaryButton>
        </form>
      </div>
    </div>
  </AppLayout>
</template>
