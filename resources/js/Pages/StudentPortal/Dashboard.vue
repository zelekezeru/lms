<script setup>
import { ref, onMounted } from "vue";
import {
    AcademicCapIcon,
    CalendarIcon,
    ChartBarIcon,
    UsersIcon,
    CurrencyDollarIcon,
} from "@heroicons/vue/24/outline";
import AppLayout from "@/Layouts/AppLayout.vue";
import { Chart } from "chart.js/auto";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    student: {
        type: Object,
        required: true,
    },
});

let chart = null;
const createChart = () => {
    const ctx = document.getElementById("gpaChart");
    chart = new Chart(ctx, {
        type: "line",
        data: {
            labels: [
                "Spring 2023",
                "Summer 2023",
                "Fall 2023",
                "Spring 2024",
                "Summer 2024",
            ],
            datasets: [
                {
                    label: "GPA",
                    data: [2.0, 4.0, 3.5, 3.75, 4.0],
                    fill: false,
                    borderColor: "rgba(75, 192, 192, 1)",
                    tension: 0.1,
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
    <AppLayout>
        <!-- Dashboard Header -->
        <div
            class="relative bg-cover bg-center rounded-lg overflow-hidden shadow-lg mb-8"
        >
            <div class="absolute inset-0 bg-black bg-opacity-25"></div>
            <div class="relative p-6 md:p-8 lg:p-10">
                <h1
                    class="text-3xl md:text-4xl lg:text-5xl font-extrabold text-dark dark: mb-3"
                >
                    Welcome, {{ student.user.name }}
                </h1>
                <p class="text-base md:text-lg text-dark dark: max-w-2xl">
                    Dive into your courses, track your progress, manage
                    payments, and stay on top of your schedule—all in one place.
                </p>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <!-- Sidebar omitted (handled by AppLayout) -->

            <div class="col-span-4 space-y-8 px-4">
                <!-- Enrolled Courses -->
                <section>
                    <h2
                        class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-6"
                    >
                        My Active Courses
                    </h2>
                    <div
                        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6"
                    >
                        <div
                            v-for="enrollment in student.enrollments"
                            :key="enrollment.id"
                            class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow hover:shadow-lg transition duration-300"
                        >
                            <Link
                                :href="
                                    route(
                                        'student.enrollments.show',
                                        enrollment.id
                                    )
                                "
                                class="block"
                            >
                                <div
                                    class="flex items-center justify-between mb-3"
                                >
                                    <AcademicCapIcon
                                        class="h-6 w-6 text-blue-500"
                                    />
                                    <span
                                        class="text-xs text-gray-500 dark:text-gray-400"
                                        >{{
                                            enrollment.course.credit_hours
                                        }}
                                        credits</span
                                    >
                                </div>

                                <h3
                                    class="text-lg font-semibold text-gray-900 dark:text-white mb-2"
                                >
                                    {{ enrollment.course.name }}
                                    <span class="text-sm text-gray-500"
                                        >({{ enrollment.course.code }})</span
                                    >
                                </h3>

                                <div
                                    class="text-sm space-y-1 text-gray-700 dark:text-gray-300"
                                >
                                    <p>
                                        <strong>Instructor:</strong>
                                        {{
                                            enrollment.instructor
                                                ? enrollment.instructor.name
                                                : "TBA"
                                        }}
                                    </p>
                                    <p>
                                        <strong>Section:</strong>
                                        {{ enrollment.section.name }}
                                        <span class="ml-1 text-gray-500 text-xs"
                                            >({{
                                                enrollment.section.track.name
                                            }}
                                            Track)</span
                                        >
                                    </p>
                                    <p>
                                        <strong>Study Mode:</strong>
                                        {{ enrollment.section.studyMode.name }}
                                    </p>
                                </div>
                            </Link>
                        </div>
                    </div>
                </section>

                <!-- GPA Chart -->
                <section>
                    <h2
                        class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4"
                    >
                        Your GPA Results
                    </h2>
                    <div
                        class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6"
                    >
                        <canvas id="gpaChart" class="w-full h-64"></canvas>
                    </div>
                </section>

                <!-- Course Results Table -->
                <section>
                    <h2
                        class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4"
                    >
                        Course Results
                    </h2>
                    <div
                        class="overflow-x-auto bg-white dark:bg-gray-800 rounded-lg shadow-md p-6"
                    >
                        <table
                            class="min-w-full text-sm text-left text-gray-500 dark:text-gray-400"
                        >
                            <thead class="bg-gray-100 dark:bg-gray-700">
                                <tr>
                                    <th class="py-3 px-6">Course</th>
                                    <th class="py-3 px-6">Credits</th>
                                    <th class="py-3 px-6">Grade</th>
                                    <th class="py-3 px-6">Instructor</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    class="border-t border-gray-200 dark:border-gray-700"
                                >
                                    <td class="py-4 px-6">
                                        Intro to Computer Science
                                    </td>
                                    <td class="py-4 px-6">3</td>
                                    <td class="py-4 px-6 font-semibold">A</td>
                                    <td class="py-4 px-6">Dr. Jane Doe</td>
                                </tr>
                                <tr
                                    class="border-t border-gray-200 dark:border-gray-700"
                                >
                                    <td class="py-4 px-6">Intro to Aljebera</td>
                                    <td class="py-4 px-6">5</td>
                                    <td class="py-4 px-6 font-semibold">C</td>
                                    <td class="py-4 px-6">Dr. Jane Doe</td>
                                </tr>
                                <tr
                                    class="border-t border-gray-200 dark:border-gray-700"
                                >
                                    <td class="py-4 px-6">
                                        Fundemintal Computer Science
                                    </td>
                                    <td class="py-4 px-6">3</td>
                                    <td class="py-4 px-6 font-semibold">A</td>
                                    <td class="py-4 px-6">Dr. Jane Doe</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- Payment Information -->
                <section>
                    <h2
                        class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4"
                    >
                        Payment Information
                    </h2>

                    <!-- Summary Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
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
                                $1,200.00
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
                                    Balance Due
                                </p>
                            </div>
                            <p class="mt-2 text-2xl font-bold text-red-500">
                                $300.00
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

                    <!-- Payment History Table -->
                    <div
                        class="overflow-x-auto bg-white dark:bg-gray-800 rounded-lg shadow-md p-6"
                    >
                        <h3
                            class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-100"
                        >
                            Payment History
                        </h3>
                        <table
                            class="min-w-full text-sm text-left text-gray-500 dark:text-gray-400"
                        >
                            <thead class="bg-gray-100 dark:bg-gray-700">
                                <tr>
                                    <th class="py-3 px-6">Date</th>
                                    <th class="py-3 px-6">Amount</th>
                                    <th class="py-3 px-6">Method</th>
                                    <th class="py-3 px-6">Receipt</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    class="border-t border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-900 transition"
                                >
                                    <td class="py-4 px-6">June 25, 2024</td>
                                    <td
                                        class="py-4 px-6 text-green-600 font-semibold"
                                    >
                                        $2,000.00
                                    </td>
                                    <td class="py-4 px-6">Credit Card</td>
                                    <td class="py-4 px-6">
                                        <button
                                            class="text-blue-600 hover:underline"
                                        >
                                            Download
                                        </button>
                                    </td>
                                </tr>
                                <!-- ...other rows -->
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </div>
    </AppLayout>
</template>
