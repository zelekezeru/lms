<script setup>
import InstructorLayout from '@/Layouts/InstructorLayout.vue';
import { AcademicCapIcon, ClockIcon, UserGroupIcon } from '@heroicons/vue/24/outline';

const courses = [
  {
    id: 1,
    title: 'Introduction to Computer Science',
    code: 'CS101',
    enrolled: 90,
    schedule: 'Mon & Wed - 10:00 AM',
    room: 'Room 204',
    batches: [
      { id: 1, name: 'CS101 - A', students: 45, time: 'Mon - 10:00 AM' },
      { id: 2, name: 'CS101 - B', students: 45, time: 'Wed - 10:00 AM' },
    ],
  },
  {
    id: 2,
    title: 'Discrete Mathematics',
    code: 'MATH201',
    enrolled: 38,
    schedule: 'Tue & Thu - 1:30 PM',
    room: 'Room 105',
    batches: [
      { id: 1, name: 'MATH201 - A', students: 38, time: 'Tue & Thu - 1:30 PM' },
    ],
  },
  {
    id: 3,
    title: 'Database Systems',
    code: 'DB301',
    enrolled: 60,
    schedule: 'Fri - 2:00 PM',
    room: 'Lab 3',
    batches: [
      { id: 1, name: 'DB301 - A', students: 30, time: 'Fri - 2:00 PM' },
      { id: 2, name: 'DB301 - B', students: 30, time: 'Fri - 4:00 PM' },
    ],
  },
];
</script>

<template>
  <InstructorLayout>
    <div class="mb-8">
      <h1 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-white">My Courses</h1>
      <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">Courses you're teaching and their batches</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="course in courses" :key="course.id" class="p-5 bg-white dark:bg-gray-800 rounded-xl shadow hover:shadow-lg transition">
        <div class="flex items-center gap-3 mb-2">
          <AcademicCapIcon class="w-6 h-6 text-indigo-500" />
          <div>
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white">{{ course.title }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ course.code }}</p>
          </div>
        </div>

        <div class="mt-3 space-y-2">
          <div class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
            <UserGroupIcon class="w-4 h-4 text-emerald-500" />
            {{ course.enrolled }} students enrolled
          </div>
          <div class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
            <ClockIcon class="w-4 h-4 text-amber-500" />
            {{ course.schedule }}
          </div>
          <div class="text-sm text-gray-500 dark:text-gray-400">Room: {{ course.room }}</div>
        </div>

        <div class="mt-4 flex justify-between">
          <button class="text-sm px-4 py-1 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition">View</button>
          <button class="text-sm px-4 py-1 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-white rounded hover:bg-gray-300 dark:hover:bg-gray-600 transition">Manage</button>
        </div>

        <div class="mt-5 border-t pt-4">
          <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Batches</h3>
          <div v-if="course.batches && course.batches.length">
            <div v-for="batch in course.batches" :key="batch.id" class="mb-3 px-3 py-2 rounded-md bg-gray-50 dark:bg-gray-700 text-sm">
              <div class="flex justify-between items-center">
                <div>
                  <div class="font-semibold text-gray-800 dark:text-white">{{ batch.name }}</div>
                  <div class="text-gray-500 dark:text-gray-300">Students: {{ batch.students }}</div>
                  <div class="text-gray-400 dark:text-gray-400 text-xs">{{ batch.time }}</div>
                </div>
                <button class="ml-3 px-3 py-1 bg-blue-500 text-white text-xs rounded hover:bg-blue-600 transition">Manage</button>
              </div>
            </div>
          </div>
          <div v-else class="text-gray-500 text-sm italic">No batches assigned</div>
        </div>
      </div>
    </div>
  </InstructorLayout>
</template>
