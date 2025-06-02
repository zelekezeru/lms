<script setup>
import { useForm } from "@inertiajs/vue3";
import { ref, onMounted, watch, computed } from "vue";
import Calendar from "primevue/calendar";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { Select } from "primevue";

const props = defineProps({
    section: { required: true, type: Object },
    course: { required: true, type: Object },
    instructor: { required: true, type: Object },
    activeSemester: { required: true, type: Object },
    classSessions: { required: true, type: Array },
    students: { required: true, type: Array },
    rooms: { required: true, type: Array },
});

const showForm = ref(false);
const showDetail = ref(false);
const takeAttendance = ref(false);
const selectedSession = ref(null);
const markAllAs = ref("");

function toggleForm() {
    showForm.value = !showForm.value;
}

function viewDetails(session) {
    selectedSession.value = session;
    showDetail.value = true;
    takeAttendance.value = false;
}

function markAttendance(session) {
    selectedSession.value = session;
    takeAttendance.value = true;
    showDetail.value = false;

    props.students.forEach((student) => {
        if (!(student.id in attendance.value)) {
            attendance.value[student.id] = "";
        }
    });
}

function backToList() {
    selectedSession.value = null;
    showDetail.value = false;
    takeAttendance.value = false;
}

const form = useForm({
    section_id: props.section.id,
    course_id: props.course.id,
    instructor_id: props.instructor.id,
    semester_id: props.activeSemester.id,

    start_date_time: null,
    end_time: null,
    type: null,
    class_about: "",
});

const startDateOnlyMin = computed(() => {
    if (!form.start_date_time) return null;
    const min = new Date(form.start_date_time);
    min.setHours(0, 0, 0, 0);
    return min;
});

const startDateOnlyMax = computed(() => {
    if (!form.start_date_time) return null;
    const max = new Date(form.start_date_time);
    max.setHours(23, 59, 59, 999);
    return max;
});

function addClassSession() {
    form.post(route("classSessions.store"), {
        onSuccess: () => {
            showForm.value = false;
            form.reset("room_id", "date_time", "class_about");
        },
    });
}

const attendance = ref({});

watch(selectedSession, (newSession) => {
    if (newSession?.attendances?.length > 0) {
        attendance.value = {};
        newSession.attendances.forEach((att) => {
            attendance.value[att.studentId] = att.status;
        });
    } else {
        attendance.value = {};
        props.students.forEach((student) => {
            attendance.value[student.id] = "";
        });
    }
});

function submitAttendance() {
    const payload = {
        class_session_id: selectedSession.value.id,
        records: Object.entries(attendance.value).map(
            ([student_id, status]) => ({
                student_id: parseInt(student_id),
                status,
            })
        ),
    };

    useForm(payload).post(route("attendances.store"), {
        onSuccess: () => {
            backToList();
        },
    });
}

watch(markAllAs, (newVal) => {
    props.students.forEach((student) => {
            attendance.value[student.id] = newVal;
        }
    );
});

onMounted(() => {
    props.students.forEach((student) => {
        if (!(student.id in attendance.value)) {
            attendance.value[student.id] = "";
        }
    });
});
</script>


<template>
    <div class="max-w-9xl mx-auto py-10 px-0 space-y-8">
        <!-- Header -->
        <div class="text-center">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
                Your Class Session for
                {{ activeSemester.year.name }} Semester -
                {{ activeSemester.level }} In This Section
            </h1>
        </div>

        <!-- Transition for detail/attendance/list views -->
        <transition
            mode="out-in"
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0 scale-75"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-75"
        >
            <!-- Details View -->
            <div
                v-if="showDetail"
                key="details"
                class="bg-white dark:bg-gray-800 p-6 rounded shadow"
            >
                <h2
                    class="text-lg font-semibold text-gray-800 dark:text-white mb-4"
                >
                    Class Session Overview
                </h2>

                <div
                    class="grid grid-cols-1 sm:grid-cols-2 gap-y-2 gap-x-6 text-sm text-gray-600 dark:text-gray-300"
                >
                    <p><strong>Date:</strong> {{ selectedSession.date }}</p>
                    <p>
                        <strong>Start Time:</strong>
                        {{ selectedSession.startTime }}
                    </p>
                    <p>
                        <strong>End Time:</strong> {{ selectedSession.endTime }}
                    </p>
                    <p><strong>Type:</strong> {{ selectedSession.type }}</p>
                    <p><strong>Status:</strong> {{ selectedSession.status }}</p>
                    <p>
                        <strong>Room:</strong>
                        {{ selectedSession.room?.name || "N/A" }}
                    </p>
                    <p>
                        <strong>About:</strong>
                        {{ selectedSession.classAbout || "N/A" }}
                    </p>
                </div>

                <!-- Attendance Summary Chart -->
                <!-- <section
                    v-if="
                        showDetail &&
                        selectedSession &&
                        selectedSession.attendances?.length > 0
                    "
                    class="bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-lg"
                >
                    <h3 class="font-bold text-gray-900 dark:text-white mb-4">
                        Attendance Summary
                    </h3>
                    <canvas
                        ref="attendanceChart"
                        style="width: 100%; height: 300px"
                    ></canvas>
                </section> -->

                <div class="mt-6">
                    <PrimaryButton @click="backToList">Back</PrimaryButton>
                </div>
            </div>

            <!-- Attendance View -->
            <div
                v-else-if="takeAttendance"
                key="attendance"
                class="bg-white dark:bg-gray-800 rounded shadow"
            >
                <div class="p-2">
                    <h2
                        class="text-lg font-semibold text-gray-800 dark:text-white mb-4"
                    >
                        Take Attendance For {{ selectedSession.date }} Session
                    </h2>
                    <div class="mb-4">
                        <h3
                            class="text-md font-medium text-gray-700 dark:text-gray-300 mb-2"
                        >
                            Mark All Students As: {{ markAllAs }}
                        </h3>
                        <div class="flex flex-wrap gap-2 sm:gap-4">
                            <label
                                class="inline-flex items-center gap-2 px-4 py-2 bg-gray-200 dark:bg-gray-700 text-green-800 dark:text-green-100 rounded cursor-pointer"
                                :class="{
                                    '!bg-green-500 !text-white':
                                        markAllAs === 'present',
                                }"
                            >
                                <input
                                    type="radio"
                                    class="sr-only"
                                    name="markAllAs"
                                    value="present"
                                    v-model="markAllAs"
                                />
                                <div
                                    class="w-5 h-5 border-2 border-gray-500 rounded-full flex items-center justify-center"
                                    :class="
                                        markAllAs === 'present'
                                            ? 'bg-green-500'
                                            : 'bg-white'
                                    "
                                >
                                    <div
                                        class="w-2 h-2 bg-white rounded-full"
                                        v-if="markAllAs === 'present'"
                                    ></div>
                                </div>

                                Present
                            </label>

                                                        <label
                                class="inline-flex items-center gap-2 px-4 py-2 bg-gray-200 dark:bg-gray-700 text-red-800 dark:text-red-100 rounded cursor-pointer"
                                :class="{
                                    '!bg-red-500 !text-white':
                                        markAllAs === 'absent',
                                }"
                            >
                                <input
                                    type="radio"
                                    class="sr-only"
                                    name="markAllAs"
                                    value="absent"
                                    v-model="markAllAs"
                                />
                                <div
                                    class="w-5 h-5 border-2 border-gray-500 rounded-full flex items-center justify-center"
                                    :class="
                                        markAllAs === 'absent'
                                            ? 'bg-red-500'
                                            : 'bg-white'
                                    "
                                >
                                    <div
                                        class="w-2 h-2 bg-white rounded-full"
                                        v-if="markAllAs === 'absent'"
                                    ></div>
                                </div>
                                Absent
                            </label>

                            <label
                                class="inline-flex items-center gap-2 px-4 py-2 bg-gray-200 dark:bg-gray-700 text-yellow-800 dark:text-yellow-100 rounded cursor-pointer"
                                :class="{
                                    '!bg-yellow-500 !text-white':
                                        markAllAs === 'permission',
                                }"
                            >
                                <input
                                    type="radio"
                                    class="sr-only"
                                    name="markAllAs"
                                    value="permission"
                                    v-model="markAllAs"
                                />
                                <div
                                    class="w-5 h-5 border-2 border-gray-500 rounded-full flex items-center justify-center"
                                    :class="
                                        markAllAs === 'permission'
                                            ? 'bg-yellow-500'
                                            : 'bg-white'
                                    "
                                >
                                    <div
                                        class="w-2 h-2 bg-white rounded-full"
                                        v-if="markAllAs === 'permission'"
                                    ></div>
                                </div>
                                Permission
                            </label>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table
                        class="w-full text-sm text-left text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 rounded"
                    >
                        <thead class="bg-gray-100 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-2 min-w-[120px]">Student</th>
                                <th
                                    class="px-2 py-2 text-center min-w-[35px] sm:min-w-[35px]"
                                >
                                    <span class="block sm:hidden">P</span>
                                    <span class="hidden sm:block">Present</span>
                                </th>
                                <th
                                    class="px-2 py-2 text-center min-w-[35px] sm:min-w-[35px]"
                                >
                                    <span class="block sm:hidden">A</span>
                                    <span class="hidden sm:block">Absent</span>
                                </th>
                                <th
                                    class="px-2 py-2 text-center min-w-[35px] sm:min-w-[35px]"
                                >
                                    <span class="block sm:hidden">Per</span>
                                    <span class="hidden sm:block"
                                        >Permission</span
                                    >
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="student in students"
                                :key="student.id"
                                class="border-t border-gray-200 dark:border-gray-700"
                            >
                                <td class="px-4 py-2">
                                    {{ student.firstName }}
                                    {{ student.middleName }}
                                    {{ student.lastName }}
                                </td>

                                <!-- Present -->
                                <td
                                    class="px-1 py-2 text-center cursor-pointer w-[60px] sm:min-w-[35px]"
                                >
                                    <label
                                        class="inline-flex items-center justify-center w-full bg-gray-200 dark:bg-gray-700 py-4 rounded-lg cursor-pointer"
                                        :class="{
                                            '!bg-green-500':
                                                attendance[student.id] ===
                                                'present',
                                        }"
                                    >
                                        <input
                                            type="radio"
                                            class="sr-only"
                                            :name="`attendance_${student.id}`"
                                            value="present"
                                            v-model="attendance[student.id]"
                                        />
                                        <div
                                            class="w-5 h-5 border-2 border-gray-500 rounded-full flex items-center justify-center"
                                            :class="
                                                attendance[student.id] ===
                                                'present'
                                                    ? 'bg-green-500'
                                                    : 'bg-white'
                                            "
                                        >
                                            <div
                                                class="w-2 h-2 bg-white rounded-full"
                                                v-if="
                                                    attendance[student.id] ===
                                                    'present'
                                                "
                                            ></div>
                                        </div>
                                    </label>
                                </td>

                                <!-- Absent -->
                                <td
                                    class="px-1 py-2 text-center cursor-pointer w-[60px] sm:min-w-[35px]"
                                >
                                    <label
                                        class="inline-flex items-center justify-center w-full bg-gray-200 dark:bg-gray-700 py-4 rounded-lg cursor-pointer"
                                        :class="{
                                            '!bg-red-500':
                                                attendance[student.id] ===
                                                'absent',
                                        }"
                                    >
                                        <input
                                            type="radio"
                                            class="sr-only"
                                            :name="`attendance_${student.id}`"
                                            value="absent"
                                            v-model="attendance[student.id]"
                                        />
                                        <div
                                            class="w-5 h-5 border-2 border-gray-500 rounded-full flex items-center justify-center"
                                            :class="
                                                attendance[student.id] ===
                                                'absent'
                                                    ? 'bg-red-500'
                                                    : 'bg-white'
                                            "
                                        >
                                            <div
                                                class="w-2 h-2 bg-white rounded-full"
                                                v-if="
                                                    attendance[student.id] ===
                                                    'absent'
                                                "
                                            ></div>
                                        </div>
                                    </label>
                                </td>

                                <!-- Permission -->
                                <td
                                    class="px-1 py-2 text-center cursor-pointer w-[60px] sm:min-w-[35px]"
                                >
                                    <label
                                        class="inline-flex items-center justify-center w-full bg-gray-200 dark:bg-gray-700 py-4 rounded-lg cursor-pointer"
                                        :class="{
                                            '!bg-yellow-500':
                                                attendance[student.id] ===
                                                'permission',
                                        }"
                                    >
                                        <input
                                            type="radio"
                                            class="sr-only"
                                            :name="`attendance_${student.id}`"
                                            value="permission"
                                            v-model="attendance[student.id]"
                                        />
                                        <div
                                            class="w-5 h-5 border-2 border-gray-500 rounded-full flex items-center justify-center"
                                            :class="
                                                attendance[student.id] ===
                                                'permission'
                                                    ? 'bg-yellow-500'
                                                    : 'bg-white'
                                            "
                                        >
                                            <div
                                                class="w-2 h-2 bg-white rounded-full"
                                                v-if="
                                                    attendance[student.id] ===
                                                    'permission'
                                                "
                                            ></div>
                                        </div>
                                    </label>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 flex gap-2">
                    <PrimaryButton @click="submitAttendance"
                        >Submit</PrimaryButton
                    >
                    <PrimaryButton
                        @click="backToList"
                        class="bg-gray-300 dark:bg-gray-600 text-gray-800 dark:text-white"
                        >Cancel</PrimaryButton
                    >
                </div>
            </div>

            <!-- Default View (Session List & Form) -->
            <div v-else key="list">
                <div class="flex justify-end mb-4">
                    <PrimaryButton @click="toggleForm">
                        {{ showForm ? "Cancel" : "Add Class Session" }}
                    </PrimaryButton>
                </div>

                <div
                    class="overflow-x-auto bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-300 dark:border-gray-600"
                >
                    <table class="w-full min-w-[500px] table-fixed">
                        <thead class="bg-gray-100 dark:bg-gray-700">
                            <tr>
                                <th
                                    class="text-left px-4 py-2 font-medium text-sm text-gray-800 dark:text-gray-200"
                                >
                                    Date @Start Time
                                </th>
                                <th
                                    class="text-left px-4 py-2 font-medium text-sm text-gray-800 dark:text-gray-200 w-24"
                                >
                                    Type
                                </th>
                                <th
                                    class="text-left px-4 py-2 font-medium text-sm text-gray-800 dark:text-gray-200 w-36"
                                >
                                    Status
                                </th>

                                <th
                                    class="text-left px-4 py-2 font-medium text-sm text-gray-800 dark:text-gray-200"
                                >
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-if="classSessions.length > 0"
                                v-for="(classSession, index) in classSessions"
                                :key="classSession.id"
                                :class="[
                                    index % 2 === 0
                                        ? 'bg-white dark:bg-gray-800'
                                        : 'bg-gray-50 dark:bg-gray-700',
                                    'border-b border-gray-300 dark:border-gray-600',
                                ]"
                            >
                                <td
                                    class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300"
                                >
                                    {{ classSession.date }} @{{
                                        classSession.startTime
                                    }}
                                </td>
                                <td
                                    class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300"
                                >
                                    {{ classSession.type }}
                                </td>
                                <td
                                    class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300"
                                >
                                    <span class="rounded-xl p-2 bg-yellow-400">
                                        {{ classSession.status }}
                                    </span>
                                </td>
                                <td
                                    class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300 flex gap-2"
                                >
                                    <button
                                        @click="markAttendance(classSession)"
                                        class="flex items-center justify-center px-3 py-1 text-xs font-medium text-gray-800 dark:text-gray-200 bg-gray-200 dark:bg-gray-600 rounded hover:bg-gray-300 dark:hover:bg-gray-500"
                                        :class="{
                                            'bg-green-500 hover:bg-green-600 dark:bg-green-600 dark:hover:bg-green-700':
                                                classSession.attendances
                                                    .length == 0,
                                        }"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-4 w-4 mr-1"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                            stroke-width="2"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M5 13l4 4L19 7"
                                            />
                                        </svg>
                                        {{
                                            classSession.attendances?.length > 0
                                                ? "Update Attendance"
                                                : "Take Attendance"
                                        }}
                                    </button>

                                    <button
                                        @click="viewDetails(classSession)"
                                        class="flex items-center justify-center px-3 py-1 text-xs font-medium text-gray-800 dark:text-gray-200 bg-gray-200 dark:bg-gray-600 rounded hover:bg-gray-300 dark:hover:bg-gray-500"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-4 w-4 mr-1"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                            stroke-width="2"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M15 10l4.553 2.276A2 2 0 0119 14.618V20a2 2 0 01-2 2h-8a2 2 0 01-2-2v-5.382a2 2 0 01.447-1.342L9 10m6-6h.01"
                                            />
                                        </svg>
                                        Details
                                    </button>
                                </td>
                            </tr>

                            <tr v-else>
                                <td
                                    colspan="3"
                                    class="text-center px-4 py-6 text-sm text-gray-500 dark:text-gray-300"
                                >
                                    No sessions set.
                                </td>
                            </tr>

                            <!-- The Form Row -->
                            <transition
                                enter-active-class="transition duration-300 ease-out"
                                enter-from-class="-translate-y-2 opacity-0"
                                enter-to-class="translate-y-0 opacity-100"
                                leave-active-class="transition duration-200 ease-in"
                                leave-from-class="translate-y-0 opacity-100"
                                leave-to-class="-translate-y-2 opacity-0"
                            >
                                <tr
                                    v-if="showForm"
                                    class="bg-gray-100 dark:bg-gray-700 border-t border-gray-300 dark:border-gray-600"
                                >
                                    <td class="px-1 py-2">
                                        <div>Start date and time</div>
                                        <Calendar
                                            v-model="form.start_date_time"
                                            showTime
                                            placeholder="Select Start Date & Time"
                                            class="w-full"
                                        />
                                        -
                                        <div>End time</div>
                                        <Calendar
                                            v-model="form.end_time"
                                            showTime
                                            :minDate="startDateOnlyMin"
                                            :maxDate="startDateOnlyMax"
                                            placeholder="Select End Time"
                                            class="w-full"
                                        />

                                        <div
                                            v-if="form.errors.date_time"
                                            class="text-red-500 text-xs mt-1"
                                        >
                                            {{ form.errors.date_time }}
                                        </div>
                                    </td>

                                    <td class="px-4 py-2">
                                        <Select
                                            v-model="form.type"
                                            :options="[
                                                {
                                                    label: 'In-person',
                                                    value: 'in-person',
                                                },
                                                {
                                                    label: 'Online',
                                                    value: 'online',
                                                },
                                            ]"
                                            option-label="label"
                                            option-value="value"
                                            placeholder="Select Type"
                                            class="w-full"
                                        />

                                        <div
                                            v-if="form.errors.type"
                                            class="text-red-500 text-xs mt-1"
                                        >
                                            {{ form.errors.type }}
                                        </div>
                                    </td>

                                    <td class="px-4 py-2">
                                        <input
                                            v-model="form.is_complete"
                                            type="checkbox"
                                        />
                                        <label for="isCompleted"
                                            >Is Completed</label
                                        >
                                        <div
                                            v-if="form.errors.is_complete"
                                            class="text-red-500 text-xs mt-1"
                                        >
                                            {{ form.errors.is_complete }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-2 sm:items-center gap-2">
                                        <PrimaryButton
                                            class="mt-2 sm:mt-0"
                                            :disabled="form.processing"
                                            @click="addClassSession"
                                        >
                                            {{
                                                form.processing
                                                    ? "Saving..."
                                                    : "Save"
                                            }}
                                        </PrimaryButton>
                                    </td>
                                </tr>
                            </transition>
                        </tbody>
                    </table>
                </div>
            </div>
        </transition>
    </div>
</template>
