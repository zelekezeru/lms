<script setup>
import { ref, onMounted, onUnmounted, computed } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import { Bars3Icon, MoonIcon, SunIcon } from "@heroicons/vue/24/outline";
import { router } from "@inertiajs/vue3";
import Sidebar from "@/Components/Sidebar.vue";
import LanguageToggle from "@/Components/LanguageToggle.vue";
import RegistrarSidebar from "@/Components/RegistrarSidebar.vue";

// Auth User
const user = computed(() => usePage().props.auth.user);
const userRoles = usePage().props.auth.user.roles;
const loggedInAs = usePage().props.auth.user.loggedInAs;

const darkMode = ref(localStorage.getItem("darkMode") === "true");
const toggleDarkMode = () => {
    darkMode.value = !darkMode.value;
    localStorage.setItem("darkMode", darkMode.value);
    document.documentElement.classList.toggle("dark", darkMode.value);
};

// Responsive sidebar logic
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

    if (darkMode.value) {
        document.documentElement.classList.add("dark");
    } else {
        document.documentElement.classList.remove("dark");
    }

    document.addEventListener("click", handleClickOutside);
});

onUnmounted(() => {
    window.removeEventListener("resize", updateScreen);
    document.removeEventListener("click", handleClickOutside);
});

const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value;
};

// Dropdown logic
const dropdownVisible = ref(false);
const dropdownRef = ref(null);

const toggleDropdown = () => {
    dropdownVisible.value = !dropdownVisible.value;
};
const closeDropdown = () => {
    dropdownVisible.value = false;
};

const handleClickOutside = (event) => {
    if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
        closeDropdown();
    }
};

// Logout
const logout = () => {
    router.post(route("logout"));
};

// Role switcher function
const changeRole = (roleName) => {
    router.visit(route("switch-role"), {
        method: "post",
        data: { role: roleName },
    });
};
</script>

<template>
    <div :class="{ dark: darkMode }">
        <div
            class="flex h-screen bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 relative"
        >
            <Sidebar
                :active-role="loggedInAs.name"
                :is-open="isSidebarOpen"
                :is-mobile="isMobile"
            />

            <div
                v-if="isMobile && isSidebarOpen"
                class="fixed inset-0 bg-black bg-opacity-50 z-40"
                @click="toggleSidebar"
            />

            <div class="flex-1 flex flex-col overflow-x-hidden">
                <header
                    class="flex items-center justify-between bg-white dark:bg-gray-800 px-4 py-3 shadow z-30"
                >
                    <!-- Sidebar toggle -->
                    <button
                        @click="toggleSidebar"
                        class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white"
                    >
                        <Bars3Icon class="h-6 w-6" />
                    </button>

                    <!-- Right side controls -->
                    <div class="flex items-center gap-4">
                        <!-- User Dropdown with logout and roles below -->
                        <div class="relative" ref="dropdownRef">
                            <button
                                @click="toggleDropdown"
                                class="flex items-center gap-2 px-2 py-1 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition"
                            >
                                <span
                                    class="h-8 w-8 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center overflow-hidden"
                                >
                                    <template v-if="user.profileImg">
                                        <img
                                            :src="user.profileImg"
                                            alt="Profile"
                                            class="h-8 w-8 object-cover rounded-full"
                                        />
                                    </template>
                                    <template v-else>
                                        <!-- Default profile icon -->
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-6 w-6 text-gray-500 dark:text-gray-300"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M5.121 17.804A8.966 8.966 0 0112 15c2.21 0 4.225.805 5.879 2.137M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                                            />
                                        </svg>
                                    </template>
                                </span>

                                <span
                                    class="hidden md:block text-sm font-medium text-gray-800 dark:text-gray-200"
                                >
                                    {{ user?.name || "Guest" }}
                                </span>

                                <svg
                                    :class="[
                                        'h-4 w-4 transition-transform text-gray-500 dark:text-gray-300',
                                        { 'rotate-180': dropdownVisible },
                                    ]"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 9l-7 7-7-7"
                                    />
                                </svg>
                            </button>

                            <!-- Dropdown menu -->
                            <ul
                                v-show="dropdownVisible"
                                class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-xl rounded-md z-50 overflow-hidden"
                            >
                                <!-- Profile Info -->
                                <li
                                    class="px-4 py-3 flex items-center space-x-3"
                                >
                                    <div
                                        class="h-10 w-10 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center overflow-hidden"
                                    >
                                        <template v-if="user.profileImg">
                                            <img
                                                :src="user.profileImg"
                                                alt="Profile Large"
                                                class="h-10 w-10 object-cover rounded-full"
                                            />
                                        </template>
                                        <template v-else>
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-6 w-6 text-gray-500 dark:text-gray-300"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M5.121 17.804A8.966 8.966 0 0112 15c2.21 0 4.225.805 5.879 2.137M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                                                />
                                            </svg>
                                        </template>
                                    </div>
                                    <div>
                                        <p
                                            class="font-semibold text-gray-900 dark:text-gray-100"
                                        >
                                            {{ user.name }}
                                        </p>
                                        <p
                                            class="text-xs text-gray-500 dark:text-gray-400"
                                        >
                                            {{ user.email }}
                                        </p>
                                    </div>
                                </li>

                                <li>
                                    <hr
                                        class="border-gray-200 dark:border-gray-700"
                                    />
                                </li>

                                <!-- Profile Links -->
                                <li>
                                    <Link
                                        :href="route('profile.edit')"
                                        class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200"
                                    >
                                        My Profile
                                    </Link>
                                </li>
                                <li>
                                    <Link
                                        href="#"
                                        class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200"
                                        @click.prevent="logout"
                                    >
                                        Logout
                                    </Link>
                                </li>

                                <li>
                                    <hr
                                        class="border-gray-200 dark:border-gray-700"
                                    />
                                </li>

                                <!-- Switch Role Section below logout -->
                                <li
                                    class="px-4 py-2 text-sm font-semibold text-gray-900 dark:text-gray-100"
                                >
                                    Switch Role
                                </li>
                                <li>
                                    <div
                                        class="flex flex-col space-y-1 px-4 py-2 max-h-40 overflow-auto"
                                        style="scrollbar-width: thin"
                                    >
                                        <button
                                            v-for="role in userRoles"
                                            :key="role"
                                            @click="changeRole(role)"
                                            :class="[
                                                'text-left px-2 py-1 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700',
                                                role === loggedInAs.name
                                                    ? 'font-bold bg-gray-300 dark:bg-gray-600'
                                                    : '',
                                            ]"
                                        >
                                            {{ role }}
                                        </button>
                                    </div>
                                </li>
                            </ul>
                        </div>

                        <!-- Language toggle -->
                        <LanguageToggle :collapsed="isMobile" />

                        <!-- Dark mode toggle -->
                        <button
                            @click="toggleDarkMode"
                            class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white"
                        >
                            <component
                                :is="darkMode ? SunIcon : MoonIcon"
                                class="h-6 w-6"
                            />
                        </button>
                    </div>
                </header>

                <main class="flex-1 overflow-y-auto p-4">
                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>
