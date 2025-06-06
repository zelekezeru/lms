<script setup>
import { ref, onMounted } from "vue";
import {
    AcademicCapIcon,
    ChartBarIcon,
    UsersIcon,
    CalendarIcon,
    CurrencyDollarIcon,
} from "@heroicons/vue/24/outline";
import InstructorLayout from "@/Layouts/AppLayout.vue";
import { Chart } from "chart.js/auto";

let chart = null;
const createChart = () => {
    const ctx = document.getElementById("performanceChart");
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
                    borderWidth: 1,
                },
            ],
        },
        options: {
            scales: {
                y: { beginAtZero: true },
            },
        },
    });
};

onMounted(createChart);
</script>

<template>
    <InstructorLayout>
        <!-- Header -->
        <div
            class="relative bg-cover bg-center rounded-lg overflow-hidden shadow-lg mb-8"
            style="
                background-image: url('https://www.pngall.com/wp-content/uploads/5/Teaching-PNG-Free-Image.png');
            "
        >
            <div class="absolute inset-0 bg-black bg-opacity-25"></div>
            <div class="relative p-6 md:p-8 lg:p-10">
                <h1
                    class="text-3xl md:text-4xl lg:text-5xl font-extrabold text-white mb-3"
                >
                    Welcome, Dr. Alex
                </h1>
                <p class="text-base md:text-lg text-gray-200 max-w-2xl">
                    Manage your courses, track student performance, view
                    feedback, and stay on top of your calendar.
                </p>
            </div>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="col-span-3 space-y-8">
                <!-- Teaching Courses -->
                <section>
                    <h2
                        class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4"
                    >
                        Courses You’re Teaching
                    </h2>
                    <div
                        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6"
                    >
                        <div
                            v-for="course in ['CS101', 'Math201', 'Physics303']"
                            :key="course"
                            class="p-5 bg-white dark:bg-gray-800 rounded-lg shadow-md hover:shadow-xl transition-all"
                        >
                            <AcademicCapIcon
                                class="h-6 w-6 text-indigo-500 mb-2"
                            />
                            <h3
                                class="text-lg font-semibold text-gray-700 dark:text-gray-200"
                            >
                                {{ course }}
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Enrolled Students: 35
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Credits: 3
                            </p>
                        </div>
                    </div>
                </section>

                <!-- Student Performance -->
                <section>
                    <h2
                        class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4"
                    >
                        Student Performance Overview
                    </h2>
                    <div
                        class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6"
                    >
                        <canvas
                            id="performanceChart"
                            class="w-full h-64"
                        ></canvas>
                    </div>
                </section>

                <!-- Feedback Summary -->
                <section>
                    <h2
                        class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4"
                    >
                        Recent Student Feedback
                    </h2>
                    <div class="space-y-4">
                        <div
                            class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow"
                        >
                            <p class="text-gray-700 dark:text-gray-200">
                                "Your CS101 lectures are super clear and
                                helpful!"
                            </p>
                            <p
                                class="text-sm text-gray-500 dark:text-gray-400 mt-1"
                            >
                                — Student A
                            </p>
                        </div>
                        <div
                            class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow"
                        >
                            <p class="text-gray-700 dark:text-gray-200">
                                "Can you upload lecture notes earlier?"
                            </p>
                            <p
                                class="text-sm text-gray-500 dark:text-gray-400 mt-1"
                            >
                                — Student B
                            </p>
                        </div>
                    </div>
                </section>

                <!-- Calendar Overview -->
                <section>
                    <h2
                        class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4"
                    >
                        Weekly Teaching Calendar
                    </h2>
                    <div
                        class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md"
                    >
                        <ul class="space-y-2">
                            <li class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-300"
                                    >Mon 10am - CS101</span
                                >
                                <span class="text-gray-500 dark:text-gray-400"
                                    >Room 204</span
                                >
                            </li>
                            <li class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-300"
                                    >Wed 2pm - Math201</span
                                >
                                <span class="text-gray-500 dark:text-gray-400"
                                    >Room 101</span
                                >
                            </li>
                            <li class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-300"
                                    >Fri 11am - Physics303</span
                                >
                                <span class="text-gray-500 dark:text-gray-400"
                                    >Room 303</span
                                >
                            </li>
                        </ul>
                    </div>
                </section>

                <!-- Payroll Info -->
                <section>
                    <h2
                        class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4"
                    >
                        Payroll Information
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div
                            class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md hover:shadow-lg transition"
                        >
                            <div class="flex items-center gap-2 text-green-600">
                                <CurrencyDollarIcon class="w-6 h-6" />
                                <p
                                    class="text-sm font-medium text-gray-500 dark:text-gray-400"
                                >
                                    Total Paid
                                </p>
                            </div>
                            <p class="mt-2 text-2xl font-bold text-green-500">
                                $6,000.00
                            </p>
                        </div>

                        <div
                            class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md hover:shadow-lg transition"
                        >
                            <div
                                class="flex items-center gap-2 text-yellow-600"
                            >
                                <CurrencyDollarIcon class="w-6 h-6" />
                                <p
                                    class="text-sm font-medium text-gray-500 dark:text-gray-400"
                                >
                                    Pending
                                </p>
                            </div>
                            <p class="mt-2 text-2xl font-bold text-yellow-500">
                                $1,200.00
                            </p>
                        </div>

                        <div
                            class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md hover:shadow-lg transition"
                        >
                            <div class="flex items-center gap-2 text-gray-600">
                                <CalendarIcon class="w-6 h-6" />
                                <p
                                    class="text-sm font-medium text-gray-500 dark:text-gray-400"
                                >
                                    Last Payment
                                </p>
                            </div>
                            <p
                                class="mt-2 text-2xl font-bold text-gray-900 dark:text-gray-100"
                            >
                                April 1, 2025
                            </p>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </InstructorLayout>
</template>
