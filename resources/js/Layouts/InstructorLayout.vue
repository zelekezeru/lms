<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import InstructorSidebar from "@/Components/InstructorSidebar.vue";
import { Bars3Icon, MoonIcon, SunIcon } from "@heroicons/vue/24/outline";

const darkMode = ref(localStorage.getItem("darkMode") === "true");
const toggleDarkMode = () => {
    darkMode.value = !darkMode.value;
    localStorage.setItem("darkMode", darkMode.value);
};

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

const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value;
};
</script>

<template>
    <div :class="{ dark: darkMode }">
        <div class="flex h-screen bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 relative">
            <InstructorSidebar :is-open="isSidebarOpen" :is-mobile="isMobile" />

            <div
                v-if="isMobile && isSidebarOpen"
                class="fixed inset-0 bg-black bg-opacity-50 z-40"
                @click="toggleSidebar"
            />

            <div
                class="flex-1 flex flex-col overflow-x-hidden"
                :class="{
                    'ml-0': !isMobile && isSidebarOpen,
                    'ml-0': !isMobile && !isSidebarOpen,
                }"
            >
                <header class="flex items-center justify-between bg-white dark:bg-gray-800 p-4 shadow">
                    <button @click="toggleSidebar" class="text-gray-500 dark:text-gray-400">
                        <Bars3Icon class="h-6 w-6" />
                    </button>

                    <div class="flex items-center space-x-3">
                        <img
                            src="https://images.pexels.com/photos/614810/pexels-photo-614810.jpeg"
                            alt="Instructor Profile"
                            class="h-10 w-10 rounded-full object-cover"
                        />
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

                <main class="flex-1 overflow-y-auto p-4 bg-gray-50 dark:bg-gray-900">
                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>
