<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { defineProps, ref } from "vue";
import { useI18n } from "vue-i18n";
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

const { t } = useI18n();

const tabs = [
    { key: "details", label: t('tracks.tabs.details'), icon: CogIcon },
    { key: "curriculums", label: t('tracks.tabs.curriculums'), icon: BookOpenIcon },
    { key: "courses", label: t('tracks.tabs.courses'), icon: AcademicCapIcon },
    { key: "sections", label: t('tracks.tabs.sections'), icon: UsersIcon },
    { key: "students", label: t('tracks.tabs.students'), icon: UsersIcon },
];
</script>

<template>
    <AppLayout>
        <div class="p-4 mx-auto max-w-7xl sm:p-6 lg:p-8">
            <h1
                class="mb-6 text-3xl font-bold text-center text-gray-900 sm:text-4xl dark:text-gray-100"
            >
                {{ track.name }} {{ t('tracks.track_title_suffix') }}
            </h1>
            <nav class="flex justify-center mb-6 space-x-4 border-b border-gray-200 dark:border-gray-700">
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

            <div class="p-6 bg-white border shadow dark:bg-gray-800 rounded-xl dark:border-gray-700">
                <transition
                    mode="out-in"
                    enter-active-class="transition duration-300 ease-out"
                    enter-from-class="scale-75 opacity-0"
                    enter-to-class="scale-100 opacity-100"
                    leave-active-class="transition duration-200 ease-in"
                    leave-from-class="scale-100 opacity-100"
                    leave-to-class="scale-75 opacity-0"
                >
                    <div :key="selectedTab">
                        <ShowDetails v-if="selectedTab === 'details'" :track="track" />
                        <ShowCurriculum
                            v-else-if="selectedTab === 'curriculums'"
                            :track="track"
                            :curriculums="curriculums"
                            :courses="courses"
                        />
                        <ShowCourses
                            v-else-if="selectedTab === 'courses'"
                            :track="track"
                            :courses="courses"
                        />
                        <ShowSections
                            v-else-if="selectedTab === 'sections'"
                            :track="track"
                            :years="years"
                            :courses="courses"
                        />
                        <ShowStudents
                            v-else-if="selectedTab === 'students'"
                            :track="track"
                        />
                    </div>
                </transition>
            </div>
        </div>
    </AppLayout>
</template>

