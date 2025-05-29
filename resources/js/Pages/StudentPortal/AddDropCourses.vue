<script setup>
import StudentLayout from "@/Layouts/StudentLayout.vue";
import { ref } from "vue";
import {
    PlusIcon,
    TrashIcon,
    MagnifyingGlassIcon,
} from "@heroicons/vue/24/outline";

const searchQuery = ref("");
const availableCourses = [
    {
        id: 1,
        title: "Machine Learning",
        instructor: "Dr. Bekele",
        calendar: "Wed/Fri",
    },
    {
        id: 2,
        title: "Software Engineering",
        instructor: "Prof. Alemayehu",
        calendar: "Mon/Wed",
    },
    {
        id: 3,
        title: "Cyber Security",
        instructor: "Dr. Hanna",
        calendar: "Tue/Thu",
    },
];

const enrolledCourses = ref([
    {
        id: 10,
        title: "Database Systems",
        instructor: "Mrs. Eleni",
        calendar: "Mon/Wed",
    },
    {
        id: 11,
        title: "Computer Networks",
        instructor: "Mr. Daniel",
        calendar: "Tue/Thu",
    },
]);
</script>

<template>
    <StudentLayout>
        <div class="max-w-7xl mx-auto py-10 px-4 space-y-10">
            <div class="flex justify-between items-center">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                    Add & Drop Courses
                </h1>
            </div>

            <!-- Add Course Section -->
            <div
                class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow space-y-4 mt-2"
            >
                <div class="flex items-center gap-2">
                    <MagnifyingGlassIcon
                        class="w-5 h-5 text-gray-500 dark:text-gray-400"
                    />
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Search available courses..."
                        class="w-full px-3 py-2 border rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    />
                </div>

                <div
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-2"
                >
                    <div
                        v-for="course in availableCourses.filter((c) =>
                            c.title
                                .toLowerCase()
                                .includes(searchQuery.toLowerCase())
                        )"
                        :key="course.id"
                        class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg shadow flex flex-col justify-between"
                    >
                        <div>
                            <h3
                                class="text-lg font-bold text-gray-900 dark:text-white"
                            >
                                {{ course.title }}
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Instructor: {{ course.instructor }}
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Calendar: {{ course.calendar }}
                            </p>
                        </div>
                        <button
                            class="mt-4 inline-flex items-center gap-1 bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-md"
                        >
                            <PlusIcon class="w-5 h-5" /> Add Course
                        </button>
                    </div>
                </div>
            </div>

            <!-- Drop Course Section -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow mt-2">
                <h2
                    class="text-xl font-semibold mb-4 text-gray-900 dark:text-white"
                >
                    Enrolled Courses
                </h2>
                <div
                    v-if="enrolledCourses.length"
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4"
                >
                    <div
                        v-for="course in enrolledCourses"
                        :key="course.id"
                        class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg shadow flex flex-col justify-between"
                    >
                        <div>
                            <h3
                                class="text-lg font-bold text-gray-900 dark:text-white"
                            >
                                {{ course.title }}
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Instructor: {{ course.instructor }}
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Calendar: {{ course.calendar }}
                            </p>
                        </div>
                        <button
                            class="mt-4 inline-flex items-center gap-1 bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-md"
                        >
                            <TrashIcon class="w-5 h-5" /> Drop Course
                        </button>
                    </div>
                </div>
                <p v-else class="text-gray-600 dark:text-gray-400">
                    You are not enrolled in any courses.
                </p>
            </div>
        </div>
    </StudentLayout>
</template>
