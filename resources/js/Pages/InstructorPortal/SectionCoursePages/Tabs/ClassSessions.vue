<script setup>
import { useForm } from "@inertiajs/vue3";
import { ref } from "vue";
import Dropdown from "primevue/dropdown";
import Calendar from "primevue/calendar";
import InputText from "primevue/inputtext";
import PrimaryButton from "@/Components/PrimaryButton.vue";

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
    room_id: null,
    date_time: null,
    class_about: "",
});

function addClassSession() {
    form.post(route("classSessions.store"), {
        onSuccess: () => {
            showForm.value = false;
            form.reset("room_id", "date_time", "class_about");
        },
    });
}
</script>

<template>
    <div class="max-w-7xl mx-auto py-10 px-4 space-y-8">
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
            <div v-if="showDetail" key="details" class="bg-white dark:bg-gray-800 p-6 rounded shadow">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">
                    Class Details
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-300">Date: {{ selectedSession.date }}</p>
                <p class="text-sm text-gray-600 dark:text-gray-300">Room: {{ selectedSession.room?.name || 'N/A' }}</p>
                <p class="text-sm text-gray-600 dark:text-gray-300">About: {{ selectedSession.class_about || 'N/A' }}</p>
                <div class="mt-4">
                    <PrimaryButton @click="backToList">Back</PrimaryButton>
                </div>
            </div>

            <!-- Attendance View -->
            <div v-else-if="takeAttendance" key="attendance" class="bg-white dark:bg-gray-800 p-6 rounded shadow">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">
                    Take Attendance
                </h2>
                <ul class="space-y-2">
                    <li
                        v-for="student in students"
                        :key="student.id"
                        class="flex justify-between items-center border-b border-gray-200 dark:border-gray-700 pb-1"
                    >
                        <span class="text-gray-800 dark:text-gray-200">{{ student.name }}</span>
                        <select class="bg-gray-100 dark:bg-gray-700 text-sm rounded px-2 py-1">
                            <option value="present">Present</option>
                            <option value="permission">Permission</option>
                            <option value="absent">Absent</option>
                        </select>
                    </li>
                </ul>
                <div class="mt-4 flex gap-2">
                    <PrimaryButton>Submit</PrimaryButton>
                    <PrimaryButton @click="backToList" class="bg-gray-300 dark:bg-gray-600 text-gray-800 dark:text-white">Cancel</PrimaryButton>
                </div>
            </div>

            <!-- Default View (Session List & Form) -->
            <div v-else key="list">
                <div class="flex justify-end">
                    <PrimaryButton @click="toggleForm">
                        {{ showForm ? "Cancel" : "Add Class Session" }}
                    </PrimaryButton>
                </div>

                <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-300 dark:border-gray-600">
                    <table class="w-full min-w-[800px] table-fixed">
                        <thead class="bg-gray-100 dark:bg-gray-700">
                            <tr>
                                <th class="text-left px-4 py-2 font-medium text-sm text-gray-800 dark:text-gray-200">Date</th>
                                <th class="text-left px-4 py-2 font-medium text-sm text-gray-800 dark:text-gray-200">Room</th>
                                <th class="text-left px-4 py-2 font-medium text-sm text-gray-800 dark:text-gray-200">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-if="classSessions.length > 0"
                                v-for="(classSession, index) in classSessions"
                                :key="classSession.id"
                                :class="[
                                    index % 2 === 0 ? 'bg-white dark:bg-gray-800' : 'bg-gray-50 dark:bg-gray-700',
                                    'border-b border-gray-300 dark:border-gray-600',
                                ]"
                            >
                                <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ classSession.date }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ classSession.room?.name || "N/A" }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300 flex gap-2">
                                    <button
                                        @click="markAttendance(classSession)"
                                        class="flex items-center justify-center px-3 py-1 text-xs font-medium text-gray-800 dark:text-gray-200 bg-gray-200 dark:bg-gray-600 rounded hover:bg-gray-300 dark:hover:bg-gray-500"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Take Attendance
                                    </button>

                                    <button
                                        @click="viewDetails(classSession)"
                                        class="flex items-center justify-center px-3 py-1 text-xs font-medium text-gray-800 dark:text-gray-200 bg-gray-200 dark:bg-gray-600 rounded hover:bg-gray-300 dark:hover:bg-gray-500"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553 2.276A2 2 0 0119 14.618V20a2 2 0 01-2 2h-8a2 2 0 01-2-2v-5.382a2 2 0 01.447-1.342L9 10m6-6h.01" />
                                        </svg>
                                        Details
                                    </button>
                                </td>
                            </tr>

                            <tr v-else>
                                <td colspan="3" class="text-center px-4 py-6 text-sm text-gray-500 dark:text-gray-300">
                                    No sessions set.
                                </td>
                            </tr>

                            <!-- The Form Row -->
                            <transition name="fade">
                                <tr v-if="showForm" class="bg-gray-100 dark:bg-gray-700 border-t border-gray-300 dark:border-gray-600">
                                    <td class="px-4 py-2">
                                        <Calendar v-model="form.date_time" showTime placeholder="Select Date & Time" class="w-full" />
                                        <div v-if="form.errors.date_time" class="text-red-500 text-xs mt-1">
                                            {{ form.errors.date_time }}
                                        </div>
                                    </td>

                                    <td class="px-4 py-2">
                                        <Dropdown v-model="form.room_id" :options="rooms" option-label="name" option-value="id" placeholder="Select Room" class="w-full" />
                                        <div v-if="form.errors.room_id" class="text-red-500 text-xs mt-1">
                                            {{ form.errors.room_id }}
                                        </div>
                                    </td>

                                    <td class="px-4 py-2 flex flex-col sm:flex-row sm:items-center gap-2">
                                        <InputText v-model="form.class_about" placeholder="Class description" class="w-full" />
                                        <div v-if="form.errors.class_about" class="text-red-500 text-xs mt-1">
                                            {{ form.errors.class_about }}
                                        </div>

                                        <PrimaryButton class="mt-2 sm:mt-0" :disabled="form.processing" @click="addClassSession">
                                            {{ form.processing ? "Saving..." : "Save" }}
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
