<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { defineProps, ref } from "vue";
import { Link, router, useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import Modal from "@/Components/Modal.vue";
import { Listbox, MultiSelect } from "primevue";
import {
    PencilIcon,
    EyeIcon,
    TrashIcon,
    CogIcon,
    AcademicCapIcon,
    UsersIcon,
    PencilSquareIcon,
    PlusCircleIcon,
    BookOpenIcon,
    HomeModernIcon,
} from "@heroicons/vue/24/solid";

// Define the props for the instructor
const props = defineProps({
    instructor: {
        type: Object,
        required: true,
    },
});

// Delete function with SweetAlert confirmation
const deleteInstructor = (id) => {
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
            router.delete(route("instructors.destroy", { instructor: id }), {
                onSuccess: () => {
                    Swal.fire(
                        "Deleted!",
                        "The instructor has been deleted.",
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
        <div
            class="dark:bg-gray-800 shadow-lg rounded-xl p-6 border dark:border-gray-700"
        >
            <!-- Instructor Image -->
            <div class="flex justify-center mb-8">
                <div
                    v-if="!imageLoaded"
                    class="rounded-full w-44 h-44 bg-gray-300 dark:bg-gray-700 animate-pulse"
                ></div>

                <img
                    v-show="imageLoaded"
                    class="rounded-full w-44 h-44 object-contain bg-gray-400"
                    :src="instructor.profileImg"
                    :alt="`Logo of ` + instructor.name"
                    @load="handleImageLoad"
                />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <!-- instructor Full Name -->
                <div class="flex flex-col">
                    <span class="text-sm text-gray-500 dark:text-gray-400"
                        >Full Name</span
                    >
                    <span
                        class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >{{ instructor.user.name }}</span
                    >
                </div>

                <!-- Email -->
                <div class="flex flex-col">
                    <span class="text-sm text-gray-500 dark:text-gray-400"
                        >Email</span
                    >
                    <span
                        class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >{{ instructor.user.email }}</span
                    >
                </div>

                <!-- Phone -->
                <div class="flex flex-col">
                    <span class="text-sm text-gray-500 dark:text-gray-400"
                        >Phone</span
                    >
                    <span
                        class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >{{ instructor.user.phone }}</span
                    >
                </div>

                <!-- Employment Type -->
                <div class="flex flex-col">
                    <span class="text-sm text-gray-500 dark:text-gray-400"
                        >Employment Type</span
                    >
                    <span
                        class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >{{ instructor.employmentType }}</span
                    >
                </div>

                <!-- Bio -->
                <div class="flex flex-col">
                    <span class="text-sm text-gray-500 dark:text-gray-400"
                        >Bio</span
                    >
                    <span
                        class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >{{ instructor.bio }}</span
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
                        <div v-if="instructor.status == 0" class="text-red-500">
                            Inactive
                        </div>
                        <div v-else class="text-green-500">Active</div>
                    </span>
                </div>

                <!-- Default Password -->
                <div class="flex flex-col">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Default Password</span>
                    <span class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        {{ instructor.user.default_password }}
                    </span>
                </div>
            </div>

            <!-- Edit and Delete Buttons -->
            <div class="flex justify-end mt-6 space-x-6">
                <!-- Edit Button, only show if user has permission -->
                <div v-if="userCan('update-instructors')">
                    <Link
                        :href="
                            route('instructors.edit', {
                                instructor: instructor.id,
                            })
                        "
                        class="text-blue-500 hover:text-blue-700"
                    >
                        <PencilIcon class="w-5 h-5" />
                        <span>Edit</span>
                    </Link>
                </div>

                <!-- Delete Button, only show if user has permission -->
                <div v-if="userCan('delete-instructors')">
                    <button
                        @click="deleteInstructor(instructor.id)"
                        class="text-red-500 hover:text-red-700"
                    >
                        <TrashIcon class="w-5 h-5" />
                        <span>Delete</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
