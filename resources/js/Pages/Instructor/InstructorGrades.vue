<script setup>
import InstructorLayout from '@/Layouts/InstructorLayout.vue'
import { ref } from 'vue'
import { AcademicCapIcon } from '@heroicons/vue/24/solid'

const selectedCourse = ref(null)
const selectedBatch = ref('')
const grades = ref({})

const courses = ref([
  { id: 1, name: 'CS101' },
  { id: 2, name: 'Math201' },
])

const batches = ref([
  { id: 1, name: 'CS101 - A' },
  { id: 2, name: 'Math201 - B' },
])

const students = ref([
  { id: 1, name: 'Liya Alemu', student_id: 'STU001' },
  { id: 2, name: 'Binyam Tesfaye', student_id: 'STU002' },
  { id: 3, name: 'Sara Tadesse', student_id: 'STU003' },
])

const submitGrades = () => {
  console.log('Grades submitted:', grades.value)
  alert('Grades submitted successfully!')
}
</script>

<template>
  <InstructorLayout>
    <div class="p-6 space-y-8">
      <!-- Page Title -->
      <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Enter Grades</h1>

      <!-- Course Selection Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="course in courses"
          :key="course.id"
          @click="selectedCourse = course; selectedBatch = ''"
          class="cursor-pointer border rounded-xl p-4 bg-white dark:bg-gray-800 shadow hover:shadow-md transition"
          :class="{
            'ring-2 ring-indigo-500': selectedCourse?.id === course.id
          }"
        >
          <div class="flex items-center space-x-4">
            <AcademicCapIcon class="h-6 w-6 text-indigo-600" />
            <div>
              <h2 class="text-lg font-semibold text-gray-800 dark:text-white">{{ course.name }}</h2>
              <p class="text-sm text-gray-500 dark:text-gray-300">Click to select</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Batch Selection -->
      <div v-if="selectedCourse" class="mt-6">
        <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">Select Batch</label>
        <select
          v-model="selectedBatch"
          class="w-full p-2 rounded-lg border dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-800 dark:text-white"
        >
          <option disabled value="">Choose a batch</option>
          <option v-for="batch in batches.filter(b => b.name.startsWith(selectedCourse.name))" :key="batch.id" :value="batch">
            {{ batch.name }}
          </option>
        </select>
      </div>

      <!-- Grade Table -->
      <div v-if="selectedBatch" class="bg-white dark:bg-gray-900 rounded-xl shadow p-4">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">
          Enter Grades for {{ selectedBatch.name }}
        </h2>
        <div class="overflow-x-auto">
          <table class="min-w-full text-sm border dark:border-gray-700">
            <thead class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
              <tr>
                <th class="text-left px-4 py-2">#</th>
                <th class="text-left px-4 py-2">Student Name</th>
                <th class="text-left px-4 py-2">Student ID</th>
                <th class="text-left px-4 py-2">Grade</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="(student, index) in students"
                :key="student.id"
                class="border-t dark:border-gray-700"
              >
                <td class="px-4 py-2">{{ index + 1 }}</td>
                <td class="px-4 py-2">{{ student.name }}</td>
                <td class="px-4 py-2">{{ student.student_id }}</td>
                <td class="px-4 py-2">
                  <input
                    v-model="grades[student.id]"
                    type="text"
                    placeholder="e.g. A+"
                    class="w-24 p-1 rounded border dark:border-gray-600 bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-white"
                  />
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Submit Button -->
        <div class="mt-6 text-right">
          <button
            @click="submitGrades"
            class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg text-sm"
          >
            Submit Grades
          </button>
        </div>
      </div>
    </div>
  </InstructorLayout>
</template>
