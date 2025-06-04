<script setup>
import { ref } from "vue";
import { AcademicCapIcon } from "@heroicons/vue/24/solid";
import { router } from "@inertiajs/vue3";
import Overview from "./Tabs/Overview.vue";
import Attendance from "./Tabs/Attendance.vue";
import Assessments from "./Tabs/Results.vue";
import ClassSchedules from "./Tabs/ClassSchedules.vue";
import ClassSessions from "./Tabs/ClassSessions.vue";
import StudentLayout from "@/Layouts/StudentLayout.vue";
import Results from "./Tabs/Results.vue";

const props = defineProps({
    enrollment: {
        type: Object,
        required: true,
    },
    student: {
        type: Object,
        required: false,
    },
    weights: {
        type: Array,
        required: false,
    },
});

const showMobileNav = ref(false);
const activeTab = ref(route().params.tab ?? "Overview");

const rightMenu = [
    { name: "Overview", key: "overview", icon: AcademicCapIcon },
    { name: "Results", key: "results", icon: AcademicCapIcon },
    { name: "Class Schedules", key: "classSchedules", icon: AcademicCapIcon },
    { name: "Class Sessions", key: "classSessions", icon: AcademicCapIcon },
];

const changeTab = (tabName) => {
    activeTab.value = tabName;

    router.visit(
        route(route().current(), {
            enrollment: props.enrollment.id,
            tab: tabName,
        })
    );
};
</script>

<template>
    <StudentLayout>
        <div
            class="max-w-7xl mx-auto py-10 px-4"
            :class="{ '!px-1': activeTab == 'classSessions' }"
        >
            <!-- Course Info -->
            <div class="flex-1">
                <div class="flex justify-between items-center">
                    <h1
                        class="text-3xl font-bold mb-4 text-gray-900 dark:text-white flex"
                    >
                        <AcademicCapIcon class="h-6 w-6 mr-2 text-blue-500" />
                        {{ enrollment.course.name }} ({{
                            enrollment.course.code
                        }})
                    </h1>
                    <span
                        class="py-1 px-3 bg-red-600 text-white rounded-3xl"
                        :class="{
                            '!bg-green-600': enrollment.status == 'Enrolled',
                        }"
                        >{{ enrollment.status }}</span
                    >
                </div>
            </div>
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
                        class="bg-white dark:bg-gray-800 rounded-xl shadow py-6 px-0 sm:px-1 sm:py-2 space-y-4"
                    >
                        <p>
                            <strong>Description:</strong>
                            {{
                                enrollment.course.description ||
                                "No description available."
                            }}
                        </p>
                        <p><strong>Instructor:</strong> Dr. Jane Doe</p>
                        <p>
                            <strong>Credits:</strong>
                            {{ enrollment.course.credit_hours }}
                        </p>
                    </div>

                    <!-- Tab Contents -->
                    <div class="mt-6">
                        <Overview
                            v-if="activeTab === 'overview'"
                            :course="course"
                        />
                        <Results
                            v-if="activeTab === 'results'"
                            :enrollment="enrollment"
                            :student="student"
                            :weights="weights"
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
    </StudentLayout>
</template>
