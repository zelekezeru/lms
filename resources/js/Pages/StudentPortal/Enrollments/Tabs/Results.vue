<script setup>
import {
    CheckBadgeIcon,
    CogIcon,
    DocumentTextIcon,
    PresentationChartBarIcon,
    ViewColumnsIcon,
    ListBulletIcon,
} from "@heroicons/vue/24/outline";
import { ref } from "vue";

const props = defineProps({
    enrollment: {
        type: Object,
        required: true,
    },
    student: {
        type: Object,
        required: true,
    },
    weights: {
        type: Array,
        required: true,
    },
});

const selectedTab = ref("results");
const viewMode = ref("card"); // Default to "card"

const changeTab = (tabKey) => {
    selectedTab.value = tabKey;
    route().params.assessmentTab = tabKey;
};

const getProgressColor = (mark) => {
    if (mark >= 90) return "bg-green-500";
    if (mark >= 75) return "bg-yellow-500";
    return "bg-red-500";
};

const toggleView = () => {
    viewMode.value = viewMode.value === "card" ? "list" : "card";
};
</script>

<template>
    <div class="mt-4">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">
                Student Assessments
            </h2>
            <button
                @click="toggleView"
                class="inline-flex items-center px-3 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-sm text-gray-800 dark:text-white rounded-lg transition"
            >
                <component
                    :is="viewMode === 'card' ? ListBulletIcon : ViewColumnsIcon"
                    class="w-5 h-5 mr-2"
                />
                {{ viewMode === "card" ? "List View" : "Card View" }}
            </button>
        </div>

        <!-- Transition Between Views -->
        <transition
            mode="out-in"
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
        >
            <!-- KEY ensures transition on mode change -->
            <div :key="viewMode">
                <!-- Card View -->
                <div
                    v-if="viewMode === 'card'"
                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6"
                >
                    <div
                        v-for="weight in weights"
                        :key="weight.id"
                        class="transition duration-300 transform hover:-translate-y-1 hover:shadow-xl bg-gray-100 dark:bg-gray-800 rounded-xl p-6 space-y-4"
                    >
                        <h3
                            class="text-lg font-semibold text-gray-900 dark:text-white"
                        >
                            {{ weight.name }}
                        </h3>

                        <p class="text-sm text-gray-700 dark:text-gray-300">
                            {{
                                weight.description || "No description provided."
                            }}
                        </p>

                        <div>
                            <div
                                class="flex items-center justify-between mb-2 text-sm"
                            >
                                <span class="text-gray-600 dark:text-gray-300">
                                    Score:
                                    <strong>
                                        {{
                                            weight.results[0]
                                                ? weight.results[0].point
                                                : "Pending"
                                        }}
                                    </strong>
                                </span>
                                <span
                                    class="text-gray-700 dark:text-white font-medium"
                                >
                                    Out of {{ weight.point }}
                                </span>
                            </div>

                            <div
                                class="w-full bg-gray-300 dark:bg-gray-600 rounded-full h-2"
                            >
                                <div
                                    :style="{
                                        width:
                                            (weight.results[0]
                                                ? (weight.results[0].point *
                                                      100) /
                                                  weight.point
                                                : 1) + '%',
                                    }"
                                    class="h-2 rounded-full transition-all duration-300"
                                    :class="
                                        getProgressColor(
                                            weight.results[0]
                                                ? weight.results[0].point
                                                : 0
                                        )
                                    "
                                ></div>
                            </div>
                        </div>

                        <div class="text-right">
                            <button
                                class="mt-4 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg shadow"
                            >
                                View Details
                            </button>
                        </div>
                    </div>
                </div>

                <!-- List View -->
                <div v-else>
                    <table
                        class="min-w-full bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow-md"
                    >
                        <thead
                            class="bg-gray-200 dark:bg-gray-700 text-left text-sm uppercase text-gray-700 dark:text-gray-200"
                        >
                            <tr>
                                <th class="px-4 py-3">Assessment</th>
                                <th class="px-4 py-3">Description</th>
                                <th class="px-4 py-3">Score</th>
                                <th class="px-4 py-3">Out Of</th>
                                <th class="px-4 py-3">Progress</th>
                                <th class="px-4 py-3 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="weight in weights"
                                :key="weight.id"
                                class="border-t border-gray-200 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm text-gray-900 dark:text-gray-100"
                            >
                                <td class="px-4 py-3 font-medium">
                                    {{ weight.name }}
                                </td>
                                <td class="px-4 py-3">
                                    {{ weight.description || "—" }}
                                </td>
                                <td class="px-4 py-3">
                                    {{
                                        weight.results[0]
                                            ? weight.results[0].point
                                            : "Pending"
                                    }}
                                </td>
                                <td class="px-4 py-3">{{ weight.point }}</td>
                                <td class="px-4 py-3 w-48">
                                    <div
                                        class="w-full bg-gray-300 dark:bg-gray-600 rounded-full h-2"
                                    >
                                        <div
                                            :style="{
                                                width:
                                                    (weight.results[0]
                                                        ? (weight.results[0]
                                                              .point *
                                                              100) /
                                                          weight.point
                                                        : 1) + '%',
                                            }"
                                            class="h-2 rounded-full transition-all duration-300"
                                            :class="
                                                getProgressColor(
                                                    weight.results[0]
                                                        ? weight.results[0]
                                                              .point
                                                        : 0
                                                )
                                            "
                                        ></div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <button
                                        class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded-lg shadow"
                                    >
                                        View
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </transition>
    </div>
</template>
