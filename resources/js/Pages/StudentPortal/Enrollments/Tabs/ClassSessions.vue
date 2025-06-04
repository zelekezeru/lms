<script setup>
import { useForm } from "@inertiajs/vue3";
import { ref, onMounted, watch, computed } from "vue";
import Calendar from "primevue/calendar";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { Select } from "primevue";

const props = defineProps({
    enrollment: { required: true, type: Object },
    activeSemester: { required: true, type: Object },
    classSessions: { required: true, type: Array },
});

const showForm = ref(false);
const showDetail = ref(false);
const selectedSession = ref(null);

function toggleForm() {
    showForm.value = !showForm.value;
}

function viewDetails(session) {
    selectedSession.value = session;
    showDetail.value = true;
}

function backToList() {
    selectedSession.value = null;
    showDetail.value = false;
}
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

            <!-- Default View (Session List & Form) -->
            <div v-else key="list">
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
                                ></td>
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
