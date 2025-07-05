<script setup>
import { ref } from "vue";
import { Link } from "@inertiajs/vue3";
import {
    ArrowPathIcon,
    EyeIcon,
    ExclamationTriangleIcon,
} from "@heroicons/vue/24/outline";

const props = defineProps({
    studyModes: {
        type: Array,
        required: true,
    },
    showCloseSemester: {
        type: Function,
        required: true,
    },
});

const refreshing = ref(false);

function refreshData() {
    refreshing.value = true;
    setTimeout(() => {
        refreshing.value = false;
    }, 1000);
}
</script>

<template>
    <div
        class="overflow-x-auto py-8 bg-gradient-to-br from-blue-50 via-white to-green-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 min-h-[300px]"
    >
        <!-- Common warning -->
        <div
            class="max-w-4xl mx-auto bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 border border-yellow-100 dark:border-yellow-700 mb-10"
        >
            <div class="flex items-center mb-6 text-center justify-center">
                <p
                    class="text-lg font-medium text-gray-700 dark:text-gray-200 mb-2"
                >
                    Please review all
                    <span class="font-semibold text-blue-600 dark:text-blue-300"
                        >course submissions</span
                    >,
                    <span
                        class="font-semibold text-green-600 dark:text-green-300"
                        >academic statuses</span
                    >,
                    <span
                        class="font-semibold text-purple-600 dark:text-purple-300"
                        >curriculum completions</span
                    >, and other semester-related data before proceeding to
                    <span class="font-bold text-red-600 dark:text-red-400"
                        >close the current semester</span
                    >.
                </p>
            </div>
            <div class="flex items-center text-center justify-center">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Ensure all records are accurate and up-to-date. This action
                    cannot be undone.
                </p>
            </div>
        </div>

        <!-- Loop through all studyModes -->
        <div v-for="studyMode in studyModes" :key="studyMode.id" class="mb-10">
            <!-- ✅ Active semester box -->
            <div
                v-if="studyMode.activeSemester"
                class="max-w-4xl mx-auto bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 border border-blue-100 dark:border-gray-700 relative transition-all duration-300 hover:shadow-2xl"
            >
                <div class="flex items-center justify-between mb-6">
                    <div class="flex flex-col items-start gap-1">
                        <h2
                            class="text-2xl font-bold text-blue-700 dark:text-blue-200 tracking-wide flex items-center gap-2"
                        >
                            <span
                                class="inline-block w-3 h-3 bg-gradient-to-tr from-blue-400 to-green-400 rounded-full animate-pulse"
                            ></span>
                            {{ studyMode.name }} -
                            {{ studyMode.activeSemester.name }} Semester
                        </h2>
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            <span class="font-semibold">{{
                                studyMode.activeSemester.year.name
                            }}</span>
                            <span class="mx-2">|</span>
                            <!-- Optional section info -->
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <button
                            @click="refreshData"
                            :disabled="refreshing"
                            class="flex items-center px-4 py-2 text-sm font-semibold text-white bg-gradient-to-r from-blue-500 to-blue-700 rounded-lg shadow hover:from-blue-600 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-400 disabled:opacity-60 transition-all duration-200"
                        >
                            <ArrowPathIcon
                                class="w-5 h-5 mr-2"
                                :class="{ 'animate-spin': refreshing }"
                            />
                            <span>Refresh</span>
                        </button>

                        <Link
                            :href="
                                route(
                                    'semesters.show',
                                    studyMode.activeSemester.id
                                )
                            "
                            class="flex items-center px-4 py-2 text-sm font-semibold text-white bg-gradient-to-r from-green-500 to-green-700 rounded-lg shadow hover:from-green-600 hover:to-green-800 focus:outline-none focus:ring-2 focus:ring-green-400 transition-all duration-200"
                        >
                            <EyeIcon class="w-5 h-5 mr-2" />
                            <span>View</span>
                        </Link>
                    </div>
                </div>

                <div class="absolute top-0 right-0 p-2">
                    <span
                        class="inline-block w-2 h-2 bg-blue-400 rounded-full animate-ping"
                    ></span>
                </div>

                <div class="flex justify-center mt-4">
                    <button
                        @click="showCloseSemester(studyMode.id)"
                        class="flex items-center gap-2 px-6 py-3 text-base font-semibold text-white bg-gradient-to-r from-red-500 to-red-700 rounded-xl shadow hover:from-red-600 hover:to-red-800 focus:outline-none focus:ring-2 focus:ring-red-400 transition-all duration-200"
                    >
                        <svg
                            class="w-5 h-5"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                        <span>Close Semester</span>
                    </button>
                </div>
            </div>

            <!-- ❌ No active semester -->
            <div
                v-else
                class="max-w-4xl mx-auto bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 border border-red-200 dark:border-red-700"
            >
                <div class="flex items-center justify-between mb-4">
                    <h2
                        class="text-2xl font-bold text-red-600 dark:text-red-300 tracking-wide"
                    >
                        {{ studyMode.name }}
                    </h2>
                    <ExclamationTriangleIcon class="w-7 h-7 text-red-400" />
                </div>

                <div class="text-center">
                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                        No active semester is set for
                        <span class="font-semibold">{{ studyMode.name }}</span
                        >. You need to set one before proceeding with semester
                        operations.
                    </p>

                    <button
                        @click="showCloseSemester(studyMode.id)"
                        class="inline-flex items-center px-5 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-semibold rounded-md shadow transition"
                    >
                        Set Active Semester
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
