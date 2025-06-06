<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { ref, onMounted } from "vue";
import { Chart, registerables } from "chart.js";
import {
    ChevronDownIcon,
    ChevronRightIcon,
    ChartBarIcon,
    DocumentArrowDownIcon,
} from "@heroicons/vue/24/outline";

// Expand/collapse logic for academic years
const expandedYears = ref({
    2024: false,
    2023: false,
    2022: false,
});

// Initialize chart.js
Chart.register(...registerables);

const renderChart = () => {
    const ctx = document.getElementById("academicChart").getContext("2d");
    new Chart(ctx, {
        type: "line",
        data: {
            labels: ["2021", "2022", "2023", "2024"],
            datasets: [
                {
                    label: "GPA",
                    data: [3.1, 3.4, 3.7, 3.5],
                    backgroundColor: "rgba(54, 162, 235, 0.2)",
                    borderColor: "rgba(54, 162, 235, 1)",
                    borderWidth: 2,
                    fill: true,
                },
            ],
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    labels: { color: "#4B5563" },
                },
            },
            scales: {
                x: {
                    ticks: { color: "#6B7280" },
                },
                y: {
                    beginAtZero: true,
                    ticks: { color: "#6B7280" },
                },
            },
        },
    });
};

// Mount the chart
onMounted(() => {
    renderChart();
});
</script>

<template>
    <AppLayout>
        <div class="max-w-7xl mx-auto py-10 px-4 space-y-8">
            <div class="flex justify-between items-center">
                <h1
                    class="text-3xl font-bold text-gray-800 dark:text-white flex items-center gap-2"
                >
                    <ChartBarIcon
                        class="w-8 h-8 text-blue-600 dark:text-blue-400"
                    />
                    Academic Results
                </h1>
                <button
                    class="inline-flex items-center gap-1 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg shadow-md transition"
                >
                    <DocumentArrowDownIcon class="w-5 h-5" />
                    Export PDF
                </button>
            </div>

            <!-- GPA Table -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow">
                <h2
                    class="text-xl font-semibold mb-4 text-gray-900 dark:text-white"
                >
                    GPA by Academic Year
                </h2>
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-gray-700">
                            <th class="px-4 py-2">Year</th>
                            <th class="px-4 py-2">GPA</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            class="border-t border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-900 transition"
                        >
                            <td class="px-4 py-2">2021</td>
                            <td
                                class="px-4 py-2 text-blue-600 dark:text-blue-400 font-semibold"
                            >
                                3.1
                            </td>
                        </tr>
                        <tr
                            class="border-t border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-900 transition"
                        >
                            <td class="px-4 py-2">2022</td>
                            <td
                                class="px-4 py-2 text-blue-600 dark:text-blue-400 font-semibold"
                            >
                                3.4
                            </td>
                        </tr>
                        <tr
                            class="border-t border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-900 transition"
                        >
                            <td class="px-4 py-2">2023</td>
                            <td
                                class="px-4 py-2 text-blue-600 dark:text-blue-400 font-semibold"
                            >
                                3.7
                            </td>
                        </tr>
                        <tr
                            class="border-t border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-900 transition"
                        >
                            <td class="px-4 py-2">2024</td>
                            <td
                                class="px-4 py-2 text-blue-600 dark:text-blue-400 font-semibold"
                            >
                                3.5
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Academic Chart -->
            <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow">
                <h2
                    class="text-xl font-semibold mb-4 text-gray-900 dark:text-white"
                >
                    GPA Trend
                </h2>
                <canvas id="academicChart" class="w-full h-64"></canvas>
            </div>

            <!-- Yearly Course Details -->
            <div class="space-y-4">
                <!-- 2024 -->
                <div
                    class="mt-2 bg-white dark:bg-gray-800 rounded-xl shadow hover:shadow-lg transition"
                >
                    <div
                        class="flex items-center justify-between px-6 py-4 cursor-pointer"
                        @click="expandedYears['2024'] = !expandedYears['2024']"
                    >
                        <h3
                            class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2"
                        >
                            <ChevronRightIcon
                                v-if="!expandedYears['2024']"
                                class="h-5 w-5 text-gray-600 dark:text-gray-300"
                            />
                            <ChevronDownIcon
                                v-else
                                class="h-5 w-5 text-gray-600 dark:text-gray-300"
                            />
                            Academic Year 2024
                        </h3>
                    </div>
                    <div
                        v-if="expandedYears['2024']"
                        class="border-t dark:border-gray-700 px-6 py-4"
                    >
                        <ul class="space-y-2">
                            <li class="flex justify-between items-center">
                                <span>AI Fundamentals</span>
                                <span
                                    class="inline-block px-2 py-0.5 rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300 font-semibold"
                                >
                                    A
                                </span>
                            </li>
                            <li class="flex justify-between items-center">
                                <span>Advanced Databases</span>
                                <span
                                    class="inline-block px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300 font-semibold"
                                >
                                    B+
                                </span>
                            </li>
                            <li class="flex justify-between items-center">
                                <span>Cloud Computing</span>
                                <span
                                    class="inline-block px-2 py-0.5 rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300 font-semibold"
                                >
                                    A-
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- 2023 -->
                <div
                    class="mt-2 bg-white dark:bg-gray-800 rounded-xl shadow hover:shadow-lg transition"
                >
                    <div
                        class="flex items-center justify-between px-6 py-4 cursor-pointer"
                        @click="expandedYears['2023'] = !expandedYears['2023']"
                    >
                        <h3
                            class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2"
                        >
                            <ChevronRightIcon
                                v-if="!expandedYears['2023']"
                                class="h-5 w-5 text-gray-600 dark:text-gray-300"
                            />
                            <ChevronDownIcon
                                v-else
                                class="h-5 w-5 text-gray-600 dark:text-gray-300"
                            />
                            Academic Year 2023
                        </h3>
                    </div>
                    <div
                        v-if="expandedYears['2023']"
                        class="border-t dark:border-gray-700 px-6 py-4"
                    >
                        <ul class="space-y-2">
                            <li class="flex justify-between items-center">
                                <span>Software Engineering</span>
                                <span
                                    class="inline-block px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300 font-semibold"
                                >
                                    B+
                                </span>
                            </li>
                            <li class="flex justify-between items-center">
                                <span>Computer Networks</span>
                                <span
                                    class="inline-block px-2 py-0.5 rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300 font-semibold"
                                >
                                    A
                                </span>
                            </li>
                            <li class="flex justify-between items-center">
                                <span>Cybersecurity</span>
                                <span
                                    class="inline-block px-2 py-0.5 rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300 font-semibold"
                                >
                                    A
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- 2022 -->
                <div
                    class="mt-2 bg-white dark:bg-gray-800 rounded-xl shadow hover:shadow-lg transition"
                >
                    <div
                        class="flex items-center justify-between px-6 py-4 cursor-pointer"
                        @click="expandedYears['2022'] = !expandedYears['2022']"
                    >
                        <h3
                            class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2"
                        >
                            <ChevronRightIcon
                                v-if="!expandedYears['2022']"
                                class="h-5 w-5 text-gray-600 dark:text-gray-300"
                            />
                            <ChevronDownIcon
                                v-else
                                class="h-5 w-5 text-gray-600 dark:text-gray-300"
                            />
                            Academic Year 2022
                        </h3>
                    </div>
                    <div
                        v-if="expandedYears['2022']"
                        class="border-t dark:border-gray-700 px-6 py-4"
                    >
                        <ul class="space-y-2">
                            <li class="flex justify-between items-center">
                                <span>Algorithms</span>
                                <span
                                    class="inline-block px-2 py-0.5 rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300 font-semibold"
                                >
                                    A-
                                </span>
                            </li>
                            <li class="flex justify-between items-center">
                                <span>Operating Systems</span>
                                <span
                                    class="inline-block px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300 font-semibold"
                                >
                                    B
                                </span>
                            </li>
                            <li class="flex justify-between items-center">
                                <span>Database Systems</span>
                                <span
                                    class="inline-block px-2 py-0.5 rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300 font-semibold"
                                >
                                    A
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
