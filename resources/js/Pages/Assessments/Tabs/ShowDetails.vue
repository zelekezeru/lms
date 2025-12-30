<script setup>
import { AcademicCapIcon } from "@heroicons/vue/24/outline";
import { Link } from "@inertiajs/vue3";
import { defineProps } from "vue";

const props = defineProps({
    course: {
        type: Object,
        required: true,
    },
    instructor: {
        type: Object,
        required: true,
    },
    section: {
        type: Object,
        required: true,
    },
    courseOffering: {
        type: Object,
        required: true,
    },

});
</script>

<template>
        <!-- Section Name -->
    <div
        class="bg-gradient-to-br from-blue-50 via-white to-blue-100 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 rounded-xl shadow-lg py-8 px-6 sm:px-8 space-y-6 border border-blue-100 dark:border-gray-700"
    >
        <div class="flex items-center space-x-4 mb-2">
            <AcademicCapIcon class="h-8 w-8 text-blue-500 dark:text-blue-400" />
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ course.name }}
            </h2>
            <span class="bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200 px-3 py-1 rounded-full text-sm font-semibold">
                {{ course.code }}
            </span>
        </div>
        <p class="text-gray-700 dark:text-gray-300 text-lg">
            <strong class="text-blue-600 dark:text-blue-400">Description:</strong>
            {{ course.description || "No description available." }}
        </p>
        <div class="flex flex-wrap gap-6">
            <div class="flex items-center">
                <span class="font-semibold text-blue-600 dark:text-blue-400 mr-2">Credits:</span>
                <span class="bg-blue-50 dark:bg-blue-900 px-2 py-1 rounded text-blue-800 dark:text-blue-200 font-medium">
                    {{ course.credit_hours }}
                </span>
            </div>
            <div class="flex items-center">
                <span class="font-semibold text-blue-600 dark:text-blue-400 mr-2">Instructor:</span>
                <span class="bg-blue-50 dark:bg-blue-900 px-2 py-1 rounded text-blue-800 dark:text-blue-200 font-medium">
                    {{ instructor.user.name || "TBA" }}
                </span>
            </div>
            <div class="flex items-center">
                <span class="font-semibold text-blue-600 dark:text-blue-400 mr-2">Grade Status:</span>
                <span
                    :class="courseOffering.completed ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200' : 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200'"
                    class="px-2 py-1 rounded font-medium"
                >
                    {{ courseOffering.completed ? "Completed" : "In Progress" }}
                </span>
            </div>
        </div>
    </div>
    
</template>
