<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Head } from "@inertiajs/vue3";

defineProps({
    result: Object,
});
</script>

<template>
    <AppLayout>
        <Head :title="`Result: ${result.first_name}`" />

        <div class="max-w-5xl mx-auto p-6">
            <!-- Page Title -->
            <div class="mb-6 text-center">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    Result Details
                </h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                    Below are the details of the selected result.
                </p>
            </div>

            <!-- Result Details Card -->
            <div class="bg-white dark:bg-gray-900 shadow-lg rounded-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                            Student Name
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ result.first_name }}
                        </p>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                            Course
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ result.course_name }}
                        </p>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                            Grade
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ result.grade }}
                        </p>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                            Semester
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ result.semester?.name || "N/A" }}
                        </p>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                            Year
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ result.year?.name || "N/A" }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
