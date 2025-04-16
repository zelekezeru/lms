<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { defineProps, ref } from "vue";
import { Link, router, useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import { PencilIcon, EyeIcon, TrashIcon } from "@heroicons/vue/24/solid";
import { PlusCircleIcon } from "@heroicons/vue/24/outline";

// Define the props for the section with validation-related data
const props = defineProps({
    section: {
        type: Object,
        required: true,
    },
});

// Delete function with SweetAlert confirmation
const deletesection = (id) => {
    Swal.fire({
        title: "Are you sure?",
        text: "This action cannot be undone!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, delete it!",
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route("sections.destroy", { section: id }), {
                onSuccess: () => {
                    Swal.fire(
                        "Deleted!",
                        "The section entry has been successfully deleted.",
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
        <div class="max-w-8xl mx-auto p-6">
            <h1
                class="text-3xl font-semibold mb-6 text-gray-900 dark:text-gray-100 text-center"
            >
                {{ section.name }} Section
            </h1>

            <div
                class="dark:bg-gray-800 shadow-lg rounded-xl p-6 border dark:border-gray-700"
            >
                <div class="grid grid-cols-2 gap-4">
                    <!-- Section Code -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Code</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ section.code }}
                        </span>
                    </div>

                    <!-- Section Name -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Name</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ section.name }}
                        </span>
                    </div>

                    <!-- Section Director -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Director</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ section.user.name }}
                        </span>
                    </div>

                    <!-- Program name -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Program name</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ section.program.name }}
                        </span>
                    </div>

                    <!-- Department  -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Department</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ section.department.name }}
                        </span>
                    </div>

                    <!-- Year name -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Academic Year</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ section.year.name }}
                        </span>
                    </div>

                    <!-- Semester name -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Semester
                        </span>
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ section.semester.name }}
                        </span>
                    </div>
                </div>

                <!-- Edit and Delete Buttons -->
                <div class="flex justify-end mt-6 space-x-6">
                    <Link
                        v-if="userCan('update-sections')"
                        :href="
                            route('sections.edit', { section: section.name })
                        "
                        class="text-blue-500 hover:text-blue-700"
                    >
                        <PencilIcon class="w-5 h-5" />
                    </Link>
                    <button
                        v-if="userCan('delete-sections')"
                        @click="deletesection(section.name)"
                        class="text-red-500 hover:text-red-700"
                    >
                        <TrashIcon class="w-5 h-5" />
                    </button>
                </div>

                <!-- Assign Courses to This section -->
                <Link
                    v-if="userCan('section-courses')"
                    :href="route('section.courses', { section: section.name })"
                    class="text-blue-500 hover:text-blue-700"
                >
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
