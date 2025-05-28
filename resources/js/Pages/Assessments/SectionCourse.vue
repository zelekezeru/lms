<script setup>
import { defineProps, ref, computed } from "vue";
import "sweetalert2/dist/sweetalert2.min.css";
import {
    CogIcon,
    DocumentTextIcon,
    PresentationChartBarIcon,
    CheckBadgeIcon,
} from "@heroicons/vue/24/solid";
import ShowDetails from "./Tabs/ShowDetails.vue";
import ShowResults from "../InstructorPortal/Components/ShowResults.vue";
import ShowWeights from "../InstructorPortal/Components/ShowWeights.vue";
import ShowGrades from "../InstructorPortal/Components/ShowGrades.vue";

import InstructorLayout from "@/Layouts/InstructorLayout.vue";
import AppLayout from "@/Layouts/AppLayout.vue"; // default
import { usePage } from "@inertiajs/vue3";

// Props
const props = defineProps({
    section: {
        type: Object,
        required: true,
    },
    course: {
        type: Object,
        required: true,
    },
    semester: {
        type: Object,
        required: true,
    },
    weights: {
        type: Array,
        required: true,
    },
    instructor: {
        type: Object,
        required: true,
    },
    grades: {
        type: Array,
        required: true,
    },
    students: {
        type: Object,
        required: true,
    },
});

// Tabs
const tabs = [
    { key: "details", label: "Details", icon: CogIcon },
    { key: "results", label: "results", icon: DocumentTextIcon },
    { key: "weights", label: "weights", icon: PresentationChartBarIcon },
    { key: "grades", label: "Grades", icon: CheckBadgeIcon },
];

const selectedTab = ref("details");

const user = usePage().props.auth.user;

const LayoutComponent = computed(() => {
    if (user.roles?.includes("INSTRUCTOR")) return InstructorLayout;
    return AppLayout;
});
</script>

<template>
    <component :is="LayoutComponent">
        <div class="max-w-5xl mx-auto p-6">
            <h1
                class="text-3xl font-semibold mb-6 text-gray-900 dark:text-gray-100 text-center"
            >
                {{ section.name }} - {{ course.name }} Course Assessments
            </h1>

            <nav
                class="flex justify-center space-x-4 mb-6 border-b border-gray-200 dark:border-gray-700"
            >
                <button
                    v-for="tab in tabs"
                    :key="tab.key"
                    @click="selectedTab = tab.key"
                    :class="[
                        'flex items-center px-4 py-2 space-x-2 text-sm font-medium transition',
                        selectedTab === tab.key
                            ? 'border-b-2 border-indigo-500 text-indigo-600'
                            : 'text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200',
                    ]"
                >
                    <component :is="tab.icon" class="w-5 h-5" />
                    <span>{{ tab.label }}</span>
                </button>
            </nav>

            <div
                class="bg-white dark:bg-gray-800 shadow rounded-xl p-6 border dark:border-gray-700"
            >
                <transition
                    mode="out-in"
                    enter-active-class="transition duration-300 ease-out"
                    enter-from-class="opacity-0 scale-75"
                    enter-to-class="opacity-100 scale-100"
                    leave-active-class="transition duration-200 ease-in"
                    leave-from-class="opacity-100 scale-100"
                    leave-to-class="opacity-0 scale-75"
                >
                    <div
                        :key="selectedTab"
                        class="bg-white dark:bg-gray-800 shadow rounded-xl p-6 border dark:border-gray-700"
                    >
                        <!-- Details Panel -->
                        <ShowDetails
                            v-if="selectedTab == 'details'"
                            :section="section"
                            :course="course"
                            :semester="semester"
                            :instructor="instructor"
                        />

                        <!-- Results Panel -->
                        <ShowResults
                            v-else-if="selectedTab == 'results'"
                            :section="section"
                            :course="course"
                            :semester="semester"
                            :instructor="instructor"
                            :weights="weights"
                            :studentsList="students"
                        />

                        <!-- Weights Panel -->
                        <ShowWeights
                            v-else-if="selectedTab == 'weights'"
                            :weights="weights"
                            :section="section"
                            :course="course"
                            :semester="semester"
                            :instructor="instructor"
                        />

                        <!-- Grades Panel -->
                        <ShowGrades
                            v-else-if="selectedTab == 'grades'"
                            :section="section"
                            :weights="weights"
                            :course="course"
                            :semester="semester"
                            :instructor="instructor"
                            :grades="grades"
                            :studentsList="students"
                        />
                    </div>
                </transition>
            </div>
        </div>
    </component>
</template>
