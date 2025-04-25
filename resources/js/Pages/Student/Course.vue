<script setup>
import StudentLayout from "@/Layouts/StudentLayout.vue";
import { ref } from "vue";
import {
  AcademicCapIcon,
  ClockIcon,
  CheckCircleIcon,
  CalendarIcon,
} from "@heroicons/vue/24/solid";

const selectedYear = ref("2024/2025");
const availableYears = ["2024/2025", "2023/2024", "2022/2023"];

const courses = {
  active: [
    { title: "Web Development I", instructor: "Mr. Amanuel" },
    { title: "Database Systems", instructor: "Mrs. Eleni" },
  ],
  enrolled: [{ title: "Introduction to AI", startDate: "May 10, 2025" }],
  next: [{ title: "Mobile App Development", schedule: "July 2025" }],
  finished: [
    { title: "Computer Fundamentals", completed: "Jan 2025", grade: "A" },
    { title: "Mathematics for CS", completed: "Feb 2025", grade: "B+" },
  ],
};
</script>

<template>
  <StudentLayout>
    <div class="max-w-7xl mx-auto py-10 px-4 space-y-10">
      <!-- Header & Year Dropdown -->
      <div class="flex justify-between items-center mt-2">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">My Courses</h1>
        <select
          v-model="selectedYear"
          class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-sm rounded-lg p-2 focus:ring-blue-500 focus:border-blue-500 dark:text-white"
        >
          <option v-for="year in availableYears" :key="year" :value="year">{{ year }}</option>
        </select>
      </div>

      <!-- Section Card Component -->
      <div
        v-for="(section, label) in [
          { icon: AcademicCapIcon, title: 'Active Courses', key: 'active', color: 'text-blue-600' },
          { icon: ClockIcon, title: 'Enrolled Courses', key: 'enrolled', color: 'text-yellow-600' },
          { icon: CalendarIcon, title: 'Next Courses', key: 'next', color: 'text-purple-600' },
          { icon: CheckCircleIcon, title: 'Finished Courses', key: 'finished', color: 'text-green-600' },
        ]"
        :key="label"
        class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 mt-2"
      >
        <!-- Section Header -->
        <div class="flex items-center gap-3 mb-6" :class="section.color">
          <component :is="section.icon" class="w-6 h-6" />
          <h2 class="text-xl font-semibold dark:text-white text-gray-900">
            {{ section.title }}
          </h2>
        </div>

        <!-- Course Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div
            v-for="(course, i) in courses[section.key]"
            :key="section.key + '-' + i"
            class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg shadow hover:shadow-lg transition"
          >
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ course.title }}</h3>
            <p
              v-if="course.instructor"
              class="text-sm text-gray-600 dark:text-gray-400"
            >
              Instructor: {{ course.instructor }}
            </p>
            <p
              v-if="course.startDate"
              class="text-sm text-gray-600 dark:text-gray-400"
            >
              Starts: {{ course.startDate }}
            </p>
            <p
              v-if="course.schedule"
              class="text-sm text-gray-600 dark:text-gray-400"
            >
              Scheduled: {{ course.schedule }}
            </p>
            <div v-if="course.completed" class="flex justify-between mt-2 text-sm">
              <span class="text-gray-600 dark:text-gray-400">Completed: {{ course.completed }}</span>
              <span class="text-gray-800 dark:text-gray-100 font-medium">Grade: {{ course.grade }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </StudentLayout>
</template>
