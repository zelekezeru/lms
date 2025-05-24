<script setup>
import InstructorLayout from '@/Layouts/InstructorLayout.vue';

const props = defineProps({
  course: Object,
  attendance: Array,
});
</script>

<template>
  <InstructorLayout>
    <div class="max-w-7xl mx-auto py-10 px-4">
      <h1 class="text-2xl font-bold mb-6">Attendance for {{ course.name }} ({{ course.code }})</h1>
      <table class="min-w-full bg-white dark:bg-gray-800 rounded-lg overflow-hidden">
        <thead>
          <tr>
            <th class="px-4 py-2">Date</th>
            <th class="px-4 py-2">Present</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="record in attendance" :key="record.date" class="border-t">
            <td class="px-4 py-2">{{ record.date }}</td>
            <td class="px-4 py-2">{{ record.present ? '✔️' : '❌' }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </InstructorLayout>
</template>

