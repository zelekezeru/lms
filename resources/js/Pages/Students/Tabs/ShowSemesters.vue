<script setup>
import { defineProps, ref } from "vue";
import { router } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";

const props = defineProps({
    student: Object,
    semesters: Array,
    activeSemester: Object,
});

const selectedSemesterId = ref("");

const registerForSemester = () => {
    if (!selectedSemesterId.value) {
        Swal.fire(
            "Warning",
            "Please select a semester to register.",
            "warning"
        );
        return;
    }

    router.post(
        route("students.registerSemester", props.student.id),
        {
            student_id: props.student.id,
            semester_id: selectedSemesterId.value,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                console.log("Registration successful.");
                Swal.fire(
                    "Registered!",
                    "Student registered successfully.",
                    "success"
                );
            },
            onError: (errors) => {
                console.error("Registration failed:", errors.error);
                Swal.fire(
                    "Error",
                    errors.error ?? "Failed to register student.",
                    "error"
                );
            },
        }
    );
};
</script>

<template>
    <div class="space-y-6">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                Student Semester Registration
            </h2>

            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4 items-end">
                <div>
                    <label
                        class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300"
                        >Select Active Semester:</label
                    >
                    <select
                        v-model="selectedSemesterId"
                        class="block w-full p-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-white"
                    >
                        <option
                            value=""
                            disabled
                            selected
                            class="bg-white dark:bg-gray-800 text-gray-900 dark:text-white"
                        >
                            Select Current Semester
                        </option>
                        <option
                            :key="activeSemester.id"
                            :value="activeSemester.id"
                            class="bg-white dark:bg-gray-800 text-gray-900 dark:text-white"
                        >
                            {{ activeSemester.name }} ({{
                                activeSemester.year.name
                            }})
                        </option>
                    </select>
                </div>
                <div>
                    <button
                        type="button"
                        @click="registerForSemester"
                        class="px-4 py-2 text-white bg-blue-600 rounded hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-800"
                    >
                        Register
                    </button>
                </div>
            </div>
        </div>

        <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                Enrollment Status
            </h3>

            <table
                class="w-full mt-4 border border-gray-300 dark:border-gray-600"
            >
                <thead class="bg-gray-100 dark:bg-gray-700">
                    <tr>
                        <th
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white"
                        >
                            #
                        </th>
                        <th
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white"
                        >
                            Semester
                        </th>
                        <th
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white"
                        >
                            Year
                        </th>
                        <th
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white"
                        >
                            Academic Status
                        </th>
                        <th
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white"
                        >
                            Payment Status
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="(enrol, index) in [...semesters].sort((a, b) => {
                            // Sort 'Active' first, then 'Inactive', then others alphabetically
                            if (a.status === b.status) return 0;
                            if (a.status === 'Active') return -1;
                            if (b.status === 'Active') return 1;
                            if (a.status === 'Inactive') return -1;
                            if (b.status === 'Inactive') return 1;
                            return a.status.localeCompare(b.status);
                        })"
                        :key="enrol.id"
                        class="odd:bg-white even:bg-gray-50 dark:odd:bg-gray-800 dark:even:bg-gray-700"
                    >
                        <td
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white"
                        >
                            {{ index + 1 }}
                        </td>
                        <td
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white"
                        >
                            {{ enrol.name }}
                        </td>
                        <td
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white"
                        >
                            {{ enrol.year.name }}
                        </td>
                        <td
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600"
                        >
                            <span
                                :class="
                                    enrol.pivot?.academic_status ===
                                        'in_progress' ||
                                    enrol.pivot?.academic_status === 'completed'
                                        ? 'bg-green-100 text-green-800 dark:bg-green-200 dark:text-green-900 px-2 py-0.5 rounded-full font-semibold'
                                        : enrol.pivot?.academic_status ===
                                          'in_progress'
                                        ? 'bg-gray-200 text-gray-800 dark:bg-gray-700 dark:text-gray-200 px-2 py-0.5 rounded-full font-semibold'
                                        : enrol.pivot?.academic_status ===
                                          'completed'
                                        ? 'bg-red-100 text-red-800 dark:bg-red-200 dark:text-red-900 px-2 py-0.5 rounded-full font-semibold'
                                        : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200 px-2 py-0.5 rounded-full font-semibold'
                                "
                            >
                                {{
                                    enrol.pivot?.academic_status
                                        ? enrol.pivot.academic_status
                                              .charAt(0)
                                              .toUpperCase() +
                                          enrol.pivot.academic_status.slice(1)
                                        : "N/A"
                                }}
                            </span>
                        </td>
                        <td
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600"
                        >
                            <span>
                                {{
                                    enrol.pivot?.payment_status
                                        ? enrol.pivot.payment_status
                                              .charAt(0)
                                              .toUpperCase() +
                                          enrol.pivot.payment_status.slice(1)
                                        : "N/A"
                                }}
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
