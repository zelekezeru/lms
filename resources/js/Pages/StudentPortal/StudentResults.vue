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
    const ctx = document.getElementById("resultsChart");
    if (!ctx) return;

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

    chart = new Chart(ctx, {
        type: "bar",
        data: {
            labels,
            datasets: [
                {
                    label: "GPA",
                    data,
                    backgroundColor: data.map((v) =>
                        v >= 3.5 ? "rgba(16,185,129,0.7)" : v >= 2.5 ? "rgba(99,102,241,0.7)" : "rgba(245,158,11,0.7)"
                    ),
                    borderColor: data.map((v) =>
                        v >= 3.5 ? "rgb(16,185,129)" : v >= 2.5 ? "rgb(99,102,241)" : "rgb(245,158,11)"
                    ),
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: (ctx) => ` GPA: ${ctx.raw}`,
                    },
                },
            },
            scales: {
                y: { beginAtZero: true, max: 4.2, ticks: { color: "#9ca3af", stepSize: 1 }, grid: { color: "rgba(156,163,175,0.15)" }, border: { display: false } },
                x: { ticks: { color: "#9ca3af" }, grid: { display: false }, border: { display: false } },
            },
        },
    });
});
</script>

<template>
    <AppLayout>
        <div class="pb-24 md:pb-8 max-w-3xl mx-auto px-4 md:px-6 pt-4 space-y-5">

            <!-- Header -->
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center">
                        <ChartBarIcon class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900 dark:text-white">Academic Results</h1>
                        <p class="text-xs text-gray-500 dark:text-gray-400">GPA performance overview</p>
                    </div>
                </div>
            </div>

            <!-- Chart card -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-5">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-bold text-gray-900 dark:text-white">GPA by Year</h2>
                    <div class="flex items-center gap-3 text-xs text-gray-400">
                        <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-sm bg-emerald-400 inline-block"></span> 3.5+</span>
                        <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-sm bg-indigo-500 inline-block"></span> 2.5+</span>
                        <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-sm bg-amber-400 inline-block"></span> Below</span>
                    </div>
                </div>
                <div class="h-52">
                    <canvas id="resultsChart"></canvas>
                </div>
            </div>

            <!-- Yearly accordion -->
            <div class="space-y-3">
                <div
                    v-for="([year, grades], idx) in grouped"
                    :key="year"
                    class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden"
                >
                    <button
                        @click="openYear = openYear === year ? null : year"
                        class="w-full flex items-center justify-between px-5 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition"
                    >
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center shrink-0">
                                <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400">{{ year.slice(-4) }}</span>
                            </div>
                            <div class="text-left">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ year }}</p>
                                <p class="text-xs text-gray-400">{{ grades.length }} course{{ grades.length !== 1 ? 's' : '' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <ChevronDownIcon
                                :class="['w-4 h-4 text-gray-400 transition-transform duration-200', openYear === year ? 'rotate-180' : '']"
                            />
                        </div>
                    </button>

                    <!-- Course list -->
                    <transition
                        enter-active-class="transition duration-200 ease-out"
                        enter-from-class="opacity-0"
                        enter-to-class="opacity-100"
                        leave-active-class="transition duration-150"
                        leave-from-class="opacity-100"
                        leave-to-class="opacity-0"
                    >
                        <div v-if="openYear === year" class="border-t border-gray-100 dark:border-gray-700 divide-y divide-gray-100 dark:divide-gray-700">
                            <div
                                v-for="grade in grades"
                                :key="grade.id"
                                class="flex items-center justify-between px-5 py-3"
                            >
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white line-clamp-1">{{ grade.course?.name }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ grade.course?.code }} · {{ grade.course?.credit_hours }} cr</p>
                                </div>
                                <span :class="['text-xs font-bold px-2.5 py-1 rounded-lg ml-3 shrink-0', gradeLetterColor(grade.grade_letter)]">
                                    {{ grade.grade_letter ?? '—' }}
                                </span>
                            </div>
                        </div>
                    </transition>
                </div>

                <!-- Empty -->
                <div v-if="!grouped.length" class="text-center py-12">
                    <div class="w-14 h-14 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center mx-auto mb-4">
                        <ChartBarIcon class="w-7 h-7 text-gray-400" />
                    </div>
                    <p class="text-sm text-gray-500 font-medium">No results yet</p>
                </div>
            </div>

        </div>
    </AppLayout>
</template>
