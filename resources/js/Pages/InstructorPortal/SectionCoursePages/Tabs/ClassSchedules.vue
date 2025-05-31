<script setup>
import { computed, ref } from "vue";
import Button from "primevue/button";
import Dropdown from "primevue/dropdown";
import "sweetalert2/dist/sweetalert2.min.css";

const props = defineProps({
    section: {
        required: true,
        type: Object,
    },
    course: {
        required: true,
        type: Object,
    },

    classSchedules: {
        required: true,
        type: Object,
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

const minDate = computed(() => {
    return new Date(props.activeSemester.start_date);
});

const maxDate = computed(() => {
    return new Date(props.activeSemester.end_date);
});
</script>
<template>
    <div class="grid grid-cols-1 gap-4">
        <h1
            class="text-3xl font-bold mb-6 text-gray-900 dark:text-white flex items-center"
        >
            {{ course.code }}- {{ section.name }} Class Schedules
        </h1>
        <!-- Day Selector -->
        <div class="col-span-2">
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
                            : 'bg-white text-gray-800 border border-gray-300 hover:bg-gray-100 dark:bg-gray-800 dark:text-white dark:border-gray-600 hover:dark:bg-gray-700',
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

        <!-- Optional: Displaying selected day -->
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">
            {{
                selectedDay ? `${selectedDay} Class Schedule` : "Class Schedule"
            }}
        </h2>

        <!-- Table Container -->
        <transition
            mode="out-in"
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0 scale-75"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-75"
        >
            <div :key="selectedDay" class="overflow-x-auto w-full col-span-2">
                <table
                    class="w-full min-w-[800px] table-fixed border border-gray-300 dark:border-gray-600"
                >
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-700">
                            <th
                                class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r w-1/4"
                            >
                                Time
                            </th>
                            <th
                                class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 w-1/4"
                            >
                                Room
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-if="filteredClassSchedules.length > 0"
                            v-for="(schedule, index) in filteredClassSchedules"
                            :key="schedule.id"
                            :class="
                                index % 2 === 0
                                    ? 'bg-white dark:bg-gray-800'
                                    : 'bg-gray-50 dark:bg-gray-700'
                            "
                            class="border-b border-gray-300 dark:border-gray-600"
                        >
                            <td
                                class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r"
                            >
                                {{ schedule.startTime }} -
                                {{ schedule.endTime }}
                            </td>
                            <td
                                class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300"
                            >
                                {{ schedule.room ?? "TBD" }}
                            </td>
                        </tr>
                        <tr v-else>
                            <td
                                colspan="4"
                                class="text-center text-sm text-gray-500 py-4 dark:text-gray-300 font-medium"
                            >
                                No schedules set for {{ selectedDay }}.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </transition>
    </div>
</template>
