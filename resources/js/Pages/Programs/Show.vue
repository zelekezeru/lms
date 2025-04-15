<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { defineProps, ref } from "vue";
import { Link, router, useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import { PencilIcon, EyeIcon, TrashIcon } from "@heroicons/vue/24/solid";
import { PlusCircleIcon } from "@heroicons/vue/24/outline";

// Define the props for the program
const props = defineProps({
    program: {
        type: Object,
        required: true,
    },
    
});

// Delete function with SweetAlert confirmation
const deleteProgram = (id) => {
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
            router.delete(route("programs.destroy", { program: id }), {
                onSuccess: () => {
                    Swal.fire(
                        "Deleted!",
                        "The program has been deleted.",
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
        <div class="max-w-2xl mx-auto p-6">
            <h1
                class="text-3xl font-semibold mb-6 text-gray-900 dark:text-gray-100 text-center"
            >
                {{ program.name }} Program
            </h1>

            <div
                class="dark:bg-gray-800 shadow-lg rounded-xl p-6 border dark:border-gray-700"
            >
                <div class="grid grid-cols-2 gap-4">
                    <!-- Program ID -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >CODE</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ program.code }}
                        </span>
                    </div>

                    <!-- Program Name -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Name</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ program.name }}
                        </span>
                    </div>

                    <!-- Program Language -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Language</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ program.language }}
                        </span>
                    </div>
                    
                    <!-- Description -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Description</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ program.description }}
                        </span>
                    </div>

                    <!-- Program Director -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Program Director</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ program.user.name }}
                        </span>
                    </div>
                </div>                

                <!-- Edit and Delete Buttons -->
                <div class="flex justify-end mt-6 space-x-6">
                    <Link
                        v-if="userCan('update-programs')"
                        :href="route('programs.edit', { program: program.id })"
                        class="text-blue-500 hover:text-blue-700"
                    >
                        <PencilIcon class="w-5 h-5" />
                            <span>Edit</span>
                    </Link>
                    <button
                        v-if="userCan('delete-programs')"
                        @click="deleteProgram(program.id)"
                        class="text-red-500 hover:text-red-700"
                    >
                        <TrashIcon class="w-5 h-5" />
                            <span>Delete</span>
                    </button>
                </div>

                <!-- Departments -->
                <div class="mt-10">
                    
                    <Link
                        v-if="userCan('create-departments')"
                        :href="route('departments.create')"
                        class="inline-flex items-center rounded-md bg-green-600 text-white px-4 py-2 text-xs font-semibold uppercase tracking-widest transition duration-150 ease-in-out hover:bg-green-700 focus:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                    >
                        + Add Department
                    </Link>

                    <div class="text-center">
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                            >Departments</span
                        >
                    </div>
                    <table class="mt-2 w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b dark:border-gray-700 p-2">
                                <th
                                    class="text-md font-medium text-gray-900 dark:text-gray-100"
                                >
                                    Code
                                </th>
                                <th
                                    class="text-md font-medium text-gray-900 dark:text-gray-100"
                                >
                                    Department Name
                                </th>
                                <th
                                    class="text-md font-medium text-gray-900 dark:text-gray-100"
                                >
                                    Duration
                                </th>
                                <th
                                    class="text-md font-medium text-gray-900 dark:text-gray-100"
                                >
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="department in program.departments"
                                :key="department.id"
                                class="border-b dark:border-gray-700 p-2 cursor-pointer"
                            >
                                <td
                                    class="text-sm text-gray-500 dark:text-gray-400"
                                >
                                    {{ department.code }}
                                </td>
                                <td
                                    class="text-sm text-gray-500 dark:text-gray-400"
                                >
                                    {{ department.name }}
                                </td>
                                <td
                                    class="text-sm text-gray-500 dark:text-gray-400"
                                >
                                    {{ program.duration }}
                                </td>
                                <td>
                                    <div v-if="userCan('view-departments')">
                                        <Link
                                            prefetch="hover"
                                            cache-for="3"
                                            :href="
                                                route('departments.show', {
                                                    department: department.id,
                                                })
                                            "
                                            class="text-blue-500 hover:text-blue-700"
                                        >
                                            <EyeIcon class="w-5 h-5" />
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
