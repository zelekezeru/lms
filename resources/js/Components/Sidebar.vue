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
    UserIcon,
    BuildingOffice2Icon,
    ArrowLeftCircleIcon,
    ArchiveBoxArrowDownIcon,
    HandRaisedIcon,
    FolderIcon,
    ClockIcon,
    LinkIcon,
    EyeIcon,
    CalendarIcon,
    ChartPieIcon,
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
    tracksMenu: false,
    programsMenu: false,
    employeesMenu: false,
    studentsMenu: false,
    coursesMenu: false,
    classesMenu: false,
    teachersMenu: false,
    attendanceMenu: false,
    examsMenu: false,
    permissionsMenu: false,
    tenantsMenu: false,
    inventoryMenu: false,
    rolesMenu: false,
    semestersMenu: false,
    yearsMenu: false,
    studyModesMenu: false,
    sectionsMenu: false,
    userDocumentsMenu: false,
    courseStudentsMenu: false,
    courseInstructorsMenu: false,
    sectionStudentsMenu: false,
    sectionInstructorsMenu: false,
    sectionCoursesMenu: false,
});

const logout = () => {
    router.flushAll;
    router.post(route("logout"));
};
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
            <a href="/">
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
                            SITS LMS
                        </h1>
                    </transition>
                </div>
            </a>
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
                    v-show="userCanAny(['view-tenants'])"
                >
                    <SidebarDrowpdownLink
                        v-show="userCan('view-tenants')"
                        :href="route('tenants.index')"
                    >
                        <CogIcon class="w-4 h-5 mr-2 text-gray-200" />
                        <span class="text-sm">Manage Tenant</span>
                    </SidebarDrowpdownLink>
                </SidebarDropdownMenu>

                <!-- Permission Navigation -->
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

                <!-- Time Frame Structure Navigation -->
                <SidebarDropdownMenu
                    :label="'Schedule Structure'"
                    :icon="ClockIcon"
                    :sidebar-hovered="sidebarHovered"
                    :sidebar-visible="sidebarVisible"
                    v-show="userCanAny(['view-years', 'view-semesters'])"
                >
                    <SidebarDrowpdownLink
                        v-show="userCan('view-years')"
                        :href="route('years.index')"
                    >
                        <CalendarIcon class="w-4 h-5 mr-2 text-gray-200" />
                        <span class="text-sm">Manage Years</span>
                    </SidebarDrowpdownLink>

                    <SidebarDrowpdownLink
                        v-show="userCan('view-semesters')"
                        :href="route('semesters.index')"
                    >
                        <ChartPieIcon class="w-4 h-5 mr-2 text-gray-200" />
                        <span class="text-sm">Manage Semesters</span>
                    </SidebarDrowpdownLink>
                </SidebarDropdownMenu>

                <!-- Academic Structure Navigation -->
                <!-- Academic Structure Navigation -->
                <SidebarDropdownMenu
                    :label="'Academic Structure'"
                    :icon="AcademicCapIcon"
                    :sidebar-hovered="sidebarHovered"
                    :sidebar-visible="sidebarVisible"
                    v-show="
                        userCanAny([
                            'view-programs',
                            'view-tracks',
                            'view-studyModes',
                            'view-courses',
                            'view-sections',
                        ])
                    "
                >
                    <SidebarDrowpdownLink
                        v-show="userCan('view-programs')"
                        :href="route('programs.index')"
                    >
                        <CogIcon class="w-4 h-5 mr-2 text-gray-200" />
                        <span class="text-sm">Manage Program</span>
                    </SidebarDrowpdownLink>

                    <SidebarDrowpdownLink
                        v-show="userCan('view-tracks')"
                        :href="route('tracks.index')"
                    >
                        <BuildingOffice2Icon
                            class="w-4 h-5 mr-2 text-gray-200"
                        />
                        <span class="text-sm">Manage Track</span>
                    </SidebarDrowpdownLink>

                    <SidebarDrowpdownLink
                        v-show="userCan('view-studyModes')"
                        :href="route('studyModes.index')"
                    >
                        <ClockIcon class="w-4 h-5 mr-2 text-gray-200" />
                        <span class="text-sm">Manage Study Modes</span>
                    </SidebarDrowpdownLink>

                    <SidebarDrowpdownLink
                        v-show="userCan('view-courses')"
                        :href="route('courses.index')"
                    >
                        <BriefcaseIcon class="w-4 h-5 mr-2 text-gray-200" />
                        <span class="text-sm">Manage Course</span>
                    </SidebarDrowpdownLink>
                </SidebarDropdownMenu>

                <!-- Sections Navigation -->
                <SidebarDropdownMenu
                    :label="'Section Classes'"
                    :icon="AcademicCapIcon"
                    :sidebar-hovered="sidebarHovered"
                    :sidebar-visible="sidebarVisible"
                    v-show="userCanAny(['view-sections'])"
                >
                    <SidebarDrowpdownLink
                        v-show="userCan('view-sections')"
                        :href="route('sections.index')"
                    >
                        <CogIcon class="w-4 h-5 mr-2 text-gray-200" />
                        <span class="text-sm">Manage Sections</span>
                    </SidebarDrowpdownLink>

                    <SidebarDrowpdownLink
                        v-show="userCan('view-results')"
                        :href="route('results.index')"
                    >
                        <CogIcon class="w-4 h-5 mr-2 text-gray-200" />
                        <span class="text-sm">Manage Results</span>
                    </SidebarDrowpdownLink>

                    <SidebarDrowpdownLink
                        v-show="userCan('view-weights')"
                        :href="route('weights.index')"
                    >
                        <CogIcon class="w-4 h-5 mr-2 text-gray-200" />
                        <span class="text-sm">Manage Weight</span>
                    </SidebarDrowpdownLink>
                </SidebarDropdownMenu>

                <!-- User Managment-->

                <SidebarDropdownMenu
                    :label="'User Management'"
                    :icon="UserIcon"
                    :sidebar-hovered="sidebarHovered"
                    :sidebar-visible="sidebarVisible"
                    v-show="userCanAny(['view-instructors', 'view-employees'])"
                >
                    <SidebarDrowpdownLink
                        v-show="userCan('view-instructors')"
                        :href="route('instructors.index')"
                    >
                        <CogIcon class="w-4 h-5 mr-2 text-gray-200" />
                        <span class="text-sm">Manage Instructors</span>
                    </SidebarDrowpdownLink>
                    <SidebarDrowpdownLink
                        v-show="userCan('view-employees')"
                        :href="route('employees.index')"
                    >
                        <BriefcaseIcon class="w-4 h-5 mr-2 text-gray-200" />
                        <span class="text-sm">Manage Employee</span>
                    </SidebarDrowpdownLink>
                    <SidebarDrowpdownLink
                        v-show="userCan('view-users')"
                        :href="route('users.index')"
                    >
                        <BriefcaseIcon class="w-4 h-5 mr-2 text-gray-200" />
                        <span class="text-sm">Manage User</span>
                    </SidebarDrowpdownLink>
                </SidebarDropdownMenu>

                <!-- Students Managmant -->
                <SidebarDropdownMenu
                    :label="'Students Management'"
                    :icon="UsersIcon"
                    class="text-nowrap"
                    :sidebar-hovered="sidebarHovered"
                    :sidebar-visible="sidebarVisible"
                    v-show="userCanAny(['view-students'])"
                >
                    <SidebarDrowpdownLink
                        v-show="userCan('view-students')"
                        :href="route('students.index')"
                    >
                        <UsersIcon class="w-4 h-5 mr-2 text-gray-200" />
                        <span class="text-sm">Manage Student</span>
                    </SidebarDrowpdownLink>

                    <SidebarDrowpdownLink
                        v-show="userCan('create-students')"
                        :href="route('students.create')"
                    >
                        <PlusIcon class="w-4 h-5 mr-2 text-gray-200" />
                        <span class="text-sm">Register Student</span>
                    </SidebarDrowpdownLink>

                    <SidebarDrowpdownLink
                        v-show="userCan('view-userDocuments')"
                        :href="route('userDocuments.index')"
                    >
                        <CogIcon class="w-4 h-5 mr-2 text-gray-200" />
                        <span class="text-sm">Manage User Document</span>
                    </SidebarDrowpdownLink>
                </SidebarDropdownMenu>

                <!-- Inventory -->

                <SidebarDropdownMenu
                    :label="'Inventory'"
                    :icon="ArchiveBoxArrowDownIcon"
                    :sidebar-hovered="sidebarHovered"
                    :sidebar-visible="sidebarVisible"
                    v-show="
                        userCanAny([
                            'view-inventory-suppliers',
                            'view-inventory-categories',
                        ])
                    "
                >
                    <SidebarDrowpdownLink
                        v-show="userCan('view-inventories')"
                        :href="route('inventories.index')"
                    >
                        <HandRaisedIcon class="w-4 h-5 mr-2 text-gray-200" />
                        <span class="text-sm">All Inventories</span>
                    </SidebarDrowpdownLink>
                    <SidebarDrowpdownLink
                        v-show="userCan('view-inventory-suppliers')"
                        :href="route('inventorySuppliers.index')"
                    >
                        <HandRaisedIcon class="w-4 h-5 mr-2 text-gray-200" />
                        <span class="text-sm">Inventory Suppliers</span>
                    </SidebarDrowpdownLink>
                    <SidebarDrowpdownLink
                        v-show="userCan('view-inventory-categories')"
                        :href="route('inventoryCategories.index')"
                    >
                        <FolderIcon class="w-4 h-5 mr-2 text-gray-200" />
                        <span class="text-sm">Inventory Categories</span>
                    </SidebarDrowpdownLink>
                </SidebarDropdownMenu>

                <!-- Profile Navigation Item -->
                <Link
                    :href="route('profile.edit')"
                    class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-700"
                >
                    <UsersIcon class="w-8 text-gray-200 p-1 rounded-full" />
                    <transition name="fade">
                        <span
                            v-if="sidebarVisible || sidebarHovered"
                            class="transition-all duration-300 truncate text-sm"
                        >
                            My Profile
                        </span>
                    </transition>
                </Link>

                <!-- Logout Navigation Item -->
                <a
                    href="#"
                    @click.prevent="logout"
                    class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-700 cursor-pointer"
                >
                    <ArrowLeftCircleIcon
                        class="w-8 text-red-400 p-1 rounded-full"
                    />
                    <transition name="fade">
                        <span
                            v-if="sidebarVisible || sidebarHovered"
                            class="transition-all duration-300 truncate text-sm text-red-400"
                        >
                            Logout
                        </span>
                    </transition>
                </a>
            </div>
        </nav>
    </aside>
</template>
