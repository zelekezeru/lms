<script setup>
import RegistrarLayout from "@/Layouts/RegistrarLayout.vue";
import { usePage } from "@inertiajs/vue3";
import {
    AcademicCapIcon,
    UserGroupIcon,
    ClipboardDocumentCheckIcon,
    Cog6ToothIcon,
    CheckCircleIcon,
    UserPlusIcon,
} from "@heroicons/vue/24/outline";
import { ref, onMounted } from "vue";
import Chart from "chart.js/auto";

const user = usePage().props.auth.user;

// Chart refs
const studentsChart = ref(null);
const coursesChart = ref(null);

onMounted(() => {
    // Students Enrollment Chart (Bar)
    if (studentsChart.value) {
        new Chart(studentsChart.value, {
            type: "bar",
            data: {
                labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun"],
                datasets: [
                    {
                        label: "New Students",
                        data: [120, 190, 170, 220, 180, 230],
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
                labels: ["Science", "Arts", "Commerce", "Technology"],
                datasets: [
                    {
                        label: "Courses",
                        data: [24, 18, 30, 10],
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
const recentActivities = [
    {
        icon: UserPlusIcon,
        text: "Added new student: John Doe",
        time: "2 hours ago",
        badge: "New",
    },
    {
        icon: CheckCircleIcon,
        text: "Grade submitted for CSC101",
        time: "4 hours ago",
        badge: "Success",
    },
    {
        icon: Cog6ToothIcon,
        text: "Updated profile settings",
        time: "1 day ago",
        badge: "Update",
    },
];
</script>

<template>
    <RegistrarLayout>
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
                        1,542
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
                        82
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
                        Grades Submitted
                    </p>
                    <h2
                        class="text-xl font-semibold text-gray-900 dark:text-white"
                    >
                        6,320
                    </h2>
                </div>
            </div>
            <div
                class="bg-white dark:bg-gray-800 p-5 rounded-3xl shadow-lg flex items-center gap-4 transform hover:scale-[1.03] transition-transform cursor-default"
            >
                <Cog6ToothIcon class="w-10 h-10 text-orange-600" />
                <div>
                    <p
                        class="text-xs text-gray-500 dark:text-gray-400 uppercase font-semibold tracking-wide"
                    >
                        Settings Updates
                    </p>
                    <h2
                        class="text-xl font-semibold text-gray-900 dark:text-white"
                    >
                        5
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
    </RegistrarLayout>
</template>
