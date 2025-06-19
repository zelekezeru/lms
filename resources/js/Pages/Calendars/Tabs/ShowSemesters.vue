<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import { ArrowPathIcon, EyeIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    oldSemesters: Object,
    search: String,
});
</script>

<template>
    
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
      <thead class="bg-gray-50 dark:bg-gray-800">
        <tr>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
            #
          </th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
            Semester Name
          </th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
            Academic Year
          </th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
            Number of Sections
          </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Status
            </th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
            Completion ( % )
          </th>
        </tr>
      </thead>
      <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
        <tr v-for="semester, index in props.oldSemesters" :key="semester.id">
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ index + 1 }}</td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
            <Link :href="route('semesters.show', { semester: semester.id })" class="text-blue-500 hover:text-blue-700">
              {{ semester.name }}
            </Link>
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ semester.year.name }}</td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ semester.sections.length ?? 0 }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                <span
                :class="[
                    'px-2 py-1 rounded text-xs font-semibold',
                    semester.is_completed === 1
                    ? 'bg-green-400 text-green-800 dark:bg-green-200 dark:text-green-800'
                    : 'bg-gray-200 text-gray-800 dark:bg-gray-700 dark:text-gray-200'
                ]"
                >
                {{ $t('semester.isCompleted') }}
                </span>
            </td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
            <div class="w-full flex items-center gap-2">
              <div class="w-full bg-gray-200 dark:bg-gray-700 rounded h-3 overflow-hidden">
                <div
                  :class="[
                    'h-3 rounded transition-all duration-300',
                    semester.completion_percentage >= 80
                      ? 'bg-green-500'
                      : semester.completion_percentage >= 50
                      ? 'bg-yellow-400'
                      : 'bg-red-500'
                  ]"
                  :style="{ width: ((semester.completion_percentage ?? 0) + '%') }"
                ></div>
              </div>
              <span
                class="min-w-[2.5rem] text-xs text-right font-semibold"
                :class="semester.completion_percentage >= 80
                  ? 'text-green-600'
                  : semester.completion_percentage >= 50
                  ? 'text-yellow-600'
                  : 'text-red-600'"
              >{{ semester.completion_percentage ?? 0 }} %</span>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
</template>
