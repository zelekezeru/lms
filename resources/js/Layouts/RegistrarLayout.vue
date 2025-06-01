<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import RegistrarSidebar from '@/Components/RegistrarSidebar.vue';
import { Bars3Icon, MoonIcon, SunIcon } from '@heroicons/vue/24/outline';

const user = computed(() => usePage().props.auth.user);
const darkMode = ref(localStorage.getItem('darkMode') === 'true');

// Dark mode toggle
const toggleDarkMode = () => {
    darkMode.value = !darkMode.value;
    localStorage.setItem('darkMode', darkMode.value);
};

// Sidebar + screen responsiveness
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
    window.addEventListener('resize', updateScreen);
});
onUnmounted(() => {
    window.removeEventListener('resize', updateScreen);
});

const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value;
};

// Dropdown (profile menu)
const dropdownVisible = ref(false);
const toggleDropdown = () => {
    dropdownVisible.value = !dropdownVisible.value;
};
const closeDropdown = () => {
    dropdownVisible.value = false;
};

// Logout
const logout = () => {
    router.post(route('logout'));
};
</script>

<template>
    <div :class="{ dark: darkMode }">
        <div class="flex h-screen bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 relative">
            <!-- Sidebar -->
            <RegistrarSidebar :is-open="isSidebarOpen" :is-mobile="isMobile" />

            <!-- Mobile backdrop -->
            <div
                v-if="isMobile && isSidebarOpen"
                class="fixed inset-0 bg-black bg-opacity-50 z-40"
                @click="toggleSidebar"
            />

            <!-- Main content -->
            <div class="flex-1 flex flex-col overflow-x-hidden">
                <!-- Header -->
                <header class="flex items-center justify-between bg-white dark:bg-gray-800 p-4 shadow">
                    <button @click="toggleSidebar" class="text-gray-500 dark:text-gray-400">
                        <Bars3Icon class="h-6 w-6" />
                    </button>

                    <div class="flex items-center gap-4 ml-auto">
                        <!-- Profile Dropdown -->
                        <div class="relative">
                            <button @click="toggleDropdown" class="flex items-center gap-2 focus:outline-none">
                                <img
                                    :src="user?.profileImg || '/img/profile.jpg'"
                                    alt="Profile"
                                    class="rounded-full w-10 h-10"
                                />
                                <span class="hidden md:block font-semibold">
                                    {{ user?.name || 'Guest' }}
                                </span>
                            </button>

                            <ul
                                v-show="dropdownVisible"
                                @click.outside="closeDropdown"
                                class="absolute right-0 mt-2 w-64 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow z-50"
                            >
                                <li class="p-4">
                                    <div class="flex items-center gap-4">
                                        <img
                                            :src="user?.profileImg || '/img/profile.jpg'"
                                            alt="Profile"
                                            class="rounded-full w-16 h-16"
                                        />
                                        <div>
                                            <h4 class="font-bold text-gray-900 dark:text-gray-100">{{ user.name }}</h4>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ user.email }}</p>
                                            <Link
                                                :href="route('profile.edit')"
                                                class="btn btn-sm mt-2 bg-gray-700 text-white rounded-md px-3 py-1 hover:bg-gray-600"
                                            >
                                                View Profile
                                            </Link>
                                        </div>
                                    </div>
                                </li>
                                <li class="border-t border-gray-200 dark:border-gray-700">
                                    <button
                                        @click="logout"
                                        class="w-full text-left px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
                                    >
                                        Logout
                                    </button>
                                </li>
                            </ul>
                        </div>

                        <!-- Dark Mode Toggle -->
                        <button @click="toggleDarkMode" class="p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700">
                            <component :is="darkMode ? SunIcon : MoonIcon" class="h-5 w-5 text-gray-500 dark:text-gray-400" />
                        </button>
                    </div>
                </header>

                <!-- Slot content -->
                <main class="flex-1 overflow-y-auto p-4 bg-gray-50 dark:bg-gray-900">
                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>
