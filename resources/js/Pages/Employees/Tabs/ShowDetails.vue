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

// Define the props for the employee
const props = defineProps({
    employee: {
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
            router.delete(route("employees.destroy", { employee: id }), {
                onSuccess: () => {
                    Swal.fire(
                        "Deleted!",
                        "The employee has been deleted.",
                        "success"
                    );
                },
            });
        }
    });
};
</script>

<template>
    <!-- Instructor Image -->

    <div class="dark:bg-gray-800 shadow-lg rounded-xl p-6 border dark:border-gray-700">
        <div class="max-w-8xl mx-auto p-6">
            
            <div class="flex justify-center mb-8">
                <div
                    v-if="!imageLoaded"
                    class="rounded-full w-44 h-44 bg-gray-300 dark:bg-gray-700 animate-pulse"
                ></div>
                <img
                    v-show="imageLoaded"
                    class="rounded-full w-44 h-44 object-contain bg-gray-400"
                    :src="employee.profileImg"
                    :alt="`profile image of ` + employee.name"
                    @load="imageLoaded = true"
                />
            </div>
            <div class="overflow-x-auto">
                <div class="mt-8 pt-4 pb-4">
            
                    <!-- Employee details -->
                    <div class="grid grid-cols-2 gap-4">

                        <!-- Employee Name -->
                        <div class="flex flex-col">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Name</span>
                            <span class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ employee.name }}
                            </span>
                        </div>

                        <!-- Employee Email -->
                        <div class="flex flex-col">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Email</span>
                            <span class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ employee.email }}
                            </span>
                        </div>
                        <!-- Employee ID -->
                        <div class="flex flex-col">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Phone Number</span>
                            <span class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ employee.user.phone }}
                            </span>
                        </div>
                        
                        <!-- Employee ID -->
                        <div class="flex flex-col">
                            <span class="text-sm text-gray-500 dark:text-gray-400">User ID</span>
                            <span class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ employee.user.user_uuid }}
                            </span>
                        </div>

                        <!-- Role -->
                        <div class="flex flex-col">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Role</span>
                            <span class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ employee.userRole }}
                            </span>
                        </div>

                        <!-- Employment Type -->
                        <div class="flex flex-col">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Employment Type</span>
                            <span class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ employee.employmentType }}
                            </span>
                        </div>

                        <!-- Job Position -->
                        <div class="flex flex-col">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Job Position</span>
                            <span class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ employee.jobPosition }}
                            </span>
                        </div>
                        <!-- Office Hours -->
                        <div class="flex flex-col">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Office Hours</span>
                            <span class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ employee.officeHours }}
                            </span>
                        </div>


                        <!-- Status -->
                        <div class="flex flex-col">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Status</span>
                            <span class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                <div v-if="employee.status == 0" class="text-red-500">Inactive</div>
                                <div v-else class="text-green-500">Active</div>
                            </span>
                        </div>

                        <!-- Default Password -->
                        <div class="flex flex-col">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Default Password</span>
                            <span class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ employee.user.default_password }}
                            </span>
                        </div>

                    </div>
                        
                </div>
            </div>

            <!-- Edit and Delete Buttons -->
            <div class="flex justify-end mt-6 space-x-6">
                <Link
                    v-if="userCan('update-employees')"
                    :href="
                        route('employees.edit', { employee: employee.id })
                    "
                    class="text-blue-500 hover:text-blue-700"
                >
                    <PencilIcon class="w-5 h-5" />
                    <span>Edit</span>
                </Link>
                <button
                    v-if="userCan('delete-employees')"
                    @click="deleteEmployee(employee.id)"
                    class="text-red-500 hover:text-red-700"
                >
                    <TrashIcon class="w-5 h-5" />
                    <span>Delete</span>
                </button>
            </div>
        </div>
    </div>
</template>
