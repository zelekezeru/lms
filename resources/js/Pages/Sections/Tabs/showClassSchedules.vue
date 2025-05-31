<script setup>
import { computed, ref, watch } from "vue";
import Button from "primevue/button";
import Dropdown from "primevue/dropdown";
import { Link, useForm } from "@inertiajs/vue3";
import { PlusCircleIcon, XMarkIcon } from "@heroicons/vue/24/outline";
import { DatePicker, Select } from "primevue";
import TextInput from "@/Components/TextInput.vue";
import Form from "../Form.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import "sweetalert2/dist/sweetalert2.min.css";
import Swal from "sweetalert2";

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

    rooms: {
        type: Array,
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
    return props.section.classSchedules.filter(
        (classSchedule) => classSchedule.dayOfWeek == selectedDay.value
    );
});

const minDate = computed(() => {
    return new Date(props.activeSemester.start_date);
});

const maxDate = computed(() => {
    return new Date(props.activeSemester.end_date);
});

const createSchedule = ref(false);

const scheduleForm = useForm({
    section_id: props.section.id,
    semester_id: props.activeSemester.id,
    day_of_week: selectedDay.value,
    course_id: "",
    start_date: "",
    end_date: "",
    start_time: "",
    end_time: "",
    room_id: "",
});

watch(
    () => scheduleForm.start_time,
    (newVal) => {
        if (newVal instanceof Date) {
            const endDate = new Date(newVal);
            endDate.setMinutes(newVal.getMinutes() + 20);
            scheduleForm.end_time = endDate;
        }
    }
);
const addSchedule = () => {
    scheduleForm.day_of_week = selectedDay.value;
    scheduleForm.post(route("classSchedules.store"), {
        onSuccess: () => {
            Swal.fire("Added!", "Schedule added successfully.", "success");
            createSchedule.value = false;
            scheduleForm.reset();
        },
    });
};
</script>
<template>
<div class="max-w-7xl mx-auto py-10 px-4 space-y-8">

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
                        v-for="(schedule, index) in filteredClassSchedules"
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

</template>
