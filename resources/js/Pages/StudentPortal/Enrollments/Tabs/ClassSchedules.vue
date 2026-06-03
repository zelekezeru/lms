<script setup>
import { computed, ref } from "vue";
import Button from "primevue/button";
import { Select } from "primevue";
import {
    ClockIcon,
    MapPinIcon,
    UserIcon,
    AcademicCapIcon
} from "@heroicons/vue/24/outline";

const props = defineProps({
    enrollment: {
        required: true,
        type: Object,
    },

    activeSemester: {
        required: true,
        type: Object,
    },

    classSchedules: {
        required: true,
        type: Array,
    },
});

const selectedDay = ref("Monday");
const days = [
    "Monday",
    "Tuesday",
    "Wednesday",
    "Thursday",
    "Friday",
    "Saturday",
    "Sunday",
];

const filteredClassSchedules = computed(() => {
    return props.classSchedules.filter(
        (classSchedule) => classSchedule.dayOfWeek == selectedDay.value
    );
});
</script>

<template>
    <div class="max-w-7xl mx-auto py-6 space-y-8">
        <!-- Header Section -->
        <div class="space-y-1.5">
            <h1 class="text-xl font-black text-gray-900 dark:text-white tracking-tight flex items-center gap-2">
                <ClockIcon class="w-6 h-6 text-indigo-500" />
                Course Class Schedules
            </h1>
            <p class="text-xs text-gray-400 font-light">
                Class sessions for {{ activeSemester.year.name }} Semester - Level {{ activeSemester.level }}
            </p>
        </div>

        <!-- Day Selector (Glassmorphic Pill Bar) -->
        <div class="bg-white/70 dark:bg-gray-800/70 backdrop-blur-md p-2 rounded-2xl border border-white/20 dark:border-gray-700/50 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <!-- Buttons on large screens -->
                <div class="hidden sm:flex flex-wrap gap-1.5 w-full justify-center">
                    <button
                        v-for="day in days"
                        :key="day"
                        @click="selectedDay = day"
                        class="transition duration-200 font-bold px-4 py-2 rounded-xl text-sm"
                        :class="[
                            selectedDay === day
                                ? 'bg-indigo-650 text-white shadow-md'
                                : 'text-gray-650 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700/60',
                        ]"
                    >
                        {{ day }}
                    </button>
                </div>

                <!-- Dropdown on small screens -->
                <div class="sm:hidden w-full">
                    <Select
                        v-model="selectedDay"
                        :options="days"
                        placeholder="Select a day"
                        class="w-full rounded-xl"
                    />
                </div>
            </div>
        </div>

        <!-- Selected Day Info header -->
        <div class="flex items-center justify-between">
            <h2 class="text-base font-black text-gray-900 dark:text-white tracking-tight flex items-center gap-2">
                <span class="w-2 h-4 bg-indigo-650 rounded-full"></span>
                {{ selectedDay }} Slots
            </h2>
            <span class="text-[10px] text-gray-400 font-bold bg-gray-105 dark:bg-gray-800 px-3 py-1 rounded-full border border-gray-200/20">
                {{ filteredClassSchedules.length }} Classes
            </span>
        </div>

        <!-- Timeline List Layout -->
        <transition
            mode="out-in"
            enter-active-class="transition duration-300 ease-out"
            enter-to-class="opacity-100 translate-y-0"
            enter-from-class="opacity-0 translate-y-4"
            leave-active-class="transition duration-250 ease-in"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 translate-y-4"
        >
            <div :key="selectedDay" class="space-y-4">
                <div v-if="filteredClassSchedules.length > 0" class="relative pl-6 border-l-2 border-indigo-100 dark:border-indigo-900/65 space-y-6">
                    <div
                        v-for="schedule in filteredClassSchedules"
                        :key="schedule.id"
                        class="relative bg-white/70 dark:bg-gray-800/70 backdrop-blur-md p-6 rounded-3xl border border-white/20 dark:border-gray-700/50 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6 hover:shadow-md transition duration-200"
                    >
                        <!-- Vertical timeline node dot -->
                        <div class="absolute left-[-32px] top-[26px] w-4 h-4 rounded-full border-4 border-white dark:border-gray-900 bg-indigo-650 shadow"></div>

                        <!-- Left: Time Frame block -->
                        <div class="flex items-center gap-3 min-w-[160px]">
                            <div class="p-2.5 bg-indigo-50 dark:bg-indigo-950/40 text-indigo-650 dark:text-indigo-400 rounded-xl">
                                <ClockIcon class="w-6 h-6" />
                            </div>
                            <div>
                                <span class="text-sm font-bold text-gray-900 dark:text-white block">{{ schedule.startTime }}</span>
                                <span class="text-xs text-gray-400 block mt-0.5">to {{ schedule.endTime }}</span>
                            </div>
                        </div>

                        <!-- Middle: Course details & Room tag -->
                        <div class="flex-1 space-y-2">
                            <h3 class="text-sm font-black text-gray-900 dark:text-white leading-tight">
                                {{ enrollment.course.name }}
                            </h3>
                            <div class="flex flex-wrap gap-2">
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold bg-purple-50 text-purple-700 dark:bg-purple-950/40 dark:text-purple-300 px-2.5 py-0.5 rounded-full border border-purple-100/50 dark:border-purple-900/30">
                                    <MapPinIcon class="w-3 h-3" />
                                    Room: {{ schedule.room ? schedule.room.name : 'TBD' }}
                                </span>
                                <span v-if="schedule.room?.capacity" class="inline-flex items-center gap-1 text-[10px] font-bold bg-gray-50 text-gray-600 dark:bg-gray-900/40 dark:text-gray-400 px-2.5 py-0.5 rounded-full border border-gray-200/30">
                                    Capacity: {{ schedule.room.capacity }}
                                </span>
                                <span v-if="schedule.section" class="inline-flex items-center gap-1 text-[10px] font-bold bg-green-50 text-green-700 dark:bg-green-950/40 dark:text-green-300 px-2.5 py-0.5 rounded-full border border-green-100/50 dark:border-green-900/30">
                                    Section: {{ schedule.section.name }}
                                </span>
                            </div>
                        </div>

                        <!-- Right: Teacher info -->
                        <div class="flex items-center gap-3 pt-4 md:pt-0 border-t md:border-t-0 border-gray-100 dark:border-gray-700/60 min-w-[200px] md:justify-end">
                            <div class="p-2 bg-gray-50 dark:bg-gray-700/50 text-gray-400 rounded-lg">
                                <UserIcon class="w-5 h-5" />
                            </div>
                            <div>
                                <span class="text-xs text-gray-400 block font-semibold uppercase tracking-wider">Instructor</span>
                                <span class="text-sm font-bold text-gray-800 dark:text-gray-250 block mt-0.5">
                                    {{ schedule.instructor ? schedule.instructor.name : (enrollment.instructor ? enrollment.instructor.name : "TBA") }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div v-else class="bg-white/70 dark:bg-gray-800/70 backdrop-blur-md p-12 text-center rounded-3xl border border-white/20 dark:border-gray-700/50 shadow-sm text-gray-400">
                    😴 No classes scheduled for {{ selectedDay }} for this course.
                </div>
            </div>
        </transition>
    </div>
</template>
