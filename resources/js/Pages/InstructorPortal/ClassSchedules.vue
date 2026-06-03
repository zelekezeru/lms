<script setup>
import { computed, ref } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import {
    CalendarDaysIcon,
    ClockIcon,
    MapPinIcon,
    UserGroupIcon,
    AcademicCapIcon,
} from "@heroicons/vue/24/outline";

const props = defineProps({
    instructor: { type: Object, required: true },
});

const days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];

const todayName = () => {
    const d = new Date().getDay();
    const map = [6, 0, 1, 2, 3, 4, 5];
    return days[map[d]];
};

const schedules = computed(() => props.instructor.classSchedules ?? []);

const selectedDay = ref(
    schedules.value.some((s) => s.dayOfWeek === todayName())
        ? todayName()
        : (schedules.value[0]?.dayOfWeek ?? days[0])
);

const filtered = computed(() =>
    schedules.value.filter((s) => s.dayOfWeek === selectedDay.value)
);

const hasSched = (day) => schedules.value.some((s) => s.dayOfWeek === day);

const formatTime = (t) => {
    if (!t) return "—";
    const [h, m] = t.split(":");
    const hr = parseInt(h);
    const ampm = hr >= 12 ? "PM" : "AM";
    return `${hr % 12 || 12}:${m} ${ampm}`;
};

const accentColors = [
    "border-violet-500 bg-violet-50 dark:bg-violet-900/10",
    "border-sky-500 bg-sky-50 dark:bg-sky-900/10",
    "border-emerald-500 bg-emerald-50 dark:bg-emerald-900/10",
    "border-rose-500 bg-rose-50 dark:bg-rose-900/10",
    "border-amber-500 bg-amber-50 dark:bg-amber-900/10",
    "border-indigo-500 bg-indigo-50 dark:bg-indigo-900/10",
];

const dotColors = ["bg-violet-500", "bg-sky-500", "bg-emerald-500", "bg-rose-500", "bg-amber-500", "bg-indigo-500"];
</script>

<template>
    <AppLayout>
        <div class="pb-24 md:pb-8 max-w-3xl mx-auto px-4 md:px-6 pt-4 space-y-5">

            <!-- Header -->
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-violet-50 dark:bg-violet-900/20 flex items-center justify-center">
                    <CalendarDaysIcon class="w-5 h-5 text-violet-600 dark:text-violet-400" />
                </div>
                <div>
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white">Class Schedule</h1>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ schedules.length }} classes this week</p>
                </div>
            </div>

            <!-- Day pills -->
            <div class="flex gap-2 overflow-x-auto pb-1 scrollbar-none">
                <button
                    v-for="day in days"
                    :key="day"
                    @click="selectedDay = day"
                    :class="[
                        'relative flex flex-col items-center gap-0.5 rounded-2xl px-3.5 py-2.5 shrink-0 transition-all duration-200 text-xs font-semibold',
                        selectedDay === day
                            ? 'bg-violet-600 text-white shadow-md shadow-violet-200 dark:shadow-violet-900/30'
                            : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-gray-700 hover:border-violet-300 dark:hover:border-violet-700',
                    ]"
                >
                    <span>{{ day.slice(0, 3) }}</span>
                    <span :class="['w-1 h-1 rounded-full', hasSched(day) ? (selectedDay === day ? 'bg-white/70' : 'bg-violet-500') : 'opacity-0']"></span>
                </button>
            </div>

            <!-- Timeline -->
            <div>
                <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                    {{ selectedDay }}'s Classes
                    <span class="ml-1.5 text-xs font-normal text-gray-400">({{ filtered.length }})</span>
                </h2>

                <div v-if="filtered.length" class="space-y-3">
                    <div v-for="(schedule, idx) in filtered" :key="schedule.id" class="flex gap-4">
                        <!-- Time column -->
                        <div class="flex flex-col items-center shrink-0 w-14">
                            <span class="text-xs font-bold text-gray-900 dark:text-white">{{ formatTime(schedule.startTime) }}</span>
                            <div class="flex-1 w-px bg-gray-200 dark:bg-gray-700 my-1"></div>
                            <span class="text-xs text-gray-400">{{ formatTime(schedule.endTime) }}</span>
                        </div>

                        <!-- Card -->
                        <div :class="['flex-1 rounded-2xl border-l-4 p-4 shadow-sm mb-1', accentColors[idx % accentColors.length]]">
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white line-clamp-2 leading-snug">{{ schedule.course.name }}</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ schedule.course.code }}</p>
                                </div>
                                <span :class="['w-2.5 h-2.5 rounded-full shrink-0 mt-1', dotColors[idx % dotColors.length]]"></span>
                            </div>

                            <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-1.5">
                                <div v-if="schedule.section" class="flex items-center gap-1.5 text-xs text-gray-600 dark:text-gray-400">
                                    <UserGroupIcon class="w-3.5 h-3.5 shrink-0 text-gray-400" />
                                    {{ schedule.section.name }}
                                </div>
                                <div v-if="schedule.room" class="flex items-center gap-1.5 text-xs text-gray-600 dark:text-gray-400">
                                    <MapPinIcon class="w-3.5 h-3.5 shrink-0 text-gray-400" />
                                    {{ schedule.room.name }} (cap. {{ schedule.room.capacity }})
                                </div>
                                <div v-if="schedule.startDate" class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-500">
                                    <CalendarDaysIcon class="w-3.5 h-3.5 shrink-0 text-gray-400" />
                                    {{ schedule.startDate }} → {{ schedule.endDate }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty state -->
                <div v-else class="text-center py-14">
                    <div class="w-14 h-14 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center mx-auto mb-4">
                        <CalendarDaysIcon class="w-7 h-7 text-gray-400" />
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">No classes on {{ selectedDay }}</p>
                    <p class="text-xs text-gray-400 mt-1">Enjoy your day off!</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
