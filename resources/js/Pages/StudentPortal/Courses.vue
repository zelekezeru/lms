<script setup>
import StudentLayout from "@/Layouts/StudentLayout.vue";
import { Link } from "@inertiajs/vue3";
import {
    AcademicCapIcon,
    ClockIcon,
    CheckCircleIcon,
    BookOpenIcon,
    CalendarIcon,
} from "@heroicons/vue/24/solid";
import Instructor from "../Instructor.vue";

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
</script>

<template>
    <StudentLayout>
        <div class="max-w-7xl mx-auto py-10 px-4 space-y-10">
            <!-- Header & Year Dropdown -->
            <div class="flex justify-between items-center mt-2">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                    My Courses
                </h1>
                <select
                    class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-sm rounded-lg p-2 focus:ring-blue-500 focus:border-blue-500 dark:text-white"
                >
                    <option>2024/2025</option>
                    <option>2023/2024</option>
                    <option>2022/2023</option>
                </select>
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
                        v-for="curriculum in student.activeCurricula"
                        class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg shadow"
                    >
                        <Link
                            :href="
                                route(
                                    'student.courses.show',
                                    curriculum.course.id
                                )
                            "
                        >
                            <h3
                                class="text-lg font-bold text-gray-900 dark:text-white flex items-center"
                            >
                                <AcademicCapIcon
                                    class="h-6 w-6 text-blue-500 mr-2"
                                />
                                {{ curriculum.course.name }} ({{
                                    curriculum.course.code
                                }})
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Instructor: {{ curriculum.course.instructor ? curriculum.course.instructor.name : 'N/A' }}
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Credits: {{ curriculum.course.creditHours }}
                            </p>

                        </Link>
                    </div>
                </div>
            </div>

            <!-- Finished Courses -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 mt-2">
                <div class="flex items-center gap-3 mb-6 text-green-600">
                    <CheckCircleIcon class="w-6 h-6" />
                    <h2
                        class="text-xl font-semibold dark:text-white text-gray-900"
                    >
                        Finished Courses
                    </h2>
                </div>
                <div
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4"
                >
                    <div
                        class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg shadow"
                    >
                        <h3
                            class="text-lg font-bold text-gray-900 dark:text-white"
                        >
                            Computer Fundamentals
                        </h3>
                        <div class="flex justify-between mt-2 text-sm">
                            <span class="text-gray-600 dark:text-gray-400"
                                >Completed: Jan 2025</span
                            >
                            <span
                                class="text-gray-800 dark:text-white font-medium"
                                >Grade: A</span
                            >
                        </div>
                    </div>
                    <div
                        class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg shadow"
                    >
                        <h3
                            class="text-lg font-bold text-gray-900 dark:text-white"
                        >
                            Mathematics for CS
                        </h3>
                        <div class="flex justify-between mt-2 text-sm">
                            <span class="text-gray-600 dark:text-gray-400"
                                >Completed: Feb 2025</span
                            >
                            <span
                                class="text-gray-800 dark:text-white font-medium"
                                >Grade: B+</span
                            >
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </StudentLayout>
</template>

