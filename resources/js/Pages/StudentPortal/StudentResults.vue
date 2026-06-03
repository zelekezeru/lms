<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { ref, computed, onMounted } from "vue";
import { Chart, registerables } from "chart.js";
import {
    ChevronDownIcon,
    ChevronRightIcon,
    ChartBarIcon,
    AcademicCapIcon,
    BookOpenIcon,
    TrophyIcon,
} from "@heroicons/vue/24/outline";

const props = defineProps({
    student: {
        type: Object,
        required: true,
    },
});

// Initialize chart.js
Chart.register(...registerables);

// Group grades by Academic Year dynamically
const groupedResults = computed(() => {
    if (!props.student.grades || props.student.grades.length === 0) {
        return [];
    }

    const yearMap = {};
    props.student.grades.forEach((grade) => {
        if (!grade.semester) return;
        const yearName = grade.semester.year?.name || "Unknown Year";
        if (!yearMap[yearName]) {
            yearMap[yearName] = {
                year: yearName,
                grades: [],
            };
        }
        yearMap[yearName].grades.push(grade);
    });

    // Helper to convert percentage to 4.0 point
    function getGradePointFromLetter(point) {
        point = parseFloat(point);
        if (isNaN(point)) return 0;
        if (point >= 94) return 4.0;
        if (point >= 90) return 3.7;
        if (point >= 87) return 3.3;
        if (point >= 84) return 3.0;
        if (point >= 80) return 2.7;
        if (point >= 77) return 2.3;
        if (point >= 74) return 2.0;
        if (point >= 70) return 1.7;
        if (point >= 67) return 1.3;
        if (point >= 64) return 1.0;
        if (point >= 60) return 0.7;
        return 0.0;
    }

    const resultList = Object.values(yearMap).map((yearData) => {
        let totalPoints = 0;
        let totalCredits = 0;
        yearData.grades.forEach((grade) => {
            const gp = getGradePointFromLetter(parseFloat(grade.grade_point) || 0);
            const credit = parseFloat(grade.course?.credit_hours || 0);
            totalPoints += gp * credit;
            totalCredits += credit;
        });
        const gpa = totalCredits > 0 ? totalPoints / totalCredits : 0;
        return {
            year: yearData.year,
            gpa: parseFloat(gpa.toFixed(2)),
            grades: yearData.grades,
        };
    });

    // Sort by year name chronologically
    return resultList.sort((a, b) => a.year.localeCompare(b.year));
});

// Expand/collapse logic for academic years
const expandedYears = ref({});
const toggleYear = (year) => {
    expandedYears.value[year] = !expandedYears.value[year];
};

let chartInstance = null;
const renderChart = () => {
    const ctx = document.getElementById("academicChart")?.getContext("2d");
    if (!ctx) return;

    const dataPoints = groupedResults.value;
    const labels =
        dataPoints.length > 0 ? dataPoints.map((d) => d.year) : ["No Records"];
    const data =
        dataPoints.length > 0 ? dataPoints.map((d) => d.gpa) : [0.0];

    chartInstance = new Chart(ctx, {
        type: "line",
        data: {
            labels: labels,
            datasets: [
                {
                    label: "GPA",
                    data: data,
                    backgroundColor: "rgba(99, 102, 241, 0.06)",
                    borderColor: "rgba(99, 102, 241, 1)",
                    borderWidth: 3,
                    pointBackgroundColor: "rgba(99, 102, 241, 1)",
                    pointBorderColor: "#ffffff",
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                    pointHoverBorderWidth: 3,
                    fill: true,
                    tension: 0.35,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false,
                },
            },
            scales: {
                x: {
                    ticks: {
                        color: "#9CA3AF",
                        font: { family: "Outfit, Inter, sans-serif" }
                    },
                    grid: { display: false },
                },
                y: {
                    beginAtZero: true,
                    max: 4.0,
                    ticks: {
                        color: "#9CA3AF",
                        stepSize: 0.5,
                        font: { family: "Outfit, Inter, sans-serif" }
                    },
                    grid: { color: "rgba(156, 163, 175, 0.08)" },
                },
            },
        },
    });
};

// Mount the chart
onMounted(() => {
    renderChart();
    if (groupedResults.value.length > 0) {
        expandedYears.value[groupedResults.value[0].year] = true;
    }
});
</script>

<template>
    <AppLayout>
        <div class="max-w-7xl mx-auto py-10 px-4 space-y-8">
            <div class="flex justify-between items-center">
                <h1
                    class="text-3xl font-black text-gray-900 dark:text-white flex items-center gap-2 tracking-tight"
                >
                    <ChartBarIcon
                        class="w-8 h-8 text-indigo-650 dark:text-indigo-400"
                    />
                    Academic Results
                </h1>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- GPA Table (Left 1 col) -->
                <div class="bg-white/70 dark:bg-gray-800/70 backdrop-blur-md rounded-3xl border border-white/20 dark:border-gray-700/50 shadow-sm p-6 lg:col-span-1 space-y-4">
                    <h2
                        class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2"
                    >
                        <TrophyIcon class="w-5 h-5 text-indigo-500" />
                        GPA Averages
                    </h2>
                    <div class="overflow-hidden rounded-2xl border border-gray-100 dark:border-gray-700/60">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700/30">
                                    <th class="px-4 py-3.5 font-semibold text-xs text-gray-500 uppercase tracking-wider">Year</th>
                                    <th class="px-4 py-3.5 font-semibold text-xs text-gray-500 uppercase tracking-wider text-right">GPA</th>
                                </tr>
                            </thead>
                            <tbody v-if="groupedResults.length > 0">
                                <tr
                                    v-for="res in groupedResults"
                                    :key="res.year"
                                    class="border-t border-gray-100 dark:border-gray-700/60 hover:bg-gray-55/30 dark:hover:bg-gray-700/10 transition duration-150"
                                >
                                    <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200 font-bold">{{ res.year }}</td>
                                    <td
                                        class="px-4 py-3 text-sm text-indigo-600 dark:text-indigo-400 font-black text-right"
                                    >
                                        {{ res.gpa.toFixed(2) }}
                                    </td>
                                </tr>
                            </tbody>
                            <tbody v-else>
                                <tr>
                                    <td colspan="2" class="text-center py-6 text-sm text-gray-500 dark:text-gray-400">
                                        No academic records.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Academic Chart (Right 2 cols) -->
                <div class="bg-white/70 dark:bg-gray-800/70 backdrop-blur-md p-6 rounded-3xl border border-white/20 dark:border-gray-700/50 shadow-sm lg:col-span-2 space-y-4">
                    <h2
                        class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2"
                    >
                        <ChartBarIcon class="w-5 h-5 text-purple-500" />
                        GPA Performance Trend
                    </h2>
                    <div class="h-64 w-full">
                        <canvas id="academicChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Yearly Course Details Accordion -->
            <div class="space-y-4">
                <h2 class="text-xl font-black text-gray-850 dark:text-white tracking-tight flex items-center gap-2">
                    <span class="w-2.5 h-6 bg-indigo-650 rounded-full"></span>
                    Course Breakdown
                </h2>
                
                <div v-if="groupedResults.length > 0" class="space-y-4">
                    <div
                        v-for="res in groupedResults"
                        :key="res.year"
                        class="bg-white/70 dark:bg-gray-800/70 backdrop-blur-md rounded-3xl border border-white/20 dark:border-gray-700/50 shadow-sm overflow-hidden"
                    >
                        <div
                            class="flex items-center justify-between px-6 py-4 cursor-pointer hover:bg-gray-50/40 dark:hover:bg-gray-700/20 transition duration-150"
                            @click="toggleYear(res.year)"
                        >
                            <h3
                                class="text-base font-bold text-gray-950 dark:text-white flex items-center gap-2"
                            >
                                <component
                                    :is="expandedYears[res.year] ? ChevronDownIcon : ChevronRightIcon"
                                    class="h-5 w-5 text-gray-450"
                                />
                                Academic Year {{ res.year }}
                            </h3>
                            <span class="text-xs bg-indigo-50 dark:bg-indigo-950/40 text-indigo-650 dark:text-indigo-400 font-black px-3.5 py-1.5 rounded-full border border-indigo-100/50 dark:border-indigo-900/30">
                                Annual GPA: {{ res.gpa.toFixed(2) }}
                            </span>
                        </div>
                        
                        <!-- Smooth Accordion Slide Transition -->
                        <div
                            v-if="expandedYears[res.year]"
                            class="border-t border-gray-100 dark:border-gray-750 px-6 py-4 bg-gray-50/30 dark:bg-gray-900/20"
                        >
                            <div class="overflow-x-auto">
                                <table class="w-full text-left">
                                    <thead>
                                        <tr class="text-xs font-semibold text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700 pb-2">
                                            <th class="pb-3">Course Title</th>
                                            <th class="pb-3">Semester Info</th>
                                            <th class="pb-3">Credit Hours</th>
                                            <th class="pb-3 text-right">Grade Scale</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                                        <tr
                                            v-for="grade in res.grades"
                                            :key="grade.id"
                                            class="text-sm text-gray-800 dark:text-gray-200"
                                        >
                                            <td class="py-4 font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                                <div class="p-1.5 bg-indigo-50 dark:bg-indigo-950/40 text-indigo-650 dark:text-indigo-400 rounded-lg">
                                                    <BookOpenIcon class="w-4 h-4" />
                                                </div>
                                                <div>
                                                    <span>{{ grade.course.name }}</span>
                                                    <span class="text-xs text-gray-400 font-mono block">{{ grade.course.code }}</span>
                                                </div>
                                            </td>
                                            <td class="py-4 text-gray-650 dark:text-gray-300">
                                                {{ grade.semester?.name || 'N/A' }}
                                            </td>
                                            <td class="py-4 text-gray-650 dark:text-gray-300">
                                                {{ grade.course.credit_hours }} credits
                                            </td>
                                            <td class="py-4 text-right">
                                                <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-300 border border-indigo-100/50 dark:border-indigo-900/30">
                                                    {{ grade.grade_letter }}
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div v-else class="bg-white/70 dark:bg-gray-800/70 backdrop-blur-md p-10 rounded-3xl text-center text-gray-500 dark:text-gray-400 border border-white/20 dark:border-gray-700/50 shadow-sm">
                    No course breakdowns available.
                </div>
            </div>
        </div>
    </AppLayout>
</template>
