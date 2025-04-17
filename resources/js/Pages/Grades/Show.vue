<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Head } from "@inertiajs/vue3";

defineProps({
    grade: Object,
});
</script>

<template>
    <AppLayout>
        <Head :title="`Grade: ${grade.name}`" />

        <div class="max-w-5xl mx-auto p-6">
            <!-- Page Title -->
            <div class="mb-6 text-center">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    Grade Details
                </h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                    Below are the details of the selected grade.
                </p>
            </div>

            <!-- Grade Details Card -->
            <div class="bg-white dark:bg-gray-900 shadow-lg rounded-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                            Name
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ grade.name }}
                        </p>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                            Grade Point
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ grade.grade_point }}
                        </p>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                            Description
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ grade.description || "N/A" }}
                        </p>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                            Assigned User
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ grade.user?.name || "N/A" }}
                        </p>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                            Year
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ grade.year?.name || "N/A" }}
                        </p>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                            Semester
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ grade.semester?.name || "N/A" }}
                        </p>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                            Course
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ grade.course?.name || "N/A" }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
