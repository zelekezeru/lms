<script setup>
import { computed } from "vue";
import {
    AcademicCapIcon,
    ClipboardDocumentListIcon,
    BookOpenIcon,
    CalendarIcon,
    ChartBarIcon,
    ArrowLeftOnRectangleIcon,
    CogIcon,
    QuestionMarkCircleIcon,
    Square2StackIcon,
} from "@heroicons/vue/24/outline";
import SidebarItem from "./SidebarItem.vue";

const props = defineProps({
    isOpen: Boolean,
    isMobile: Boolean,
});

const wrapperClasses = computed(() => {
    if (props.isMobile) {
        return [
            "fixed inset-y-0 left-0 z-50 bg-white dark:bg-gray-800 shadow-md transition-transform duration-300",
            props.isOpen ? "translate-x-0 w-64" : "-translate-x-full w-64",
        ];
    }
    return [
        "bg-white dark:bg-gray-800 shadow-md transition-all duration-300 flex-shrink-0 h-full",
        props.isOpen ? "w-64" : "w-20",
    ];
});
</script>

<template>
    <aside :class="wrapperClasses">
        <div class="flex flex-col h-full ml-2">
            <div class="flex items-center justify-center h-16 px-2">
                <AcademicCapIcon class="h-8 w-8 text-indigo-600 dark:text-indigo-400" />
                <span v-if="isOpen" class="ml-2 text-xl font-bold text-gray-800 dark:text-white">
                    Instructor Portal
                </span>
            </div>

            <nav class="flex-1 px-2 space-y-2 mt-4">
                <SidebarItem
                    :icon="AcademicCapIcon"
                    label="Dashboard"
                    :href="route('instructor.dashboard')"
                    :isCollapsed="!isOpen"
                />
                <SidebarItem
                    :icon="BookOpenIcon"
                    label="My Courses"
                    :href="route('instructor.courses')"
                    :isCollapsed="!isOpen"
                />
                <SidebarItem
                    :icon="Square2StackIcon"
                    label="My Sections"
                    :href="route('instructor.sections')"
                    :isCollapsed="!isOpen"
                />
                <SidebarItem
                    :icon="ClipboardDocumentListIcon"
                    label="Attendance"
                    href="/instructor/attendance"
                    :isCollapsed="!isOpen"
                />
                <SidebarItem
                    :icon="ChartBarIcon"
                    label="Grades"
                    :href="route('instructor.grades')"
                    :isCollapsed="!isOpen"
                />
                <SidebarItem
                    :icon="CalendarIcon"
                    label="Schedule"
                    :href="route('instructor.schedule')"
                    :isCollapsed="!isOpen"
                />
            </nav>

            <div class="px-2 pb-4 mt-auto">
                <SidebarItem
                    :icon="CogIcon"
                    label="Settings"
                    href="#"
                    :isCollapsed="!isOpen"
                />
                <SidebarItem
                    :icon="QuestionMarkCircleIcon"
                    label="Help"
                    href="#"
                    :isCollapsed="!isOpen"
                />
            </div>
        </div>
    </aside>
</template>
