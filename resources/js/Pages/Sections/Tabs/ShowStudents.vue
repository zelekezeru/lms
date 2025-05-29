<script setup>
import { defineProps, computed } from "vue";
import { Link } from "@inertiajs/vue3";
import "sweetalert2/dist/sweetalert2.min.css";
import { CogIcon } from "@heroicons/vue/24/solid";
import { EyeIcon } from "@heroicons/vue/24/outline";

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
            <!-- Export & Import Students List in one row -->
            <div
                class="flex flex-col md:flex-row md:items-center mb-4 md:space-x-12 space-y-4 md:space-y-0"
            >
                <div class="relative">
                    <!-- Export list to Excel -->
                    <a
                        :href="route('sectionStudents.export', section.id)"
                        class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded block text-center"
                    >
                        Export Students
                    </a>
                </div>

                <div
                    class="relative bg-white dark:bg-gray-800 rounded-lg shadow p-4 flex flex-col md:flex-row items-stretch md:items-center space-y-3 md:space-y-0 md:space-x-4 border border-gray-200 dark:border-gray-700 w-full md:w-auto"
                >
                    <form
                        action="{{ route('students.import') }}"
                        method="POST"
                        enctype="multipart/form-data"
                        class="flex flex-col md:flex-row items-stretch md:items-center space-y-3 md:space-y-0 md:space-x-3 w-full"
                    >
                        

                        <input
                            type="hidden"
                            name="section_id"
                            value="{{ $section->id ?? '' }}"
                        />

                        <input
                            type="file"
                            name="file"
                            required
                            class="your-input-classes"
                        />

                        <button type="submit" class="your-button-classes">
                            Import Students
                        </button>
                    </form>
                </div>
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
                                Track
                            </th>
                            <th
                                class="w-40 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200"
                            >
                                Status
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(student, index) in [
                                ...section.students,
                            ].sort((a, b) =>
                                a.firstName.localeCompare(b.firstName)
                            )"
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
                                    {{ student.firstName }}
                                    {{ student.middleName }}
                                    {{ student.lastName }}
                                </Link>
                            </td>
                            <td
                                class="w-40 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                            >
                                {{ student.idNo }}
                            </td>
                            <td
                                class="w-60 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                            >
                                {{ section.track.name }}
                            </td>

                            <!-- Course Assessments -->
                            <td
                                class="w-40 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                            >
                                <Link
                                    :href="
                                        route('students.show', {
                                            student: student.id,
                                        })
                                    "
                                    class="text-blue-500 hover:text-blue-700"
                                >
                                    <EyeIcon class="w-5 h-5" />
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
