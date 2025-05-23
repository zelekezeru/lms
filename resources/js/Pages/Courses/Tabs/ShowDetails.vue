<script setup>
import { PencilIcon, TrashIcon } from "@heroicons/vue/24/outline";
import { Link } from "@inertiajs/vue3";
import { defineProps } from "vue";

const props = defineProps({
    course: {
        type: Object,
        required: true,
    },
});

// Delete function with SweetAlert confirmation
const deleteCourse = (id) => {
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
                    Swal.fire(
                        "Deleted!",
                        "The course has been deleted.",
                        "success"
                    );
                },
            });
        }
    });
};
</script>

<template>
    <div>
        <h1
            class="text-3xl font-semibold mb-6 text-gray-900 dark:text-gray-100 text-center"
        >
            Course Details
        </h1>

        <div
            class="dark:bg-gray-800 shadow-lg rounded-xl p-6 border dark:border-gray-700"
        >
            <div class="grid grid-cols-2 gap-4">
                <!-- course Code -->
                <div class="flex flex-col">
                    <span class="text-sm text-gray-500 dark:text-gray-400"
                        >Code</span
                    >
                    <span
                        class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >{{ course.code }}</span
                    >
                </div>

                <!-- course Name -->
                <div class="flex flex-col">
                    <span class="text-sm text-gray-500 dark:text-gray-400"
                        >Name</span
                    >
                    <span
                        class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >{{ course.name || "N/A" }}</span
                    >
                </div>

                <!-- Credit -->
                <div class="flex flex-col">
                    <span class="text-sm text-gray-500 dark:text-gray-400"
                        >Credit Hours</span
                    >
                    <span
                        class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >{{ course.credit_hours }}</span
                    >
                </div>
                <!-- Duration -->
                <div class="flex flex-col">
                    <span class="text-sm text-gray-500 dark:text-gray-400"
                        >Duration</span
                    >
                    <span
                        class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >{{ course.duration }}</span
                    >
                </div>
                <!-- Description -->
                <div class="flex flex-col">
                    <span class="text-sm text-gray-500 dark:text-gray-400"
                        >Description</span
                    >
                    <span
                        class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >{{ course.description }}</span
                    >
                </div>

                <!-- Status -->
                <div class="flex flex-col">
                    <span class="text-sm text-gray-500 dark:text-gray-400"
                        >Status</span
                    >
                    <span
                        class="text-lg font-medium text-gray-900 dark:text-gray-100"
                    >
                        {{ course.status ? "Active" : "Inactive" }}
                    </span>
                </div>
            </div>

            <!-- Edit and Delete Buttons -->
            <div class="flex justify-end mt-6 space-x-4">
                <!-- Edit Button, only show if user has permission -->
                <div v-if="userCan('update-courses')">
                    <Link
                        :href="
                            route('courses.edit', {
                                course: course.id,
                            })
                        "
                        class="flex items-center space-x-1 text-blue-500 hover:text-blue-700"
                    >
                        <PencilIcon class="w-5 h-5" />
                        <span>Edit</span>
                    </Link>
                </div>
                <!-- Delete Button, only show if user has permission -->
                <div v-if="userCan('delete-courses')">
                    <button
                        @click="deleteCourse(course.id)"
                        class="flex items-center space-x-1 text-red-500 hover:text-red-700"
                    >
                        <TrashIcon class="w-5 h-5" />
                        <span>Delete</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
