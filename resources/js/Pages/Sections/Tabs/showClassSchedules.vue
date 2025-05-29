<script setup>
import { ref } from "vue";
import Button from "primevue/button";
import Dropdown from "primevue/dropdown";
import { useForm } from "@inertiajs/vue3";
import { PlusCircleIcon } from "@heroicons/vue/24/outline";

const props = defineProps({
    section: {
        required: true,
        type: Object,
    },

    activeCourses: {
        type: Array,
        required: true,
    },

    activeSemester: {
        type: Object,
        requried: true,
    },
});
const createTrack = ref(false);

const trackForm = useForm({
    section_id: props.section.id,
    semester_id: props.activeSemester.id,
    course_id: "",
    start_date: "",
    end_date: "",
    start_time: "",
    end_time: "",
});

const schedules = ref([
    {
        id: 1,
        course: { name: "Introduction to Programming" },
        startTime: "08:00",
        endTime: "09:30",
        startDate: "2025-06-01",
        endDate: "2025-07-15",
        room: "Room 101",
    },
    {
        id: 2,
        course: { name: "Database Systems" },
        startTime: "10:00",
        endTime: "11:30",
        startDate: "2025-06-01",
        endDate: "2025-07-15",
        room: "Room 202",
    },
    {
        id: 3,
        course: { name: "Web Development" },
        startTime: "13:00",
        endTime: "14:30",
        startDate: "2025-06-01",
        endDate: "2025-07-15",
        room: "Room 303",
    },
    {
        id: 4,
        course: { name: "Software Engineering" },
        startTime: "15:00",
        endTime: "16:30",
        startDate: "2025-06-01",
        endDate: "2025-07-15",
        room: null,
    },
]);
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
</script>
<template>
    <div class="grid grid-cols-2 gap-4">
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

        <div class="col-span-2 flex justify-end mt-4">
            <Button
                label="Add New Schedule"
                :icon="PlusCircleIcon"
                class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg text-sm transition"
                @click="openScheduleForm"
            />
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
                    class="w-full table-fixed border border-gray-300 dark:border-gray-600"
                >
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-700">
                            <th
                                class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r w-1/4"
                            >
                                Course Name
                            </th>
                            <th
                                class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r w-1/4"
                            >
                                Time
                            </th>
                            <th
                                class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r w-1/4"
                            >
                                Date Range
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
                            v-for="(schedule, index) in schedules"
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
                                {{ schedule.course.name }}
                            </td>
                            <td
                                class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r"
                            >
                                {{ schedule.startTime }} -
                                {{ schedule.endTime }}
                            </td>
                            <td
                                class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r"
                            >
                                {{ schedule.startDate }} -
                                {{ schedule.endDate }}
                            </td>
                            <td
                                class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300"
                            >
                                {{ schedule.room ?? "TBD" }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </transition>
    </div>
</template>
