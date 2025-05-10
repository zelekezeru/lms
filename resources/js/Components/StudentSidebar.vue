<script setup>
import { computed } from "vue";
import {
    AcademicCapIcon,
    CurrencyDollarIcon,
    ClipboardDocumentListIcon,
    BookOpenIcon,
    ArrowPathIcon,
    ChartBarIcon,
    CalendarIcon,
    ArrowLeftOnRectangleIcon,
    CogIcon,
    QuestionMarkCircleIcon,
} from "@heroicons/vue/24/outline";
import SidebarItem from "./SidebarItem.vue";

const props = defineProps({
    isOpen: Boolean,
    isMobile: Boolean,
});

const wrapperClasses = computed(() => {
    if (props.isMobile) {
        // overlay drawer for mobile
        return [
            "fixed inset-y-0 left-0 z-50 bg-white dark:bg-gray-800 shadow-md transition-transform duration-300",
            props.isOpen ? "translate-x-0 w-64" : "-translate-x-full w-64",
        ];
    }
    // desktop fixed sidebar, collapsible width
    return [
        "bg-white dark:bg-gray-800 shadow-md transition-all duration-300 flex-shrink-0 h-full",
        props.isOpen ? "w-64" : "w-20",
    ];
});
</script>

<template>
    <aside :class="wrapperClasses">
        <div class="flex flex-col h-full ml-2">
            <!-- Logo / Title -->
            <div class="flex items-center justify-center h-16 px-2">
                <AcademicCapIcon
                    class="h-8 w-8 text-indigo-600 dark:text-indigo-400"
                />
                <span
                    v-if="isOpen"
                    class="ml-2 text-xl font-bold text-gray-800 dark:text-white"
                >
                    Student Portal
                </span>
            </div>

            <!-- Menu items -->
            <nav class="flex-1 px-2 space-y-2 mt-4">
                <SidebarItem
                    :icon="AcademicCapIcon"
                    label="Dashboard"
                    :href="route('student.dashboard')"
                    :isCollapsed="!isOpen"
                />
                <SidebarItem
                    :icon="BookOpenIcon"
                    label="Courses"
                    :href="route('student.courses')"
                    :isCollapsed="!isOpen"
                />
                <SidebarItem
                    :icon="CurrencyDollarIcon"
                    label="Payment Info"
                    href="/payment"
                    :isCollapsed="!isOpen"
                />
                <SidebarItem
                    :icon="ClipboardDocumentListIcon"
                    label="Registration"
                    href="/student/registration"
                    :isCollapsed="!isOpen"
                />
                <SidebarItem
                    :icon="ArrowPathIcon"
                    label="Drop Semester"
                    href="/student/add"
                    :isCollapsed="!isOpen"
                />
                <SidebarItem
                    :icon="ChartBarIcon"
                    label="Result"
                    href="/student/result"
                    :isCollapsed="!isOpen"
                />
            </nav>

            <!-- Bottom links -->
            <div class="px-2 pb-4 mt-auto">
                <SidebarItem
                    :icon="CogIcon"
                    label="Settings"
                    href="/settings"
                    :isCollapsed="!isOpen"
                />
                <SidebarItem
                    :icon="QuestionMarkCircleIcon"
                    label="Help"
                    href="/help"
                    :isCollapsed="!isOpen"
                />
            </div>
        </div>
    </aside>
</template>
