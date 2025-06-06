<script setup>
import InstructorLayout from '@/Layouts/AppLayout.vue'
import { ref } from 'vue'
import { CalendarIcon, ClockIcon } from '@heroicons/vue/24/solid'

const selectedCourse = ref('')
const schedule = ref([
  { course: 'CS101', date: '2025-04-26', time: '09:00 AM' },
  { course: 'CS101', date: '2025-04-27', time: '02:00 PM' },
  { course: 'Math201', date: '2025-04-28', time: '11:00 AM' },
  { course: 'Math201', date: '2025-04-29', time: '01:00 PM' },
])
const courses = ref([
  { id: 1, name: 'CS101' },
  { id: 2, name: 'Math201' },
])

const addSession = () => {
  if (selectedCourse.value && newSession.value.date && newSession.value.time) {
    schedule.value.push({ 
      course: selectedCourse.value.name,
      date: newSession.value.date,
      time: newSession.value.time,
    })
    alert('Session added successfully!')
  } else {
    alert('Please complete all fields.')
  }
}

const newSession = ref({
  date: '',
  time: '',
})

const selectCourse = (course) => {
  selectedCourse.value = course
}
</script>

<template>
  <InstructorLayout>
    <div class="p-6 space-y-8">
      <!-- Page Title -->
      <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Manage Schedule</h1>

      <!-- Course Selection -->
      <div class="mt-6">
        <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">Select Course</label>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <div 
            v-for="course in courses" 
            :key="course.id" 
            class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow cursor-pointer hover:bg-indigo-600 hover:text-white transition duration-200"
            @click="selectCourse(course)"
            :class="{'bg-indigo-600 text-white': selectedCourse.value?.id === course.id}"
          >
            <div class="flex items-center space-x-3">
              <CalendarIcon class="h-6 w-6 text-indigo-600 dark:text-indigo-300" />
              <span class="font-semibold">{{ course.name }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Add Session Form -->
      <div v-if="selectedCourse" class="bg-white dark:bg-gray-900 rounded-xl shadow p-6 mt-8">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Add New Session</h2>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
          <!-- Date Input -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Session Date</label>
            <input
              v-model="newSession.date"
              type="date"
              class="w-full p-2 rounded-lg border dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-800 dark:text-white"
            />
          </div>

          <!-- Time Input -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Session Time</label>
            <input
              v-model="newSession.time"
              type="time"
              class="w-full p-2 rounded-lg border dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-800 dark:text-white"
            />
          </div>
        </div>

        <!-- Add Session Button -->
        <div class="mt-6 text-right">
          <button
            @click="addSession"
            class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg text-sm"
          >
            Add Session
          </button>
        </div>
      </div>

      <!-- Schedule Table -->
      <div v-if="schedule.length > 0" class="mt-8 bg-white dark:bg-gray-900 rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Course Schedule</h2>
        <div class="overflow-x-auto">
          <table class="min-w-full text-sm border dark:border-gray-700">
            <thead class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
              <tr>
                <th class="text-left px-4 py-2">#</th>
                <th class="text-left px-4 py-2">Course</th>
                <th class="text-left px-4 py-2">Date</th>
                <th class="text-left px-4 py-2">Time</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(session, index) in schedule" :key="index" class="border-t dark:border-gray-700">
                <td class="px-4 py-2">{{ index + 1 }}</td>
                <td class="px-4 py-2">{{ session.course }}</td>
                <td class="px-4 py-2">{{ session.date }}</td>
                <td class="px-4 py-2">{{ session.time }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- No Schedule Message -->
      <div v-else class="mt-6 text-gray-500 dark:text-gray-400">
        <p>No sessions scheduled yet. Add a session above.</p>
      </div>
    </div>
  </InstructorLayout>
</template>
