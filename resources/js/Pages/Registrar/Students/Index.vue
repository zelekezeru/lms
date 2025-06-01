<script setup>
import RegistrarLayout from '@/Layouts/RegistrarLayout.vue'
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import { MagnifyingGlassIcon, EyeIcon } from '@heroicons/vue/24/outline'

const search = ref('')
const students = [
  { id: 1, name: 'John Doe', email: 'john@example.com', status: 'Active', department: 'Computer Science' },
  { id: 2, name: 'Jane Smith', email: 'jane@example.com', status: 'Inactive', department: 'Business' },
  { id: 3, name: 'Ali Musa', email: 'ali@example.com', status: 'Active', department: 'Engineering' },
]
</script>

<template>
  <RegistrarLayout>
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Students</h1>
    </div>

    <!-- Search -->
    <div class="mb-4 flex items-center justify-between">
      <div class="relative w-full sm:w-1/2">
        <MagnifyingGlassIcon class="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" />
        <input
          v-model="search"
          type="text"
          placeholder="Search by name or email"
          class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-sm text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
      </div>
    </div>

    <!-- Student Table -->
    <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-lg shadow">
      <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
        <thead class="bg-gray-100 dark:bg-gray-700">
          <tr>
            <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">Name</th>
            <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">Email</th>
            <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">Department</th>
            <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">Status</th>
            <th class="px-4 py-3 text-right font-semibold text-gray-700 dark:text-gray-200">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
          <tr
            v-for="student in students.filter(s =>
              s.name.toLowerCase().includes(search.toLowerCase()) ||
              s.email.toLowerCase().includes(search.toLowerCase())
            )"
            :key="student.id"
          >
            <td class="px-4 py-3 text-gray-800 dark:text-gray-100 font-medium">{{ student.name }}</td>
            <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ student.email }}</td>
            <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ student.department }}</td>
            <td class="px-4 py-3">
              <span
                :class="student.status === 'Active'
                  ? 'bg-green-100 text-green-800 dark:bg-green-700 dark:text-white'
                  : 'bg-red-100 text-red-800 dark:bg-red-700 dark:text-white'"
                class="px-2 py-1 rounded-full text-xs font-semibold"
              >
                {{ student.status }}
              </span>
            </td>
            <td class="px-4 py-3 text-right">
              <Link
                :href="`/registrar/students/${student.id}`"
                class="inline-flex items-center text-blue-600 hover:underline dark:text-blue-400"
              >
                <EyeIcon class="w-4 h-4 mr-1" />
                View
              </Link>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </RegistrarLayout>
</template>
