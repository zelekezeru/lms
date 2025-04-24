<script setup>
import { defineProps } from "vue";
import { Link } from "@inertiajs/vue3";
import "sweetalert2/dist/sweetalert2.min.css";
import { CogIcon} from "@heroicons/vue/24/solid";

const props = defineProps({
    section: {
        type: Object,
        required: true,
    },
    courses: {
        type: Object,
        required: false,
    },
    instructors: {
        type: Object,
        required: false,
    },
});
</script>

<template>
    <div class="">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Students
            </h2>
            <button
                class="flex items-center text-indigo-600 hover:text-indigo-800"
            >
                Add Student
            </button>
        </div>

        <div class="overflow-x-auto">
            <div
                class="mt-8 border-t border-b border-gray-300 dark:border-gray-600 pt-4 pb-4"
            >
                <div class="flex items-center justify-between mb-4">
                    <h2
                        class="text-xl font-semibold text-gray-900 dark:text-gray-100"
                    >
                        Students
                    </h2>
                </div>
                <!-- Section Students list -->
                <div class="overflow-x-auto">
                    <table
                        class="min-w-full table-auto border border-gray-300 dark:border-gray-600"
                    >
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-700">
                                <th
                                    class="w-10 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600"
                                >
                                    No.
                                </th>
                                <th
                                    class="w-80 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600"
                                >
                                    Name
                                </th>
                                <th
                                    class="w-40 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600"
                                >
                                    ID Number
                                </th>
                                <th
                                    class="w-60 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600"
                                >
                                    Department
                                </th>
                                <th
                                    class="w-40 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200"
                                >
                                    Assesment
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(student, index) in section.students"
                                :key="student.id"
                                :class="
                                    index % 2 === 0
                                        ? 'bg-white dark:bg-gray-800'
                                        : 'bg-gray-50 dark:bg-gray-700'
                                "
                                class="border-b border-gray-300 dark:border-gray-600"
                            >
                                <td
                                    class="w-10 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                    {{ index + 1 }}
                                </td>
                                <td
                                    class="w-80 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                    <Link
                                        :href="
                                            route('students.show', {
                                                student: student.id,
                                            })
                                        "
                                    >
                                        {{ student.first_name }}
                                        {{ student.middle_name }}
                                        {{ student.last_name }}
                                    </Link>
                                </td>
                                <td
                                    class="w-40 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                    {{ student.id_no }}
                                </td>
                                <td
                                    class="w-60 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                    {{ section.department.name }}
                                </td>

                                <!-- Course Assessments -->
                                <td
                                    class="w-40 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                    <Link
                                        :href="
                                            route(
                                                'assessments.section_student',
                                                {
                                                    student: student.id,
                                                    section: section.id,
                                                }
                                            )
                                        "
                                        class="text-green-500 hover:text-green-700"
                                    >
                                        <CogIcon class="w-5 h-5 inline-block" />
                                        <span class="inline-block"
                                            >Assessments</span
                                        >
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>
