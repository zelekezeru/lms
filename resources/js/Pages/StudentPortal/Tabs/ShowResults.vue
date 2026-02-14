<script setup>
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { defineProps } from "vue";

const props = defineProps({
    results: {
        type: Array,
        required: true,
    },
    grades: {
        type: Array,
        required: false,
        default: () => [],
    },
});

const openModal = (tab) => {
    // Placeholder for modal logic
};
</script>

<template>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-300 dark:border-gray-600">
        <h2 class="text-xl font-bold mb-4">Results</h2>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[600px] table-fixed">
                <thead class="bg-gray-100 dark:bg-gray-700">
                    <tr>
                        <th class="text-left px-3 py-2 font-medium text-xs w-8 text-gray-800 dark:text-gray-200">#</th>
                        <th class="text-left px-4 py-2 font-medium text-sm text-gray-800 dark:text-gray-200">Course Name</th>
                        <th class="text-left px-4 py-2 font-medium text-sm text-gray-800 dark:text-gray-200">Weight</th>
                        <th class="text-left px-4 py-2 font-medium text-sm text-gray-800 dark:text-gray-200">Result Point</th>
                        <th class="text-left px-4 py-2 font-medium text-sm text-gray-800 dark:text-gray-200">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-if="props.results.length > 0"
                        v-for="(result, index) in props.results"
                        :key="result.id"
                        :class="[
                            index % 2 === 0
                                ? 'bg-white dark:bg-gray-800'
                                : 'bg-gray-50 dark:bg-gray-700',
                            'border-b border-gray-200 dark:border-gray-600',
                        ]"
                    >
                        <td class="px-3 py-3 text-sm text-gray-700 dark:text-gray-300">{{ index + 1 }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ result.weight?.course?.name ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                            {{ result.weight?.name ?? 'N/A' }}
                            (<span class="font-bold">{{ result.weight?.point ?? 'N/A' }}</span>/100%)
                        </td>
                        <td class="px-4 py-3 text-sm font-bold text-gray-700 dark:text-gray-300">{{ result.point ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                            <PrimaryButton size="sm" @click="openModal('results')">View</PrimaryButton>
                        </td>
                    </tr>
                    <tr v-else>
                        <td colspan="5" class="text-center px-4 py-6 text-sm text-gray-500 dark:text-gray-300">
                            No results available.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>