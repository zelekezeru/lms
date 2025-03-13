<script setup>
import { ref, onMounted, onUnmounted, computed } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import { useDark } from "@vueuse/core";
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
console.log(user.value);
</script>

<template>
  <div class="flex h-screen bg-gray-100 dark:bg-gray-900 relative transition-colors duration-900">
    <Sidebar :sidebarHovered="sidebarHovered" :sidebarVisible="sidebarVisible" :isMobile="isMobile" :handle-aside-hover="handleAsideHover" />

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

          <!-- Sidebar Toggler -->
          <button @click="sidebarVisible = !sidebarVisible">
            <Bars3Icon class="w-6 h-6 text-gray-200" />
          </button>
        </div>

        <!-- Other Navbar Content -->
        <div class="flex items-center gap-4 ml-auto">
          <!-- User Info -->
          <div class="text-sm font-medium text-gray-200 flex items-center gap-2">
            <img :src="user.profileImg ? user.profileImg : '/img/mlane.jpg'" alt="Avatar" class="w-10 h-10 rounded-full" />
            <div>
              <div class="text-base">{{ user.name }}</div>
              <div class="text-xs">{{ user.email }}</div>
            </div>
          </div>
          <!-- Theme Toggle Button -->
          <button @click="toggleTheme" class="p-2">
            <MoonIcon v-if="!isDarkMode" class="w-6 h-6 text-gray-200" />
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
