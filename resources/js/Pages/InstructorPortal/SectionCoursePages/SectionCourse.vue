<script setup>
import { ref } from "vue";
import { AcademicCapIcon } from "@heroicons/vue/24/solid";
import { router } from "@inertiajs/vue3";
import Overview from "./Tabs/Overview.vue";
import Attendance from "./Tabs/Attendance.vue";
import Assessments from "./Tabs/Assessments.vue";
import Students from "./Tabs/Students.vue";
import Announcements from "./Tabs/Announcments.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import ClassSchedules from "./Tabs/ClassSchedules.vue";
import ClassSessions from "./Tabs/ClassSessions.vue";
import StudentResults from "@/Pages/StudentPortal/StudentResults.vue";

const props = defineProps({
    course: {
        type: Object,
        required: true,
    },
    section: {
        type: Object,
        required: true,
    },
    semester: {
        type: Object,
        required: true,
    },
    instructor: {
        type: Object,
        required: true,
    },
    classSchedules: {
        type: Array,
        required: false,
    },
    classSessions: {
        type: Array,
        required: false,
    },
    weights: {
        type: Object,
        required: true,
    },
    grades: {
        type: Object,
        required: true,
    },
    students: {
        type: Array,
        required: true,
    },
    studentsUnformatted: {
        type: Array,
        required: true,
    },
    rooms: {
        type: Array,
        required: true,
    },
    studentResults: {
        type: Array,
    },
    courseOffering: {
        type: Object,
        required: true,
    },
});

const showMobileNav = ref(false);
const activeTab = ref(route().params.tab ?? "Overview");

const rightMenu = [
    { name: "Overview", key: "overview", icon: AcademicCapIcon },
    { name: "Students", key: "students", icon: AcademicCapIcon },
    { name: "Assessments", key: "assessments", icon: AcademicCapIcon },
    { name: "Class Schedules", key: "classSchedules", icon: AcademicCapIcon },
    { name: "Class Sessions", key: "classSessions", icon: AcademicCapIcon },
];

const changeTab = (tabName) => {
    activeTab.value = tabName;

    router.visit(
        route(route().current(), {
            section: props.section.id,
            course: props.course.id,
            tab: tabName,
        })
    );
};
</script>

<template>
    <AppLayout>
        <div
            class="max-w-7xl mx-auto py-10 px-4"
            :class="{ '!px-1': activeTab == 'classSessions' }"
        >
            <!-- Course Header -->
            <h1
                class="text-3xl font-bold mb-6 text-gray-900 dark:text-white flex items-center"
            >
                <AcademicCapIcon class="h-6 w-6 mr-2 text-blue-500" />
                {{ course.name }} ({{ course.code }})
            </h1>

            <!-- Toggle Button (mobile only) -->
            <div class="lg:hidden mb-4 flex justify-end">
                <button
                    @click="showMobileNav = !showMobileNav"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition"
                >
                    {{ showMobileNav ? "Close" : "Course Navigation" }}
                </button>
            </div>

            <!-- Slide-in Navigation -->
            <Transition
                enter-active-class="transition ease-out duration-300"
                enter-from-class="translate-x-full opacity-0"
                enter-to-class="translate-x-0 opacity-100"
                leave-active-class="transition ease-in duration-200"
                leave-from-class="translate-x-0 opacity-100"
                leave-to-class="translate-x-full opacity-0"
            >
                <div
                    v-if="showMobileNav"
                    class="fixed inset-0 bg-black bg-opacity-40 z-40 flex justify-end lg:hidden"
                    @click.self="showMobileNav = false"
                >
                    <div
                        class="w-64 bg-white dark:bg-gray-900 shadow-lg p-4 z-50"
                    >
                        <h2
                            class="text-lg font-bold text-gray-800 dark:text-white mb-4"
                        >
                            Course Navigation
                        </h2>
                        <ul class="space-y-2 flex flex-col">
                            <li
                                v-for="item in rightMenu"
                                :key="item.key"
                                @click="
                                    () => {
                                        changeTab(item.key);
                                        showMobileNav = false;
                                    }
                                "
                                class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-800 dark:text-gray-200 cursor-pointer transition duration-200"
                                :class="{
                                    'bg-gray-100 dark:bg-gray-800 font-semibold':
                                        activeTab === item.key,
                                }"
                            >
                                <component
                                    :is="item.icon"
                                    class="w-5 h-5 mr-3"
                                />
                                <span>{{ item.name }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </Transition>

            <div class="flex flex-col lg:flex-row">
                <!-- Main Content -->
                <div class="flex-1">
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

                    <!-- Tab Contents -->
                    <div class="mt-6">
                        <Overview
                            v-if="activeTab === 'overview'"
                            :course="course"
                        />
                        <Students
                            v-if="activeTab === 'students'"
                            :course="course"
                            :section="section"
                            :students="students"
                        />
                        <ClassSchedules
                            v-if="activeTab === 'classSchedules'"
                            :course="course"
                            :section="section"
                            :class-schedules="classSchedules"
                            :rooms="rooms"
                            :active-semester="semester"
                        />
                        <ClassSessions
                            v-if="activeTab === 'classSessions'"
                            :course="course"
                            :section="section"
                            :students="students"
                            :instructor="instructor"
                            :class-sessions="classSessions"
                            :rooms="rooms"
                            :active-semester="semester"
                        />
                        <Assessments
                            v-if="activeTab === 'assessments'"
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
                        <Attendance
                            v-if="activeTab === 'attendance'"
                            :attendanceRecords="attendanceRecords"
                        />
                    </div>
                </div>

                <!-- Sidebar Navigation (large screens only) -->
                <div class="hidden lg:block lg:w-1/3">
                    <div
                        class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg p-4 max-h-[70vh] lg:fixed lg:top-32 lg:right-12 lg:w-64 overflow-y-auto"
                    >
                        <h2
                            class="text-lg font-bold text-gray-800 dark:text-white mb-4"
                        >
                            Course Navigation
                        </h2>
                        <ul class="space-y-2 flex flex-col">
                            <li
                                v-for="item in rightMenu"
                                :key="item.key"
                                @click="changeTab(item.key)"
                                class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-800 dark:text-gray-200 cursor-pointer transition duration-200"
                                :class="{
                                    'bg-gray-100 dark:bg-gray-800 font-semibold':
                                        activeTab === item.key,
                                }"
                            >
                                <component
                                    :is="item.icon"
                                    class="w-5 h-5 mr-3"
                                />
                                <span>{{ item.name }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
