<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import { ArrowPathIcon, EyeIcon } from '@heroicons/vue/24/outline'

// Example data, replace with your actual logic

const props = defineProps({
    sections: Object,
    search: String,
});

// If using i18n and route helpers, make sure they're globally available
</script>

<template>
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
      <thead class="bg-gray-50 dark:bg-gray-800">
        <tr>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
            #
          </th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
            Code
          </th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
            Name
          </th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
            Number of Students
          </th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
            Grade Submission(%)
          </th>
        </tr>
      </thead>
      <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
        <tr v-for="section, index in props.sections.data" :key="section.id">
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ index + 1 }}</td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ section.code }}</td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ section.name }}</td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ section.students.length }}</td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
            <div class="w-full flex items-center gap-2">
              <div class="w-full bg-gray-200 dark:bg-gray-700 rounded h-3 overflow-hidden">
              <div
                :class="[
                'h-3 rounded transition-all duration-300',
                section.grade_submission_percentage >= 80
                  ? 'bg-green-500'
                  : section.grade_submission_percentage >= 50
                  ? 'bg-yellow-400'
                  : 'bg-red-500'
                ]"
                :style="{ width: ((section.grade_submission_percentage ?? 0) + '%') }"
              ></div>
              </div>
              <span
              class="min-w-[2.5rem] text-xs text-right font-semibold"
              :class="section.grade_submission_percentage >= 80
                ? 'text-green-600'
                : section.grade_submission_percentage >= 50
                ? 'text-yellow-600'
                : 'text-red-600'"
              >{{ section.grade_submission_percentage ?? 0 }} %</span>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
</template>