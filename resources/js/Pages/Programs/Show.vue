<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { defineProps, ref } from "vue";
import { router } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import { PencilIcon, EyeIcon } from "@heroicons/vue/24/solid";
import { CogIcon } from "@heroicons/vue/24/outline";
import ShowDetails from "./Tabs/ShowDetails.vue";
import ShowTracks from "./Tabs/ShowTracks.vue";
import ShowStudyModes from "./Tabs/ShowStudyModes.vue";

const props = defineProps({
    program: { type: Object, required: true },
    studyModes: { type: Object, required: false },
    users: { type: Array, required: false },
});

const tabs = [
    { key: "details", label: "Details", icon: CogIcon },
    { key: "tracks", label: "Tracks", icon: PencilIcon },
    { key: "modes", label: "Study Modes", icon: EyeIcon },
];
const selectedTab = ref("details");
</script>

<template>
    <AppLayout>
        <div class="max-w-5xl p-1 mx-auto">
            <h1
                class="mb-6 text-3xl font-semibold text-center text-gray-900 dark:text-gray-100"
            >
                {{ program.name }} {{ $t('programs.show.title') }}
            </h1>

            <nav
                class="flex justify-center mb-6 space-x-4 border-b border-gray-200 dark:border-gray-700"
            >
                <button
                    v-for="tab in tabs"
                    :key="tab.key"
                    @click="selectedTab = tab.key"
                    :class="[ 
                        'flex items-center px-4 py-2 space-x-2 text-sm font-medium transition',
                        selectedTab === tab.key
                            ? 'border-b-2 border-indigo-500 text-indigo-600'
                            : 'text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200',
                    ]"
                >
                    <component :is="tab.icon" class="w-5 h-5" />
                    <span>{{ $t(`programs.show.tabs.${tab.key}`) }}</span>
                </button>
            </nav>

            <div
                class="p-3 bg-white border shadow dark:bg-gray-800 rounded-xl md:p-6 dark:border-gray-700"
            >
                <transition
                    mode="out-in"
                    enter-active-class="transition duration-300 ease-out"
                    enter-from-class="scale-75 opacity-0"
                    enter-to-class="scale-100 opacity-100"
                    leave-active-class="transition duration-200 ease-in"
                    leave-from-class="scale-100 opacity-100"
                    leave-to-class="scale-75 opacity-0"
                >
                    <div :key="selectedTab">
                        <ShowDetails
                            v-show="selectedTab == 'details'"
                            :program="program"
                        />

                        <ShowTracks
                            v-show="selectedTab == 'tracks'"
                            :program="program"
                            :users="users"
                        />

                        <ShowStudyModes
                            v-show="selectedTab == 'modes'"
                            :program="program"
                            :study-modes="studyModes"
                        />
                    </div>
                </transition>
            </div>
        </div>
    </AppLayout>
</template>
