<script setup>
import { computed, ref } from "vue";
import Button from "primevue/button";
import Dropdown from "primevue/dropdown";
import { Link } from "@inertiajs/vue3";
import { DatePicker, Select } from "primevue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import "sweetalert2/dist/sweetalert2.min.css";
import AppLayout from "@/Layouts/AppLayout.vue";

const props = defineProps({
    instructor: {
        type: Object,
        required: true,
    },
    activeSemester: {
        type: Object,
        required: true,
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
    return props.instructor.classSchedules.filter(
        (classSchedule) => classSchedule.dayOfWeek == selectedDay.value
    );
});
</script>
<template>
    <AppLayout>
        <div class="max-w-7xl mx-auto py-10 px-4 space-y-8">
            <!-- Header -->
            <div class="text-center">
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
                    Your Class Schedules for
                    {{ activeSemester.year.name }} Semester -
                    {{ activeSemester.level }}
                </h1>
            </div>

            <!-- Day Selector -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
                <div
                    class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
                >
                    <div class="text-gray-700 dark:text-gray-300 font-semibold">
                        Select a Day
                    </div>

                    <!-- Buttons on large screens -->
                    <div class="hidden sm:flex flex-wrap gap-2">
                        <Button
                            v-for="day in days"
                            :key="day"
                            :label="day"
                            :outlined="selectedDay !== day"
                            :severity="selectedDay === day ? 'primary' : null"
                            @click="selectedDay = day"
                            :class="[
                                selectedDay === day
                                    ? 'bg-blue-600 text-white hover:bg-blue-700'
                                    : 'bg-white text-gray-800 border border-gray-300 hover:bg-gray-100 dark:bg-gray-700 dark:text-white dark:border-gray-600 hover:dark:bg-gray-600',
                                'transition font-medium px-4 py-2 rounded-lg text-sm',
                            ]"
                        />
                    </div>

                    <!-- Dropdown on small screens -->
                    <div class="sm:hidden">
                        <Dropdown
                            v-model="selectedDay"
                            :options="days"
                            placeholder="Select a day"
                            class="w-full"
                        />
                    </div>
                </div>
            </div>

            <!-- Selected Day Title -->
            <h2 class="text-lg font-bold text-gray-700 dark:text-gray-200 mt-4">
                {{
                    selectedDay
                        ? `${selectedDay} Class Schedule`
                        : "Class Schedule"
                }}
            </h2>

            <!-- Schedule Table -->
            <transition
                mode="out-in"
                enter-active-class="transition duration-300 ease-out"
                enter-to-class="opacity-100 scale-100"
                leave-active-class="transition duration-200 ease-in"
                leave-from-class="opacity-100 scale-100"
                leave-to-class="opacity-0 scale-75"
            >
                <div
                    :key="selectedDay"
                    class="overflow-x-auto bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-300 dark:border-gray-600"
                >
                    <table class="w-full min-w-[800px] table-fixed">
                        <thead class="bg-gray-100 dark:bg-gray-700">
                            <tr>
                                <th
                                    class="text-left px-4 py-2 font-medium text-sm text-gray-800 dark:text-gray-200"
                                >
                                    Course Name
                                </th>
                                <th
                                    class="text-left px-4 py-2 font-medium text-sm text-gray-800 dark:text-gray-200"
                                >
                                    Time
                                </th>
                                <th
                                    class="text-left px-4 py-2 font-medium text-sm text-gray-800 dark:text-gray-200"
                                >
                                    Date Range
                                </th>
                                <th
                                    class="text-left px-4 py-2 font-medium text-sm text-gray-800 dark:text-gray-200"
                                >
                                    Room (Capacity)
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Schedule Rows -->
                            <tr
                                v-if="filteredClassSchedules.length > 0"
                                v-for="(
                                    schedule, index
                                ) in filteredClassSchedules"
                                :key="schedule.id"
                                :class="[
                                    index % 2 === 0
                                        ? 'bg-white dark:bg-gray-800'
                                        : 'bg-gray-50 dark:bg-gray-700',
                                    'border-b border-gray-200 dark:border-gray-600',
                                ]"
                            >
                                <!-- Course Name -->
                                <td
                                    class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300"
                                >
                                    {{ schedule.course.name }}
                                </td>

                                <!-- Time -->
                                <td
                                    class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300"
                                >
                                    {{ schedule.startTime }} -
                                    {{ schedule.endTime }}
                                </td>

                                <!-- Date Range -->
                                <td
                                    class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300"
                                >
                                    {{ schedule.startDate }} -
                                    {{ schedule.endDate }}
                                </td>

                                <!-- Room -->
                                <td
                                    class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300"
                                >
                                    <span v-if="schedule.room">
                                        {{ schedule.room.name }} ({{
                                            schedule.room.capacity
                                        }})
                                    </span>
                                    <span
                                        v-else
                                        class="text-gray-500 dark:text-gray-400"
                                        >TBD</span
                                    >
                                </td>
                            </tr>

                            <!-- No Schedules -->
                            <tr v-else>
                                <td
                                    colspan="4"
                                    class="text-center px-4 py-6 text-sm text-gray-500 dark:text-gray-300"
                                >
                                    No schedules set for {{ selectedDay }}.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </transition>
        </div>
    </AppLayout>
</template>
