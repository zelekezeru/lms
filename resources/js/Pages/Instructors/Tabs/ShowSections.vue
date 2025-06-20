<script setup>
import { defineProps, ref } from "vue";
import { Link, router, useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import { CogIcon } from "@heroicons/vue/24/solid";

// Define the props for the instructor
const props = defineProps({
    instructor: {
        type: Object,
        required: true,
    },
    courses: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <div>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Section Assignments
            </h2>

        </div>

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
                            class="w-60 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600"
                        >
                            Name
                        </th>
                        <th
                            class="w-40 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200"
                        >
                            Code
                        </th>
                        <th
                            class="w-40 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200"
                        >
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="(section, index) in instructor.sections"
                        :key="section.id"
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
                            class="w-60 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                        >
                            <Link
                                :href="
                                    route('sections.show', {
                                        section: section.id,
                                    })
                                "
                            >
                                {{ section.name }}
                            </Link>
                        </td>
                        <td
                            class="w-40 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                        >
                            {{ section.code }}
                        </td>

                        <!-- Section Assessments -->
                        <td
                            class="w-40 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                        >
                            <Link
                                :href="route('sections.show', { section: section.id })"
                                class="text-green-500 hover:text-green-700"
                            >
                                <CogIcon class="w-5 h-5 inline-block" />
                                <span class="inline-block">View</span>
                            </Link>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
