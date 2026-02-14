<script setup>
import { ref } from "vue";

const props = defineProps({
  course: {
    type: Object,
    required: true,
  },
  section: {
    type: Object,
    required: true,
  },
  students: {
    type: Array,
    required: true,
  },
});

// reactive id of opened student modal (null = none)
const openStudentModal = ref(null);

// helper open/close functions
function openModal(id) {
  openStudentModal.value = id;
}
function closeModal() {
  openStudentModal.value = null;
}
</script>

<template>
  <div class="max-w-7xl mx-auto py-10 px-4">
    <h1 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">
      Students for {{ course.name }} ({{ course.code }})
    </h1>
    
    <div class="overflow-x-auto rounded-xl shadow ring-1 ring-black ring-opacity-5">
      <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-700">
          <tr>
            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">ID</th>
            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Name</th>
            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">ID Number</th>
            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
          <tr
            v-for="student, index in students"
            :key="student.id"
            class="hover:bg-gray-50 dark:hover:bg-gray-700 transition"
          >
            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ index + 1 }}</td>
            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
              {{ student.firstName }} {{ student.middleName }} {{ student.lastName }}
            </td>
            <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
              {{ student.idNo }}
            </td>
            <td class="px-6 py-4 space-x-2">
              <!-- open modal using reactive state -->
              <button
                @click="openModal(student.id)"
                class="inline-flex items-center px-3 py-1 text-sm font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-white rounded hover:bg-gray-200 dark:hover:bg-gray-600"
              >
                Detail
              </button>

              <!-- Modal (controlled by reactive state) -->
              <div
                v-if="openStudentModal === student.id"
                class="fixed inset-0 z-50 flex items-center justify-center"
                role="dialog"
                aria-modal="true"
              >
                <!-- backdrop -->
                <div
                  class="absolute inset-0 bg-black bg-opacity-50"
                  @click="closeModal"
                ></div>

                <!-- modal panel -->
                <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-md mx-4 z-10 overflow-hidden">
                  <div class="px-6 py-4 border-b dark:border-gray-700 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Student Info</h3>
                    <button
                      @click="closeModal"
                      class="text-gray-500 hover:text-gray-700 dark:text-gray-300"
                      aria-label="Close"
                    >
                      ✕
                    </button>
                  </div>
                  
                  <div class="px-6 py-4 space-y-2 text-sm text-gray-700 dark:text-gray-300">
                    <p><strong>Name:</strong> {{ student.firstName }} {{ student.middleName }} {{ student.lastName }}</p>
                    <p><strong>ID Number:</strong> {{ student.idNo }}</p>
                    <p v-if="student.email"><strong>Email:</strong> {{ student.email }}</p>
                    <p v-if="student.phone"><strong>Phone:</strong> {{ student.phone }}</p>
                    <!-- add more fields as needed -->
                  </div>

                  <div class="px-6 py-3 border-t dark:border-gray-700 flex justify-end">
                    <button
                      @click="closeModal"
                      class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700"
                    >
                      Close
                    </button>
                  </div>
                </div>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
