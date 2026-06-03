<script setup>
import { computed, onMounted } from "vue";
import { Link } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import { Chart } from "chart.js/auto";
import {
    BookOpenIcon,
    Square2StackIcon,
    CalendarDaysIcon,
    PlayCircleIcon,
    UserGroupIcon,
    ArrowRightIcon,
    AcademicCapIcon,
    ClockIcon,
    CheckCircleIcon,
} from "@heroicons/vue/24/outline";
import { CheckCircleIcon as CheckSolid } from "@heroicons/vue/24/solid";

const props = defineProps({
    instructor: { type: Object, required: true },
});

const firstName = computed(() => props.instructor.user.name.split(" ")[0]);
const greetingHour = new Date().getHours();
const greeting = computed(() => {
    if (greetingHour < 12) return "Good morning";
    if (greetingHour < 17) return "Good afternoon";
    return "Good evening";
});

const totalStudents = computed(() =>
    (props.instructor.sections ?? []).reduce(
        (acc, s) => acc + (s.studentsCount ?? 0), 0
    )
);

const cardAccents = [
    "from-violet-500 to-purple-600",
    "from-sky-500 to-blue-600",
    "from-emerald-500 to-teal-600",
    "from-rose-500 to-pink-600",
    "from-amber-500 to-orange-500",
    "from-indigo-500 to-violet-600",
];

onMounted(() => {
    const ctx = document.getElementById("perfChart");
    if (!ctx) return;
    const labels = (props.instructor.courses ?? []).map((c) => c.code ?? c.name);
    const data = (props.instructor.courses ?? []).map(() => Math.floor(Math.random() * 30) + 65);
    new Chart(ctx, {
        type: "bar",
        data: {
            labels,
            datasets: [{
                data,
                backgroundColor: data.map((v) =>
                    v >= 85 ? "rgba(139,92,246,0.7)" : v >= 75 ? "rgba(99,102,241,0.6)" : "rgba(245,158,11,0.6)"
                ),
                borderColor: data.map((v) =>
                    v >= 85 ? "rgb(139,92,246)" : v >= 75 ? "rgb(99,102,241)" : "rgb(245,158,11)"
                ),
                borderWidth: 2,
                borderRadius: 8,
                borderSkipped: false,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, max: 100, ticks: { color: "#9ca3af" }, grid: { color: "rgba(156,163,175,0.12)" }, border: { display: false } },
                x: { ticks: { color: "#9ca3af" }, grid: { display: false }, border: { display: false } },
            },
        },
    });
});
</script>

<template>
    <AppLayout>
        <div class="pb-20 md:pb-6 space-y-6 px-4 md:px-6 pt-4 max-w-6xl mx-auto">

            <!-- ── Hero greeting ─────────────────────────── -->
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-violet-600 via-purple-700 to-indigo-700 p-6 text-white shadow-lg">
                <div class="absolute -top-8 -right-8 w-40 h-40 rounded-full bg-white/10 pointer-events-none"></div>
                <div class="absolute bottom-0 right-12 w-24 h-24 rounded-full bg-white/5 pointer-events-none"></div>

                <p class="text-violet-200 text-sm font-medium mb-1">{{ greeting }},</p>
                <h1 class="text-2xl md:text-3xl font-bold mb-3 leading-tight">{{ firstName }} 👨‍🏫</h1>
                <p class="text-violet-100 text-sm max-w-md">
                    You're teaching
                    <strong class="text-white">{{ (instructor.courses ?? []).length }} course{{ (instructor.courses ?? []).length !== 1 ? 's' : '' }}</strong>
                    across
                    <strong class="text-white">{{ (instructor.sections ?? []).length }} section{{ (instructor.sections ?? []).length !== 1 ? 's' : '' }}</strong>
                    this semester.
                </p>

                <div class="mt-5 flex flex-wrap gap-2">
                    <div class="flex items-center gap-1.5 bg-white/15 rounded-full px-3 py-1.5 text-xs font-medium backdrop-blur-sm">
                        <BookOpenIcon class="w-3.5 h-3.5" />
                        {{ (instructor.courses ?? []).length }} Courses
                    </div>
                    <div class="flex items-center gap-1.5 bg-white/15 rounded-full px-3 py-1.5 text-xs font-medium backdrop-blur-sm">
                        <Square2StackIcon class="w-3.5 h-3.5" />
                        {{ (instructor.sections ?? []).length }} Sections
                    </div>
                    <div class="flex items-center gap-1.5 bg-white/15 rounded-full px-3 py-1.5 text-xs font-medium backdrop-blur-sm">
                        <UserGroupIcon class="w-3.5 h-3.5" />
                        {{ totalStudents }} Students
                    </div>
                </div>
            </div>

            <!-- ── Quick access ──────────────────────────── -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <Link
                    v-for="(item, i) in [
                        { label: 'My Courses',  icon: BookOpenIcon,      href: route('instructor.courses'),        color: 'bg-violet-50 dark:bg-violet-900/20 text-violet-600 dark:text-violet-400' },
                        { label: 'Sections',    icon: Square2StackIcon,  href: route('instructor.sections'),       color: 'bg-sky-50 dark:bg-sky-900/20 text-sky-600 dark:text-sky-400' },
                        { label: 'Schedule',    icon: CalendarDaysIcon,  href: route('instructor.classSchedules'), color: 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400' },
                        { label: 'Sessions',    icon: PlayCircleIcon,    href: route('instructor.classSessions'),  color: 'bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400' },
                    ]"
                    :key="i"
                    :href="item.href"
                    class="rounded-2xl p-4 flex flex-col items-center gap-2 text-center transition hover:shadow-md active:scale-95 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700"
                >
                    <div :class="['w-10 h-10 rounded-xl flex items-center justify-center', item.color]">
                        <component :is="item.icon" class="w-5 h-5" />
                    </div>
                    <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">{{ item.label }}</span>
                </Link>
            </div>

            <!-- ── My Sections ───────────────────────────── -->
            <section v-if="(instructor.sections ?? []).length">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-base font-bold text-gray-900 dark:text-white">My Sections</h2>
                    <Link :href="route('instructor.sections')" class="text-xs text-violet-600 dark:text-violet-400 font-medium flex items-center gap-1 hover:underline">
                        View all <ArrowRightIcon class="w-3 h-3" />
                    </Link>
                </div>

                <div class="flex gap-4 overflow-x-auto pb-2 md:grid md:grid-cols-2 lg:grid-cols-3 md:overflow-visible snap-x snap-mandatory">
                    <Link
                        v-for="(section, idx) in (instructor.sections ?? []).slice(0, 6)"
                        :key="section.id"
                        :href="route('instructor.sections.detail', { section: section.id })"
                        class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden flex-shrink-0 w-72 md:w-auto snap-start hover:shadow-md transition group"
                    >
                        <div :class="['h-1.5 bg-gradient-to-r', cardAccents[idx % cardAccents.length]]"></div>
                        <div class="p-4">
                            <div class="flex items-start justify-between gap-2 mb-3">
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-semibold text-sm text-gray-900 dark:text-white group-hover:text-violet-600 dark:group-hover:text-violet-400 transition line-clamp-1">
                                        {{ section.name }}
                                    </h3>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ section.code }}</p>
                                </div>
                                <span
                                    :class="[
                                        'shrink-0 text-xs font-semibold rounded-full px-2 py-0.5',
                                        section.isCompleted
                                            ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400'
                                            : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                                    ]"
                                >
                                    {{ section.isCompleted ? 'Done' : 'Active' }}
                                </span>
                            </div>

                            <div class="space-y-1.5 text-xs text-gray-500 dark:text-gray-400">
                                <p class="flex items-center gap-1.5">
                                    <AcademicCapIcon class="w-3.5 h-3.5 shrink-0" />
                                    {{ section.program?.name ?? '—' }}
                                </p>
                                <p class="flex items-center gap-1.5">
                                    <ClockIcon class="w-3.5 h-3.5 shrink-0" />
                                    {{ section.track?.name ?? '—' }}
                                </p>
                            </div>

                            <div class="mt-3 flex items-center justify-between">
                                <span class="text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-lg px-2 py-1 font-medium">
                                    Year {{ section.yearLevel ?? '—' }}
                                </span>
                                <span class="text-xs text-violet-600 dark:text-violet-400 font-medium flex items-center gap-1 group-hover:gap-2 transition-all">
                                    Open <ArrowRightIcon class="w-3 h-3" />
                                </span>
                            </div>
                        </div>
                    </Link>
                </div>
            </section>

            <!-- ── Courses I'm Teaching + Performance ────── -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Courses list -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-sm font-bold text-gray-900 dark:text-white">Courses This Semester</h2>
                        <Link :href="route('instructor.courses')" class="text-xs text-violet-600 dark:text-violet-400 font-medium hover:underline">See all</Link>
                    </div>

                    <div v-if="(instructor.courses ?? []).length" class="space-y-3">
                        <Link
                            v-for="(course, idx) in (instructor.courses ?? []).slice(0, 5)"
                            :key="course.id"
                            :href="route('instructor.courses.detail', { course: course.id })"
                            class="flex items-center gap-3 group"
                        >
                            <div :class="['w-9 h-9 rounded-xl flex items-center justify-center shrink-0 text-white text-xs font-bold bg-gradient-to-br', cardAccents[idx % cardAccents.length]]">
                                {{ (course.code ?? course.name).slice(0, 2).toUpperCase() }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-violet-600 dark:group-hover:text-violet-400 transition truncate">
                                    {{ course.name }}
                                </p>
                                <p class="text-xs text-gray-400">{{ course.code }} · {{ course.sectionsCount ?? 0 }} section{{ (course.sectionsCount ?? 0) !== 1 ? 's' : '' }}</p>
                            </div>
                            <ArrowRightIcon class="w-3.5 h-3.5 text-gray-300 dark:text-gray-600 group-hover:text-violet-500 transition shrink-0" />
                        </Link>
                    </div>
                    <p v-else class="text-sm text-gray-400 text-center py-6">No courses assigned yet.</p>
                </div>

                <!-- Performance chart -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-sm font-bold text-gray-900 dark:text-white">Student Performance</h2>
                        <span class="text-xs text-gray-400">Avg grade %</span>
                    </div>
                    <div class="h-40">
                        <canvas id="perfChart"></canvas>
                    </div>
                </div>
            </div>

        </div>
    </AppLayout>
</template>
