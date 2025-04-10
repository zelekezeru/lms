<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { defineProps, ref } from "vue";
import { Link, router } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import { PencilIcon, TrashIcon } from "@heroicons/vue/24/solid";

// Define the props for the instructor
defineProps({
  instructor: {
    type: Object,
    required: true,
  },

});

const imageLoaded = ref(false);

const handleImageLoad = () => {
    console.log("hello");

    imageLoaded.value = true;
};

// Delete function with SweetAlert confirmation
const deleteInstructor = (id) => {
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
      router.delete(route("instructors.destroy", { instructor: id }), {
        onSuccess: () => {
          Swal.fire("Deleted!", "The instructor has been deleted.", "success");
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
        {{ instructor.user.name }}
      </h1>

      <div class="dark:bg-gray-800 shadow-lg rounded-xl p-6 border dark:border-gray-700">
        <!-- Instructor Image -->
          <div class="flex justify-center mb-8">
              <div v-if="!imageLoaded" class="rounded-full w-44 h-44 bg-gray-300 dark:bg-gray-700 animate-pulse" ></div>
              
              <img v-show="imageLoaded" class="rounded-full w-44 h-44 object-contain bg-gray-400"
                  :src="instructor.profileImg" :alt="`Logo of ` + instructor.name"
                  @load="handleImageLoad"/>
          </div>

          <!-- instructor Full Name -->
          <div class="flex flex-col">
            <span class="text-sm text-gray-500 dark:text-gray-400">Full Name</span>
            <span class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ instructor.user.name }}</span>
          </div>

          <!-- Email -->
          <div class="flex flex-col">
            <span class="text-sm text-gray-500 dark:text-gray-400">Email</span>
            <span class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ instructor.user.email }}</span>
          </div>

          <!-- Hire Date -->
          <div class="flex flex-col">
            <span class="text-sm text-gray-500 dark:text-gray-400">Hire Date</span>
            <span class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ instructor.hireDate }}</span>
          </div>
          
          <!-- Employment Type -->
          <div class="flex flex-col">
            <span class="text-sm text-gray-500 dark:text-gray-400">Employment Type</span>
            <span class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ instructor.employmentType }}</span>
          </div>

          <!-- Bio -->
          <div class="flex flex-col">
            <span class="text-sm text-gray-500 dark:text-gray-400">Bio</span>
            <span class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ instructor.bio }}</span>
          </div>
                    
          <!-- Status -->
          <div class="flex flex-col">
              <span class="text-sm text-gray-500 dark:text-gray-400"
                  >Status</span
              >
              <span
                  class="text-lg font-medium text-gray-900 dark:text-gray-100"
                  >
                  <div v-if="instructor.status == 0" class="text-red-500">
                      Inactive
                  </div>
                  <div v-else class="text-green-500">
                      Active
                  </div>
              </span>
          </div>
        </div>

        <!-- Edit and Delete Buttons -->
        <div class="flex justify-end mt-6 space-x-2">
          <!-- Edit Button, only show if user has permission -->
          <div v-if="userCan('update-instructors')">
            <Link :href="route('instructors.edit', { instructor: instructor.id })" class="text-blue-500 hover:text-blue-700">
              <PencilIcon class="w-5 h-5" />
            </Link>
          </div>

          <!-- Delete Button, only show if user has permission -->
          <div v-if="userCan('delete-instructors')">
            <button @click="deleteInstructor(instructor.id)" class="text-red-500 hover:text-red-700">
              <TrashIcon class="w-5 h-5" />
            </button>
          </div>
        </div>
    </div>
  </AppLayout>
</template>

