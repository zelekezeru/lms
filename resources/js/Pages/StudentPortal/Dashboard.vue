<script setup>
import { ref, onMounted, computed } from "vue";
import {
    AcademicCapIcon,
    CalendarIcon,
    ChartBarIcon,
    UsersIcon,
    CurrencyDollarIcon,
    CreditCardIcon,
    ClockIcon,
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

// Dynamic GPA Calculation and Chronological Sorting
const sortedSemestersWithGPA = computed(() => {
    if (!props.student.grades || props.student.grades.length === 0) {
        return [];
    }

    const semesterMap = {};
    props.student.grades.forEach((grade) => {
        if (!grade.semester) return;
        const semId = grade.semester.id;
        if (!semesterMap[semId]) {
            semesterMap[semId] = {
                id: semId,
                name: grade.semester.name,
                yearName: grade.semester.year?.name || "",
                grades: [],
            };
        }
        semesterMap[semId].grades.push(grade);
    });

    const semesterList = Object.values(semesterMap);

    const semesterOrder = {
        "First Semester": 1,
        "Second Semester": 2,
    };
    semesterList.sort((a, b) => {
        const yearA = a.yearName;
        const yearB = b.yearName;
        if (yearA < yearB) return -1;
        if (yearA > yearB) return 1;
        const semA = a.name;
        const semB = b.name;
        return (semesterOrder[semA] || 0) - (semesterOrder[semB] || 0);
    });

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

    return semesterList.map((sem) => {
        let totalPoints = 0;
        let totalCredits = 0;
        sem.grades.forEach((grade) => {
            const gp = getGradePointFromLetter(parseFloat(grade.grade_point) || 0);
            const credit = parseFloat(grade.course?.credit_hours || 0);
            totalPoints += gp * credit;
            totalCredits += credit;
        });
        const gpa = totalCredits > 0 ? totalPoints / totalCredits : 0;
        return {
            label: `${sem.yearName} - ${sem.name}`,
            gpa: parseFloat(gpa.toFixed(2)),
        };
    });
});

// Dynamic Payment Overview calculations
const totalPaid = computed(() => {
    if (!props.student.payments) return 0;
    return props.student.payments
        .filter((p) => p.status === "completed")
        .reduce((sum, p) => sum + parseFloat(p.total_amount || 0), 0);
});

const balanceDue = computed(() => {
    if (!props.student.payments) return 0;
    return props.student.payments
        .filter((p) => p.status === "pending")
        .reduce((sum, p) => sum + parseFloat(p.total_amount || 0), 0);
});

const lastPaymentDate = computed(() => {
    if (!props.student.payments) return "N/A";
    const completedPayments = props.student.payments.filter(
        (p) => p.status === "completed"
    );
    if (completedPayments.length === 0) return "N/A";
    const sorted = [...completedPayments].sort(
        (a, b) => new Date(b.payment_date) - new Date(a.payment_date)
    );
    const d = new Date(sorted[0].payment_date);
    return d.toLocaleDateString("en-US", {
        year: "numeric",
        month: "long",
        day: "numeric",
    });
});

let chart = null;
const createChart = () => {
    const ctx = document.getElementById("gpaChart");
    if (!ctx) return;

    const dataPoints = sortedSemestersWithGPA.value;
    const labels =
        dataPoints.length > 0 ? dataPoints.map((d) => d.label) : ["No Grades"];
    const data =
        dataPoints.length > 0 ? dataPoints.map((d) => d.gpa) : [0.0];

    chart = new Chart(ctx, {
        type: "line",
        data: {
            labels: labels,
            datasets: [
                {
                    label: "GPA",
                    data: data,
                    fill: true,
                    backgroundColor: "rgba(99, 102, 241, 0.05)",
                    borderColor: "rgba(99, 102, 241, 1)",
                    borderWidth: 3,
                    pointBackgroundColor: "rgba(99, 102, 241, 1)",
                    pointBorderColor: "#ffffff",
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                    pointHoverBorderWidth: 3,
                    tension: 0.35,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 4.0,
                    ticks: {
                        stepSize: 0.5,
                        color: "rgba(156, 163, 175, 1)",
                        font: { family: "Outfit, Inter, sans-serif" }
                    },
                    grid: {
                        color: "rgba(156, 163, 175, 0.08)",
                    },
                },
                x: {
                    ticks: {
                        color: "rgba(156, 163, 175, 1)",
                        font: { family: "Outfit, Inter, sans-serif" }
                    },
                    grid: {
                        display: false,
                    },
                },
            },
            plugins: {
                legend: {
                    display: false,
                },
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
            class="relative bg-gradient-to-r from-indigo-700 via-purple-700 to-blue-800 rounded-3xl overflow-hidden shadow-lg mb-8 p-6 md:p-8 lg:p-10"
        >
            <!-- Glow background circles -->
            <div class="absolute top-[-50px] right-[-50px] w-48 h-48 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-[-30px] left-[10%] w-32 h-32 bg-indigo-500/20 rounded-full blur-2xl"></div>

            <div class="relative z-10">
                <h1
                    class="text-3xl md:text-4xl lg:text-5xl font-black text-white mb-3 tracking-tight"
                >
                    Welcome, {{ student.user.name }}
                </h1>
                <p class="text-base md:text-lg text-indigo-150 max-w-2xl font-light leading-relaxed">
                    Dive into your courses, track your progress, manage
                    payments, and stay on top of your schedule—all in one place.
                </p>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="col-span-4 space-y-8 px-4">
                <!-- Enrolled Courses -->
                <section>
                    <h2
                        class="text-2xl font-black text-gray-800 dark:text-gray-150 mb-6 tracking-tight flex items-center gap-2"
                    >
                        <span class="w-2.5 h-6 bg-indigo-650 rounded-full"></span>
                        My Active Courses
                    </h2>
                    <div
                        v-if="student.enrollments && student.enrollments.length > 0"
                        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6"
                    >
                        <div
                            v-for="enrollment in student.enrollments"
                            :key="enrollment.id"
                            class="bg-white/70 dark:bg-gray-800/70 backdrop-blur-md p-6 rounded-3xl border border-white/20 dark:border-gray-700/50 shadow-sm hover:shadow-xl hover:-translate-y-1.5 hover:border-indigo-500/30 dark:hover:border-indigo-400/30 transition duration-300"
                        >
                            <Link
                                :href="
                                    route(
                                        'student.enrollments.show',
                                        enrollment.id
                                    )
                                "
                                class="block space-y-4"
                            >
                                <div
                                    class="flex items-center justify-between"
                                >
                                    <div class="p-2.5 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 rounded-xl">
                                        <AcademicCapIcon class="h-6 w-6" />
                                    </div>
                                    <span
                                        class="text-xs font-bold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/40 px-3 py-1 rounded-full"
                                        >{{
                                            enrollment.course.credit_hours
                                        }}
                                        credits</span
                                    >
                                </div>

                                <div>
                                    <h3
                                        class="text-lg font-black text-gray-900 dark:text-white leading-snug group-hover:text-indigo-600"
                                    >
                                        {{ enrollment.course.name }}
                                    </h3>
                                    <span class="text-xs text-gray-400 font-mono mt-1 block">
                                        {{ enrollment.course.code }}
                                    </span>
                                </div>

                                <div
                                    class="text-sm space-y-2 pt-2 border-t border-gray-100 dark:border-gray-700/60 text-gray-650 dark:text-gray-300"
                                >
                                    <p class="flex items-center justify-between">
                                        <span class="text-gray-400 font-medium">Instructor:</span>
                                        <span class="font-bold text-gray-800 dark:text-gray-250">
                                            {{ enrollment.instructor ? enrollment.instructor.name : "TBA" }}
                                        </span>
                                    </p>
                                    <p class="flex items-center justify-between">
                                        <span class="text-gray-400 font-medium">Section:</span>
                                        <span class="font-bold text-gray-800 dark:text-gray-250">
                                            {{ enrollment.section.name }}
                                        </span>
                                    </p>
                                    <p class="flex items-center justify-between">
                                        <span class="text-gray-400 font-medium">Study Mode:</span>
                                        <span class="text-xs bg-gray-100 dark:bg-gray-700/60 px-2 py-0.5 rounded font-bold">
                                            {{ enrollment.section.studyMode.name }}
                                        </span>
                                    </p>
                                </div>
                            </Link>
                        </div>
                    </div>
                    <div v-else class="bg-white/70 dark:bg-gray-800/70 backdrop-blur-md p-10 rounded-3xl text-center text-gray-500 dark:text-gray-400 border border-white/20 dark:border-gray-700/50 shadow-sm">
                        No active course enrollments found.
                    </div>
                </section>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- GPA Chart -->
                    <section class="space-y-4">
                        <h2
                            class="text-xl font-black text-gray-800 dark:text-gray-150 tracking-tight flex items-center gap-2"
                        >
                            <span class="w-2.5 h-5 bg-indigo-650 rounded-full"></span>
                            Your GPA Results
                        </h2>
                        <div
                            class="bg-white/70 dark:bg-gray-800/70 backdrop-blur-md rounded-3xl border border-white/20 dark:border-gray-700/50 shadow-sm p-6"
                        >
                            <div class="h-64 w-full">
                                <canvas id="gpaChart"></canvas>
                            </div>
                        </div>
                    </section>

                    <!-- Course Results Table -->
                    <section class="space-y-4">
                        <h2 class="text-xl font-black text-gray-800 dark:text-gray-150 tracking-tight flex items-center gap-2">
                            <span class="w-2.5 h-5 bg-indigo-650 rounded-full"></span>
                            Course Results
                        </h2>
                        <div class="bg-white/70 dark:bg-gray-800/70 backdrop-blur-md rounded-3xl border border-white/20 dark:border-gray-700/50 shadow-sm p-6 overflow-hidden">
                            <div class="overflow-x-auto rounded-2xl border border-gray-100 dark:border-gray-700/50">
                                <table class="w-full text-left">
                                    <thead class="bg-gray-50 dark:bg-gray-700/30">
                                        <tr>
                                            <th class="px-4 py-3 font-semibold text-xs text-gray-500 uppercase tracking-wider w-12">#</th>
                                            <th class="px-4 py-3 font-semibold text-xs text-gray-500 uppercase tracking-wider">Course Name</th>
                                            <th class="px-4 py-3 font-semibold text-xs text-gray-500 uppercase tracking-wider">Credits</th>
                                            <th class="px-4 py-3 font-semibold text-xs text-gray-500 uppercase tracking-wider text-right">Grade</th>
                                        </tr>
                                    </thead>
                                    <tbody v-if="student.grades && student.grades.length > 0" class="divide-y divide-gray-100 dark:divide-gray-700/50">
                                        <tr
                                            v-for="(grade, index) in student.grades.slice(0, 5)"
                                            :key="grade.id"
                                            class="hover:bg-gray-50/30 dark:hover:bg-gray-700/10 transition duration-150"
                                        >
                                            <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 font-medium">{{ index + 1 }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-850 dark:text-gray-200 font-bold">{{ grade.course.name }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-650 dark:text-gray-450">{{ grade.course.credit_hours }} credits</td>
                                            <td class="px-4 py-3 text-right">
                                                <span class="inline-block px-2.5 py-0.5 rounded text-xs font-bold bg-indigo-50 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-300">
                                                    {{ grade.grade_letter }}
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tbody v-else>
                                        <tr>
                                            <td colspan="4" class="text-center py-10 text-gray-500 dark:text-gray-400">
                                                No grades available yet.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </section>
                </div>

                <!-- Payment Information -->
                <section class="space-y-4">
                    <h2
                        class="text-xl font-black text-gray-800 dark:text-gray-150 tracking-tight flex items-center gap-2"
                    >
                        <span class="w-2.5 h-6 bg-indigo-650 rounded-full"></span>
                        Payment Information
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <!-- Total Paid Card -->
                        <div
                            class="bg-white/70 dark:bg-gray-800/70 backdrop-blur-md p-6 rounded-3xl border border-white/20 dark:border-gray-700/50 shadow-sm hover:shadow-md transition duration-300"
                        >
                            <div class="flex items-center gap-4">
                                <div class="p-3.5 bg-green-50 dark:bg-green-950/40 text-green-600 dark:text-green-400 rounded-2xl">
                                    <CurrencyDollarIcon class="w-6 h-6" />
                                </div>
                                <div>
                                    <p
                                        class="text-xs font-semibold text-gray-400 uppercase tracking-wider"
                                    >
                                        Total Paid
                                    </p>
                                    <p class="text-2xl font-black text-green-600 dark:text-green-400 mt-0.5">
                                        ${{ totalPaid.toFixed(2) }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Balance Due Card -->
                        <div
                            class="bg-white/70 dark:bg-gray-800/70 backdrop-blur-md p-6 rounded-3xl border border-white/20 dark:border-gray-700/50 shadow-sm hover:shadow-md transition duration-300"
                        >
                            <div class="flex items-center gap-4">
                                <div class="p-3.5 bg-yellow-50 dark:bg-yellow-950/40 text-yellow-600 dark:text-yellow-400 rounded-2xl">
                                    <CreditCardIcon class="w-6 h-6" />
                                </div>
                                <div>
                                    <p
                                        class="text-xs font-semibold text-gray-400 uppercase tracking-wider"
                                    >
                                        Balance Due
                                    </p>
                                    <p class="text-2xl font-black text-yellow-600 dark:text-yellow-400 mt-0.5">
                                        ${{ balanceDue.toFixed(2) }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Last Payment Card -->
                        <div
                            class="bg-white/70 dark:bg-gray-800/70 backdrop-blur-md p-6 rounded-3xl border border-white/20 dark:border-gray-700/50 shadow-sm hover:shadow-md transition duration-300"
                        >
                            <div class="flex items-center gap-4">
                                <div class="p-3.5 bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 rounded-2xl">
                                    <CalendarIcon class="w-6 h-6" />
                                </div>
                                <div>
                                    <p
                                        class="text-xs font-semibold text-gray-400 uppercase tracking-wider"
                                    >
                                        Last Payment
                                    </p>
                                    <p
                                        class="text-lg font-bold text-gray-800 dark:text-gray-100 mt-1"
                                    >
                                        {{ lastPaymentDate }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div
                        class="bg-white/70 dark:bg-gray-800/70 backdrop-blur-md rounded-3xl border border-white/20 dark:border-gray-700/50 shadow-sm p-6 overflow-hidden"
                    >
                        <h3
                            class="text-lg font-bold mb-4 text-gray-800 dark:text-gray-150"
                        >
                            Payment History
                        </h3>
                        <div class="overflow-x-auto rounded-2xl border border-gray-100 dark:border-gray-750">
                            <table
                                class="w-full text-left"
                            >
                                <thead class="bg-gray-50 dark:bg-gray-700/30">
                                    <tr>
                                        <th class="py-3 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="py-3 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Amount</th>
                                        <th class="py-3 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Method</th>
                                        <th class="py-3 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Status</th>
                                    </tr>
                                </thead>
                                <tbody v-if="student.payments && student.payments.length > 0" class="divide-y divide-gray-100 dark:divide-gray-700/50">
                                    <tr
                                        v-for="payment in student.payments"
                                        :key="payment.id"
                                        class="hover:bg-gray-55/30 dark:hover:bg-gray-700/10 transition duration-150"
                                    >
                                        <td class="py-4 px-6 text-sm text-gray-700 dark:text-gray-300">
                                            {{ new Date(payment.payment_date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }) }}
                                        </td>
                                        <td
                                            class="py-4 px-6 text-sm text-green-650 dark:text-green-400 font-black"
                                        >
                                            ${{ parseFloat(payment.total_amount).toFixed(2) }}
                                        </td>
                                        <td class="py-4 px-6 text-sm text-gray-700 dark:text-gray-300">
                                            {{ payment.paymentMethod?.name || 'Credit Card' }}
                                        </td>
                                        <td class="py-4 px-6 text-right">
                                            <span
                                                class="px-2.5 py-1 text-xs font-bold rounded-full"
                                                :class="
                                                    payment.status === 'completed'
                                                        ? 'bg-green-100 text-green-800 dark:bg-green-950/50 dark:text-green-300'
                                                        : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-950/50 dark:text-yellow-300'
                                                "
                                            >
                                                {{ payment.status === 'completed' ? 'Completed' : 'Pending' }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                                <tbody v-else>
                                    <tr>
                                        <td colspan="4" class="text-center py-10 text-gray-500 dark:text-gray-400">
                                            No payment records found.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </AppLayout>
</template>
