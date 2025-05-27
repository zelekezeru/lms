<script setup>
import { ref } from "vue";
import {
    UserGroupIcon,
    ClipboardDocumentListIcon,
    CalendarDaysIcon,
} from "@heroicons/vue/24/solid";
import { usePage, useForm, router } from "@inertiajs/vue3";
import InstructorLayout from "@/Layouts/InstructorLayout.vue";

const props = usePage().props;

// Tabs setup
const tabs = [
    {
        key: "student",
        label: "Student",
        icon: UserGroupIcon,
        route: route("instructor.sections.courses.students", {
            section: props.section.id,
            course: props.course.id,
        }),
    },
    {
        key: "assessments",
        label: "Assessments",
        icon: ClipboardDocumentListIcon,
        route: route("instructor.sections.courses.assessments", {
            section: props.section.id,
            course: props.course.id,
        }),
    },
    {
        key: "attendance",
        label: "Attendance",
        icon: CalendarDaysIcon,
        route: route("instructor.sections.courses.attendance", {
            section: props.section.id,
            course: props.course.id,
        }),
    },
];

const currentRoute = usePage().component;

const selectedTab = ref(
  tabs.find((tab) => currentRoute.toLowerCase().includes(tab.key))?.key ||
  "student"
);

function navigateTo(tab) {
    selectedTab.value = tab.key;
    router.visit(tab.route);
}

</script>

<template>
    <InstructorLayout>
        <div class="max-w-6xl mx-auto p-6">
            <nav
                class="flex justify-center space-x-4 mb-6 border-b border-gray-200 dark:border-gray-700"
            >
                <button
                    v-for="tab in tabs"
                    :key="tab.key"
                    @click="navigateTo(tab)"
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
                <slot />
            </div>
        </div>
    </InstructorLayout>
</template>
