<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { defineProps, ref } from "vue";
import { usePage, Link, router } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import {
    ArrowPathIcon,
    TrashIcon,
    EyeIcon,
    PlusIcon,
    PencilSquareIcon,
    PencilIcon,
    CalendarDaysIcon,
    CheckBadgeIcon,
    ClipboardDocumentCheckIcon,
    CheckCircleIcon,
} from "@heroicons/vue/24/solid";

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
    year_id: year.id,
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
    // console.log('Submitting semester with year_id:', semesterForm.value.year_id); // Debugging line to check form data
    router.post(route("semesters.store"), semesterForm.value, {
        onSuccess: () => {
            Swal.fire("Added!", "Semester has been added.", "success");
            semesterForm.value = {
                name: "",
                status: "inactive",
                year_id: year.id,
                is_approved: false,
                is_completed: false,
                year_id: year.id, // Reset year_id after submission to keep it consistent
            };
            showForm.value = false; // Collapse the form after submission
        },
    });
};

// Refresh Data function
const refreshData = () => {
    refreshing.value = true;
    router.visit(route("years.show", year.id), {
        only: ["year"],
        onFinish: () => {
            refreshing.value = false;
        },
    });
};

// Delete function with SweetAlert confirmation
const deleteYear = (id) => {
    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, delete it!",
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route("years.destroy", { year: id }), {
                onSuccess: () => {
                    Swal.fire(
                        "Deleted!",
                        "The Year has been deleted.",
                        "success"
                    );
                },
                onError: () => {
                    Swal.fire(
                        "Error!",
                        "There was a problem deleting the Year.",
                        "error"
                    );
                },
            });
        }
    });
};
</script>

<template>
    <AppLayout>
        <div class="max-w-4xl mx-auto ">
            <h1
                class="text-3xl font-semibold mb-6 text-center text-gray-900 dark:text-gray-100"
            >
                Year Details
            </h1>

            <!-- Year Information -->
            <div
                class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 border dark:border-gray-700"
            >
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Year Name -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Name</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ year.name }}
                        </span>
                    </div>

                    <!-- Year Approval -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Approval</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ year.is_approved ? "Approved" : "Not Approved" }}
                        </span>
                    </div>

                    <!-- Year Status -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Status</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ year.status }}
                        </span>
                    </div>
                </div>

                <!-- Edit and Delete Buttons -->
                <div class="flex justify-end mt-6 space-x-4">
                    <!-- Edit Button, only show if user has permission -->
                    <div v-if="userCan('update-years')">
                        <Link
                            :href="
                                route('years.edit', {
                                    year: year.id,
                                })
                            "
                            class="flex items-center space-x-1 text-blue-500 hover:text-blue-700"
                        >
                            <PencilIcon class="w-5 h-5" />
                            <span>Edit</span>
                        </Link>
                    </div>
                    <!-- Delete Button, only show if user has permission -->
                    <div v-if="userCan('delete-years')">
                        <button
                            @click="deleteYear(year.id)"
                            class="flex items-center space-x-1 text-red-500 hover:text-red-700"
                        >
                            <TrashIcon class="w-5 h-5" />
                            <span>Delete</span>
                        </button>
                    </div>
                </div>

                <!-- semesters -->
                <div class="mt-10">
                    <div class="text-center">
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                            >Semesters in {{ year.name }}</span
                        >
                    </div>
                </div>

                <div class="mt-6">
                    <div class="flex space-x-6">
                        <button
                            @click="refreshData"
                            class="inline-flex items-center rounded-md bg-blue-800 text-white px-4 py-2 text-xs font-semibold uppercase tracking-widest transition hover:bg-blue-700"
                            title="Refresh Data"
                        >
                            <ArrowPathIcon
                                class="w-5 h-5 mr-2"
                                :class="{ 'animate-spin': refreshing }"
                            />
                            Refresh
                        </button>

                        <button
                            @click="toggleForm"
                            class="inline-flex items-center rounded-md bg-green-600 text-white px-4 py-2 text-xs font-semibold uppercase tracking-widest transition hover:bg-green-700"
                        >
                            <PlusIcon class="w-5 h-5 mr-2" /> Add Semester
                        </button>
                    </div>
                </div>

                <!-- Semesters Table -->
                <div class="mt-6">
                    <div
                        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6"
                    >
                        <div
                            v-for="semester in year.semesters"
                            :key="semester.id"
                            class="rounded-xl p-5 transition duration-300 shadow-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-[#1E293B]"
                        >
                            <!-- Header -->
                            <div
                                class="flex items-center mb-3 text-indigo-600 dark:text-indigo-400"
                            >
                                <CalendarDaysIcon class="w-6 h-6 mr-2" />
                                <h2
                                    class="text-lg font-semibold text-gray-800 dark:text-white"
                                >
                                    <Link
                                        v-if="semester.name"
                                        :href="
                                            route('semesters.show', {
                                                semester: semester.id,
                                            })
                                        "
                                        class="hover:underline"
                                    >
                                        {{ semester.name }}
                                    </Link>
                                    <span
                                        v-else
                                        class="text-gray-500 dark:text-gray-400"
                                        >No Name</span
                                    >
                                </h2>
                            </div>

                            <!-- Body -->
                            <div class="text-sm space-y-1">
                                <p class="text-gray-600 dark:text-gray-300">
                                    <CheckBadgeIcon
                                        class="w-4 h-4 inline-block mr-1 text-green-500 dark:text-green-400"
                                    />
                                    <span
                                        class="font-medium text-gray-800 dark:text-white"
                                        >Status:</span
                                    >
                                    {{ semester.status }}
                                </p>
                                <p class="text-gray-600 dark:text-gray-300">
                                    <ClipboardDocumentCheckIcon
                                        class="w-4 h-4 inline-block mr-1 text-yellow-500 dark:text-yellow-400"
                                    />
                                    <span
                                        class="font-medium text-gray-800 dark:text-white"
                                        >Approved:</span
                                    >
                                    {{ semester.is_approved ? "Yes" : "No" }}
                                </p>
                                <p class="text-gray-600 dark:text-gray-300">
                                    <CheckCircleIcon
                                        class="w-4 h-4 inline-block mr-1 text-blue-500 dark:text-blue-400"
                                    />
                                    <span
                                        class="font-medium text-gray-800 dark:text-white"
                                        >Completed:</span
                                    >
                                    {{ semester.is_completed ? "Yes" : "No" }}
                                </p>
                            </div>

                            <!-- Footer -->
                            <div class="flex justify-end mt-4">
                                <Link
                                    v-if="userCan('view-semesters')"
                                    :href="
                                        route('semesters.show', {
                                            semester: semester.id,
                                        })
                                    "
                                    class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300"
                                >
                                    <EyeIcon class="w-5 h-5" />
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Add Semester Form -->
                <div
                    v-if="showForm"
                    class="mt-4 bg-gray-100 dark:bg-gray-800 p-4 rounded-md"
                >
                    <h3
                        class="text-lg font-bold text-gray-900 dark:text-white mb-4"
                    >
                        Add New Semester
                    </h3>

                    <form @submit.prevent="submitSemester">
                        <input
                            id="year_id"
                            name="year_id"
                            type="hidden"
                            v-model="semesterForm.year_id"
                        />

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
                                <option value="Inactive">Inactive</option>
                                <option value="Active">Active</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <input
                                id="is_approved"
                                type="checkbox"
                                v-model="semesterForm.is_approved"
                                class="rounded text-indigo-600"
                            />
                            <label
                                for="is_approved"
                                class="ml-2 text-sm text-gray-700 dark:text-gray-300"
                            >
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
            </div>
        </div>
    </AppLayout>
</template>
