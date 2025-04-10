<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { defineProps, ref } from "vue";
import { router } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import { PlusIcon } from "@heroicons/vue/24/solid";

// Props
const props = defineProps({
    year: {
        type: Object,
        required: true,
    },
});

const { year } = props;

// Ref for toggling the form
const showForm = ref(false);

// Ref for handling semester form data
const semesterForm = ref({
    name: "",
    status: "inactive",
    is_approved: false,
    is_completed: false,
});

// Function to toggle form visibility
const toggleForm = () => {
    showForm.value = !showForm.value;
};

// Function to submit the semester form
const submitSemester = () => {
    console.log('Submitting semester with year_id:', semesterForm.value.year_id); // Debugging line to check form data
    router.post(route("semesters.store", { year: year.id }), semesterForm.value, {
        onSuccess: () => {
            Swal.fire("Added!", "Semester has been added.", "success");
            semesterForm.value = {
                name: "",
                status: "inactive",
                is_approved: false,
                is_completed: false,
                year_id: year.id, // Reset year_id after submission to keep it consistent
            };
            showForm.value = false; // Collapse the form after submission
        },
    });
};
</script>



<template>
    <AppLayout>
        <div class="max-w-4xl mx-auto p-6">
            <h1 class="text-3xl font-semibold mb-6 text-center text-gray-900 dark:text-gray-100">
                Year Details
            </h1>

            <!-- Year Information -->
            <div
                class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 border dark:border-gray-700"
            >
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Year Name -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Name</span>
                        <span class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            {{ year.name }}
                        </span>
                    </div>

                    <!-- Year Status -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Status</span>
                        <span class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            {{ year.status }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Add Semester Button -->
            <div class="mt-6 flex justify-between items-center">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                    Semesters
                </h2>
                <button
                    @click="toggleForm"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                >
                    <PlusIcon class="w-5 h-5 mr-2" /> Add Semester
                </button>
            </div>

            <!-- Add Semester Form -->
            <div v-if="showForm" class="mt-4 bg-gray-100 dark:bg-gray-800 p-4 rounded-md">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
                    Add New Semester
                </h3>
                <form @submit.prevent="submitSemester">
                    <div class="mb-4">
                        <label
                            for="name"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                        >
                            Semester Name
                        </label>
                        <input
                            id="name"
                            type="text"
                            v-model="semesterForm.name"
                            required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-100"
                        />
                    </div>

                    <div class="mb-4">
                        <label
                            for="status"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                        >
                            Status
                        </label>
                        <select
                            id="status"
                            v-model="semesterForm.status"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-100"
                        >
                            <option value="inactive">Inactive</option>
                            <option value="active">Active</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <input
                            id="is_approved"
                            type="checkbox"
                            v-model="semesterForm.is_approved"
                            class="rounded text-indigo-600"
                        />
                        <label for="is_approved" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                            Is Approved
                        </label>
                    </div>

                    <div class="mb-4">
                        <input
                            id="is_completed"
                            type="checkbox"
                            v-model="semesterForm.is_completed"
                            class="rounded text-indigo-600"
                        />
                        <label
                            for="is_completed"
                            class="ml-2 text-sm text-gray-700 dark:text-gray-300"
                        >
                            Is Completed
                        </label>
                    </div>

                    <button
                        type="submit"
                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700"
                    >
                        Submit
                    </button>
                </form>
            </div>

            <!-- Semesters Table -->
            <div class="mt-6">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th class="px-6 py-3">Name</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Approved</th>
                            <th class="px-6 py-3">Completed</th>
                            <th class="px-6 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="semester in year.semesters"
                            :key="semester.id"
                            class="border-b bg-white dark:bg-gray-900 dark:border-gray-700"
                        >
                            <td class="px-6 py-4">{{ semester.name }}</td>
                            <td class="px-6 py-4">{{ semester.status }}</td>
                            <td class="px-6 py-4">{{ semester.is_approved ? "Yes" : "No" }}</td>
                            <td class="px-6 py-4">{{ semester.is_completed ? "Yes" : "No" }}</td>
                            <td class="px-6 py-4 flex space-x-2">
                                <button
                                    @click="deleteSemester(semester.id)"
                                    class="text-red-500 hover:text-red-700"
                                >
                                    <TrashIcon class="w-5 h-5" />
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>
