<script setup>
import { computed } from "vue";
import {
    AcademicCapIcon,
    KeyIcon,
    ClockIcon,
    CalendarIcon,
    ChartPieIcon,
    BuildingOffice2Icon,
    BriefcaseIcon,
    UserIcon,
    UsersIcon,
    PlusIcon,
    HandRaisedIcon,
    FolderIcon,
    ArchiveBoxArrowDownIcon,
    CurrencyDollarIcon,
    BanknotesIcon,
    CogIcon,
    QuestionMarkCircleIcon,
} from "@heroicons/vue/24/outline";
import SidebarDropdownMenu from "./SidebarDropdownMenu.vue";
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
        props.isOpen ? "w-64" : "w-14",
    ];
});
</script>

<template>
    <aside :class="wrapperClasses">
        <div
            class="flex flex-col h-full overflow-y-auto scrollbar-thin scrollbar-thumb-gray-500 scrollbar-track-gray-200 py-2"
        >
            <a :href="route('dashboard')">
                <div class="flex items-center justify-center h-16 px-2">
                    <AcademicCapIcon
                        class="h-8 w-8 text-indigo-600 dark:text-indigo-400"
                    />
                    <span
                        v-if="isOpen"
                        class="ml-2 text-xl font-bold text-gray-800 dark:text-white"
                        >{{ $t("admin_dashboard.title") }}</span
                    >
                </div>
            </a>

            <div class="space-y-2 text-sm">
                <SidebarDropdownMenu
                    :label="$t('sidebar.tenants')"
                    :icon="CogIcon"
                    :sidebar-visible="isOpen"
                >
                    <SidebarItem
                        :icon="CogIcon"
                        :label="$t('sidebar.manage_tenant')"
                        :href="route('tenants.index')"
                        :isCollapsed="!isOpen"
                    />
                </SidebarDropdownMenu>

                <SidebarDropdownMenu
                    :label="$t('sidebar.roles_permissions')"
                    :icon="KeyIcon"
                    :sidebar-visible="isOpen"
                >
                    <SidebarItem
                        :icon="CogIcon"
                        :label="$t('sidebar.roles')"
                        :href="route('roles.index')"
                        :isCollapsed="!isOpen"
                    />
                    <SidebarItem
                        :icon="CogIcon"
                        :label="$t('sidebar.permissions')"
                        :href="route('permissions.index')"
                        :isCollapsed="!isOpen"
                    />
                </SidebarDropdownMenu>

                <SidebarDropdownMenu
                    :label="$t('sidebar.calendar_management')"
                    :icon="CalendarIcon"
                    :sidebar-visible="isOpen"
                >
                    <SidebarItem
                        :icon="CalendarIcon"
                        :label="$t('sidebar.manage_years')"
                        :href="route('years.index')"
                        :isCollapsed="!isOpen"
                    />
                    <SidebarItem
                        :icon="ChartPieIcon"
                        :label="$t('sidebar.semesters')"
                        :href="route('semesters.index')"
                        :isCollapsed="!isOpen"
                    />
                    <SidebarItem
                        :icon="ClockIcon"
                        :label="$t('sidebar.calendars')"
                        :href="route('calendars.index')"
                        :isCollapsed="!isOpen"
                    />
                </SidebarDropdownMenu>

                <SidebarDropdownMenu
                        :label="$t('sidebar.academic_structure')"
                    :icon="AcademicCapIcon"
                    :sidebar-visible="isOpen"
                >
                    <SidebarItem
                        :icon="CogIcon"
                        :label="$t('sidebar.manage_programs')"
                        :href="route('programs.index')"
                        :isCollapsed="!isOpen"
                    />
                    <SidebarItem
                        :icon="BuildingOffice2Icon"
                        :label="$t('sidebar.manage_departments')"
                        :href="route('tracks.index')"
                        :isCollapsed="!isOpen"
                    />
                    <SidebarItem
                        :icon="ClockIcon"
                        :label="$t('sidebar.manage_study_modes')"
                        :href="route('studyModes.index')"
                        :isCollapsed="!isOpen"
                    />
                    <SidebarItem
                        :icon="BriefcaseIcon"
                        :label="$t('sidebar.manage_courses')"
                        :href="route('courses.index')"
                        :isCollapsed="!isOpen"
                    />
                </SidebarDropdownMenu>

                <SidebarDropdownMenu
                        :label="$t('sidebar.section_classes')"
                    :icon="AcademicCapIcon"
                    :sidebar-visible="isOpen"
                >
                    <SidebarItem
                        :icon="CogIcon"
                        :label="$t('sidebar.manage_sections')"
                        :href="route('sections.index')"
                        :isCollapsed="!isOpen"
                    />
                    <SidebarItem
                        :icon="CogIcon"
                        :label="$t('sidebar.manage_rooms')"
                        :href="route('rooms.index')"
                        :isCollapsed="!isOpen"
                    />
                    <SidebarItem
                        :icon="CogIcon"
                        :label="$t('sidebar.manage_weights')"
                        :href="route('weights.index')"
                        :isCollapsed="!isOpen"
                    />
                </SidebarDropdownMenu>

                <SidebarDropdownMenu
                        :label="$t('sidebar.user_management')"
                    :icon="UserIcon"
                    :sidebar-visible="isOpen"
                >
                    <SidebarItem
                        :icon="CogIcon"
                        :label="$t('sidebar.manage_instructors')"
                        :href="route('instructors.index')"
                        :isCollapsed="!isOpen"
                    />
                    <SidebarItem
                        :icon="BriefcaseIcon"
                        :label="$t('sidebar.manage_employees')"
                        :href="route('employees.index')"
                        :isCollapsed="!isOpen"
                    />
                    <SidebarItem
                        :icon="BriefcaseIcon"
                        :label="$t('sidebar.manage_users')"
                        :href="route('users.index')"
                        :isCollapsed="!isOpen"
                    />
                </SidebarDropdownMenu>

                <SidebarDropdownMenu
                        :label="$t('sidebar.student_management')"
                    :icon="UsersIcon"
                    :sidebar-visible="isOpen"
                >
                    <SidebarItem
                        :icon="UsersIcon"
                        :label="$t('sidebar.all_students')"
                        :href="route('students.index')"
                        :isCollapsed="!isOpen"
                    />
                    <SidebarItem
                        :icon="PlusIcon"
                        :label="$t('sidebar.add_student')"
                        :href="route('students.create')"
                        :isCollapsed="!isOpen"
                    />
                    <SidebarItem
                        :icon="CogIcon"
                        :label="$t('sidebar.manage_students')"
                        :href="route('userDocuments.index')"
                        :isCollapsed="!isOpen"
                    />
                </SidebarDropdownMenu>

                <SidebarDropdownMenu
                        :label="$t('sidebar.center_management')"
                    :icon="BuildingOffice2Icon"
                    :sidebar-visible="isOpen"
                >
                    <SidebarItem
                        :icon="BuildingOffice2Icon"
                        :label="$t('sidebar.all_centers')"
                        :href="route('centers.index')"
                        :isCollapsed="!isOpen"
                    />
                </SidebarDropdownMenu>

                <SidebarDropdownMenu
                        :label="$t('sidebar.inventory_management')"
                    :icon="ArchiveBoxArrowDownIcon"
                    :sidebar-visible="isOpen"
                >
                    <SidebarItem
                        :icon="HandRaisedIcon"
                        :label="$t('sidebar.inventories')"
                        :href="route('inventories.index')"
                        :isCollapsed="!isOpen"
                    />
                    <!-- <SidebarItem
                        :icon="HandRaisedIcon"
                        :label="$t('sidebar.inventory_items')"
                        :href="route('inventorySuppliers.index')"
                        :isCollapsed="!isOpen"
                    /> -->
                    <SidebarItem
                        :icon="FolderIcon"
                        :label="$t('sidebar.inventory_categories')"
                        :href="route('inventoryCategories.index')"
                        :isCollapsed="!isOpen"
                    />
                </SidebarDropdownMenu>

                <SidebarDropdownMenu
                        :label="$t('sidebar.payment_management')"
                    :icon="CurrencyDollarIcon"
                    :sidebar-visible="isOpen"
                >
                    <SidebarItem
                        :icon="BanknotesIcon"
                        :label="$t('sidebar.payments')"
                        :href="route('payments.index')"
                        :isCollapsed="!isOpen"
                    />
                </SidebarDropdownMenu>
            </div>

            <div class="px-2 pb-4 mt-auto">
                <SidebarItem
                    :icon="CogIcon"
                        :label="$t('sidebar.profile')"
                    :href="route('profile.edit')"
                    :isCollapsed="!isOpen"
                />
                <SidebarItem
                    :icon="QuestionMarkCircleIcon"
                        :label="$t('sidebar.help')"
                    href="#"
                    :isCollapsed="!isOpen"
                />
            </div>
        </div>
    </aside>
</template>
