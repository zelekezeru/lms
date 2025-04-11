<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { defineProps, ref } from "vue";
import { router } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import { PlusIcon } from "@heroicons/vue/24/solid";

// Props
const props = defineProps({
    semester: {
        type: Object,
        required: true,
    },
    year: {
        type: Object,
        required: true,
    },
});

const { semester } = props;

// Ref for toggling the form
const showForm = ref(false);

// Ref for handling semester form data
const semesterForm = ref({
    name: "",
    semester_id: semester.id,
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
    router.post(route("semesters.store"), semesterForm.value, {
        onSuccess: () => {
            Swal.fire("Added!", "Semester has been added.", "success");
            semesterForm.value = {
                name: "",
                status: "inactive",
                semester_id: semester.id,
                is_approved: false,
                is_completed: false,
            };
            showForm.value = false;
        },
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
            </div>
        </div>
    </AppLayout>
</template>
