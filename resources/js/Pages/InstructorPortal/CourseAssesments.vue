<script setup>
import InstructorLayout from '@/Layouts/InstructorLayout.vue';

const props = defineProps({
  course: Object,
  assessments: Array,
});
</script>

<template>
  <InstructorLayout>
    <div class="max-w-7xl mx-auto py-10 px-4">
      <h1 class="text-2xl font-bold mb-6">Assessments for {{ course.name }} ({{ course.code }})</h1>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="a in assessments" :key="a.id" class="bg-white dark:bg-gray-800 rounded-xl p-6">
          <h3 class="text-lg font-semibold mb-2">{{ a.title }}</h3>
          <p><strong>Date:</strong> {{ a.date }}</p>
          <p><strong>Due:</strong> {{ a.due_date }}</p>
          <p><strong>Mark:</strong> {{ a.mark }} / 100</p>
        </div>
      </div>
    </div>
  </InstructorLayout>
</template>

