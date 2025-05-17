<script setup>
import { ref, computed } from "vue";
import { router, Head, useForm } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";

// Props
const props = defineProps({
    student: {
        type: Object,
        required: true,
    },
    user: {
        type: Object,
        required: true,
    },
    semesters: {
        type: Array,
        required: true,
    },
});


// Use grades instead of results
const transcript = props.student?.grades ?? []
const program = props.student?.program
const department = props.student?.track

// Group grades by year and semester
const groupedTranscript = computed(() => {
  const grouped = {}

  transcript.forEach((record) => {
    const yearName = record.year?.name ?? 'Unknown Year'
    const semesterName = record.semester?.name ?? 'Unknown Semester'
    const key = `${yearName} - ${semesterName}`

    if (!grouped[key]) grouped[key] = []
    grouped[key].push(record)
  })

  return grouped
})

// GPA calculation
function calculateGPA(grades) {
  let totalPoints = 0
  let totalCredits = 0

  grades.forEach((g) => {
    const gradePoint = parseFloat(g.grade_point || 0)
    const credit = parseFloat(g.course?.credit || 0)

    totalPoints += gradePoint * credit
    totalCredits += credit
  })

  return totalCredits > 0 ? (totalPoints / totalCredits).toFixed(2) : '0.00'
}
</script>


<template>
    <Head title="Student Transcript" />
  <AppLayout>
    
    <div class="overflow-x-auto px-4 py-6">
      <div class="max-w-4xl mx-auto bg-white dark:bg-gray-900 rounded-lg shadow p-6">
        <h1 class="text-2xl font-bold mb-2">Transcript</h1>
        
        <div class="mb-4 text-sm text-gray-600 dark:text-gray-300">
            <p><strong>Student:</strong> {{ student.firstName }}  {{ student.middleName }}  {{ student.lastName }} </p>
            <p><strong>ID:</strong> {{ student?.idNo }}</p>
            <p><strong>Program:</strong> {{ student.program?.name }}</p>
            <p><strong>Track:</strong> {{ student.track?.name }}</p>
            <p><strong>Class of:</strong> {{ student.year?.name }}</p>
            <p><strong>Semester:</strong> {{ student.semester?.name }}</p>
            <p><strong>Study Mode:</strong> {{ student.studyMode?.name }}</p>
        </div>

        <div
          v-for="(grades, key) in groupedTranscript"
          :key="key"
          class="mb-8"
        >
          <h2 class="text-lg font-semibold mb-2 text-green-700 dark:text-green-400">
            {{ key }}
          </h2>

          <table class="min-w-full border rounded shadow bg-white dark:bg-gray-800">
            <thead class="bg-gray-100 dark:bg-gray-700 text-sm font-semibold text-gray-800 dark:text-gray-200">
              <tr>
                <th class="px-4 py-2 text-left">Course</th>
                <th class="px-4 py-2 text-left">Credit</th>
                <th class="px-4 py-2 text-left">Grade Point</th>
                <th class="px-4 py-2 text-left">Grade Letter</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="(record, i) in grades"
                :key="record.id"
                :class="i % 2 === 0 ? 'bg-white' : 'bg-gray-50 dark:bg-gray-700'"
                class="text-sm text-gray-900 dark:text-gray-100 border-b"
              >
                <td class="px-4 py-2">{{ record.course?.title }}</td>
                <td class="px-4 py-2">{{ record.course?.credit }}</td>
                <td class="px-4 py-2">{{ record.grade_point }}</td>
                <td class="px-4 py-2">{{ record.grade_letter }}</td>
              </tr>
            </tbody>
            <tfoot>
              <tr class="bg-gray-50 dark:bg-gray-800 text-sm font-semibold">
                <td class="px-4 py-2 text-right" colspan="3">Semester GPA</td>
                <td class="px-4 py-2 text-green-700 dark:text-green-400">
                  {{ calculateGPA(grades) }}
                </td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
