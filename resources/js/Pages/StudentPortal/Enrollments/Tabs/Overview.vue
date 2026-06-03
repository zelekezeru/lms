<script setup>
import { BookOpenIcon, UserIcon, ClockIcon, TrophyIcon } from "@heroicons/vue/24/outline";

const props = defineProps({
    course: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <div class="space-y-6">
        <!-- Course Description & Syllabus -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-150 dark:border-gray-700 shadow-sm space-y-4">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <BookOpenIcon class="w-5 h-5 text-indigo-500" />
                Course Outline & Description
            </h3>
            <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
                {{ course.description || "No course description or syllabus outline has been uploaded for this course offering yet." }}
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Credits Card -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-150 dark:border-gray-700 shadow-sm flex items-center space-x-4">
                <div class="p-3 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 rounded-xl">
                    <TrophyIcon class="w-6 h-6" />
                </div>
                <div>
                    <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Credit Hours</h4>
                    <p class="text-xl font-bold text-gray-850 dark:text-gray-100 mt-0.5">
                        {{ course.credit_hours }} Credits
                    </p>
                </div>
            </div>

            <!-- Subject Code -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-150 dark:border-gray-700 shadow-sm flex items-center space-x-4">
                <div class="p-3 bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400 rounded-xl">
                    <ClockIcon class="w-6 h-6" />
                </div>
                <div>
                    <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Course Code</h4>
                    <p class="text-xl font-bold text-gray-850 dark:text-gray-100 mt-0.5">
                        {{ course.code }}
                    </p>
                </div>
            </div>

            <!-- Learning Format -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-150 dark:border-gray-700 shadow-sm flex items-center space-x-4">
                <div class="p-3 bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 rounded-xl">
                    <UserIcon class="w-6 h-6" />
                </div>
                <div>
                    <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Language</h4>
                    <p class="text-xl font-bold text-gray-850 dark:text-gray-100 mt-0.5">
                        English
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
