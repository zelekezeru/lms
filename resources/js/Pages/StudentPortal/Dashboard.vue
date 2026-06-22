<script setup>
import { ref, computed, onMounted } from "vue";
import { Link } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import { Chart } from "chart.js/auto";
import {
    BookOpenIcon,
    ClipboardDocumentListIcon,
    CalendarDaysIcon,
    ChartBarIcon,
    ArrowRightIcon,
    AcademicCapIcon,
    ClockIcon,
} from "@heroicons/vue/24/outline";
import { CheckCircleIcon } from "@heroicons/vue/24/solid";

const props = defineProps({
    student: { type: Object, required: true },
});

const greetingHour = new Date().getHours();
const greeting = computed(() => {
    if (greetingHour < 12) return "Good morning";
    if (greetingHour < 17) return "Good afternoon";
    return "Good evening";
});

const firstName = computed(() => props.student.user.name.split(" ")[0]);

const activeEnrollments = computed(() =>
    (props.student.enrollments || []).filter((e) => e.status === "enrolled")
);
const completedEnrollments = computed(() =>
    (props.student.enrollments || []).filter((e) => e.status === "completed")
);
const recentGrades = computed(() => (props.student.grades || []).slice(0, 5));

const gradeColor = (letter) => {
    if (!letter) return "bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400";
    const l = letter.toUpperCase();
    if (l === "A" || l === "A+") return "bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400";
    if (l.startsWith("A")) return "bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400";
    if (l.startsWith("B")) return "bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-400";
    if (l.startsWith("C")) return "bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-400";
    return "bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400";
};

// Color palette for course cards
const cardAccents = [
    "from-indigo-500 to-purple-600",
    "from-sky-500 to-blue-600",
    "from-emerald-500 to-teal-600",
    "from-rose-500 to-pink-600",
    "from-amber-500 to-orange-600",
    "from-violet-500 to-indigo-600",
];

let gpaChart = null;
onMounted(() => {
    const ctx = document.getElementById("gpaChart");
    if (!ctx) return;
    const canvasContext = ctx.getContext('2d');
    const gradient = canvasContext.createLinearGradient(0, 0, 0, 160);
    gradient.addColorStop(0, 'rgba(99, 102, 241, 0.25)');
    gradient.addColorStop(1, 'rgba(99, 102, 241, 0.0)');

    gpaChart = new Chart(ctx, {
        type: "line",
        data: {
            labels: ["Sem 1", "Sem 2", "Sem 3", "Sem 4", "Sem 5"],
            datasets: [{
                data: [2.0, 3.5, 3.2, 3.75, 4.0],
                fill: true,
                borderColor: "#6366f1",
                backgroundColor: gradient,
                tension: 0.4,
                pointBackgroundColor: "#6366f1",
                pointBorderColor: "#ffffff",
                pointBorderWidth: 1.5,
                pointRadius: 5,
                pointHoverRadius: 7,
                borderWidth: 2.5,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    beginAtZero: false,
                    min: 0,
                    max: 4,
                    ticks: { color: "#9ca3af", stepSize: 1 },
                    grid: { color: "rgba(156,163,175,0.08)" },
                    border: { display: false },
                },
                x: {
                    ticks: { color: "#9ca3af" },
                    grid: { display: false },
                    border: { display: false },
                },
            },
        },
    });
});
</script>

<template>
    <AppLayout>
        <!-- Padding bottom for mobile bottom nav -->
        <div class="pb-20 md:pb-6 space-y-6 px-4 md:px-6 pt-4 max-w-6xl mx-auto">

            <!-- ── Hero greeting ─────────────────────────── -->
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-600 via-indigo-700 to-purple-700 p-6 text-white shadow-lg">
                <!-- Decorative circles -->
                <div class="absolute -top-8 -right-8 w-40 h-40 rounded-full bg-white/10 pointer-events-none"></div>
                <div class="absolute bottom-0 right-12 w-24 h-24 rounded-full bg-white/5 pointer-events-none"></div>

                <p class="text-indigo-200 text-sm font-medium mb-1">{{ greeting }},</p>
                <h1 class="text-2xl md:text-3xl font-bold mb-3 leading-tight">{{ firstName }} 👋</h1>
                <p class="text-indigo-100 text-sm max-w-md">
                    You have <strong class="text-white">{{ activeEnrollments.length }} active course{{ activeEnrollments.length !== 1 ? 's' : '' }}</strong> this semester.
                    Keep it up!
                </p>

                <!-- Quick stat pills -->
                <div class="mt-5 flex flex-wrap gap-2">
                    <div class="flex items-center gap-1.5 bg-white/15 rounded-full px-3 py-1.5 text-xs font-medium backdrop-blur-sm">
                        <BookOpenIcon class="w-3.5 h-3.5" />
                        {{ activeEnrollments.length }} Enrolled
                    </div>
                    <div class="flex items-center gap-1.5 bg-white/15 rounded-full px-3 py-1.5 text-xs font-medium backdrop-blur-sm">
                        <CheckCircleIcon class="w-3.5 h-3.5" />
                        {{ completedEnrollments.length }} Completed
                    </div>
                    <div class="flex items-center gap-1.5 bg-white/15 rounded-full px-3 py-1.5 text-xs font-medium backdrop-blur-sm">
                        <AcademicCapIcon class="w-3.5 h-3.5" />
                        {{ student.program?.name ?? 'N/A' }}
                    </div>
                </div>
            </div>

            <!-- ── Quick access grid ──────────────────────── -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <Link
                    v-for="(item, i) in [
                        { label: 'My Courses', icon: BookOpenIcon, href: route('student.enrollments'), color: 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400' },
                        { label: 'Schedule', icon: CalendarDaysIcon, href: route('student.classSchedules'), color: 'bg-sky-50 dark:bg-sky-900/20 text-sky-600 dark:text-sky-400' },
                        { label: 'Grades', icon: ChartBarIcon, href: route('student.grades'), color: 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400' },
                        { label: 'Transcript', icon: ClipboardDocumentListIcon, href: route('student.transcripts'), color: 'bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400' },
                    ]"
                    :key="i"
                    :href="item.href"
                    :class="['rounded-2xl p-4 flex flex-col items-center gap-2 text-center transition hover:shadow-md active:scale-95 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700']"
                >
                    <div :class="['w-10 h-10 rounded-xl flex items-center justify-center', item.color]">
                        <component :is="item.icon" class="w-5 h-5" />
                    </div>
                    <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">{{ item.label }}</span>
                </Link>
            </div>

            <!-- ── Active courses ────────────────────────── -->
            <section v-if="activeEnrollments.length > 0">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-base font-bold text-gray-900 dark:text-white">Active Courses</h2>
                    <Link :href="route('student.enrollments')" class="text-xs text-indigo-600 dark:text-indigo-400 font-medium flex items-center gap-1 hover:underline">
                        View all <ArrowRightIcon class="w-3 h-3" />
                    </Link>
                </div>

                <!-- Horizontal scroll on mobile, grid on tablet+ -->
                <div class="flex gap-4 overflow-x-auto pb-2 md:grid md:grid-cols-2 lg:grid-cols-3 md:overflow-visible snap-x snap-mandatory">
                    <Link
                        v-for="(enrollment, idx) in activeEnrollments"
                        :key="enrollment.id"
                        :href="route('student.enrollments.show', enrollment.id)"
                        class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden flex-shrink-0 w-72 md:w-auto snap-start hover:shadow-md transition group"
                    >
                        <!-- Gradient banner -->
                        <div :class="['h-2 bg-gradient-to-r', cardAccents[idx % cardAccents.length]]"></div>
                        <div class="p-4">
                            <div class="flex items-start justify-between gap-2 mb-2">
                                <div>
                                    <h3 class="font-semibold text-gray-900 dark:text-white text-sm leading-snug line-clamp-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition">
                                        {{ enrollment.course.name }}
                                    </h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ enrollment.course.code }}</p>
                                </div>
                                <span class="shrink-0 text-xs font-bold bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-lg px-2 py-1">
                                    {{ enrollment.course.credit_hours }} cr
                                </span>
                            </div>

                            <div class="space-y-1">
                                <div class="flex items-center gap-1.5 text-xs text-gray-600 dark:text-gray-400">
                                    <ClockIcon class="w-3.5 h-3.5 shrink-0" />
                                    {{ enrollment.instructor ? enrollment.instructor.name : 'TBA' }}
                                </div>
                                <div class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-500">
                                    <AcademicCapIcon class="w-3.5 h-3.5 shrink-0" />
                                    {{ enrollment.section?.name ?? '' }}
                                    <span v-if="enrollment.section?.track" class="text-gray-400">· {{ enrollment.section.track.name }}</span>
                                </div>
                            </div>
                        </div>
                    </Link>
                </div>
            </section>

            <!-- ── GPA Trend + Recent Grades ─────────────── -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- GPA Chart -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-gray-700">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-sm font-bold text-gray-900 dark:text-white">GPA Trend</h2>
                        <span class="text-xs text-gray-400">per semester</span>
                    </div>
                    <div class="h-40">
                        <canvas id="gpaChart"></canvas>
                    </div>
                </div>

                <!-- Recent Grades -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-gray-700">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-sm font-bold text-gray-900 dark:text-white">Recent Grades</h2>
                        <Link :href="route('student.grades')" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline font-medium">
                            See all
                        </Link>
                    </div>

                    <div v-if="recentGrades.length" class="space-y-2">
                        <div
                            v-for="grade in recentGrades"
                            :key="grade.id"
                            class="flex items-center gap-3"
                        >
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-medium text-gray-900 dark:text-gray-100 truncate">{{ grade.course.name }}</p>
                                <p class="text-[11px] text-gray-400">{{ grade.course.credit_hours }} credit hrs</p>
                            </div>
                            <span :class="['text-xs font-bold px-2.5 py-1 rounded-lg shrink-0', gradeColor(grade.grade_letter)]">
                                {{ grade.grade_letter ?? '—' }}
                            </span>
                        </div>
                    </div>
                    <p v-else class="text-sm text-gray-400 text-center py-6">No grades recorded yet.</p>
                </div>
            </div>

        </div>
    </AppLayout>
</template>
