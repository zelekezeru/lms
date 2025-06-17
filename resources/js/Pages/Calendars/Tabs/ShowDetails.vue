<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import { ArrowPathIcon, EyeIcon } from '@heroicons/vue/24/outline'

// Example data, replace with your actual logic
const activeSemester = ref({ id: 1, name: 'Spring 2024' })
const refreshing = ref(false)

function refreshData() {
    refreshing.value = true
    setTimeout(() => {
        refreshing.value = false
    }, 1000)
}

// If using i18n and route helpers, make sure they're globally available
</script>

<template>
    <div class="overflow-x-auto py-8 bg-gradient-to-br from-blue-50 via-white to-green-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 min-h-[300px]">
        <div
            v-if="activeSemester"
            class="max-w-2xl mx-auto bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 border border-blue-100 dark:border-gray-700 relative transition-all duration-300 hover:shadow-2xl"
        >
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-blue-700 dark:text-blue-200 tracking-wide flex items-center gap-2">
                    <span class="inline-block w-3 h-3 bg-gradient-to-tr from-blue-400 to-green-400 rounded-full animate-pulse"></span>
                    {{ activeSemester.name }}
                </h2>
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
                        <span>
                            {{ $t("common.refresh") }}
                        </span>
                    </button>
                    <Link
                        :href="route('semesters.show', activeSemester.id)"
                        class="flex items-center px-4 py-2 text-sm font-semibold text-white bg-gradient-to-r from-green-500 to-green-700 rounded-lg shadow hover:from-green-600 hover:to-green-800 focus:outline-none focus:ring-2 focus:ring-green-400 transition-all duration-200"
                    >
                        <EyeIcon class="w-5 h-5 mr-2" />
                        <span>
                            {{ $t("common.view") }}
                        </span>
                    </Link>
                </div>
            </div>
            <div class="absolute top-0 right-0 p-2">
                <span class="inline-block w-2 h-2 bg-blue-400 rounded-full animate-ping"></span>
            </div>
        </div>
        <div v-else class="text-center text-gray-500 dark:text-gray-400 py-16 text-lg font-medium">
            <span class="inline-block mb-4">
                <svg class="w-10 h-10 mx-auto text-blue-300 dark:text-gray-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"></path>
                    <circle cx="12" cy="12" r="10" />
                </svg>
            </span>
            <div>
                {{ $t('semester.no_data') }}
            </div>
        </div>
        <div class="max-w-2xl mx-auto bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 mt-6 border border-red-100 dark:border-gray-700">

            <div class="flex items-center mb-6 max-w-xl text-center">
                <p class="text-lg font-medium text-gray-700 dark:text-gray-200 mb-2">
                    Please review all <span class="font-semibold text-blue-600 dark:text-blue-300">course submissions</span>, 
                    <span class="font-semibold text-green-600 dark:text-green-300">academic statuses</span>, 
                    <span class="font-semibold text-purple-600 dark:text-purple-300">curriculum completions</span>, 
                    and other semester-related data before proceeding to <span class="font-bold text-red-600 dark:text-red-400">close the current semester</span>.
                </p>
            </div>
            <div class="flex items-center mb-6 max-w-xl text-center">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Ensure all records are accurate and up-to-date. This action cannot be undone.
                </p>
            </div>

            <div class="flex justify-center mt-4">
                <div>
                <Link
                    :href="route('semesters.closeForm')"
                    class="flex items-center gap-2 px-6 py-3 text-base font-semibold text-white bg-gradient-to-r from-red-500 to-red-700 rounded-xl shadow hover:from-red-600 hover:to-red-800 focus:outline-none focus:ring-2 focus:ring-red-400 transition-all duration-200"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    <span>{{ $t("semester.close_current") }}</span>
                </Link>
                </div>
            </div>
        </div>
    </div>
</template>