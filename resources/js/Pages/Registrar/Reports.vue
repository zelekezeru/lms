<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { ref, onMounted } from "vue";
import { Link } from "@inertiajs/vue3";
import {
    PrinterIcon,
    ArrowDownTrayIcon,
    UsersIcon,
    AcademicCapIcon,
    BanknotesIcon,
    GiftIcon,
    PresentationChartLineIcon,
    BuildingOffice2Icon,
    BookOpenIcon,
    CheckCircleIcon,
} from "@heroicons/vue/24/outline";
import Chart from "chart.js/auto";

const props = defineProps({
    totals: { type: Object, default: () => ({}) },
    enrollmentTrend: { type: Object, default: () => ({ labels: [], data: [] }) },
    programDistribution: { type: Object, default: () => ({ labels: [], data: [] }) },
    studyModeDistribution: { type: Object, default: () => ({ labels: [], data: [] }) },
    genderDistribution: { type: Object, default: () => ({ labels: [], data: [] }) },
    yearDistribution: { type: Object, default: () => ({ labels: [], data: [] }) },
    generatedAt: { type: String, default: "" },
});

const trendChart = ref(null);
const programChart = ref(null);
const studyModeChart = ref(null);
const genderChart = ref(null);

const palette = [
    "rgba(59,130,246,0.7)",
    "rgba(34,197,94,0.7)",
    "rgba(139,92,246,0.7)",
    "rgba(234,179,8,0.7)",
    "rgba(244,63,94,0.7)",
    "rgba(14,165,233,0.7)",
    "rgba(249,115,22,0.7)",
    "rgba(20,184,166,0.7)",
];

const summaryCards = [
    { label: "Total Students", value: props.totals.students, icon: UsersIcon, color: "text-blue-600" },
    { label: "Active", value: props.totals.active, icon: CheckCircleIcon, color: "text-green-600" },
    { label: "Graduated", value: props.totals.graduated, icon: AcademicCapIcon, color: "text-indigo-600" },
    { label: "Scholarship", value: props.totals.scholarship, icon: GiftIcon, color: "text-pink-600" },
    { label: "Pending Payment", value: props.totals.pendingPayment, icon: BanknotesIcon, color: "text-amber-600" },
    { label: "Courses", value: props.totals.courses, icon: BookOpenIcon, color: "text-emerald-600" },
    { label: "Instructors", value: props.totals.instructors, icon: PresentationChartLineIcon, color: "text-sky-600" },
    { label: "Centers", value: props.totals.centers, icon: BuildingOffice2Icon, color: "text-orange-600" },
];

const total = (arr) => (arr || []).reduce((a, b) => a + b, 0);
const pct = (value, arr) => {
    const t = total(arr);
    return t ? Math.round((value / t) * 100) : 0;
};

const buildTable = (dist) =>
    (dist.labels || []).map((label, i) => ({
        label,
        value: dist.data[i] || 0,
        pct: pct(dist.data[i] || 0, dist.data),
    }));

onMounted(() => {
    if (trendChart.value) {
        new Chart(trendChart.value, {
            type: "line",
            data: {
                labels: props.enrollmentTrend.labels,
                datasets: [
                    {
                        label: "New Students",
                        data: props.enrollmentTrend.data,
                        borderColor: "rgba(37,99,235,1)",
                        backgroundColor: "rgba(37,99,235,0.15)",
                        fill: true,
                        tension: 0.35,
                        pointRadius: 3,
                    },
                ],
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
            },
        });
    }

    if (programChart.value) {
        new Chart(programChart.value, {
            type: "bar",
            data: {
                labels: props.programDistribution.labels,
                datasets: [{ label: "Students", data: props.programDistribution.data, backgroundColor: palette, borderRadius: 6 }],
            },
            options: {
                indexAxis: "y",
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { x: { beginAtZero: true, ticks: { precision: 0 } } },
            },
        });
    }

    if (studyModeChart.value) {
        new Chart(studyModeChart.value, {
            type: "doughnut",
            data: {
                labels: props.studyModeDistribution.labels,
                datasets: [{ data: props.studyModeDistribution.data, backgroundColor: palette, borderWidth: 0 }],
            },
            options: { responsive: true, plugins: { legend: { position: "bottom" } } },
        });
    }

    if (genderChart.value) {
        new Chart(genderChart.value, {
            type: "pie",
            data: {
                labels: props.genderDistribution.labels,
                datasets: [{ data: props.genderDistribution.data, backgroundColor: palette, borderWidth: 0 }],
            },
            options: { responsive: true, plugins: { legend: { position: "bottom" } } },
        });
    }
});

// Export each distribution to CSV
const exportCsv = () => {
    const sections = [
        ["Program Distribution"],
        ["Program", "Students"],
        ...props.programDistribution.labels.map((l, i) => [l, props.programDistribution.data[i]]),
        [],
        ["Study Mode Distribution"],
        ["Study Mode", "Students"],
        ...props.studyModeDistribution.labels.map((l, i) => [l, props.studyModeDistribution.data[i]]),
        [],
        ["Year Distribution"],
        ["Year", "Students"],
        ...props.yearDistribution.labels.map((l, i) => [l, props.yearDistribution.data[i]]),
        [],
        ["Gender Distribution"],
        ["Gender", "Students"],
        ...props.genderDistribution.labels.map((l, i) => [l, props.genderDistribution.data[i]]),
    ];
    const csv = sections
        .map((row) => row.map((cell) => `"${String(cell ?? "").replace(/"/g, '""')}"`).join(","))
        .join("\n");
    const blob = new Blob([csv], { type: "text/csv;charset=utf-8;" });
    const link = document.createElement("a");
    link.href = URL.createObjectURL(blob);
    link.download = `registrar-report-${new Date().toISOString().slice(0, 10)}.csv`;
    link.click();
    URL.revokeObjectURL(link.href);
};

const printReport = () => window.print();
</script>

<template>
    <AppLayout>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Registrar Reports</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">Generated {{ generatedAt }}</p>
            </div>
            <div class="flex flex-wrap gap-3 print:hidden">
                <Link
                    :href="route('registrar.students')"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-white hover:bg-gray-300 dark:hover:bg-gray-600 transition"
                >
                    <UsersIcon class="w-5 h-5" /> Student Directory
                </Link>
                <button
                    @click="exportCsv"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-green-600 text-white hover:bg-green-700 transition"
                >
                    <ArrowDownTrayIcon class="w-5 h-5" /> Export CSV
                </button>
                <button
                    @click="printReport"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-blue-800 text-white hover:bg-blue-700 transition"
                >
                    <PrinterIcon class="w-5 h-5" /> Print
                </button>
            </div>
        </div>

        <!-- Summary cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 xl:grid-cols-8 gap-4 mb-8">
            <div
                v-for="card in summaryCards"
                :key="card.label"
                class="bg-white dark:bg-gray-800 p-4 rounded-2xl shadow flex flex-col gap-1"
            >
                <component :is="card.icon" class="w-6 h-6" :class="card.color" />
                <span class="text-xl font-bold text-gray-900 dark:text-white">{{ card.value ?? 0 }}</span>
                <span class="text-xs text-gray-500 dark:text-gray-400 leading-tight">{{ card.label }}</span>
            </div>
        </div>

        <!-- Enrollment trend -->
        <section class="bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-lg mb-8">
            <h3 class="font-bold text-gray-900 dark:text-white mb-4">Enrollment Trend (Last 12 months)</h3>
            <canvas ref="trendChart" class="w-full" style="max-height: 320px"></canvas>
        </section>

        <!-- Distribution charts -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <section class="bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-lg lg:col-span-1">
                <h3 class="font-bold text-gray-900 dark:text-white mb-4">By Study Mode</h3>
                <canvas ref="studyModeChart" class="w-full" style="max-height: 280px"></canvas>
            </section>
            <section class="bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-lg lg:col-span-1">
                <h3 class="font-bold text-gray-900 dark:text-white mb-4">By Gender</h3>
                <canvas ref="genderChart" class="w-full" style="max-height: 280px"></canvas>
            </section>
            <section class="bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-lg lg:col-span-1">
                <h3 class="font-bold text-gray-900 dark:text-white mb-4">By Program</h3>
                <canvas ref="programChart" class="w-full" style="max-height: 280px"></canvas>
            </section>
        </div>

        <!-- Breakdown tables -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <section class="bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-lg">
                <h3 class="font-bold text-gray-900 dark:text-white mb-4">Students by Program</h3>
                <table class="min-w-full text-sm">
                    <thead class="text-left text-gray-500 dark:text-gray-400 border-b dark:border-gray-700">
                        <tr><th class="py-2">Program</th><th class="py-2 text-right">Students</th><th class="py-2 text-right">%</th></tr>
                    </thead>
                    <tbody>
                        <tr v-for="row in buildTable(programDistribution)" :key="row.label" class="border-b dark:border-gray-700/60">
                            <td class="py-2 text-gray-800 dark:text-gray-200">{{ row.label }}</td>
                            <td class="py-2 text-right font-medium text-gray-900 dark:text-white">{{ row.value }}</td>
                            <td class="py-2 text-right text-gray-500 dark:text-gray-400">{{ row.pct }}%</td>
                        </tr>
                    </tbody>
                </table>
            </section>

            <section class="bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-lg">
                <h3 class="font-bold text-gray-900 dark:text-white mb-4">Students by Academic Year</h3>
                <table class="min-w-full text-sm">
                    <thead class="text-left text-gray-500 dark:text-gray-400 border-b dark:border-gray-700">
                        <tr><th class="py-2">Year</th><th class="py-2 text-right">Students</th><th class="py-2 text-right">%</th></tr>
                    </thead>
                    <tbody>
                        <tr v-for="row in buildTable(yearDistribution)" :key="row.label" class="border-b dark:border-gray-700/60">
                            <td class="py-2 text-gray-800 dark:text-gray-200">{{ row.label }}</td>
                            <td class="py-2 text-right font-medium text-gray-900 dark:text-white">{{ row.value }}</td>
                            <td class="py-2 text-right text-gray-500 dark:text-gray-400">{{ row.pct }}%</td>
                        </tr>
                    </tbody>
                </table>
            </section>
        </div>
    </AppLayout>
</template>
