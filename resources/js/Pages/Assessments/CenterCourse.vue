<script setup>
import { defineProps, ref, computed } from "vue";
import "sweetalert2/dist/sweetalert2.min.css";
import {
    CogIcon,
    DocumentTextIcon,
    PresentationChartBarIcon,
    CheckBadgeIcon,
} from "@heroicons/vue/24/solid";
import ShowDetails from "./Tabs/ShowDetails.vue";
import ShowResults from "../InstructorPortal/Components/ShowResults.vue";
import ShowWeights from "../InstructorPortal/Components/ShowWeights.vue";
import ShowGrades from "../InstructorPortal/Components/ShowGrades.vue";
import AppLayout from "@/Layouts/AppLayout.vue"; // default
import { usePage } from "@inertiajs/vue3";


const props = defineProps({
    course: Object,
    center: Object,
    grades: Array,
    students: Object,
  
});
</script>

<template>
    <AppLayout>
        <div class="mb-8">
            <div class="overflow-x-auto px-4 py-6">
                <h2 class="text-xl font-bold mb-4 text-gray-900 dark:text-gray-100">Submitted Grades</h2>
                <table class="min-w-full border rounded shadow bg-white dark:bg-gray-900">
                    <thead class="bg-gray-100 dark:bg-gray-800 text-sm">
                        <tr>
                            <th class="px-4 py-2 text-gray-700 dark:text-gray-200">#</th>
                            <th class="px-4 py-2 text-left text-gray-700 dark:text-gray-200">Student</th>
                            <th class="px-4 py-2 text-gray-700 dark:text-gray-200">Total</th>
                            <th class="px-4 py-2 text-gray-700 dark:text-gray-200">Grade</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(student, index) in props.students"
                            :key="student.id"
                            :class="index % 2 === 0 ? 'bg-white dark:bg-gray-900' : 'bg-gray-50 dark:bg-gray-800'"
                            class="text-sm"
                        >
                            <td class="px-4 py-2 text-gray-700 dark:text-gray-200">{{ index + 1 }}</td>
                            <td class="px-4 py-2 text-gray-700 dark:text-gray-200">
                                <a
                                    :href="route('students.show', student.id)"
                                    class="text-blue-600 dark:text-blue-400 hover:underline"
                                >
                                    {{ student?.first_name }} {{ student?.middle_name }} {{ student?.last_name }}
                                </a>
                            </td>
                            <td class="px-4 py-2 text-gray-700 dark:text-gray-200">
                                {{
                                    student.grades && student.grades.length > 0
                                        ? student.grades[0].grade_point
                                        : "N/A"
                                }}
                            </td>
                            <td class="px-4 py-2 text-gray-700 dark:text-gray-200">
                                {{
                                    student.grades && student.grades.length > 0
                                        ? student.grades[0].grade_letter
                                        : "N/A"
                                }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>