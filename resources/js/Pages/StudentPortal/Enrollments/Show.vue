<script setup>
import { ref } from "vue";
import { router } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import Overview from "./Tabs/Overview.vue";
import Results from "./Tabs/Results.vue";
import ClassSchedules from "./Tabs/ClassSchedules.vue";
import ClassSessions from "./Tabs/ClassSessions.vue";
import {
    AcademicCapIcon,
    ClockIcon,
    UserIcon,
    BookmarkIcon,
    ChartBarIcon,
    CalendarDaysIcon,
    PlayCircleIcon,
    Squares2X2Icon,
    ChevronLeftIcon,
} from "@heroicons/vue/24/outline";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    enrollment: { type: Object, required: true },
    student: { type: Object, required: false },
    activeSemester: { type: Object, required: false },
    weights: { type: Array, required: false },
    classSchedules: { type: Array, required: true },
    classSessions: { type: Array, required: true },
});

const activeTab = ref(route().params.tab ?? "overview");

const tabs = [
    { key: "overview",       label: "Overview",   icon: Squares2X2Icon },
    { key: "results",        label: "Results",    icon: ChartBarIcon },
    { key: "classSchedules", label: "Schedule",   icon: CalendarDaysIcon },
    { key: "classSessions",  label: "Sessions",   icon: PlayCircleIcon },
];

const changeTab = (key) => {
    activeTab.value = key;
    router.visit(
        route(route().current(), { enrollment: props.enrollment.id, tab: key }),
        { preserveState: true, preserveScroll: true }
    );
};

const statusStyle = (status) => {
    if (status?.toLowerCase() === "enrolled")
        return "bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400";
    return "bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400";
};
</script>

<template>
    <AppLayout>
        <div class="pb-24 md:pb-8 max-w-4xl mx-auto px-4 md:px-6 pt-4">

            <!-- Back link -->
            <Link
                :href="route('student.enrollments')"
                class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 mb-4 transition"
            >
                <ChevronLeftIcon class="w-4 h-4" /> Back to courses
            </Link>

            <!-- Course hero card -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden mb-5">
                <!-- Gradient top stripe -->
                <div class="h-2 bg-gradient-to-r from-indigo-500 to-purple-600"></div>

                <div class="p-5">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <AcademicCapIcon class="w-5 h-5 text-indigo-500 shrink-0" />
                                <span class="text-xs font-semibold text-indigo-500 uppercase tracking-wider">
                                    {{ enrollment.course.code }}
                                </span>
                            </div>
                            <h1 class="text-xl font-bold text-gray-900 dark:text-white leading-snug">
                                {{ enrollment.course.name }}
                            </h1>
                            <p v-if="enrollment.course.description" class="text-sm text-gray-500 dark:text-gray-400 mt-1 line-clamp-2">
                                {{ enrollment.course.description }}
                            </p>
                        </div>
                        <span :class="['text-xs font-semibold rounded-full px-3 py-1 shrink-0', statusStyle(enrollment.status)]">
                            {{ enrollment.status }}
                        </span>
                    </div>

                    <!-- Meta chips -->
                    <div class="mt-4 flex flex-wrap gap-2">
                        <div class="flex items-center gap-1.5 bg-gray-50 dark:bg-gray-700/50 rounded-xl px-3 py-1.5 text-xs text-gray-700 dark:text-gray-300">
                            <UserIcon class="w-3.5 h-3.5 text-gray-400" />
                            {{ enrollment.instructor?.name ?? 'TBA' }}
                        </div>
                        <div class="flex items-center gap-1.5 bg-gray-50 dark:bg-gray-700/50 rounded-xl px-3 py-1.5 text-xs text-gray-700 dark:text-gray-300">
                            <BookmarkIcon class="w-3.5 h-3.5 text-gray-400" />
                            {{ enrollment.section?.name ?? '' }}
                            <template v-if="enrollment.section?.track"> · {{ enrollment.section.track.name }}</template>
                        </div>
                        <div class="flex items-center gap-1.5 bg-indigo-50 dark:bg-indigo-900/20 rounded-xl px-3 py-1.5 text-xs font-semibold text-indigo-600 dark:text-indigo-400">
                            <ClockIcon class="w-3.5 h-3.5" />
                            {{ enrollment.course.credit_hours }} credit hrs
                        </div>
                        <div v-if="enrollment.section?.studyMode" class="flex items-center gap-1.5 bg-gray-50 dark:bg-gray-700/50 rounded-xl px-3 py-1.5 text-xs text-gray-700 dark:text-gray-300">
                            {{ enrollment.section.studyMode.name }}
                        </div>
                    </div>
                </div>

                <!-- ── Sticky tab bar ── -->
                <div class="sticky top-0 z-10 bg-white dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700">
                    <div class="flex overflow-x-auto scrollbar-none">
                        <button
                            v-for="tab in tabs"
                            :key="tab.key"
                            @click="changeTab(tab.key)"
                            :class="[
                                'flex items-center gap-2 px-5 py-3.5 text-sm font-medium whitespace-nowrap border-b-2 transition-all duration-150 shrink-0',
                                activeTab === tab.key
                                    ? 'border-indigo-600 text-indigo-600 dark:border-indigo-400 dark:text-indigo-400'
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
                    <Overview v-if="activeTab === 'overview'" :course="enrollment.course" />
                    <Results
                        v-else-if="activeTab === 'results'"
                        :enrollment="enrollment"
                        :student="student"
                        :weights="weights"
                    />
                    <ClassSchedules
                        v-else-if="activeTab === 'classSchedules'"
                        :enrollment="enrollment"
                        :student="student"
                        :active-semester="activeSemester"
                        :classSchedules="classSchedules"
                    />
                    <ClassSessions
                        v-else-if="activeTab === 'classSessions'"
                        :enrollment="enrollment"
                        :active-semester="activeSemester"
                        :student="student"
                        :classSessions="classSessions"
                    />
                </div>
            </transition>
        </div>
    </AppLayout>
</template>
