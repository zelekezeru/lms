<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { defineProps, ref } from "vue";
import "sweetalert2/dist/sweetalert2.min.css";
import {
    CogIcon,
    AcademicCapIcon,
    UsersIcon,
    BookOpenIcon,
} from "@heroicons/vue/24/solid";
import ShowDetails from "./Tabs/ShowDetails.vue";
import ShowCurriculum from "./Tabs/ShowCurriculum.vue";
import ShowCourses from "./Tabs/ShowCourses.vue";
import ShowSections from "./Tabs/ShowSections.vue";
import ShowStudents from "./Tabs/ShowStudents.vue";

// Define the props for the track
const props = defineProps({
    track: {
        type: Object,
        required: true,
    },

    courses: {
        type: Object,
        required: true,
    },
    
    years: {
        type: Object,
        required: true,
    },
    curriculums: {
        type: Object,
        required: true,
    },
});

// Multi nav header options
const selectedTab = ref("details");

const tabs = [
    { key: "details", label: "Details", icon: CogIcon },
    { key: "curriculums", label: "Curriculums", icon: BookOpenIcon },
    { key: "courses", label: "Courses", icon: AcademicCapIcon },
    { key: "sections", label: "Sections", icon: UsersIcon },
    { key: "students", label: "Students", icon: UsersIcon },
];
</script>

<template>
    <AppLayout>
        <div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
            <h1
                class="text-3xl sm:text-4xl font-bold mb-6 text-center text-gray-900 dark:text-gray-100"
            >
                {{ track.name }} Track
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
                    <div :key="selectedTab">
                        <!-- Details Panel -->
                        <ShowDetails
                            v-if="selectedTab == 'details'"
                            :track="track"
                        />

                        <!-- Curriculum Panel -->
                        <ShowCurriculum
                            v-else-if="selectedTab == 'curriculums'"
                            :track="track"
                            :curriculums="curriculums"
                            :courses="courses"
                        />

                        <!-- Courses Panel -->
                        <ShowCourses
                            v-else-if="selectedTab == 'courses'"
                            :track="track"
                            :courses="courses"
                        />

                        <!-- Section Panel -->
                        <ShowSections
                            v-else-if="selectedTab == 'sections'"
                            :track="track"
                            :years="years"
                            :courses="courses"
                        />

                        <ShowStudents
                            v-else-if="selectedTab == 'students'"
                            :track="track"
                        />
                    </div>
                </transition>
            </div>
        </div>
    </AppLayout>
</template>
