<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { defineProps, ref } from "vue";
import { Link, router, useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import { PencilIcon, EyeIcon, TrashIcon } from "@heroicons/vue/24/solid";
import { PlusCircleIcon } from "@heroicons/vue/24/outline";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

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
    duration: "",
    fees: "",
});

const addMode = () => {
    modeForm.post(
        route("studyModes.store", {
            redirectTo: route("programs.show", {program: props.program.id}),
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
                modeForm.duration = "";
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
</script>

<template>
    <AppLayout>
        <div class="max-w-5xl mx-auto p-6">
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
                            {{ program.director ? program.director.name : 'N/A'}}
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
                            class="flex items-center space-x-6 text-indigo-600 hover:text-indigo-800 transition"
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
                                    ) in program.studyModes"
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
                                                class="flex items-center space-x-6"
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
            </div>
        </div>
    </AppLayout>
</template>
