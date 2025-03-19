<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { defineProps } from "vue";
import { Link, router } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import { PencilIcon, TrashIcon } from "@heroicons/vue/24/solid";

// Define the props for the department
defineProps({
  department: {
    type: Object,
    required: true,
  },
});

// Delete function with SweetAlert confirmation
const deleteDepartment = (id) => {
  Swal.fire({
    title: "Are you sure?",
    text: "You won't be able to revert this!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "Yes, delete it!",
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(route("departments.destroy", { department: id }), {
        onSuccess: () => {
          Swal.fire("Deleted!", "The department has been deleted.", "success");
        },
      });
    }
  });
};
</script>

<template>
  <AppLayout>
    <div class="max-w-2xl mx-auto p-6">
      <h1 class="text-3xl font-semibold mb-6 text-gray-900 dark:text-gray-100 text-center">
        {{ department.name }} Department
      </h1>

      <div class="dark:bg-gray-800 shadow-lg rounded-xl p-6 border dark:border-gray-700">
        <div class="grid grid-cols-2 gap-4">
          
          <!-- Department Code -->
          <div class="flex flex-col">
            <span class="text-sm text-gray-500 dark:text-gray-400">Code</span>
            <span class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ department.code }}</span>
          </div>

          <!-- Department Program -->
          <div class="flex flex-col">
            <span class="text-sm text-gray-500 dark:text-gray-400">Program</span>
            <span class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ department.program.name }}</span>
          </div>

          <!-- Description -->
          <div class="flex flex-col">
            <span class="text-sm text-gray-500 dark:text-gray-400">Description</span>
            <span class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ department.description }}</span>
          </div>

          <!-- Department Duration -->
          <div class="flex flex-col">
            <span class="text-sm text-gray-500 dark:text-gray-400">Duration</span>
            <span class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ department.duration }}</span>
          </div>

        </div>

        <!-- Edit and Delete Buttons -->
        <div class="flex justify-end mt-6 space-x-2">
          <!-- Edit Button, only show if user has permission -->
          <div v-if="userCan('update-departments')">
            <Link :href="route('departments.edit', { department: department.id })" class="text-blue-500 hover:text-blue-700">
              <PencilIcon class="w-5 h-5" />
            </Link>
          </div>

          <!-- Delete Button, only show if user has permission -->
          <div v-if="userCan('delete-departments')">
            <button @click="confirmDelete(department.id)" class="text-red-500 hover:text-red-700">
              <TrashIcon class="w-5 h-5" />
            </button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
