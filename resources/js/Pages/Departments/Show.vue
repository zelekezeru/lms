<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { defineProps, ref } from "vue";
import { Link, router, useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import { PencilIcon, TrashIcon } from "@heroicons/vue/24/solid";
import { PlusCircleIcon } from "@heroicons/vue/24/outline";

// Define the props for the department
const props = defineProps({
    department: {
        type: Object,
        required: true,
    },
});

// Delete function with SweetAlert confirmation
const deleteDepartment = (id) => {
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
            router.delete(route("departments.destroy", { department: id }), {
                onSuccess: () => {
                    Swal.fire(
                        "Deleted!",
                        "The department has been deleted.",
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
        <div class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8">
            <h1
                class="text-3xl sm:text-4xl font-bold mb-6 text-center text-gray-900 dark:text-gray-100"
            >
                {{ department.name }} Department
            </h1>

            <div
                class="bg-white dark:bg-gray-800 shadow-lg rounded-xl p-6 border border-gray-200 dark:border-gray-700"
            >
                <div class="grid gap-4 sm:grid-cols-2">
                    <!-- Department Code -->
                    <div>
                        <span
                            class="block text-sm text-gray-500 dark:text-gray-400"
                            >Code</span
                        >
                        <span
                            class="block text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ department.code }}
                        </span>
                    </div>

                    <!-- Department Program -->
                    <div>
                        <span
                            class="block text-sm text-gray-500 dark:text-gray-400"
                            >Program</span
                        >
                        <span
                            class="block text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ department.program.name }}
                        </span>
                    </div>

                    <!-- Description -->
                    <div>
                        <span
                            class="block text-sm text-gray-500 dark:text-gray-400"
                            >Description</span
                        >
                        <span
                            class="block text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ department.description }}
                        </span>
                    </div>
                </div>

                <!-- Edit and Delete Buttons -->
                <div class="flex justify-end mt-6 space-x-4">
                    <!-- Edit Button, only show if user has permission -->
                    <div v-if="userCan('update-departments')">
                        <Link
                            :href="
                                route('departments.edit', {
                                    department: department.id,
                                })
                            "
                            class="flex items-center space-x-1 text-blue-500 hover:text-blue-700"
                        >
                            <PencilIcon class="w-5 h-5" />
                            <span>Edit</span>
                        </Link>
                    </div>
                    <!-- Delete Button, only show if user has permission -->
                    <div v-if="userCan('delete-departments')">
                        <button
                            @click="deleteDepartment(department.id)"
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
