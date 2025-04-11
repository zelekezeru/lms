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
                Semester Details
            </h1>

            <!-- Semester Information -->
            <div
                class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 border dark:border-gray-700"
            >
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Semester Name -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Semester</span>
                        <span class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            {{ semester.name }}
                        </span>
                    </div>

                    <!-- year Name -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Year</span>
                        <span class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            {{ semester.year.name }}
                        </span>
                    </div>

                    <!-- Semester Status -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Status</span>
                        <span class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            {{ semester.status }}
                        </span>
                    </div>
                    <!-- Semester Approval -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Approval</span>
                        <span class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            {{ semester.is_approved ? "Approved" : "Not Approved" }}
                        </span>
                    </div>
                        
                </div>
                

                <!-- Edit and Delete Buttons -->
                <div class="flex justify-end mt-6 space-x-4">
                    <!-- Edit Button, only show if user has permission -->
                    <div v-if="userCan('update-semesters')">
                        <Link
                            :href="
                                route('semesters.edit', {
                                    semester: semester.id,
                                })
                            "
                            class="flex items-center space-x-1 text-blue-500 hover:text-blue-700"
                        >
                            <PencilIcon class="w-5 h-5" />
                            <span>Edit</span>
                        </Link>
                    </div>
                    <!-- Delete Button, only show if user has permission -->
                    <div v-if="userCan('delete-semesters')">
                        <button
                            @click="deleteSemester(semester.id)"
                            class="flex items-center space-x-1 text-red-500 hover:text-red-700"
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
