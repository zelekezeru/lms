<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import {
    AcademicCapIcon,
    ClipboardIcon,
    CalendarDaysIcon,
    UserIcon,
    DocumentTextIcon,
    BookmarkIcon,
} from "@heroicons/vue/24/outline";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    section: {
        type: Object,
        required: true,
    },
    instructor: {
        type: Object,
        required: false,
    },
});
</script>

<template>
    <AppLayout>
        <div class="max-w-7xl mx-auto py-10 px-4 space-y-8">
            <!-- Section Header -->
            <div class="flex items-center space-x-3 pb-4 border-b border-gray-200 dark:border-gray-700/60">
                <div class="p-3 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-xl">
                    <AcademicCapIcon class="h-7 w-7" />
                </div>
                <div>
                    <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 dark:text-white">
                        {{ section.name }}
                    </h1>
                    <p class="text-xs font-semibold text-gray-400 tracking-wider mt-0.5">SECTION CODE: {{ section.code }}</p>
                </div>
            </div>

            <!-- Section Info Cards -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700/80 p-6 space-y-6">
                <div>
                    <h3 class="text-sm font-semibold uppercase tracking-wider text-gray-400 mb-2">Section Description</h3>
                    <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                        {{ section.description || "No specific details available for this section class." }}
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 pt-6 border-t border-gray-100 dark:border-gray-700/60">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-lg">
                            <BookmarkIcon class="w-5 h-5" />
                        </div>
                        <div>
                            <span class="block text-[10px] uppercase font-bold text-gray-400">Program</span>
                            <span class="text-sm font-bold text-gray-800 dark:text-gray-200">{{ section.program?.name }}</span>
                        </div>
                    </div>

                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 rounded-lg">
                            <BookmarkIcon class="w-5 h-5" />
                        </div>
                        <div>
                            <span class="block text-[10px] uppercase font-bold text-gray-400">Track Specialty</span>
                            <span class="text-sm font-bold text-gray-800 dark:text-gray-200">{{ section.track?.name }}</span>
                        </div>
                    </div>

                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-lg">
                            <DocumentTextIcon class="w-5 h-5" />
                        </div>
                        <div>
                            <span class="block text-[10px] uppercase font-bold text-gray-400">Instruction Language</span>
                            <span class="text-sm font-bold text-gray-800 dark:text-gray-200">{{ section.program?.language || "English" }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Courses Grid -->
            <div class="space-y-6">
                <div>
                    <h2 class="text-lg md:text-xl font-bold text-gray-800 dark:text-white">Assigned Course Offerings</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Select a course to view students, take attendance, or submit student marks.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div
                        v-for="course in section.courses"
                        :key="course.id"
                        class="group bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900/60 border border-gray-200 dark:border-gray-700/80 rounded-2xl shadow-sm hover:shadow-lg transition flex flex-col justify-between p-6"
                    >
                        <div class="space-y-4">
                            <!-- Course Header -->
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <h3 class="text-md font-bold text-gray-900 dark:text-white line-clamp-1 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition">
                                        {{ course.name }}
                                    </h3>
                                    <p class="text-xs text-gray-400 font-semibold mt-0.5">Code: {{ course.code }}</p>
                                </div>
                                <div class="p-2 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-lg shrink-0">
                                    <AcademicCapIcon class="w-5 h-5" />
                                </div>
                            </div>

                            <!-- Instructor Info -->
                            <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 pt-2 border-t border-gray-100 dark:border-gray-700/60">
                                <UserIcon class="w-4 h-4 text-gray-400" />
                                <span>Instructor: <strong class="text-gray-700 dark:text-gray-300">{{ course.instructor?.name || "TBA" }}</strong></span>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-6 flex flex-col gap-2">
                            <Link
                                :href="
                                    route('instructor.sections.courses', {
                                        section: section.id,
                                        course: course.id,
                                        tab: 'students',
                                    })
                                "
                                class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-sm transition space-x-2 w-full"
                            >
                                <UserIcon class="w-4 h-4" /> 
                                <span>Class Roster</span>
                            </Link>

                            <Link
                                :href="
                                    route('instructor.sections.courses', {
                                        section: section.id,
                                        course: course.id,
                                        tab: 'assessments',
                                    })
                                "
                                class="inline-flex items-center justify-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-xs font-semibold rounded-xl shadow-sm transition space-x-2 w-full"
                            >
                                <ClipboardIcon class="w-4 h-4" />
                                <span>Grades & Assessments</span>
                            </Link>

                            <Link
                                :href="
                                    route('instructor.sections.courses', {
                                        section: section.id,
                                        course: course.id,
                                        tab: 'attendance',
                                    })
                                "
                                class="inline-flex items-center justify-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold rounded-xl shadow-sm transition space-x-2 w-full"
                            >
                                <CalendarDaysIcon class="w-4 h-4" />
                                <span>Attendance Matrix</span>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
