<script setup>
import { ref, onMounted, onUnmounted, computed } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import StudentSidebar from "@/Components/StudentSidebar.vue";
import { Bars3Icon, MoonIcon, SunIcon } from "@heroicons/vue/24/outline";
import { router } from "@inertiajs/vue3";

// Auth User
const user = computed(() => usePage().props.auth.user);

// Dark mode
const darkMode = ref(localStorage.getItem("darkMode") === "true");

const toggleDarkMode = () => {
    darkMode.value = !darkMode.value;
    localStorage.setItem("darkMode", darkMode.value);
};

// Mobile detection + sidebar state
const isMobile = ref(false);
const isSidebarOpen = ref(true);

function updateScreen() {
    const mobile = window.innerWidth < 768;
    if (mobile !== isMobile.value) {
        isSidebarOpen.value = !mobile;
    }
    isMobile.value = mobile;
}

onMounted(() => {
    updateScreen();
    window.addEventListener("resize", updateScreen);
});
onUnmounted(() => {
    window.removeEventListener("resize", updateScreen);
});

// Sidebar toggle
const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value;
};

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
    <div :class="{ dark: darkMode }">
        <div
            class="flex h-screen bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 relative"
        >
            <!-- Sidebar -->
            <StudentSidebar :is-open="isSidebarOpen" :is-mobile="isMobile" />

            <!-- Mobile backdrop -->
            <div
                v-if="isMobile && isSidebarOpen"
                class="fixed inset-0 bg-black bg-opacity-50 z-40"
                @click="toggleSidebar"
            />

            <!-- Main content area -->
            <div
                class="flex-1 flex flex-col overflow-x-hidden"
                :class="{
                    'ml-0': !isMobile && isSidebarOpen, // Sidebar will push content when opened on desktop
                    'ml-0': !isMobile && !isSidebarOpen, // Sidebar will not push content when collapsed on desktop
                }"
            >
                <!-- Navbar -->
                <header
                    class="flex items-center justify-between bg-white dark:bg-gray-800 p-4 shadow"
                >
                    <!-- Hamburger toggle (always visible) -->
                    <button
                        @click="toggleSidebar"
                        class="text-gray-500 dark:text-gray-400"
                    >
                        <Bars3Icon class="h-6 w-6" />
                    </button>
                    
                         <!-- Right-side nav -->
                    <div class="flex items-center gap-4 ml-auto">

                        <!-- User Dropdown -->
                        <div class="relative nav-item topbar-user dropdown hidden-caret" >

                            <button
                                class="dropdown-toggle profile-pic flex items-center gap-2 focus:outline-none"
                                @click="toggleDropdown"
                                aria-expanded="dropdownVisible"
                            >
                                <div class="avatar-sm">
                                    
                                    <img
                                        :src="user?.profileImg ? user.profileImg : '/img/profile.jpg'"
                                        alt="Profile Image"
                                        class="avatar-img rounded-full w-10 h-15"
                                    />
                                </div>
                                <span
                                    class="profile-username text-sm hidden md:block"
                                >
                                    <span class="fw-bold font-semibold">{{
                                        user?.name || 'Guest'
                                    }}</span>
                                </span>
                            </button>

                            <ul
                                v-show="dropdownVisible"
                                @click.outside="closeDropdown"
                                class="dropdown-menu dropdown-user animated fadeIn absolute right-0 mt-2 w-64 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 shadow-lg rounded-md z-50"
                            >
                                <div
                                    class="dropdown-user-scroll scrollbar-outer p-4"
                                >
                                    <li class="user-box flex items-center gap-4">
                                        <img :src="user?.profileImg ? user.profileImg : '/img/profile.jpg'"
                                            alt="Profile Image" class="avatar-img rounded-circle w-10 h-10" />

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
                                                :href="route('profile.show', { user: user.id })"
                                                class="btn btn-xs btn-secondary btn-sm mt-2 inline-block px-3 py-1 bg-gray-800 text-white rounded-md hover:bg-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600"
                                            >
                                                View Profile
                                            </Link>
                                        </div>
                                    </li>

                                    <li>
                                        <div
                                            class="dropdown-divider border-t my-2"
                                        ></div>
                                        <Link
                                            class="dropdown-item block px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
                                            :href="route('profile.show', { user: user.id })"
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
                                           :href="route('profile.edit')"
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

                        <!-- Dark mode toggle -->
                        <button
                            @click="toggleDarkMode"
                            class="p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700"
                        >
                            <component
                                :is="darkMode ? SunIcon : MoonIcon"
                                class="h-5 w-5 text-gray-500 dark:text-gray-400"
                            />
                        </button>
                    </div>
                </header>

                <!-- Main content -->
                <main
                    class="flex-1 overflow-y-auto p-4 bg-gray-50 dark:bg-gray-900"
                >
                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>
