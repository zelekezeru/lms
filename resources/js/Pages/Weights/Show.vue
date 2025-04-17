<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Head } from "@inertiajs/vue3";

defineProps({
    weight: Object,
});
</script>

<template>
    <AppLayout>
        <Head :title="`Weight: ${weight.name}`" />

        <div class="max-w-5xl mx-auto p-6">
            <!-- Page Title -->
            <div class="mb-6 text-center">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    Weight Details
                </h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                    Below are the details of the selected weight.
                </p>
            </div>

            <!-- Weight Details Card -->
            <div class="bg-white dark:bg-gray-900 shadow-lg rounded-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                            Name
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ weight.name }}
                        </p>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                            Weight Point
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ weight.weight_point }}
                        </p>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                            Description
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ weight.weight_description || "N/A" }}
                        </p>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                            Assigned User
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ weight.user?.name || "N/A" }}
                        </p>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                            Year
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ weight.year?.name || "N/A" }}
                        </p>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                            Semester
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ weight.semester?.name || "N/A" }}
                        </p>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                            Course
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ weight.course?.name || "N/A" }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
