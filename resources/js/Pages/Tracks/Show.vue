<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { defineProps, ref } from "vue";
import { useI18n } from "vue-i18n";
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
import { Deferred } from "@inertiajs/vue3";

const props = defineProps({
    track: Object,
    centers: Array,
    years: Array,
    courses: Array,
    students: Array,
});

const { t } = useI18n();

const tabs = ref([
    { key: "details", label: t("tracks.tabs.details"), icon: CogIcon },
    {
        key: "curriculums",
        label: t("tracks.tabs.curriculums"),
        icon: BookOpenIcon,
    },
    { key: "courses", label: t("tracks.tabs.courses"), icon: AcademicCapIcon },
    { key: "sections", label: t("tracks.tabs.sections"), icon: UsersIcon },
    { key: "students", label: t("tracks.tabs.students"), icon: UsersIcon },
]);

const selectedTab = ref("details");
const isDrawerOpen = ref(false);
const toggleDrawer = () => {
    isDrawerOpen.value = !isDrawerOpen.value;
};
</script>

<template>
    <AppLayout>
        <div class="max-w-7xl p-4 mx-auto relative">
            <div class="flex justify-end mb-4 md:hidden">
                <button
                    @click="toggleDrawer"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition"
                >
                    <CogIcon class="w-5 h-5 mr-2" />
                    Tabs
                </button>
            </div>

            <div
                class="fixed top-0 right-0 h-full w-72 bg-white dark:bg-gray-900 shadow-lg z-50 transform transition-transform duration-300 md:hidden"
                :class="{
                    'translate-x-0': isDrawerOpen,
                    'translate-x-full': !isDrawerOpen,
                }"
            >
                <div
                    class="p-4 border-b dark:border-gray-700 flex justify-between items-center"
                >
                    <h2
                        class="text-lg font-semibold text-gray-900 dark:text-gray-100"
                    >
                        Navigation
                    </h2>
                    <button
                        @click="toggleDrawer"
                        class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300"
                    >
                        ✕
                    </button>
                </div>

                <nav class="flex flex-col px-4 py-2 space-y-2">
                    <button
                        v-for="tab in tabs"
                        :key="tab.key"
                        @click="
                            () => {
                                selectedTab = tab.key;
                                toggleDrawer();
                            }
                        "
                        class="flex items-center space-x-2 px-3 py-2 rounded-lg transition"
                        :class="
                            selectedTab === tab.key
                                ? 'bg-indigo-100 dark:bg-indigo-800 text-indigo-700 dark:text-white'
                                : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800'
                        "
                    >
                        <component :is="tab.icon" class="w-5 h-5" />
                        <span>{{ tab.label }}</span>
                    </button>
                </nav>
            </div>

            <div
                v-if="isDrawerOpen"
                @click="toggleDrawer"
                class="fixed inset-0 bg-black bg-opacity-25 z-40 md:hidden"
            ></div>

            <nav
                class="hidden md:flex overflow-x-auto pb-2 mb-6 border-b border-gray-200 dark:border-gray-700 justify-center"
            >
                <div class="flex space-x-4 min-w-max">
                    <button
                        v-for="tab in tabs"
                        :key="tab.key"
                        @click="selectedTab = tab.key"
                        class="flex-shrink-0 flex items-center px-4 py-2 space-x-2 text-sm font-medium transition whitespace-nowrap"
                        :class="
                            selectedTab === tab.key
                                ? 'border-b-2 border-indigo-500 text-indigo-600 dark:text-indigo-400'
                                : 'text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200'
                        "
                    >
                        <component :is="tab.icon" class="w-5 h-5" />
                        <span>{{ tab.label }}</span>
                    </button>
                </div>
            </nav>

            <h1
                class="mb-6 text-3xl font-bold text-center text-gray-900 sm:text-4xl dark:text-gray-100"
            >
                {{ track.name }} {{ t("tracks.track_title_suffix") }}
            </h1>

            <transition
                mode="out-in"
                enter-active-class="transition duration-300 ease-out"
                enter-from-class="scale-75 opacity-0"
                enter-to-class="scale-100 opacity-100"
                leave-active-class="transition duration-200 ease-in"
                leave-from-class="scale-100 opacity-100"
                leave-to-class="scale-75 opacity-0"
            >
                <div
                    :key="selectedTab"
                    class="bg-white dark:bg-gray-800 shadow rounded-xl p-6 border dark:border-gray-700"
                >
                    <ShowDetails
                        v-if="selectedTab === 'details'"
                        :track="track"
                    />
                    <ShowCurriculum
                        v-else-if="selectedTab === 'curriculums'"
                        :track="track"
                        :courses="courses"
                    />
                    <Deferred
                        v-else-if="selectedTab === 'courses'"
                        :data="['courses']"
                    >
                        <template #fallback>
                            <div class="flex justify-center items-center h-32">
                                <div
                                    class="w-6 h-6 border-4 border-dashed rounded-full animate-spin border-indigo-500 dark:border-white"
                                ></div>
                            </div>
                        </template>
                        <ShowCourses :track="track" :courses="courses" />
                    </Deferred>
                    <ShowSections
                        v-else-if="selectedTab === 'sections'"
                        :track="track"
                        :years="years"
                        :courses="courses"
                        :centers="centers"
                    />
                    <Deferred
                        v-else-if="selectedTab === 'students'"
                        :data="['students']"
                    >
                        <template #fallback>
                            <div class="flex justify-center items-center h-32">
                                <div
                                    class="w-6 h-6 border-4 border-dashed rounded-full animate-spin border-indigo-500 dark:border-white"
                                ></div>
                            </div>
                        </template>

                        <ShowStudents :track="track" :students="students" />
                    </Deferred>
                </div>
            </transition>
        </div>
    </AppLayout>
</template>
