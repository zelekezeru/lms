<script setup>
import { ref } from "vue";
import { Link, router } from "@inertiajs/vue3";
import {
    HomeIcon,
    XMarkIcon,
    PlusIcon,
    CogIcon,
    UsersIcon,
    BriefcaseIcon,
    KeyIcon,
    AcademicCapIcon,
    BuildingOffice2Icon,
    ArrowLeftCircleIcon,
    ArchiveBoxArrowDownIcon,
    HandRaisedIcon,
} from "@heroicons/vue/24/outline";
import SidebarDropdownMenu from "./SidebarDropdownMenu.vue";
import SidebarDrowpdownLink from "./SidebarDrowpdownLink.vue";
import PrimaryButton from "../Components/PrimaryButton.vue";

const props = defineProps({
    sidebarVisible: {
        type: Boolean,
        required: true,
    },
    sidebarHovered: {
        type: Boolean,
        required: true,
    },
    isMobile: {
        type: Boolean,
        required: true,
    },
    handleAsideHover: {
        type: Function,
        required: true,
    },
});

const openMenus = ref({
    userPages: false,
    departmentsMenu: false,
    programsMenu: false,
    employeesMenu: false,
    studentsMenu: false,
    coursesMenu: false,
    teachersMenu: false,
    attendanceMenu: false,
    examsMenu: false,
    permissionsMenu: false,
    tenantsMenu: false,
});

// Custom Transition Hooks for Smooth Height Animation
const beforeEnter = (el) => {
    el.style.height = "0";
    el.style.opacity = "0";
};

const enter = (el) => {
    el.style.transition = "all 0.1s ease";
    el.style.height = el.scrollHeight + "px";
    el.style.opacity = "1";
};

const afterEnter = (el) => {
    el.style.height = "auto";
};

const beforeLeave = (el) => {
    el.style.height = el.scrollHeight + "px";
    el.style.opacity = "1";
};

const leave = (el) => {
    el.style.transition = "all 0.1s ease";
    // Force reflow to ensure the transition occurs
    void el.offsetHeight;
    el.style.height = "0";
    el.style.opacity = "0";
};

const afterLeave = (el) => {
    el.style.height = "";
};
</script>

<template>
    <!-- Sidebar -->
    <aside
        :class="{
            '-translate-x-full': !sidebarVisible && isMobile,
            'translate-x-0': sidebarVisible && isMobile,
            'w-64 fixed top-0 bottom-0':
                sidebarVisible || sidebarHovered || isMobile,
            'w-20 fixed top-0 bottom-0': !sidebarVisible && !isMobile,
            'fixed inset-y-0 left-0 transform z-50 transition-all duration-300 ease-in-out':
                isMobile,
        }"
        @mouseenter="handleAsideHover"
        @mouseleave="handleAsideHover"
        class="transition-width duration-300 ease-in-out flex flex-col text-sm bg-gray-800 dark:bg-gray-900 text-gray-300"
    >
        <div
            class="h-[70px] flex"
            :class="{
                'justify-between': isMobile,
                'justify-center': !isMobile,
            }"
        >
            <div class="flex gap-4 items-center justify-center h-full">
                <img
                    src="/img/logo.png"
                    class="w-[48px] rounded-full"
                    alt="Logo"
                />
                <transition name="fade">
                    <h1
                        v-if="sidebarVisible || sidebarHovered"
                        class="text-xl font-bold tracking-wide truncate"
                        style="font-family: transity"
                    >
                        SITS
                    </h1>
                </transition>
            </div>
            <button v-if="isMobile" @click="sidebarVisible = false">
                <XMarkIcon
                    class="w-8 h-8 text-gray-200 hover:text-red-500 transition"
                />
            </button>
        </div>

        <nav class="pt-1 text-sm font-medium">
            <Link
                href="/"
                class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-gray-700"
            >
                <HomeIcon class="w-8 h-8 p-1 rounded-full" />
                <transition name="fade">
                    <span
                        v-if="sidebarVisible || sidebarHovered"
                        class="transition-all duration-300 truncate"
                    >
                        Dashboard
                    </span>
                </transition>
            </Link>

            <div
                class="h-[500px] overflow-y-auto py-2 scrollbar scrollbar-thin scrollbar-thumb-gray-500 scrollbar-track-gray-200"
            >
                <h2 class="font-bold px-4 text-sm">Menu</h2>
                
                <!-- Tenant Navigation -->
                <SidebarDropdownMenu
                    :label="'Tenants'"
                    :icon="AcademicCapIcon"
                    :sidebar-hovered="sidebarHovered"
                    :sidebar-visible="sidebarVisible"
                    v-show="userCanAny(['view-tenants', 'create-tenants'])"
                >
                    <SidebarDrowpdownLink v-show="userCan('create-tenants')" :href="route('tenants.create')">
                        <PlusIcon class="w-4 h-5 mr-2 text-gray-200" />
                        <span class="text-sm">Add Tenant</span>
                    </SidebarDrowpdownLink>
                    <SidebarDrowpdownLink v-show="userCan('view-tenants')" :href="route('tenants.index')">
                        <CogIcon class="w-4 h-5 mr-2 text-gray-200" />
                        <span class="text-sm">Manage Tenant</span>
                    </SidebarDrowpdownLink>
                </SidebarDropdownMenu>

                <!-- Employees Navigation -->
                <SidebarDropdownMenu
                    :label="'Employees'"
                    :icon="BriefcaseIcon"
                    :sidebar-hovered="sidebarHovered"
                    :sidebar-visible="sidebarVisible"
                    v-show="userCanAny(['view-employees', 'create-employees'])"
                >
                    <SidebarDrowpdownLink
                        v-show="userCan('create-employees')"
                        :href="route('employees.create')"
                    >
                        <PlusIcon class="w-4 h-5 mr-2 text-gray-200" />
                        <span class="text-sm">Add Employee</span>
                    </SidebarDrowpdownLink>
                    <SidebarDrowpdownLink
                        v-show="userCan('view-employees')"
                        :href="route('employees.index')"
                    >
                        <CogIcon class="w-4 h-5 mr-2 text-gray-200" />
                        <span class="text-sm">Manage Employee</span>
                    </SidebarDrowpdownLink>
                </SidebarDropdownMenu>

                <!-- Students Navigation -->
                <SidebarDropdownMenu
                    :label="'Students'"
                    :icon="UsersIcon"
                    :sidebar-hovered="sidebarHovered"
                    :sidebar-visible="sidebarVisible"
                    v-show="userCanAny(['view-students', 'create-students'])"
                >
                    <SidebarDrowpdownLink
                        v-show="userCan('create-students')"
                        :href="route('students.create')"
                    >
                        <PlusIcon class="w-4 h-5 mr-2 text-gray-200" />
                        <span class="text-sm">Add Student</span>
                    </SidebarDrowpdownLink>
                    <SidebarDrowpdownLink
                        v-show="userCan('view-students')"
                        :href="route('students.index')"
                    >
                        <CogIcon class="w-4 h-5 mr-2 text-gray-200" />
                        <span class="text-sm">Manage Student</span>
                    </SidebarDrowpdownLink>
                </SidebarDropdownMenu>

                <!-- Students Navigation -->
                <SidebarDropdownMenu
                    :label="'Roles And Permissions'"
                    class="text-nowrap"
                    :icon="KeyIcon"
                    :sidebar-hovered="sidebarHovered"
                    :sidebar-visible="sidebarVisible"
                    v-show="userCanAny(['view-roles', 'view-permissions'])"
                >
                    <SidebarDrowpdownLink
                        v-show="userCan('view-roles')"
                        :href="route('roles.index')"
                    >
                        <CogIcon class="w-4 h-5 mr-2 text-gray-200" />
                        <span class="text-sm">Manage Roles</span>
                    </SidebarDrowpdownLink>
                    <SidebarDrowpdownLink
                        v-show="userCan('view-permissions')"
                        :href="route('permissions.index')"
                    >
                        <CogIcon class="w-4 h-5 mr-2 text-gray-200" />
                        <span class="text-sm">Manage Permissions</span>
                    </SidebarDrowpdownLink>
                </SidebarDropdownMenu>

                <!-- Departments Navigation -->
                <SidebarDropdownMenu
                    :label="'Departments'"
                    :icon="BuildingOffice2Icon"
                    :sidebar-hovered="sidebarHovered"
                    :sidebar-visible="sidebarVisible"
                    v-show="userCanAny(['view-employees', 'create-employees'])"
                >
                    <SidebarDrowpdownLink
                        v-show="userCan('create-departments')"
                        :href="route('departments.create')"
                    >
                        <PlusIcon class="w-4 h-5 mr-2 text-gray-200" />
                        <span class="text-sm">Add Department</span>
                    </SidebarDrowpdownLink>
                    <SidebarDrowpdownLink
                        v-show="userCan('view-departments')"
                        :href="route('departments.index')"
                    >
                        <CogIcon class="w-4 h-5 mr-2 text-gray-200" />
                        <span class="text-sm">Manage Department</span>
                    </SidebarDrowpdownLink>
                </SidebarDropdownMenu>

                <!-- Programs Navigation -->
                <SidebarDropdownMenu
                    :label="'Programs'"
                    :icon="AcademicCapIcon"
                    :sidebar-hovered="sidebarHovered"
                    :sidebar-visible="sidebarVisible"
                    v-show="userCanAny(['view-programs', 'create-programs'])"
                >
                    <SidebarDrowpdownLink
                        v-show="userCan('create-programs')"
                        :href="route('programs.create')"
                    >
                        <PlusIcon class="w-4 h-5 mr-2 text-gray-200" />
                        <span class="text-sm">Add Program</span>
                    </SidebarDrowpdownLink>
                    <SidebarDrowpdownLink
                        v-show="userCan('view-programs')"
                        :href="route('programs.index')"
                    >
                        <CogIcon class="w-4 h-5 mr-2 text-gray-200" />
                        <span class="text-sm">Manage Program</span>
                    </SidebarDrowpdownLink>
                </SidebarDropdownMenu>

                <SidebarDropdownMenu
                    :label="'Inventory'"
                    :icon="ArchiveBoxArrowDownIcon"
                    :sidebar-hovered="sidebarHovered"
                    :sidebar-visible="sidebarVisible"
                    v-show="userCanAny(['view-inventory-suppliers'])"
                >
                    <SidebarDrowpdownLink
                        v-show="userCan('view-inventory-suppliers')"
                        :href="route('inventorySuppliers.index')"
                    >
                        <HandRaisedIcon class="w-4 h-5 mr-2 text-gray-200" />
                        <span class="text-sm">Inventory Suppliers</span>
                    </SidebarDrowpdownLink>
                </SidebarDropdownMenu>

                <!-- Profile Navigation Item -->
                <Link
                    :href="route('profile.edit')"
                    class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-700"
                >
                    <UsersIcon class="w-8 h-8 text-gray-200 p-1 rounded-full" />
                    <transition name="fade">
                        <span
                            v-if="sidebarVisible || sidebarHovered"
                            class="transition-all duration-300 truncate text-sm"
                        >
                            My Profile
                        </span>
                    </transition>
                </Link>
                <!-- Profile Navigation Item -->
                <PrimaryButton
                    @click="router.post(route('logout'))"
                    class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-700"
                >
                    <ArrowLeftCircleIcon class="w-8 h-8 text-gray-200 p-1 rounded-full" />
                    <transition name="fade">
                        <span
                            v-if="sidebarVisible || sidebarHovered"
                            class="transition-all duration-300 truncate text-sm"
                        >
                            Logout
                        </span>
                    </transition>
                </PrimaryButton>
            </div>
        </nav>
    </aside>
</template>
