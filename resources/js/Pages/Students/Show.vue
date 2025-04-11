<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { defineProps, ref } from 'vue';
import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';
import { PencilIcon, TrashIcon } from "@heroicons/vue/24/solid";

const props = defineProps({
  student: {
    type: Object,
    required: true,
  },
});

const imageLoaded = ref(false);
const handleImageLoad = () => {
  imageLoaded.value = true;
};

const deleteStudent = (id) => {
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
      router.delete(route("students.destroy", { student: id }), {
        onSuccess: () => {
          Swal.fire("Deleted!", "The student has been deleted.", "success");
        },
      });
    }
  });
};
</script>

<template>
  <Head title="Student Details" />
  <AppLayout>
    <div class="max-w-2xl mx-auto p-6">
      <h1 class="text-3xl font-semibold mb-6 text-gray-900 dark:text-gray-100 text-center">
        Student Details
      </h1>
      <div class="dark:bg-gray-800 shadow-lg rounded-xl p-6 border dark:border-gray-700">
        <!-- Profile Image -->
        <div class="flex justify-center mb-8">
          <div
            v-if="!imageLoaded"
            class="rounded-full w-44 h-44 bg-gray-300 dark:bg-gray-700 animate-pulse"
          ></div>
          <img
            v-show="imageLoaded"
            class="rounded-full w-44 h-44 object-contain bg-gray-400"
            :src="student.profile_image_url"
            :alt="`Profile image of ` + student.student_name"
            @load="handleImageLoad"
          />
        </div>

        <!-- Personal Details Grid -->
        <div class="grid sm:grid-cols-2 gap-4">
          <!-- ID -->
          <div class="flex flex-col">
            <span class="text-sm text-gray-500 dark:text-gray-400">ID</span>
            <span class="text-lg font-medium text-gray-900 dark:text-gray-100">
              {{ student.id }}
            </span>
          </div>
          <!-- Student ID -->
          <div class="flex flex-col">
            <span class="text-sm text-gray-500 dark:text-gray-400">Student ID</span>
            <span class="text-lg font-medium text-gray-900 dark:text-gray-100">
              {{ student.student_id }}
            </span>
          </div>
          <!-- Full Name -->
          <div class="flex flex-col">
            <span class="text-sm text-gray-500 dark:text-gray-400">Full Name</span>
            <span class="text-lg font-medium text-gray-900 dark:text-gray-100">
              {{ student.student_name }} {{ student.father_name }} {{ student.grand_father_name }}
            </span>
          </div>
          <!-- Email -->
          <div class="flex flex-col">
            <span class="text-sm text-gray-500 dark:text-gray-400">Email</span>
            <span class="text-lg font-medium text-gray-900 dark:text-gray-100">
              {{ student.email }}
            </span>
          </div>
          <!-- Mobile Phone -->
          <div class="flex flex-col">
            <span class="text-sm text-gray-500 dark:text-gray-400">Mobile Phone</span>
            <span class="text-lg font-medium text-gray-900 dark:text-gray-100">
              {{ student.mobile_phone }}
            </span>
          </div>
          <!-- Office Phone -->
          <div class="flex flex-col">
            <span class="text-sm text-gray-500 dark:text-gray-400">Office Phone</span>
            <span class="text-lg font-medium text-gray-900 dark:text-gray-100">
              {{ student.office_phone }}
            </span>
          </div>
          <!-- Date of Birth -->
          <div class="flex flex-col">
            <span class="text-sm text-gray-500 dark:text-gray-400">Date of Birth</span>
            <span class="text-lg font-medium text-gray-900 dark:text-gray-100">
              {{ student.date_of_birth }}
            </span>
          </div>
          <!-- Marital Status -->
          <div class="flex flex-col">
            <span class="text-sm text-gray-500 dark:text-gray-400">Marital Status</span>
            <span class="text-lg font-medium text-gray-900 dark:text-gray-100">
              {{ student.marital_status }}
            </span>
          </div>
          <!-- Sex -->
          <div class="flex flex-col">
            <span class="text-sm text-gray-500 dark:text-gray-400">Sex</span>
            <span class="text-lg font-medium text-gray-900 dark:text-gray-100">
              {{ student.sex }}
            </span>
          </div>
        </div>

        <!-- Academic & Church Details Grid -->
        <div class="grid sm:grid-cols-2 gap-4 mt-6">
          <!-- Academic Year -->
          <div class="flex flex-col">
            <span class="text-sm text-gray-500 dark:text-gray-400">Academic Year</span>
            <span class="text-lg font-medium text-gray-900 dark:text-gray-100">
              {{ student.academic_year }}
            </span>
          </div>
          <!-- Semester -->
          <div class="flex flex-col">
            <span class="text-sm text-gray-500 dark:text-gray-400">Semester</span>
            <span class="text-lg font-medium text-gray-900 dark:text-gray-100">
              {{ student.semester }}
            </span>
          </div>
          <!-- Program -->
          <div class="flex flex-col">
            <span class="text-sm text-gray-500 dark:text-gray-400">Program</span>
            <span class="text-lg font-medium text-gray-900 dark:text-gray-100">
              {{ student.program }}
            </span>
          </div>
          <!-- Pastor's Name -->
          <div class="flex flex-col">
            <span class="text-sm text-gray-500 dark:text-gray-400">Pastor's Name</span>
            <span class="text-lg font-medium text-gray-900 dark:text-gray-100">
              {{ student.pastor_name }}
            </span>
          </div>
          <!-- Pastor's Phone -->
          <div class="flex flex-col">
            <span class="text-sm text-gray-500 dark:text-gray-400">Pastor's Phone</span>
            <span class="text-lg font-medium text-gray-900 dark:text-gray-100">
              {{ student.pastor_phone }}
            </span>
          </div>
          <!-- Address -->
          <div class="flex flex-col">
            <span class="text-sm text-gray-500 dark:text-gray-400">Address</span>
            <span class="text-lg font-medium text-gray-900 dark:text-gray-100">
              {{ student.address_1 }}
            </span>
          </div>
          <!-- Position/Denomination -->
          <div class="flex flex-col">
            <span class="text-sm text-gray-500 dark:text-gray-400">Position/Denomination</span>
            <span class="text-lg font-medium text-gray-900 dark:text-gray-100">
              {{ student.position_denomination }}
            </span>
          </div>
          <!-- Church Name -->
          <div class="flex flex-col">
            <span class="text-sm text-gray-500 dark:text-gray-400">Church Name</span>
            <span class="text-lg font-medium text-gray-900 dark:text-gray-100">
              {{ student.church_name }}
            </span>
          </div>
          <!-- Church Address -->
          <div class="flex flex-col">
            <span class="text-sm text-gray-500 dark:text-gray-400">Church Address</span>
            <span class="text-lg font-medium text-gray-900 dark:text-gray-100">
              {{ student.church_address }}
            </span>
          </div>
          <!-- Office Use Notes (Full Width) -->
          <div class="flex flex-col sm:col-span-2">
            <span class="text-sm text-gray-500 dark:text-gray-400">Office Use Notes</span>
            <span class="text-lg font-medium text-gray-900 dark:text-gray-100">
              {{ student.office_use_notes }}
            </span>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end mt-6 space-x-2">
          <!-- Edit Button -->
          <div v-if="userCan('update-students')">
            <Link
              :href="route('students.edit', student.id)"
              class="text-blue-500 hover:text-blue-700"
              title="Edit"
            >
              <PencilIcon class="w-5 h-5" />
                            <span>Edit</span>
            </Link>
          </div>
          <!-- Delete Button -->
          <div v-if="userCan('delete-students')">
            <button
              @click="deleteStudent(student.id)"
              class="text-red-500 hover:text-red-700"
              title="Delete"
            >
              <TrashIcon class="w-5 h-5" />
                            <span>Delete</span>
            </button>
          </div>
        </div>

        <!-- Back to List Button -->
        <div class="flex justify-end mt-4">
          <Link
            :href="route('students.index')"
            class="inline-flex items-center rounded-md border border-transparent bg-gray-800 dark:bg-gray-200 dark:text-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-gray-700 focus:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
          >
            Back to List
          </Link>
        </div>
      </div>
    </div>
  </AppLayout>
</template>