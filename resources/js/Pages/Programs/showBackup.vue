<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { defineProps, ref } from "vue";
import { Link, router, useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import { usePage } from "@inertiajs/vue3";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { PencilIcon, EyeIcon, TrashIcon, PlusCircleIcon } from "@heroicons/vue/24/solid";

// Define the props for the program
const props = defineProps({
    program: {
        type: Object,
        required: true,
    },

});
const createMode = ref(false);

const modeForm = useForm({
    program_id: props.program.id,
    mode: "",
    fees: "",
});

const addMode = () => {
    modeForm.post(
        route("studyModes.store", {
            redirectTo: "programs.show",
            params: { program: props.program.id },
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
                modeForm.fees = "";
            },
        }
    );
};

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

// Ensure program.user, program.departments, and program.studyModes have fallbacks
const programUser = props.program.user || { name: "N/A" };
const programDepartments = props.program.departments || [];
const studyModes = props.program.studyModes || [];
</script>

<template>
    <AppLayout>
        <div class="max-w-2xl mx-auto p-6">
            <h1
                class="text-3xl font-semibold mb-6 text-gray-900 dark:text-gray-100 text-center"
            >
                {{ program.name || "N/A" }} Program
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
                            {{ program.code || "N/A" }}
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
                            {{ program.name || "N/A" }}
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
                            {{ program.language || "N/A" }}
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
                            {{ program.description || "N/A" }}
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
                            {{ programUser.name }}
                        </span>
                    </div>
                </div>                

                <!-- Edit and Delete Buttons -->
                <div class="flex justify-end mt-6 space-x-2">
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
                                v-for="department in programDepartments"
                                :key="department.id"
                                class="border-b dark:border-gray-700 p-2 cursor-pointer"
                            >
                                <td
                                    class="text-sm text-gray-500 dark:text-gray-400"
                                >
                                    {{ department.code || "N/A" }}
                                </td>
                                <td
                                    class="text-sm text-gray-500 dark:text-gray-400"
                                >
                                    {{ department.name || "N/A" }}
                                </td>
                                <td
                                    class="text-sm text-gray-500 dark:text-gray-400"
                                >
                                    {{ program.duration || "N/A" }}
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
                <!-- Study Modes -->
                <div class="mt-10">
                    <Link
                        v-if="userCan('create-study-modes')"
                        @click="createMode = true"
                        class="inline-flex items-center rounded-md bg-green-600 text-white px-4 py-2 text-xs font-semibold uppercase tracking-widest transition duration-150 ease-in-out hover:bg-green-700 focus:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                    >
                        + Add Study Mode
                    </Link>

                    <div class="text-center">
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                            >Study Modes</span
                        >
                    </div>
                    <table class="mt-2 w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b dark:border-gray-700 p-2">
                                <th
                                    class="text-md font-medium text-gray-900 dark:text-gray-100"
                                >
                                    Mode
                                </th>
                                <th
                                    class="text-md font-medium text-gray-900 dark:text-gray-100"
                                >
                                    Fees
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
                                v-for="mode in studyModes"
                                :key="mode.id"
                                class="border-b dark:border-gray-700 p-2 cursor-pointer"
                            >
                                <td
                                    class="text-sm text-gray-500 dark:text-gray-400"
                                >
                                    {{ mode.mode || "N/A" }}
                                </td>
                                <td
                                    class="text-sm text-gray-500 dark:text-gray-400"
                                >
                                    {{ mode.fees || "N/A" }}
                                </td>
                                <td>
                                    <div v-if="userCan('delete-study-modes')">
                                        <button
                                            @click="
                                                deleteProgram(mode.id)
                                            "
                                            class="text-red-500 hover:text-red-700"
                                        >
                                            <TrashIcon class="w-5 h-5" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Add Study Mode Modal -->
                    <div
                        v-if="createMode"
                        class="fixed inset
                            0 z-50 flex items-center justify-center bg-black bg-opacity-50"
                    >
                </div>  
            </div>
            
        </div>
        </div>
    </AppLayout>
</template>
