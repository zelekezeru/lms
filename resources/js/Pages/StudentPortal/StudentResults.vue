<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { ref, onMounted } from "vue";
import { Chart, registerables } from "chart.js";
import {
    ChartBarIcon,
    ChevronDownIcon,
    DocumentArrowDownIcon,
} from "@heroicons/vue/24/outline";

Chart.register(...registerables);

const props = defineProps({
    student: { type: Object, required: false },
    grades: { type: Array, default: () => [] },
});

const openYear = ref(null);

// Group grades by academic year
const grouped = (() => {
    const map = {};
    (props.grades ?? []).forEach((g) => {
        const yr = g.semester?.year?.name ?? "Unknown Year";
        if (!map[yr]) map[yr] = [];
        map[yr].push(g);
    });
    return Object.entries(map).sort(([a], [b]) => (a < b ? -1 : 1));
})();

const gradeLetterColor = (letter) => {
    if (!letter) return "bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400";
    const l = letter.toUpperCase();
    if (l === "A" || l === "A+") return "bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400";
    if (l.startsWith("A")) return "bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400";
    if (l.startsWith("B")) return "bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400";
    if (l.startsWith("C")) return "bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400";
    return "bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400";
};

let chart = null;

onMounted(() => {
    const canvas = document.getElementById("resultsChart");
    if (!canvas) return;
    const ctx = canvas.getContext("2d");

    const labels = grouped.map(([yr]) => yr);
    const data = grouped.map(([, grades]) => {
        let pts = 0, creds = 0;
        grades.forEach((g) => {
            const gp = parseFloat(g.grade_point ?? 0);
            const cr = parseFloat(g.course?.credit_hours ?? 0);
            pts += gp * cr; creds += cr;
        });
        return creds > 0 ? parseFloat((pts / creds).toFixed(2)) : 0;
    });

    // Create custom linear gradients for bars
    const getBarColor = (val) => {
        if (val >= 3.5) {
            const grad = ctx.createLinearGradient(0, 0, 0, 180);
            grad.addColorStop(0, "rgba(16, 185, 129, 0.85)");
            grad.addColorStop(1, "rgba(16, 185, 129, 0.15)");
            return grad;
        } else if (val >= 2.5) {
            const grad = ctx.createLinearGradient(0, 0, 0, 180);
            grad.addColorStop(0, "rgba(99, 102, 241, 0.85)");
            grad.addColorStop(1, "rgba(99, 102, 241, 0.15)");
            return grad;
        } else {
            const grad = ctx.createLinearGradient(0, 0, 0, 180);
            grad.addColorStop(0, "rgba(245, 158, 11, 0.85)");
            grad.addColorStop(1, "rgba(245, 158, 11, 0.15)");
            return grad;
        }
    };

    chart = new Chart(ctx, {
        type: "bar",
        data: {
            labels,
            datasets: [
                {
                    label: "GPA",
                    data,
                    backgroundColor: data.map(v => getBarColor(v)),
                    borderColor: data.map((v) =>
                        v >= 3.5 ? "rgb(16,185,129)" : v >= 2.5 ? "rgb(99,102,241)" : "rgb(245,158,11)"
                    ),
                    borderWidth: 1.5,
                    borderRadius: 12,
                    borderSkipped: false,
                    barPercentage: 0.45,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: "rgba(17, 24, 39, 0.95)",
                    titleFont: { size: 11, weight: "bold" },
                    bodyFont: { size: 12, weight: "600" },
                    padding: 10,
                    cornerRadius: 12,
                    displayColors: true,
                    callbacks: {
                        label: (ctx) => ` GPA Score: ${ctx.raw}`,
                    },
                },
            },
            scales: {
                y: { 
                    beginAtZero: true, 
                    max: 4.2, 
                    ticks: { 
                        color: "#9ca3af", 
                        stepSize: 1,
                        font: { size: 10 }
                    }, 
                    grid: { 
                        color: "rgba(156,163,175,0.08)" 
                    }, 
                    border: { display: false } 
                },
                x: { 
                    ticks: { 
                        color: "#9ca3af",
                        font: { size: 10 }
                    }, 
                    grid: { display: false }, 
                    border: { display: false } 
                },
            },
        },
    });
});
</script>

<template>
    <AppLayout>
        <div class="pb-24 md:pb-8 max-w-3xl mx-auto px-4 md:px-6 pt-6 space-y-6">

            <!-- Header -->
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-950/30 border border-indigo-100 dark:border-indigo-900/50 flex items-center justify-center">
                        <ChartBarIcon class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900 dark:text-white">Academic Results</h1>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Yearly GPA performance overview</p>
                    </div>
                </div>
            </div>

            <!-- Chart Card -->
            <div class="bg-white dark:bg-gray-800/80 backdrop-blur-md rounded-2xl border border-gray-150/80 dark:border-gray-700/80 shadow-sm p-5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-6">
                    <h2 class="text-sm font-bold text-gray-800 dark:text-gray-200 uppercase tracking-wider">GPA Trajectory</h2>
                    <div class="flex flex-wrap items-center gap-3 text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wide">
                        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-400 inline-block shadow-sm"></span> A- / A / A+ (3.5+)</span>
                        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-indigo-500 inline-block shadow-sm"></span> B Range (2.5+)</span>
                        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-amber-400 inline-block shadow-sm"></span> C / D / F (&lt; 2.5)</span>
                    </div>
                </div>
                <div class="h-56">
                    <canvas id="resultsChart"></canvas>
                </div>
            </div>

            <!-- Yearly Accordion -->
            <div class="space-y-3">
                <div
                    v-for="([year, grades], idx) in grouped"
                    :key="year"
                    class="bg-white dark:bg-gray-800/90 rounded-2xl border border-gray-150/80 dark:border-gray-750/70 shadow-sm overflow-hidden transition-all duration-250"
                >
                    <button
                        @click="openYear = openYear === year ? null : year"
                        class="w-full flex items-center justify-between px-5 py-4 hover:bg-gray-50/50 dark:hover:bg-gray-750/30 transition text-left"
                    >
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center shrink-0 border border-indigo-100/50 dark:border-indigo-900/40">
                                <span class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400">{{ year.slice(-4) }}</span>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ year }}</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ grades.length }} course{{ grades.length !== 1 ? 's' : '' }} enrolled</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <ChevronDownIcon
                                :class="['w-4 h-4 text-gray-400 transition-transform duration-250', openYear === year ? 'rotate-180 text-indigo-500' : '']"
                            />
                        </div>
                    </button>

                    <!-- Course List Details -->
                    <transition
                        enter-active-class="transition duration-200 ease-out"
                        enter-from-class="opacity-0"
                        enter-to-class="opacity-100"
                        leave-active-class="transition duration-150 ease-in"
                        leave-from-class="opacity-100"
                        leave-to-class="opacity-0"
                    >
                        <div v-if="openYear === year" class="border-t border-gray-100 dark:border-gray-700 divide-y divide-gray-100 dark:divide-gray-700 bg-gray-50/10 dark:bg-gray-800/40">
                            <div
                                v-for="grade in grades"
                                :key="grade.id"
                                class="flex items-center justify-between px-5 py-3.5 hover:bg-gray-50/45 dark:hover:bg-gray-700/20 transition"
                            >
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ grade.course?.name }}</p>
                                    <p class="text-xs text-gray-400 dark:text-gray-550 mt-1 flex items-center gap-1.5">
                                        <span class="font-medium bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded text-gray-600 dark:text-gray-450">{{ grade.course?.code }}</span>
                                        <span>·</span>
                                        <span>{{ grade.course?.credit_hours }} Credit Hours</span>
                                    </p>
                                </div>
                                <span :class="['text-xs font-extrabold px-3 py-1 rounded-xl shadow-sm border', gradeLetterColor(grade.grade_letter)]">
                                    {{ grade.grade_letter ?? '—' }}
                                </span>
                            </div>
                        </div>
                    </transition>
                </div>

                <!-- Empty State -->
                <div v-if="!grouped.length" class="text-center py-16 bg-white dark:bg-gray-805 rounded-2xl border border-dashed border-gray-200 dark:border-gray-700">
                    <div class="w-14 h-14 rounded-2xl bg-gray-50 dark:bg-gray-800 flex items-center justify-center mx-auto mb-4 border border-gray-100 dark:border-gray-750">
                        <ChartBarIcon class="w-6 h-6 text-gray-450" />
                    </div>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">No Results Recorded</p>
                    <p class="text-xs text-gray-400 mt-1">Your academic performance logs are currently empty.</p>
                </div>
            </div>

        </div>
    </AppLayout>
</template>
