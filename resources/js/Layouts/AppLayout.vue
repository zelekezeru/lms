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
import LanguageToggle from '@/Components/LanguageToggle.vue';

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
const toggleDropdown = () => {
    dropdownVisible.value = !dropdownVisible.value;
};
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
        class="relative flex h-screen transition-colors bg-gray-100 dark:bg-gray-900 duration-900"
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
            class="fixed inset-0 z-40 bg-gray-800 bg-opacity-50 dark:bg-gray-800"
            @click="sidebarVisible = false"
        ></div>

        <!-- Main Content -->
        <div
            class="flex flex-col flex-1 overflow-x-hidden duration-300 transition-margin"
            :class="{
                'ml-64': !isMobile && sidebarVisible,
                'ml-20': !isMobile && !sidebarVisible,
            }"
        >
            <!-- Fixed Navbar -->
            <nav
                class="fixed top-0 right-0 z-50 flex items-center justify-between px-4 py-4 text-gray-200 transition-all duration-300 bg-gray-800 border-b border-gray-300 dark:border-gray-700"
                :class="{
                    'left-64': !isMobile && sidebarVisible,
                    'left-20': !isMobile && !sidebarVisible,
                    'left-0': isMobile,
                }"
            >
                <div class="flex items-center gap-3">
                    <button @click="sidebarVisible = !sidebarVisible">
                        <Bars3Icon class="w-6 h-6 text-gray-200" />
                    </button>
                    <button
                        @click="goBack"
                        class="p-2 rounded-md hover:bg-gray-700"
                    >
                        <ArrowLeftIcon class="w-6 h-6 text-gray-200" />
                    </button>
                    <button
                        @click="goForward"
                        class="p-2 rounded-md hover:bg-gray-700"
                    >
                        <ArrowRightIcon class="w-6 h-6 text-gray-200" />
                    </button>
                </div>

                <!-- Right-side nav -->
                <div class="flex items-center gap-4 ml-auto">
                    <!-- User Dropdown -->
                    <div
                        class="relative nav-item topbar-user dropdown hidden-caret"
                    >
                        <button
                            class="flex items-center gap-2 dropdown-toggle profile-pic focus:outline-none"
                            @click="toggleDropdown"
                            aria-expanded="dropdownVisible"
                        >
                            <div class="avatar-sm">
                                <img
                                    :src="
                                        user.profileImg
                                            ? user.profileImg
                                            : '/img/profile.jpg'
                                    "
                                    alt="Profile Image"
                                    class="w-10 h-10 avatar-img rounded-circle"
                                />
                            </div>
                            <span
                                class="hidden text-sm profile-username md:block"
                            >
                                <span class="font-semibold fw-bold">{{
                                    user.name
                                }}</span>
                            </span>
                        </button>

                        <ul
                            v-show="dropdownVisible"
                            @click.outside="closeDropdown"
                            class="absolute right-0 z-50 w-64 mt-2 bg-white border border-gray-300 rounded-md shadow-lg dropdown-menu dropdown-user animated fadeIn dark:bg-gray-800 dark:border-gray-700"
                        >
                            <div
                                class="p-4 dropdown-user-scroll scrollbar-outer"
                            >
                                <li class="flex items-center gap-4 user-box">
                                    <div class="avatar-lg">
                                        <img
                                            :src="
                                                user.profileImg
                                                    ? user.profileImg
                                                    : '/img/profile.jpg'
                                            "
                                            alt="Profile Image"
                                            class="w-16 h-16 rounded-full avatar-img"
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
                                            class="inline-block px-3 py-1 mt-2 text-white bg-gray-800 rounded-md btn btn-xs btn-secondary btn-sm hover:bg-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600"
                                        >
                                            View Profile
                                        </Link>
                                    </div>
                                </li>

                                <li>
                                    <div
                                        class="my-2 border-t dropdown-divider"
                                    ></div>
                                    <Link
                                        class="block px-4 py-2 text-gray-700 dropdown-item dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
                                        :href="route('profile.edit')"
                                    >
                                        My Profile
                                    </Link>
                                    <Link
                                        class="block px-4 py-2 text-gray-700 dropdown-item dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
                                        href="#"
                                    >
                                        My Balance
                                    </Link>
                                    <Link
                                        class="block px-4 py-2 text-gray-700 dropdown-item dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
                                        href="#"
                                    >
                                        Inbox
                                    </Link>
                                    <div
                                        class="my-2 border-t dropdown-divider"
                                    ></div>
                                    <Link
                                        class="block px-4 py-2 text-gray-700 dropdown-item dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
                                        href="#"
                                    >
                                        Account Settings
                                    </Link>
                                    <div
                                        class="my-2 border-t dropdown-divider"
                                    ></div>
                                    <div>
                                        <button
                                            @click="logout"
                                            type="submit"
                                            class="block w-full px-4 py-2 text-left text-gray-700 dropdown-item dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
                                        >
                                            Log Out
                                        </button>
                                    </div>
                                </li>
                            </div>
                        </ul>
                    </div>

                    <!-- Theme toggle -->
                    <button @click="toggleTheme" class="p-2">
                        <MoonIcon
                            v-if="!isDarkMode"
                            class="w-6 h-6 text-gray-200"
                        />
                        <SunIcon v-else class="w-6 h-6 text-gray-200" />
                    </button>

                    <LanguageToggle />

                </div>
            </nav>

            <!-- Main Page Content -->
            <main class="flex-1 p-6 pt-24 bg-gray-100 dark:bg-gray-800">
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
