<script setup>
import { defineProps } from "vue";

// Props
const props = defineProps({
  student: {
    type: Object,
    required: true,
  },
  grades: {
    type: Array,
    default: () => [],
  },
});
</script>

<template>
  <div class="max-w-5xl mx-auto p-6">
    <h2 class="text-2xl font-semibold mb-4 text-gray-800 dark:text-gray-100">
      Academic Grade Report for
      {{ props.student.firstName }} {{ props.student.middleName }} {{ props.student.lastName }}
    </h2>

    <div
      v-if="grades && grades.length > 0"
      class="overflow-x-auto"
    >
      <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 shadow rounded-lg">
        <thead class="bg-gray-100 dark:bg-gray-800">
          <tr>
            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">#</th>
            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Course</th>
            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Semester</th>
            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Grade Letter</th>
            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Grade Point</th>
          </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">
          <tr
            v-for="(grade, index) in grades"
            :key="`${grade.course?.id}-${grade.semester?.id}-${index}`"
            class="hover:bg-gray-50 dark:hover:bg-gray-700"
          >
            <td class="px-4 py-2 text-gray-800 dark:text-gray-200">{{ index + 1 }}</td>
            <td class="px-4 py-2 text-gray-700 dark:text-gray-300">{{ grade.course?.name || 'N/A' }}</td>
            <td class="px-4 py-2 text-gray-700 dark:text-gray-300">{{ grade.semester?.name || 'N/A' }}</td>
            <td class="px-4 py-2 text-green-600 dark:text-green-400 font-semibold">{{ grade.grade_letter || '-' }}</td>
            <td class="px-4 py-2 text-blue-600 dark:text-blue-400 font-semibold">{{ grade.grade_point || '-' }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-else class="text-center text-gray-500 dark:text-gray-400 mt-10">
      No grade records found for this student.
    </div>
  </div>
</template>
