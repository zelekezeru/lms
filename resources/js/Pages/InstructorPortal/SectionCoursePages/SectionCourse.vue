<script setup>
import { ref } from "vue";
import { router, Link } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import Overview from "./Tabs/Overview.vue";
import Attendance from "./Tabs/Attendance.vue";
import Assessments from "./Tabs/Assessments.vue";
import Students from "./Tabs/Students.vue";
import ClassSchedules from "./Tabs/ClassSchedules.vue";
import ClassSessions from "./Tabs/ClassSessions.vue";
import {
    AcademicCapIcon,
    UserGroupIcon,
    ChartBarIcon,
    CalendarDaysIcon,
    PlayCircleIcon,
    Squares2X2Icon,
    ClipboardDocumentCheckIcon,
    ChevronLeftIcon,
    ClockIcon,
    UserIcon,
} from "@heroicons/vue/24/outline";

const props = defineProps({
    course: { type: Object, required: true },
    section: { type: Object, required: true },
    semester: { type: Object, required: true },
    instructor: { type: Object, required: true },
    classSchedules: { type: Array, required: false },
    classSessions: { type: Array, required: false },
    weights: { type: Object, required: true },
    grades: { type: Object, required: true },
    students: { type: Array, required: true },
    studentsUnformatted: { type: Array, required: true },
    rooms: { type: Array, required: true },
    studentResults: { type: Array },
    courseOffering: { type: Object, required: true },
});

const activeTab = ref(route().params.tab ?? "overview");

const tabs = [
    { key: "overview",       label: "Overview",   icon: Squares2X2Icon },
    { key: "students",       label: "Students",   icon: UserGroupIcon },
    { key: "assessments",    label: "Assessments", icon: ChartBarIcon },
    { key: "classSchedules", label: "Schedule",   icon: CalendarDaysIcon },
    { key: "classSessions",  label: "Sessions",   icon: PlayCircleIcon },
];

const changeTab = (key) => {
    activeTab.value = key;
    router.visit(
        route(route().current(), { section: props.section.id, course: props.course.id, tab: key }),
        { preserveState: true, preserveScroll: true }
    );
};

const statusStyle = (completed) =>
    completed
        ? "bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400"
        : "bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400";
</script>

<template>
    <AppLayout>
        <div class="pb-24 md:pb-8 max-w-4xl mx-auto px-4 md:px-6 pt-4">

            <!-- Back -->
            <Link
                :href="route('instructor.sections.detail', { section: section.id })"
                class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400 hover:text-violet-600 dark:hover:text-violet-400 mb-4 transition"
            >
                <ChevronLeftIcon class="w-4 h-4" /> {{ section.name }}
            </Link>

            <!-- Course hero card -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden mb-5">
                <div class="h-2 bg-gradient-to-r from-violet-500 to-indigo-600"></div>
                <div class="p-5">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <AcademicCapIcon class="w-5 h-5 text-violet-500 shrink-0" />
                                <span class="text-xs font-semibold text-violet-500 uppercase tracking-wider">{{ course.code }}</span>
                            </div>
                            <h1 class="text-xl font-bold text-gray-900 dark:text-white leading-snug">{{ course.name }}</h1>
                            <p v-if="course.description" class="text-sm text-gray-500 dark:text-gray-400 mt-1 line-clamp-2">{{ course.description }}</p>
                        </div>
                        <span :class="['text-xs font-semibold rounded-full px-3 py-1 shrink-0', statusStyle(courseOffering.completed)]">
                            {{ courseOffering.completed ? 'Completed' : 'In Progress' }}
                        </span>
                    </div>

                    <!-- Meta chips -->
                    <div class="mt-4 flex flex-wrap gap-2">
                        <div class="flex items-center gap-1.5 bg-gray-50 dark:bg-gray-700/50 rounded-xl px-3 py-1.5 text-xs text-gray-700 dark:text-gray-300">
                            <UserIcon class="w-3.5 h-3.5 text-gray-400" />
                            {{ instructor.user.name }}
                        </div>
                        <div class="flex items-center gap-1.5 bg-gray-50 dark:bg-gray-700/50 rounded-xl px-3 py-1.5 text-xs text-gray-700 dark:text-gray-300">
                            <UserGroupIcon class="w-3.5 h-3.5 text-gray-400" />
                            {{ students.length }} student{{ students.length !== 1 ? 's' : '' }}
                        </div>
                        <div class="flex items-center gap-1.5 bg-violet-50 dark:bg-violet-900/20 rounded-xl px-3 py-1.5 text-xs font-semibold text-violet-600 dark:text-violet-400">
                            <ClockIcon class="w-3.5 h-3.5" />
                            {{ course.credit_hours }} credit hrs
                        </div>
                    </div>
                </div>

                <!-- Sticky tab bar -->
                <div class="sticky top-0 z-10 bg-white dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700">
                    <div class="flex overflow-x-auto scrollbar-none">
                        <button
                            v-for="tab in tabs"
                            :key="tab.key"
                            @click="changeTab(tab.key)"
                            :class="[
                                'flex items-center gap-2 px-5 py-3.5 text-sm font-medium whitespace-nowrap border-b-2 transition-all duration-150 shrink-0',
                                activeTab === tab.key
                                    ? 'border-violet-600 text-violet-600 dark:border-violet-400 dark:text-violet-400'
                                    : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300',
                            ]"
                        >
                            <component :is="tab.icon" class="w-4 h-4" />
                            {{ tab.label }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tab content -->
            <transition
                mode="out-in"
                enter-active-class="transition duration-200 ease-out"
                enter-from-class="opacity-0 translate-y-2"
                enter-to-class="opacity-100 translate-y-0"
                leave-active-class="transition duration-150 ease-in"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div :key="activeTab">
                    <Overview v-if="activeTab === 'overview'" :course="course" />
                    <Students
                        v-else-if="activeTab === 'students'"
                        :course="course"
                        :section="section"
                        :students="students"
                    />
                    <Assessments
                        v-else-if="activeTab === 'assessments'"
                        :course="course"
                        :section="section"
                        :grades="grades"
                        :weights="weights"
                        :instructor="instructor"
                        :semester="semester"
                        :students="studentsUnformatted"
                        :studentResults="studentResults"
                        :courseOffering="courseOffering"
                    />
                    <ClassSchedules
                        v-else-if="activeTab === 'classSchedules'"
                        :course="course"
                        :section="section"
                        :class-schedules="classSchedules"
                        :rooms="rooms"
                        :active-semester="semester"
                    />
                    <ClassSessions
                        v-else-if="activeTab === 'classSessions'"
                        :course="course"
                        :section="section"
                        :students="students"
                        :instructor="instructor"
                        :class-sessions="classSessions"
                        :rooms="rooms"
                        :active-semester="semester"
                    />
                </div>
            </transition>
        </div>
    </AppLayout>
</template>
