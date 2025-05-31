<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { defineProps, ref } from "vue";
import "sweetalert2/dist/sweetalert2.min.css";
import { AcademicCapIcon, CogIcon } from "@heroicons/vue/24/outline";
import ShowDetails from "./Tabs/ShowDetails.vue";

// Define the props for the room
defineProps({
    room: {
        type: Object,
        required: true,
    },
});

const selectedTab = ref("details");

const tabs = [
    { key: "details", label: "Details", icon: CogIcon },
];
</script>

<template>
    <AppLayout>
        <div class="max-w-7xl mx-auto p-6">
            <h1
                class="text-3xl font-semibold mb-6 text-gray-900 dark:text-gray-100 text-center"
            >
                {{ room.name }} Room
            </h1>

            <nav
                class="flex justify-center space-x-4 overflow-x-auto pb-2 mb-6 border-b border-gray-200 dark:border-gray-700"
            >
                <button
                    v-for="tab in tabs"
                    :key="tab.key"
                    @click="selectedTab = tab.key"
                    class="flex-shrink-0 flex items-center px-4 py-2 space-x-2 text-sm font-medium transition whitespace-nowrap"
                    :class="
                        selectedTab === tab.key
                            ? 'border-b-2 border-indigo-500 text-indigo-600'
                            : 'text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200'
                    "
                >
                    <component :is="tab.icon" class="w-5 h-5" />
                    <span>{{ tab.label }}</span>
                </button>
            </nav>

            <!-- Details Panel -->
            <transition
                mode="out-in"
                enter-active-class="transition duration-300 ease-out"
                enter-from-class="opacity-0 scale-75"
                enter-to-class="opacity-100 scale-100"
                leave-active-class="transition duration-200 ease-in"
                leave-from-class="opacity-100 scale-100"
                leave-to-class="opacity-0 scale-75"
            >
                <div
                    :key="selectedTab"
                    class="bg-white dark:bg-gray-800 shadow rounded-xl p-6 border dark:border-gray-700"
                >
                    <ShowDetails
                        v-if="selectedTab === 'details'"
                        :room="room"
                    />
                </div>
            </transition>
        </div>
    </AppLayout>
</template>
