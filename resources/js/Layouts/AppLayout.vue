<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import { Link } from "@inertiajs/vue3";
import { useDark } from "@vueuse/core";
import {
    HomeIcon,
    TableCellsIcon,
    ChartBarIcon,
    DocumentTextIcon,
    FolderIcon,
    Cog6ToothIcon,
    Bars3Icon,
    BuildingStorefrontIcon,
    XMarkIcon,
    MoonIcon,
    SunIcon,
    ArrowLeftIcon,
    ArrowRightIcon,
    ChevronUpIcon,
    ChevronDownIcon,
    PlusIcon,
    ClipboardDocumentListIcon,
    DocumentIcon,
    UsersIcon,
} from "@heroicons/vue/24/outline";

// Sidebar state
const isMobile = ref(window.innerWidth < 768);
const sidebarVisible = ref(!isMobile.value);
const sidebarHovered = ref(false);
const openMenus = ref({ userPages: false, departmentsMenu: false , studentssMenu: false });

// Dark mode handling
const isDarkMode = useDark();

const toggleTheme = () => {
    isDarkMode.value = !isDarkMode.value;
    if (isDarkMode.value) {
        document.documentElement.classList.add("dark");
    } else {
        document.documentElement.classList.remove("dark");
    }
};

// Navigation functions
const goBack = () => {
    window.history.length > 1
        ? window.history.back()
        : (window.location.href = "/dashboard");
};

const goForward = () => {
    window.history.forward();
};

// Detect screen size changes
const updateScreenSize = () => {
    console.log("updated screen");

    isMobile.value = window.innerWidth < 768;
    sidebarVisible.value = !isMobile.value;
};

// Add event listeners
onMounted(() => {
    console.log("mounted");

    updateScreenSize();
    window.addEventListener("resize", updateScreenSize);
});

const handleAsideHover = (e) => {
    if (sidebarVisible.value) return;
    sidebarHovered.value = e.type === "mouseenter";
};

onUnmounted(() => {
    window.removeEventListener("resize", updateScreenSize);
});
</script>

<template>
    <div
        class="flex h-screen bg-gray-100 dark:bg-gray-900 relative transition-colors duration-900"
    >
        <!-- Sidebar -->
        <aside
            :class="{
                '-translate-x-full': !sidebarVisible && isMobile,
                'translate-x-0': sidebarVisible && isMobile,
                'w-64 fixed top-0 bottom-0': sidebarVisible || sidebarHovered,
                'w-20 fixed top-0 bottom-0': !sidebarVisible && !isMobile,
                'fixed inset-y-0 left-0 transform z-50 transition-all duration-300 ease-in-out':
                    isMobile,
            }"
            @mouseenter="handleAsideHover"
            @mouseleave="handleAsideHover"
            class="transition-width duration-300 ease-in-out flex flex-col bg-[#1a2035] dark:bg-gray-900 text-[#a2a4ab] dark:text-gray-200"
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
                        alt=""
                        srcset=""
                    />
                    <h1
                        v-if="sidebarVisible || sidebarHovered"
                        class="text-xl font-bold tracking-wide"
                        style="font-family: transity"
                    >
                        SITS
                    </h1>
                </div>
                <button v-if="isMobile" @click="sidebarVisible = false">
                    <XMarkIcon
                        class="w-8 h-8 text-[#a2a4ab] dark:text-gray-200 hover:text-red-500 transition"
                    />
                </button>
            </div>

            <nav class="pt-1 font-bold">
                <Link
                    href="/dashboard"
                    class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-[#00000029] dark:hover:bg-gray-700"
                >
                    <HomeIcon class="w-8 h-8 text-[#a2a4ab] p-1 rounded-full" />
                    <span
                        class="transition-all duration-300"
                        v-if="sidebarVisible || sidebarHovered"
                        >Dashboard</span
                    >
                </Link>

                <div class="transition-all duration-300">
                    <h2 class="font-bold px-4">Menu</h2>
                    <button
                        @click="
                            openMenus.departmentsMenu =
                                !openMenus.departmentsMenu
                        "
                        class="w-full flex departments-center justify-between px-4 py-2 hover:bg-[#00000029] dark:hover:bg-gray-700"
                    >
                        <div class="flex departments-center space-x-3">
                            <DocumentIcon class="w-7 p-1" />
                            <span v-if="sidebarVisible || sidebarHovered"
                                >Departments</span
                            >
                        </div>
                        <component
                            :is="
                                openMenus.departmentsMenu
                                    ? ChevronUpIcon
                                    : ChevronDownIcon
                            "
                            class="w-5 h-5"
                        />
                    </button>
                    <transition name="fade">
                        <div
                            v-if="
                                openMenus.departmentsMenu &&
                                (sidebarVisible || sidebarHovered)
                            "
                            class="space-y-2 rounded-md p-2"
                        >
                            <Link
                                :href="route('departments.index')"
                                class="flex departments-center px-4 py-2 hover:bg-[#00000029] dark:hover:bg-gray-700 rounded"
                            >
                                <ClipboardDocumentListIcon
                                    class="w-4 h-5 mr-2"
                                />
                                Manage Departments
                            </Link>
                            <Link
                                :href="route('departments.create')"
                                class="flex departments-center px-4 py-2 hover:bg-[#00000029] dark:hover:bg-gray-700 rounded"
                            >
                                <PlusIcon class="w-4 h-5 mr-2" /> Add
                                Departments
                            </Link>
                        </div>
                    </transition>
                </div>

                <div class="transition-all duration-300">
                    <button
                        @click="
                            openMenus.studentsMenu =
                                !openMenus.studentsMenu
                        "
                        class="w-full flex students-center justify-between px-4 py-2 hover:bg-[#00000029] dark:hover:bg-gray-700"
                    >
                        <div class="flex students-center space-x-3">
                            <DocumentIcon class="w-7 p-1" />
                            <span v-if="sidebarVisible || sidebarHovered"
                                >Students</span
                            >
                        </div>
                        <component
                            :is="
                                openMenus.studentsMenu
                                    ? ChevronUpIcon
                                    : ChevronDownIcon
                            "
                            class="w-5 h-5"
                        />
                    </button>
                    <transition name="fade">
                        <div
                            v-if="
                                openMenus.studentsMenu &&
                                (sidebarVisible || sidebarHovered)
                            "
                            class="space-y-2 rounded-md p-2"
                        >
                            <Link
                                :href="route('students.index')"
                                class="flex students-center px-4 py-2 hover:bg-[#00000029] dark:hover:bg-gray-700 rounded"
                            >
                                <ClipboardDocumentListIcon
                                    class="w-4 h-5 mr-2"
                                />
                                Manage Students
                            </Link>
                            <Link
                                :href="route('students.create')"
                                class="flex students-center px-4 py-2 hover:bg-[#00000029] dark:hover:bg-gray-700 rounded"
                            >
                                <PlusIcon class="w-4 h-5 mr-2" /> Add
                                Students
                            </Link>
                        </div>
                    </transition>
                </div>
            </nav>
        </aside>

        <!-- Mobile Sidebar Overlay -->
        <div
            v-if="isMobile && sidebarVisible"
            class="fixed inset-0 bg-black bg-opacity-50 z-40"
            @click="sidebarVisible = false"
        ></div>

        <!-- Main Content -->
        <div
            class="flex-1 flex flex-col transition-margin duration-300"
            :class="{
                'ml-64': !isMobile && sidebarVisible,
                'ml-20': !isMobile && !sidebarVisible,
            }"
        >
            <!-- Navbar -->
            <nav
                class="border-b border-gray-300 dark:border-gray-700 px-4 py-4 flex justify-between items-center bg-[#1a2035] text-[#a2a4ab] dark:bg-gray-800"
            >
                <div class="flex items-center gap-3">
                    <!-- Back Button -->
                    <button
                        @click="goBack"
                        class="p-2 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-md"
                    >
                        <ArrowLeftIcon
                            class="w-6 h-6 text-[#a2a4ab] dark:text-gray-200"
                        />
                    </button>

                    <!-- Forward Button -->
                    <button
                        @click="goForward"
                        class="p-2 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-md"
                    >
                        <ArrowRightIcon
                            class="w-6 h-6 text-[#a2a4ab] dark:text-gray-200"
                        />
                    </button>

                    <!-- Sidebar Toggler -->
                    <button @click="sidebarVisible = !sidebarVisible">
                        <Bars3Icon
                            class="w-6 h-6 text-[#a2a4ab] dark:text-gray-200"
                        />
                    </button>
                </div>

                <!-- Other Navbar Content -->
                <div class="flex items-center gap-4 ml-auto">
                    <!-- User Info -->
                    <div
                        class="text-sm font-medium text-[#a2a4ab] dark:text-gray-200 flex items-center gap-2"
                    >
                        <img
                            src="images/mlane.jpg"
                            alt="Avatar"
                            class="w-10 h-10 rounded-full"
                        />
                        <div>
                            <div class="text-base">
                                {{ $page.props.auth.user.name }}
                            </div>
                            <div class="text-xs">
                                {{ $page.props.auth.user.email }}
                            </div>
                        </div>
                    </div>
                    <!-- Theme Toggle Button -->
                    <button @click="toggleTheme" class="p-2">
                        <MoonIcon
                            v-if="!isDarkMode"
                            class="w-6 h-6 text-gray-600"
                        />
                        <SunIcon v-else class="w-6 h-6 text-gray-200" />
                    </button>
                </div>
            </nav>

            <!-- Page Content -->
            <main class="flex-1 p-6 bg-gray dark:bg-[#1a2035]">
                <slot />
            </main>
        </div>
    </div>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
