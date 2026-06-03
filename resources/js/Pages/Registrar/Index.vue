<script setup>
import {
    AcademicCapIcon,
    UserGroupIcon,
    ClipboardDocumentCheckIcon,
    CheckCircleIcon,
    UserPlusIcon,
    GlobeAltIcon,
    PresentationChartLineIcon,
    ExclamationTriangleIcon,
    GiftIcon,
    UsersIcon,
    BookOpenIcon,
    CalendarDaysIcon,
    ChartBarIcon,
} from "@heroicons/vue/24/outline";
import { ref, onMounted } from "vue";
import { Link } from "@inertiajs/vue3";
import Chart from "chart.js/auto";
import AppLayout from "@/Layouts/AppLayout.vue";

const props = defineProps({
    user: { type: Object, required: true },
    totalStudents: { type: Number, default: 0 },
    totalCourses: { type: Number, default: 0 },
    totalInstructors: { type: Number, default: 0 },
    availablePrograms: { type: Number, default: 0 },
    availableCenters: { type: Number, default: 0 },
    activeStudents: { type: Number, default: 0 },
    graduatedStudents: { type: Number, default: 0 },
    scholarshipStudents: { type: Number, default: 0 },
    pendingPaymentStudents: { type: Number, default: 0 },
    unassignedStudents: { type: Number, default: 0 },
    enrollments: { type: Object, default: () => ({ labels: [], data: [] }) },
    courseDistribution: { type: Object, default: () => ({ labels: [], data: [] }) },
    studyModeDistribution: { type: Object, default: () => ({ labels: [], data: [] }) },
    recentActivities: { type: Array, default: () => [] },
});

// Chart refs
const studentsChart = ref(null);
const coursesChart = ref(null);
const studyModeChart = ref(null);

// Headline metric cards
const metrics = [
    { label: "Total Students", value: props.totalStudents, icon: UserGroupIcon, color: "text-blue-600" },
    { label: "Total Courses", value: props.totalCourses, icon: AcademicCapIcon, color: "text-green-600" },
    { label: "Available Programs", value: props.availablePrograms, icon: ClipboardDocumentCheckIcon, color: "text-purple-600" },
    { label: "Distance Centers", value: props.availableCenters, icon: GlobeAltIcon, color: "text-orange-600" },
];

// Follow-up cards (actionable). Some link to a filtered student directory.
const followUps = [
    { label: "Active Students", value: props.activeStudents, icon: CheckCircleIcon, ring: "bg-green-100 dark:bg-green-900/40 text-green-600 dark:text-green-300", href: route("registrar.students", { statuses: ["active"] }) },
    { label: "Pending Payment", value: props.pendingPaymentStudents, icon: ExclamationTriangleIcon, ring: "bg-amber-100 dark:bg-amber-900/40 text-amber-600 dark:text-amber-300", href: route("registrar.students", { payment: "pending" }) },
    { label: "Scholarship", value: props.scholarshipStudents, icon: GiftIcon, ring: "bg-pink-100 dark:bg-pink-900/40 text-pink-600 dark:text-pink-300", href: route("registrar.students", { payment: "is_scholarship" }) },
    { label: "Graduated", value: props.graduatedStudents, icon: AcademicCapIcon, ring: "bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-300", href: route("registrar.students", { statuses: ["graduated"] }) },
    { label: "Unassigned to Section", value: props.unassignedStudents, icon: UsersIcon, ring: "bg-red-100 dark:bg-red-900/40 text-red-600 dark:text-red-300", href: route("registrar.students") },
    { label: "Instructors", value: props.totalInstructors, icon: PresentationChartLineIcon, ring: "bg-sky-100 dark:bg-sky-900/40 text-sky-600 dark:text-sky-300", href: route("instructors.index") },
];

// Quick actions
const quickActions = [
    { label: "Student Directory", icon: UsersIcon, href: route("registrar.students"), color: "from-blue-600 to-blue-400" },
    { label: "Reports", icon: ChartBarIcon, href: route("registrar.reports"), color: "from-purple-600 to-purple-400" },
    { label: "Manage Courses", icon: BookOpenIcon, href: route("courses.index"), color: "from-green-600 to-green-400" },
    { label: "Manage Sections", icon: AcademicCapIcon, href: route("sections.index"), color: "from-amber-600 to-amber-400" },
    { label: "Manage Semesters", icon: CalendarDaysIcon, href: route("semesters.index"), color: "from-rose-600 to-rose-400" },
    { label: "Add Student", icon: UserPlusIcon, href: route("students.create"), color: "from-teal-600 to-teal-400" },
];

const palette = [
    "rgba(34,197,94,0.7)",
    "rgba(139,92,246,0.7)",
    "rgba(234,179,8,0.7)",
    "rgba(59,130,246,0.7)",
    "rgba(244,63,94,0.7)",
    "rgba(14,165,233,0.7)",
];

onMounted(() => {
    // Enrollment trend (bar)
    if (studentsChart.value) {
        new Chart(studentsChart.value, {
            type: "bar",
            data: {
                labels: props.enrollments.labels,
                datasets: [
                    {
                        label: "New Students",
                        data: props.enrollments.data,
                        backgroundColor: "rgba(37, 99, 235, 0.7)",
                        borderRadius: 6,
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

    // Course distribution (doughnut)
    if (coursesChart.value) {
        new Chart(coursesChart.value, {
            type: "doughnut",
            data: {
                labels: props.courseDistribution.labels,
                datasets: [
                    {
                        label: "Courses",
                        data: props.courseDistribution.data,
                        backgroundColor: palette,
                        hoverOffset: 20,
                        borderWidth: 0,
                    },
                ],
            },
            options: {
                responsive: true,
                plugins: { legend: { position: "bottom" } },
            },
        });
    }

    // Study mode distribution (horizontal bar)
    if (studyModeChart.value) {
        new Chart(studyModeChart.value, {
            type: "bar",
            data: {
                labels: props.studyModeDistribution.labels,
                datasets: [
                    {
                        label: "Students",
                        data: props.studyModeDistribution.data,
                        backgroundColor: palette,
                        borderRadius: 6,
                    },
                ],
            },
            options: {
                indexAxis: "y",
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { x: { beginAtZero: true, ticks: { precision: 0 } } },
            },
        });
    }
});

const iconFor = (type) =>
    type === "Created new grade" ? CheckCircleIcon : UserPlusIcon;

const formatTime = (value) =>
    value ? new Date(value).toLocaleString() : "";

const badgeFor = (type) =>
    type === "Created new grade" ? "Success" : "New";
</script>

<template>
    <AppLayout>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
            <div>
                <h1 class="text-2xl font-extrabold text-gray-900 dark:text-white">
                    Welcome, {{ user.name }}
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Registrar dashboard — manage students, academics and reporting.
                </p>
            </div>
            <Link
                :href="route('registrar.reports')"
                class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-purple-600 to-purple-400 px-4 py-2 text-sm font-semibold text-white shadow hover:from-purple-700 hover:to-purple-500 transition"
            >
                <ChartBarIcon class="w-5 h-5" />
                View Reports
            </Link>
        </div>

        <!-- Headline Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-6">
            <div
                v-for="metric in metrics"
                :key="metric.label"
                class="bg-white dark:bg-gray-800 p-5 rounded-3xl shadow-lg flex items-center gap-4 transform hover:scale-[1.02] transition-transform"
            >
                <component :is="metric.icon" class="w-10 h-10" :class="metric.color" />
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-semibold tracking-wide">
                        {{ metric.label }}
                    </p>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                        {{ metric.value }}
                    </h2>
                </div>
            </div>
        </div>

        <!-- Follow-up Cards -->
        <h3 class="text-sm font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-3">
            Follow-up
        </h3>
        <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">
            <Link
                v-for="item in followUps"
                :key="item.label"
                :href="item.href"
                class="bg-white dark:bg-gray-800 p-4 rounded-2xl shadow flex flex-col gap-2 hover:shadow-md hover:-translate-y-0.5 transition"
            >
                <span class="w-9 h-9 rounded-full flex items-center justify-center" :class="item.ring">
                    <component :is="item.icon" class="w-5 h-5" />
                </span>
                <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ item.value }}</span>
                <span class="text-xs text-gray-500 dark:text-gray-400 leading-tight">{{ item.label }}</span>
            </Link>
        </div>

        <!-- Quick Actions -->
        <h3 class="text-sm font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-3">
            Quick Actions
        </h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">
            <Link
                v-for="action in quickActions"
                :key="action.label"
                :href="action.href"
                class="flex flex-col items-center justify-center gap-2 rounded-2xl p-4 text-white text-center font-semibold text-sm shadow bg-gradient-to-br hover:opacity-90 transition"
                :class="action.color"
            >
                <component :is="action.icon" class="w-7 h-7" />
                {{ action.label }}
            </Link>
        </div>

        <!-- Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <section class="bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-lg">
                <h3 class="font-bold text-gray-900 dark:text-white mb-4">
                    Student Enrollments (Last 6 months)
                </h3>
                <canvas ref="studentsChart" class="w-full"></canvas>
            </section>

            <section class="bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-lg">
                <h3 class="font-bold text-gray-900 dark:text-white mb-4">
                    Courses Distribution
                </h3>
                <canvas ref="coursesChart" class="w-full"></canvas>
            </section>

            <section class="bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-lg">
                <h3 class="font-bold text-gray-900 dark:text-white mb-4">
                    Students by Study Mode
                </h3>
                <canvas ref="studyModeChart" class="w-full"></canvas>
            </section>
        </div>

        <!-- Recent Activity -->
        <section class="bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-lg">
            <h3 class="font-bold text-gray-900 dark:text-white mb-4">Recent Activity</h3>
            <ul v-if="recentActivities.length" class="divide-y divide-gray-200 dark:divide-gray-700">
                <li
                    v-for="(activity, index) in recentActivities"
                    :key="index"
                    class="flex items-center justify-between py-3"
                >
                    <div class="flex items-center gap-3">
                        <component :is="iconFor(activity.type)" class="w-6 h-6 text-blue-500 dark:text-blue-400" />
                        <p class="text-gray-700 dark:text-gray-300 font-medium">
                            {{ activity.type }}: {{ activity.name }}
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span
                            class="px-2 py-0.5 rounded-full text-xs font-semibold text-white"
                            :class="badgeFor(activity.type) === 'Success' ? 'bg-green-500' : 'bg-blue-500'"
                        >
                            {{ badgeFor(activity.type) }}
                        </span>
                        <time class="text-xs text-gray-400 dark:text-gray-500">{{ formatTime(activity.created_at) }}</time>
                    </div>
                </li>
            </ul>
            <p v-else class="text-sm text-gray-500 dark:text-gray-400">No recent activity.</p>
        </section>
    </AppLayout>
</template>
