<script setup>
import { defineProps } from "vue";

const props = defineProps({
    results: {
        type: Array,
        required: true,
    },
    section: {
        type: Object,
        required: true,
    },
    weights: {
        type: Array,
        required: true,
    },
    sumOfWeightPoints: {
        type: Number,
        required: true,
    },
    activeWeightId: {
        type: Number,
        required: true,
    },
    setActiveWeightId: {
        type: Function,
        required: true,
    },
    setResultValue: {
        type: Function,
        required: true,
    },
    getResultValue: {
        type: Function,
        required: true,
    },
    getStudentTotalPoints: {
        type: Function,
        required: true,
    },
    getStudentGradeLetter: {
        type: Function,
        required: true,
    },
    submitWeightResults: {
        type: Function,
        required: true,
    },
    
});
</script>

<template>
    <div>
        <div class="flex items-center justify-center mb-2">
            <h2
                class="text-xl font-semibold text-gray-900 dark:text-gray-100"
            >
                Assessment Results
            </h2>
        </div>
        <!-- Assesment  Results List -->
        <div class="overflow-x-auto">
        
            <div
            class="mt-4 border-t border-b border-gray-300 dark:border-gray-600 pt-2 pb-4"
            >
                <!-- Section Students list -->
                <div class="overflow-x-auto">
                    <div v-if="selectedTab === 'results'" class="overflow-x-auto mt-6">
                        <table class="min-w-full divide-y divide-gray-200 border rounded shadow-sm bg-white dark:bg-gray-900">
                            <thead class="bg-gray-100 dark:bg-gray-800 text-md font-semibold text-gray-700 dark:text-gray-200">
                                <tr>
                                    <th class="px-4 py-2 text-left">#</th>
                                    <th class="px-4 py-2 text-left">Student</th>
                                    <th v-for="weight in props.weights" :key="weight.id" class="px-4 py-2 text-center">
                                        <div class="flex items-center justify-between">
                                            <span>{{ weight.name }} ({{ weight.point }}pt)</span>
                                            <button
                                                v-if="!activeWeightId"
                                                @click="activateWeight(weight.id)"
                                                class="ml-2 text-xs text-white bg-blue-600 hover:bg-blue-700 px-2 py-1 rounded"
                                            >
                                                <PencilIcon class="w-5 h-5" />
                                            </button>
                                        </div>
                                    </th>
                                    <th class="px-4 py-2 text-left">Total ({{ sumOfWeightPoints }}pt)</th>
                                    <th class="px-4 py-2 text-left">Grade</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Section Students Iteration -->
                                <tr
                                    v-for="(student, index) in section.students"
                                    :key="student.id"
                                    :class="
                                        index % 2 === 0
                                            ? 'bg-white dark:bg-gray-800'
                                            : 'bg-gray-50 dark:bg-gray-700'
                                    "
                                    class="border-b border-gray-300 dark:border-gray-600 text-sm text-gray-600 dark:text-gray-300"
                                >
                                    <td class="px-4 py-2 border-gray-300 dark:border-gray-600">
                                        {{ index + 1 }}
                                    </td>
                                    <td class="px-4 py-2">
                                        {{ student.first_name }} {{ student.middle_name }}
                                    </td>

                                    <!-- Section Course Weights -->
                                    <td
                                        v-for="weight in props.weights"
                                        :key="weight.id"
                                        class="px-4 py-2 text-center border-gray-300 dark:border-gray-600"
                                    >
                                        <!-- Editable Input for Active Weight -->
                                        <span v-if="activeWeightId === weight.id">
                                            <input
                                                type="number"
                                                step="0.01"
                                                min="0"
                                                max="100"
                                                class="px-1 py-0.5 h-7 rounded-md dark:bg-gray-800 dark:text-gray-100 text-center"
                                                :value="getResultValue(student.id, weight.id)"
                                                @input="setResultValue(student.id, weight.id, $event.target.value)"
                                            />
                                        </span>

                                        <!-- Result or N/A when not active -->
                                        <span v-else>
                                            <span
                                                v-if="weight.results.some(result => result.student_id === student.id)"
                                                class="text-gray-900 dark:text-gray-100"
                                            >
                                                {{ weight.results.find(result => result.student_id === student.id)?.point }}
                                            </span>
                                            <span v-else class="text-gray-400">N/A</span>
                                        </span>
                                    </td>

                                    <!-- Total Points Column -->
                                    <td class="px-4 py-2 border-gray-300 dark:border-gray-600">
                                        {{ getStudentTotalPoints(student.id) }}
                                    </td>

                                    <!-- Grade Column -->
                                    <td class="px-4 py-2 border-gray-300 dark:border-gray-600">
                                        {{ getStudentGradeLetter(student.id) }}
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                    <tr>
                                        <td colspan="100%" class="px-4 py-3 text-center bg-gray-50 dark:bg-gray-800 border-t border-gray-300 dark:border-gray-600">
                                        <div class="relative group inline-block">
                                            <button
                                                v-if="activeWeightId"
                                                @click="submitWeightResults"
                                                class="text-sm px-4 py-2 rounded text-white bg-green-600 hover:bg-green-700 disabled:bg-gray-400 disabled:cursor-not-allowed"
                                            >
                                                Save Results
                                            </button>
                                        </div>
                                        </td>
                                    </tr>
                                </tfoot>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>
