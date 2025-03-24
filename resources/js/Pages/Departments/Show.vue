<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { defineProps, ref } from "vue";
import { Link, router, useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import { PencilIcon, TrashIcon } from "@heroicons/vue/24/solid";
import { PlusCircleIcon } from "@heroicons/vue/24/outline";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

// Define the props for the department
const props = defineProps({
    department: {
        type: Object,
        required: true,
    },
});

const createMode = ref(false);

const modeForm = useForm({
    department_id: props.department.id,
    mode: "",
    duration: "",
    fees: "",
});

const addMode = () => {
    modeForm.post(
        route("studyModes.store", {
            redirectTo: "departments.show",
            params: { department: props.department.id },
        }),
        {
            onSuccess: () => {
                Swal.fire(
                    "Added!",
                    "The Study Mode you entered has been inserted succesfully.",
                    "success"
                );

                createMode.value = false;
                modeForm.mode = "";
                modeForm.duration = "";
                modeForm.fees = "";
            },
        }
    );
};

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

                    <!-- Department Duration -->
                    <div>
                        <span
                            class="block text-sm text-gray-500 dark:text-gray-400"
                            >Duration</span
                        >
                        <span
                            class="block text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ department.duration }}
                        </span>
                    </div>
                </div>

                <!-- Study Modes Section -->
                <div
                    class="mt-8 border-t border-b border-gray-300 dark:border-gray-600 pt-4 pb-4"
                >
                    <div class="flex items-center justify-between mb-4">
                        <h2
                            class="text-xl font-semibold text-gray-900 dark:text-gray-100"
                        >
                            Study Modes
                        </h2>
                        <button
                            @click="createMode = !createMode"
                            class="flex items-center space-x-2 text-indigo-600 hover:text-indigo-800 transition"
                        >
                            <PlusCircleIcon class="w-8 h-8" />
                            <span class="hidden sm:inline">Add Mode</span>
                        </button>
                    </div>
                    <div class="overflow-x-auto">
                        <table
                            class="min-w-full table-auto border border-gray-300 dark:border-gray-600"
                        >
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700">
                                    <th
                                        class="w-40 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600"
                                    >
                                        Mode
                                    </th>
                                    <th
                                        class="w-40 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600"
                                    >
                                        Duration
                                    </th>
                                    <th
                                        class="w-40 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200"
                                    >
                                        Fees
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="(
                                        mode, index
                                    ) in department.studyModes"
                                    :key="mode.id"
                                    :class="
                                        index % 2 === 0
                                            ? 'bg-white dark:bg-gray-800'
                                            : 'bg-gray-50 dark:bg-gray-700'
                                    "
                                    class="border-b border-gray-300 dark:border-gray-600"
                                >
                                    <td
                                        class="w-40 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                    >
                                        {{ mode.mode }}
                                    </td>
                                    <td
                                        class="w-40 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                    >
                                        {{ mode.duration }}
                                    </td>
                                    <td
                                        class="w-40 px-4 py-2 text-sm text-gray-600 dark:text-gray-300"
                                    >
                                        {{ mode.fees }}
                                    </td>
                                </tr>

                                <!-- Create New Mode Row -->
                                <transition
                                    enter-active-class="transition duration-300 ease-out"
                                    enter-from-class="opacity-0 -translate-y-2"
                                    enter-to-class="opacity-100 translate-y-0"
                                    leave-active-class="transition duration-200 ease-in"
                                    leave-from-class="opacity-100 translate-y-0"
                                    leave-to-class="opacity-0 -translate-y-2"
                                >
                                    <tr
                                        v-if="createMode"
                                        class="bg-gray-50 dark:bg-gray-700 border-b border-gray-300 dark:border-gray-600"
                                    >
                                        <td class="px-4 py-2">
                                            <select
                                                v-model="modeForm.mode"
                                                class="w-full px-2 py-1 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100"
                                            >
                                                <option value="">
                                                    Select Mode
                                                </option>
                                                <option value="REGULAR">
                                                    REGULAR
                                                </option>
                                                <option value="EXTENSION">
                                                    EXTENSION
                                                </option>
                                                <option value="DISTANCE">
                                                    DISTANCE
                                                </option>
                                                <option value="ONLINE">
                                                    ONLINE
                                                </option>
                                            </select>
                                        </td>
                                        <td class="px-4 py-2">
                                            <TextInput
                                                v-model="modeForm.duration"
                                                type="number"
                                                placeholder="Duration"
                                                class="w-full px-2 py-1 h-9 border rounded-md dark:bg-gray-800 dark:text-gray-100"
                                            />
                                        </td>
                                        <td class="px-4 py-2">
                                            <div
                                                class="flex items-center space-x-2"
                                            >
                                                <TextInput
                                                    v-model="modeForm.fees"
                                                    type="number"
                                                    placeholder="Fees"
                                                    class="w-full px-2 py-1 h-9 border rounded-md dark:bg-gray-800 dark:text-gray-100"
                                                />
                                                <PrimaryButton
                                                    class="px-4 py-1 h-9 bg-green-500 text-white rounded-md hover:bg-green-600"
                                                    @click="addMode"
                                                >
                                                    Save
                                                </PrimaryButton>
                                            </div>
                                        </td>
                                    </tr>
                                </transition>
                            </tbody>
                        </table>
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
                            @click="confirmDelete(department.id)"
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
