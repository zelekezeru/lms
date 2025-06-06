<script setup>
import { computed } from "vue";
import {
    AcademicCapIcon,
    ClipboardDocumentListIcon,
    UsersIcon,
    BookOpenIcon,
    CalendarIcon,
    ChartBarIcon,
    CogIcon,
    QuestionMarkCircleIcon,
    UserCircleIcon,
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
            <!-- Logo / Title -->
            <div class="flex items-center justify-center h-16 px-2">
                <AcademicCapIcon
                    class="h-8 w-8 text-indigo-600 dark:text-indigo-400"
                />
                <span
                    v-if="isOpen"
                    class="ml-2 text-xl font-bold text-gray-800 dark:text-white select-none"
                >
                    Registrar
                </span>
            </div>

            <!-- Menu items -->
            <nav class="flex-1 px-2 space-y-1 mt-4">
                <SidebarItem
                    :icon="AcademicCapIcon"
                    label="Dashboard"
                    :href="route('registrar.dashboard')"
                    :isCollapsed="!isOpen"
                />
                <SidebarItem
                    :icon="UsersIcon"
                    label="Students"
                    :href="route('registrar.students')"
                    :isCollapsed="!isOpen"
                />
                <SidebarItem
                    :icon="ClipboardDocumentListIcon"
                    label="Registrations"
                    href="/registrations"
                    :isCollapsed="!isOpen"
                />
                <SidebarItem
                    :icon="BookOpenIcon"
                    label="Programs"
                    href="/programs"
                    :isCollapsed="!isOpen"
                />
                <SidebarItem
                    :icon="CalendarIcon"
                    label="Semesters"
                    href="/semesters"
                    :isCollapsed="!isOpen"
                />
                <SidebarItem
                    :icon="ChartBarIcon"
                    label="Reports"
                    href="/reports"
                    :isCollapsed="!isOpen"
                />
                <SidebarItem
                    :icon="UserCircleIcon"
                    label="Profile"
                    href="/profile"
                    :isCollapsed="!isOpen"
                />
            </nav>

            <!-- Bottom links -->
            <div class="px-2 pb-4 mt-auto">
                <SidebarItem
                    :icon="CogIcon"
                    label="Profile"
                    :href="route('profile.edit')"
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
