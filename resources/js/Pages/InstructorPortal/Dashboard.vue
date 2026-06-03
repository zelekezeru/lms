<script setup>
import { onMounted, ref } from "vue";
import {
    AcademicCapIcon,
    BookmarkIcon,
    ArrowRightIcon,
    ClockIcon,
    PresentationChartBarIcon,
    UserGroupIcon,
    SparklesIcon,
} from "@heroicons/vue/24/outline";
import AppLayout from "@/Layouts/AppLayout.vue";
import { Chart } from "chart.js/auto";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    instructor: {
        type: Object,
        required: true,
    },
});

let chart = null;
const createChart = () => {
    const ctx = document.getElementById("performanceChart");
    if (!ctx) return; // defensive check

    chart = new Chart(ctx, {
        type: "bar",
        data: {
            labels: ["CS101", "Math201", "Physics303"],
            datasets: [
                {
                    label: "Avg Student Grade (%)",
                    data: [85, 78, 90],
                    backgroundColor: "rgba(59, 130, 246, 0.5)",
                    borderColor: "rgba(59, 130, 246, 1)",
                    borderWidth: 1.5,
                    borderRadius: 6,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: {
                        color: document.documentElement.classList.contains('dark') ? '#9ca3af' : '#4b5563'
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: {
                        color: document.documentElement.classList.contains('dark') ? '#9ca3af' : '#4b5563'
                    }
                },
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        color: document.documentElement.classList.contains('dark') ? '#9ca3af' : '#4b5563'
                    },
                    grid: {
                        color: document.documentElement.classList.contains('dark') ? '#374151' : '#e5e7eb'
                    }
                },
            },
        },
    });
};

onMounted(createChart);
</script>

<template>
    <AppLayout>
        <!-- Header Banner with CSS Gradients -->
        <div
            class="relative bg-gradient-to-r from-blue-700 via-indigo-700 to-purple-800 rounded-2xl overflow-hidden shadow-md mb-8 p-8 md:p-12 lg:py-14"
        >
            <div class="absolute inset-0 bg-grid-white/[0.08] bg-[size:20px_20px] pointer-events-none"></div>
            <div class="absolute -right-16 -top-16 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute -left-16 -bottom-16 w-64 h-64 bg-indigo-500/20 rounded-full blur-3xl"></div>
            
            <div class="relative max-w-3xl space-y-4">
                <div class="inline-flex items-center space-x-2 bg-white/10 text-white text-xs font-semibold px-3 py-1 rounded-full backdrop-blur-md">
                    <SparklesIcon class="w-4 h-4 text-yellow-300 animate-pulse" />
                    <span>Instructor Dashboard</span>
                </div>
                <h1
                    class="text-3xl md:text-4xl lg:text-5xl font-extrabold text-white tracking-tight leading-tight"
                >
                    Welcome back, {{ instructor.user.name }}
                </h1>
                <p class="text-sm md:text-base lg:text-lg text-indigo-100 font-medium">
                    Manage your assigned courses, view student enrollment, track academic performance, and organize your weekly teaching sessions from a centralized portal.
                </p>
            </div>
        </div>

        <!-- Main Content -->
        <div class="space-y-8">
            <!-- Sections list -->
            <section>
                <div class="mb-6">
                    <h2
                        class="text-xl md:text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2"
                    >
                        <UserGroupIcon class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                        Sections you're teaching in
                    </h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Quick access to student rosters, grades, and sessions for each section.</p>
                </div>

                <div
                    class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6"
                >
                    <div
                        v-for="section in instructor.sections"
                        :key="section.id"
                        class="group transition duration-300 transform hover:-translate-y-1 hover:shadow-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700/80 rounded-2xl p-6 flex flex-col justify-between"
                    >
                        <div class="space-y-4">
                            <!-- Section Header -->
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3
                                        class="text-lg font-bold text-gray-900 dark:text-white"
                                    >
                                        {{ section.name }}
                                    </h3>
                                    <span
                                        class="text-[10px] uppercase font-semibold tracking-wider text-gray-400 dark:text-gray-500"
                                        >Section Code</span
                                    >
                                </div>
                                <span
                                    class="text-xs font-bold text-blue-700 dark:text-blue-300 bg-blue-50 dark:bg-blue-900/30 rounded-full px-2.5 py-1"
                                >
                                    {{ section.code }}
                                </span>
                            </div>

                            <!-- Program & Track Info -->
                            <div
                                class="text-xs text-gray-700 dark:text-gray-300 space-y-2 pt-2 border-t border-gray-100 dark:border-gray-700/60"
                            >
                                <p class="flex items-center gap-2">
                                    <BookmarkIcon
                                        class="w-4 h-4 text-gray-400 dark:text-gray-500"
                                    />
                                    <span><strong>Program:</strong> {{ section.program?.name }}</span>
                                </p>
                                <p class="flex items-center gap-2">
                                    <BookmarkIcon
                                        class="w-4 h-4 text-gray-400 dark:text-gray-500"
                                    />
                                    <span><strong>Track:</strong> {{ section.track?.name }}</span>
                                </p>
                                <div class="flex gap-4 pt-1 font-semibold text-gray-500">
                                    <p>Year: <span class="text-gray-800 dark:text-gray-200">{{ section.yearLevel }}</span></p>
                                    <p>Semester: <span class="text-gray-800 dark:text-gray-200">{{ section.semester?.level }}</span></p>
                                </div>
                            </div>
                        </div>

                        <!-- View Details Button -->
                        <Link
                            :href="
                                route('instructor.sections.detail', {
                                    section: section.id,
                                })
                            "
                            class="inline-flex items-center justify-center w-full mt-5 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-sm transition space-x-2"
                        >
                            <span>Open Section</span>
                            <ArrowRightIcon
                                class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1"
                            />
                        </Link>
                    </div>
                </div>
            </section>

            <!-- Teaching Courses -->
            <section>
                <div class="mb-6">
                    <h2
                        class="text-xl md:text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2"
                    >
                        <AcademicCapIcon class="w-6 h-6 text-indigo-600 dark:text-indigo-400" />
                        Courses You’re Teaching
                    </h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">View information and details on your current course offerings.</p>
                </div>

                <div
                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6"
                >
                    <div
                        v-for="course in instructor.courses"
                        :key="course.id"
                        class="group p-5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700/80 rounded-2xl shadow-sm hover:shadow-lg transition flex flex-col justify-between"
                    >
                        <div class="space-y-3">
                            <div class="p-2 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-lg inline-block">
                                <AcademicCapIcon class="h-6 w-6" />
                            </div>
                            <div>
                                <h3
                                    class="text-md font-bold text-gray-800 dark:text-white line-clamp-1"
                                >
                                    {{ course.name }}
                                </h3>
                                <p class="text-xs text-gray-400 font-semibold">{{ course.code }}</p>
                            </div>
                        </div>

                        <Link
                            :href="
                                route('instructor.courses.detail', {
                                    course: course.id,
                                })
                            "
                            class="inline-flex items-center justify-center w-full mt-5 px-4 py-2 bg-gray-50 dark:bg-gray-700/40 text-gray-700 dark:text-gray-200 text-xs font-semibold rounded-xl hover:bg-indigo-50 dark:hover:bg-indigo-950/20 hover:text-indigo-600 dark:hover:text-indigo-400 transition space-x-2"
                        >
                            <span>Course Outline</span>
                            <ArrowRightIcon
                                class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1"
                            />
                        </Link>
                    </div>
                </div>
            </section>

            <!-- Schedule and Chart Panels (Side by Side Grid) -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Schedule Overview -->
                <section class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border dark:border-gray-700 flex flex-col justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                            <ClockIcon class="w-5 h-5 text-indigo-500" />
                            Weekly Teaching Schedule
                        </h2>
                        <ul class="space-y-4 divide-y divide-gray-100 dark:divide-gray-700">
                            <li class="flex justify-between items-center py-2">
                                <div class="flex flex-col">
                                    <span class="font-semibold text-gray-800 dark:text-white text-sm">CS101 - Intro to CS</span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">Monday @ 10:00 AM</span>
                                </div>
                                <span class="text-xs font-semibold px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-lg">Room 204</span>
                            </li>
                            <li class="flex justify-between items-center py-2 pt-4">
                                <div class="flex flex-col">
                                    <span class="font-semibold text-gray-800 dark:text-white text-sm">Math201 - Linear Algebra</span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">Wednesday @ 2:00 PM</span>
                                </div>
                                <span class="text-xs font-semibold px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-lg">Room 101</span>
                            </li>
                            <li class="flex justify-between items-center py-2 pt-4">
                                <div class="flex flex-col">
                                    <span class="font-semibold text-gray-800 dark:text-white text-sm">Physics303 - Classical Mechanics</span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">Friday @ 11:00 AM</span>
                                </div>
                                <span class="text-xs font-semibold px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-lg">Room 303</span>
                            </li>
                        </ul>
                    </div>
                </section>

                <!-- Class Performance Overview (Bar Chart) -->
                <section class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border dark:border-gray-700">
                    <h2 class="text-lg font-bold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                        <PresentationChartBarIcon class="w-5 h-5 text-blue-600" />
                        Class Performance Analytics
                    </h2>
                    <div class="relative h-64 w-full">
                        <canvas id="performanceChart"></canvas>
                    </div>
                </section>
            </div>
        </div>
    </AppLayout>
</template>
