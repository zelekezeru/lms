<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { defineProps, ref } from "vue";
import { Link, router, useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import Modal from "@/Components/Modal.vue";
import {
    PencilIcon,
    EyeIcon,
    TrashIcon,
    CogIcon,
    AcademicCapIcon,
    UsersIcon,
    PencilSquareIcon,
    PlusCircleIcon,
    BookOpenIcon,
    HomeModernIcon,
    ClipboardDocumentListIcon,
    ClockIcon,
} from "@heroicons/vue/24/solid";
import ShowDetails from "./Tabs/ShowDetails.vue";
import ShowAcademic from "./Tabs/ShowAcademic.vue";
import ShowCourses from "./Tabs/ShowCourses.vue";
import ShowSections from "./Tabs/ShowSections.vue";

const props = defineProps({
    instructor: Object,
    courses: Object,
});

const selectedTab = ref("details");

const tabs = ref([
    { key: "details", label: "Details", icon: CogIcon },
    { key: "academics", label: "Academics", icon: BookOpenIcon },
    { key: "courses", label: "Courses", icon: AcademicCapIcon },
    { key: "sections", label: "Sections", icon: UsersIcon },
]);

const isDrawerOpen = ref(false);
const toggleDrawer = () => {
    isDrawerOpen.value = !isDrawerOpen.value;
};
</script>

<template>
    <AppLayout>
        <div class="max-w-8xl mx-auto p-6 relative">
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
                class="hidden md:flex overflow-x-auto pb-2 mb-6 border-b border-gray-200 dark:border-gray-700"
                :class="'justify-center'"
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
                class="text-3xl font-semibold mb-6 text-gray-900 dark:text-gray-100 text-center"
            >
                {{ instructor?.user?.name }}
            </h1>

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
                    <ShowDetails
                        v-if="selectedTab === 'details'"
                        :instructor="instructor"
                    />
                    <ShowAcademic v-else-if="selectedTab === 'academics'" />
                    <ShowCourses
                        v-else-if="selectedTab === 'courses'"
                        :instructor="instructor"
                        :courses="courses"
                    />
                    <ShowSections
                        v-else-if="selectedTab === 'sections'"
                        :instructor="instructor"
                        :courses="courses"
                    />
                </div>
            </transition>
        </div>
    </AppLayout>
</template>
