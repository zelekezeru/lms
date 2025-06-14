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
    UserIcon,
    BuildingOffice2Icon,
    ClockIcon,
    BriefcaseIcon,
    PlusIcon,
    ChartPieIcon,
} from "@heroicons/vue/24/outline";
import SidebarItem from "./SidebarItem.vue";
import SidebarDropdownMenu from "./SidebarDropdownMenu.vue";

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
            <a :href="route('dashboard')">
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
            </a>
            
            <!-- Menu items -->
            <nav class="flex-1 px-2 space-y-1 mt-4 max-h-[800px] overflow-auto">
                <SidebarItem
                    :icon="AcademicCapIcon"
                    label="Dashboard"
                    :href="route('registrar.dashboard')"
                    :isCollapsed="!isOpen"
                />
                <SidebarDropdownMenu
                    label="Calendar Structure"
                    :icon="ClockIcon"
                    :sidebar-visible="isOpen"
                >
                    <SidebarItem
                        :icon="CalendarIcon"
                        label="Manage Years"
                        :href="route('years.index')"
                        :isCollapsed="!isOpen"
                    />
                    <SidebarItem
                        :icon="ChartPieIcon"
                        label="Manage Semesters"
                        :href="route('semesters.index')"
                        :isCollapsed="!isOpen"
                    />
                    <SidebarItem
                        :icon="ClockIcon"
                        label="Manage Calendars"
                        :href="route('calendars.index')"
                        :isCollapsed="!isOpen"
                    />
                </SidebarDropdownMenu>

                <SidebarDropdownMenu
                    label="Academic Structure"
                    :icon="AcademicCapIcon"
                    :sidebar-visible="isOpen"
                >
                    <SidebarItem
                        :icon="CogIcon"
                        label="Manage Programs"
                        :href="route('programs.index')"
                        :isCollapsed="!isOpen"
                    />
                    <SidebarItem
                        :icon="BuildingOffice2Icon"
                        label="Manage Tracks"
                        :href="route('tracks.index')"
                        :isCollapsed="!isOpen"
                    />
                    <SidebarItem
                        :icon="ClockIcon"
                        label="Manage Study Modes"
                        :href="route('studyModes.index')"
                        :isCollapsed="!isOpen"
                    />
                    <SidebarItem
                        :icon="BriefcaseIcon"
                        label="Manage Courses"
                        :href="route('courses.index')"
                        :isCollapsed="!isOpen"
                    />
                </SidebarDropdownMenu>

                <SidebarDropdownMenu
                    label="Section Classes"
                    :icon="AcademicCapIcon"
                    :sidebar-visible="isOpen"
                >
                    <SidebarItem
                        :icon="CogIcon"
                        label="Manage Sections"
                        :href="route('sections.index')"
                        :isCollapsed="!isOpen"
                    />
                    <SidebarItem
                        :icon="CogIcon"
                        label="Manage Rooms"
                        :href="route('rooms.index')"
                        :isCollapsed="!isOpen"
                    />
                    <SidebarItem
                        :icon="CogIcon"
                        label="Manage Weights"
                        :href="route('weights.index')"
                        :isCollapsed="!isOpen"
                    />
                </SidebarDropdownMenu>

                <SidebarDropdownMenu
                    label="User Management"
                    :icon="UserIcon"
                    :sidebar-visible="isOpen"
                >
                    <SidebarItem
                        :icon="CogIcon"
                        label="Manage Instructors"
                        :href="route('instructors.index')"
                        :isCollapsed="!isOpen"
                    />
                    <SidebarItem
                        :icon="BriefcaseIcon"
                        label="Manage Employees"
                        :href="route('employees.index')"
                        :isCollapsed="!isOpen"
                    />
                    <SidebarItem
                        :icon="BriefcaseIcon"
                        label="Manage Users"
                        :href="route('users.index')"
                        :isCollapsed="!isOpen"
                    />
                </SidebarDropdownMenu>

                <SidebarDropdownMenu
                    label="Student Management"
                    :icon="UsersIcon"
                    :sidebar-visible="isOpen"
                >
                    <SidebarItem
                        :icon="UsersIcon"
                        label="Manage Students"
                        :href="route('students.index')"
                        :isCollapsed="!isOpen"
                    />
                    <SidebarItem
                        :icon="PlusIcon"
                        label="Register Student"
                        :href="route('students.create')"
                        :isCollapsed="!isOpen"
                    />
                    <SidebarItem
                        :icon="CogIcon"
                        label="User Documents"
                        :href="route('userDocuments.index')"
                        :isCollapsed="!isOpen"
                    />
                </SidebarDropdownMenu>

                <SidebarDropdownMenu
                    label="Centers"
                    :icon="BuildingOffice2Icon"
                    :sidebar-visible="isOpen"
                >
                    <SidebarItem
                        :icon="BuildingOffice2Icon"
                        label="Manage Centers"
                        :href="route('centers.index')"
                        :isCollapsed="!isOpen"
                    />
                </SidebarDropdownMenu>
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
