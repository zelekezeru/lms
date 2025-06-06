<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import {
    AcademicCapIcon,
    ClipboardIcon,
    CalendarDaysIcon,
    UserIcon,
} from "@heroicons/vue/24/outline";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    section: {
        type: Object,
        required: true,
    },
    instructor: {
        type: Object,
        required: false,
    },
});
</script>

<template>
    <AppLayout>
        <div class="max-w-7xl mx-auto py-10 px-4">
            <!-- Section Header -->
            <h1
                class="text-3xl font-bold mb-8 flex items-center text-gray-900 dark:text-white"
            >
                <AcademicCapIcon class="h-6 w-6 mr-2 text-blue-500" />
                {{ section.name }} ({{ section.code }})
            </h1>

            <!-- Section Info -->
            <div>
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 space-y-4 mb-8"
                >
                    <p><strong>Program:</strong> {{ section.program?.name }}</p>
                    <p><strong>Track:</strong> {{ section.track?.name }}</p>
                    <p>
                        <strong>Language:</strong>
                        {{ section.program?.language }}
                    </p>
                    <p>
                        <strong>Description:</strong>
                        {{ section.description || "N/A" }}
                    </p>
                </div>

                <!-- Courses Grid -->
                <div
                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6"
                >
                    <div
                        v-for="course in section.courses"
                        :key="course.id"
                        class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 rounded-2xl shadow-lg hover:shadow-2xl transition-transform transform hover:-translate-y-1 p-6 space-y-5"
                    >
                        <!-- Header -->
                        <div class="flex items-center justify-between">
                            <div>
                                <h2
                                    class="text-xl font-bold text-gray-900 dark:text-white"
                                >
                                    {{ course.name }}
                                </h2>
                                <p
                                    class="text-sm text-gray-500 dark:text-gray-400"
                                >
                                    Code: {{ course.code }}
                                </p>
                            </div>
                            <AcademicCapIcon class="w-7 h-7 text-blue-600" />
                        </div>

                        <!-- Instructor Info -->
                        <div
                            class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300"
                        >
                            <UserIcon class="w-5 h-5" />
                            <span
                                >Instructor:
                                {{ course.instructor?.name || "—" }}</span
                            >
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-col gap-2">
                            <Link
                                :href="
                                    route('instructor.sections.courses', {
                                        section: section.id,
                                        course: course.id,
                                        tab: 'students',
                                    })
                                "
                                class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition"
                            >
                                <ClipboardIcon class="w-5 h-5 mr-2" /> Students
                            </Link>

                            <Link
                                :href="
                                    route('instructor.sections.courses', {
                                        section: section.id,
                                        course: course.id,
                                        tab: 'assessments',
                                    })
                                "
                                class="inline-flex items-center justify-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition"
                            >
                                <ClipboardIcon class="w-5 h-5 mr-2" />
                                Assessments
                            </Link>

                            <Link
                                :href="
                                    route('instructor.sections.courses', {
                                        section: section.id,
                                        course: course.id,
                                        tab: 'attendance',
                                    })
                                "
                                class="inline-flex items-center justify-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition"
                            >
                                <CalendarDaysIcon class="w-5 h-5 mr-2" />
                                Attendance
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
