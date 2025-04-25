<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import StudentSidebar from "@/Components/StudentSidebar.vue";
import { Bars3Icon, MoonIcon, SunIcon } from "@heroicons/vue/24/outline";

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
    // only flip sidebar open/closed when changing between mobile <-> desktop
    if (mobile !== isMobile.value) {
        isSidebarOpen.value = !mobile;
    }
    isMobile.value = mobile;
}

onMounted(() => {
    // initial
    updateScreen();
    window.addEventListener("resize", updateScreen);
});
onUnmounted(() => {
    window.removeEventListener("resize", updateScreen);
});

// toggle
const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value;
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
                    'ml-64': !isMobile && isSidebarOpen, // Sidebar will push content when opened on desktop
                    'ml-20': !isMobile && !isSidebarOpen, // Sidebar will not push content when collapsed on desktop
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

                    <!-- Profile image (circular) -->
                    <div class="flex items-center space-x-3">
                        <!-- Profile Image -->
                        <img
                            src="https://images.pexels.com/photos/771742/pexels-photo-771742.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1"
                            alt="Profile Image"
                            class="h-10 w-10 rounded-full object-cover"
                        />

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
