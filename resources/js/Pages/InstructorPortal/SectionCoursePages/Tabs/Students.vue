<script setup>
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
              {{ student.firstName }} {{ student.lastName }}
            </td>
            <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
              {{ student.idNo }}
            </td>
            <td class="px-6 py-4 space-x-2">
              <button
                class="inline-flex items-center px-3 py-1 text-sm font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-white rounded hover:bg-gray-200 dark:hover:bg-gray-600"
              >
                Detail
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
