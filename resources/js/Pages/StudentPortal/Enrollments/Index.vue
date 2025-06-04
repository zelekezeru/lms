<script setup>
import StudentLayout from "@/Layouts/StudentLayout.vue";
import { Link } from "@inertiajs/vue3";
import {
    AcademicCapIcon,
    CheckCircleIcon,
    BookOpenIcon,
} from "@heroicons/vue/24/solid";
import { XCircleIcon } from "@heroicons/vue/24/outline";

const props = defineProps({
    student: {
        type: Object,
        required: true,
    },
    instructor: {
        type: Object,
        required: true,
    },
});

const enrolledCourses = props.student.enrollments.filter(
    (enrollment) => enrollment.status == "Enrolled"
);
const completedCourses = props.student.enrollments.filter(
    (enrollment) => enrollment.status == "Completed"
);
const droppedCourses = props.student.enrollments.filter(
    (enrollment) => enrollment.status == "Dropped"
);
const failedCourses = props.student.enrollments.filter(
    (enrollment) => enrollment.status == "Failed"
);
</script>

<template>
    <StudentLayout>
        <div class="max-w-7xl mx-auto py-10 px-4 space-y-10">
            <!-- Header & Year Dropdown -->
            <div class="flex justify-between items-center mt-2">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                    My Courses
                </h1>
            </div>

            <!-- Active Courses -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 mt-2">
                <div class="flex items-center gap-3 mb-6 text-blue-600">
                    <BookOpenIcon class="w-6 h-6" />
                    <h2
                        class="text-xl font-semibold dark:text-white text-gray-900"
                    >
                        Active Courses
                    </h2>
                </div>
                <div
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4"
                >
                    <div
                        v-for="enrollment in enrolledCourses"
                        :key="enrollment.id"
                        class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg shadow"
                    >
                        <Link
                            :href="
                                route('student.enrollments.show', enrollment.id)
                            "
                        >
                            <h3
                                class="text-lg font-bold text-gray-900 dark:text-white flex items-center"
                            >
                                <AcademicCapIcon
                                    class="h-6 w-6 text-blue-500 mr-2"
                                />
                                {{ enrollment.course.name }} ({{
                                    enrollment.course.code
                                }})
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Instructor:
                                {{
                                    enrollment.instructor
                                        ? enrollment.instructor.name
                                        : "N/A"
                                }}
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Section:
                                {{
                                    enrollment.section
                                        ? enrollment.section.name
                                        : "N/A"
                                }}
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Credits: {{ enrollment.course.credit_hours }}
                            </p>
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Dropped Courses -->
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 mt-2"
                v-if="completedCourses.length > 0"
            >
                <div class="flex items-center gap-3 mb-6 text-green-600">
                    <CheckCircleIcon class="w-6 h-6" />
                    <h2
                        class="text-xl font-semibold dark:text-white text-gray-900"
                    >
                        Dropped Courses
                    </h2>
                </div>
                <div
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4"
                >
                    <div
                        class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg shadow"
                        v-for="enrollment in completedCourses"
                    >
                        <h3
                            class="text-lg font-bold text-gray-900 dark:text-white"
                        >
                            {{ enrollment.course.name }}
                        </h3>
                        <div class="flex justify-between mt-2 text-sm">
                            <span class="text-gray-600 dark:text-gray-400"
                                >Completed: Jan 2025
                            </span>
                            <span
                                class="text-gray-800 dark:text-white font-medium"
                                >Grade: A</span
                            >
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 mt-2"
                v-if="droppedCourses.length > 0"
            >
                <div class="flex items-center gap-3 mb-6 text-green-600">
                    <XCircleIcon class="w-6 h-6" />
                    <h2
                        class="text-xl font-semibold dark:text-white text-gray-900"
                    >
                        Dropped Courses
                    </h2>
                </div>
                <div
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4"
                >
                    <div
                        class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg shadow"
                        v-for="enrollment in droppedCourses"
                    >
                        <h3
                            class="text-lg font-bold text-gray-900 dark:text-white"
                        >
                            {{ enrollment.course.name }}
                        </h3>
                        <div class="flex justify-between mt-2 text-sm">
                            <span class="text-gray-600 dark:text-gray-400"
                                >Completed: Jan 2025
                            </span>
                            <span
                                class="text-gray-800 dark:text-white font-medium"
                                >Grade: A</span
                            >
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </StudentLayout>
</template>
