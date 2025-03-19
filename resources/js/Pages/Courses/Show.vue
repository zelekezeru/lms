<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { defineProps } from "vue";
import { Link, router } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import { PencilIcon, TrashIcon } from "@heroicons/vue/24/solid";

// Define the props for the course
defineProps({
    course: {
        type: Object,
        required: true,
    },
    user: {
        type: Object,
        required: true,
    },
});

// Delete function with SweetAlert confirmation
const deletecourse = (id) => {
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
            router.delete(route("courses.destroy", { course: id }), {
                onSuccess: () => {
                    Swal.fire("Deleted!", "The course has been deleted.", "success");
                },
            });
        }
    });
};

// Permission check function for user roles
const userCan = (permission) => {
    return user.permissions.includes(permission);
};
</script>


<template>
    <AppLayout title="course Details">
        <div class="max-w-2xl mx-auto p-6">
            <h1 class="text-3xl font-semibold mb-6 text-gray-900 dark:text-gray-100 text-center">
                Course Details
            </h1>

            <div class="dark:bg-gray-800 shadow-lg rounded-xl p-6 border dark:border-gray-700">
                <div class="grid grid-cols-2 gap-4">
                    <!-- course ID -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400">ID</span>
                        <span class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ course.id }}</span>
                    </div>

                    <!-- course Name -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Name</span>
                        <span class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ course.user?.name || "N/A" }}</span>
                    </div>

                    <!-- Specialization -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Specialization</span>
                        <span class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ course.specialization }}</span>
                    </div>

                    <!-- Employment Type -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Employment Type</span>
                        <span class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ course.employment_type }}</span>
                    </div>

                    <!-- Hire Date -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Hire Date</span>
                        <span class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ course.hire_date }}</span>
                    </div>

                    <!-- Status -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Status</span>
                        <span class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ course.status }}</span>
                    </div>

                    <!-- Bio -->
                    <div class="flex flex-col col-span-2">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Bio</span>
                        <span class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ course.bio || "N/A" }}</span>
                    </div>
                </div>

                <!-- Edit and Delete Buttons -->
                <div class="flex justify-end mt-6 space-x-2">
                    <Link :href="route('courses.edit', { course: course.id })" class="text-blue-500 hover:text-blue-700" v-if="userCan('update-courses')">
                        <PencilIcon class="w-5 h-5" />
                    </Link>
                    <button @click="deletecourse(course.id)" class="text-red-500 hover:text-red-700" v-if="userCan('delete-courses')">
                        <TrashIcon class="w-5 h-5" />
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
