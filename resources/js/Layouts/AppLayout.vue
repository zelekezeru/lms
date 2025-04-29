<script setup>
import { ref, onMounted, onUnmounted, computed } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import { useDark } from "@vueuse/core";
import { router } from "@inertiajs/vue3";
import {
    Bars3Icon,
    MoonIcon,
    SunIcon,
    ArrowLeftIcon,
    ArrowRightIcon,
} from "@heroicons/vue/24/outline";
import Sidebar from "@/Components/Sidebar.vue";

// Auth User
const user = computed(() => usePage().props.auth.user);

// Sidebar state
const isMobile = ref(window.innerWidth < 768);
const sidebarVisible = ref(!isMobile.value);
const sidebarHovered = ref(false);

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
    isMobile.value = window.innerWidth < 768;
    sidebarVisible.value = !isMobile.value;
};

// Add event listeners
onMounted(() => {
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

// Dropdown visibility state
const dropdownVisible = ref(false);

// Toggle dropdown visibility
const toggleDropdown = () => {
    dropdownVisible.value = !dropdownVisible.value;
};

// Close dropdown on outside click
const closeDropdown = () => {
    dropdownVisible.value = false;
};

// Logout function
const logout = () => {
    router.flushAll;
    router.post(route("logout"));
};
</script>

<template>
    <div
        class="flex h-screen bg-gray-100 dark:bg-gray-900 relative transition-colors duration-900"
    >
        <Sidebar
            :sidebarHovered="sidebarHovered"
            :sidebarVisible="sidebarVisible"
            :isMobile="isMobile"
            :handle-aside-hover="handleAsideHover"
        />

        <!-- Mobile Sidebar Overlay -->
        <div
            v-if="isMobile && sidebarVisible"
            class="fixed inset-0 bg-black bg-opacity-50 z-40"
            @click="sidebarVisible = false"
        ></div>

        <!-- Main Content -->
        <div
            class="flex-1 flex flex-col transition-margin duration-300 overflow-x-hidden"
            :class="{
                'ml-64': !isMobile && sidebarVisible,
                'ml-20': !isMobile && !sidebarVisible,
            }"
        >
            <!-- Navbar -->
            <nav
                class="border-b border-gray-300 dark:border-gray-700 px-4 py-4 flex justify-between items-center bg-gray-800 text-gray-200"
            >
                <div class="flex items-center gap-3">
                    <!-- Sidebar Toggler -->
                    <button @click="sidebarVisible = !sidebarVisible">
                        <Bars3Icon class="w-6 h-6 text-gray-200" />
                    </button>
                    <!-- Back Button -->
                    <button
                        @click="goBack"
                        class="p-2 hover:bg-gray-700 rounded-md"
                    >
                        <ArrowLeftIcon class="w-6 h-6 text-gray-200" />
                    </button>

                    <!-- Forward Button -->
                    <button
                        @click="goForward"
                        class="p-2 hover:bg-gray-700 rounded-md"
                    >
                        <ArrowRightIcon class="w-6 h-6 text-gray-200" />
                    </button>
                </div>

                <!-- Other Navbar Content -->
                <div class="flex items-center gap-4 ml-auto">
                    <!-- User Info -->
                    <div
                        class="relative nav-item topbar-user dropdown hidden-caret"
                    >
                        <!-- Dropdown Toggle -->
                        <button
                            class="dropdown-toggle profile-pic flex items-center gap-2 focus:outline-none"
                            @click="toggleDropdown"
                            aria-expanded="dropdownVisible"
                        >
                            <!-- Avatar Image -->
                            <div class="avatar-sm">
                                <img
                                    :src="user.value.profileImg ? user.value.profileImg : '/img/profile.jpg'"
                                    alt="Profile Image"
                                    class="avatar-img rounded-circle w-10 h-10"
                                />
                            </div>

                            <!-- User Name (hidden on mobile screens) -->
                            <span
                                class="profile-username text-sm hidden md:block"
                            >
                                <span class="fw-bold font-semibold">{{
                                    user.name
                                }}</span>
                            </span>
                        </button>

                        <!-- Dropdown Menu -->
                        <ul
                            v-show="dropdownVisible"
                            @click.outside="closeDropdown"
                            class="dropdown-menu dropdown-user animated fadeIn absolute right-0 mt-2 w-64 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 shadow-lg rounded-md z-50"
                        >
                            <div
                                class="dropdown-user-scroll scrollbar-outer p-4"
                            >
                                <!-- User Box -->
                                <li class="user-box flex items-center gap-4">
                                    <div class="avatar-lg">
                                        <img
                                            :src="
                                                user.profileImg
                                                    ? user.profileImg
                                                    : '/img/profile.jpg'
                                            "
                                            alt="Profile Image"
                                            class="avatar-img rounded-full w-16 h-16"
                                        />
                                    </div>
                                    <div class="u-text">
                                        <h4
                                            class="font-bold text-gray-900 dark:text-gray-100"
                                        >
                                            {{ user.name }}
                                        </h4>
                                        <p
                                            class="text-sm text-gray-500 dark:text-gray-400"
                                        >
                                            {{ user.email }}
                                        </p>
                                        <Link
                                            :href="route('profile.edit')"
                                            class="btn btn-xs btn-secondary btn-sm mt-2 inline-block px-3 py-1 bg-gray-800 text-white rounded-md hover:bg-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600"
                                        >
                                            View Profile
                                    </Link>
                                    </div>
                                </li>

                                <!-- Dropdown Links -->
                                <li>
                                    <div
                                        class="dropdown-divider border-t my-2"
                                    ></div>
                                    <Link
                                        class="dropdown-item block px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
                                        :href="route('profile.edit')"
                                    >
                                        My Profile
                                </Link>
                                    <Link
                                        class="dropdown-item block px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
                                        href="#"
                                    >
                                        My Balance
                                </Link>
                                    <Link
                                        class="dropdown-item block px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
                                        href="#"
                                    >
                                        Inbox
                                </Link>
                                    <div
                                        class="dropdown-divider border-t my-2"
                                    ></div>
                                    <Link
                                        class="dropdown-item block px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
                                        href="#"
                                    >
                                        Account Settings
                                </Link>
                                    <div
                                        class="dropdown-divider border-t my-2"
                                    ></div>
                                    <div>
                                        <button
                                            @click="logout"
                                            type="submit"
                                            class="dropdown-item block w-full text-left px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
                                        >
                                            Log Out
                                        </button>
                                    </div>
                                </li>
                            </div>
                        </ul>
                    </div>

                    <!-- Theme Toggle Button -->
                    <button @click="toggleTheme" class="p-2">
                        <MoonIcon
                            v-if="!isDarkMode"
                            class="w-6 h-6 text-gray-200"
                        />
                        <SunIcon v-else class="w-6 h-6 text-gray-200" />
                    </button>
                </div>
            </nav>

            <!-- Page Content -->
            <main class="flex-1 p-6 bg-gray-100 dark:bg-gray-800">
                <slot />
            </main>
        </div>
    </div>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.1s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
.fade-enter-to,
.fade-leave-from {
    opacity: 1;
}
</style>
