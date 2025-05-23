<script setup>
import InstructorLayout from "@/Layouts/InstructorLayout.vue";
import {
    AcademicCapIcon,
    ClockIcon,
    UserGroupIcon,
} from "@heroicons/vue/24/outline";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    courses: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <InstructorLayout>
        <div class="mb-8">
            <h1
                class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-white"
            >
                My Courses
            </h1>
            <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">
                Courses you're teaching and With Sections
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div
                v-for="course in courses"
                :key="course.id"
                class="p-5 bg-white dark:bg-gray-800 rounded-xl shadow hover:shadow-lg transition"
            >
                <div class="flex items-center gap-3 mb-2">
                    <AcademicCapIcon class="w-6 h-6 text-indigo-500" />
                    <div>
                        <h2
                            class="text-lg font-semibold text-gray-800 dark:text-white"
                        >
                            {{ course.name }}
                        </h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ course.code }}
                        </p>
                    </div>
                </div>

                <div class="mt-3 space-y-2">
                    <div
                        class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300"
                    >
                        <UserGroupIcon class="w-4 h-4 text-emerald-500" />
                        {{ course.enrolled ?? "N/A" }} students enrolled
                    </div>
                    <div
                        class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300"
                    >
                        <ClockIcon class="w-4 h-4 text-amber-500" />
                        {{ course.schedule ?? "N/A" }}
                    </div>
                </div>

                <div class="mt-4 flex justify-between">
                    <Link
                        :href="route('instructor.courses.detail', {course: course.id})"
                        class="text-sm px-4 py-1 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition"
                    >
                        View
                    </Link>
                    <button
                        class="text-sm px-4 py-1 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-white rounded hover:bg-gray-300 dark:hover:bg-gray-600 transition"
                    >
                        Manage
                    </button>
                </div>

                <div class="mt-5 border-t pt-4">
                    <h3
                        class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                    >
                        Sections
                    </h3>

                    <div v-if="course.sections && course.sections.length">
                        <!-- Scrollable container for sections -->
                        <div
                            class="space-y-3 overflow-y-auto max-h-[360px] pr-1"
                        >
                            <div
                                v-for="section in course.sections"
                                :key="section.id"
                                class="px-3 py-2 rounded-md bg-gray-50 dark:bg-gray-700 text-sm"
                            >
                                <div class="flex justify-between items-center">
                                    <div>
                                        <div
                                            class="font-semibold text-gray-800 dark:text-white"
                                        >
                                            {{ section.name }}
                                        </div>
                                        <div
                                            class="text-gray-500 dark:text-gray-300"
                                        >
                                            <span class="font-bold">Program</span>: {{ section.program.name }}
                                        </div>
                                        <div
                                            class="text-gray-500 dark:text-gray-300"
                                        >
                                            <span class="font-bold">Track</span>: {{ section.track.name }}
                                        </div>
                                        <div
                                            class="text-gray-500 dark:text-gray-300"
                                        >
                                            Study Mode:
                                            {{
                                                section.studyMode
                                                    ? section.studyMode.name
                                                    : "N/A"
                                            }}
                                        </div>
                                    </div>
                                    <button
                                        class="ml-3 px-3 py-1 bg-blue-500 text-white text-xs rounded hover:bg-blue-600 transition"
                                    >
                                        Manage
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-else class="text-gray-500 text-sm italic">
                        No sections assigned
                    </div>
                </div>
            </div>
        </div>
    </InstructorLayout>
</template>
