<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link } from "@inertiajs/vue3";
import {
    AcademicCapIcon,
    ClipboardIcon,
    CalendarDaysIcon,
    ChartBarIcon,
    UserGroupIcon,
    ChevronLeftIcon,
    BookOpenIcon,
    ClockIcon,
} from "@heroicons/vue/24/outline";

const props = defineProps({
    section: { type: Object, required: true },
    instructor: { type: Object, required: false },
});

const cardAccents = [
    "from-violet-500 to-purple-600",
    "from-sky-500 to-blue-600",
    "from-emerald-500 to-teal-600",
    "from-rose-500 to-pink-600",
    "from-amber-500 to-orange-500",
    "from-indigo-500 to-violet-600",
];

const courseActions = (sectionId, courseId) => [
    {
        label: "Students",
        tab: "students",
        icon: UserGroupIcon,
        color: "bg-violet-600 hover:bg-violet-700 text-white",
    },
    {
        label: "Assessments",
        tab: "assessments",
        icon: ChartBarIcon,
        color: "bg-indigo-600 hover:bg-indigo-700 text-white",
    },
    {
        label: "Attendance",
        tab: "attendance",
        icon: CalendarDaysIcon,
        color: "bg-emerald-600 hover:bg-emerald-700 text-white",
    },
];
</script>

<template>
    <AppLayout>
        <div class="pb-24 md:pb-8 max-w-4xl mx-auto px-4 md:px-6 pt-4 space-y-5">

            <!-- Back -->
            <Link
                :href="route('instructor.sections')"
                class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400 hover:text-violet-600 dark:hover:text-violet-400 transition"
            >
                <ChevronLeftIcon class="w-4 h-4" /> Back to sections
            </Link>

            <!-- Section hero card -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="h-2 bg-gradient-to-r from-violet-500 to-purple-600"></div>
                <div class="p-5">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <AcademicCapIcon class="w-5 h-5 text-violet-500 shrink-0" />
                                <span class="text-xs font-semibold text-violet-500 uppercase tracking-wider">{{ section.code }}</span>
                            </div>
                            <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ section.name }}</h1>
                            <p v-if="section.description" class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ section.description }}</p>
                        </div>
                        <span
                            :class="[
                                'text-xs font-semibold rounded-full px-3 py-1',
                                section.isCompleted
                                    ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400'
                                    : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                            ]"
                        >
                            {{ section.isCompleted ? 'Completed' : 'In Progress' }}
                        </span>
                    </div>

                    <!-- Info chips -->
                    <div class="mt-4 flex flex-wrap gap-2">
                        <div v-if="section.program?.name" class="flex items-center gap-1.5 bg-gray-50 dark:bg-gray-700/50 rounded-xl px-3 py-1.5 text-xs text-gray-700 dark:text-gray-300">
                            <BookOpenIcon class="w-3.5 h-3.5 text-gray-400" />
                            {{ section.program.name }}
                        </div>
                        <div v-if="section.track?.name" class="flex items-center gap-1.5 bg-gray-50 dark:bg-gray-700/50 rounded-xl px-3 py-1.5 text-xs text-gray-700 dark:text-gray-300">
                            <ClockIcon class="w-3.5 h-3.5 text-gray-400" />
                            {{ section.track.name }} Track
                        </div>
                        <div v-if="section.yearLevel" class="flex items-center gap-1.5 bg-violet-50 dark:bg-violet-900/20 rounded-xl px-3 py-1.5 text-xs font-semibold text-violet-600 dark:text-violet-400">
                            Year {{ section.yearLevel }}
                        </div>
                        <div v-if="section.program?.language" class="flex items-center gap-1.5 bg-gray-50 dark:bg-gray-700/50 rounded-xl px-3 py-1.5 text-xs text-gray-700 dark:text-gray-300">
                            {{ section.program.language }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Courses in section -->
            <section>
                <h2 class="text-base font-bold text-gray-900 dark:text-white mb-3">
                    Courses in this Section
                    <span class="ml-1.5 text-sm font-normal text-gray-400">({{ (section.courses ?? []).length }})</span>
                </h2>

                <div v-if="(section.courses ?? []).length" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div
                        v-for="(course, idx) in section.courses"
                        :key="course.id"
                        class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden shadow-sm"
                    >
                        <div :class="['h-1.5 bg-gradient-to-r', cardAccents[idx % cardAccents.length]]"></div>
                        <div class="p-4">
                            <!-- Course title -->
                            <div class="flex items-start justify-between gap-2 mb-3">
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white line-clamp-2">{{ course.name }}</h3>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ course.code }}</p>
                                </div>
                                <span v-if="course.credit_hours" class="shrink-0 text-xs font-bold bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-lg px-2 py-1">
                                    {{ course.credit_hours }} cr
                                </span>
                            </div>

                            <!-- Instructor info -->
                            <p v-if="course.instructor?.name" class="text-xs text-gray-500 dark:text-gray-400 mb-3 flex items-center gap-1.5">
                                <AcademicCapIcon class="w-3.5 h-3.5 shrink-0" />
                                {{ course.instructor.name }}
                            </p>

                            <!-- Action buttons -->
                            <div class="flex flex-col gap-2">
                                <Link
                                    v-for="action in courseActions(section.id, course.id)"
                                    :key="action.tab"
                                    :href="route('instructor.sections.courses', { section: section.id, course: course.id, tab: action.tab })"
                                    :class="['flex items-center justify-center gap-2 px-3 py-2 rounded-xl text-xs font-semibold transition active:scale-95', action.color]"
                                >
                                    <component :is="action.icon" class="w-3.5 h-3.5" />
                                    {{ action.label }}
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else class="text-center py-10">
                    <BookOpenIcon class="w-10 h-10 text-gray-300 dark:text-gray-600 mx-auto mb-3" />
                    <p class="text-sm text-gray-400">No courses in this section.</p>
                </div>
            </section>

        </div>
    </AppLayout>
</template>
