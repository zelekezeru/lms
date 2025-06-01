<script setup>
import RegistrarLayout from '@/Layouts/RegistrarLayout.vue'
import { usePage } from '@inertiajs/vue3'
import { AcademicCapIcon, UserGroupIcon, ClipboardDocumentCheckIcon, Cog6ToothIcon } from '@heroicons/vue/24/outline'

const user = usePage().props.auth.user
</script>

<template>
  <RegistrarLayout>
    <h1 class="text-2xl font-bold mb-4 text-gray-800 dark:text-white">Welcome, {{ user.name }}</h1>

    <!-- Metrics Section -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
      <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl shadow flex items-center gap-4">
        <UserGroupIcon class="w-10 h-10 text-blue-600" />
        <div>
          <p class="text-sm text-gray-500 dark:text-gray-400">Total Students</p>
          <h2 class="text-lg font-semibold text-gray-800 dark:text-white">1,542</h2>
        </div>
      </div>
      <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl shadow flex items-center gap-4">
        <AcademicCapIcon class="w-10 h-10 text-green-600" />
        <div>
          <p class="text-sm text-gray-500 dark:text-gray-400">Total Courses</p>
          <h2 class="text-lg font-semibold text-gray-800 dark:text-white">82</h2>
        </div>
      </div>
      <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl shadow flex items-center gap-4">
        <ClipboardDocumentCheckIcon class="w-10 h-10 text-purple-600" />
        <div>
          <p class="text-sm text-gray-500 dark:text-gray-400">Grades Submitted</p>
          <h2 class="text-lg font-semibold text-gray-800 dark:text-white">6,320</h2>
        </div>
      </div>
      <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl shadow flex items-center gap-4">
        <Cog6ToothIcon class="w-10 h-10 text-orange-600" />
        <div>
          <p class="text-sm text-gray-500 dark:text-gray-400">Settings Updates</p>
          <h2 class="text-lg font-semibold text-gray-800 dark:text-white">5</h2>
        </div>
      </div>
    </div>

    <!-- Recent Actions -->
    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow">
      <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-white">Recent Activity</h3>
      <ul class="space-y-3 text-sm text-gray-700 dark:text-gray-300">
        <li class="flex justify-between">
          <span>Added new student: John Doe</span>
          <span class="text-xs text-gray-400">2 hours ago</span>
        </li>
        <li class="flex justify-between">
          <span>Grade submitted for CSC101</span>
          <span class="text-xs text-gray-400">4 hours ago</span>
        </li>
        <li class="flex justify-between">
          <span>Updated profile settings</span>
          <span class="text-xs text-gray-400">1 day ago</span>
        </li>
      </ul>
    </div>
  </RegistrarLayout>
</template>
