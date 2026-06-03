<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import {
    AcademicCapIcon,
    ArrowRightIcon,
    BookOpenIcon,
    DocumentTextIcon,
    BookmarkIcon,
    ClockIcon,
} from "@heroicons/vue/24/outline";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    course: Object,
    instructor: Object,
});
</script>

<template>
    <AppLayout>
        <div class="max-w-7xl mx-auto py-10 px-4 space-y-8">
            <!-- Header Block -->
            <div class="flex items-center space-x-3 pb-4 border-b border-gray-200 dark:border-gray-700/60">
                <div class="p-3 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-xl">
                    <AcademicCapIcon class="h-7 w-7" />
                </div>
                <div>
                    <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 dark:text-white">
                        {{ course.name }}
                    </h1>
                    <p class="text-xs font-semibold text-gray-400 tracking-wider mt-0.5">COURSE CODE: {{ course.code }}</p>
                </div>
            </div>

            <!-- Course Meta Info Card -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700/80 p-6 space-y-6">
                <div>
                    <h3 class="text-sm font-semibold uppercase tracking-wider text-gray-400 mb-2">Course Description</h3>
                    <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                        {{ course.description || "No description available for this course outline." }}
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 pt-6 border-t border-gray-100 dark:border-gray-700/60">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-lg">
                            <BookOpenIcon class="w-5 h-5" />
                        </div>
                        <div>
                            <span class="block text-[10px] uppercase font-bold text-gray-400">Course Credits</span>
                            <span class="text-sm font-bold text-gray-800 dark:text-gray-200">{{ course.credit_hours || 3 }} Credits</span>
                        </div>
                    </div>

                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-lg">
                            <DocumentTextIcon class="w-5 h-5" />
                        </div>
                        <div>
                            <span class="block text-[10px] uppercase font-bold text-gray-400">Teaching Sections</span>
                            <span class="text-sm font-bold text-gray-800 dark:text-gray-200">{{ course.sections.length }} Section{{ course.sections.length === 1 ? "" : "s" }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sections Taught Grid -->
            <div class="space-y-6">
                <div>
                    <h2 class="text-lg md:text-xl font-bold text-gray-800 dark:text-white">Sections Assigned to This Course</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400">View schedules, submit assignments, and record attendance for each class section.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <div
                        v-for="section in course.sections"
                        :key="section.id"
                        class="group transition duration-300 transform hover:-translate-y-1 hover:shadow-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700/80 rounded-2xl p-6 flex flex-col justify-between"
                    >
                        <div class="space-y-4">
                            <!-- Section Title -->
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="text-md font-bold text-gray-900 dark:text-white">
                                        {{ section.name }}
                                    </h3>
                                    <span class="text-[10px] uppercase font-semibold tracking-wider text-gray-400 dark:text-gray-500">Section Code</span>
                                </div>
                                <span class="text-xs font-bold text-blue-700 dark:text-blue-300 bg-blue-50 dark:bg-blue-900/30 rounded-full px-2.5 py-1">
                                    {{ section.code }}
                                </span>
                            </div>

                            <!-- Program and Track Info -->
                            <div class="text-xs text-gray-700 dark:text-gray-300 space-y-2 pt-2 border-t border-gray-100 dark:border-gray-700/60">
                                <p class="flex items-center gap-2">
                                    <BookmarkIcon class="w-4 h-4 text-gray-400" />
                                    <span><strong>Program:</strong> {{ section.program?.name }}</span>
                                </p>
                                <p class="flex items-center gap-2">
                                    <BookmarkIcon class="w-4 h-4 text-gray-400" />
                                    <span><strong>Track:</strong> {{ section.track?.name }}</span>
                                </p>
                                <div class="flex gap-4 pt-1 font-semibold text-gray-500">
                                    <p>Year: <span class="text-gray-800 dark:text-gray-200">{{ section.yearLevel }}</span></p>
                                    <p>Sem: <span class="text-gray-800 dark:text-gray-200">{{ section.semester?.level }}</span></p>
                                </div>
                            </div>

                            <!-- Status / Completion -->
                            <div class="text-xs font-semibold pt-1">
                                <span 
                                    v-if="section.isCompleted" 
                                    class="inline-flex items-center px-2 py-0.5 rounded text-green-700 bg-green-50 dark:bg-green-950/20 dark:text-green-400"
                                >
                                    Completed
                                </span>
                                <span 
                                    v-else 
                                    class="inline-flex items-center px-2 py-0.5 rounded text-yellow-700 bg-yellow-50 dark:bg-yellow-950/20 dark:text-yellow-400"
                                >
                                    In Progress
                                </span>
                            </div>
                        </div>

                        <!-- View Details Button -->
                        <Link
                            :href="
                                route('instructor.sections.detail', {
                                    section: section.id,
                                })
                            "
                            class="inline-flex items-center justify-center w-full mt-5 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-sm transition space-x-2"
                        >
                            <span>Open Section</span>
                            <ArrowRightIcon class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1" />
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
