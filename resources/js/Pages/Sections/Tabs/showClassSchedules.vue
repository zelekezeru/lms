<script setup>
import { computed, ref, watch } from "vue";
import Button from "primevue/button";
import Dropdown from "primevue/dropdown";
import { useForm } from "@inertiajs/vue3";
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
    room: "",
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
            <button
                class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg text-sm transition flex items-center gap-2"
                @click="createSchedule = !createSchedule"
            >
                <component
                    :is="createSchedule ? XMarkIcon : PlusCircleIcon"
                    class="w-5 h-5"
                />
                Add New Schedule
            </button>
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
                            v-for="(schedule, index) in section.classSchedules.filter(classSchedule => classSchedule.dayOfWeek == selectedDay)"
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

                        <!-- Create New Track Row -->
                        <transition
                            enter-active-class="transition duration-300 ease-out"
                            enter-from-class="-translate-y-2 opacity-0"
                            enter-to-class="translate-y-0 opacity-100"
                            leave-active-class="transition duration-200 ease-in"
                            leave-from-class="translate-y-0 opacity-100"
                            leave-to-class="-translate-y-2 opacity-0"
                        >
                            <tr
                                v-if="createSchedule"
                                class="border-b border-gray-300 bg-gray-50 dark:bg-gray-700 dark:border-gray-600"
                            >
                                <td class="px-4 py-2">
                                    <Select
                                        id="course"
                                        v-model="scheduleForm.course_id"
                                        :options="activeCourses"
                                        option-value="id"
                                        option-label="name"
                                        checkmark
                                        filter
                                        placeholder="Select Course"
                                        :maxSelectevdLabels="3"
                                        class="w-full"
                                    />
                                </td>

                                <td class="px-4 py-2">
                                    <div class="flex gap-3 items-center">
                                        <DatePicker
                                            id="datepicker-timeonly"
                                            v-model="scheduleForm.start_time"
                                            timeOnly
                                            placeholder="Start Date"
                                            hour-format="12"
                                            fluid
                                        />
                                        <p>-</p>
                                        <DatePicker
                                            id="datepicker-timeonly"
                                            v-model="scheduleForm.end_time"
                                            placeholder="End Date"
                                            timeOnly
                                            hour-format="12"
                                            fluid
                                        />
                                    </div>
                                </td>
                                <td class="px-4 py-2">
                                    <div class="flex gap-3 items-center">
                                        <DatePicker
                                            id="datepicker"
                                            :min-date="minDate"
                                            :max-date="maxDate"
                                            v-model="scheduleForm.start_date"
                                            placeholder="Start Date"
                                            hour-format="12"
                                        />
                                        <p>-</p>
                                        <DatePicker
                                            id="datepicker"
                                            :min-date="minDate"
                                            :max-date="maxDate"
                                            v-model="scheduleForm.end_date"
                                            placeholder="End Date"
                                            hour-format="12"
                                        />
                                    </div>
                                </td>
                                <td class="flex justify-between px-4 py-2">
                                    <TextInput
                                        v-model="scheduleForm.room"
                                        placeholder="Room"
                                        class="max-w-[70%] px-2 py-1 h-9 border rounded-md dark:bg-gray-800 dark:text-gray-100"
                                    />
                                    <PrimaryButton
                                        class="px-4 py-1 text-white bg-green-500 rounded-md h-9 hover:bg-green-600"
                                        @click="addSchedule"
                                    >
                                        {{ $t("common.save") }}
                                    </PrimaryButton>
                                </td>
                            </tr>
                        </transition>
                    </tbody>
                </table>
            </div>
        </transition>
    </div>
</template>
