<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import {
    AcademicCapIcon,
    ClockIcon,
    UserGroupIcon,
    ArrowRightIcon,
} from "@heroicons/vue/24/outline";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    instructor: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <AppLayout>
        <div class="mb-8">
            <h1
                class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-white"
            >
                My Courses
            </h1>
            <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">
                Courses you're teaching in different sections during this academic semester.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div
                v-for="course in instructor.courses"
                :key="course.id"
                class="group p-6 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700/80 rounded-2xl shadow-sm hover:shadow-lg transition flex flex-col justify-between"
            >
                <div>
                    <!-- Course Header -->
                    <div class="flex items-start gap-4 mb-4">
                        <div class="p-3 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-xl">
                            <AcademicCapIcon class="w-6 h-6" />
                        </div>
                        <div>
                            <h2
                                class="text-lg font-bold text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition"
                            >
                                {{ course.name }}
                            </h2>
                            <p class="text-xs font-semibold text-gray-400 tracking-wider">
                                {{ course.code }}
                            </p>
                        </div>
                    </div>

                    <!-- Details -->
                    <div class="mt-4 space-y-3 pt-3 border-t border-gray-100 dark:border-gray-700/60 text-xs text-gray-600 dark:text-gray-300">
                        <div class="flex items-center gap-2">
                            <UserGroupIcon class="w-4 h-4 text-emerald-500" />
                            <span>{{ course.enrolled ?? "N/A" }} students enrolled</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <ClockIcon class="w-4 h-4 text-amber-500" />
                            <span>Credits: <strong class="text-gray-800 dark:text-gray-200">{{ course.credit_hours || 3 }}</strong></span>
                        </div>
                    </div>
                </div>

                <!-- Footer Section Count & Actions -->
                <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-700/60 space-y-4">
                    <div class="flex justify-between items-center text-xs font-semibold">
                        <span class="text-gray-400">Assigned Sections</span>
                        <span class="px-2 py-0.5 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                            {{ course.sectionsCount }} section{{ course.sectionsCount === 1 ? "" : "s" }}
                        </span>
                    </div>

                    <Link
                        :href="
                            route('instructor.courses.detail', {
                                course: course.id,
                            })
                        "
                        class="inline-flex items-center justify-center w-full px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-sm transition space-x-2"
                    >
                        <span>View Course Details</span>
                        <ArrowRightIcon class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1" />
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
