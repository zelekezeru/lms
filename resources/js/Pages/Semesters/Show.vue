<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { defineProps, ref } from "vue";
import { usePage, Link, router } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import { ArrowPathIcon, TrashIcon, EyeIcon, PlusIcon, PencilSquareIcon, PencilIcon } from "@heroicons/vue/24/solid";


// Props
const props = defineProps({
    semester: {
        type: Object,
        required: true,
    },
});

// Delete function with SweetAlert confirmation
const deleteSemester = (id) => {
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
            router.delete(route("semesters.destroy", { semester: id }), {
                onSuccess: () => {
                    Swal.fire(
                        "Deleted!",
                        "The semester has been deleted.",
                        "success"
                    );
                },
            });
        }
    });
};
</script>

<template>
    <AppLayout>
        <div class="max-w-4xl mx-auto p-6">
            <h1 class="text-3xl font-semibold mb-6 text-center text-gray-900 dark:text-gray-100">
                {{ semester.name }} - Semester  of {{ semester.year.name }} Details
            </h1>

            <!-- Semester Information -->
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 border dark:border-gray-700">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <!-- Semester Name -->
                    <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Semester</span>
                        <div class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                            {{ semester.name }}
                        </div>
                    </div>

                    <!-- Year Name -->
                    <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Year</span>
                        <div class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                            <Link
                                :href="route('years.show', { year: semester.year.id })"
                                class="text-blue-600 hover:text-blue-800 hover:underline"
                            >
                                {{ semester.year.name }}
                            </Link>
                        </div>
                    </div>

                    <!-- Semester Status -->
                    <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Status</span>
                        <div :class="semester.status === 'Active' ? 'text-green-600' : 'text-red-600'" class="text-lg font-semibold">
                            {{ semester.status }}
                        </div>
                    </div>

                    <!-- Semester Start Date -->
                    <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Start Date</span>
                        <div class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                            {{ new Date(semester.start_date).toLocaleDateString() }}
                        </div>
                    </div>

                    <!-- Semester End Date -->
                    <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg">
                        <span class="text-sm text-gray-500 dark:text-gray-400">End Date</span>
                        <div class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                            {{ new Date(semester.end_date).toLocaleDateString() }}
                        </div>
                    </div>

                    <!-- Semester Approval -->
                    <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Approval</span>
                        <div :class="semester.is_approved ? 'text-green-600' : 'text-red-600'" class="text-lg font-semibold">
                            {{ semester.is_approved ? "Approved" : "Not Approved" }}
                        </div>
                    </div>
                </div>

                <!-- Edit and Delete Buttons -->
                <div class="flex justify-end mt-6 space-x-4">
                    <div v-if="userCan('delete-semesters')">
                        <button
                            @click="deleteSemester(semester.id)"
                            class="inline-flex items-center space-x-2 text-red-600 hover:text-red-800 hover:underline"
                        >
                            <TrashIcon class="w-5 h-5" />
                            <span>Delete</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
