<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import { ArrowPathIcon, EyeIcon } from '@heroicons/vue/24/outline'

// Example data, replace with your actual logic

const props = defineProps({
    sections: Object,
    search: String,
    gradesPercentage: Object,
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
            Program
          </th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
            Number of Students
          </th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
            Grade Submission ( % )
          </th>
        </tr>
      </thead>
      <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
        <tr v-for="section, index in props.sections.data" :key="section.id">
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ index + 1 }}</td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ section.code }}</td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
            <Link :href="route('sections.show', { section: section.id })" class="text-blue-500 hover:text-blue-700">
              {{ section.name }}
            </Link>
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ section.program.name }}</td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ section.students.length }}</td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">

            <div v-for="gradesPercentage in props.gradesPercentage" :key="gradesPercentage.section_id">
              <span v-if="gradesPercentage.section_id === section.id" class="text-xs text-gray-500 dark:text-gray-400">
                
                <div class="flex items-center space-x-2">
                  <div class="w-32 bg-gray-200 dark:bg-gray-700 rounded h-2">
                    <div
                      class="h-2 rounded transition-all duration-700"
                      :class="{
                        'bg-green-500': gradesPercentage.percentage_submitted >= 80,
                        'bg-yellow-400': gradesPercentage.percentage_submitted >= 50 && gradesPercentage.percentage_submitted < 80,
                        'bg-red-500': gradesPercentage.percentage_submitted < 50
                      }"
                      :style="{ width: gradesPercentage.percentage_submitted + '%' }"
                    ></div>
                  </div>
                  <span
                    :class="{
                      'text-green-600 dark:text-green-400': gradesPercentage.percentage_submitted >= 80,
                      'text-yellow-600 dark:text-yellow-400': gradesPercentage.percentage_submitted >= 50 && gradesPercentage.percentage_submitted < 80,
                      'text-red-600 dark:text-red-400': gradesPercentage.percentage_submitted < 50
                    }"
                    class="text-xs"
                  >
                    {{ gradesPercentage.percentage_submitted }} %
                  </span>
                </div>
              </span>
            </div>            
          </td>
        </tr>
      </tbody>
    </table>
</template>