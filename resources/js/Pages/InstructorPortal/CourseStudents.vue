<script setup>
import InstructorLayout from '@/Layouts/InstructorLayout.vue';

const props = defineProps({
  course: Object,
  section: Object,
  students: Array,
});

</script>

<template>
  <InstructorLayout>
    <div class="max-w-7xl mx-auto py-10 px-4">
      <h1 class="text-2xl font-bold mb-6">Students for {{ course.name }} ({{ course.code }})</h1>
      <table class="min-w-full text-left bg-white dark:bg-gray-800 rounded-lg overflow-hidden">
        <thead>
          <tr>
            <th class="px-4 py-2">ID</th>
            <th class="px-4 py-2">Name</th>
            <th class="px-4 py-2">Email</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="student in students" :key="student.id" class="border-t">
            <td class="px-4 py-2">{{ student.id }}</td>
            <td class="px-4 py-2">{{ student.firstName }} {{ student.lastName }}</td>
            <td class="px-4 py-2">{{ student.user.email }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </InstructorLayout>
</template>

