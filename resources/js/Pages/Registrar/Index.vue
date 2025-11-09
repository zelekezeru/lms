<script setup>
import { usePage } from "@inertiajs/vue3";
import {
    AcademicCapIcon,
    UserGroupIcon,
    ClipboardDocumentCheckIcon,
    Cog6ToothIcon,
    CheckCircleIcon,
    UserPlusIcon,
    GlobeAltIcon
} from "@heroicons/vue/24/outline";
import { ref, onMounted } from "vue";
import Chart from "chart.js/auto";
import AppLayout from "@/Layouts/AppLayout.vue";
import en from "@/lang/locales/en";

const props = defineProps({
    programs: { type: Array, required: true },
    user: { type: Object, required: true },
    totalStudents: { type: Number, required: true },
    totalCourses: { type: Number, required: true },
    enrollments: { type: Object, required: true },
    courseDistribution: { type: Object, required: true },
    availablePrograms: { type: Number, required: true },
    availableCenters: { type: Number, required: true },
    recentActivities: { type: Array, required: true },
});

// Chart refs
const studentsChart = ref(null);
const coursesChart = ref(null);

onMounted(() => {
    // Students Enrollment Chart (Bar)
    if (studentsChart.value) {
        new Chart(studentsChart.value, {
            type: "bar",
            data: {
                labels: [props.enrollments.labels[0], props.enrollments.labels[1], props.enrollments.labels[2], props.enrollments.labels[3], props.enrollments.labels[4], props.enrollments.labels[5]],
                datasets: [
                    {
                        label: "New Students",
                        data: [props.enrollments.data[0], props.enrollments.data[1], props.enrollments.data[2], props.enrollments.data[3], props.enrollments.data[4], props.enrollments.data[5]],
                        backgroundColor: "rgba(37, 99, 235, 0.7)", // blue-600
                        borderRadius: 6,
                    },
                ],
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 50 } },
                },
            },
        });
    }

    // Courses Distribution Pie Chart
    if (coursesChart.value) {
        new Chart(coursesChart.value, {
            type: "doughnut",
            data: {
                labels: props.courseDistribution.labels.map(label => label),
                datasets: [
                    {
                        label: "Courses",
                        data: props.courseDistribution.data.map(item => item),
                        backgroundColor: [
                            "rgba(34,197,94, 0.7)", // green-500
                            "rgba(139,92,246, 0.7)", // purple-500
                            "rgba(234,179,8, 0.7)", // yellow-400
                            "rgba(59,130,246, 0.7)", // blue-500
                        ],
                        hoverOffset: 30,
                        borderWidth: 0,
                    },
                ],
            },

            options: {
                responsive: true,
                plugins: {
                    legend: { position: "bottom", labels: { color: "#fff" } },
                },
            },
        });
    }
});

// Sample recent activities with icons & badges
const recentActivities = props.recentActivities.map(activity => ({
    icon:
        activity.type === "New Student"
            ? UserPlusIcon
            : activity.type === "Registered new Instructor"
            ? UserPlusIcon
            : activity.type === "Created new grade"
            ? CheckCircleIcon
            : UserPlusIcon, // fallback icon
    text: `${activity.type}: ${activity.name}`,
    time: activity.created_at
        ? new Date(activity.created_at).toLocaleString()
        : "",
    badge:
        activity.type === "Created new grade"
            ? "Success"
            : "New",
}));
</script>

<template>
    <AppLayout>
        <h1 class="text-2xl font-extrabold mb-6 text-gray-900 dark:text-white">
            Welcome, {{ user.name }}
        </h1>
        
        <!-- Metrics Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
            <div
                class="bg-white dark:bg-gray-800 p-5 rounded-3xl shadow-lg flex items-center gap-4 transform hover:scale-[1.03] transition-transform cursor-default"
            >
                <UserGroupIcon class="w-10 h-10 text-blue-600" />
                <div>
                    <p
                        class="text-xs text-gray-500 dark:text-gray-400 uppercase font-semibold tracking-wide"
                    >
                        Total Students
                    </p>
                    <h2
                        class="text-xl font-semibold text-gray-900 dark:text-white"
                    >
                        {{ totalStudents }}
                    </h2>
                </div>
            </div>
            <div
                class="bg-white dark:bg-gray-800 p-5 rounded-3xl shadow-lg flex items-center gap-4 transform hover:scale-[1.03] transition-transform cursor-default"
            >
                <AcademicCapIcon class="w-10 h-10 text-green-600" />
                <div>
                    <p
                        class="text-xs text-gray-500 dark:text-gray-400 uppercase font-semibold tracking-wide"
                    >
                        Total Courses
                    </p>
                    <h2
                        class="text-xl font-semibold text-gray-900 dark:text-white"
                    >
                        {{ totalCourses }}
                    </h2>
                </div>
            </div>
            <div
                class="bg-white dark:bg-gray-800 p-5 rounded-3xl shadow-lg flex items-center gap-4 transform hover:scale-[1.03] transition-transform cursor-default"
            >
                <ClipboardDocumentCheckIcon class="w-10 h-10 text-purple-600" />
                <div>
                    <p
                        class="text-xs text-gray-500 dark:text-gray-400 uppercase font-semibold tracking-wide"
                    >
                        Available Programs
                    </p>
                    <h2
                        class="text-xl font-semibold text-gray-900 dark:text-white"
                    >
                        {{ availablePrograms }}
                    </h2>
                </div>
            </div>
            <div
                class="bg-white dark:bg-gray-800 p-5 rounded-3xl shadow-lg flex items-center gap-4 transform hover:scale-[1.03] transition-transform cursor-default"
            >
                <GlobeAltIcon class="w-10 h-10 text-orange-600" />
                <div>
                    <p
                        class="text-xs text-gray-500 dark:text-gray-400 uppercase font-semibold tracking-wide"
                    >
                        Distance Centers
                    </p>
                    <h2
                        class="text-xl font-semibold text-gray-900 dark:text-white"
                    >
                        {{ availableCenters }}
                    </h2>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <section
                class="bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-lg"
            >
                <h3 class="font-bold text-gray-900 dark:text-white mb-4">
                    Student Enrollments (Last 6 months)
                </h3>
                <canvas ref="studentsChart" class="w-full h-48"></canvas>
            </section>

            <section
                class="bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-lg"
            >
                <h3 class="font-bold text-gray-900 dark:text-white mb-4">
                    Courses Distribution
                </h3>
                <canvas ref="coursesChart" class="w-full h-48"></canvas>
            </section>

            <section
                class="bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-lg overflow-y-auto max-h-72"
            >
                <h3 class="font-bold text-gray-900 dark:text-white mb-4">
                    Recent Activity
                </h3>
                <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                    <li
                        v-for="(activity, index) in recentActivities"
                        :key="index"
                        class="flex items-center justify-between py-3"
                    >
                        <div class="flex items-center gap-3">
                            <component
                                :is="activity.icon"
                                class="w-6 h-6 text-blue-500 dark:text-blue-400"
                            />
                            <p
                                class="text-gray-700 dark:text-gray-300 font-medium"
                            >
                                {{ activity.text }}
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span
                                class="px-2 py-0.5 rounded-full text-xs font-semibold text-white"
                                :class="{
                                    'bg-green-500':
                                        activity.badge === 'Success',
                                    'bg-blue-500': activity.badge === 'New',
                                    'bg-yellow-400':
                                        activity.badge === 'Update',
                                }"
                            >
                                {{ activity.badge }}
                            </span>
                            <time
                                class="text-xs text-gray-400 dark:text-gray-500"
                                >{{ activity.time }}</time
                            >
                        </div>
                    </li>
                </ul>
            </section>
        </div>
    </AppLayout>
</template>
