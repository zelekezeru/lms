<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
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
});

const enrolledCourses = props.student.enrollments.filter(
    (enrollment) => enrollment.status == "enrolled"
);
const completedCourses = props.student.enrollments.filter(
    (enrollment) => enrollment.status == "completed"
);
const droppedCourses = props.student.enrollments.filter(
    (enrollment) => enrollment.status == "dropped"
);
const failedCourses = props.student.enrollments.filter(
    (enrollment) => enrollment.status == "failed"
);

// Get grade for a course
const getCourseGrade = (courseId) => {
    if (!props.student.grades) return "N/A";
    const gradeObj = props.student.grades.find((g) => g.course_id === courseId);
    return gradeObj ? gradeObj.grade_letter : "N/A";
};
</script>

<template>
    <AppLayout>
        <div class="max-w-7xl mx-auto py-10 px-4 space-y-8">
            <!-- Header -->
            <div class="flex justify-between items-center mt-2">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                    My Courses
                </h1>
            </div>

            <!-- Active Courses -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-150 dark:border-gray-700 shadow-sm p-6 mt-2">
                <div class="flex items-center gap-3 mb-6 text-blue-600">
                    <BookOpenIcon class="w-6 h-6" />
                    <h2
                        class="text-xl font-bold dark:text-white text-gray-900"
                    >
                        Active Courses
                    </h2>
                </div>
                <div
                    v-if="enrolledCourses.length > 0"
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
                >
                    <div
                        v-for="enrollment in enrolledCourses"
                        :key="enrollment.id"
                        class="bg-gray-50/50 dark:bg-gray-700/30 p-5 rounded-xl border border-gray-150 dark:border-gray-700 hover:shadow-md transition duration-300"
                    >
                        <Link
                            :href="
                                route('student.enrollments.show', enrollment.id)
                            "
                            class="block space-y-3"
                        >
                            <h3
                                class="text-lg font-bold text-gray-900 dark:text-white flex items-center"
                            >
                                <AcademicCapIcon
                                    class="h-6 w-6 text-blue-500 mr-2"
                                />
                                {{ enrollment.course.name }}
                            </h3>
                            <div class="text-sm space-y-1 text-gray-600 dark:text-gray-300">
                                <p><span class="font-medium">Code:</span> {{ enrollment.course.code }}</p>
                                <p>
                                    <span class="font-medium">Instructor:</span>
                                    {{
                                        enrollment.instructor
                                            ? enrollment.instructor.name
                                            : "N/A"
                                    }}
                                </p>
                                <p>
                                    <span class="font-medium">Section:</span>
                                    {{
                                        enrollment.section
                                            ? enrollment.section.name
                                            : "N/A"
                                    }}
                                </p>
                                <p>
                                    <span class="font-medium">Credits:</span> {{ enrollment.course.credit_hours }}
                                </p>
                            </div>
                        </Link>
                    </div>
                </div>
                <div v-else class="text-center py-6 text-gray-500 dark:text-gray-400">
                    You have no active courses currently.
                </div>
            </div>

            <!-- Completed Courses -->
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-150 dark:border-gray-700 shadow-sm p-6 mt-2"
                v-if="completedCourses.length > 0"
            >
                <div class="flex items-center gap-3 mb-6 text-green-600">
                    <CheckCircleIcon class="w-6 h-6" />
                    <h2
                        class="text-xl font-bold dark:text-white text-gray-900"
                    >
                        Completed Courses
                    </h2>
                </div>
                <div
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
                >
                    <div
                        class="bg-gray-50/50 dark:bg-gray-700/30 p-5 rounded-xl border border-gray-150 dark:border-gray-700"
                        v-for="enrollment in completedCourses"
                        :key="enrollment.id"
                    >
                        <h3
                            class="text-lg font-bold text-gray-900 dark:text-white"
                        >
                            {{ enrollment.course.name }}
                        </h3>
                        <div class="flex justify-between items-center mt-3 text-sm">
                            <span class="text-gray-500 dark:text-gray-400">
                                Completed: {{ new Date(enrollment.updated_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short' }) }}
                            </span>
                            <span
                                class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300"
                                >Grade: {{ getCourseGrade(enrollment.course.id) }}</span
                            >
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dropped Courses -->
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-150 dark:border-gray-700 shadow-sm p-6 mt-2"
                v-if="droppedCourses.length > 0"
            >
                <div class="flex items-center gap-3 mb-6 text-red-500">
                    <XCircleIcon class="w-6 h-6" />
                    <h2
                        class="text-xl font-bold dark:text-white text-gray-900"
                    >
                        Dropped Courses
                    </h2>
                </div>
                <div
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
                >
                    <div
                        class="bg-gray-50/50 dark:bg-gray-700/30 p-5 rounded-xl border border-gray-150 dark:border-gray-700"
                        v-for="enrollment in droppedCourses"
                        :key="enrollment.id"
                    >
                        <h3
                            class="text-lg font-bold text-gray-900 dark:text-white"
                        >
                            {{ enrollment.course.name }}
                        </h3>
                        <div class="flex justify-between items-center mt-3 text-sm">
                            <span class="text-gray-500 dark:text-gray-400">
                                Status: Dropped
                            </span>
                            <span
                                class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300"
                                >Dropped</span
                            >
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
